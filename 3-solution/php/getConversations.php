<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id'])) {
  http_response_code(401);
  echo json_encode(["conversations" => []]);
  exit;
}

require_once(__DIR__ . "/db_credentials.php");
$me = (int)$_SESSION['id'];

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo json_encode(["conversations" => []]);
  exit;
}
$mysqli->set_charset("utf8mb4");

/*
Retourne pour chaque "other user":
- idTrainer, username
- lastMessage = dernier message (content)
- unreadCount = messages reçus non lus
*/

$sql = "
SELECT
  t.idTrainer,
  t.username,

  (SELECT m2.content
   FROM messages m2
   WHERE (m2.idSender = ? AND m2.idReceiver = t.idTrainer)
      OR (m2.idSender = t.idTrainer AND m2.idReceiver = ?)
   ORDER BY m2.idMessage DESC
   LIMIT 1) AS lastMessage,

  (SELECT COUNT(*)
   FROM messages m3
   WHERE m3.idSender = t.idTrainer
     AND m3.idReceiver = ?
     AND m3.isRead = 0) AS unreadCount

FROM trainers t
WHERE t.idTrainer <> ?
  AND EXISTS (
    SELECT 1 FROM messages m
    WHERE (m.idSender = ? AND m.idReceiver = t.idTrainer)
       OR (m.idSender = t.idTrainer AND m.idReceiver = ?)
  )
ORDER BY
  (SELECT m4.sentAt
   FROM messages m4
   WHERE (m4.idSender = ? AND m4.idReceiver = t.idTrainer)
      OR (m4.idSender = t.idTrainer AND m4.idReceiver = ?)
   ORDER BY m4.idMessage DESC
   LIMIT 1) DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iiiiiiii",
  $me, $me,
  $me,
  $me,
  $me, $me,
  $me, $me
);

$stmt->execute();
$res = $stmt->get_result();

$conversations = [];
while ($row = $res->fetch_assoc()) {
  $conversations[] = [
    "idTrainer"   => (int)$row["idTrainer"],
    "username"    => $row["username"],
    "lastMessage" => $row["lastMessage"] ?? "",
    "unreadCount" => (int)($row["unreadCount"] ?? 0)
  ];
}

echo json_encode(["conversations" => $conversations]);

$stmt->close();
$mysqli->close();
