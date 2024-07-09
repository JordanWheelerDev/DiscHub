<?php
include 'functions.php';

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];

    if ($msg == 'payment-success') {
        // Redirect after 5 seconds
        header("refresh:5;url=" . $base_url . "/my-servers");
    }

} else {
    header('Location: ' . $base_url . '/index');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>/images/logo-w.png">
    <meta name="description" content="Discover and manage your Discord servers.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DiscHub | Success</title>
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
        <div class="row mb-4 d-flex justify-content-center">
            <?php if ($msg == 'payment-success') { ?>
                <div class="col-md-8">
                    <div class="ds-header-l mb-3">Success!</div>
                    <div class="card ds-card rounded-0">
                        <div class="card-body">
                            <p>
                                Thank you for your payment. Your server is now featured, you can now enjoy more exposures on
                                DiscHub!
                            </p>
                            <p class="text-sm text-center">Redirecting you back to your servers in 5 seconds.</p>
                        </div>
                    </div>
                </div>
            <?php } ?>
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