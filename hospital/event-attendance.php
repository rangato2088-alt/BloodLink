<?php

session_start();

if (!isset($_SESSION["hospital_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../admin/include/db.php";

$hospital_id = $_SESSION["hospital_id"];
$hospital_name = $_SESSION["hospital_name"];

if (!isset($_GET["event_id"]) || !is_numeric($_GET["event_id"])) {
    header("Location: events.php");
    exit();
}

$event_id = (int) $_GET["event_id"];

$error = "";
$registration = null;

$registration_id = null;



if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        isset($_POST["registration_id"]) &&
        is_numeric($_POST["registration_id"])
    ) {
        $registration_id = (int) $_POST["registration_id"];
    }

}



if (
    $registration_id === null &&
    isset($_GET["registration_id"]) &&
    is_numeric($_GET["registration_id"])
) {

    $registration_id = (int) $_GET["registration_id"];

}



if ($registration_id !== null) {

    $sql = "SELECT er.Registration_ID,
                   er.Event_ID,
                   er.Donor_ID,
                   er.Registration_Date,
                   er.Attendance_Status,
                   d.Full_Name,
                   d.Blood_Group
            FROM event_registration er
            INNER JOIN donor d
                ON er.Donor_ID = d.Donor_ID
            WHERE er.Registration_ID = ?
            AND er.Event_ID = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ii",
        $registration_id,
        $event_id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $registration = $result->fetch_assoc();

    } else {

        $error = "No registration found for this event.";

    }

    $stmt->close();

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Event Attendance | BloodLink</title>

    <link rel="stylesheet" href="assets/css/hospital-dashboard.css">

</head>

<body>

    <div class="dashboard-container">

        <!-- Sidebar -->

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

                <a href="events.php" class="nav-link active">
                    Events
                </a>

                <a href="donations.php" class="nav-link">
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

                    <h1>Event Attendance</h1>

                    <p>
                        Record donor attendance for this event
                    </p>

                </div>

                <div class="hospital-user">

                    <?php echo htmlspecialchars($hospital_name); ?>

                </div>

            </header>


            <section class="dashboard-content">

                <div class="dashboard-section attendance-section">

                    <div class="section-header">

                        <div>

                            <h2>Mark Donor Attendance</h2>

                            <p>
                                Enter the donor's event registration ID
                                to record attendance.
                            </p>

                        </div>

                    </div>


                    <form method="POST" action="" class="attendance-search-form">

                        <div class="form-group">

                            <label for="registration_id">
                                Donor Registration ID
                            </label>

                            <input
                                type="number"
                                id="registration_id"
                                name="registration_id"
                                placeholder="Enter Registration ID"
                                required
                            >

                        </div>

                        <button
                            type="submit"
                            class="primary-btn"
                        >
                            Search Registration
                        </button>

                    </form>

                    <?php if ($error !== ""): ?>

    <div class="registration-error">
        <?php echo htmlspecialchars($error); ?>
    </div>

<?php endif; ?>


<?php if ($registration !== null): ?>

    <div class="attendance-result">

        <h3>Donor Registration</h3>

        <div class="donor-details">

            <div>
                <strong>Registration ID</strong>
                <span>
                    <?php echo htmlspecialchars($registration["Registration_ID"]); ?>
                </span>
            </div>

            <div>
                <strong>Donor Name</strong>
                <span>
                    <?php echo htmlspecialchars($registration["Full_Name"]); ?>
                </span>
            </div>

            <div>
                <strong>Blood Type</strong>
                <span>
                    <?php echo htmlspecialchars($registration["Blood_Group"]); ?>
                </span>
            </div>

            <div>
                <strong>Attendance Status</strong>
                <span>
                    <?php echo htmlspecialchars($registration["Attendance_Status"]); ?>
                </span>
            </div>

        </div>


        <?php if ($registration["Attendance_Status"] === "Registered"): ?>

    <form method="POST" action="mark-attendance.php">

        <input
            type="hidden"
            name="registration_id"
            value="<?php echo $registration["Registration_ID"]; ?>"
        >

        <input
            type="hidden"
            name="event_id"
            value="<?php echo $event_id; ?>"
        >

        <button type="submit" class="primary-btn">
            Mark Attendance
        </button>

    </form>

<?php elseif ($registration["Attendance_Status"] === "Attended"): ?>

    <a
        href="record-donation.php?registration_id=<?php echo $registration["Registration_ID"]; ?>&event_id=<?php echo $event_id; ?>"
        class="primary-btn"
    >
        Record Donation
    </a>

<?php endif; ?>

            <form method="POST" action="mark-attendance.php">

                <input
                    type="hidden"
                    name="registration_id"
                    value="<?php echo htmlspecialchars($registration["Registration_ID"]); ?>"
                >

                <input
                    type="hidden"
                    name="event_id"
                    value="<?php echo htmlspecialchars($event_id); ?>"
                >

                <button
                    type="submit"
                    class="primary-btn"
                >
                    Mark Attendance
                </button>

            </form>

        <?php endif; ?>

    </div>



                    <div class="attendance-info">

                        <p>
                            The registration ID must belong to this
                            selected event.
                        </p>

                    </div>

                </div>

            </section>

        </main>

    </div>

</body>

</html>