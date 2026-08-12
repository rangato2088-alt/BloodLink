<?php
// Donor login page
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

    <main class="login-page">

        <section class="login-card">

            <!-- Login Header -->
            <div class="login-header">

                <div class="login-logo">
                    B
                </div>

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
                        Username <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-input"
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
                        Password <span class="required">*</span>
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Enter your password"
                            maxlength="72"
                            autocomplete="current-password"
                        >

                        <button
                            type="button"
                            id="togglePassword"
                            class="password-toggle">
                            Show
                        </button>

                    </div>

                    <small
                        id="passwordError"
                        class="error-message">
                    </small>

                </div>


                <!-- Forgot Password -->
                <div class="forgot-password-container">

                    <a href="#" class="forgot-password">
                        Forgot Password?
                    </a>

                </div>


                <!-- Login Button -->
                <button
                    type="submit"
                    class="login-button">
                    Login
                </button>

            </form>


            <!-- Registration -->
            <div class="register-section">

                <p>
                    Don't have a donor account?
                    <a href="#" class="register-link">
                        Create Account
                    </a>
                </p>

            </div>

        </section>

    </main>


    <script src="../js/donor-login.js"></script>

</body>
</html>