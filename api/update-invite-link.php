<?php
include '../functions.php';

// Check if POST data exists
if (isset($_POST['server_id'], $_POST['invite_link'])) {
    // Retrieve POST data
    $server_id = $_POST['server_id'];
    $invite_link = $_POST['invite_link'];

    // Example query: Update guild details
    $stmt = $conn->prepare("UPDATE servers SET invite_link = ? WHERE server_id = ?");
    $stmt->bind_param("si", $invite_link, $server_id);

    if ($stmt->execute()) {
        echo "Database updated successfully";
    } else {
        echo "Error updating database: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Missing POST data";
}

// Additional debugging: Log $_POST data
error_log(print_r($_POST, true));
