/* =========================================
   BLOODLINK DONOR ELIGIBILITY PRE-SCREEN
   ========================================= */

document.addEventListener("DOMContentLoaded", function () {
  /* =========================================
       OPEN / CLOSE BUTTONS
       ========================================= */

  const openButton = document.getElementById("openEligibility");

  const openHeroButton = document.getElementById("openEligibilityHero");

  const openBottomButton = document.getElementById("openEligibilityBottom");

  const closeButton = document.getElementById("closeEligibility");

  const closeResultButton = document.getElementById("closeResult");

  const overlay = document.getElementById("eligibilityOverlay");

  /* =========================================
       QUESTION ELEMENTS
       ========================================= */

  const questionArea = document.getElementById("questionArea");

  const questionText = document.getElementById("questionText");

  const questionNumber = document.getElementById("questionNumber");

  const progressFill = document.getElementById("progressFill");

  const yesButton = document.getElementById("yesButton");

  const noButton = document.getElementById("noButton");

  /* =========================================
       RESULT ELEMENTS
       ========================================= */

  const resultArea = document.getElementById("eligibilityResult");

  const resultTitle = document.getElementById("resultTitle");

  const resultMessage = document.getElementById("resultMessage");

  const continueButton = document.getElementById("continueRegistration");

  /* =========================================
       4 ELIGIBILITY QUESTIONS
       
       Expected answer for ALL questions:
       NO

       If the donor answers YES to any
       question, the pre-screen stops.
       ========================================= */

  const questions = [
    {
      question:
        "Have you ever had heart disease, a bleeding disorder, unexplained weight loss, or cancer?",

      expectedAnswer: "no",
    },

    {
      question:
        "Have you ever tested positive for HIV, Hepatitis B, Hepatitis C, or Syphilis?",

      expectedAnswer: "no",
    },

    {
      question:
        "Have you ever injected recreational drugs, non-prescription steroids, or cosmetic fillers using shared or unverified needles?",

      expectedAnswer: "no",
    },

    {
      question:
        "Have you had high-risk sexual activity, such as commercial sex work, multiple sexual partners, or sex with an intravenous drug user?",

      expectedAnswer: "no",
    },
  ];

  /* =========================================
       CURRENT QUESTION
       ========================================= */

  let currentQuestion = 0;

  /* =========================================
       OPEN ELIGIBILITY POPUP
       ========================================= */

  function openEligibility() {
    currentQuestion = 0;

    questionArea.style.display = "block";

    resultArea.classList.remove("active");

    overlay.classList.add("active");

    showQuestion();
  }

  /* =========================================
       DISPLAY QUESTION
       ========================================= */

  function showQuestion() {
    const current = questions[currentQuestion];

    questionText.textContent = current.question;

    questionNumber.textContent = currentQuestion + 1;

    const progress = ((currentQuestion + 1) / questions.length) * 100;

    progressFill.style.width = progress + "%";
  }

  /* =========================================
       CHECK ANSWER
       ========================================= */

  function checkAnswer(answer) {
    const current = questions[currentQuestion];

    /*
     * All four questions expect
     * the answer "No".
     */

    if (answer !== current.expectedAnswer) {
      showNotEligible();

      return;
    }

    /*
     * Correct answer.
     * Move to next question.
     */

    currentQuestion++;

    /*
     * All 4 questions completed.
     */

    if (currentQuestion >= questions.length) {
      showEligible();

      return;
    }

    showQuestion();
  }

  /* =========================================
       NOT ELIGIBLE MESSAGE
       ========================================= */

  function showNotEligible() {
    questionArea.style.display = "none";

    resultArea.classList.add("active");

    resultTitle.textContent = "Thank You for Your Honesty";

    resultMessage.textContent =
      "Thank you for your kindness and willingness to help save lives. Based on your answers, you are not eligible to donate blood at the moment. You may be able to donate at a later time. Please contact your local blood service for further guidance.";

    /*
     * Do not allow the donor to continue
     * to the registration form.
     */

    continueButton.style.display = "none";
  }

  /* =========================================
       ELIGIBLE MESSAGE
       ========================================= */

  function showEligible() {
    questionArea.style.display = "none";

    resultArea.classList.add("active");

    resultTitle.textContent = "Eligibility Check Completed";

    resultMessage.textContent =
      "Thank you for completing the eligibility check. Based on your answers, you can continue to the BloodLink donor registration form. Final eligibility will be confirmed by the blood service.";

    /*
     * Allow donor to continue.
     */

    continueButton.style.display = "block";
  }

  /* =========================================
       CLOSE POPUP
       ========================================= */

  function closeEligibility() {
    overlay.classList.remove("active");

    questionArea.style.display = "block";

    resultArea.classList.remove("active");
  }

  /* =========================================
       HEADER REGISTER BUTTON
       ========================================= */

  if (openButton) {
    openButton.addEventListener("click", openEligibility);
  }

  /* =========================================
       HERO REGISTER BUTTON
       ========================================= */

  if (openHeroButton) {
    openHeroButton.addEventListener("click", openEligibility);
  }

  /* =========================================
       BOTTOM REGISTER BUTTON
       ========================================= */

  if (openBottomButton) {
    openBottomButton.addEventListener("click", openEligibility);
  }

  /* =========================================
       CLOSE BUTTON
       ========================================= */

  if (closeButton) {
    closeButton.addEventListener("click", closeEligibility);
  }

  if (closeResultButton) {
    closeResultButton.addEventListener("click", closeEligibility);
  }

  /* =========================================
       YES BUTTON
       ========================================= */

  if (yesButton) {
    yesButton.addEventListener("click", function () {
      checkAnswer("yes");
    });
  }

  /* =========================================
       NO BUTTON
       ========================================= */

  if (noButton) {
    noButton.addEventListener("click", function () {
      checkAnswer("no");
    });
  }

  /* =========================================
       CONTINUE TO REGISTRATION
       ========================================= */

  if (continueButton) {
    continueButton.addEventListener("click", function () {
      window.location.href = "donor/register.php";
    });
  }

  /* =========================================
       ESCAPE KEY CLOSE
       ========================================= */

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && overlay.classList.contains("active")) {
      closeEligibility();
    }
  });
});
