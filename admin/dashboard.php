<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
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

                <a href="#" class="nav-link active">
                    Dashboard
                </a>

                <a href="#" class="nav-link">
                    Hospitals
                </a>

                <a href="#" class="nav-link">
                    Donors
                </a>

                <a href="#" class="nav-link">
                    Events
                </a>

                <a href="#" class="nav-link">
                    Blood Requests
                </a>

                <a href="#" class="nav-link">
                    Notifications
                </a>

                <a href="#" class="nav-link">
                    Reports
                </a>

            </nav>

            <div class="sidebar-bottom">

                <a href="#" class="nav-link logout-link">
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
                        <p>0</p>
                    </div>

                    <div class="kpi-card">
                        <h3>Total Hospitals</h3>
                        <p>0</p>
                    </div>

                    <div class="kpi-card">
                        <h3>Blood Requests</h3>
                        <p>0</p>
                    </div>

                    <div class="kpi-card">
                        <h3>Upcoming Events</h3>
                        <p>0</p>
                    </div>

                </div>


                
                <div class="dashboard-section">

                    <div class="section-header">
                        <h2>Recent Activity</h2>
                    </div>

                    <div class="empty-state">
                        <p>No recent activity available.</p>
                    </div>

                </div>

            </section>

        </main>

    </div>

</body>
</html>