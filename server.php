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

if (isset($_POST['submitReport'])) {
    $report_reason = $_POST['report_reason'];
    $report_message = $_POST['report_message'];
    $report_screenshot = $_POST['report_screenshot'];
    $stmt = $conn->prepare("INSERT INTO reports (server_id, report_reason, report_message, report_screenshot) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $sid, $report_reason, $report_message, $report_screenshot);
    $stmt->execute();
    $stmt->close();
    header('Location: ' . $base_url . 'server/' . $sid . '?report=success');
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>/images/logo-w.png">
    <meta name="description" content="Discover and manage your Discord servers.">
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
                            <div class="title-area">
                                <img src="<?php echo $guild['server_image']; ?>" class="server-image" alt="">
                                <?php echo $guild['name']; ?> | <span
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

                <?php
                $lastbump = new DateTime($guild['last_bump']);
                $nextbump = new DateTime($guild['last_bump']);
                $nextbump->add(new DateInterval('PT2H'));

                $currenttime = new DateTime();

                if ($currenttime >= $nextbump) {
                    echo '<a href="#" class="btn btn-primary rounded-1 w-100 mb-2" data-server-id="' . $guild['id'] . '"><i class="fa-solid fa-arrow-up-short-wide"></i> Bump Server</a>';
                } else {
                    // Calculate time difference in seconds
                    $timeDiff = $nextbump->getTimestamp() - $currenttime->getTimestamp();
                    echo '<div class="ds-servers">';
                    echo '<div class="ds-server">';
                    echo '<div class="countdown text-center" data-time="' . $timeDiff . '"></div>';
                    echo '</div>';
                    echo '</div>';

                }
                ?>
                <div class="mb-2">
                    <button type="button" class="btn discord-join-btn rounded-1 mt-3 w-100"
                        onclick="window.open('<?php echo $guild['invite_link']; ?>', '_blank')"><i
                            class="fa-brands fa-discord"></i> Join Discord Server</button>
                </div>
                <?php if (isset($_GET['report']) && $_GET['report'] == 'success') { ?>
                    <div class="msg-danger mt-3">
                        <div>
                            <p><b>Thank you!</b> We have receieved your report and will review it as soon as possible. Thank
                                you
                                for help improve and keep the community safe.</p>
                        </div>
                        <div class="text-sm">All reports are anonymous for the safety of our members. The only information
                            we receive is the reason for reporting, your message and if provided, your screenshots. We don't
                            store information on who submits the reports. See <a
                                href="<?php echo $base_url; ?>/privacy-policy">our privacy policy</a> for more information.
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="mb-2">
                        <button type="button" class="btn report-btn rounded-1 mt-3 w-100" data-bs-toggle="modal"
                            data-bs-target="#reportModal"><i class="fa-solid fa-flag"></i>
                            Report <?php echo $guild['name']; ?></button>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-0 ds-modal ">
                <div class="modal-header ds-modal-header">
                    <h1 class="modal-title fs-5" id="reportModalLabel">Report <?php echo $guild['name']; ?></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                <!-- <div class="mb-3">
                                    <div class="mb-3">
                                        <label for="serverName" class="ds-label">Server Name</label>
                                        <input type="text" name="serverName" id="serverName" class="ds-input" readonly>
                                    </div>
                                </div> -->
                                <div class="mb-3">
                                    <label for="reportReason" class="ds-label">Report Reason</label>
                                    <select name="report_reason" id="reportReason" class="ds-select" required>
                                        <option value="" selected>Please select an option</option>
                                        <option value="Terms of Service Violation">Terms of Service Violation</option>
                                        <option value="Spamming or Harassment">Spamming or Harassment</option>
                                        <option value="Scamming">Scamming</option>
                                        <option value="Harmful Content">Harmful Content</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="reportMessage" class="ds-label">Report Message</label>
                                    <textarea name="report_message" id="reportMessage" class="ds-textarea" rows="5"
                                        required></textarea>
                                    <div class="mt-2">
                                        <label for="reportScreenshot" class="ds-label">Attach Screenshot
                                            (optional)</label>
                                        <input type="text" name="report_screenshot" id="reportScreenshot"
                                            class="ds-input">
                                        <div class="text-sm mt-2">Attach a link to an image, to images or to a video
                                            supporting the report. You can host images on <a
                                                href="https://imgur.com">https://imgur.com</a>. This is completely
                                            optional but recommended.</div>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" name="submitReport"
                                        class="btn report-btn rounded-0 float-end">Send
                                        Report</button>
                                </div>
                            </form>
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
        document.querySelectorAll('.btn-primary').forEach(btn => {
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