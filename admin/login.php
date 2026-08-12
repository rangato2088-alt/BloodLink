<?php

session_start();

require_once "include/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $admin_id = $_POST["admin_id"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM admin 
            WHERE Admin_ID = ? 
            AND Password_Hash = ? 
            AND Status = 'Active'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $admin_id, $password);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $admin = $result->fetch_assoc();

        $_SESSION["admin_id"] = $admin["Admin_ID"];
        $_SESSION["admin_name"] = $admin["Full_Name"];

        header("Location: dashboard.php");
        exit();

    } else {

        $error = "Invalid Admin ID or Password.";

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | BloodLink</title>

    <link rel="stylesheet" href="assets/css/admin.css">
</head>

<body>

    <div class="login-container">

        <div class="login-box">

            <img src="assets/image/BloodLink_logo_800px_transparent.webp" alt="BloodLink Logo"class="login-logo">

            <p class="login-subtitle">Administrator Portal</p>

            <h2>Admin Login</h2>

            <form method="POST" action="">

                <div class="form-group">
                    <label for="admin_id">Admin ID</label>
                    <input 
                        type="text" 
                        id="admin_id" 
                        name="admin_id"
                        placeholder="Enter Admin ID"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        placeholder="Enter Password"
                        required
                    >
                </div>

                <button type="submit">Login</button>

            </form>

        </div>

    </div>

    <script src="assets/js/admin.js"></script>

</body>
</html>