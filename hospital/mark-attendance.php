<?php

session_start();

if (!isset($_SESSION["hospital_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../admin/include/db.php";

$hospital_id = $_SESSION["hospital_id"];


if (
    !isset($_POST["registration_id"]) ||
    !isset($_POST["event_id"]) ||
    !is_numeric($_POST["registration_id"]) ||
    !is_numeric($_POST["event_id"])
) {
    header("Location: events.php");
    exit();
}


$registration_id = (int) $_POST["registration_id"];
$event_id = (int) $_POST["event_id"];



$sql = "SELECT Event_ID
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

$stmt->close();


// Mark the donor as attended

$sql = "UPDATE event_registration
        SET Attendance_Status = 'Attended'
        WHERE Registration_ID = ?
        AND Event_ID = ?
        AND Attendance_Status = 'Registered'";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $registration_id,
    $event_id
);

$stmt->execute();

$stmt->close();



header(
    "Location: event-attendance.php?event_id="
    . $event_id
    . "&registration_id="
    . $registration_id
);

exit();

?>