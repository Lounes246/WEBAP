<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

/**
 * sendMessage.php
 * Reçoit en POST:
 *  - receiverId (int)
 *  - messageText (string)
 * Utilise la session:
 *  - $_SESSION['id'] (idTrainer)
 * Insère dans la table messages:
 *  - idSender, idReceiver, content, isRead(=0)
 */

// 1) Auth
if (!isset($_SESSION['id'])) {
  http_response_code(401);
  echo json_encode(["success" => false, "message" => "Not logged in"]);
  exit;
}

// 2) Input
$receiverId = isset($_POST["receiverId"]) ? (int)$_POST["receiverId"] : 0;
$messageText = isset($_POST["messageText"]) ? trim((string)$_POST["messageText"]) : "";

if ($receiverId <= 0) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Missing/invalid receiverId"]);
  exit;
}

if ($messageText === "") {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Empty message"]);
  exit;
}

// limites raisonnables
if (mb_strlen($messageText) > 500) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Message too long (max 500)"]);
  exit;
}

$me = (int)$_SESSION['id'];

if ($receiverId === $me) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "You cannot message yourself"]);
  exit;
}

// 3) DB
require_once(__DIR__ . "/db_credentials.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "DB connection failed"]);
  exit;
}
$mysqli->set_charset("utf8mb4");

// 4) Vérifier que le receiver existe
$check = $mysqli->prepare("SELECT idTrainer FROM trainers WHERE idTrainer = ?");
$check->bind_param("i", $receiverId);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
  $check->close();
  $mysqli->close();
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Receiver does not exist"]);
  exit;
}
$check->close();

// 5) Insert message
$stmt = $mysqli->prepare("
  INSERT INTO messages (idSender, idReceiver, content, isRead)
  VALUES (?, ?, ?, 0)
");
if (!$stmt) {
  $mysqli->close();
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Prepare failed: " . $mysqli->error]);
  exit;
}

$stmt->bind_param("iis", $me, $receiverId, $messageText);

if (!$stmt->execute()) {
  $err = $stmt->error;
  $stmt->close();
  $mysqli->close();
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Insert failed: " . $err]);
  exit;
}

// 6) OK
$newId = $stmt->insert_id;

$stmt->close();
$mysqli->close();

echo json_encode([
  "success" => true,
  "idMessage" => (int)$newId
]);
