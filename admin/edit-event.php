<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "include/db.php";

if (
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
) {
    header("Location: events.php");
    exit();
}

$event_id = (int) $_GET["id"];

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $event_name = trim($_POST["event_name"]);
    $event_date = trim($_POST["event_date"]);
    $event_time = trim($_POST["event_time"]);
    $venue = trim($_POST["venue"]);
    $description = trim($_POST["description"]);
    $status = trim($_POST["status"]);

    if (
        $event_name === "" ||
        $event_date === "" ||
        $event_time === "" ||
        $venue === "" ||
        $status === ""
    ) {

        $error = "Please fill in all required fields.";

    } else {

        $sql = "UPDATE events
                SET Event_Name = ?,
                    Event_Date = ?,
                    Event_Time = ?,
                    Venue = ?,
                    Description = ?,
                    Status = ?
                WHERE Event_ID = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssssssi",
            $event_name,
            $event_date,
            $event_time,
            $venue,
            $description,
            $status,
            $event_id
        );

        if ($stmt->execute()) {

            $stmt->close();

            header("Location: events.php?updated=1");
            exit();

        } else {

            $error = "Unable to update event.";

        }

        $stmt->close();
    }
}

$sql = "SELECT
            Event_ID,
            Event_Name,
            Event_Date,
            Event_Time,
            Venue,
            Description,
            Status,
            Hospital_ID
        FROM events
        WHERE Event_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $event_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: events.php");
    exit();

}

$event = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Event | BloodLink Admin</title>

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

                <h1>Edit Event</h1>

                <p>
                    Update blood donation event information
                </p>

            </div>

            <div class="admin-user">
                Admin
            </div>

        </header>

        <section class="dashboard-content">

            <div class="dashboard-section edit-section">

                <div class="section-header">

                    <div>

                        <h2>Event Information</h2>

                        <p>
                            Update the selected event's details.
                        </p>

                    </div>

                </div>

                <?php if ($error !== ""): ?>

                    <div class="form-error">

                        <?php
                        echo htmlspecialchars($error);
                        ?>

                    </div>

                <?php endif; ?>

                <form
                    method="POST"
                    action="edit-event.php?id=<?php echo $event_id; ?>"
                    class="edit-form"
                >

                    <div class="form-grid">

                        <div class="form-group">

                            <label>
                                Event ID
                            </label>

                            <input
                                type="text"
                                value="<?php echo htmlspecialchars($event["Event_ID"]); ?>"
                                disabled
                            >

                        </div>

                        <div class="form-group">

                            <label>
                                Hospital ID
                            </label>

                            <input
                                type="text"
                                value="<?php echo htmlspecialchars($event["Hospital_ID"]); ?>"
                                disabled
                            >

                        </div>

                        <div class="form-group">

                            <label for="event_name">
                                Event Name
                            </label>

                            <input
                                type="text"
                                id="event_name"
                                name="event_name"
                                value="<?php echo htmlspecialchars($event["Event_Name"]); ?>"
                                required
                            >

                        </div>

                        <div class="form-group">

                            <label for="event_date">
                                Event Date
                            </label>

                            <input
                                type="date"
                                id="event_date"
                                name="event_date"
                                value="<?php echo htmlspecialchars($event["Event_Date"]); ?>"
                                required
                            >

                        </div>

                        <div class="form-group">

                            <label for="event_time">
                                Event Time
                            </label>

                            <input
                                type="time"
                                id="event_time"
                                name="event_time"
                                value="<?php echo htmlspecialchars($event["Event_Time"]); ?>"
                                required
                            >

                        </div>

                        <div class="form-group">

                            <label for="venue">
                                Venue
                            </label>

                            <input
                                type="text"
                                id="venue"
                                name="venue"
                                value="<?php echo htmlspecialchars($event["Venue"]); ?>"
                                required
                            >

                        </div>

                        <div class="form-group">

                            <label for="status">
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                required
                            >

                                <option
                                    value="Upcoming"
                                    <?php
                                    echo $event["Status"] === "Upcoming"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Upcoming
                                </option>

                                <option
                                    value="Completed"
                                    <?php
                                    echo $event["Status"] === "Completed"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Completed
                                </option>

                                <option
                                    value="Cancelled"
                                    <?php
                                    echo $event["Status"] === "Cancelled"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Cancelled
                                </option>

                            </select>

                        </div>

                        <div class="form-group form-full">

                            <label for="description">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="5"
                            ><?php echo htmlspecialchars($event["Description"] ?? ""); ?></textarea>

                        </div>

                    </div>

                    <div class="form-actions">

                        <a
                            href="events.php"
                            class="secondary-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="primary-btn"
                        >
                            Update Event
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>

</body>

</html>