<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id'])) {
  http_response_code(401);
  echo json_encode(["success" => false, "messages" => []]);
  exit;
}

require_once(__DIR__ . "/db_credentials.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo json_encode(["success" => false, "messages" => []]);
  exit;
}
$mysqli->set_charset("utf8mb4");

// Get last 100 global chat messages
$sql = "
SELECT 
  gc.idMessage,
  gc.idSender,
  gc.messageText,
  gc.createdAt,
  t.username
FROM global_chat gc
JOIN trainers t ON gc.idSender = t.idTrainer
ORDER BY gc.idMessage ASC
LIMIT 100
";

$stmt = $mysqli->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();

$messages = [];
$me = (int)$_SESSION['id'];

while ($row = $res->fetch_assoc()) {
  $messages[] = [
    "idMessage" => (int)$row["idMessage"],
    "idSender" => (int)$row["idSender"],
    "username" => $row["username"],
    "messageText" => $row["messageText"],
    "createdAt" => $row["createdAt"],
    "isSent" => ((int)$row["idSender"] === $me)
  ];
}

echo json_encode(["success" => true, "messages" => $messages]);

$stmt->close();
$mysqli->close();
?>
