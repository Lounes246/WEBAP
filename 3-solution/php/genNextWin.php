<?php
// Start or resume the session
session_start();

// Check if the user is logged in (adjust this based on your authentication system)
if (!isset($_SESSION['user'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(array("message" => "User not authenticated."));
    exit;
}

$min = 1;
$max = 809;
$randomNumber = rand($min, $max);

$_SESSION["wildcatch"]=$randomNumber;
echo $randomNumber;
