<?php
header('Content-Type: application/json');

// POST check
if (
    isset($_POST['username'], $_POST['birthdate'], $_POST['gender'],
          $_POST['hometown'], $_POST['password'], $_POST['passwordc'])
) {
    $username  = trim($_POST['username']);
    $birthdate = $_POST['birthdate'];
    $gender    = $_POST['gender'];
    $hometown  = trim($_POST['hometown']);
    $password  = $_POST['password'];
    $passwordc = $_POST['passwordc'];

    // ✅ IMPORTANT: correct path
    require_once("./db_credentials.php");

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
    if ($mysqli->connect_error) {
        echo json_encode(["success"=>false,"message"=>"Connection failed"]);
        exit;
    }
    $mysqli->set_charset("utf8mb4");

    if ($password !== $passwordc) {
        echo json_encode(["success"=>false,"message"=>"Passwords do not match!"]);
        exit;
    }

    // (optional) check duplicate username in DB
    $check = $mysqli->prepare("SELECT idTrainer FROM trainers WHERE username=?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(["success"=>false,"message"=>"Username already exists"]);
        exit;
    }
    $check->close();

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare(
        "INSERT INTO trainers (username, birthdate, gender, hometown, password)
         VALUES (?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        echo json_encode(["success"=>false,"message"=>"Prepare failed: ".$mysqli->error]);
        exit;
    }

    $stmt->bind_param("sssss", $username, $birthdate, $gender, $hometown, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["success"=>true,"message"=>"User registered successfully!"]);
    } else {
        echo json_encode(["success"=>false,"message"=>"Insert error: ".$stmt->error]);
    }

    $stmt->close();
    $mysqli->close();

} else {
    echo json_encode(["success"=>false,"message"=>"Incomplete POST data."]);
}
