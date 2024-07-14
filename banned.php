<?php
include 'functions.php';

$pagename = "server";

if (!checkForBan()) {
    header('Location: ' . $base_url . '/index');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM bans WHERE user_id =?");
$stmt->bind_param('i', $userId);
$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>/images/logo-w.png">
    <meta name="description" content="Discover and manage your Discord servers.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banned | DiscHub</title>
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
    <nav class="navbar navbar-expand-lg ds-navbar">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $base_url; ?>/index">
                <img src="images/site/logo.png" class="nav-logo img-fluid" alt="">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Help
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" target="_blank"
                                    href="https://discord.gg/UqcnkAsUyb"><b>Appeal</b></a>
                        </ul>
                    </li>
                </ul>
                <div class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span
                            style="margin-right: 7px; text-transform: uppercase;"><?php echo $user['username']; ?></span>
                        <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Profile Image"
                            class="rounded-circle ds-profile-nav">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?php echo $base_url; ?>/logout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mt-5 mb-5">
        <div class="row mb-4 d-flex justify-content-center">
            <div class="col-md-8">
                <div class="servers">
                    <div class="server">
                        <div class="banned-title text-center mb-3">You Have Been Banned</div>
                        <div class="mb-3">
                            <table class="table ds-table">
                                <tr>
                                    <th>Reason:</th>
                                    <td><?php echo htmlspecialchars($row['reason']); ?></td>
                                </tr>
                                <tr>
                                    <th>Lift Date:</th>
                                    <td>
                                        <?php
                                        $date = new DateTime($row['lift_date']);
                                        echo $date->format('F j, Y');
                                        ?>

                                    </td>
                                </tr>
                            </table>
                        </div>
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