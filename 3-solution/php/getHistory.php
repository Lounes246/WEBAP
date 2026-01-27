<?php

// Start or resume the session
session_start();

// Check if the user is logged in (adjust this based on your authentication system)
if (!isset($_SESSION['id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(array("message" => "User not authenticated."));
    exit;
}

$idTrainer = $_SESSION['id'];

// Include database connection details
require_once("db_credentials.php");

// Create a new MySQLi connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);


// Query to retrieve battles with turn=0 for the given user
$sql = "SELECT battles.idBattle, battles.idPokemonDefendant, battles.idPokemonChallenger,
               battles.idWinner, battles.arena, battles.idTrainerChallenger, battles.idTrainerDefendant,
               battles.turn, trainers.username AS challenger_username
        FROM battles
        JOIN trainers ON battles.idTrainerChallenger = trainers.idTrainer
        WHERE battles.idTrainerDefendant = ? OR battles.idTrainerChallenger = ? AND battles.turn >=1 AND battles.winner==NULL";

// Prepare and execute the query
$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("i", $idTrainer);

if ($stmt->execute() === false) {
    die("Query execution failed: " . $stmt->error);
}

// Get the results
$result = $stmt->get_result();

// Fetch and format the data
$battles = array();
while ($row = $result->fetch_assoc()) {
    $battles[] = $row;
}

// Close the prepared statement and the database connection
$stmt->close();
$mysqli->close();

// Convert the result to JSON
echo json_encode($battles);
?>