<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "include/db.php";



$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}



if ($search !== "") {

    $sql = "SELECT
                Hospital_ID,
                Hospital_Name,
                Phone,
                Email,
                Address,
                City,
                Is_Verified
            FROM hospital
            WHERE Hospital_ID = ?
               OR Hospital_Name LIKE ?
            ORDER BY Hospital_ID DESC";

    $stmt = $conn->prepare($sql);

    $search_name = "%" . $search . "%";

    if (is_numeric($search)) {

        $hospital_id = (int) $search;

        $stmt->bind_param(
            "is",
            $hospital_id,
            $search_name
        );

    } else {


        $hospital_id = -1;

        $stmt->bind_param(
            "is",
            $hospital_id,
            $search_name
        );
    }

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $sql = "SELECT
                Hospital_ID,
                Hospital_Name,
                Phone,
                Email,
                Address,
                City,
                Is_Verified
            FROM hospital
            ORDER BY Hospital_ID DESC";

    $result = $conn->query($sql);

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hospitals | BloodLink Admin</title>

    <link rel="stylesheet" href="assets/css/admin.css">

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

            <a href="hospitals.php" class="nav-link active">
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

        <h1>Hospitals</h1>

        <p>
            Manage registered hospitals
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

                        <h2>Hospital Management</h2>

                        <p>
                            Search and manage registered hospitals.
                        </p>

                    </div>

                </div>


                <!-- Search -->

                <form
                    method="GET"
                    action="hospitals.php"
                    class="search-form"
                >

                    <input
                        type="text"
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Search by Hospital ID or Hospital Name"
                    >

                    <button
                        type="submit"
                        class="primary-btn"
                    >
                        Search
                    </button>

                    <?php if ($search !== ""): ?>

                        <a
                            href="hospitals.php"
                            class="secondary-btn"
                        >
                            Clear
                        </a>

                    <?php endif; ?>

                </form>


                <!-- Hospital Table -->

                <div class="table-container">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>ID</th>

                                <th>Hospital Name</th>

                                <th>Phone</th>

                                <th>Email</th>

                                <th>City</th>

                                <th>Status</th>

                                <th>Actions</th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php if ($result->num_rows > 0): ?>

                            <?php while ($hospital = $result->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $hospital["Hospital_ID"]
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $hospital["Hospital_Name"]
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $hospital["Phone"]
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $hospital["Email"]
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $hospital["City"]
                                        );
                                        ?>
                                    </td>


                                    <td>

                                        <?php if ($hospital["Is_Verified"] == 1): ?>

                                            <span class="status-verified">
                                                Approved
                                            </span>

                                        <?php else: ?>

                                            <span class="status-pending">
                                                Pending
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <div class="action-buttons">

                                            <a
                                                href="edit-hospital.php?id=<?php echo $hospital["Hospital_ID"]; ?>"
                                                class="action-btn edit-btn"
                                            >
                                                Edit
                                            </a>


                                            <?php if ($hospital["Is_Verified"] == 0): ?>

                                                <a
                                                    href="approve-hospital.php?id=<?php echo $hospital["Hospital_ID"]; ?>"
                                                    class="action-btn approve-btn"
                                                    onclick="return confirm('Approve this hospital?');"
                                                >
                                                    Approve
                                                </a>

                                            <?php endif; ?>


                                            <a
                                                href="delete-hospital.php?id=<?php echo $hospital["Hospital_ID"]; ?>"
                                                class="action-btn delete-btn"
                                                onclick="return confirm('Are you sure you want to delete this hospital?');"
                                            >
                                                Delete
                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="no-data"
                                >
                                    No hospitals found.
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