<?php
session_start();

include 'functions.php';

// Redirect to dashboard if user is already logged in
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$client_id = "1258119042401701928";
$client_secret = "s8ps-iplp2zcjV7yh_6B3maZYYDTYoZj";
$scopes = "identify+email+guilds+connections";
$redirect_url = "http://localhost/discord-servers/discord";

if (!isset($_GET['code'])) {
    // Redirect to Discord's authorization endpoint
    $authorize_url = "https://discord.com/api/oauth2/authorize?client_id={$client_id}&redirect_uri={$redirect_url}&response_type=code&scope={$scopes}";
    header("Location: {$authorize_url}");
    exit;
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    echo "Authorization Code: " . htmlspecialchars($code); // Debug: Show authorization code

    // Exchange code for access token
    $token_url = "https://discord.com/api/oauth2/token";
    $token_request_data = array(
        'grant_type' => 'authorization_code',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_url,
        'code' => $code
    );

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_request_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $token_data = json_decode($response, true);
    var_dump($token_data); // Debug: Check token response

    if (isset($token_data['access_token'])) {
        $access_token = $token_data['access_token'];

        // Fetch user data using access token
        $user_url = 'https://discord.com/api/users/@me';
        $ch = curl_init($user_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $user_response = curl_exec($ch);
        curl_close($ch);

        $user_data = json_decode($user_response, true);
        var_dump($user_data); // Debug: Check user data

        if (isset($user_data['id'])) {
            $avatarHash = $user_data['avatar'];
            $userId = $user_data['id'];
            $avatarExtension = (substr($avatarHash, 0, 2) === 'a_') ? 'gif' : 'png';
            $avatarUrl = "https://cdn.discordapp.com/avatars/{$userId}/{$avatarHash}.{$avatarExtension}";

            // Store user data and access token in session
            $_SESSION['user'] = $user_data;
            $_SESSION['user']['avatar_url'] = $avatarUrl;
            $_SESSION['user']['access_token'] = $access_token; // Store access token in session

            var_dump($_SESSION); // Debug: Check session after setting user data

            try {
                // Save or update user data in your database
                $stmt = $conn->prepare("SELECT * FROM users WHERE discord_id = ?");
                $stmt->bind_param('s', $userId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Update existing user record
                    $update_stmt = $conn->prepare("
                        UPDATE users SET username = ?, email = ?, avatar = ?, access_token = ?, refresh_token = ?, last_login = CURRENT_TIMESTAMP
                        WHERE discord_id = ?
                    ");
                    $update_stmt->bind_param(
                        'ssssss',
                        $user_data['username'],
                        $user_data['email'],
                        $avatarUrl,
                        $access_token,
                        $token_data['refresh_token'],
                        $userId
                    );
                    $update_stmt->execute();
                    $update_stmt->close();
                } else {
                    // Insert new user record
                    $insert_stmt = $conn->prepare("
                        INSERT INTO users (discord_id, username, email, avatar, access_token, refresh_token, last_login)
                        VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
                    ");
                    $insert_stmt->bind_param(
                        'ssssss',
                        $userId,
                        $user_data['username'],
                        $user_data['email'],
                        $avatarUrl,
                        $access_token,
                        $token_data['refresh_token']
                    );
                    $insert_stmt->execute();
                    $insert_stmt->close();
                }

                // Redirect to index or dashboard page
                header('Location: index.php');
                exit;

            } catch (Exception $e) {
                echo "Database error: " . $e->getMessage();
            }
        } else {
            echo "Failed to retrieve user data.";
        }
    } else {
        echo "Failed to obtain access token.";
    }
} else {
    echo "No code found in the URL.";
}