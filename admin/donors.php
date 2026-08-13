<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "include/db.php";


// Search value

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}


// Get donors

if ($search !== "") {

    $sql = "SELECT
                Donor_ID,
                Full_Name,
                Username,
                Blood_Group,
                Date_Of_Birth,
                Gender,
                Address,
                Email,
                Phone_Number,
                Last_Donation_Date
            FROM donor
            WHERE Donor_ID = ?
               OR Full_Name LIKE ?
            ORDER BY Donor_ID DESC";

    $stmt = $conn->prepare($sql);

    $search_name = "%" . $search . "%";

    if (is_numeric($search)) {

        $donor_id = (int) $search;

        $stmt->bind_param(
            "is",
            $donor_id,
            $search_name
        );

    } else {

        $donor_id = -1;

        $stmt->bind_param(
            "is",
            $donor_id,
            $search_name
        );
    }

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $sql = "SELECT
                Donor_ID,
                Full_Name,
                Username,
                Blood_Group,
                Date_Of_Birth,
                Gender,
                Address,
                Email,
                Phone_Number,
                Last_Donation_Date
            FROM donor
            ORDER BY Donor_ID DESC";

    $result = $conn->query($sql);

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

    <title>Donors | BloodLink Admin</title>

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

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
                class="nav-link active"
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
                class="nav-link"
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


    <!-- Main Content -->

    <main class="main-content">


        <header class="top-header">

            <div>

                <h1>Donors</h1>

                <p>
                    Manage registered donors
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

                        <h2>Donor Management</h2>

                        <p>
                            Search and manage registered donors.
                        </p>

                    </div>

                </div>


                <!-- Search -->

                <form
                    method="GET"
                    action="donors.php"
                    class="search-form"
                >

                    <input
                        type="text"
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Search by Donor ID or Donor Name"
                    >

                    <button
                        type="submit"
                        class="primary-btn"
                    >
                        Search
                    </button>


                    <?php if ($search !== ""): ?>

                        <a
                            href="donors.php"
                            class="secondary-btn"
                        >
                            Clear
                        </a>

                    <?php endif; ?>

                </form>


                <!-- Donor Table -->

                <div class="table-container">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>ID</th>

                                <th>Donor Name</th>

                                <th>Blood Group</th>

                                <th>Gender</th>

                                <th>Email</th>

                                <th>Phone</th>

                                <th>Last Donation</th>

                                <th>Actions</th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php if ($result->num_rows > 0): ?>

                            <?php while ($donor = $result->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $donor["Donor_ID"]
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $donor["Full_Name"]
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $donor["Blood_Group"]
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $donor["Gender"]
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $donor["Email"]
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $donor["Phone_Number"]
                                        );
                                        ?>
                                    </td>


                                    <td>

                                        <?php

                                        if (
                                            !empty(
                                                $donor["Last_Donation_Date"]
                                            )
                                        ) {

                                            echo htmlspecialchars(
                                                $donor["Last_Donation_Date"]
                                            );

                                        } else {

                                            echo "Never";

                                        }

                                        ?>

                                    </td>


                                    <td>

                                        <div class="action-buttons">

                                            <a
                                                href="edit-donor.php?id=<?php echo $donor["Donor_ID"]; ?>"
                                                class="action-btn edit-btn"
                                            >
                                                Edit
                                            </a>


                                            <a
                                                href="delete-donor.php?id=<?php echo $donor["Donor_ID"]; ?>"
                                                class="action-btn delete-btn"
                                                onclick="return confirm('Are you sure you want to delete this donor?');"
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
                                    colspan="8"
                                    class="no-data"
                                >
                                    No donors found.
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