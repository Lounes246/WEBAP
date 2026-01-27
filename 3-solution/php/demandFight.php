<?php
// Start or resume the session
session_start();

// Check if the user is logged in (adjust this based on your authentication system)
if (!isset($_SESSION['user'])) {
    http_response_code(401); // Unauthorized
    echo "User not authenticated.";
    exit;
}


// Include database connection details
require_once("db_credentials.php");


if ($_POST["opponent"] == "bot") {
    //check if a new opponent is available
    if (isset($_SESSION["wildcatch"])) {

        if ($_POST["fight"] == "true") {
            $opponent = $_SESSION["wildcatch"];
            setupFight(0,$opponent,$_SESSION['id'],"Wilderness");
        } else if ($_POST["fight"] == "false") {
            // Generate a random number between 0 and 1
            $randomNumber = mt_rand(0, 100);
            if ($randomNumber < 8) {
                killRandomTeamMember();
                echo ("You managed to flee but sadly one of your companions sacrificed their life for yours.");
            } else if ($randomNumber > 70) {
                hurtRandomTeamMember();
                echo ("You managed to flee but one of your Buddies suffered an injury.");
            } else {
                echo ("You managed to flee! Everbody is safe.");
            }
        } else {
            echo ("Incorrect case- system error");
        }

        unset($_SESSION["wildcatch"]);
    } else {
        echo ("Your opponent has escaped.");
    }
} else {

    if($_POST["fight"] == "true" && isset($_POST["chalPoke"]) && isset($_POST["arena"]) && isset($_POST["defTrainer"])){
        setupFight($_SESSION['id'],$_POST["chalPoke"],$_POST["defTrainer"],$_POST["arena"]);
    }
    echo ("Let's meet in the Arena!");
}


function hurtRandomTeamMember()
{

    // Create a new MySQLi connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
    // Check if the connection was successful
    if ($mysqli->connect_error) {
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => "Database connection error."));
        exit;
    }


    // Prepare an SQL SELECT statement to retrieve the trainer's Pokémon
    $userID = $_SESSION['id']; // Assuming you store the trainer's ID in the session
    $sqlSelect = "SELECT idPokemon FROM pokemon WHERE idTrainer = ? ORDER BY RAND() LIMIT 1";

    $stmtSelect = $mysqli->prepare($sqlSelect);

    if ($stmtSelect === false) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmtSelect->bind_param("i", $userID);

    if ($stmtSelect->execute() === false) {
        die("Select query execution failed: " . $stmtSelect->error);
    }

    $stmtSelect->bind_result($randomPokemonId);

    if ($stmtSelect->fetch() === false) {
        die("No matching Pokemon found.");
    }

    $stmtSelect->close();

    // Step 2: Update the HP of the selected Pokemon
    $sqlUpdate = "UPDATE pokemon SET health = health / 2 WHERE idPokemon = ?";

    $stmtUpdate = $mysqli->prepare($sqlUpdate);

    if ($stmtUpdate === false) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmtUpdate->bind_param("i", $randomPokemonId);

    if ($stmtUpdate->execute() === false) {
        echo "Error: " . $stmtUpdate->error;
    }
    // Close the statement and database connection
    $mysqli->close();
}



function killRandomTeamMember()
{

    // Create a new MySQLi connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
    // Check if the connection was successful
    if ($mysqli->connect_error) {
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => "Database connection error."));
        exit;
    }


    // Prepare an SQL SELECT statement to retrieve the trainer's Pokémon
    $userID = $_SESSION['id']; // Assuming you store the trainer's ID in the session
    $sqlSelect = "SELECT idPokemon FROM pokemon WHERE idTrainer = ? ORDER BY RAND() LIMIT 1";

    $stmtSelect = $mysqli->prepare($sqlSelect);

    if ($stmtSelect === false) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmtSelect->bind_param("i", $userID);

    if ($stmtSelect->execute() === false) {
        die("Select query execution failed: " . $stmtSelect->error);
    }

    $stmtSelect->bind_result($randomPokemonId);

    if ($stmtSelect->fetch() === false) {
        die("No matching Pokemon found.");
    }

    $stmtSelect->close();

    // Step 2: Update the HP of the selected Pokemon
    $sqlUpdate = "DELETE FROM pokemon WHERE idPokemon =  ?";

    $stmtUpdate = $mysqli->prepare($sqlUpdate);

    if ($stmtUpdate === false) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmtUpdate->bind_param("i", $randomPokemonId);

    if ($stmtUpdate->execute() === false) {
        echo "Error: " . $stmtUpdate->error;
    }
    // Close the statement and database connection
    $mysqli->close();
}


// Function to create a new Pokémon
function createNewPokemon($idSpecies)
{

    // Create a new MySQLi connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
    // Check if the connection was successful
    if ($mysqli->connect_error) {
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => "Database connection error."));
        exit;
    }
    $idTrainer = 0;    // Set the trainer's ID to 0
    $health = 100;     // Set initial health to 100
    $level = 1;        // Set initial level to 1
    $experience = 0;   // Set initial experience to 0

    // SQL query to insert a new Pokémon
    $sql = "INSERT INTO pokemon (idTrainer, idSpecies, health, level, experience)
            VALUES (?, ?, ?, ?, ?)";

    // Prepare the SQL statement
    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        return "Prepare failed: " . $mysqli->error;
    }

    // Bind parameters and execute the query
    $stmt->bind_param("iiiii", $idTrainer, $idSpecies, $health, $level, $experience);

    if ($stmt->execute() === true) {
        return "New Pokémon created successfully.";
    } else {
        return "Error: " . $stmt->error;
    }

    // Close the prepared statement (if needed)
    $stmt->close();
}



function setupFight($chalUser, $chalPoke, $defUser, $arena)
{

    // Create a new MySQLi connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
    // Check if the connection was successful
    if ($mysqli->connect_error) {
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => "Database connection error."));
        exit;
    }


    // Check if the connection was successful
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }


    // SQL query to insert a new battle
    $sql = "INSERT INTO battles (idPokemonChallenger, idTrainerChallenger,idTrainerDefendant, arena) VALUES (?, ?, ?,?)";

    // Prepare the SQL statement
    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $mysqli->error);
    }

    // Bind parameters and execute the query
    $stmt->bind_param("iiis", $chalPoke, $chalUser, $defUser , $arena);

    if ($stmt->execute() === true) {
        echo "New battle setup successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}
