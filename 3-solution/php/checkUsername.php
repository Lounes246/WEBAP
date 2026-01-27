<?php
// Include database connection details
require_once("db_credentials.php");

// Check if the POST parameter 'username' is set
if (isset($_POST['username'])) {
    // Retrieve the username from the POST data
    $username = $_POST['username'];

    // Create a new MySQLi connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);

    // Check if the connection was successful
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Prepare an SQL SELECT statement to check if the username exists
    $stmt = $mysqli->prepare("SELECT username FROM trainers WHERE username = ?");

    if (!$stmt) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        http_response_code(500); // Internal Server Error
        exit;
    }

    // Bind the parameter to the statement
    $stmt->bind_param("s", $username);

    // Execute the statement
    if ($stmt->execute()) {
        // Store the result
        $stmt->store_result();

        // Check if a row with the given username exists
        if ($stmt->num_rows > 0) {
            echo "exsists";
            http_response_code(200); // Conflict
        } else {
            echo "ok";
            http_response_code(200); // OK
        }
    } else {
        echo "Error: " . $stmt->error;
        http_response_code(500); // Internal Server Error
    }

    // Close the statement and database connection
    $stmt->close();
    $mysqli->close();
} else {
    echo "Invalid POST data.";
    http_response_code(400); // Bad Request
}


