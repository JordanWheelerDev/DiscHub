<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "jordan88";
$dbname = "discord-servers";

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}