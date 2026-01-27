<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id'])) {
  http_response_code(401);
  echo json_encode(["success" => false]);
  exit;
}

if (!isset($_POST["trainerId"])) {
  http_response_code(400);
  echo json_encode(["success" => false]);
  exit;
}

$me = (int)$_SESSION['id'];
$other = (int)$_POST["trainerId"];

require_once(__DIR__ . "/db_credentials.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo json_encode(["success" => false]);
  exit;
}
$mysqli->set_charset("utf8mb4");

$stmt = $mysqli->prepare("UPDATE messages SET isRead = 1 WHERE idSender = ? AND idReceiver = ? AND isRead = 0");
$stmt->bind_param("ii", $other, $me);
$stmt->execute();

echo json_encode(["success" => true]);

$stmt->close();
$mysqli->close();
