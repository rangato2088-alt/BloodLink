<?php

session_start();

if (!isset($_SESSION["hospital_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../admin/include/db.php";

$hospital_id = $_SESSION["hospital_id"];
$hospital_name = $_SESSION["hospital_name"];


$sql = "SELECT COUNT(*) AS total
        FROM events
        WHERE Hospital_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hospital_id);
$stmt->execute();

$result = $stmt->get_result();
$total_events = $result->fetch_assoc()["total"];

$stmt->close();


// Registered Donors
$sql = "SELECT COUNT(*) AS total
        FROM event_registration er
        INNER JOIN events e
            ON er.Event_ID = e.Event_ID
        WHERE e.Hospital_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hospital_id);
$stmt->execute();

$result = $stmt->get_result();
$registered_donors = $result->fetch_assoc()["total"];

$stmt->close();


// Recorded Donations
$sql = "SELECT COUNT(*) AS total
        FROM donation_history
        WHERE Hospital_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hospital_id);
$stmt->execute();

$result = $stmt->get_result();
$total_donations = $result->fetch_assoc()["total"];

$stmt->close();


// Upcoming Events
$sql = "SELECT COUNT(*) AS total
        FROM events
        WHERE Hospital_ID = ?
        AND Status = 'Upcoming'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hospital_id);
$stmt->execute();

$result = $stmt->get_result();
$upcoming_events = $result->fetch_assoc()["total"];


$stmt->close();

// Recent Events

$recent_events = [];

$sql = "SELECT Event_ID,
               Event_Name,
               Event_Date,
               Event_Time,
               Venue,
               Status
        FROM events
        WHERE Hospital_ID = ?
        ORDER BY Event_Date DESC, Event_Time DESC
        LIMIT 5";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $hospital_id
);

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $recent_events[] = $row;
}

$stmt->close();


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hospital Dashboard | BloodLink</title>

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

                <a href="dashboard.php" class="nav-link active">
                    Dashboard
                </a>

                <a href="events.php" class="nav-link">
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

                    <h1>Hospital Dashboard</h1>

                    <p>
                        Welcome back,
                        <?php echo htmlspecialchars($hospital_name); ?>
                    </p>

                </div>


                <div class="hospital-user">

                    <span>
                        <?php echo htmlspecialchars($_SESSION["hospital_admin_name"]); ?>
                    </span>

                </div>

            </header>




            <section class="dashboard-content">


                <div class="welcome-section">

                    <h2>
                        Welcome, <?php echo htmlspecialchars($hospital_name); ?>
                    </h2>

                    <p>
                        Manage your hospital's blood donation activities
                        through BloodLink.
                    </p>

                </div>




                <div class="kpi-grid">


                    <div class="kpi-card">

                        <h3>Total Events</h3>

                        <p><?php echo $total_events; ?></p>

                    </div>


                    <div class="kpi-card">

                        <h3>Recorded Donations</h3>

                        <p><?php echo $total_donations; ?></p>

                    </div>


                    <div class="kpi-card">

                        <h3>Upcoming Events</h3>

                        <p><?php echo $upcoming_events; ?></p>

                    </div>


                    <div class="kpi-card">

                        <h3>Hospital Status</h3>

                        <p class="status-verified">
                            Verified
                        </p>

                    </div>


                </div>




                <div class="dashboard-section">

                    <div class="section-header">

                        <h2>Recent Events</h2>

                        <a href="events.php" class="view-all">
                            View All
                        </a>

                    </div>


<?php if (count($recent_events) > 0): ?>

    <?php foreach ($recent_events as $event): ?>

        <div class="recent-event-item">

            <div class="recent-event-info">

                <h4>
                    <?php echo htmlspecialchars($event["Event_Name"]); ?>
                </h4>

                <p>
                    <?php echo htmlspecialchars($event["Event_Date"]); ?>
                    &nbsp; | &nbsp;
                    <?php echo htmlspecialchars($event["Event_Time"]); ?>
                </p>

                <span>
                    <?php echo htmlspecialchars($event["Venue"]); ?>
                </span>

            </div>

            <div class="recent-event-status">

                <?php echo htmlspecialchars($event["Status"]); ?>

            </div>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <div class="empty-state">

        <p>
            No events available.
        </p>

    </div>

<?php endif; ?>

                </div>


            </section>

        </main>

    </div>


</body>

</html>