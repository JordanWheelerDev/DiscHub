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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DiscHub | Editing <?php echo htmlspecialchars($guild['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/all.min.css">
</head>

<body>
    <?php include "parts/navbar.php"; ?>
    <div class="container mt-5 mb-5">
        <div class="row mb-4 d-flex justify-content-center">
            <div class="col-md-8">
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
                <div class="card ds-card rounded-0">
                    <div class="card-body">
                        <div class="mb-3 ds-header-s">Analytics</div>
                        <div class="d-flex justify-content-between">
                            <div>Views</div>
                            <div><?php echo $guild['views']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('category').addEventListener('change', function () {
            var selectedOption = this.options[this.selectedIndex];
            var slug = selectedOption.getAttribute('data-slug');
            document.getElementById('category_slug').value = slug;
        });
        // Initialize tags array from PHP
        var tagsArray = <?php echo json_encode($tags); ?>;

        function addTag(event) {
            if (event.key === ',') {
                var tagsInput = document.getElementById('tagsInput');
                var tagsContainer = document.getElementById('tagsContainer');
                var tagsHiddenInput = document.getElementById('tags');
                var tagLimitMsg = document.getElementById('tagLimitMsg');
                var tagText = tagsInput.value.trim().slice(0, -1); // Remove the comma

                if (tagsArray.includes(tagText)) {
                    alert('Tag already exists.');
                    tagsInput.value = '';
                    return;
                }

                if (tagsArray.length >= 5) {
                    tagsInput.disabled = true;
                    tagLimitMsg.style.display = 'block';
                    return;
                }

                if (tagText !== '') {
                    var tagSpan = document.createElement('span');
                    tagSpan.className = 'form-tag';
                    tagSpan.textContent = tagText;
                    tagSpan.addEventListener('click', function () {
                        removeTag(tagText, tagSpan);
                    });

                    tagsContainer.appendChild(tagSpan);
                    tagsArray.push(tagText);
                    tagsHiddenInput.value = tagsArray.join(',');
                    tagsInput.value = '';

                    if (tagsArray.length < 5) {
                        tagsInput.disabled = false;
                        tagLimitMsg.style.display = 'none';
                    }
                }

                event.preventDefault();
            }
        }

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