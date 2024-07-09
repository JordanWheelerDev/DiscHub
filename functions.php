<?php
include 'config/db.php';

define("BASE_URL", "/discord-servers");

$stmt = $conn->prepare("SELECT * FROM settings");
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();

$base_url = $settings['base_url'];
$discord_bot_uri = $settings['discord_bot_uri'];
$discord_login_uri = $settings['discord_login_uri'];

$stmt->close();

date_default_timezone_set('UTC');

session_start();

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}

function limit_words($text, $limit)
{
    if (str_word_count($text, 0) > $limit) {
        $words = str_word_count($text, 2);
        $pos = array_keys($words);
        $text = substr($text, 0, $pos[$limit]) . '...';
    }
    return $text;
}

function getMostUsedTags()
{
    global $conn;

    // Check if the connection is still active
    if (!$conn || $conn->connect_errno) {
        die("Database connection error: " . $conn->connect_error);
    }

    // Prepare the SQL statement
    $sql = "SELECT tags FROM servers";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Execute the statement
    $stmt->execute();

    // Bind the result variable
    $stmt->bind_result($tags);

    $tagCounts = [];

    // Fetch the results and process each row
    while ($stmt->fetch()) {
        $tagList = explode(',', $tags);

        foreach ($tagList as $tag) {
            $tag = trim(strtolower($tag)); // Trim and convert to lowercase

            if (!empty($tag)) {
                if (!isset($tagCounts[$tag])) {
                    $tagCounts[$tag] = 1;
                } else {
                    $tagCounts[$tag]++;
                }
            }
        }
    }

    // Close the statement
    $stmt->close();

    // Sort tags by their counts in descending order
    arsort($tagCounts);

    // Display up to 8 most used tags wrapped in <span>
    $count = 0;
    foreach ($tagCounts as $tag => $countValue) {
        if ($count >= 8) {
            break;
        }
        echo "<a href='search/" . $tag . "' style='text-decoration:none;'><span class='tag'>#$tag</span></a>";
        $count++;
    }
}


function getAds()
{
    global $conn;

    // Prepare the SQL statement
    $sql = "SELECT id, title, description, image, link, discord_link, active_until FROM ads WHERE active_until > NOW()";
    $stmt = $conn->prepare($sql);

    // Execute the statement
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($id, $title, $description, $image, $link, $discord_link, $active_until);

    // Fetch results into an array
    $ads = [];
    while ($stmt->fetch()) {
        $ads[] = [
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'link' => $link,
            'discord_link' => $discord_link,
            'active_until' => $active_until,
        ];
    }

    // Close the statement
    $stmt->close();

    // Generate HTML for displaying ads
    echo '<div class="row mb-5 d-flex justify-content-center">';

    for ($i = 0; $i < 2; $i++) {
        if (isset($ads[$i])) {
            // Replace with ad details
            echo '<div class="col-md-6 col-sm-12 mb-3">';
            echo '<a href="' . $ads[$i]['link'] . '"><img src="' . htmlspecialchars($ads[$i]['image']) . '" alt="' . htmlspecialchars($ads[$i]['title']) . '" class="img-fluid ad-css"></a>';
            echo '</div>';
        } else {
            // Keep the placeholder
            echo '<div class="col-md-6 col-sm-12 mb-3">';
            echo '<a href="advertise"><img src="images/ad/yah.png" alt="" class="img-fluid ad-css"></a>';
            echo '</div>';
        }
    }

    echo '</div>';
}

function getRecentlyBumpedServers()
{
    global $conn;

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM servers ORDER BY last_bump DESC LIMIT 10");
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $tags = explode(',', $row['tags']); // Assuming 'tags' is the column name in your database
        $member_count = $row['user_count'];

        $category = $row['category'];

        echo '<a href="server/' . $row['server_id'] . '" class="ds-server-link">';
        echo '<div class="ds-servers mb-2">';
        echo '    <div class="ds-server">';
        echo '        <div class="d-flex justify-content-between mb-3">';
        echo '            <div class="title-area">';
        echo '                <img src="' . $row['server_image'] . '" alt="Server Image" class="server-image" style="margin-right:10px;">';
        echo '                ' . htmlspecialchars($row['name']) . ' | <span class="category">' . htmlspecialchars($category) . '</span>';

        if ($row['is_nsfw'] == 1) {
            echo ' <span class="is_nsfw">NSFW</span>';
        }

        echo '            </div>';
        echo '            <div><span class="server-info"><i class="fa-light fa-user" style="margin-right: 5px;"></i> ' . number_format($member_count) . '</span></div>';
        echo '        </div>';
        echo '        <div class="mb-3">';
        foreach ($tags as $tag) {
            echo '            <span class="server-tag">' . htmlspecialchars(trim($tag)) . '</span>';
        }
        echo '        </div>';
        echo '        <div class="description">' . limit_words(htmlspecialchars($row['description']), 50) . '</div>';
        echo '    </div>';
        echo '</div>';
        echo '</a>';

    }


    // Close the statement
    $stmt->close();
}

function getFeaturedServers()
{
    global $conn;

    // Prepare the SQL statement
    $is_featured = 1;
    $stmt = $conn->prepare("SELECT * FROM servers WHERE is_featured = ? ORDER BY RAND() LIMIT 3");
    $stmt->bind_param('i', $is_featured);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Loop through the results and generate HTML
    while ($row = $result->fetch_assoc()) {
        // Explode the tags into an array
        $tags = explode(',', $row['tags']); // Assuming 'tags' is the column name in your database
        $member_count = "123123"; // You might want to replace this with $row['member_count'] if you fetch this from DB.

        // Fetch the category (assuming a 'category' column exists in the database)
        $category = $row['category'];

        echo '<a href="server/' . $row['id'] . '" class="ds-server-link">';
        echo '<div class="ds-servers-featured">';
        echo '    <div class="ds-server-featured">';
        echo '        <div class="d-flex justify-content-between mb-3">';
        echo '            <div class="title-area">' . htmlspecialchars($row['name']) . ' | <span class="category">' . htmlspecialchars($category) . '</span>';

        // Check if the server is NSFW
        if ($row['is_nsfw'] == 1) {
            echo ' <span class="is_nsfw">NSFW</span>';
        }

        echo '            </div>';
        echo '            <div><span class="server-info"><i class="fa-light fa-user" style="margin-right: 5px;"></i> ' . number_format($member_count) . '</span></div>';
        echo '        </div>';
        echo '        <div class="mb-3">';

        // Loop through tags and display them
        foreach ($tags as $tag) {
            echo '            <span class="server-tag">' . htmlspecialchars(trim($tag)) . '</span>';
        }

        echo '        </div>';
        echo '        <div class="description">' . htmlspecialchars($row['description']) . '</div>';
        echo '    </div>';
        echo '</div>';
        echo '</a>';
    }


    // Close the statement
    $stmt->close();
}

function searchServers($query)
{
    global $conn;

    // Sanitize the search query
    $query = "%" . $conn->real_escape_string($query) . "%";

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM servers WHERE tags LIKE ? OR description LIKE ? ORDER BY last_bump DESC");
    $stmt->bind_param('ss', $query, $query);
    $stmt->execute();
    $result = $stmt->get_result();

    $servers = [];
    while ($row = $result->fetch_assoc()) {
        $servers[] = $row;
    }

    $stmt->close();
    return $servers;
}