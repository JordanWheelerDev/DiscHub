<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            max-width: 400px;
            width: 100%;
        }

        .form-row {
            margin-bottom: 20px;
        }

        #card-errors {
            color: red;
        }
    </style>
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">Stripe Payment</h2>
        <form id="payment-form">
            <div class="form-row">
                <label for="amount-select">Select Amount</label>
                <select id="amount-select" class="form-select mb-3">
                    <option value="500">1 Week/$5.00 USD</option>
                    <option value="2000">1 Month/$20.00 USD</option>
                    <option value="10000">1 Year/$100.00 USD</option>
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

        var form = document.getElementById('payment-form');

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            stripe.createToken(card).then(function (result) {
                if (result.error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            var amount = document.getElementById('amount-select').value;

            // Create a form to submit to your server
            var form = document.createElement('form');
            form.setAttribute('method', 'POST');
            form.setAttribute('action', 'process-payment.php');

            // Add token and amount to form
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

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>

</html>