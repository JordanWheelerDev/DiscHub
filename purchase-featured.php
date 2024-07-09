<?php
include 'functions.php';

if (!isset($_SESSION['user'])) {
    header('Location: ' . $base_url . '/index');
    exit;
}

$user = $_SESSION['user'];
$pagename = "purchase";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DiscHub | Purchase Featured</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/all.min.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body>
    <?php include "parts/navbar.php"; ?>
    <div class="container mt-5 mb-5">
        <div class="row mb-4 d-flex justify-content-center">
            <div class="col-md-4">
                <div class="card ds-card rounded-0 mb-3">
                    <div class="card-body">
                        <div class="mb-3 ds-header-s">Select Server</div>
                        <form id="payment-form">
                            <select name="featureServer" id="featureServer" class="ds-select">
                                <option selected>Select your sever</option>
                                <?php
                                $uid = $user['id'];
                                $stmt = $conn->prepare("SELECT * FROM servers WHERE owner_id =?");
                                $stmt->bind_param('i', $uid);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <option value="<?php echo htmlspecialchars($row['server_id']); ?>">
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="servers">
                    <div class="server">
                        <div class="mb-3 ds-header-m">
                            Featured Time
                        </div>
                        <div class="form-row">
                            <label for="amount-select">Select Amount</label>
                            <select id="amount-select" class="ds-select mb-3">
                                <option value="500">1 Week/$5.00 USD</option>
                                <option value="1000">2 Weeks/$10.00 USD</option>
                                <option value="1500">3 Weeks/$20.00 USD</option>
                                <option value="2000">4 Weeks/$30.00 USD</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <label for="card-element">Credit or debit card</label>
                            <div id="card-element">
                                <!-- A Stripe Element will be inserted here. -->
                            </div>
                            <!-- Used to display form errors -->
                            <div id="card-errors" role="alert"></div>
                        </div>

                        <button id="pay-button" class="btn btn-primary mb-3" style="width: 100%;">Pay</button>
                        </form>
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
        // Set your publishable API key here
        var stripe = Stripe('pk_test_51PaQvWJ2yiAWQzDVKnADdw7EX27sZclYwycXWX0fOPpPmYK8d7EUsGWjWCOraNvV2n6VXdQpOX13gC3IMEOvpolJ00dHAsGLpM');
        var elements = stripe.elements();

        var card = elements.create('card');
        card.mount('#card-element');

        var paymentForm = document.getElementById('payment-form');

        paymentForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Get selected server ID
            var serverForm = document.querySelector('#payment-form'); // Correct form selection
            var serverId = serverForm.querySelector('#featureServer').value;

            stripe.createToken(card).then(function (result) {
                if (result.error) {
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    // Send token, amount, and server ID to process-payment.php
                    stripeTokenHandler(result.token, serverId);
                }
            });
        });

        function stripeTokenHandler(token, serverId) {
            var amount = document.getElementById('amount-select').value;

            var form = document.createElement('form');
            form.setAttribute('method', 'POST');
            form.setAttribute('action', 'process-payment.php');

            var hiddenTokenInput = document.createElement('input');
            hiddenTokenInput.setAttribute('type', 'hidden');
            hiddenTokenInput.setAttribute('name', 'stripeToken');
            hiddenTokenInput.setAttribute('value', token.id);
            form.appendChild(hiddenTokenInput);

            var hiddenAmountInput = document.createElement('input');
            hiddenAmountInput.setAttribute('type', 'hidden');
            hiddenAmountInput.setAttribute('name', 'amount');
            hiddenAmountInput.setAttribute('value', amount);
            form.appendChild(hiddenAmountInput);

            var hiddenServerIdInput = document.createElement('input');
            hiddenServerIdInput.setAttribute('type', 'hidden');
            hiddenServerIdInput.setAttribute('name', 'serverId');
            hiddenServerIdInput.setAttribute('value', serverId);
            form.appendChild(hiddenServerIdInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>

</body>

</html>