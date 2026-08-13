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
                e.Event_ID,
                e.Event_Name,
                e.Event_Date,
                e.Event_Time,
                e.Venue,
                e.Status,
                e.Hospital_ID,
                h.Hospital_Name
            FROM events e
            INNER JOIN hospital h
                ON e.Hospital_ID = h.Hospital_ID
            WHERE e.Event_ID = ?
               OR e.Event_Name LIKE ?
            ORDER BY e.Event_ID DESC";

    $stmt = $conn->prepare($sql);

    $search_name = "%" . $search . "%";

    if (is_numeric($search)) {

        $event_id = (int) $search;

        $stmt->bind_param(
            "is",
            $event_id,
            $search_name
        );

    } else {

        $event_id = -1;

        $stmt->bind_param(
            "is",
            $event_id,
            $search_name
        );
    }

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $sql = "SELECT
                e.Event_ID,
                e.Event_Name,
                e.Event_Date,
                e.Event_Time,
                e.Venue,
                e.Status,
                e.Hospital_ID,
                h.Hospital_Name
            FROM events e
            INNER JOIN hospital h
                ON e.Hospital_ID = h.Hospital_ID
            ORDER BY e.Event_ID DESC";

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

    <title>Events | BloodLink Admin</title>

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
                class="nav-link active"
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

    <main class="main-content">

        <header class="top-header">

            <div>

                <h1>Events</h1>

                <p>
                    Manage blood donation events
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

                        <h2>Event Management</h2>

                        <p>
                            Search and manage registered events.
                        </p>

                    </div>

                </div>

                <form
                    method="GET"
                    action="events.php"
                    class="search-form"
                >

                    <input
                        type="text"
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Search by Event ID or Event Name"
                    >

                    <button
                        type="submit"
                        class="primary-btn"
                    >
                        Search
                    </button>

                    <?php if ($search !== ""): ?>

                        <a
                            href="events.php"
                            class="secondary-btn"
                        >
                            Clear
                        </a>

                    <?php endif; ?>

                </form>

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

                                <th>Actions</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if ($result->num_rows > 0): ?>

                            <?php while ($event = $result->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $event["Event_ID"]
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $event["Event_Name"]
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $event["Hospital_Name"]
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $event["Event_Date"]
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $event["Event_Time"]
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $event["Venue"]
                                        );
                                        ?>
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
                                                <?php
                                                echo htmlspecialchars(
                                                    $event["Status"]
                                                );
                                                ?>
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>

                                        <div class="action-buttons">

                                            <a
                                                href="edit-event.php?id=<?php echo $event["Event_ID"]; ?>"
                                                class="action-btn edit-btn"
                                            >
                                                Edit
                                            </a>

                                            <a
                                                href="delete-event.php?id=<?php echo $event["Event_ID"]; ?>"
                                                class="action-btn delete-btn"
                                                onclick="return confirm('Are you sure you want to delete this event?');"
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
                                    No events found.
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