<?php

session_start();

if (!isset($_SESSION["hospital_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../admin/include/db.php";

$hospital_id = $_SESSION["hospital_id"];
$hospital_name = $_SESSION["hospital_name"];

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $event_name = trim($_POST["event_name"]);
    $event_date = $_POST["event_date"];
    $event_time = $_POST["event_time"];
    $venue = trim($_POST["venue"]);
    $description = trim($_POST["description"]);
    $status = $_POST["status"];


    $sql = "INSERT INTO events
            (
                Hospital_ID,
                Event_Name,
                Event_Date,
                Event_Time,
                Venue,
                Description,
                Status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "issssss",
            $hospital_id,
            $event_name,
            $event_date,
            $event_time,
            $venue,
            $description,
            $status
        );

        if ($stmt->execute()) {

            header("Location: events.php");
            exit();

        } else {

            $error = "Failed to create event. Please try again.";

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

    <title>Create Event | BloodLink</title>

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

                    <h1>Create Event</h1>

                    <p>
                        Create a new blood donation event
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
                                Enter the details of the blood donation event.
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
                                    placeholder="Enter event name"
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
                                    placeholder="Enter event venue"
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
                                placeholder="Enter event description"
                            ></textarea>

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

                                <option value="Upcoming">
                                    Upcoming
                                </option>

                                <option value="Ongoing">
                                    Ongoing
                                </option>

                                <option value="Completed">
                                    Completed
                                </option>

                                <option value="Cancelled">
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
                                Create Event
                            </button>

                        </div>

                    </form>

                </div>

            </section>

        </main>

    </div>

</body>

</html>