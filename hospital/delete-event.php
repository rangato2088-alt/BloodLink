<?php

session_start();

if (!isset($_SESSION["hospital_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../admin/include/db.php";

$hospital_id = $_SESSION["hospital_id"];


if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: events.php");
    exit();
}

$event_id = (int) $_GET["id"];


$sql = "DELETE FROM events
        WHERE Event_ID = ?
        AND Hospital_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $event_id,
    $hospital_id
);

$stmt->execute();

$stmt->close();

header("Location: events.php");
exit();

?>