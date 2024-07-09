<?php
include 'functions.php';

$pagename = "my servers";

if (!isset($_SESSION['user'])) {
    header('Location: ' . $base_url . '/index');
    exit;
}

$user = $_SESSION['user'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DiscHub | My Servers</title>
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
            <div class="ds-header-m mb-4">My Servers</div>
            <?php
            $my_id = $user['id'];
            $stmt = $conn->prepare("SELECT * FROM servers WHERE owner_id =?");
            $stmt->bind_param('i', $my_id);
            $stmt->execute();

            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="col-lg-4">
                    <div class="ds-servers mb-3">
                        <div>
                            <img src="<?php echo $row['server_image']; ?>" class="server-img" alt="">
                        </div>
                        <a href="edit/<?php echo $row['server_id']; ?>" class="ds-server-link">
                            <div class="ds-server">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="ms-title-area"><?php echo $row['name']; ?> | <span
                                            class="category"><?php echo $row['category']; ?></span>
                                        <?php if ($row['is_nsfw'] == 1) {
                                            echo '<span class="is_nsfw">NSFW</span>';
                                        } ?>
                                    </div>
                                    <div><span class="server-info"><i class="fa-light fa-user"
                                                style="margin-right: 5px;"></i>
                                            <?php echo number_format($row['user_count']); ?></span></div>
                                </div>
                                <div class="ms-description mb-3">
                                    <?php echo $row['description']; ?>
                                </div>
                                <?php
                                $lastbump = new DateTime($row['last_bump']);
                                $nextbump = new DateTime($row['last_bump']);
                                $nextbump->add(new DateInterval('PT2H'));

                                $currenttime = new DateTime();

                                if ($currenttime >= $nextbump) {
                                    echo '<a href="#" class="btn btn-primary-sm" data-server-id="' . $row['id'] . '">Bump Server</a>';
                                } else {
                                    // Calculate time difference in seconds
                                    $timeDiff = $nextbump->getTimestamp() - $currenttime->getTimestamp();
                                    echo '<div class="countdown text-center" data-time="' . $timeDiff . '"></div>';
                                }
                                ?>
                            </div>
                        </a>
                    </div>
                </div>
            <?php }
            $stmt->close(); ?>
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
                fetch('api/bump-server.php', {
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