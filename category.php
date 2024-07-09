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
                    $stmt = $conn->prepare("SELECT * FROM servers WHERE category_slug =?");
                    $stmt->bind_param('s', $category);
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
                                        <span class="form-tag">#<?php echo $tag; ?></span>
                                    <?php } ?>
                                </div>
                                <div class="description">
                                    <?php echo htmlspecialchars($row['description']); ?>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
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