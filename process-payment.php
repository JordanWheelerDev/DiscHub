<?php

include 'functions.php';

include 'stripe/init.php';

\Stripe\Stripe::setApiKey('sk_test_51PaQvWJ2yiAWQzDVp96RFTcdJrWufFjigLNz2x1jswAKYzYcyynhNKE3X5sfJJoMG1GS2P5UFQLgAPTPvpC7hfZq00UVXhYqGs');

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

    if ($amount == 500) {
        $stmt = $conn->prepare("INSERT INTO featured_servers (server_id, featured_until) VALUES (?, DATE_ADD(NOW(), INTERVAL 1 WEEK))");
        $stmt->bind_param('i', $serverId);
        $stmt->execute();
        $stmt->close();
    } elseif ($amount == 1000) {
        $stmt = $conn->prepare("INSERT INTO featured_servers (server_id, featured_until) VALUES (?, DATE_ADD(NOW(), INTERVAL 2 WEEK))");
        $stmt->bind_param('i', $serverId);
        $stmt->execute();
        $stmt->close();
    } elseif ($amount == 1500) {
        $stmt = $conn->prepare("INSERT INTO featured_servers (server_id, featured_until) VALUES (?, DATE_ADD(NOW(), INTERVAL 3 WEEK))");
        $stmt->bind_param('i', $serverId);
        $stmt->execute();
        $stmt->close();
    } elseif ($amount == 2000) {
        $stmt = $conn->prepare("INSERT INTO featured_servers (server_id, featured_until) VALUES (?, DATE_ADD(NOW(), INTERVAL 4 WEEK))");
        $stmt->bind_param('i', $serverId);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: success.php');
} catch (\Stripe\Exception\CardException $e) {
    // Payment failed, redirect to error page
    header('Location: error.php');
}