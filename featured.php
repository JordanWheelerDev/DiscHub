<?php
include 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DiscHub | Terms of Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/all.min.css">
</head>

<body>
    <?php include "parts/navbar.php"; ?>
    <div class="container mt-5 mb-5">
        <div class="row mb-4 d-flex justify-content-center">
            <div class="col-md-8">
                <div class="ds-header-l mb-4">Get Featured</div>
                <div class="card ds-card rounded-0">
                    <div class="card-body">
                        <p>Are you looking to make your server stand out? Look no further! DiscHub offers a premium
                            feature for Discord server owners that make your listing stand out to users across our
                            platform.</p>
                        <p>Your server listing will feature an eye-catching purple shadow around it and will be listed
                            on the home page and at the top of your listings category. This attracts more
                            users to your listing, resulting in more users joining your server!</p>
                        <p>To purchase featured for your server listing(s), <a
                                href="<?php echo $base_url; ?>/purchase-featured">click here</a> and select the server
                            you
                            wish to
                            have featured and make a payment!</p>
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