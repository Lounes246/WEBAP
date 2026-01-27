<?php

// This code tries to log the user in with the given credentials.
    // POST parameters required:
    //     username
    //     password

// Check if the Request is valid
if(!isset($_POST['username']) || !isset($_POST['password'])) {
    echo("Invalid Post Parameters");
    http_response_code(400); 
    die();
}

session_start();


require_once ("./db_credentials.php");
$mysqli = new mysqli(DB_HOST,DB_USER,DB_PW,DB_NAME);

// Oh no! A connect_errno exists so the connection attempt failed!
if ($mysqli->connect_errno) {
    // The connection failed. What do you want to do?
    // You could contact yourself (email?), log the error, show a nice page, etc.
    // You do not want to reveal sensitive information

    // Let's try this:
    echo "Sorry, this website is experiencing problems.";

    // Something you should not do on a public site, but this example will show you
    // anyways, is print out MySQL error related information -- you might log this
    echo "Error: could not connect to sql server \n";
    echo "Errno: " . $mysqli->connect_errno . "\n";
    echo "Error: " . $mysqli->connect_error . "\n";

    // You might want to show them something nice, but we will simply exit
    exit;
}

// Change character set to utf8
$mysqli -> set_charset("utf8");

/* Prepared statement, stage 1: prepare */
if (!($stmt = $mysqli->prepare("SELECT idTrainer, password, username FROM trainers WHERE username=?"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
// set the values of the previously defined parameters
$userName= $mysqli->real_escape_string($_POST['username']);

if (!$stmt->bind_param("s", $userName)) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
//execute the prepared statement.
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

// save the returned information from the databse.
$user = $stmt->get_result();

//check if there has been exactly one user found.
if (mysqli_num_rows($user) == 1) {
    $password = $_POST['password'];
    $row = mysqli_fetch_assoc($user);
    if (password_verify($password, $row['password'])) {
        $_SESSION["user"] = $row['username'];
        $_SESSION["id"] = $row['idTrainer'];
        http_response_code(200); // OK
    } else {
        // Password wrong
        echo("Password incorrect");
        http_response_code(403); // Forbidden
    }
} else {
    // Username not found
    echo("Username not found");
    http_response_code(404); // Not found
}


$mysqli -> close();