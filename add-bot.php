<?php
include 'functions.php'; // Include your database connection and other functions

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ' . $base_url . '/index');
    exit;
}

// Get server ID from URL parameter
if (!isset($_GET['server_id'])) {
    // Handle error if server_id is not provided
    die('Server ID not provided.');
}

$server_id = $_GET['server_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bot</title>
    <!-- Include any necessary CSS or JavaScript -->
</head>

<body>
    <h2 style="text-align: center;">Adding Bot to Server ID <?php echo htmlspecialchars($server_id); ?></h2>

    <script>
        // Construct the bot invitation URL
        const clientId = '1258119042401701928'; // Replace with your bot's client ID
        const redirectUri = encodeURIComponent('<?php echo $discord_bot_uri; ?>');
        const inviteUrl = `https://discord.com/oauth2/authorize?client_id=${clientId}&scope=bot&permissions=19473&disable_guild_select=true&guild_id=<?php echo urlencode($server_id); ?>&redirect_uri=${redirectUri}&response_type=code`;

        // Redirect the user to the bot invitation URL
        window.location.href = inviteUrl;
    </script>
</body>


</html>