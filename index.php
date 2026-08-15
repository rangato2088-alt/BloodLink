<?php
// =========================================
// BLOODLINK HOME PAGE
// =========================================

require_once "config/database.php";


// =========================================
// HOMEPAGE STATISTICS
// =========================================

// Total registered donors
$donorCount = 0;

$donorQuery = "SELECT COUNT(*) AS total_donors FROM donor";

$donorResult = $conn->query($donorQuery);

if ($donorResult) {

    $donorData = $donorResult->fetch_assoc();

    $donorCount = (int)$donorData["total_donors"];
}


// Upcoming events count
$upcomingEventCount = 0;

$upcomingCountQuery = "
    SELECT COUNT(*) AS total_upcoming
    FROM events
    WHERE Status = 'Upcoming'
";

$upcomingCountResult = $conn->query($upcomingCountQuery);

if ($upcomingCountResult) {

    $upcomingCountData = $upcomingCountResult->fetch_assoc();

    $upcomingEventCount = (int)$upcomingCountData["total_upcoming"];
}


// Ongoing events count
$ongoingEventCount = 0;

$ongoingCountQuery = "
    SELECT COUNT(*) AS total_ongoing
    FROM events
    WHERE Status = 'Ongoing'
";

$ongoingCountResult = $conn->query($ongoingCountQuery);

if ($ongoingCountResult) {

    $ongoingCountData = $ongoingCountResult->fetch_assoc();

    $ongoingEventCount = (int)$ongoingCountData["total_ongoing"];
}


// =========================================
// PUBLIC EVENTS
// ONLY UPCOMING + ONGOING
// =========================================

$eventsQuery = "
    SELECT
        Event_Name,
        Event_Date,
        Event_Time,
        Venue,
        Status
    FROM events
    WHERE Status IN ('Upcoming', 'Ongoing')
    ORDER BY
        CASE
            WHEN Status = 'Ongoing' THEN 1
            WHEN Status = 'Upcoming' THEN 2
        END,
        Event_Date ASC,
        Event_Time ASC
";

$eventsResult = $conn->query($eventsQuery);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        BloodLink | Connecting Lives Through Blood
    </title>

    <link
        rel="stylesheet"
        href="css/home.css">

    <link
        rel="stylesheet"
        href="css/donor-eligibility.css">

</head>


<body>


<!-- =========================================
     HEADER
     ========================================= -->

<header class="main-header">

    <div class="header-container">


        <!-- LOGO -->

        <a
            href="#home"
            class="logo">

            <img
                src="img/bloodlink-logo.webp"
                alt="BloodLink Logo">

        </a>


        <!-- NAVIGATION -->

        <nav class="main-navigation">

            <a href="#home">
                Home
            </a>

            <a href="#events">
                Events
            </a>

            <a href="#how-it-works">
                How It Works
            </a>

            <a href="#contact">
                Contact
            </a>

        </nav>


        <!-- HEADER BUTTONS -->

        <div class="header-buttons">

            <a
                href="donor/login.php"
                class="login-button">

                Login

            </a>


            <button
                type="button"
                class="register-button"
                id="openEligibility">

                Register

            </button>

        </div>

    </div>

</header>



<!-- =========================================
     MAIN
     ========================================= -->

<main>


<!-- =========================================
     HERO SECTION
     ========================================= -->

<section
    class="hero-section"
    id="home">

    <div class="hero-container">


        <!-- HERO CONTENT -->

        <div class="hero-content">

            <p class="hero-label">
                CONNECTING DONORS WITH THOSE IN NEED
            </p>


            <h1>
                Every Drop Can
                <span>Save a Life.</span>
            </h1>


            <p class="hero-description">

                BloodLink connects generous blood donors
                with people who need life-saving blood.
                Together, we can make a difference.

            </p>


            <div class="hero-buttons">

                <button
                    type="button"
                    class="primary-button"
                    id="openEligibilityHero">

                    Become a Donor

                </button>


                <a
                    href="donor/login.php"
                    class="secondary-button">

                    Donor Login

                </a>

            </div>

        </div>



        <!-- =====================================
             HERO STATISTICS
             ===================================== -->

        <div class="hero-statistics">


            <div class="statistics-header">

                <span class="statistics-label">
                    BLOODLINK IMPACT
                </span>


                <h2>
                    Together We Make
                    <span>a Difference</span>
                </h2>


                <p>

                    Our growing community is helping
                    connect donors with people who need
                    blood.

                </p>

            </div>



            <div class="statistics-list">


                <!-- DONORS -->

                <div class="statistics-card">

                    <div class="statistics-icon">
                        ♥
                    </div>

                    <div class="statistics-content">

                        <strong>
                            <?php
                            echo number_format($donorCount);
                            ?>
                        </strong>

                        <span>
                            Registered Donors
                        </span>

                    </div>

                </div>



                <!-- UPCOMING EVENTS -->

                <div class="statistics-card">

                    <div class="statistics-icon">
                        +
                    </div>

                    <div class="statistics-content">

                        <strong>
                            <?php
                            echo number_format($upcomingEventCount);
                            ?>
                        </strong>

                        <span>
                            Upcoming Events
                        </span>

                    </div>

                </div>



                <!-- ONGOING EVENTS -->

                <div class="statistics-card">

                    <div class="statistics-icon">
                        ●
                    </div>

                    <div class="statistics-content">

                        <strong>
                            <?php
                            echo number_format($ongoingEventCount);
                            ?>
                        </strong>

                        <span>
                            Ongoing Events
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =========================================
     EVENTS SECTION
     ========================================= -->

<section
    class="events-section"
    id="events">


    <div class="events-container">


        <!-- SECTION HEADER -->

        <div class="events-section-header">

            <p class="section-label">
                BLOOD DONATION EVENTS
            </p>


            <h2>
                Upcoming &amp;
                <span>Ongoing Events</span>
            </h2>


            <p class="events-section-description">

                View current BloodLink blood donation
                events and find an opportunity to help
                save a life.

            </p>

        </div>



        <!-- EVENTS TABLE -->

        <div class="events-table-wrapper">

            <table class="events-table">


                <thead>

                    <tr>

                        <th>
                            Event Name
                        </th>

                        <th>
                            Event Date
                        </th>

                        <th>
                            Event Time
                        </th>

                        <th>
                            Venue
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php if ($eventsResult && $eventsResult->num_rows > 0): ?>


                    <?php while ($event = $eventsResult->fetch_assoc()): ?>


                        <tr>


                            <!-- EVENT NAME -->

                            <td
                                class="event-name-cell"
                                data-label="Event Name">

                                <?php
                                echo htmlspecialchars(
                                    $event["Event_Name"]
                                );
                                ?>

                            </td>



                            <!-- EVENT DATE -->

                            <td
                                data-label="Event Date">

                                <?php

                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $event["Event_Date"]
                                    )
                                );

                                ?>

                            </td>



                            <!-- EVENT TIME -->

                            <td
                                data-label="Event Time">

                                <?php

                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $event["Event_Time"]
                                    )
                                );

                                ?>

                            </td>



                            <!-- VENUE -->

                            <td
                                data-label="Venue">

                                <?php

                                echo htmlspecialchars(
                                    $event["Venue"]
                                );

                                ?>

                            </td>



                            <!-- STATUS -->

                            <td
                                data-label="Status">

                                <?php if ($event["Status"] === "Ongoing"): ?>

                                    <span
                                        class="public-event-status ongoing">

                                        Ongoing

                                    </span>

                                <?php else: ?>

                                    <span
                                        class="public-event-status upcoming">

                                        Upcoming

                                    </span>

                                <?php endif; ?>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="5"
                            class="no-public-events">

                            <div class="no-events-message">

                                <div class="no-events-icon">
                                    ♥
                                </div>

                                <h3>
                                    No Events Available
                                </h3>

                                <p>
                                    There are currently no
                                    upcoming or ongoing blood
                                    donation events.
                                </p>

                            </div>

                        </td>

                    </tr>


                <?php endif; ?>

                </tbody>

            </table>

        </div>


    </div>

</section>



<!-- =========================================
     HOW IT WORKS
     ========================================= -->

<section
    class="how-section"
    id="how-it-works">


    <div class="section-container">


        <p class="section-label">
            HOW IT WORKS
        </p>


        <h2>
            Help Save a Life in
            <span>Three Steps</span>
        </h2>



        <div class="steps-container">


            <div class="step-card">

                <div class="step-number">
                    01
                </div>

                <h3>
                    Register
                </h3>

                <p>

                    Create your BloodLink donor
                    account and provide your
                    basic information.

                </p>

            </div>



            <div class="step-card">

                <div class="step-number">
                    02
                </div>

                <h3>
                    Connect
                </h3>

                <p>

                    Connect with blood requests
                    and donation opportunities
                    in the community.

                </p>

            </div>



            <div class="step-card">

                <div class="step-number">
                    03
                </div>

                <h3>
                    Save a Life
                </h3>

                <p>

                    Donate blood and help someone
                    who needs it.

                </p>

            </div>


        </div>

    </div>

</section>



<!-- =========================================
     CONTACT / INQUIRY
     ========================================= -->

<section
    class="contact-section"
    id="contact">

    <div class="contact-container">

        <!-- LEFT: EXISTING CONTACT CONTENT -->

        <div class="contact-info">

            <p class="section-label">
                CONTACT BLOODLINK
            </p>

            <h2>
                Ready to Make a
                <span>Difference?</span>
            </h2>

            <p class="contact-description">

                Have a question, need assistance, or want
                to learn more about BloodLink? Send us an
                inquiry and our team will get back to you.

            </p>

            <div class="contact-info-list">

                <div class="contact-info-item">

                    <div class="contact-info-icon">
                        @
                    </div>

                    <div>
                        <strong>Email</strong>
                        <span>supunranga495@gmail.com</span>
                    </div>

                </div>

                <div class="contact-info-item">

                    <div class="contact-info-icon">
                        ♥
                    </div>

                    <div>
                        <strong>BloodLink Support</strong>
                        <span>We are here to help you.</span>
                    </div>

                </div>

            </div>

            <div class="contact-buttons">

                <button
                    type="button"
                    class="primary-button"
                    id="openEligibilityBottom">

                    Become a Donor

                </button>

                <a
                    href="donor/login.php"
                    class="secondary-button">

                    Login

                </a>

            </div>

        </div>


        <!-- RIGHT: INQUIRY FORM -->

        <div class="inquiry-card">

            <div class="inquiry-header">

                <h3>
                    Send an Inquiry
                </h3>

                <p>
                    Fill in the form below and our team
                    will get back to you.
                </p>

            </div>

            <div
                id="inquiryMessage"
                class="inquiry-message"
                role="alert"
                aria-live="polite">
            </div>

            <form
                id="inquiryForm"
                action="send_inquiry.php"
                method="POST"
                novalidate>

                <!-- Honeypot anti-spam field -->
                <div class="inquiry-honeypot" aria-hidden="true">
                    <label>
                        Leave this field empty
                        <input
                            type="text"
                            name="website"
                            tabindex="-1"
                            autocomplete="off">
                    </label>
                </div>

                <div class="inquiry-form-row">

                    <div class="inquiry-field">

                        <label for="inquiryName">
                            Full Name <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="inquiryName"
                            name="full_name"
                            placeholder="Enter your full name"
                            maxlength="100"
                            autocomplete="name"
                            required>

                    </div>


                    <div class="inquiry-field">

                        <label for="inquiryEmail">
                            Email Address <span>*</span>
                        </label>

                        <input
                            type="email"
                            id="inquiryEmail"
                            name="email"
                            placeholder="Enter your email address"
                            maxlength="150"
                            autocomplete="email"
                            required>

                    </div>

                </div>


                <div class="inquiry-form-row">

                    <div class="inquiry-field">

                        <label for="inquiryPhone">
                            Phone Number
                        </label>

                        <input
                            type="tel"
                            id="inquiryPhone"
                            name="phone"
                            placeholder="Enter your phone number"
                            maxlength="30"
                            autocomplete="tel">

                    </div>


                    <div class="inquiry-field">

                        <label for="inquirySubject">
                            Subject <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="inquirySubject"
                            name="subject"
                            placeholder="Enter your subject"
                            maxlength="150"
                            required>

                    </div>

                </div>


                <div class="inquiry-field">

                    <label for="inquiryText">
                        Message <span>*</span>
                    </label>

                    <textarea
                        id="inquiryText"
                        name="message"
                        rows="6"
                        placeholder="Write your inquiry here..."
                        maxlength="3000"
                        required></textarea>

                </div>


                <button
                    type="submit"
                    class="inquiry-submit-button"
                    id="inquirySubmitButton">

                    <span id="inquiryButtonText">
                        Send Inquiry
                    </span>

                </button>

            </form>

        </div>

    </div>

</section>


</main>



<!-- =========================================
     FOOTER
     ========================================= -->

<footer class="main-footer">

    <div class="footer-container">


        <img
            src="img/bloodlink-logo.webp"
            alt="BloodLink Logo">


        <p>

            © 2026 BloodLink. Connecting lives
            through blood donation.

        </p>


    </div>

</footer>



<!-- =========================================
     DONOR ELIGIBILITY POPUP
     ========================================= -->

<div
    class="eligibility-overlay"
    id="eligibilityOverlay">


    <div class="eligibility-card">


        <button
            type="button"
            class="close-eligibility"
            id="closeEligibility">

            &times;

        </button>


        <div class="eligibility-header">

            <img
                src="img/bloodlink-logo.webp"
                alt="BloodLink Logo">


            <h2>
                Donor Eligibility Check
            </h2>


            <p>

                Please answer these questions honestly
                before continuing with registration.

            </p>

        </div>


        <div class="progress-area">

            <div class="progress-text">

                Question
                <span id="questionNumber">1</span>
                of 4

            </div>


            <div class="progress-bar">

                <div
                    class="progress-fill"
                    id="progressFill">
                </div>

            </div>

        </div>


        <div
            class="question-area"
            id="questionArea">


            <h3 id="questionText">
                Question will appear here.
            </h3>


            <div class="answer-buttons">


                <button
                    type="button"
                    class="answer-button"
                    id="noButton">

                    No

                </button>


                <button
                    type="button"
                    class="answer-button"
                    id="yesButton">

                    Yes

                </button>


            </div>

        </div>


        <div
            class="eligibility-result"
            id="eligibilityResult">


            <div
                class="result-icon"
                id="resultIcon">

                !

            </div>


            <h3 id="resultTitle">
                Thank you
            </h3>


            <p id="resultMessage">
            </p>


            <button
                type="button"
                class="result-button"
                id="continueRegistration">

                Continue to Registration

            </button>


            <button
                type="button"
                class="result-close-button"
                id="closeResult">

                Close

            </button>


        </div>

    </div>

</div>




<script>

const inquiryForm = document.getElementById("inquiryForm");
const inquiryMessage = document.getElementById("inquiryMessage");
const inquirySubmitButton = document.getElementById("inquirySubmitButton");
const inquiryButtonText = document.getElementById("inquiryButtonText");

if (inquiryForm) {

    inquiryForm.addEventListener("submit", async function (event) {

        event.preventDefault();

        inquiryMessage.className = "inquiry-message";
        inquiryMessage.textContent = "";

        if (!inquiryForm.checkValidity()) {

            inquiryForm.reportValidity();

            return;
        }

        inquirySubmitButton.disabled = true;
        inquiryButtonText.textContent = "Sending...";

        try {

            const response = await fetch("send_inquiry.php", {

                method: "POST",
                body: new FormData(inquiryForm),
                headers: {
                    "Accept": "application/json"
                }

            });

            const data = await response.json();

            if (data.success) {

                inquiryMessage.className =
                    "inquiry-message success";

                inquiryMessage.textContent =
                    data.message;

                inquiryForm.reset();

            } else {

                inquiryMessage.className =
                    "inquiry-message error";

                inquiryMessage.textContent =
                    data.message ||
                    "Unable to send your inquiry.";

            }

        } catch (error) {

            inquiryMessage.className =
                "inquiry-message error";

            inquiryMessage.textContent =
                "Something went wrong. Please try again later.";

        } finally {

            inquirySubmitButton.disabled = false;
            inquiryButtonText.textContent = "Send Inquiry";

        }

    });

}

</script>


<script src="js/donor-eligibility.js"></script>


</body>

</html>