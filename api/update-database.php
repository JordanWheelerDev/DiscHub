<?php
include '../functions.php';

$apiKey = "auZgmHp2YtZFFQmvgBFWz214pijB7TtEdT8etbj1bu4EvHgdBcUMnxh552SPpZrr";

if (isset($_POST['api_key']) && $_POST['api_key'] == $apiKey) {
    // Check if POST data exists
    if (isset($_POST['server_id'], $_POST['server_image'], $_POST['user_count'])) {
        // Retrieve POST data
        $server_id = $_POST['server_id'];
        $server_image = $_POST['server_image'];
        $user_count = $_POST['user_count'];

        // Example query: Update guild details
        $stmt = $conn->prepare("UPDATE servers SET server_image = ?, user_count = ? WHERE server_id = ?");
        $stmt->bind_param("ssi", $server_image, $user_count, $server_id);

        if ($stmt->execute()) {
            echo "Database updated successfully";
        } else {
            echo "Error updating database: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Missing POST data";
    }
} else {
    echo "Invalid or missing API key";
}