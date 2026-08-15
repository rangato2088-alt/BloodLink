/* =========================================
   BLOODLINK DONOR DASHBOARD
   PROFILE POPUP
   ========================================= */

document.addEventListener("DOMContentLoaded", function () {
  const viewProfileButton = document.getElementById("viewProfileButton");

  const profilePopup = document.getElementById("profilePopup");

  const closeProfile = document.getElementById("closeProfile");

  /* Check elements */

  if (!viewProfileButton || !profilePopup || !closeProfile) {
    console.error("Profile popup elements were not found.");
    return;
  }

  /* Open popup */

  viewProfileButton.addEventListener("click", function () {
    profilePopup.classList.add("active");
  });

  /* Close popup */

  closeProfile.addEventListener("click", function () {
    profilePopup.classList.remove("active");
  });

  /* Close when clicking outside card */

  profilePopup.addEventListener("click", function (event) {
    if (event.target === profilePopup) {
      profilePopup.classList.remove("active");
    }
  });

  /* Close with Escape key */

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && profilePopup.classList.contains("active")) {
      profilePopup.classList.remove("active");
    }
  });
});
