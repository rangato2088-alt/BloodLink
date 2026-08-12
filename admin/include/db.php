<?php

$host = "localhost:3306";
$username = "user";
$password = "1234";
$database = "bloodlink_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

echo "Database connected successfully!";

?>