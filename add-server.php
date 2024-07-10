<?php
include 'functions.php'; // Include your database connection and other functions

$pagename = "add server";

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ' . $base_url . '/login'); // Redirect to login if not logged in
    exit;
}

// Get user data from session
$user = $_SESSION['user'];
$userId = $user['id'];
$accessToken = $_SESSION['user']['access_token'];

// Fetch user's guilds from Discord
$guildsUrl = 'https://discord.com/api/users/@me/guilds';
$ch = curl_init($guildsUrl);
curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    array(
        'Authorization: Bearer ' . $accessToken
    )
);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$guildsResponse = curl_exec($ch);
curl_close($ch);

$guildsData = json_decode($guildsResponse, true);
if (!$guildsData) {
    die('Failed to fetch guilds from Discord.');
}

// Filter guilds to get only the ones the user owns or administers
$ownedGuilds = [];
foreach ($guildsData as $guild) {
    $permissions = $guild['permissions'];

    // Check if permissions is an array or object, otherwise handle it
    if (!is_array($permissions) && !is_object($permissions)) {
        // Convert integer permissions bitmask to array of permissions
        $guildPermissions = [];
        for ($i = 0; $i < strlen(decbin($permissions)); $i++) {
            if (($permissions & pow(2, $i)) != 0) { // Permission is set
                $guildPermissions[] = $i;
            }
        }
    } else {
        // Permissions is already an array or object, use it directly
        $guildPermissions = $permissions;
    }

    // Check if user owns the guild or has manage permissions
    if ($guild['owner'] || in_array('MANAGE_GUILD', $guildPermissions) || in_array('ADMINISTRATOR', $guildPermissions)) {
        // Check if the guild already exists in the database
        $serverId = $guild['id'];
        $stmt = $conn->prepare("SELECT * FROM servers WHERE server_id = ?");
        $stmt->bind_param('s', $serverId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $ownedGuilds[] = $guild;
        }
    }
}

if (isset($_POST['addServerBtn'])) {
    $server_id = $_POST['servId'];
    $name = $_POST['serverName'];
    $description = $_POST['description'];
    $invite_link = $_POST['inviteLink'];
    $category = $_POST['category'];
    $category_slug = $_POST['category_slug'];
    $tags = $_POST['tags'];
    $server_image = "";
    $owner_id = $userId;
    $last_bump = date('Y-m-d H:i:s');
    $user_count = ""; // get the user count for the server;
    $is_nsfw = 0; // set it to 0 by default;
    $views = 0; // set it to 0 by default;

    // Add server to the database
    $stmt = $conn->prepare("INSERT INTO servers (server_id, name, description, invite_link, category, category_slug, tags, server_image, owner_id, last_bump, user_count, is_nsfw, views) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('ssssssssssiis', $server_id, $name, $description, $invite_link, $category, $category_slug, $tags, $server_image, $owner_id, $last_bump, $user_count, $is_nsfw, $views);
    $stmt->execute();

    // Redirect to the servers page
    header('Location: add-bot?server_id=' . urlencode($server_id));
    exit;

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>/images/logo-w.png">
    <meta name="description" content="Discover and manage your Discord servers.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DiscHub | Add Your Server</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/all.min.css">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9469778418525272"
        crossorigin="anonymous"></script>
</head>

<body>
    <?php include "parts/navbar.php"; ?>
    <div class="container mt-5 mb-5">
        <div class="row mb-4">
            <div class="ds-header-m mb-4">Add Your Server</div>
            <div class="col-md-4">
                <div class="card ds-card rounded-0">
                    <div class="card-body">
                        <select name="serverSelect" class="ds-select" id="serverSelect" onchange="showAddForm()">
                            <option value="" selected>Select a server</option>
                            <?php foreach ($ownedGuilds as $guild): ?>
                                <option value="<?php echo htmlspecialchars($guild['id']); ?>">
                                    <?php echo htmlspecialchars($guild['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card ds-card rounded-0">
                    <div class="card-body">
                        <div id="selectServerMsg">Select a server from the left.</div>
                        <div id="addFormContainer" style="display: none;">
                            <form action="" method="post">
                                <div class="mb-3">
                                    <label for="serverName" class="ds-label">Server Name</label>
                                    <input type="text" name="serverName" id="serverName" class="ds-input" readonly>
                                </div>
                                <input type="hidden" name="servId" id="servId" value="<?php echo $serverId; ?>">
                                <div class="mb-3">
                                    <label for="description" class="ds-label">Server Description</label>
                                    <textarea id="description" class="ds-textarea" name="description" rows="4"
                                        cols="50"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="category" class="ds-label">Server Category</label>
                                    <select name="category" id="category" class="ds-select">
                                        <option value="" selected>Please select a category</option>
                                        <?php
                                        $stmt = $conn->prepare("SELECT * FROM categories");
                                        $stmt->execute();

                                        $result = $stmt->get_result();

                                        while ($row = $result->fetch_assoc()) {
                                            ?>
                                            <option value="<?php echo htmlspecialchars($row['category']); ?>"
                                                data-slug="<?php echo htmlspecialchars($row['slug']); ?>">
                                                <?php echo htmlspecialchars($row['category']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <input type="hidden" name="category_slug" id="category_slug" value="">
                                </div>
                                <div class="mb-3">
                                    <label for="tags" class="ds-label">Server Tags</label>
                                    <div class="mb-3 form-help-text">
                                        <ul>
                                            <li>Up to 5 tags allowed.</li>
                                            <li>Use a comma to separate tags.</li>
                                            <li>Click on a tag to remove.</li>
                                        </ul>
                                    </div>
                                    <input type="text" id="tagsInput" class="ds-input mb-2" onkeyup="addTag(event)">
                                    <div id="tagsContainer" class="mb-2"></div>
                                    <input type="hidden" id="tags" name="tags">
                                    <div id="tagLimitMsg" class="form-help-text" style="color: red; display: none;">
                                        Maximum of 5 tags allowed.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="inviteLink" class="ds-label">Server Invite Link</label>
                                    <small>This is generated after you click "Add Server", invite the bot to your server
                                        and type !setup in a channel where the bot has permission.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="isNsfw" class="ds-label">Is this server NSFW?</label>
                                    <select name="isNsfw" id="isNsfw" class="ds-select">
                                        <option selected>Please select an option</option>
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <!-- Hidden inputs -->
                                <!-- <input type="hidden" id="serverId" name="serverId" value="">
                                <input type="hidden" id="ownerId" name="ownerId" value=""> -->
                                <button type="submit" class="btn btn-primary" name="addServerBtn">Add Server</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var currentPath = window.location.pathname.replace(/\/{2,}/g, "/");

        if (currentPath !== window.location.pathname) {
            window.location.replace(window.location.origin + currentPath);
        }

        document.getElementById('category').addEventListener('change', function () {
            var selectedOption = this.options[this.selectedIndex];
            var slug = selectedOption.getAttribute('data-slug');
            document.getElementById('category_slug').value = slug;
        });
        // Array to hold tags
        var tagsArray = [];

        function showAddForm() {
            var serverSelect = document.getElementById('serverSelect');
            var selectedIndex = serverSelect.selectedIndex;
            if (selectedIndex >= 0) { // Ensure a valid selection
                var serverName = serverSelect.options[selectedIndex].text;
                document.getElementById('serverName').value = serverName;
                document.getElementById('servId').value = serverSelect.value;
                document.getElementById('addFormContainer').style.display = 'block';
                document.getElementById('selectServerMsg').style.display = 'none';
            } else {
                alert('Please select a server.');
            }
        }

        function addTag() {
            var tagsInput = document.getElementById('tagsInput');
            var tagsContainer = document.getElementById('tagsContainer');
            var tagsHiddenInput = document.getElementById('tags');
            var tagLimitMsg = document.getElementById('tagLimitMsg');
            var tagText = tagsInput.value.trim();

            // Split the input value by commas
            var tagParts = tagText.split(',');

            // Process each part
            for (var i = 0; i < tagParts.length; i++) {
                var tag = tagParts[i].trim();

                // Skip empty parts
                if (tag === '') continue;

                // Check if the tag already exists
                if (tagsArray.includes(tag)) {
                    alert('Tag already exists: ' + tag);
                    continue;
                }

                // Ensure the tags input doesn't exceed 5 tags
                if (tagsArray.length >= 5) {
                    tagsInput.disabled = true;
                    tagLimitMsg.style.display = 'block';
                    break;
                }

                // Create a new span element for the tag
                var tagSpan = document.createElement('span');
                tagSpan.className = 'form-tag';
                tagSpan.textContent = tag;

                // Add an event listener to remove the tag when clicked
                tagSpan.addEventListener('click', function () {
                    removeTag(tag, tagSpan);
                });

                // Append the span to the tags container
                tagsContainer.appendChild(tagSpan);

                // Add tag to the tags array
                tagsArray.push(tag);
            }

            // Update the hidden input with the tags array
            tagsHiddenInput.value = tagsArray.join(',');

            // Clear the input field
            tagsInput.value = '';

            // Hide the tag limit message if previously displayed
            if (tagsArray.length < 5) {
                tagsInput.disabled = false;
                tagLimitMsg.style.display = 'none';
            }
        }

        document.getElementById('tagsInput').addEventListener('input', addTag);

        function removeTag(tagText, tagElement) {
            // Find the index of the tag and remove it from the array
            var tagIndex = tagsArray.indexOf(tagText);
            if (tagIndex > -1) {
                tagsArray.splice(tagIndex, 1);
            }

            // Remove the tag element from the DOM
            tagElement.parentNode.removeChild(tagElement);

            // Update the hidden input with the new tags array
            document.getElementById('tags').value = tagsArray.join(',');

            // Enable the input field if less than 5 tags
            if (tagsArray.length < 5) {
                document.getElementById('tagsInput').disabled = false;
                document.getElementById('tagLimitMsg').style.display = 'none';
            }
        }


    </script>
</body>

</html>