<?php
include 'functions.php';

if (isset($_GET['topic'])) {
    $topic = $_GET['topic'];
} else {
    header('Location: ' . $base_url . '/index');
}

if (!isset($_SESSION['user'])) {
    header('Location: ' . $base_url . '/index');
    exit;
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
    <title>DiscHub | Help</title>
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
            <div class="col-md-8">
                <?php if ($topic === 'getting-started') { ?>
                    <div class="ds-header-l mb-4">Getting Started</div>
                    <div class="card ds-card rounded-0">
                        <div class="card-body">
                            <p>Ooooh, what an exciting time! Listing your Discord server is super simple with
                                <b>DiscHub</b>, this guide will walk you through step by step on getting started with
                                DiscHub.
                            </p>
                            <p>
                            <ol>
                                <li><b>Logging In</b>.
                                    <ol>
                                        <li>If you're not logged in, on the navigation bar at the top right corner of your
                                            screen you should see "My Account". Click on it and authorize DiscHub. <i>If you
                                                don't see My Account, it's likely you're on a mobile device, click on the
                                                hamburger icon and click my account.</i></li>

                                    </ol>
                                <li><b>After Logging In</b>. <ol>
                                        <li>At the same location, you should now see your Discord username with your Discord
                                            profile image at the top right hand corner where "My Account" used to be, hover
                                            over it.</li>
                                        <li>Click on "Add Server".</li>
                                        <li>On the Add Server page, on the left hand side, you should see a dropdown field
                                            saying "Select a server". You must select a server from that dropdown menu, once
                                            selected, the right size should appear with form options.</li>
                                        <li>*Fill out as much detail as possible, users searching for servers to join like
                                            detail! It is also recommened to have atleast <b>ONE</b> tag. Having no tags
                                            will result in your listing not showing up in while users search.</li>
                                        <li>Click the "Add Server" button, the button might also say "Add (your server
                                            name)".</li>
                                        <li>Authorize the DiscHub bot to join your server. Once done, The DiscHub bot should
                                            be in your server. </li>
                                        <li>In an channel that allows users to type, or even an open #bot channel, type
                                            <b>!setup</b>. NOTE: you have administrator priviledges and the channel must be
                                            public and <b>NOT</b> private. If
                                            private, the bot won't be able to do it's setup.
                                            <ul>
                                                <li>Alternatively; you can give the bot admin permissions, and then type
                                                    !setup. Doing this overrides the bots current permissions and allows
                                                    setup in private channels.</li>
                                            </ul>
                                        </li>
                                        <li>The bot will generate an invite link and update our database with the link, so
                                            users can join your server.</li>

                                    </ol>
                                </li>
                                </li>
                                <li><b>All Done</b>
                                    <ol>
                                        <li>You're now fully setup on DiscHub. Any user in your server can type
                                            <b>!bump</b>, which allows the bot to send your servers profile link in the chat
                                            for bumping!
                                        </li>
                                    </ol>
                                </li>
                            </ol>
                            </p>
                        </div>
                    </div>
                <?php } ?>
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