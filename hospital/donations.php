<?php

session_start();

if (!isset($_SESSION["hospital_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../admin/include/db.php";

$hospital_id = $_SESSION["hospital_id"];
$hospital_name = $_SESSION["hospital_name"];

$donations = [];

$sql = "SELECT dh.Donation_ID,
               dh.Donation_Date,
               d.Full_Name,
               d.Blood_Group,
               e.Event_Name
        FROM donation_history dh

        INNER JOIN donor d
            ON dh.Donor_ID = d.Donor_ID

        INNER JOIN events e
            ON dh.Event_ID = e.Event_ID

        WHERE dh.Hospital_ID = ?

        ORDER BY dh.Donation_Date DESC";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $hospital_id
);

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $donations[] = $row;
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Donations | BloodLink</title>

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

                    <h1>Donations</h1>

                    <p>
                        View recorded blood donations
                    </p>

                </div>


                <div class="hospital-user">

                    <?php echo htmlspecialchars($hospital_name); ?>

                </div>

            </header>


            <section class="dashboard-content">


                <div class="page-header">

                    <div>

                        <h2>Donation History</h2>

                        <p>
                            Blood donations recorded by your hospital.
                        </p>

                    </div>

                </div>


                <div class="dashboard-section">

                    <div class="table-container">

                        <table class="admin-table">

                            <thead>

                                <tr>

                                    <th>Donation ID</th>

                                    <th>Donor</th>

                                    <th>Blood Group</th>

                                    <th>Event</th>

                                    <th>Donation Date</th>

                                </tr>

                            </thead>


<tbody>

<?php if (count($donations) > 0): ?>

    <?php foreach ($donations as $donation): ?>

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
                <?php echo htmlspecialchars($donation["Event_Name"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($donation["Donation_Date"]); ?>
            </td>

        </tr>

    <?php endforeach; ?>

<?php else: ?>

    <tr>

        <td colspan="5" class="no-data">
            No donations recorded yet.
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