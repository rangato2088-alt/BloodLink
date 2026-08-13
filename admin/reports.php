<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "include/db.php";

$total_donors = 0;
$total_hospitals = 0;
$total_events = 0;
$total_donations = 0;

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM donor"
);

if ($result) {
    $row = $result->fetch_assoc();
    $total_donors = $row["total"];
}

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM hospital"
);

if ($result) {
    $row = $result->fetch_assoc();
    $total_hospitals = $row["total"];
}

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM events"
);

if ($result) {
    $row = $result->fetch_assoc();
    $total_events = $row["total"];
}

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM donation_history"
);

if ($result) {
    $row = $result->fetch_assoc();
    $total_donations = $row["total"];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reports | BloodLink Admin</title>

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

            <a
                href="dashboard.php"
                class="nav-link"
            >
                Dashboard
            </a>

            <a
                href="hospitals.php"
                class="nav-link"
            >
                Hospitals
            </a>

            <a
                href="donors.php"
                class="nav-link"
            >
                Donors
            </a>

            <a
                href="events.php"
                class="nav-link"
            >
                Events
            </a>

            <a
                href="reports.php"
                class="nav-link active"
            >
                Reports
            </a>

        </nav>

        <div class="sidebar-bottom">

            <a
                href="logout.php"
                class="nav-link logout-link"
            >
                Logout
            </a>

        </div>

    </aside>

    <main class="main-content">

        <header class="top-header">

            <div>

                <h1>Reports</h1>

                <p>
                    BloodLink system reports
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

                        <h2>Reports Overview</h2>

                        <p>
                            Generate reports from BloodLink system data.
                        </p>

                    </div>

                </div>

                <div class="kpi-grid">

                    <div class="kpi-card">

                        <h3>Total Donors</h3>

                        <p>
                            <?php echo $total_donors; ?>
                        </p>

                    </div>

                    <div class="kpi-card">

                        <h3>Total Hospitals</h3>

                        <p>
                            <?php echo $total_hospitals; ?>
                        </p>

                    </div>

                    <div class="kpi-card">

                        <h3>Total Events</h3>

                        <p>
                            <?php echo $total_events; ?>
                        </p>

                    </div>

                    <div class="kpi-card">

                        <h3>Total Donations</h3>

                        <p>
                            <?php echo $total_donations; ?>
                        </p>

                    </div>

                </div>

            </div>

            <div class="dashboard-section">

                <div class="section-header">

                    <div>

                        <h2>Available Reports</h2>

                        <p>
                            Select a report to view detailed information.
                        </p>

                    </div>

                </div>

                <div class="report-grid">

                    <a
                        href="donor-report.php"
                        class="report-card"
                    >

                        <h3>Donor Report</h3>

                        <p>
                            View registered donor information and blood groups.
                        </p>

                        <span>
                            View Report →
                        </span>

                    </a>

                    <a
                        href="hospital-report.php"
                        class="report-card"
                    >

                        <h3>Hospital Report</h3>

                        <p>
                            View registered hospitals and verification status.
                        </p>

                        <span>
                            View Report →
                        </span>

                    </a>

                    <a
                        href="event-report.php"
                        class="report-card"
                    >

                        <h3>Event Report</h3>

                        <p>
                            View blood donation events and their current status.
                        </p>

                        <span>
                            View Report →
                        </span>

                    </a>

                    <a
                        href="donation-report.php"
                        class="report-card"
                    >

                        <h3>Donation Report</h3>

                        <p>
                            View recorded blood donations and related details.
                        </p>

                        <span>
                            View Report →
                        </span>

                    </a>

                </div>

            </div>

        </section>

    </main>

</div>

</body>

</html>