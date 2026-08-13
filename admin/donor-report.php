<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "include/db.php";

$sql = "SELECT
            Donor_ID,
            Full_Name,
            Blood_Group,
            Gender,
            Email,
            Phone_Number,
            Last_Donation_Date
        FROM donor
        ORDER BY Donor_ID DESC";

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

    <title>Donor Report | BloodLink Admin</title>

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

                <h1>Donor Report</h1>

                <p>
                    Registered donor information
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

                        <h2>Donor Report</h2><br>

                        <p>
                            List of registered donors.
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
                                <th>Full Name</th>
                                <th>Blood Group</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Last Donation</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if ($result && $result->num_rows > 0): ?>

                            <?php while ($donor = $result->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars($donor["Donor_ID"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($donor["Full_Name"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($donor["Blood_Group"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($donor["Gender"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($donor["Email"] ?? ""); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($donor["Phone_Number"] ?? ""); ?>
                                    </td>

                                    <td>

                                        <?php

                                        if (!empty($donor["Last_Donation_Date"])) {

                                            echo htmlspecialchars(
                                                $donor["Last_Donation_Date"]
                                            );

                                        } else {

                                            echo "Never";

                                        }

                                        ?>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="no-data"
                                >
                                    No donor records found.
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