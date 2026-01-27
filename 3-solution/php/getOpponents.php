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

// Query to retrieve opponent (trainer) data and count of Pokémon in their team
$sql = "SELECT trainers.idTrainer, trainers.username,
               COUNT(pokemon.idPokemon) AS pokemonCount
        FROM trainers
        LEFT JOIN pokemon ON trainers.idTrainer = pokemon.idTrainer
        WHERE trainers.idTrainer <> ? AND trainers.idTrainer <> 0
        GROUP BY trainers.idTrainer, trainers.username";

// Prepare and execute the query
$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("i", $_SESSION["id"]);

if ($stmt->execute() === false) {
    die("Query execution failed: " . $stmt->error);
}

// Get the results
$result = $stmt->get_result();

// Fetch and format the data
$opponents = array();
while ($row = $result->fetch_assoc()) {
    $opponents[] = $row;
}

// Close the prepared statement and the database connection
$stmt->close();
$mysqli->close();

// Set the response content type to JSON
header('Content-Type: application/json');

// Encode the opponent data as JSON and send it
echo json_encode($opponents);
?>