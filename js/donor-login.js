document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("donorLoginForm");

  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");

  const usernameError = document.getElementById("usernameError");
  const passwordError = document.getElementById("passwordError");

  const togglePassword = document.getElementById("togglePassword");

  /* SHOW / HIDE PASSWORD */

  togglePassword.addEventListener("click", function () {
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      togglePassword.textContent = "Hide";
    } else {
      passwordInput.type = "password";
      togglePassword.textContent = "Show";
    }
  });

  /*USERNAME VALIDATION*/

  function validateUsername() {
    const username = usernameInput.value.trim();

    usernameInput.classList.remove("input-error", "input-success");

    usernameError.textContent = "";
    usernameError.classList.remove("show");

    if (username === "") {
      usernameInput.classList.add("input-error");

      usernameError.textContent = "Please enter your username.";

      usernameError.classList.add("show");

      return false;
    }

    if (username.length < 3) {
      usernameInput.classList.add("input-error");

      usernameError.textContent =
        "Username must contain at least 3 characters.";

      usernameError.classList.add("show");

      return false;
    }

    if (username.length > 50) {
      usernameInput.classList.add("input-error");

      usernameError.textContent = "Username cannot exceed 50 characters.";

      usernameError.classList.add("show");

      return false;
    }

    if (!/^[A-Za-z0-9._-]+$/.test(username)) {
      usernameInput.classList.add("input-error");

      usernameError.textContent =
        "Username can contain only letters, numbers, dot, underscore, or hyphen.";

      usernameError.classList.add("show");

      return false;
    }

    usernameInput.classList.add("input-success");

    return true;
  }

  /*PASSWORD VALIDATION*/

  function validatePassword() {
    const password = passwordInput.value;

    passwordInput.classList.remove("input-error", "input-success");

    passwordError.textContent = "";
    passwordError.classList.remove("show");

    if (password === "") {
      passwordInput.classList.add("input-error");

      passwordError.textContent = "Please enter your password.";

      passwordError.classList.add("show");

      return false;
    }

    if (password.length < 8) {
      passwordInput.classList.add("input-error");

      passwordError.textContent =
        "Password must contain at least 8 characters.";

      passwordError.classList.add("show");

      return false;
    }

    if (password.length > 72) {
      passwordInput.classList.add("input-error");

      passwordError.textContent = "Password cannot exceed 72 characters.";

      passwordError.classList.add("show");

      return false;
    }

    passwordInput.classList.add("input-success");

    return true;
  }

  /* LIVE VALIDATION  */

  usernameInput.addEventListener("input", function () {
    if (usernameInput.value !== "") {
      validateUsername();
    }
  });

  passwordInput.addEventListener("input", function () {
    if (passwordInput.value !== "") {
      validatePassword();
    }
  });

  /*BLUR VALIDATION */

  usernameInput.addEventListener("blur", validateUsername);

  passwordInput.addEventListener("blur", validatePassword);

  /* FORM SUBMIT */

  loginForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const usernameValid = validateUsername();
    const passwordValid = validatePassword();

    if (!usernameValid || !passwordValid) {
      return;
    }

    alert("Validation successful.");
  });
});
