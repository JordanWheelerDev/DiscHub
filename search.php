<?php
include 'functions.php';

$pagename = "search";
$searchResults = [];

if (isset($_GET['query'])) {
    $query = trim($_GET['query']);
    $searchResults = searchServers($query);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
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
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="ds-servers">
                    <?php if (!empty($searchResults)): ?>
                        <?php foreach ($searchResults as $server): ?>
                            <div class="ds-server">
                                <a href="server/<?php echo htmlspecialchars($server['server_id']); ?>" class="ds-server-link">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div class="title-area">
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
                                        <?php echo htmlspecialchars($server['description']); ?>
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
                <div class="servers mb-4">
                    <div class="server">
                        <!-- Additional content or filters -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>