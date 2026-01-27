
<?php
// Start or resume the session
session_start();

// Check if the user is logged in (adjust this based on your authentication system)
if (!isset($_SESSION['id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(array("message" => "User not authenticated."));
    exit;
}

// Check if the payload contains a Battle to handle.
if (!isset($_POST['idBattle'])) {
    http_response_code(404); // Not found
    echo json_encode(array("message" => "No Battle in Payload"));
    exit;
}



// Include database connection details
require_once("db_credentials.php");

// Create a new MySQLi connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);

// Get the current user's ID from the session
    $currentUserId = $_SESSION['id'];

    // SQL query to delete the battle
    $sql = "DELETE FROM battles WHERE idBattle = ? AND idTrainerDefendant = ?";

    // Prepare and execute the query
    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        return "Prepare failed: " . $mysqli->error;
    }

    $stmt->bind_param("ii", $_POST['idBattle'], $currentUserId);

    if ($stmt->execute() === false) {
        return "Query execution failed: " . $stmt->error;
    }
    else{
        echo("Battle Rejected");
    }

    // Close the prepared statement and the database connection
    $stmt->close();
    $mysqli->close();
    

?>