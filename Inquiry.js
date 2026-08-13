const form = document.getElementById("inquiryForm");

const nameInput = document.getElementById("name");
const emailInput = document.getElementById("email");
const phoneInput = document.getElementById("phone");
const inquiryType = document.getElementById("inquiryType");
const subjectInput = document.getElementById("subject");
const messageInput = document.getElementById("message");
const agreement = document.getElementById("agreement");
const submitButton = document.getElementById("submitButton");

const charCount = document.getElementById("charCount");

const formStatus = document.getElementById("formStatus");
const submissionStatus = new URLSearchParams(window.location.search).get("status");
const statusMessages = {
    sent: "Your inquiry was sent successfully.",
    invalid: "Please check the form and submit it again.",
    "configuration-error": "Email delivery has not been configured on this server.",
    "send-error": "The message could not be sent. Please try again later."
};

if (formStatus && submissionStatus && statusMessages[submissionStatus]) {
    formStatus.textContent = statusMessages[submissionStatus];
    formStatus.classList.add(submissionStatus === "sent" ? "success" : "failure");
    formStatus.hidden = false;
    history.replaceState({}, document.title, window.location.pathname);
}


/* =========================
   MESSAGE CHARACTER COUNT
========================= */

messageInput.addEventListener("input", function () {

    const length = messageInput.value.length;

    charCount.textContent =
        `${length} / 500`;

});


/* =========================
   PHONE - ONLY NUMBERS
========================= */

phoneInput.addEventListener("input", function () {

    this.value =
        this.value.replace(/[^0-9]/g, "");

});


/* =========================
   FORM VALIDATION
========================= */

form.addEventListener("submit", function (event) {

    let isValid = true;

    clearErrors();


    /* NAME */

    const name = nameInput.value.trim();

    if (name === "") {

        showError(
            nameInput,
            "nameError",
            "Full name is required."
        );

        isValid = false;

    } else if (name.length < 3) {

        showError(
            nameInput,
            "nameError",
            "Name must contain at least 3 characters."
        );

        isValid = false;
    }


    /* EMAIL */

    const email =
        emailInput.value.trim();

    const emailPattern =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email === "") {

        showError(
            emailInput,
            "emailError",
            "Email address is required."
        );

        isValid = false;

    } else if (!emailPattern.test(email)) {

        showError(
            emailInput,
            "emailError",
            "Please enter a valid email address."
        );

        isValid = false;
    }


    /* PHONE */

    const phone =
        phoneInput.value.trim();

    const phonePattern =
        /^0[0-9]{9}$/;

    if (
        phone !== "" &&
        !phonePattern.test(phone)
    ) {

        showError(
            phoneInput,
            "phoneError",
            "Enter a valid 10-digit phone number."
        );

        isValid = false;
    }


    /* INQUIRY TYPE */

    if (inquiryType.value === "") {

        showError(
            inquiryType,
            "inquiryTypeError",
            "Please select an inquiry type."
        );

        isValid = false;
    }


    /* SUBJECT */

    const subject =
        subjectInput.value.trim();

    if (subject === "") {

        showError(
            subjectInput,
            "subjectError",
            "Subject is required."
        );

        isValid = false;

    } else if (subject.length < 5) {

        showError(
            subjectInput,
            "subjectError",
            "Subject must contain at least 5 characters."
        );

        isValid = false;
    }


    /* MESSAGE */

    const message =
        messageInput.value.trim();

    if (message === "") {

        showError(
            messageInput,
            "messageError",
            "Please enter your inquiry."
        );

        isValid = false;

    } else if (message.length < 10) {

        showError(
            messageInput,
            "messageError",
            "Message must contain at least 10 characters."
        );

        isValid = false;
    }


    /* AGREEMENT */

    if (!agreement.checked) {

        document.getElementById(
            "agreementError"
        ).textContent =
            "Please confirm the information before submitting.";

        isValid = false;
    }


    /* STOP SUBMISSION */

    if (!isValid) {
        event.preventDefault();
        const firstInvalid = form.querySelector(".invalid");
        if (firstInvalid) {
            firstInvalid.focus();
        }
        return;
    }

    submitButton.disabled = true;
    submitButton.querySelector(".button-label").textContent = "Sending...";

});


/* =========================
   SHOW ERROR
========================= */

function showError(
    input,
    errorId,
    message
) {

    document.getElementById(
        errorId
    ).textContent = message;

    input.classList.add("invalid");
}


/* =========================
   CLEAR ERRORS
========================= */

function clearErrors() {

    const errors =
        document.querySelectorAll(".error");

    errors.forEach(function (error) {
        error.textContent = "";
    });


    const inputs =
        document.querySelectorAll(
            "input, select, textarea"
        );

    inputs.forEach(function (input) {
        input.classList.remove("invalid");
    });
}
