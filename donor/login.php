<?php

session_start();

require_once "../config/database.php";

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    /* =========================================
       BASIC VALIDATION
       ========================================= */

    if ($username === "" || $password === "") {

        $errorMessage = "Please enter your username and password.";

    } else {

        /* =========================================
           FIND DONOR
           ========================================= */

        $sql = "SELECT Donor_ID, Full_Name, Username, Password_Hash
                FROM donor
                WHERE Username = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        if ($stmt) {

            $stmt->bind_param("s", $username);

            $stmt->execute();

            $result = $stmt->get_result();

            /* =========================================
               CHECK USERNAME
               ========================================= */

            if ($result->num_rows === 1) {

                $donor = $result->fetch_assoc();

                /* =========================================
                   CHECK PASSWORD
                   ========================================= */

                if (password_verify($password, $donor["Password_Hash"])) {

                    /* Create donor session */

                    $_SESSION["donor_id"] = $donor["Donor_ID"];
                    $_SESSION["donor_name"] = $donor["Full_Name"];
                    $_SESSION["donor_username"] = $donor["Username"];

                    /* Go to donor dashboard */

                    header("Location: dashboard.php");
                    exit;

                } else {

                    $errorMessage =
                        "Invalid username or password.";

                }

            } else {

                $errorMessage =
                    "Invalid username or password.";

            }

            $stmt->close();

        } else {

            $errorMessage =
                "Unable to process your login. Please try again.";
        }
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Donor Login | BloodLink</title>

    <link
        rel="stylesheet"
        href="../css/donor-login.css">

</head>


<body>


    <!-- Desktop Only Message -->

    <div class="desktop-message">

        <h2>BloodLink Donor Login</h2>

        <p>
            This page is available on desktop web browsers only.
        </p>

    </div>


    <!-- Login Page -->

    <main class="login-page">


        <div class="login-card">


            <!-- Logo -->

            <div class="logo">

                <img
                    src="../img/bloodlink-logo.webp"
                    alt="BloodLink Logo">

            </div>


            <!-- Header -->

            <div class="login-header">

                <h1>Donor Login</h1>

                <p>
                    Sign in to access your BloodLink donor account.
                </p>

            </div>


            <!-- PHP Error -->

            <?php if ($errorMessage !== ""): ?>

                <div class="login-error">

                    <?php echo htmlspecialchars($errorMessage); ?>

                </div>

            <?php endif; ?>


            <!-- Login Form -->

            <form
                id="donorLoginForm"
                method="POST"
                action=""
                novalidate>


                <!-- Username -->

                <div class="form-group">

                    <label for="username">

                        Username

                        <span>*</span>

                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter your username"
                        maxlength="100"
                        autocomplete="username">

                    <small
                        id="usernameError"
                        class="error-message">
                    </small>

                </div>


                <!-- Password -->

                <div class="form-group">

                    <label for="password">

                        Password

                        <span>*</span>

                    </label>


                    <div class="password-box">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            maxlength="72"
                            autocomplete="current-password">


                        <button
                            type="button"
                            id="togglePassword"
                            class="show-password">

                            Show

                        </button>

                    </div>


                    <small
                        id="passwordError"
                        class="error-message">
                    </small>

                </div>


                <!-- Login Button -->

                <button
                    type="submit"
                    class="login-button">

                    Login

                </button>


            </form>


        </div>

    </main>


    <script src="../js/donor-login.js"></script>


</body>

</html>