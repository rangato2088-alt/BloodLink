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

$sql = "SELECT Event_ID, Event_Name
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

$sql = "DELETE FROM events
        WHERE Event_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $event_id
);

if ($stmt->execute()) {

    $stmt->close();

    header("Location: events.php?deleted=1");
    exit();

}

$stmt->close();

header("Location: events.php?delete_error=1");
exit();

?>