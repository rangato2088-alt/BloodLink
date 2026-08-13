<?php

session_start();

if (!isset($_SESSION["hospital_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../admin/include/db.php";

$hospital_id = $_SESSION["hospital_id"];


// Validate required parameters

if (
    !isset($_GET["registration_id"]) ||
    !isset($_GET["event_id"]) ||
    !is_numeric($_GET["registration_id"]) ||
    !is_numeric($_GET["event_id"])
) {
    header("Location: events.php");
    exit();
}


$registration_id = (int) $_GET["registration_id"];
$event_id = (int) $_GET["event_id"];



$sql = "SELECT er.Registration_ID,
               er.Donor_ID,
               er.Event_ID,
               er.Attendance_Status,
               d.Full_Name,
               d.Blood_Group,
               e.Event_Name
        FROM event_registration er

        INNER JOIN donor d
            ON er.Donor_ID = d.Donor_ID

        INNER JOIN events e
            ON er.Event_ID = e.Event_ID

        WHERE er.Registration_ID = ?
        AND er.Event_ID = ?
        AND e.Hospital_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "iii",
    $registration_id,
    $event_id,
    $hospital_id
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: events.php");
    exit();

}


$registration = $result->fetch_assoc();

$stmt->close();


// Donation can only be recorded after attendance

if ($registration["Attendance_Status"] !== "Attended") {

    header(
        "Location: event-attendance.php?event_id="
        . $event_id
    );

    exit();

}


// Check whether donation has already been recorded

$sql = "SELECT Donation_ID
        FROM donation_history
        WHERE Donor_ID = ?
        AND Hospital_ID = ?
        AND Event_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "iii",
    $registration["Donor_ID"],
    $hospital_id,
    $event_id
);

$stmt->execute();

$result = $stmt->get_result();

$already_recorded = ($result->num_rows > 0);

$stmt->close();


// Record donation

if ($_SERVER["REQUEST_METHOD"] === "POST" && !$already_recorded) {

    $donor_id = $registration["Donor_ID"];
    $donation_date = date("Y-m-d");

    $sql = "INSERT INTO donation_history
            (
                Donor_ID,
                Hospital_ID,
                Event_ID,
                Donation_Date
            )
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iiis",
        $donor_id,
        $hospital_id,
        $event_id,
        $donation_date
    );


    if ($stmt->execute()) {

        $stmt->close();

        header("Location: donations.php");
        exit();

    }

    $stmt->close();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Record Donation | BloodLink</title>

    <link rel="stylesheet" href="assets/css/hospital-dashboard.css">

</head>

<body>

    <div class="dashboard-container">


        <aside class="sidebar">

            <div class="sidebar-logo">

                <img
                    src="assets/image/BloodLink_logo_800px_transparent.webp"
                    alt="BloodLink Logo"
                >

            </div>

            <div class="hospital-label">
                Hospital Portal
            </div>


            <nav class="sidebar-nav">

                <a href="dashboard.php" class="nav-link">
                    Dashboard
                </a>

                <a href="events.php" class="nav-link">
                    Events
                </a>

                <a href="donations.php" class="nav-link active">
                    Donations
                </a>

                <a href="profile.php" class="nav-link">
                    Profile
                </a>


            </nav>


            <div class="sidebar-bottom">

                <a href="logout.php" class="nav-link logout-link">
                    Logout
                </a>

            </div>

        </aside>


        <main class="main-content">


            <header class="top-header">

                <div>

                    <h1>Record Donation</h1>

                    <p>
                        Record the completed blood donation
                    </p>

                </div>

                <div class="hospital-user">

                    <?php
                    echo htmlspecialchars(
                        $_SESSION["hospital_name"]
                    );
                    ?>

                </div>

            </header>


            <section class="dashboard-content">


                <div class="dashboard-section donation-confirmation">

                    <h2>Donation Confirmation</h2>

                    <p class="confirmation-text">
                        Please confirm that this donor completed
                        the donation at this event.
                    </p>


                    <div class="donation-details">


                        <div>

                            <strong>Registration ID</strong>

                            <span>
                                <?php
                                echo htmlspecialchars(
                                    $registration["Registration_ID"]
                                );
                                ?>
                            </span>

                        </div>


                        <div>

                            <strong>Donor Name</strong>

                            <span>
                                <?php
                                echo htmlspecialchars(
                                    $registration["Full_Name"]
                                );
                                ?>
                            </span>

                        </div>


                        <div>

                            <strong>Blood Group</strong>

                            <span>
                                <?php
                                echo htmlspecialchars(
                                    $registration["Blood_Group"]
                                );
                                ?>
                            </span>

                        </div>


                        <div>

                            <strong>Event</strong>

                            <span>
                                <?php
                                echo htmlspecialchars(
                                    $registration["Event_Name"]
                                );
                                ?>
                            </span>

                        </div>


                        <div>

                            <strong>Attendance</strong>

                            <span>
                                <?php
                                echo htmlspecialchars(
                                    $registration["Attendance_Status"]
                                );
                                ?>
                            </span>

                        </div>


                        <div>

                            <strong>Donation Date</strong>

                            <span>
                                <?php echo date("Y-m-d"); ?>
                            </span>

                        </div>


                    </div>


                    <?php if ($already_recorded): ?>

                        <div class="registration-error">

                            Donation has already been recorded
                            for this donor at this event.

                        </div>

                        <div class="form-actions">

                            <a
                                href="donations.php"
                                class="primary-btn"
                            >
                                View Donations
                            </a>

                        </div>


                    <?php else: ?>

                        <form method="POST" action="">

                            <div class="form-actions">

                                <a
                                    href="event-attendance.php?event_id=<?php echo $event_id; ?>"
                                    class="secondary-btn"
                                >
                                    Cancel
                                </a>

                                <button
                                    type="submit"
                                    class="primary-btn"
                                >
                                    Record Donation
                                </button>

                            </div>

                        </form>

                    <?php endif; ?>


                </div>


            </section>

        </main>

    </div>

</body>

</html>