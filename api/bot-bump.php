<?php
include '../functions.php';

// Ensure this file is accessed via POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit('Method Not Allowed');
}

// Validate incoming data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['server_id'])) {
    http_response_code(400); // Bad Request
    exit('Invalid data received');
}

$server_id = $data['server_id'];

// Query to fetch last bump time for the server
$stmt = $conn->prepare("SELECT last_bump FROM servers WHERE server_id = ?");
$stmt->bind_param('i', $server_id);
$stmt->execute();
$stmt->bind_result($last_bump);
$stmt->fetch();
$stmt->close();

// Calculate cooldown period (2 hours in seconds)
$cooldown_duration = 2 * 60 * 60; // 2 hours in seconds

// Calculate time since last bump
$current_time = time();
$last_bump_time = strtotime($last_bump);
$time_since_last_bump = $current_time - $last_bump_time;

// Determine if cooldown has expired
$cooldown_expired = ($time_since_last_bump >= $cooldown_duration);

// Prepare response data
$response = [
    'cooldownExpired' => $cooldown_expired,
];

if (!$cooldown_expired) {
    // Calculate remaining time until cooldown expires
    $remaining_time = $cooldown_duration - $time_since_last_bump;
    $response['remainingTime'] = $remaining_time;
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Update last bump time if cooldown has expired
if ($cooldown_expired) {
    $newtime = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("UPDATE servers SET last_bump = ? WHERE server_id = ?");
    $stmt->bind_param('si', $newtime, $server_id);
    $stmt->execute();
    $stmt->close();
}
