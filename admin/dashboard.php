<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "include/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["approve_hospital"])) {

    $hospital_id = $_POST["hospital_id"];

    
    $sql = "UPDATE hospital
            SET Is_Verified = 1
            WHERE Hospital_ID = ?
            AND Is_Verified = 0";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();


    
    $sql = "UPDATE hospital_admin
            SET Status = 'Active'
            WHERE Hospital_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();


    header("Location: dashboard.php");
    exit();
}

$total_donors = 0;

$sql = "SELECT COUNT(*) AS total FROM donor";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $total_donors = $row["total"];
}

$total_hospitals = 0;

$sql = "SELECT COUNT(*) AS total FROM hospital";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $total_hospitals = $row["total"];
}

$total_upcoming_events = 0;

$sql = "SELECT COUNT(*) AS total
        FROM events
        WHERE Event_Date >= CURDATE()
        AND Status = 'Upcoming'";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $total_upcoming_events = $row["total"];
}

$recent_activity = [];

$sql = "
    SELECT
        'Donation' AS Activity_Type,
        CONCAT(
            'Donation recorded for ',
            d.Full_Name
        ) AS Activity_Text,
        dh.Donation_Date AS Activity_Date
    FROM donation_history dh
    INNER JOIN donor d
        ON dh.Donor_ID = d.Donor_ID

    UNION ALL

    SELECT
        'Registration' AS Activity_Type,
        CONCAT(
            d.Full_Name,
            ' registered for ',
            e.Event_Name
        ) AS Activity_Text,
        DATE(er.Registration_Date) AS Activity_Date
    FROM event_registration er
    INNER JOIN donor d
        ON er.Donor_ID = d.Donor_ID
    INNER JOIN events e
        ON er.Event_ID = e.Event_ID

    UNION ALL

    SELECT
        'Event' AS Activity_Type,
        CONCAT(
            'Event created: ',
            Event_Name
        ) AS Activity_Text,
        Event_Date AS Activity_Date
    FROM events

    ORDER BY Activity_Date DESC
    LIMIT 5
";

$result = $conn->query($sql);

if ($result) {

    while ($row = $result->fetch_assoc()) {
        $recent_activity[] = $row;
    }

}



$total_donations = 0;

$sql = "SELECT COUNT(*) AS total FROM donation_history";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $total_donations = $row["total"];
}

$pending_hospitals = 0;

$sql = "SELECT COUNT(*) AS total 
        FROM hospital 
        WHERE Is_Verified = 0";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $pending_hospitals = $row["total"];
}

$pending_hospital_list = [];

$sql = "SELECT Hospital_ID, Hospital_Name, City, Phone
        FROM hospital
        WHERE Is_Verified = 0
        ORDER BY Hospital_ID DESC";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pending_hospital_list[] = $row;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | BloodLink</title>

    <link rel="stylesheet" href="assets/css/admin.css">
</head>

<body>

    <div class="admin-layout">

        
        <aside class="sidebar">

            <div class="sidebar-logo">
                <img src="assets/image/BloodLink_logo_800px_transparent.webp" alt="BloodLink Logo"class="login-logo">
                
            </div>

            <nav class="sidebar-nav">

                <a href="dashboard.php" class="nav-link active">
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


                <a href="reports.php" class="nav-link">
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
                    <h1>Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($_SESSION["admin_name"]); ?></p>
                </div>

                <div class="admin-profile">
                    <span>Admin</span>
                </div>

            </header>


            
            <section class="dashboard-content">

                <div class="page-heading">
                    <h2>Overview</h2>
                    <p>BloodLink system summary</p>
                </div>


                
                <div class="kpi-grid">

                    <div class="kpi-card">
                        <h3>Total Donors</h3>
                        <p><?php echo $total_donors; ?></p>
                    </div>

                    <div class="kpi-card">
                        <h3>Total Hospitals</h3>
                        <p><?php echo $total_hospitals; ?></p>
                    </div>

                    <div class="kpi-card">
                        <h3>Recorded Donations</h3>
                        <p><?php echo $total_donations; ?></p>
                    </div>

                    <div class="kpi-card">
                        <h3>Upcoming Events</h3>
                        <p><?php echo $total_upcoming_events; ?></p>
                    </div>

                </div>

<div class="dashboard-section">

    <div class="section-header">
        <h2>Hospital Approvals</h2>
        <p>Pending registrations: <?php echo $pending_hospitals; ?></p>
    </div>

    <?php if (count($pending_hospital_list) > 0): ?>

        <div class="table-container">

            <table class="admin-table">

                <thead>
                    <tr>
                        <th>Hospital ID</th>
                        <th>Hospital Name</th>
                        <th>City</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($pending_hospital_list as $hospital): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($hospital["Hospital_ID"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($hospital["Hospital_Name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($hospital["City"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($hospital["Phone"]); ?>
                            </td>

                            <td>

                            <form method="POST" action="">

                                <input type="hidden" name="hospital_id"  value="<?php echo $hospital["Hospital_ID"]; ?>">
                                <button type="submit" name="approve_hospital" class="approve-btn">
                                    Approve
                                </button>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="empty-state">
            <p>No pending hospital registrations.</p>
        </div>

    <?php endif; ?>

</div>


                
                <div class="dashboard-section">

                    <div class="section-header">
                        <h2>Recent Activity</h2>
                    </div>

<?php if (count($recent_activity) > 0): ?>

    <div class="activity-list">

        <?php foreach ($recent_activity as $activity): ?>

            <div class="activity-item">

                <div class="activity-icon">

                    <?php
                    echo htmlspecialchars(
                        substr(
                            $activity["Activity_Type"],
                            0,
                            1
                        )
                    );
                    ?>

                </div>

                <div class="activity-details">

                    <p>
                        <?php
                        echo htmlspecialchars(
                            $activity["Activity_Text"]
                        );
                        ?>
                    </p>

                    <span>
                        <?php
                        echo htmlspecialchars(
                            $activity["Activity_Date"]
                        );
                        ?>
                    </span>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

<?php else: ?>

    <div class="empty-state">

        <p>
            No recent activity available.
        </p>

    </div>

<?php endif; ?>

                </div>

            </section>

        </main>

    </div>

</body>
</html>