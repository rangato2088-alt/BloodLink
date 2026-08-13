<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "include/db.php";


// Check Donor ID

if (
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
) {
    header("Location: donors.php");
    exit();
}

$donor_id = (int) $_GET["id"];


// Check whether donor exists

$sql = "SELECT Donor_ID, Full_Name
        FROM donor
        WHERE Donor_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $donor_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: donors.php");
    exit();

}

$donor = $result->fetch_assoc();

$stmt->close();


// Delete donor

$sql = "DELETE FROM donor
        WHERE Donor_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $donor_id
);

if ($stmt->execute()) {

    $stmt->close();

    header("Location: donors.php?deleted=1");
    exit();

}

$stmt->close();

header("Location: donors.php?delete_error=1");
exit();

?>