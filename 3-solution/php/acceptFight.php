<?php
// Start or resume the session
session_start();

// Check if the user is logged in (adjust this based on your authentication system)
if (!isset($_SESSION['user'])) {
    http_response_code(401); // Unauthorized
    echo "User not authenticated.";
    exit;
}

// User's trainer ID (replace with the actual ID)
$idTrainer = $_SESSION['id'];


// Check if the user is logged in (adjust this based on your authentication system)
if (!isset($_POST['battle'])) {
    http_response_code(401); // Unauthorized
    echo "No battle selected";
    exit;
}

// Check if the user is logged in (adjust this based on your authentication system)
if (!isset($_POST['pokemon'])) {
    http_response_code(401); // Unauthorized
    echo "No pokemon chosen";
    exit;
}

// Battle ID (replace with the actual ID)
$idBattle = $_POST['battle'];

// Pokémon ID chosen by the defendant (replace with the actual ID)
$idPokemonChosen = $_POST['pokemon'];

// Include database connection details
require_once("db_credentials.php");



    // Create a new MySQLi connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
    // Check if the connection was successful
    if ($mysqli->connect_error) {
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => "Database connection error."));
        exit;
    }



// Check if the battle exists and has an empty entry for idPokemonDefendant
$sqlCheckBattle = "SELECT idPokemonDefendant, idTrainerDefendant
                   FROM battles
                   WHERE idBattle = ?";

$stmtCheckBattle = $mysqli->prepare($sqlCheckBattle);

if ($stmtCheckBattle === false) {
    die("Prepare failed: " . $mysqli->error);
}

$stmtCheckBattle->bind_param("i", $idBattle);

if ($stmtCheckBattle->execute() === false) {
    die("Query execution failed: " . $stmtCheckBattle->error);
}

$stmtCheckBattle->bind_result($idPokemonDefendant, $idTrainerDefendant);

if ($stmtCheckBattle->fetch() === false) {
    die("Battle not found.");
}

$stmtCheckBattle->close();

// Check if the idPokemonDefendant is empty (null)
if (is_null($idPokemonDefendant)) {
    // Check if the user's trainer ID matches the idTrainerDefendant in the battle
    if ($idTrainer === $idTrainerDefendant) {
        // Check if the chosen Pokémon belongs to the defendant
        $sqlCheckPokemonOwnership = "SELECT idPokemon
                                     FROM pokemon
                                     WHERE idTrainer = ? AND idPokemon = ?";

        $stmtCheckPokemonOwnership = $mysqli->prepare($sqlCheckPokemonOwnership);

        if ($stmtCheckPokemonOwnership === false) {
            die("Prepare failed: " . $mysqli->error);
        }

        $stmtCheckPokemonOwnership->bind_param("ii", $idTrainer, $idPokemonChosen);

        if ($stmtCheckPokemonOwnership->execute() === false) {
            die("Query execution failed: " . $stmtCheckPokemonOwnership->error);
        }

        $stmtCheckPokemonOwnership->bind_result($idPokemonOwned);

        if ($stmtCheckPokemonOwnership->fetch() === false) {
            echo "The chosen Pokémon does not belong to you.";
        } else {
            // Update the battle with the chosen Pokémon
            $sqlUpdateBattle = "UPDATE battles
                                SET idPokemonDefendant = ?
                                WHERE idBattle = ?";

            $stmtUpdateBattle = $mysqli->prepare($sqlUpdateBattle);

            if ($stmtUpdateBattle === false) {
                die("Prepare failed: " . $mysqli->error);
            }

            $stmtUpdateBattle->bind_param("ii", $idPokemonChosen, $idBattle);

            if ($stmtUpdateBattle->execute() === true) {
                echo "Pokémon chosen successfully.";
            } else {
                echo "Error: " . $stmtUpdateBattle->error;
            }

            $stmtUpdateBattle->close();
        }

        $stmtCheckPokemonOwnership->close();
    } else {
        echo "You are not the defendant in this battle.";
    }
} else {
    echo "The defendant has already chosen a Pokémon for this battle.";
}

// Close the database connection
$mysqli->close();
?>