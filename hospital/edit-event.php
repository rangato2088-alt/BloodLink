<?php

session_start();

if (!isset($_SESSION["hospital_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../admin/include/db.php";

$hospital_id = $_SESSION["hospital_id"];
$hospital_name = $_SESSION["hospital_name"];




if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: events.php");
    exit();
}

$event_id = (int) $_GET["id"];




$sql = "SELECT Event_ID,
               Event_Name,
               Event_Date,
               Event_Time,
               Venue,
               Description,
               Status
        FROM events
        WHERE Event_ID = ?
        AND Hospital_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $event_id,
    $hospital_id
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

$error = "";




if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $event_name = trim($_POST["event_name"]);
    $event_date = $_POST["event_date"];
    $event_time = $_POST["event_time"];
    $venue = trim($_POST["venue"]);
    $description = trim($_POST["description"]);
    $status = $_POST["status"];


    $sql = "UPDATE events
            SET Event_Name = ?,
                Event_Date = ?,
                Event_Time = ?,
                Venue = ?,
                Description = ?,
                Status = ?
            WHERE Event_ID = ?
            AND Hospital_ID = ?";


    $stmt = $conn->prepare($sql);


    if ($stmt) {

        $stmt->bind_param(
            "ssssssii",
            $event_name,
            $event_date,
            $event_time,
            $venue,
            $description,
            $status,
            $event_id,
            $hospital_id
        );


        if ($stmt->execute()) {

            $stmt->close();

            header("Location: events.php");
            exit();

        } else {

            $error = "Failed to update event. Please try again.";

        }


        $stmt->close();

    } else {

        $error = "Database error. Please try again.";

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Event | BloodLink</title>

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

                <a href="events.php" class="nav-link active">
                    Events
                </a>

                <a href="donations.php" class="nav-link">
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

                    <h1>Edit Event</h1>

                    <p>
                        Update your blood donation event
                    </p>

                </div>

                <div class="hospital-user">

                    <?php echo htmlspecialchars($hospital_name); ?>

                </div>

            </header>


            <section class="dashboard-content">

                <div class="dashboard-section event-form-section">

                    <div class="section-header">

                        <div>

                            <h2>Event Information</h2>

                            <p>
                                Update the event details below.
                            </p>

                        </div>

                    </div>

                    <?php if ($error !== ""): ?>

                         <div class="registration-error">
                            <?php echo htmlspecialchars($error); ?>
                        </div>

                    <?php endif; ?>


                    <form method="POST" action="">


                        <div class="form-grid">


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


                        </div>


                        <div class="form-group">

                            <label for="description">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="5"
                            ><?php echo htmlspecialchars($event["Description"]); ?></textarea>

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

                                <option value="Upcoming"
                                    <?php echo ($event["Status"] === "Upcoming") ? "selected" : ""; ?>>
                                    Upcoming
                                </option>

                                <option value="Ongoing"
                                    <?php echo ($event["Status"] === "Ongoing") ? "selected" : ""; ?>>
                                    Ongoing
                                </option>

                                <option value="Completed"
                                    <?php echo ($event["Status"] === "Completed") ? "selected" : ""; ?>>
                                    Completed
                                </option>

                                <option value="Cancelled"
                                    <?php echo ($event["Status"] === "Cancelled") ? "selected" : ""; ?>>
                                    Cancelled
                                </option>

                            </select>

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