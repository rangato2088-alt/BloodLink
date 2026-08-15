/* =========================================
   BLOODLINK DONOR REGISTRATION
   ========================================= */

document.addEventListener("DOMContentLoaded", function () {
  /* =========================================
       GET FORM ELEMENTS
       ========================================= */

  const form = document.getElementById("donorRegisterForm");

  const fullName = document.getElementById("fullName");
  const dateOfBirth = document.getElementById("dateOfBirth");
  const gender = document.getElementById("gender");
  const bloodGroup = document.getElementById("bloodGroup");

  const lastDonationDate = document.getElementById("lastDonationDate");

  const neverDonated = document.getElementById("neverDonated");

  const email = document.getElementById("email");
  const phoneNumber = document.getElementById("phoneNumber");

  const username = document.getElementById("username");
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirmPassword");

  const togglePassword = document.getElementById("togglePassword");

  /* =========================================
       CHECK FORM
       ========================================= */

  if (!form) {
    console.error("Donor registration form was not found.");
    return;
  }

  /* =========================================
       SHOW / HIDE PASSWORD
       ========================================= */

  if (togglePassword) {
    togglePassword.addEventListener("click", function () {
      if (password.type === "password") {
        password.type = "text";
        togglePassword.textContent = "Hide";
      } else {
        password.type = "password";
        togglePassword.textContent = "Show";
      }
    });
  }

  /* =========================================
       SHOW ERROR
       ========================================= */

  function showError(input, errorId, message) {
    input.classList.add("input-error");
    input.classList.remove("input-success");

    const error = document.getElementById(errorId);

    if (error) {
      error.textContent = message;
      error.classList.add("show");
    }
  }

  /* =========================================
       CLEAR ERROR
       ========================================= */

  function clearError(input, errorId) {
    input.classList.remove("input-error");
    input.classList.add("input-success");

    const error = document.getElementById(errorId);

    if (error) {
      error.textContent = "";
      error.classList.remove("show");
    }
  }

  /* =========================================
       FULL NAME VALIDATION
       ========================================= */

  function validateFullName() {
    const value = fullName.value.trim();

    if (value === "") {
      showError(fullName, "fullNameError", "Please enter your full name.");

      return false;
    }

    if (value.length < 3) {
      showError(
        fullName,
        "fullNameError",
        "Full name must contain at least 3 characters.",
      );

      return false;
    }

    clearError(fullName, "fullNameError");

    return true;
  }

  /* =========================================
       DATE OF BIRTH VALIDATION
       ========================================= */

  function validateDateOfBirth() {
    if (dateOfBirth.value === "") {
      showError(
        dateOfBirth,
        "dateOfBirthError",
        "Please select your date of birth.",
      );

      return false;
    }

    const selectedDate = new Date(dateOfBirth.value);

    const today = new Date();

    if (selectedDate >= today) {
      showError(
        dateOfBirth,
        "dateOfBirthError",
        "Date of birth must be in the past.",
      );

      return false;
    }

    clearError(dateOfBirth, "dateOfBirthError");

    return true;
  }

  /* =========================================
       GENDER VALIDATION
       ========================================= */

  function validateGender() {
    if (gender.value === "") {
      showError(gender, "genderError", "Please select your gender.");

      return false;
    }

    clearError(gender, "genderError");

    return true;
  }

  /* =========================================
       BLOOD GROUP VALIDATION
       ========================================= */

  function validateBloodGroup() {
    if (bloodGroup.value === "") {
      showError(
        bloodGroup,
        "bloodGroupError",
        "Please select your blood group.",
      );

      return false;
    }

    clearError(bloodGroup, "bloodGroupError");

    return true;
  }

  /* =========================================
       LAST DONATION DATE VALIDATION
       ========================================= */

  function validateLastDonationDate() {
    /* Never donated */

    if (neverDonated.checked) {
      lastDonationDate.value = "";

      lastDonationDate.classList.remove("input-error", "input-success");

      const error = document.getElementById("lastDonationDateError");

      if (error) {
        error.textContent = "";
        error.classList.remove("show");
      }

      return true;
    }

    /* Donated before */

    if (lastDonationDate.value === "") {
      showError(
        lastDonationDate,
        "lastDonationDateError",
        "Please enter your last blood donation date or select 'I have never donated blood before'.",
      );

      return false;
    }

    const donationDate = new Date(lastDonationDate.value);

    const today = new Date();

    if (donationDate > today) {
      showError(
        lastDonationDate,
        "lastDonationDateError",
        "Last donation date cannot be in the future.",
      );

      return false;
    }

    clearError(lastDonationDate, "lastDonationDateError");

    return true;
  }

  /* =========================================
       NEVER DONATED CHECKBOX
       ========================================= */

  if (neverDonated) {
    neverDonated.addEventListener("change", function () {
      if (neverDonated.checked) {
        lastDonationDate.value = "";

        lastDonationDate.disabled = true;

        lastDonationDate.classList.remove("input-error", "input-success");

        const error = document.getElementById("lastDonationDateError");

        if (error) {
          error.textContent = "";
          error.classList.remove("show");
        }
      } else {
        lastDonationDate.disabled = false;
      }
    });
  }

  /* =========================================
       EMAIL VALIDATION
       ========================================= */

  function validateEmail() {
    const value = email.value.trim();

    /* Email is optional */

    if (value === "") {
      email.classList.remove("input-error", "input-success");

      return true;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailPattern.test(value)) {
      showError(email, "emailError", "Please enter a valid email address.");

      return false;
    }

    clearError(email, "emailError");

    return true;
  }

  /* =========================================
       PHONE VALIDATION
       ========================================= */

  function validatePhone() {
    const value = phoneNumber.value.trim();

    /* Phone is optional */

    if (value === "") {
      phoneNumber.classList.remove("input-error", "input-success");

      return true;
    }

    const phonePattern = /^[0-9+\-\s()]+$/;

    if (!phonePattern.test(value)) {
      showError(
        phoneNumber,
        "phoneNumberError",
        "Please enter a valid phone number.",
      );

      return false;
    }

    clearError(phoneNumber, "phoneNumberError");

    return true;
  }

  /* =========================================
       USERNAME VALIDATION
       ========================================= */

  function validateUsername() {
    const value = username.value.trim();

    if (value === "") {
      showError(username, "usernameError", "Please create a username.");

      return false;
    }

    if (value.length < 3) {
      showError(
        username,
        "usernameError",
        "Username must contain at least 3 characters.",
      );

      return false;
    }

    const usernamePattern = /^[A-Za-z0-9._-]+$/;

    if (!usernamePattern.test(value)) {
      showError(
        username,
        "usernameError",
        "Username can contain only letters, numbers, dot, underscore, or hyphen.",
      );

      return false;
    }

    clearError(username, "usernameError");

    return true;
  }

  /* =========================================
       PASSWORD VALIDATION
       ========================================= */

  function validatePassword() {
    const value = password.value;

    if (value === "") {
      showError(password, "passwordError", "Please create a password.");

      return false;
    }

    if (value.length < 8) {
      showError(
        password,
        "passwordError",
        "Password must contain at least 8 characters.",
      );

      return false;
    }

    clearError(password, "passwordError");

    return true;
  }

  /* =========================================
       CONFIRM PASSWORD VALIDATION
       ========================================= */

  function validateConfirmPassword() {
    const value = confirmPassword.value;

    if (value === "") {
      showError(
        confirmPassword,
        "confirmPasswordError",
        "Please confirm your password.",
      );

      return false;
    }

    if (value !== password.value) {
      showError(
        confirmPassword,
        "confirmPasswordError",
        "Passwords do not match.",
      );

      return false;
    }

    clearError(confirmPassword, "confirmPasswordError");

    return true;
  }

  /* =========================================
       FORM SUBMISSION
       ========================================= */

  form.addEventListener("submit", function (event) {
    /*
     * Stop the browser from submitting
     * before JavaScript validation.
     */

    event.preventDefault();

    /* Run all validations */

    const validFullName = validateFullName();

    const validDate = validateDateOfBirth();

    const validGender = validateGender();

    const validBloodGroup = validateBloodGroup();

    const validLastDonation = validateLastDonationDate();

    const validEmail = validateEmail();

    const validPhone = validatePhone();

    const validUsername = validateUsername();

    const validPassword = validatePassword();

    const validConfirmPassword = validateConfirmPassword();

    /* Check validation result */

    const formIsValid =
      validFullName &&
      validDate &&
      validGender &&
      validBloodGroup &&
      validLastDonation &&
      validEmail &&
      validPhone &&
      validUsername &&
      validPassword &&
      validConfirmPassword;

    /* Stop if validation failed */

    if (!formIsValid) {
      return;
    }

    /*
     * Everything is valid.
     *
     * Submit the form normally to PHP.
     */

    form.submit();
  });
});
