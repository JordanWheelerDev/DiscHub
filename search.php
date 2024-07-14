<?php
include 'functions.php';

$pagename = "search";
$searchResults = [];

if (isset($_GET['query'])) {
    $query = trim($_GET['query']);
    $searchResults = searchServers($query);
}
if (isset($_SESSION['user'])) {
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
    <title>Search Results</title>
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
            <div class="ds-header-m mb-4">Featured Servers</div>
            <?php
            // Fetch featured server IDs
            $is_featured = 1;
            $stmt = $conn->prepare("SELECT server_id FROM featured_servers WHERE NOW() < featured_until");
            $stmt->execute();
            $result = $stmt->get_result();

            // Array to store featured server IDs
            $featuredServerIds = [];
            while ($row = $result->fetch_assoc()) {
                $featuredServerIds[] = $row['server_id'];
            }
            $stmt->close();

            // Query to fetch details of featured servers with additional filters
            if (!empty($featuredServerIds)) {
                $placeholders = implode(',', array_fill(0, count($featuredServerIds), '?'));
                $tagsFilter = '%' . $query . '%'; // Adjust as per your search criteria
                $descriptionFilter = '%' . $query . '%'; // Adjust as per your search criteria
            
                $qry = "SELECT * FROM servers WHERE server_id IN ($placeholders) AND (tags LIKE ? OR description LIKE ?) ORDER BY RAND() LIMIT 3";
                $stmt = $conn->prepare($qry);

                // Bind parameters: server IDs followed by tags and description filters
                $bindParams = str_repeat('i', count($featuredServerIds)) . 'ss'; // 'ss' for two string parameters
                $bindValues = array_merge($featuredServerIds, [$tagsFilter, $descriptionFilter]);
                $stmt->bind_param($bindParams, ...$bindValues);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if any rows were returned
                if ($result->num_rows == 0) {
                    echo '<div class="col-12 text-center">No featured servers found.</div>';
                } else {
                    while ($row = $result->fetch_assoc()) {
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
                                        <div class="description">
                                            <?php echo limit_words(htmlspecialchars($row['description']), 50); ?>
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
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="ds-servers">
                    <?php if (!empty($searchResults)): ?>
                        <?php foreach ($searchResults as $server): ?>
                            <div class="ds-server mb-2">
                                <a href="<?php echo $base_url; ?>/server/<?php echo htmlspecialchars($server['server_id']); ?>"
                                    class="ds-server-link">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div class="title-area">
                                            <img src="<?php echo $server['server_image']; ?>" class="server-image" alt="">
                                            <?php echo htmlspecialchars($server['name']); ?> |
                                            <span class="category"><?php echo htmlspecialchars($server['category']); ?></span>
                                        </div>
                                        <div>
                                            <span class="server-info"><i class="fa-light fa-user"
                                                    style="margin-right: 5px;"></i>
                                                <?php echo number_format($server['user_count']); ?></span>
                                        </div>
                                    </div>
                                    <div class="description">
                                        <?php echo limit_words(htmlspecialchars($server['description']), 50); ?>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-md-12 text-center">No servers found!</div>
                    <?php endif; ?>
                </div>
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
    <script>
        var currentPath = window.location.pathname.replace(/\/{2,}/g, "/");

        if (currentPath !== window.location.pathname) {
            window.location.replace(window.location.origin + currentPath);
        }
    </script>
</body>

</html>