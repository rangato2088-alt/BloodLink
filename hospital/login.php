<?php

session_start();

require_once "../admin/include/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $hospital_name = $_POST["hospital_name"];
    $password = $_POST["password"];

    $sql = "SELECT h.Hospital_ID,
                   h.Hospital_Name,
                   h.Is_Verified,
                   ha.Hospital_Admin_ID,
                   ha.Full_Name,
                   ha.Status
            FROM hospital h
            INNER JOIN hospital_admin ha
                ON h.Hospital_ID = ha.Hospital_ID
            WHERE h.Hospital_Name = ?
              AND ha.Password_Hash = ?
              AND h.Is_Verified = 1
              AND ha.Status = 'Active'";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ss", $hospital_name, $password);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $hospital = $result->fetch_assoc();

        $_SESSION["hospital_id"] = $hospital["Hospital_ID"];
        $_SESSION["hospital_name"] = $hospital["Hospital_Name"];
        $_SESSION["hospital_admin_id"] = $hospital["Hospital_Admin_ID"];
        $_SESSION["hospital_admin_name"] = $hospital["Full_Name"];

        header("Location: dashboard.php");
        exit();

    } else {

        $error = "Invalid hospital name, password, or hospital approval status.";

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hospital Login | BloodLink</title>

    <link rel="stylesheet" href="assets/css/hospital.css">

</head>

<body>

    <div class="login-container">

        <div class="login-box">

            <img
                src="assets/image/BloodLink_logo_800px_transparent.webp"
                alt="BloodLink Logo"
                class="login-logo"
            >

            <p class="login-subtitle">Hospital Portal</p>

            <h2>Hospital Login</h2>

            <?php if ($error !== ""): ?>

            <div class="login-error">
                <?php echo htmlspecialchars($error); ?>
            </div>

                <?php endif; ?>

            <form method="POST" action="">

                <div class="form-group">

                    <label for="hospital_name">
                        Hospital Name
                    </label>

                        <input
                            type="text"
                            id="hospital_name"
                            name="hospital_name"
                            placeholder="Enter Hospital Name"
                            required
                        >

                </div>


                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>


                <button type="submit">
                    Login
                </button>

            </form>

                    <div class="register-section">

                    <p>Don't have a hospital account?</p>

                    <a href="registration.php" class="register-btn">
                     Register Hospital
                    </a>
            </div>

        </div>

    </div>

</body>

</html>