<?php
include 'functions.php'; // Include your database connection and other functions


$serverId = $_POST['server_id']; // Assuming you pass server_id via POST

// Retrieve server details including last_bump time
$stmt = $conn->prepare("SELECT last_bump FROM servers WHERE id = ?");
$stmt->bind_param('i', $serverId);
$stmt->execute();

$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $lastBumpTime = strtotime($row['last_bump']);
    $currentTime = time();
    $cooldown = 2 * 60 * 60; // 2 hours in seconds
    $timeSinceBump = $currentTime - $lastBumpTime;
    $secondsRemaining = max(0, $cooldown - $timeSinceBump); // Ensure no negative values

    echo $secondsRemaining; // Output remaining seconds
} else {
    header('HTTP/1.1 404 Not Found');
}

$stmt->close();
