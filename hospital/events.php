<?php

session_start();

if (!isset($_SESSION["hospital_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../admin/include/db.php";

$hospital_id = $_SESSION["hospital_id"];
$hospital_name = $_SESSION["hospital_name"];

$events = [];

$sql = "SELECT Event_ID,
               Event_Name,
               Event_Date,
               Event_Time,
               Venue,
               Description,
               Status
        FROM events
        WHERE Hospital_ID = ?
        ORDER BY Event_Date ASC, Event_Time ASC";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $hospital_id);

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Events | BloodLink</title>

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

                    <h1>Events</h1>

                    <p>
                        Manage your hospital's blood donation events
                    </p>

                </div>

                <div class="hospital-user">

                    <?php echo htmlspecialchars($hospital_name); ?>

                </div>

            </header>


            <section class="dashboard-content">




                <div class="page-header">

                    <div>

                        <h2>My Events</h2>

                        <p>
                            Create and manage your blood donation events.
                        </p>

                    </div>

                    <a href="create-event.php" class="primary-btn">
                        + Create New Event
                    </a>

                </div>




                <div class="dashboard-section">

                    <div class="table-container">

                        <table class="admin-table">

                            <thead>

                                <tr>

                                    <th>Event</th>

                                    <th>Date</th>

                                    <th>Time</th>

                                    <th>Venue</th>

                                    <th>Status</th>

                                    <th>Actions</th>

                                </tr>

                            </thead>

<tbody>

<?php if (count($events) > 0): ?>

    <?php foreach ($events as $event): ?>

        <tr>

            <td>
                <?php echo htmlspecialchars($event["Event_Name"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($event["Event_Date"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($event["Event_Time"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($event["Venue"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($event["Status"]); ?>
            </td>

            <td>

                <a href="edit-event.php?id=<?php echo $event['Event_ID']; ?>" class="action-btn edit-btn">
                    Edit
                </a>

                <a href="event-attendance.php?event_id=<?php echo $event['Event_ID']; ?>" class="action-btn attendance-btn">
                    Attendance
                </a>

                <a href="delete-event.php?id=<?php echo $event['Event_ID']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this event?');">
                    Delete
                </a>

            </td>

        </tr>

    <?php endforeach; ?>

<?php else: ?>

    <tr>

        <td colspan="6" class="no-data">
            No events available.
        </td>

    </tr>

<?php endif; ?>

</tbody>

                        </table>

                    </div>

                </div>


            </section>

        </main>

    </div>

</body>

</html>