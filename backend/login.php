<?php
include 'functions.php'; // Assuming $conn is already defined here

$client_id = "1258119042401701928";
$client_secret = "s8ps-iplp2zcjV7yh_6B3maZYYDTYoZj";
$scopes = "identify+email+guilds+connections";
$redirect_url = "http://localhost/discord-servers/login.php";

if (!isset($_GET['code'])) {
    $auth_url = "https://discord.com/api/oauth2/authorize?client_id={$client_id}&redirect_uri={$redirect_url}&response_type=code&scope={$scopes}";
    header("Location: {$auth_url}");
    exit;
}

// Step 2: If code is present, exchange it for an access token
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Exchange code for access token
    $token_url = "https://discord.com/api/oauth2/token";
    $token_data = array(
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirect_url,
        'scope' => $scopes
    );

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $token_response = curl_exec($ch);
    curl_close($ch);

    // Check if token exchange was successful
    $token_data = json_decode($token_response, true);
    if (isset($token_data['access_token'])) {
        $access_token = $token_data['access_token'];

        // Step 3: Fetch user data using the access token
        $user_url = "https://discord.com/api/users/@me";
        $ch = curl_init($user_url);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Bearer ' . $access_token
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $user_response = curl_exec($ch);
        curl_close($ch);

        // Check if user data retrieval was successful
        $user_data = json_decode($user_response, true);
        if (isset($user_data['id'])) {
            // Session handling
            session_start();
            $_SESSION['user'] = $user_data;

            // Optionally fetch avatar URL
            $avatarHash = $user_data['avatar'];
            $userId = $user_data['id'];
            $avatarExtension = (substr($avatarHash, 0, 2) === 'a_') ? 'gif' : 'png';
            $avatarUrl = "https://cdn.discordapp.com/avatars/{$userId}/{$avatarHash}.{$avatarExtension}";
            $_SESSION['user']['avatar_url'] = $avatarUrl;

            // Optional: Store or update user data in database
            // Replace with your database logic
            // Example: Insert/update user data in database
            // Example: header('Location: dashboard.php'); // Uncomment for actual use
            echo "User logged in successfully!";
            exit;
        } else {
            echo "Failed to fetch user data from Discord.";
        }
    } else {
        echo "Failed to obtain access token.";
    }
} else {
    echo "No code found in the URL.";
}