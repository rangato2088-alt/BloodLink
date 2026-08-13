<?php

session_start();

require_once "../admin/include/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $hospital_name = trim($_POST["hospital_name"]);
    $phone = trim($_POST["phone"]);
    $hospital_email = trim($_POST["hospital_email"]);
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);

    $full_name = trim($_POST["full_name"]);
    $admin_email = trim($_POST["admin_email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];


    if ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        $conn->begin_transaction();

        try {

            // Insert hospital
            $sql = "INSERT INTO hospital
                    (Hospital_Name, Phone, Email, Address, City, Is_Verified)
                    VALUES (?, ?, ?, ?, ?, 0)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "sssss",
                $hospital_name,
                $phone,
                $hospital_email,
                $address,
                $city
            );

            $stmt->execute();

            // Get the newly created Hospital_ID
            $hospital_id = $conn->insert_id;


            // Insert hospital administrator
            $sql = "INSERT INTO hospital_admin
                    (Hospital_ID, Full_Name, Email, Password_Hash, Status)
                    VALUES (?, ?, ?, ?, 'Inactive')";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "isss",
                $hospital_id,
                $full_name,
                $admin_email,
                $password
            );

            $stmt->execute();


            // Save both records
            $conn->commit();

            $success = "Hospital registration submitted successfully. Please wait for administrator approval.";

        } catch (Exception $e) {

            $conn->rollback();

            $error = "Registration failed. Please try again.";

        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hospital Registration | BloodLink</title>

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

            <h2>Hospital Registration</h2>

            <?php if ($error !== ""): ?>

                <div class="registration-error">
            <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>


            <?php if ($success !== ""): ?>

             <div class="registration-success">
            <?php echo htmlspecialchars($success); ?>
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

                    <label for="phone">
                        Phone
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        placeholder="Enter phone number"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="hospital_email">
                        Hospital Email
                    </label>

                    <input
                        type="email"
                        id="hospital_email"
                        name="hospital_email"
                        placeholder="Enter hospital email"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="address">
                        Address
                    </label>

                    <input
                        type="text"
                        id="address"
                        name="address"
                        placeholder="Enter hospital address"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="city">
                        City
                    </label>

                    <input
                        type="text"
                        id="city"
                        name="city"
                        placeholder="Enter city"
                        required
                    >

                </div>


                <hr><br>


                <h3 class="form-section-title">
                    Hospital Administrator
                </h3><br>


                <div class="form-group">

                    <label for="full_name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        placeholder="Enter administrator name"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="admin_email">
                        Administrator Email
                    </label>

                    <input
                        type="email"
                        id="admin_email"
                        name="admin_email"
                        placeholder="Enter administrator email"
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
                        placeholder="Create a password"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="confirm_password">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm your password"
                        required
                    >

                </div>


                <button type="submit">
                    Register Hospital
                </button>

            </form>


            <div class="register-section">

                <p>Already have a hospital account?</p>

                <a href="login.php" class="register-btn">
                    Hospital Login
                </a>

            </div>


        </div>

    </div>

</body>

</html>