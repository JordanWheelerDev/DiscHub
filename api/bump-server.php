<?php
include '../functions.php'; // Adjust path as per your file structure

// Ensure POST data exists and is JSON
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// Ensure JSON data was successfully decoded
if ($input === null) {
    http_response_code(400); // Bad request
    echo json_encode(array('error' => 'Invalid JSON data'));
    exit;
}

// Retrieve and sanitize server_id from JSON data
$server_id = isset($input['server_id']) ? intval($input['server_id']) : null;

if (!$server_id) {
    http_response_code(400); // Bad request
    echo json_encode(array('error' => 'Invalid server ID'));
    exit;
}

// Retrieve server details
$stmt = $conn->prepare("SELECT last_bump FROM servers WHERE id = ?");
$stmt->bind_param('i', $server_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    http_response_code(404); // Not found
    echo json_encode(array('error' => 'Server not found'));
    exit;
}

$lastBump = new DateTime($row['last_bump']);
$nextBump = clone $lastBump;
$nextBump->add(new DateInterval('PT2H')); // Adjust time interval as needed

$currentUtcTime = new DateTime('now', new DateTimeZone('UTC'));

// Ensure enough time has passed since last bump
if ($currentUtcTime >= $nextBump) {
    // Perform database update to set last_bump timestamp in UTC
    $utcNow = $currentUtcTime->format('Y-m-d H:i:s');
    $stmt = $conn->prepare("UPDATE servers SET last_bump = ? WHERE id = ?");
    $stmt->bind_param('si', $utcNow, $server_id);

    if ($stmt->execute()) {
        // Success response
        http_response_code(200); // OK
        echo json_encode(array('success' => true));
    } else {
        // Error response
        http_response_code(500); // Internal server error
        echo json_encode(array('error' => 'Failed to update server bump'));
    }

    $stmt->close();
} else {
    // Return error response if bumping is attempted too soon
    http_response_code(403); // Forbidden
    echo json_encode(array('error' => 'Bump interval not reached'));
}
