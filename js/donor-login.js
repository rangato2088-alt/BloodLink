/*BLOODLINK DONOR LOGIN VALIDATION*/

document.addEventListener("DOMContentLoaded", function () {
  /* Get form elements */

  const loginForm = document.getElementById("donorLoginForm");

  const username = document.getElementById("username");

  const password = document.getElementById("password");

  const usernameError = document.getElementById("usernameError");

  const passwordError = document.getElementById("passwordError");

  const togglePassword = document.getElementById("togglePassword");

  /*SHOW / HIDE PASSWOR*/

  togglePassword.addEventListener("click", function () {
    if (password.type === "password") {
      password.type = "text";

      togglePassword.textContent = "Hide";
    } else {
      password.type = "password";

      togglePassword.textContent = "Show";
    }
  });

  /*USERNAME VALIDATIO*/

  function validateUsername() {
    const value = username.value.trim();

    username.classList.remove("input-error", "input-success");

    usernameError.textContent = "";

    usernameError.classList.remove("show");

    /* Empty username */

    if (value === "") {
      username.classList.add("input-error");

      usernameError.textContent = "Please enter your username.";

      usernameError.classList.add("show");

      return false;
    }

    /* Minimum length */

    if (value.length < 3) {
      username.classList.add("input-error");

      usernameError.textContent =
        "Username must contain at least 3 characters.";

      usernameError.classList.add("show");

      return false;
    }

    /* Maximum length */

    if (value.length > 50) {
      username.classList.add("input-error");

      usernameError.textContent = "Username cannot exceed 50 characters.";

      usernameError.classList.add("show");

      return false;
    }

    /* Allowed characters */

    const usernamePattern = /^[A-Za-z0-9._-]+$/;

    if (!usernamePattern.test(value)) {
      username.classList.add("input-error");

      usernameError.textContent =
        "Username can contain only letters, numbers, dot, underscore, or hyphen.";

      usernameError.classList.add("show");

      return false;
    }

    /* Valid */

    username.classList.add("input-success");

    return true;
  }

  /*PASSWORD VALIDATION*/

  function validatePassword() {
    const value = password.value;

    password.classList.remove("input-error", "input-success");

    passwordError.textContent = "";

    passwordError.classList.remove("show");

    /* Empty password */

    if (value === "") {
      password.classList.add("input-error");

      passwordError.textContent = "Please enter your password.";

      passwordError.classList.add("show");

      return false;
    }

    /* Minimum password length */

    if (value.length < 8) {
      password.classList.add("input-error");

      passwordError.textContent =
        "Password must contain at least 8 characters.";

      passwordError.classList.add("show");

      return false;
    }

    /* Maximum password length */

    if (value.length > 72) {
      password.classList.add("input-error");

      passwordError.textContent = "Password cannot exceed 72 characters.";

      passwordError.classList.add("show");

      return false;
    }

    /* Valid */

    password.classList.add("input-success");

    return true;
  }

  /*VALIDATE WHEN LEAVING INPUT*/

  username.addEventListener("blur", validateUsername);

  password.addEventListener("blur", validatePassword);

  /*LIVE VALIDATION*/

  username.addEventListener("input", function () {
    if (username.value.trim() !== "") {
      validateUsername();
    }
  });

  password.addEventListener("input", function () {
    if (password.value !== "") {
      validatePassword();
    }
  });

  /*FORM SUBMIT*/

  loginForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const usernameValid = validateUsername();

    const passwordValid = validatePassword();

    /* Stop if validation fails */

    if (!usernameValid || !passwordValid) {
      return;
    }

    /*
               Database login authentication
               will be added later.
            */

    alert("Form validation successful.");
  });
});
