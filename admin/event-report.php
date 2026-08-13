<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "include/db.php";

$sql = "SELECT
            e.Event_ID,
            e.Event_Name,
            h.Hospital_Name,
            e.Event_Date,
            e.Event_Time,
            e.Venue,
            e.Status
        FROM events e
        INNER JOIN hospital h
            ON e.Hospital_ID = h.Hospital_ID
        ORDER BY e.Event_Date DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Event Report | BloodLink Admin</title>

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

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

        <nav class="sidebar-nav">

            <a href="dashboard.php" class="nav-link">
                Dashboard
            </a>

            <a href="hospitals.php" class="nav-link">
                Hospitals
            </a>

            <a href="donors.php" class="nav-link">
                Donors
            </a>

            <a href="events.php" class="nav-link">
                Events
            </a>

            <a href="reports.php" class="nav-link active">
                Reports
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

                <h1>Event Report</h1>

                <p>
                    Blood donation event information
                </p>

            </div>

            <div class="admin-user">
                Admin
            </div>

        </header>

        <section class="dashboard-content">

            <div class="dashboard-section">

                <div class="section-header">

                    <div>

                        <h2>Event Report</h2><br>

                        <p>
                            List of blood donation events.
                        </p><br>

                    </div>

                    <button
                        onclick="window.print()"
                        class="primary-btn"
                    >
                        Print Report
                    </button>

                </div>

                <div class="table-container">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>ID</th>
                                <th>Event Name</th>
                                <th>Hospital</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Venue</th>
                                <th>Status</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if ($result && $result->num_rows > 0): ?>

                            <?php while ($event = $result->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars($event["Event_ID"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($event["Event_Name"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($event["Hospital_Name"]); ?>
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

                                        <?php if ($event["Status"] === "Upcoming"): ?>

                                            <span class="status-verified">
                                                Upcoming
                                            </span>

                                        <?php elseif ($event["Status"] === "Completed"): ?>

                                            <span class="status-completed">
                                                Completed
                                            </span>

                                        <?php else: ?>

                                            <span class="status-pending">
                                                <?php echo htmlspecialchars($event["Status"]); ?>
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="no-data"
                                >
                                    No event records found.
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