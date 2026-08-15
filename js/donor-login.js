/* =========================================
   BLOODLINK DONOR LOGIN
   ========================================= */

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("donorLoginForm");

  const username = document.getElementById("username");

  const password = document.getElementById("password");

  const togglePassword = document.getElementById("togglePassword");

  /* =========================================
       CHECK FORM
       ========================================= */

  if (!form) {
    console.error("Donor login form was not found.");
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
       USERNAME VALIDATION
       ========================================= */

  function validateUsername() {
    const value = username.value.trim();

    if (value === "") {
      showError(username, "usernameError", "Please enter your username.");

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
      showError(password, "passwordError", "Please enter your password.");

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
       FORM SUBMISSION
       ========================================= */

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    const validUsername = validateUsername();

    const validPassword = validatePassword();

    if (!validUsername || !validPassword) {
      return;
    }

    /*
     * Validation successful.
     *
     * Send the form to PHP.
     */

    HTMLFormElement.prototype.submit.call(form);
  });
});
