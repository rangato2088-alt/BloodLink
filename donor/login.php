<?php
// BloodLink Donor Login
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Donor Login | BloodLink</title>

    <link rel="stylesheet" href="../css/donor-login.css">

</head>

<body>

    <!-- Desktop Only Message -->
    <div class="desktop-message" id="desktopMessage">
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
                <img src="../img/bloodlink-logo.webp" alt="BloodLink Logo">
            </div>


            <!-- Heading -->
            <div class="login-header">

                <h1>Donor Login</h1>

                <p>
                    Sign in to access your BloodLink donor account.
                </p>

            </div>


            <!-- Login Form -->
            <form id="donorLoginForm" novalidate>


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
                        maxlength="50"
                        autocomplete="username"
                    >

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
                            autocomplete="current-password"
                        >

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