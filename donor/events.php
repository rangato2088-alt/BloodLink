<?php

session_start();

require_once "../config/database.php";


/* =========================================================
   MAKE SURE DONOR IS LOGGED IN
========================================================= */

if (!isset($_SESSION["donor_id"])) {

    header("Location: ../login.php");
    exit;
}

$donorId = $_SESSION["donor_id"];


/* =========================================================
   GET LOGGED-IN DONOR INFORMATION
========================================================= */

$donorQuery = "
    SELECT
        Full_Name,
        Blood_Group
    FROM donor
    WHERE Donor_ID = ?
";

$stmt = $conn->prepare($donorQuery);

$stmt->bind_param(
    "i",
    $donorId
);

$stmt->execute();

$donorResult = $stmt->get_result();

$donor = $donorResult->fetch_assoc();


if (!$donor) {

    die("Donor information not found.");

}


$bloodGroup = $donor["Blood_Group"];


/* =========================================================
   CANCEL EVENT REGISTRATION
========================================================= */

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["cancel_event"])
) {

    header("Content-Type: application/json");

    $eventId = (int)$_POST["event_id"];


    /*
       Only a currently Registered event can be cancelled.
    */

    $cancelQuery = "
        UPDATE event_registration

        SET Attendance_Status = 'Cancelled'

        WHERE Event_ID = ?
        AND Donor_ID = ?
        AND Attendance_Status = 'Registered'
    ";


    $stmt = $conn->prepare($cancelQuery);


    $stmt->bind_param(
        "ii",
        $eventId,
        $donorId
    );


    if (
        $stmt->execute()
        && $stmt->affected_rows > 0
    ) {

        echo json_encode([
            "success" => true,
            "message" => "Your event registration has been cancelled."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Unable to cancel registration."
        ]);

    }


    exit;
}


/* =========================================================
   EVENT REGISTRATION PROCESS
========================================================= */

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["register_event"])
) {

    header("Content-Type: application/json");

    $eventId = (int)$_POST["event_id"];


    /* ---------------------------------------------------------
       CHECK EVENT AND BLOOD GROUP
    --------------------------------------------------------- */

    $eventCheck = "
        SELECT
            e.Event_ID,
            e.Event_Name,
            e.Event_Date,
            e.Event_Time,
            e.Venue

        FROM events e

        WHERE e.Event_ID = ?
        AND e.Status = 'upcoming'

        AND EXISTS (
            SELECT 1
            FROM event_blood_group ebg
            WHERE ebg.Event_ID = e.Event_ID
            AND (
                ebg.Blood_Group = ?
                OR ebg.Blood_Group = 'ALL'
            )
        )

        LIMIT 1
    ";


    $stmt = $conn->prepare($eventCheck);


    $stmt->bind_param(
        "is",
        $eventId,
        $bloodGroup
    );


    $stmt->execute();


    $eventResult = $stmt->get_result();

    $selectedEvent = $eventResult->fetch_assoc();


    if (!$selectedEvent) {

        echo json_encode([
            "success" => false,
            "message" => "This event is not available for your blood group."
        ]);

        exit;
    }


    /* ---------------------------------------------------------
       GET LAST DONATION
    --------------------------------------------------------- */

    $donationQuery = "
        SELECT
            MAX(Donation_Date) AS Last_Donation

        FROM donation_history

        WHERE Donor_ID = ?
    ";


    $stmt = $conn->prepare($donationQuery);


    $stmt->bind_param(
        "i",
        $donorId
    );


    $stmt->execute();


    $donationResult = $stmt->get_result();

    $donationData = $donationResult->fetch_assoc();


    $lastDonation = $donationData["Last_Donation"];


    /* ---------------------------------------------------------
       4 MONTH ELIGIBILITY CHECK
    --------------------------------------------------------- */

    if ($lastDonation) {

        $eligibleDate = date(
            "Y-m-d",
            strtotime($lastDonation . " +4 months")
        );


        if (
            $selectedEvent["Event_Date"]
            < $eligibleDate
        ) {

            echo json_encode([

                "success" => false,

                "eligible" => false,

                "lastDonation" => date(
                    "d M Y",
                    strtotime($lastDonation)
                ),

                "eligibleDate" => date(
                    "d M Y",
                    strtotime($eligibleDate)
                ),

                "eventDate" => date(
                    "d M Y",
                    strtotime($selectedEvent["Event_Date"])
                )

            ]);

            exit;
        }
    }


    /* ---------------------------------------------------------
       CHECK EXISTING REGISTRATION
    --------------------------------------------------------- */

    $alreadyQuery = "
        SELECT
            Registration_ID,
            Attendance_Status

        FROM event_registration

        WHERE Event_ID = ?
        AND Donor_ID = ?

        ORDER BY Registration_ID DESC

        LIMIT 1
    ";


    $stmt = $conn->prepare($alreadyQuery);


    $stmt->bind_param(
        "ii",
        $eventId,
        $donorId
    );


    $stmt->execute();


    $alreadyResult = $stmt->get_result();

    $existingRegistration =
        $alreadyResult->fetch_assoc();


    if ($existingRegistration) {


        if (
            $existingRegistration["Attendance_Status"]
            === "Registered"
        ) {

            echo json_encode([

                "success" => false,

                "message" =>
                    "You are already registered for this event."

            ]);

        } else {

            echo json_encode([

                "success" => false,

                "message" =>
                    "You have already cancelled your registration for this event."

            ]);

        }


        exit;
    }


    /* ---------------------------------------------------------
       REGISTER DONOR
    --------------------------------------------------------- */

    $registrationDate =
        date("Y-m-d H:i:s");


    $insertQuery = "
        INSERT INTO event_registration
        (
            Event_ID,
            Donor_ID,
            Registration_Date,
            Attendance_Status
        )

        VALUES
        (
            ?,
            ?,
            ?,
            'Registered'
        )
    ";


    $stmt = $conn->prepare($insertQuery);


    $stmt->bind_param(
        "iis",
        $eventId,
        $donorId,
        $registrationDate
    );


    if ($stmt->execute()) {

        /*
           Get the exact Registration_ID automatically created
           by MySQL for this new registration.
           No new database column or custom number is created.
        */
        $registrationId = $stmt->insert_id;

        echo json_encode([

            "success" => true,

            "message" =>
                "You have successfully registered for this event.",

            "registrationId" => (int)$registrationId

        ]);

    } else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Registration failed. Please try again."

        ]);

    }


    exit;
}


/* =========================================================
   GET EVENTS FOR DONOR
========================================================= */

/*
   EVENT VISIBILITY RULES

   1. Donor's own blood group event -> SHOW
   2. ALL blood group event -> SHOW TO EVERY DONOR
   3. Registered -> SHOW as Registered + Cancel Registration
   4. Cancelled -> SHOW until the event date passes
   5. Attended -> REMOVE
   6. Did Not Attend -> REMOVE
   7. Cancelled + event date passed -> REMOVE
*/

$eventQuery = "

    SELECT

        e.Event_ID,
        e.Event_Name,
        e.Event_Date,
        e.Event_Time,
        e.Venue,
        e.Description,
        e.Status,

        /*
           If the event is an ALL event, display the
           logged-in donor's own blood group on the card.
           Otherwise display the matching event group.
        */
        CASE
            WHEN EXISTS (
                SELECT 1
                FROM event_blood_group ebg_all
                WHERE ebg_all.Event_ID = e.Event_ID
                AND ebg_all.Blood_Group = 'ALL'
            )
            THEN ?
            ELSE (
                SELECT ebg_match.Blood_Group
                FROM event_blood_group ebg_match
                WHERE ebg_match.Event_ID = e.Event_ID
                AND ebg_match.Blood_Group = ?
                LIMIT 1
            )
        END AS Blood_Group,

        /* Latest registration status for this donor */
        COALESCE(
            (
                SELECT er.Attendance_Status
                FROM event_registration er
                WHERE er.Event_ID = e.Event_ID
                AND er.Donor_ID = ?
                ORDER BY er.Registration_ID DESC
                LIMIT 1
            ),
            'Upcoming'
        ) AS Attendance_Status

    FROM events e

    WHERE e.Status = 'upcoming'

    /*
       EXTRA SAFETY:
       Completed and Cancelled events must never appear
       on this Upcoming Events page.
    */
    AND e.Status NOT IN ('Completed', 'Cancelled')

    /*
       SHOW EVENT IF:
       - it matches donor blood group
       OR
       - it is an ALL event
    */
    AND (
        EXISTS (
            SELECT 1
            FROM event_blood_group ebg_match
            WHERE ebg_match.Event_ID = e.Event_ID
            AND ebg_match.Blood_Group = ?
        )

        OR

        EXISTS (
            SELECT 1
            FROM event_blood_group ebg_all
            WHERE ebg_all.Event_ID = e.Event_ID
            AND ebg_all.Blood_Group = 'ALL'
        )
    )

    /*
       REMOVE events already completed by this donor.
    */
    AND NOT EXISTS (
        SELECT 1
        FROM event_registration er2
        WHERE er2.Event_ID = e.Event_ID
        AND er2.Donor_ID = ?
        AND er2.Attendance_Status IN
        (
            'Attended',
            'Did Not Attend'
        )
    )

    /*
       A cancelled registration remains visible until
       the event date passes.
    */
    AND NOT EXISTS (
        SELECT 1
        FROM event_registration er3
        WHERE er3.Event_ID = e.Event_ID
        AND er3.Donor_ID = ?
        AND er3.Attendance_Status = 'Cancelled'
        AND e.Event_Date < CURDATE()
    )

    ORDER BY
        e.Event_Date ASC,
        e.Event_Time ASC
";

/*
   Parameters:
   1. Donor blood group -> display for ALL event
   2. Donor blood group -> display for matching event
   3. Donor ID          -> latest registration status
   4. Donor blood group -> event visibility
   5. Donor ID          -> attended/did-not-attend filter
   6. Donor ID          -> cancelled-after-event filter
*/
$stmt = $conn->prepare($eventQuery);

$stmt->bind_param(
    "ssisii",
    $bloodGroup,
    $bloodGroup,
    $donorId,
    $bloodGroup,
    $donorId,
    $donorId
);

$stmt->execute();

$events = $stmt->get_result();
?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Upcoming Blood Donation Events - BloodLink
    </title>


    <link
        rel="stylesheet"
        href="../css/event.css"
    >

</head>


<body>


<!-- =========================================================
     HEADER
========================================================= -->

<header class="event-header">


    <div class="event-logo">

        <a href="dashboard.php">

            <img
                src="../img/bloodlink-logo.webp"
                alt="BloodLink"
            >

        </a>

    </div>


    <div class="event-user">

        <span>

            Welcome,
            <?php
            echo htmlspecialchars(
                $donor["Full_Name"]
            );
            ?>

        </span>


        <a href="logout.php">
            Logout
        </a>

    </div>


</header>



<!-- =========================================================
     MAIN
========================================================= -->

<main class="event-container">


    <!-- =====================================================
         PAGE HEADER
    ====================================================== -->

    <section class="event-page-header">


        <h1>
            Upcoming Blood Donation Events
        </h1>


        <p>
            Find upcoming blood donation events that match your
            blood group and register to participate.
        </p>


    </section>



    <!-- =====================================================
         EVENTS
    ====================================================== -->

    <section class="events-section">


        <?php if ($events->num_rows > 0): ?>


            <div class="event-grid">


                <?php while ($event = $events->fetch_assoc()): ?>


                    <article class="event-card">


                        <!-- =================================================
                             CARD TOP
                        ================================================== -->

                        <div class="event-card-top">


                            <?php
                            /*
                               REGISTERED
                            */

                            if (
                                $event["Attendance_Status"]
                                === "Registered"
                            ):
                            ?>


                                <span
                                    class="event-status registered-status"
                                >

                                    Registered

                                </span>


                            <?php
                            /*
                               CANCELLED
                            */

                            elseif (
                                $event["Attendance_Status"]
                                === "Cancelled"
                            ):
                            ?>


                                <span
                                    class="event-status cancelled-status"
                                >

                                    Cancelled

                                </span>


                            <?php
                            /*
                               UPCOMING
                            */

                            else:
                            ?>


                                <span
                                    class="event-status"
                                >

                                    Upcoming

                                </span>


                            <?php endif; ?>


                            <span class="event-blood">

                                <?php

                                echo htmlspecialchars(
                                    $event["Blood_Group"]
                                );

                                ?>

                            </span>


                        </div>



                        <!-- =================================================
                             EVENT NAME
                        ================================================== -->

                        <h2>

                            <?php

                            echo htmlspecialchars(
                                $event["Event_Name"]
                            );

                            ?>

                        </h2>



                        <!-- =================================================
                             EVENT DETAILS
                        ================================================== -->

                        <div class="event-details">


                            <!-- DATE -->

                            <div class="event-detail">


                                <strong>
                                    Date
                                </strong>


                                <span>

                                    <?php

                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $event["Event_Date"]
                                        )
                                    );

                                    ?>

                                </span>


                            </div>



                            <!-- TIME -->

                            <div class="event-detail">


                                <strong>
                                    Time
                                </strong>


                                <span>

                                    <?php

                                    echo date(
                                        "h:i A",
                                        strtotime(
                                            $event["Event_Time"]
                                        )
                                    );

                                    ?>

                                </span>


                            </div>



                            <!-- LOCATION -->

                            <div class="event-detail">


                                <strong>
                                    Location
                                </strong>


                                <span>

                                    <?php

                                    echo htmlspecialchars(
                                        $event["Venue"]
                                    );

                                    ?>

                                </span>


                            </div>


                        </div>



                        <!-- =================================================
                             DESCRIPTION
                        ================================================== -->

                        <?php
                        if (
                            !empty(
                                $event["Description"]
                            )
                        ):
                        ?>


                            <div class="event-description">


                                <?php

                                echo htmlspecialchars(
                                    $event["Description"]
                                );

                                ?>


                            </div>


                        <?php endif; ?>



                        <!-- =================================================
                             EVENT ACTION BUTTON
                        ================================================== -->

                        <?php
                        /*
                           REGISTERED
                           Show Cancel Registration
                        */

                        if (
                            $event["Attendance_Status"]
                            === "Registered"
                        ):
                        ?>


                            <button
                                type="button"
                                class="cancel-event-button"
                                onclick="cancelEvent(
                                    <?php
                                    echo (int)$event["Event_ID"];
                                    ?>
                                )"
                            >

                                Cancel Registration

                            </button>


                        <?php
                        /*
                           CANCELLED
                           Disabled button
                        */

                        elseif (
                            $event["Attendance_Status"]
                            === "Cancelled"
                        ):
                        ?>


                            <button
                                type="button"
                                class="cancel-event-button cancelled-button"
                                disabled
                            >

                                Cancelled

                            </button>


                        <?php
                        /*
                           UPCOMING
                           Show Register Event
                        */

                        else:
                        ?>


                            <button
                                type="button"
                                class="register-event-button"
                                onclick="registerEvent(
                                    <?php
                                    echo (int)$event["Event_ID"];
                                    ?>
                                )"
                            >

                                Register Event

                            </button>


                        <?php endif; ?>


                    </article>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <!-- =================================================
                 NO EVENTS
            ================================================== -->

            <div class="no-events">


                <div class="no-events-icon">
                    ♥
                </div>


                <h2>
                    No Upcoming Events
                </h2>


                <p>
                    There are currently no upcoming blood donation
                    events matching your blood group.
                </p>


                <a href="dashboard.php">
                    Back to Dashboard
                </a>


            </div>


        <?php endif; ?>


    </section>


</main>



<!-- =========================================================
     EVENT POPUP
========================================================= -->

<div
    id="eventPopup"
    class="event-popup-overlay"
>


    <div class="event-popup-card">


        <button
            type="button"
            class="event-popup-close"
            onclick="closeEventPopup()"
        >

            &times;

        </button>


        <div id="eventPopupContent"></div>


    </div>


</div>



<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>


/* =========================================================
   REGISTER EVENT
========================================================= */

function registerEvent(eventId) {


    fetch("events.php", {

        method: "POST",

        headers: {

            "Content-Type":
                "application/x-www-form-urlencoded"

        },

        body:
            "register_event=1" +
            "&event_id=" +
            encodeURIComponent(eventId)

    })


    .then(response => response.json())


    .then(data => {


        /* =================================================
           NOT ELIGIBLE
        ================================================= */

        if (data.eligible === false) {


            showEventPopup(`

                <div class="event-popup-icon warning">
                    !
                </div>


                <h2>
                    Donation Not Available
                </h2>


                <p>
                    You are not currently eligible
                    to donate blood.
                </p>


                <div class="eligibility-box">


                    <div>

                        <span>
                            Last Donation
                        </span>

                        <strong>
                            ${data.lastDonation}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Eligible From
                        </span>

                        <strong>
                            ${data.eligibleDate}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Event Date
                        </span>

                        <strong>
                            ${data.eventDate}
                        </strong>

                    </div>


                </div>


                <p class="eligibility-message">

                    You can try registering for an event
                    on or after your eligible date.

                </p>


                <button
                    class="event-popup-button"
                    onclick="closeEventPopup()"
                >

                    Close

                </button>

            `);


            return;

        }



        /* =================================================
           OTHER ERROR
        ================================================= */

        if (!data.success) {


            showEventPopup(`

                <div class="event-popup-icon warning">
                    !
                </div>


                <h2>
                    Registration Unavailable
                </h2>


                <p>
                    ${data.message}
                </p>


                <button
                    class="event-popup-button"
                    onclick="closeEventPopup()"
                >

                    Close

                </button>

            `);


            return;

        }



        /* =================================================
           REGISTRATION SUCCESS
        ================================================= */

        showEventPopup(`


            <div class="success-popup-content">


                <div class="event-popup-icon success">
                    ✓
                </div>


                <h2>
                    Registration Successful
                </h2>


                <p class="success-message">

                    You have successfully registered
                    for this blood donation event.

                </p>


                <!-- =========================================
                     REGISTRATION ID
                ========================================== -->

                <div class="registration-id-box">

                    <span class="registration-id-label">
                        Your Registration Number
                    </span>

                    <strong class="registration-id-value">
                        ${data.registrationId}
                    </strong>

                </div>



                <!-- =========================================
                     DONATION INSTRUCTIONS
                ========================================== -->

                <div class="donation-instructions">


                    <h3>
                        Before You Donate
                    </h3>


                    <p class="instruction-intro">

                        Please follow these important instructions
                        before coming to the donation event.

                    </p>



                    <div class="instruction-item">

                        <span class="instruction-number">
                            1
                        </span>

                        <span>

                            Do not donate if you have a fever,
                            sore throat, cough, or stomach illness.

                        </span>

                    </div>



                    <div class="instruction-item">

                        <span class="instruction-number">
                            2
                        </span>

                        <span>

                            Get at least 6 hours of continuous sleep
                            the night before donation.

                        </span>

                    </div>



                    <div class="instruction-item">

                        <span class="instruction-number">
                            3
                        </span>

                        <span>

                            Eat a main meal within 4 hours before
                            donating blood.

                        </span>

                    </div>



                    <div class="instruction-item">

                        <span class="instruction-number">
                            4
                        </span>

                        <span>

                            Do not consume alcohol within 12–24 hours
                            before donating blood.

                        </span>

                    </div>



                    <div class="instruction-item">

                        <span class="instruction-number">
                            5
                        </span>

                        <span>

                            Do not smoke immediately before or after
                            donating blood.

                        </span>

                    </div>



                    <div class="instruction-item">

                        <span class="instruction-number">
                            6
                        </span>

                        <span>

                            Bring a valid identification document and
                            follow the instructions given by the blood
                            donation staff.

                        </span>

                    </div>


                </div>



                <button
                    class="event-popup-button"
                    onclick="closeEventPopup(); location.reload();"
                >

                    Done

                </button>


            </div>


        `);

    })


    .catch(() => {


        showEventPopup(`

            <div class="event-popup-icon warning">
                !
            </div>


            <h2>
                Something Went Wrong
            </h2>


            <p>
                Please try again.
            </p>


            <button
                class="event-popup-button"
                onclick="closeEventPopup()"
            >

                Close

            </button>

        `);

    });

}



/* =========================================================
   CANCEL EVENT
========================================================= */

function cancelEvent(eventId) {


    showEventPopup(`

        <div class="event-popup-icon warning">
            !
        </div>


        <h2>
            Cancel Registration?
        </h2>


        <p>

            Are you sure you want to cancel
            your registration for this event?

        </p>


        <button
            class="event-popup-button"
            onclick="confirmCancelEvent(${eventId})"
        >

            Yes, Cancel Registration

        </button>


        <button
            class="event-popup-button"
            onclick="closeEventPopup()"
            style="background:#64748b;"
        >

            Keep Registration

        </button>

    `);

}



/* =========================================================
   CONFIRM CANCELLATION
========================================================= */

function confirmCancelEvent(eventId) {


    fetch("events.php", {

        method: "POST",

        headers: {

            "Content-Type":
                "application/x-www-form-urlencoded"

        },

        body:
            "cancel_event=1" +
            "&event_id=" +
            encodeURIComponent(eventId)

    })


    .then(response => response.json())


    .then(data => {


        if (data.success) {


            showEventPopup(`

                <div class="event-popup-icon success">
                    ✓
                </div>


                <h2>
                    Registration Cancelled
                </h2>


                <p>

                    Your registration has been
                    cancelled successfully.

                </p>


                <button
                    class="event-popup-button"
                    onclick="location.reload();"
                >

                    Done

                </button>

            `);


        } else {


            showEventPopup(`

                <div class="event-popup-icon warning">
                    !
                </div>


                <h2>
                    Cancellation Failed
                </h2>


                <p>
                    ${data.message}
                </p>


                <button
                    class="event-popup-button"
                    onclick="closeEventPopup()"
                >

                    Close

                </button>

            `);

        }

    })


    .catch(() => {


        showEventPopup(`

            <div class="event-popup-icon warning">
                !
            </div>


            <h2>
                Something Went Wrong
            </h2>


            <p>
                Please try again.
            </p>


            <button
                class="event-popup-button"
                onclick="closeEventPopup()"
            >

                Close

            </button>

        `);

    });

}



/* =========================================================
   SHOW POPUP
========================================================= */

function showEventPopup(content) {


    document.getElementById(
        "eventPopupContent"
    ).innerHTML = content;


    document
        .getElementById("eventPopup")
        .classList.add("active");

}



/* =========================================================
   CLOSE POPUP
========================================================= */

function closeEventPopup() {


    document
        .getElementById("eventPopup")
        .classList.remove("active");

}


</script>


</body>

</html>