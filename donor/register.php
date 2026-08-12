<?php
// BloodLink Donor Registration
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Donor Registration | BloodLink</title>

    <link rel="stylesheet" href="../css/donor-register.css">

</head>

<body>

    <!-- Desktop Only Message -->

    <div class="desktop-message">

        <h2>BloodLink Donor Registration</h2>

        <p>
            This page is available on desktop web browsers only.
        </p>

    </div>


    <!-- Registration Page -->

    <main class="register-page">

        <div class="register-card">


            <!-- BloodLink Logo -->

            <div class="logo">

                <img
                    src="../img/bloodlink-logo.webp"
                    alt="BloodLink Logo">

            </div>


            <!-- Header -->

            <div class="register-header">

                <h1>Donor Registration</h1>

                <p>
                    Create your BloodLink donor account.
                </p>

            </div>


            <!-- Registration Form -->

            <form
                id="donorRegisterForm"
                method="POST"
                novalidate>


                <!-- =================================
                     PERSONAL INFORMATION
                     ================================= -->

                <div class="form-section">

                    <h2>Personal Information</h2>


                    <!-- Full Name -->

                    <div class="form-group">

                        <label for="fullName">
                            Full Name
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="fullName"
                            name="full_name"
                            placeholder="Enter your full name"
                            maxlength="100"
                            autocomplete="name">

                        <small
                            id="fullNameError"
                            class="error-message">
                        </small>

                    </div>


                    <!-- Date of Birth + Gender -->

                    <div class="form-row">


                        <!-- Date of Birth -->

                        <div class="form-group">

                            <label for="dateOfBirth">
                                Date of Birth
                                <span>*</span>
                            </label>

                            <input
                                type="date"
                                id="dateOfBirth"
                                name="date_of_birth">

                            <small
                                id="dateOfBirthError"
                                class="error-message">
                            </small>

                        </div>


                        <!-- Gender -->

                        <div class="form-group">

                            <label for="gender">
                                Gender
                                <span>*</span>
                            </label>

                            <select
                                id="gender"
                                name="gender">

                                <option value="">
                                    Select gender
                                </option>

                                <option value="Male">
                                    Male
                                </option>

                                <option value="Female">
                                    Female
                                </option>

                                <option value="Other">
                                    Other
                                </option>

                            </select>

                            <small
                                id="genderError"
                                class="error-message">
                            </small>

                        </div>

                    </div>


                    <!-- Address -->

                    <div class="form-group">

                        <label for="address">
                            Address
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            placeholder="Enter your address"
                            maxlength="255"></textarea>

                    </div>

                </div>


                <!-- =================================
                     BLOOD INFORMATION
                     ================================= -->

                <div class="form-section">

                    <h2>Blood Information</h2>


                    <!-- Blood Group -->

                    <div class="form-group">

                        <label for="bloodGroup">
                            Blood Group
                            <span>*</span>
                        </label>

                        <select
                            id="bloodGroup"
                            name="blood_group">

                            <option value="">
                                Select blood group
                            </option>

                            <option value="A+">A+</option>
                            <option value="A-">A-</option>

                            <option value="B+">B+</option>
                            <option value="B-">B-</option>

                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>

                            <option value="O+">O+</option>
                            <option value="O-">O-</option>

                        </select>

                        <small
                            id="bloodGroupError"
                            class="error-message">
                        </small>

                    </div>


                    <!-- Last Donation Date -->

                    <div class="form-group">

                        <label for="lastDonationDate">
                            Last Blood Donation Date
                        </label>

                        <input
                            type="date"
                            id="lastDonationDate"
                            name="last_donation_date">

                        <small
                            class="field-help">
                            If you have never donated blood before,
                            select the option below.
                        </small>

                        <small
                            id="lastDonationDateError"
                            class="error-message">
                        </small>

                    </div>


                    <!-- Never Donated -->

                    <div class="checkbox-group">

                        <input
                            type="checkbox"
                            id="neverDonated"
                            name="never_donated"
                            value="1">

                        <label for="neverDonated">
                            I have never donated blood before.
                        </label>

                    </div>

                </div>


                <!-- =================================
                     CONTACT INFORMATION
                     ================================= -->

                <div class="form-section">

                    <h2>Contact Information</h2>


                    <div class="form-row">


                        <!-- Email -->

                        <div class="form-group">

                            <label for="email">
                                Email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="Enter your email"
                                maxlength="150"
                                autocomplete="email">

                            <small
                                id="emailError"
                                class="error-message">
                            </small>

                        </div>


                        <!-- Phone -->

                        <div class="form-group">

                            <label for="phoneNumber">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                id="phoneNumber"
                                name="phone_number"
                                placeholder="Enter phone number"
                                maxlength="20"
                                autocomplete="tel">

                            <small
                                id="phoneNumberError"
                                class="error-message">
                            </small>

                        </div>

                    </div>

                </div>


                <!-- =================================
                     ACCOUNT INFORMATION
                     ================================= -->

                <div class="form-section">

                    <h2>Account Information</h2>


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
                            placeholder="Create a username"
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
                                placeholder="Create a password"
                                maxlength="72"
                                autocomplete="new-password">

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


                    <!-- Confirm Password -->

                    <div class="form-group">

                        <label for="confirmPassword">
                            Confirm Password
                            <span>*</span>
                        </label>

                        <div class="password-box">

                            <input
                                type="password"
                                id="confirmPassword"
                                name="confirm_password"
                                placeholder="Confirm your password"
                                maxlength="72"
                                autocomplete="new-password">

                        </div>

                        <small
                            id="confirmPasswordError"
                            class="error-message">
                        </small>

                    </div>

                </div>


                <!-- Register Button -->

                <button
                    type="submit"
                    class="register-button">

                    Create Donor Account

                </button>


            </form>

        </div>

    </main>


    <script src="../js/donor-register.js"></script>

</body>

</html>