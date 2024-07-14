<?php

include 'functions.php';

$pagename = "index";

if (!isset($_SESSION['user'])) {
    // do nothing if not logged in
} else {
    if (checkForBan()) {
        header('Location: ' . $base_url . '/banned');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>/images/logo-w.png">
    <meta name="description" content="Discover and manage your Discord servers.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DiscHub | Discord Server Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/all.min.css">
    <script>
        function handleSearch(event) {
            event.preventDefault();
            const query = document.querySelector('input[name="query"]').value.trim();
            if (query) {
                window.location.href = `search/${encodeURIComponent(query)}`;
            }
        }
    </script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9469778418525272"
        crossorigin="anonymous"></script>
</head>

<body>
    <?php include "parts/navbar.php"; ?>
    <div class="ds-home-header">
        <div class="mb-3">
            <h1>Discover <span id="discordServerTypes"></span></h1>
        </div>
        <div class="search-field-home">
            <form onsubmit="handleSearch(event)" class="mb-3 d-flex align-items-center">
                <input type="text" name="query" class="form-control me-2" placeholder="Search servers">
                <button class="btn btn-primary" type="submit"><i class="fa-light fa-magnifying-glass"></i></button>
            </form>
            <div class="mb-3 text-center">
                <?php getMostUsedTags(); ?>
            </div>
        </div>
    </div>
    <div class="container mt-5 mb-5">
        <div class="row mb-4">
            <div class="ds-header-m mb-4">Featured Servers</div>
            <?php
            $is_featured = 1;
            $stmt = $conn->prepare("SELECT server_id FROM featured_servers WHERE NOW() < featured_until");
            $stmt->execute();
            $result = $stmt->get_result();

            // Array to store server IDs
            $featuredServerIds = [];
            while ($row = $result->fetch_assoc()) {
                $featuredServerIds[] = $row['server_id'];
            }
            $stmt->close();

            // Query to fetch details of featured servers from the 'servers' table, excluding those owned by banned users
            if (!empty($featuredServerIds)) {
                $placeholders = implode(',', array_fill(0, count($featuredServerIds), '?'));
                $query = "
        SELECT s.* 
        FROM servers s
        LEFT JOIN bans b ON s.owner_id = b.user_id
        WHERE s.server_id IN ($placeholders) AND b.user_id IS NULL
    ";
                $stmt = $conn->prepare($query);
                $stmt->bind_param(str_repeat('i', count($featuredServerIds)), ...$featuredServerIds);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if any rows were returned
                if ($result->num_rows == 0) {
                    echo '<div class="col-12 text-center">No featured servers found.</div>';
                } else {
                    while ($row = $result->fetch_assoc()) {
                        $tags = explode(',', $row['tags']);
                        ?>
                        <div class="col-lg-4">
                            <a href="server/<?php echo $row['server_id']; ?>" class="ds-server-link">
                                <div class="ds-servers-featured">
                                    <div class="ds-server-featured">
                                        <div class="d-flex justify-content-between mb-3">
                                            <div class="title-area">
                                                <img src="<?php echo $row['server_image']; ?>" class="server-image-f" alt="">
                                                <?php echo htmlspecialchars($row['name']); ?> | <span
                                                    class="category"><?php echo htmlspecialchars($row['category']); ?></span>
                                                <?php if ($row['is_nsfw'] == 1) {
                                                    echo '<span class="is_nsfw">NSFW</span>';
                                                } ?>
                                            </div>
                                            <div><span class="server-info"><i class="fa-light fa-user"
                                                        style="margin-right: 5px;"></i>
                                                    <?php echo number_format($row['user_count']); ?></span></div>
                                        </div>
                                        <div class="mb-3">
                                            <?php foreach ($tags as $tag) {
                                                echo '<span class="form-tag-sm">#' . htmlspecialchars($tag) . '</span>';
                                            } ?>
                                        </div>
                                        <div class="description">
                                            <?php echo limit_words(htmlspecialchars($row['description']), 20); ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php }
                }
                $stmt->close();
            } else {
                echo '<div class="col-12 text-center">No featured servers found.</div>';
            }
            ?>

        </div>
        <div class="row">
            <div class="ds-header-m mb-3">Recently Bumped Servers</div>
            <div class="col-md-8">
                <?php getRecentlyBumpedServers(); ?>
            </div>
            <div class="col-md-4">
                <div class="card ds-card rounded-0">
                    <div class="card-body">
                        <!-- Sidebar Ad -->
                        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-9469778418525272"
                            data-ad-slot="2794913944" data-ad-format="auto" data-full-width-responsive="true"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="js/script.js"></script>
    <script>
        var currentPath = window.location.pathname.replace(/\/{2,}/g, "/");

        if (currentPath !== window.location.pathname) {
            window.location.replace(window.location.origin + currentPath);
        }

        const discoverTexts = [];

        async function fetchCategories() {
            try {
                const response = await fetch('api/get-categories.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                const categories = await response.json();
                return categories;
            } catch (error) {
                console.error('Error fetching categories:', error);
                return [];
            }
        }

        async function populateDiscoverTexts() {
            const categories = await fetchCategories();
            categories.forEach(category => {
                discoverTexts.push(category.category + " Servers");
            });

            // Start typing animation or any other function that uses discoverTexts
            type();
        }

        // Typing animation logic
        let index = 0;
        let currentText = "";
        let isDeleting = false;

        function type() {
            const fullText = discoverTexts[index];
            if (isDeleting) {
                currentText = fullText.substring(0, currentText.length - 1);
            } else {
                currentText = fullText.substring(0, currentText.length + 1);
            }
            document.getElementById("discordServerTypes").innerHTML = currentText;

            let speed = 200; // Adjust typing speed here

            if (isDeleting) {
                speed /= 2; // Adjust deleting speed here
            }

            if (!isDeleting && currentText === fullText) {
                isDeleting = true;
                speed = 500; // Time to wait before starting to delete
            } else if (isDeleting && currentText === "") {
                isDeleting = false;
                index = (index + 1) % discoverTexts.length;
                speed = 200; // Time to wait before starting to type the next text
            }

            setTimeout(type, speed);
        }

        // Start the process
        populateDiscoverTexts();
    </script>
</body>

</html>