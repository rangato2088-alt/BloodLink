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
    header("Location: hospitals.php");
    exit();
}

$hospital_id = (int) $_GET["id"];




$sql = "SELECT Hospital_ID, Hospital_Name
        FROM hospital
        WHERE Hospital_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $hospital_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: hospitals.php");
    exit();

}

$hospital = $result->fetch_assoc();

$stmt->close();



$sql = "DELETE FROM hospital
        WHERE Hospital_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $hospital_id
);

if ($stmt->execute()) {

    $stmt->close();

    header("Location: hospitals.php?deleted=1");
    exit();

}

$stmt->close();

header("Location: hospitals.php?delete_error=1");
exit();

?>