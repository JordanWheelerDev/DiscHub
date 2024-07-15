<?php

include 'functions.php';
include 'stripe/init.php';

\Stripe\Stripe::setApiKey($live_stripe_sk);

$amount = $_POST['amount'];
$token = $_POST['stripeToken'];
$serverId = $_POST['serverId'];

try {
    $charge = \Stripe\Charge::create([
        'amount' => $amount,
        'currency' => 'usd',
        'source' => $token,
        'description' => 'Featured Server Purchase',
    ]);

    // Mapping amount to interval weeks
    $intervals = [
        500 => 1,
        1000 => 2,
        1500 => 3,
        2000 => 4
    ];

    if (array_key_exists($amount, $intervals)) {
        $weeks = $intervals[$amount];

        $stmt = $conn->prepare("
            INSERT INTO featured_servers (server_id, featured_until) 
            VALUES (?, DATE_ADD(NOW(), INTERVAL ? WEEK))
            ON DUPLICATE KEY UPDATE 
            featured_until = GREATEST(featured_until, NOW()) + INTERVAL ? WEEK
        ");
        $stmt->bind_param('iii', $serverId, $weeks, $weeks);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: ' . $base_url . '/success/payment-success');
} catch (\Stripe\Exception\CardException $e) {
    // Payment failed, redirect to error page
    header('Location: error.php');
}
?>