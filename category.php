<?php
include 'functions.php';

$pagename = "category";

if (!isset($_GET['category'])) {
    header('Location: index');
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
    <title>DiscHub | Browse Servers</title>
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
            <div class="col-md-8">
                <div class="ds-servers">
                    <?php
                    $category = $_GET['category'];
                    $is_public = 1;
                    $items_per_page = 10;

                    // Get the current page from the URL, defaulting to 1 if not set
                    $current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                    if ($current_page < 1) {
                        $current_page = 1;
                    }

                    // Calculate the offset for the query
                    $offset = ($current_page - 1) * $items_per_page;

                    // Fetch the total number of items
                    $total_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM servers WHERE category_slug = ? AND is_public = ?");
                    $total_stmt->bind_param('si', $category, $is_public);
                    $total_stmt->execute();
                    $total_result = $total_stmt->get_result();
                    $total_row = $total_result->fetch_assoc();
                    $total_items = $total_row['total'];
                    $total_pages = ceil($total_items / $items_per_page);

                    // Fetch the items for the current page
                    $stmt = $conn->prepare("SELECT * FROM servers WHERE category_slug = ? AND is_public = ? ORDER BY last_bump DESC LIMIT ? OFFSET ?");
                    $stmt->bind_param('siii', $category, $is_public, $items_per_page, $offset);
                    $stmt->execute();

                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $tags = explode(',', $row['tags']);
                        ?>
                        <div class="ds-server mb-2">
                            <a href="<?php echo $base_url; ?>/server/<?php echo htmlspecialchars($row['server_id']); ?>"
                                class="ds-server-link">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="title-area">
                                        <img src="<?php echo $row['server_image']; ?>" class="server-image" alt="">
                                        <?php echo htmlspecialchars($row['name']); ?> |
                                        <span class="category"><?php echo htmlspecialchars($row['category']); ?></span>
                                    </div>
                                    <div>
                                        <span class="server-info"><i class="fa-light fa-user"
                                                style="margin-right: 5px;"></i>
                                            <?php echo number_format($row['user_count']); ?></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <?php foreach ($tags as $tag) { ?>
                                        <span class="form-tag">#<?php echo htmlspecialchars($tag); ?></span>
                                    <?php } ?>
                                </div>
                                <div class="description">
                                    <?php echo htmlspecialchars($row['description']); ?>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <!-- Pagination -->
                    <div class="ds-pagination mt-4">
                        <?php if ($current_page > 1) { ?>
                            <a
                                href="<?php echo $base_url; ?>/category/<?php echo htmlspecialchars($category); ?>&page=<?php echo $current_page - 1; ?>">Previous</a>
                        <?php } else { ?>
                            <a class="disabled">Previous</a>
                        <?php } ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                            <?php if ($i == $current_page) { ?>
                                <a class="active"
                                    href="<?php echo $base_url; ?>/category/<?php echo htmlspecialchars($category); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php } else { ?>
                                <a
                                    href="<?php echo $base_url; ?>/category/<?php echo htmlspecialchars($category); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($current_page < $total_pages) { ?>
                            <a
                                href="<?php echo $base_url; ?>/category/<?php echo htmlspecialchars($category); ?>&page=<?php echo $current_page + 1; ?>">Next</a>
                        <?php } else { ?>
                            <a class="disabled">Next</a>
                        <?php } ?>
                    </div>
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