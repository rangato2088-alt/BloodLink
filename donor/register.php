<?php

require_once "../config/database.php";

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullName = trim($_POST["full_name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";
    $bloodGroup = $_POST["blood_group"] ?? "";
    $dateOfBirth = $_POST["date_of_birth"] ?? "";
    $gender = $_POST["gender"] ?? "";
    $address = trim($_POST["address"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phoneNumber = trim($_POST["phone_number"] ?? "");
    $lastDonationDate = $_POST["last_donation_date"] ?? "";
    $neverDonated = isset($_POST["never_donated"]);


    /* =========================================
       BASIC VALIDATION
       ========================================= */

    if (
        $fullName === "" ||
        $username === "" ||
        $password === "" ||
        $bloodGroup === "" ||
        $dateOfBirth === "" ||
        $gender === ""
    ) {

        $errorMessage = "Please complete all required fields.";

    } elseif (strlen($username) < 4) {

        $errorMessage = "Username must contain at least 4 characters.";

    } elseif (strlen($password) < 8) {

        $errorMessage = "Password must contain at least 8 characters.";

    } elseif ($password !== $confirmPassword) {

        $errorMessage = "Passwords do not match.";

    } else {

        /* =========================================
           CHECK USERNAME
           ========================================= */

        $checkUsername = $conn->prepare(
            "SELECT Donor_ID FROM donor WHERE Username = ?"
        );

        $checkUsername->bind_param(
            "s",
            $username
        );

        $checkUsername->execute();

        $usernameResult =
            $checkUsername->get_result();


        if ($usernameResult->num_rows > 0) {

            $errorMessage =
                "This username is already registered.";

        } else {

            /* =========================================
               CHECK EMAIL
               ========================================= */

            if ($email !== "") {

                $checkEmail = $conn->prepare(
                    "SELECT Donor_ID FROM donor WHERE Email = ?"
                );

                $checkEmail->bind_param(
                    "s",
                    $email
                );

                $checkEmail->execute();

                $emailResult =
                    $checkEmail->get_result();


                if ($emailResult->num_rows > 0) {

                    $errorMessage =
                        "This email address is already registered.";

                }

            }


            /* =========================================
               CREATE ACCOUNT
               ========================================= */

            if ($errorMessage === "") {

                $passwordHash =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );


                /*
                 * If the donor has never donated,
                 * save NULL instead of an empty date.
                 */

                if ($neverDonated) {

                    $lastDonationValue = null;

                } else {

                    $lastDonationValue = $lastDonationDate;

                }


                $sql = "
                    INSERT INTO donor
                    (
                        Full_Name,
                        Username,
                        Password_Hash,
                        Blood_Group,
                        Date_Of_Birth,
                        Gender,
                        Address,
                        Email,
                        Phone_Number,
                        Last_Donation_Date
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";


                $stmt = $conn->prepare($sql);


                $stmt->bind_param(
                    "ssssssssss",
                    $fullName,
                    $username,
                    $passwordHash,
                    $bloodGroup,
                    $dateOfBirth,
                    $gender,
                    $address,
                    $email,
                    $phoneNumber,
                    $lastDonationValue
                );


                if ($stmt->execute()) {

                    $successMessage =
                        "Your donor account has been created successfully.";

                } else {

                    $errorMessage =
                        "Unable to create your account. Please try again.";

                }

                $stmt->close();

            }

        }

        $checkUsername->close();

    }

}

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

            <?php if ($successMessage !== ""): ?>

<style>
    #successPopup {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;

        background: rgba(17, 24, 39, 0.65) !important;

        display: flex !important;
        justify-content: center !important;
        align-items: center !important;

        z-index: 999999 !important;

        padding: 20px !important;
        margin: 0 !important;

        box-sizing: border-box !important;
    }

    #successPopupBox {
        width: 430px !important;
        max-width: 90vw !important;

        background: #ffffff !important;

        border-radius: 16px !important;

        padding: 40px !important;

        text-align: center !important;

        box-sizing: border-box !important;

        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.30) !important;
    }

    #successPopupIcon {
        width: 65px !important;
        height: 65px !important;

        margin: 0 auto 20px !important;

        border-radius: 50% !important;

        background: #e8f7ee !important;

        color: #198754 !important;

        display: flex !important;
        justify-content: center !important;
        align-items: center !important;

        font-size: 32px !important;
        font-weight: bold !important;
    }

    #successPopupBox h2 {
        margin: 0 0 12px 0 !important;

        color: #111827 !important;

        font-family: Arial, Helvetica, sans-serif !important;

        font-size: 24px !important;

        font-weight: 700 !important;
    }

    #successPopupBox p {
        margin: 0 0 25px 0 !important;

        color: #6b7280 !important;

        font-family: Arial, Helvetica, sans-serif !important;

        font-size: 15px !important;

        line-height: 1.6 !important;
    }

    #successOkButton {
        width: 100% !important;
        height: 48px !important;

        border: none !important;
        border-radius: 8px !important;

        background: #d90429 !important;

        color: #ffffff !important;

        font-family: Arial, Helvetica, sans-serif !important;

        font-size: 16px !important;
        font-weight: 700 !important;

        cursor: pointer !important;

        transition: background 0.2s ease !important;
    }

    #successOkButton:hover {
        background: #9d0208 !important;
    }
</style>


<div id="successPopup">

    <div id="successPopupBox">

        <div id="successPopupIcon">
            ✓
        </div>

        <h2>
            Donor Account Created
        </h2>

        <p>
            Your BloodLink donor account has been created successfully.
        </p>

        <button
            type="button"
            id="successOkButton">

            OK

        </button>

    </div>

</div>

<?php endif; ?>


            <?php if ($errorMessage !== ""): ?>

                <div class="error-message">
                    <?php echo htmlspecialchars($errorMessage); ?>
            </div>

        <?php endif; ?>


            <!-- Registration Form -->

            <form
                id="donorRegisterForm"
                method="POST"
                action=""
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

    <script>
    document.addEventListener("DOMContentLoaded", function () {

        const successOkButton =
            document.getElementById("successOkButton");

        if (successOkButton) {

            successOkButton.addEventListener("click", function () {

                window.location.href = "../index.php";

            });

        }

    });
</script>


</body>
</html>

</body>

</html>