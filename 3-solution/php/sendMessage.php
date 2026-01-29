<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["id"])) {
  http_response_code(401);
  echo json_encode(["success" => false, "message" => "Not logged in"]);
  exit;
}

$senderId   = (int)$_SESSION["id"];
$receiverId = (int)($_POST["receiverId"] ?? 0);
$message    = trim($_POST["messageText"] ?? "");

if ($receiverId <= 0 || $message === "") {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Invalid data"]);
  exit;
}

if (mb_strlen($message) > 1000) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Message too long"]);
  exit;
}

require_once(__DIR__ . "/db_credentials.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "DB connection failed"]);
  exit;
}
$mysqli->set_charset("utf8mb4");

// ✅ Colonnes exactes de ta table: idSender, idReceiver, content, sentAt, isRead
$sql = "INSERT INTO messages (idSender, idReceiver, content, sentAt, isRead)
        VALUES (?, ?, ?, NOW(), 0)";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Prepare failed"]);
  exit;
}

$stmt->bind_param("iis", $senderId, $receiverId, $message);

if (!$stmt->execute()) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Execute failed"]);
  exit;
}

$stmt->close();
$mysqli->close();

echo json_encode(["success" => true]);
