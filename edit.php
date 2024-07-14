<?php
include 'functions.php';

if (!isset($_SESSION['user'])) {
    header('Location: ' . $base_url . '/index');
    exit;
}

$user = $_SESSION['user'];
$pagename = "server";

if (isset($_GET['sid'])) {
    $sid = $_GET['sid'];
    $oid = $user['id'];
    $stmt = $conn->prepare("SELECT * FROM servers WHERE server_id = ? AND owner_id = ?");
    $stmt->bind_param('ss', $sid, $oid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header('Location: ' . $base_url . 'index');
        exit;
    }

    $guild = $result->fetch_assoc();
    $tags = explode(',', $guild['tags']); // Extract tags
} else {
    header('Location: ' . $base_url . 'index');
    exit;
}


if (isset($_POST['editServerBtn'])) {
    $name = $_POST['serverName'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $category_slug = $_POST['category_slug'];
    $tags = $_POST['tags'];
    $invite_link = $_POST['inviteLink'];
    $is_nsfw = $_POST['isNsfw'];
    $is_featured = $guild['is_featured'];

    $stmt = $conn->prepare("UPDATE servers SET name =?, description =?, category =?, category_slug =?, tags =?, invite_link =?, is_nsfw =?, is_featured =? WHERE server_id =? AND owner_id =?");
    $stmt->bind_param('ssssssiiii', $name, $description, $category, $category_slug, $tags, $invite_link, $is_nsfw, $is_featured, $sid, $oid);
    $stmt->execute();
    header('Location: ' . $base_url . 'my-servers');
}

if (isset($_POST['delServer'])) {
    $stmt = $conn->prepare("DELETE FROM servers WHERE server_id =? AND owner_id =?");
    $stmt->bind_param('ss', $sid, $oid);
    $stmt->execute();
    header('Location: ' . $base_url . 'my-servers');
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>/images/logo-w.png">
    <meta name="description" content="Discover and manage your Discord servers.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DiscHub | Editing <?php echo htmlspecialchars($guild['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/all.min.css">
    <script src="https://js.stripe.com/v3/"></script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9469778418525272"
        crossorigin="anonymous"></script>
</head>

<body>
    <?php include "parts/navbar.php"; ?>
    <div class="container mt-5 mb-5">
        <div class="row mb-4 d-flex justify-content-center">
            <div class="col-md-8">
                <?php
                $stmt = $conn->prepare("SELECT * FROM server_flags WHERE server_id =?");
                $stmt->bind_param('s', $sid);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                if ($result->num_rows > 0) {
                    if (empty($row['reasoning']) && empty($row['reviewed_by']) && $row['approved'] == 0) {
                        ?>
                        <div class="msg-danger mb-3">
                            <div><b>Oh No!</b> It appears that your server is currently under review. There are a
                                few things
                                that
                                could have flagged this:</div>
                            <div>
                                <ul>
                                    <li><b>Prohibited keywords</b>: your description, server name, or tags may have flagged our
                                        prohibited keyword algorithm.</li>
                                    <li><b>ToS breaking content</b>: your server may have content that goes against our <a
                                            href="<?php echo $base_url; ?>/tos">Terms of Service</a>.</li>
                                    <li><b>User reports</b>: your listing may have been reported several times - thus forcing us
                                        conduct an investigation.</li>
                                </ul>
                            </div>
                            <div>
                                <b>Please note:</b> We're here to help and we'll do our best to get your server approved as soon
                                as possible.
                                When we have reviewed your sever and if we respond with approval then this message will be
                                disappear, if we respond with a rejection, this message will be replaced with the reasoning for
                                rejection.
                            </div>
                        </div>
                    <?php } else if ($row['approved'] == 0 && !empty($row['reasoning'])) { ?>
                            <div class="msg-danger mb-3">
                                <div><b>Oh No!</b> It appears that your server has been rejected.</div>
                                <div>
                                    <b>Reasoning:</b> <?php echo htmlspecialchars($row['reasoning']); ?>
                                </div>
                            </div>
                    <?php }
                } ?>
                <div class="servers">
                    <div class="server">
                        <div class="mb-3 ds-header-m">
                            You're Editing <?php echo htmlspecialchars($guild['name']); ?>
                        </div>
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="serverName" class="ds-label">Server Name</label>
                                <input type="text" name="serverName" id="serverName"
                                    value="<?php echo htmlspecialchars($guild['name']); ?>" class="ds-input" readonly>
                            </div>
                            <input type="hidden" name="servId" id="servId"
                                value="<?php echo htmlspecialchars($serverId); ?>">
                            <div class="mb-3">
                                <label for="description" class="ds-label">Server Description</label>
                                <textarea id="description" class="ds-textarea" name="description" rows="4"
                                    cols="50"><?php echo htmlspecialchars($guild['description']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="ds-label">Server Category</label>
                                <select name="category" id="category" class="ds-select">
                                    <option value="<?php echo htmlspecialchars($guild['category']); ?>" selected>
                                        <?php echo htmlspecialchars($guild['category']); ?>
                                    </option>
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
                                <!-- Show tags, split them from database -->
                                <div id="tagsContainer" class="mb-2">
                                    <?php foreach ($tags as $tag) { ?>
                                        <span class="form-tag"
                                            onclick="removeTag('<?php echo htmlspecialchars($tag); ?>', this)">
                                            <?php echo htmlspecialchars($tag); ?>
                                        </span>
                                    <?php } ?>
                                </div>
                                <input type="hidden" id="tags" name="tags"
                                    value="<?php echo htmlspecialchars($guild['tags']); ?>">
                                <div id="tagLimitMsg" class="form-help-text" style="color: red; display: none;">
                                    Maximum of 5 tags allowed.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="inviteLink" class="ds-label">Server Invite Link</label>
                                <input type="text" id="inviteLink" class="ds-input" name="inviteLink"
                                    value="<?php echo $guild['invite_link']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="isNsfw" class="ds-label">Is this server NSFW?</label>
                                <select name="isNsfw" id="isNsfw" class="ds-select">
                                    <?php
                                    if ($guild['is_nsfw'] == 1) {
                                        ?>
                                        <option value="1" selected>Yes</option>
                                        <option value="0">No</option>
                                    <?php } else { ?>
                                        <option value="1">Yes</option>
                                        <option value="0" selected>No</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <button type="submit" name="editServerBtn" class="btn btn-primary">Update
                                <?php echo $guild['name']; ?></button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card ds-card rounded-0 mb-3">
                    <div class="card-body">
                        <div class="mb-3 ds-header-s">Analytics</div>
                        <div class="d-flex justify-content-between">
                            <div>Views</div>
                            <div><?php echo $guild['views']; ?></div>
                        </div>
                    </div>
                </div>
                <form action="" method="post">
                    <button type="submit" name="delServer" class="del-serv-btn">Delete
                        <?php echo $guild['name']; ?></button>
                </form>
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
        // Initialize tags array from PHP
        var tagsArray = <?php echo json_encode($tags); ?>;

        function addTag(event) {
            var tagsInput = document.getElementById('tagsInput');
            var tagsContainer = document.getElementById('tagsContainer');
            var tagsHiddenInput = document.getElementById('tags');
            var tagLimitMsg = document.getElementById('tagLimitMsg');
            var tagText = tagsInput.value.trim();

            // Check if the input contains a comma
            if (tagText.includes(',')) {
                // Remove the comma and any trailing spaces
                tagText = tagText.slice(0, -1).trim();

                // Convert to lowercase and replace spaces with hyphens
                tagText = tagText.toLowerCase().replace(/\s+/g, '-');

                // Check if the tag already exists
                if (tagsArray.includes(tagText)) {
                    alert('Tag already exists.');
                    tagsInput.value = '';
                    return;
                }

                // Ensure the tags input doesn't exceed 5 tags
                if (tagsArray.length >= 5) {
                    tagsInput.disabled = true;
                    tagLimitMsg.style.display = 'block';
                    return;
                }

                if (tagText !== '') {
                    // Create a new span element for the tag
                    var tagSpan = document.createElement('span');
                    tagSpan.className = 'form-tag';
                    tagSpan.textContent = tagText;

                    // Add an event listener to remove the tag when clicked
                    tagSpan.addEventListener('click', function () {
                        removeTag(tagText, tagSpan);
                    });

                    // Append the span to the tags container
                    tagsContainer.appendChild(tagSpan);

                    // Add tag to the tags array
                    tagsArray.push(tagText);

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
            }
        }

        document.getElementById('tagsInput').addEventListener('input', addTag);

        function removeTag(tagText, tagElement) {
            var tagIndex = tagsArray.indexOf(tagText);
            if (tagIndex > -1) {
                tagsArray.splice(tagIndex, 1);
            }

            tagElement.parentNode.removeChild(tagElement);
            document.getElementById('tags').value = tagsArray.join(',');

            if (tagsArray.length < 5) {
                document.getElementById('tagsInput').disabled = false;
                document.getElementById('tagLimitMsg').style.display = 'none';
            }
        }
    </script>
</body>

</html>