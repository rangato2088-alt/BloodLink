<?php

session_start();

if (!isset($_SESSION["hospital_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../admin/include/db.php";

$hospital_id = $_SESSION["hospital_id"];
$hospital_name = $_SESSION["hospital_name"];


// Get logged-in hospital details

$sql = "SELECT Hospital_ID,
               Hospital_Name,
               Email,
               Phone,
               Address
        FROM hospital
        WHERE Hospital_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $hospital_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: dashboard.php");
    exit();

}

$hospital = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hospital Profile | BloodLink</title>

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

                <a href="events.php" class="nav-link">
                    Events
                </a>

                <a href="donations.php" class="nav-link">
                    Donations
                </a>

                <a href="profile.php" class="nav-link active">
                    Profile
                </a>


            </nav>


            <div class="sidebar-bottom">

                <a href="logout.php" class="nav-link logout-link">
                    Logout
                </a>

            </div>

        </aside>


        <!-- Main Content -->

        <main class="main-content">


            <header class="top-header">

                <div>

                    <h1>Hospital Profile</h1>

                    <p>
                        View your registered hospital information
                    </p>

                </div>


                <div class="hospital-user">

                    <?php echo htmlspecialchars($hospital_name); ?>

                </div>

            </header>


            <section class="dashboard-content">


                <div class="dashboard-section profile-section">

                    <div class="section-header">

                        <div>

                            <h2>Hospital Information</h2>

                            <p>
                                Your registered hospital details.
                            </p>

                        </div>

                    </div>


                    <div class="profile-grid">


                        <div class="profile-item">

                            <span>Hospital ID</span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $hospital["Hospital_ID"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="profile-item">

                            <span>Hospital Name</span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $hospital["Hospital_Name"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="profile-item">

                            <span>Email</span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $hospital["Email"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="profile-item">

                            <span>Phone Number</span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $hospital["Phone"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="profile-item profile-full">

                            <span>Address</span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $hospital["Address"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="profile-item">

                            <span>Registration Status</span>

                            <strong class="status-verified">
                                <p>Verified</p>


                            </strong>

                        </div>


                    </div>

                </div>


            </section>

        </main>

    </div>

</body>

</html>