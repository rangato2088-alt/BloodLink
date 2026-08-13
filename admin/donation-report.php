<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "include/db.php";

$sql = "SELECT
            dh.Donation_ID,
            d.Full_Name,
            d.Blood_Group,
            h.Hospital_Name,
            e.Event_Name,
            dh.Donation_Date
        FROM donation_history dh
        INNER JOIN donor d
            ON dh.Donor_ID = d.Donor_ID
        INNER JOIN hospital h
            ON dh.Hospital_ID = h.Hospital_ID
        INNER JOIN events e
            ON dh.Event_ID = e.Event_ID
        ORDER BY dh.Donation_Date DESC";

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

    <title>Donation Report | BloodLink Admin</title>

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

                <h1>Donation Report</h1>

                <p>
                    Recorded blood donation information
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

                        <h2>Donation Report</h2><br>

                        <p>
                            List of recorded blood donations.
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
                                <th>Donor</th>
                                <th>Blood Group</th>
                                <th>Hospital</th>
                                <th>Event</th>
                                <th>Donation Date</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if ($result && $result->num_rows > 0): ?>

                            <?php while ($donation = $result->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars($donation["Donation_ID"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($donation["Full_Name"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($donation["Blood_Group"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($donation["Hospital_Name"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($donation["Event_Name"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($donation["Donation_Date"]); ?>
                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="6"
                                    class="no-data"
                                >
                                    No donation records found.
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