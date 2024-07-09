<?php
include 'functions.php';

$pagename = "server";

if (isset($_GET['sid'])) {
    $sid = $_GET['sid'];
    $stmt = $conn->prepare("SELECT * FROM servers WHERE server_id =?");
    $stmt->bind_param('s', $sid);
    $stmt->execute();
    $result = $stmt->get_result();
    $guild = $result->fetch_assoc();
}

// Update view count
$stmt = $conn->prepare("UPDATE servers SET views = views + 1 WHERE server_id =?");
$stmt->bind_param('s', $sid);
$stmt->execute();
$stmt->close();

$tags = explode(',', $guild['tags']);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $guild['name']; ?></title>
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
                <div class="servers">
                    <div class="server">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="title-area"><?php echo $guild['name']; ?> | <span
                                    class="category"><?php echo $guild['category']; ?></span>
                                <?php if ($guild['is_nsfw'] == 1) {
                                    echo '<span class="is_nsfw">NSFW</span>';
                                } ?>
                            </div>
                            <div><span class="server-info"><i class="fa-light fa-user" style="margin-right: 5px;"></i>
                                    <?php echo number_format($guild['user_count']); ?></span></div>
                        </div>
                        <div class="mb-4">
                            <?php foreach ($tags as $tag) { ?>
                                <span class="form-tag">#<?php echo $tag; ?></span>
                            <?php } ?>
                        </div>
                        <div class="description">
                            <?php echo nl2br($guild['description']); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="servers mb-4">
                    <div class="server">
                        <?php
                        $lastbump = new DateTime($guild['last_bump']);
                        $nextbump = new DateTime($guild['last_bump']);
                        $nextbump->add(new DateInterval('PT2H'));

                        $currenttime = new DateTime();

                        if ($currenttime >= $nextbump) {
                            echo '<a href="#" class="btn btn-primary-sm" data-server-id="' . $guild['id'] . '">Bump Server</a>';
                        } else {
                            // Calculate time difference in seconds
                            $timeDiff = $nextbump->getTimestamp() - $currenttime->getTimestamp();
                            echo '<div class="countdown text-center" data-time="' . $timeDiff . '"></div>';
                        }
                        ?>
                    </div>
                </div>
                <button type="button" class="btn discord-join-btn rounded-1"
                    onclick="window.open('<?php echo $guild['invite_link']; ?>', '_blank')">Join Discord Server</button>
            </div>
        </div>
    </div>
    <script>
        var currentPath = window.location.pathname.replace(/\/{2,}/g, "/");

        if (currentPath !== window.location.pathname) {
            window.location.replace(window.location.origin + currentPath);
        }
        function startCountdown(element, time) {
            let countdownTime = time;

            const updateCountdown = () => {
                if (countdownTime > 0) {
                    countdownTime--;

                    const hours = Math.floor(countdownTime / 3600);
                    const minutes = Math.floor((countdownTime % 3600) / 60);
                    const seconds = countdownTime % 60;

                    // Create a readable countdown string
                    let countdownText = 'Next Bump: ';
                    if (hours > 0) {
                        countdownText += `${hours}h `;
                    }
                    if (minutes > 0 || hours > 0) {
                        countdownText += `${minutes}m `;
                    }
                    countdownText += `${seconds}s`;

                    element.innerText = countdownText;
                } else {
                    element.innerText = 'You can bump now!';
                    clearInterval(countdownInterval);
                    // Optional: Refresh the page or update UI to show the bump button
                }
            };

            updateCountdown(); // Initial call to display the timer
            const countdownInterval = setInterval(updateCountdown, 1000);
        }

        // Initialize countdown for each countdown element
        document.querySelectorAll('.countdown').forEach(element => {
            const time = parseInt(element.getAttribute('data-time'), 10);
            startCountdown(element, time);
        });

        // Add event listener for bump button clicks
        document.querySelectorAll('.btn-primary-sm').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const serverId = this.getAttribute('data-server-id');
                fetch('../api/bump-server.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        server_id: serverId,
                    }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Successful bump, handle UI update
                            console.log('Server bumped successfully');
                            location.reload();
                        } else {
                            // Handle error if necessary
                            console.error('Failed to bump server:', data.error);
                            // Display error message or retry logic
                        }
                    })
                    .catch(error => {
                        console.error('Error bumping server:', error);
                        // Handle fetch error
                    });
            });
        });
    </script>
</body>

</html>