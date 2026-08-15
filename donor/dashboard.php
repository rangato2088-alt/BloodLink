<?php

session_start();

if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

$donorID = $_SESSION['donor_id'];


/* =========================================
   GET LOGGED-IN DONOR
========================================= */

$sql = "SELECT
            Donor_ID,
            Full_Name,
            Blood_Group,
            Date_Of_Birth,
            Gender,
            Address,
            Email,
            Phone_Number,
            Last_Donation_Date
        FROM donor
        WHERE Donor_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $donorID);

$stmt->execute();

$result = $stmt->get_result();

$donor = $result->fetch_assoc();

if (!$donor) {
    session_destroy();
    header("Location: login.php");
    exit;
}


/* =========================================
   UPCOMING BLOOD EVENTS
========================================= */

$upcomingEvents = null;

$eventQuery = "
    SELECT
        e.Event_ID,
        e.Event_Name,
        e.Event_Date,
        e.Event_Time,
        e.Venue,
        e.Description,
        ebg.Blood_Group
    FROM events e
    INNER JOIN event_blood_group ebg
        ON e.Event_ID = ebg.Event_ID
    WHERE e.Status = 'upcoming'
      AND e.Event_Date >= CURDATE()
      AND ebg.Blood_Group = ?
    ORDER BY e.Event_Date ASC, e.Event_Time ASC
    LIMIT 1
";

$eventStmt = $conn->prepare($eventQuery);

$eventStmt->bind_param("s", $donor['Blood_Group']);

$eventStmt->execute();

$eventResult = $eventStmt->get_result();

if ($eventResult && $eventResult->num_rows > 0) {
    $upcomingEvents = $eventResult->fetch_assoc();
}


/* =========================================
   DONATION HISTORY
   ONLY ATTENDED DONATIONS
========================================= */

$donationHistory = [];

$historyQuery = "
    SELECT
        er.Registration_ID,
        er.Event_ID,
        er.Donor_ID,
        er.Registration_Date,
        er.Attendance_Status,
        e.Event_Date,
        e.Event_Name,
        e.Hospital_ID,
        h.Hospital_Name
    FROM event_registration er

    INNER JOIN events e
        ON er.Event_ID = e.Event_ID

    LEFT JOIN hospital h
        ON e.Hospital_ID = h.Hospital_ID

    WHERE er.Donor_ID = ?
      AND er.Attendance_Status = 'Attended'

    ORDER BY e.Event_Date DESC
";

$historyStmt = $conn->prepare($historyQuery);

$historyStmt->bind_param("i", $donorID);

$historyStmt->execute();

$historyResult = $historyStmt->get_result();

while ($history = $historyResult->fetch_assoc()) {
    $donationHistory[] = $history;
}


/* =========================================
   TOTAL ATTENDED DONATIONS
========================================= */

$totalDonations = count($donationHistory);


/* =========================================
   GET LATEST 3 DONATIONS
========================================= */

$recentDonations = array_slice($donationHistory, 0, 3);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Donor Dashboard | BloodLink</title>

    <link
        rel="stylesheet"
        href="../css/donor-dashboard.css">

</head>


<body>


<!-- =========================================
     DASHBOARD HEADER
========================================= -->

<header class="dashboard-header">

    <div class="dashboard-logo">

        <img
            src="../img/bloodlink-logo.webp"
            alt="BloodLink Logo">

    </div>


    <div class="dashboard-user">

        <span>
            Welcome,
            <?php echo htmlspecialchars($donor['Full_Name']); ?>
        </span>

        <a href="logout.php">
            Logout
        </a>

    </div>

</header>



<!-- =========================================
     MAIN DASHBOARD
========================================= -->

<main class="dashboard-container">


    <!-- =========================================
         WELCOME SECTION
    ========================================= -->

    <section class="welcome-section">

        <h1>
            Welcome to Your Donor Dashboard
        </h1>

        <p>
            Manage your BloodLink donor account and donation information.
        </p>

    </section>



    <!-- =========================================
         SUMMARY CARDS
    ========================================= -->

    <section class="summary-cards">


        <!-- TOTAL DONATIONS -->

        <div class="summary-card">

            <div class="summary-icon">
                ♥
            </div>

            <div class="summary-content">

                <h2>
                    <?php echo $totalDonations; ?>
                </h2>

                <p>
                    Total Donations
                </p>

                <button
                    type="button"
                    onclick="openDonationHistory()">

                    View history →

                </button>

            </div>

        </div>



        <!-- BLOOD GROUP -->

        <div class="summary-card">

            <div class="summary-icon">
                ●
            </div>

            <div class="summary-content">

                <h2>
                    <?php
                    echo htmlspecialchars(
                        $donor['Blood_Group']
                    );
                    ?>
                </h2>

                <p>
                    Blood Group
                </p>

                <button type="button">
                    View profile →
                </button>

            </div>

        </div>



        <!-- LAST DONATION -->

        <div class="summary-card">

            <div class="summary-icon">
                □
            </div>

            <div class="summary-content">

                <h2>
                    120
                </h2>

                <p>
                    Days Since Last Donation
                </p>

                <button type="button">
                    See details →
                </button>

            </div>

        </div>



       

    </section>



    <!-- =========================================
         MAIN DASHBOARD CARDS
    ========================================= -->

    <section class="dashboard-main-cards">



        <!-- =====================================
             MY PROFILE
        ===================================== -->

        <div class="dashboard-card">


            <div class="card-header">

                <div class="card-icon">
                    ●
                </div>

                <div>

                    <h2>
                        My Profile
                    </h2>

                    <p>
                        View and manage your personal
                        information and donor details.
                    </p>

                </div>

            </div>



            <div class="profile-details">


                <!-- FULL NAME -->

                <div class="profile-row">

                    <span>
                        Full Name
                    </span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $donor['Full_Name']
                        );
                        ?>
                    </strong>

                </div>



                <!-- BLOOD GROUP -->

                <div class="profile-row">

                    <span>
                        Blood Group
                    </span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $donor['Blood_Group']
                        );
                        ?>
                    </strong>

                </div>



                <!-- EMAIL -->

                <div class="profile-row">

                    <span>
                        Email
                    </span>

                    <strong>

                        <?php

                        if (!empty($donor['Email'])) {

                            echo htmlspecialchars(
                                $donor['Email']
                            );

                        } else {

                            echo "Not provided";

                        }

                        ?>

                    </strong>

                </div>



                <!-- PHONE -->

                <div class="profile-row">

                    <span>
                        Phone
                    </span>

                    <strong>

                        <?php

                        if (!empty($donor['Phone_Number'])) {

                            echo htmlspecialchars(
                                $donor['Phone_Number']
                            );

                        } else {

                            echo "Not provided";

                        }

                        ?>

                    </strong>

                </div>



                <!-- ADDRESS -->

                <div class="profile-row">

                    <span>
                        Address
                    </span>

                    <strong>

                        <?php

                        if (!empty($donor['Address'])) {

                            echo htmlspecialchars(
                                $donor['Address']
                            );

                        } else {

                            echo "Not provided";

                        }

                        ?>

                    </strong>

                </div>

            </div>



            <button
                type="button"
                id="viewProfileButton">

                View Full Profile

            </button>


        </div>



        <!-- =====================================
             DONATION HISTORY
        ===================================== -->

        <div class="dashboard-card">


            <div class="card-header">

                <div class="card-icon">
                    ●
                </div>

                <div>

                    <h2>
                        Donation History
                    </h2>

                    <p>
                        View your previous donations
                        and donation records.
                    </p>

                </div>

            </div>



            <!-- =================================
                 LATEST 3 DONATIONS
            ================================= -->

            <div class="donation-list">


                <?php if (count($recentDonations) > 0): ?>


                    <?php foreach ($recentDonations as $history): ?>


                        <div class="donation-item">


                            <div class="donation-date">

                                <strong>
                                    <?php
                                    echo date(
                                        "d",
                                        strtotime(
                                            $history['Event_Date']
                                        )
                                    );
                                    ?>
                                </strong>

                                <span>
                                    <?php
                                    echo strtoupper(
                                        date(
                                            "M",
                                            strtotime(
                                                $history['Event_Date']
                                            )
                                        )
                                    );
                                    ?>
                                </span>

                                <small>
                                    <?php
                                    echo date(
                                        "Y",
                                        strtotime(
                                            $history['Event_Date']
                                        )
                                    );
                                    ?>
                                </small>

                            </div>



                            <div class="donation-info">

                                <strong>
                                    Blood Donation
                                </strong>

                                <span>

                                    <?php

                                    if (!empty(
                                        $history['Hospital_Name']
                                    )) {

                                        echo htmlspecialchars(
                                            $history['Hospital_Name']
                                        );

                                    } else {

                                        echo "Hospital";

                                    }

                                    ?>

                                </span>

                            </div>



                            <span class="status">
                                Attended
                            </span>


                        </div>


                    <?php endforeach; ?>


                <?php else: ?>


                    <div class="no-donation-history">

                        No donation history available.

                    </div>


                <?php endif; ?>


            </div>



            <!-- VIEW ALL HISTORY -->

            <button
                type="button"
                class="card-button"
                onclick="openDonationHistory()">

                View All History

            </button>


        </div>



        <!-- =====================================
             BLOOD REQUESTS
        ===================================== -->

        <div class="dashboard-card">


            <div class="card-header">

                <div class="card-icon">
                    ♥
                </div>

                <div>

                    <h2>
                        Blood Requests
                    </h2>

                    <p>
                        View upcoming blood donation events
                        that match your blood group.
                    </p>

                </div>

            </div>



            <?php if ($upcomingEvents): ?>


                <div class="request-box">


                    <div class="request-top">

                        <strong>
                            <?php
                            echo htmlspecialchars(
                                $upcomingEvents['Event_Name']
                            );
                            ?>
                        </strong>

                        <span>
                            <?php
                            echo date(
                                "d M Y",
                                strtotime(
                                    $upcomingEvents['Event_Date']
                                )
                            );
                            ?>
                        </span>

                    </div>



                    <div class="request-details">


                        <div>

                            <span>
                                Blood Group
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $upcomingEvents['Blood_Group']
                                );
                                ?>
                            </strong>

                        </div>



                        <div>

                            <span>
                                Location
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $upcomingEvents['Venue']
                                );
                                ?>
                            </strong>

                        </div>


                    </div>



                    <div class="request-needed">

                        <span>
                            Event
                        </span>

                        <strong>
                            <?php
                            echo htmlspecialchars(
                                $upcomingEvents['Description']
                            );
                            ?>
                        </strong>

                    </div>


                </div>



                <button
                    type="button"
                    class="card-button"
                    onclick="window.location.href='events.php'">

                    View All Requests

                </button>


            <?php else: ?>


                <div class="request-box no-request">


                    <strong>
                        No Upcoming Events
                    </strong>


                    <p>
                        There are currently no upcoming blood
                        donation events matching your blood group.
                    </p>


                </div>



                <button
                    type="button"
                    class="card-button"
                    onclick="window.location.href='events.php'">

                    View All Requests

                </button>


            <?php endif; ?>


        </div>


    </section>


</main>



<!-- =========================================
     FULL DONATION HISTORY POPUP
========================================= -->

<div
    id="donationHistoryPopup"
    class="history-popup-overlay">


    <div class="history-popup-card">


        <button
            type="button"
            class="history-popup-close"
            onclick="closeDonationHistory()">

            &times;

        </button>



        <h2>
            Donation History
        </h2>


        <p class="history-popup-subtitle">
            Your complete blood donation history
        </p>



        <div class="full-history-list">


            <?php if (count($donationHistory) > 0): ?>


                <?php foreach ($donationHistory as $history): ?>


                    <div class="full-history-item">


                        <div class="donation-date">

                            <strong>
                                <?php
                                echo date(
                                    "d",
                                    strtotime(
                                        $history['Event_Date']
                                    )
                                );
                                ?>
                            </strong>

                            <span>
                                <?php
                                echo strtoupper(
                                    date(
                                        "M",
                                        strtotime(
                                            $history['Event_Date']
                                        )
                                    )
                                );
                                ?>
                            </span>

                            <small>
                                <?php
                                echo date(
                                    "Y",
                                    strtotime(
                                        $history['Event_Date']
                                    )
                                );
                                ?>
                            </small>

                        </div>



                        <div class="donation-info">

                            <strong>
                                Blood Donation
                            </strong>

                            <span>

                                <?php

                                if (!empty(
                                    $history['Hospital_Name']
                                )) {

                                    echo htmlspecialchars(
                                        $history['Hospital_Name']
                                    );

                                } else {

                                    echo "Hospital";

                                }

                                ?>

                            </span>

                        </div>



                        <span class="status">
                            Attended
                        </span>


                    </div>


                <?php endforeach; ?>


            <?php else: ?>


                <p class="history-empty">
                    No donation history available.
                </p>


            <?php endif; ?>


        </div>


    </div>

</div>



<!-- =========================================
     FULL PROFILE POPUP
========================================= -->

<div
    class="profile-popup-overlay"
    id="profilePopup">


    <div class="profile-popup">


        <button
            type="button"
            class="profile-close"
            id="closeProfile">

            &times;

        </button>



        <h2>
            My Full Profile
        </h2>



        <p class="profile-popup-subtitle">
            Your BloodLink donor information
        </p>



        <div class="profile-popup-details">


            <div class="popup-row">

                <span>
                    Full Name
                </span>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $donor['Full_Name']
                    );
                    ?>
                </strong>

            </div>



            <div class="popup-row">

                <span>
                    Blood Group
                </span>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $donor['Blood_Group']
                    );
                    ?>
                </strong>

            </div>



            <div class="popup-row">

                <span>
                    Date of Birth
                </span>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $donor['Date_Of_Birth']
                    );
                    ?>
                </strong>

            </div>



            <div class="popup-row">

                <span>
                    Gender
                </span>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $donor['Gender']
                    );
                    ?>
                </strong>

            </div>



            <div class="popup-row">

                <span>
                    Address
                </span>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $donor['Address'] ?: 'Not provided'
                    );
                    ?>
                </strong>

            </div>



            <div class="popup-row">

                <span>
                    Email
                </span>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $donor['Email'] ?: 'Not provided'
                    );
                    ?>
                </strong>

            </div>



            <div class="popup-row">

                <span>
                    Phone Number
                </span>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $donor['Phone_Number'] ?: 'Not provided'
                    );
                    ?>
                </strong>

            </div>



            <div class="popup-row">

                <span>
                    Last Donation
                </span>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $donor['Last_Donation_Date']
                        ?: 'Never donated'
                    );
                    ?>
                </strong>

            </div>


        </div>


    </div>

</div>



<script src="../js/donor-dashboard.js"></script>


<script>

/* =========================================
   DONATION HISTORY POPUP
========================================= */

function openDonationHistory() {

    document
        .getElementById("donationHistoryPopup")
        .classList.add("active");

}


function closeDonationHistory() {

    document
        .getElementById("donationHistoryPopup")
        .classList.remove("active");

}


/* =========================================
   CLOSE HISTORY WHEN CLICKING OUTSIDE
========================================= */

document
    .getElementById("donationHistoryPopup")
    .addEventListener("click", function(event) {

        if (event.target === this) {

            closeDonationHistory();

        }

    });


</script>


</body>

</html>