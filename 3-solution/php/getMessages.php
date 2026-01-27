<?php
/**
 * getMessages.php
 * 
 * This script retrieves all direct messages between the current user and another specific user.
 * Used to load the chat history when opening a direct message conversation.
 * 
 * Expected GET parameters:
 *   - trainerId (int): The ID of the user to get messages with
 * 
 * Returns JSON containing:
 *   - username: Name of the other user
 *   - messages: Array of message objects (id, text, timestamp, sender info)
 */

// Start session to access user information
session_start();
// Set response header as JSON with UTF-8 encoding
header('Content-Type: application/json; charset=utf-8');

/**
 * AUTHENTICATION CHECK
 * Verify that user is logged in by checking session ID
 */
if (!isset($_SESSION['id'])) {
  http_response_code(401);  // HTTP 401: Unauthorized
  echo json_encode(["username" => "", "messages" => []]);
  exit;
}

/**
 * INPUT VALIDATION
 * Check if trainerId parameter is provided
 */
if (!isset($_GET["trainerId"])) {
  http_response_code(400);  // HTTP 400: Bad Request
  echo json_encode(["username" => "", "messages" => []]);
  exit;
}

// Get user IDs from session and GET parameters
$me = (int)$_SESSION['id'];
$other = (int)$_GET["trainerId"];

// Validate that trainerId is positive
if ($other <= 0) {
  http_response_code(400);  // HTTP 400: Bad Request
  echo json_encode(["username" => "", "messages" => []]);
  exit;
}

/**
 * DATABASE CONNECTION
 * Include database credentials and establish connection
 */
require_once(__DIR__ . "/db_credentials.php");

// Create new MySQL connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
// Check if connection failed
if ($mysqli->connect_errno) {
  http_response_code(500);  // HTTP 500: Server Error
  echo json_encode(["username" => "", "messages" => []]);
  exit;
}
// Set character set to UTF-8 for proper Unicode handling
$mysqli->set_charset("utf8mb4");

/**
 * FETCH OTHER USER'S USERNAME
 * Query to get the name of the user we're chatting with
 */
$u = $mysqli->prepare("SELECT username FROM trainers WHERE idTrainer = ?");
// Bind the other user's ID
$u->bind_param("i", $other);
// Execute query
$u->execute();
// Bind result to variable
$u->bind_result($otherName);
// Fetch the result
$u->fetch();
// Close this statement
$u->close();
// Set default empty string if user not found
if (!$otherName) $otherName = "";

/**
 * RÉCUPÉRER LES MESSAGES DIRECTS
 * Requête pour obtenir tous les messages entre l'utilisateur actuel et l'autre utilisateur
 * Les messages sont récupérés par ordre croissant (les plus anciens d'abord)
 * Limité aux 300 derniers messages pour éviter de charger trop de données
 */
$sql = "
SELECT idMessage, idSender, idReceiver, content, sentAt
FROM messages
WHERE (idSender = ? AND idReceiver = ?)
   OR (idSender = ? AND idReceiver = ?)
ORDER BY idMessage ASC
LIMIT 300
";

$stmt = $mysqli->prepare($sql);
// Lier les paramètres: moi->autre, autre->moi (les deux directions)
$stmt->bind_param("iiii", $me, $other, $other, $me);
// Exécuter la requête
$stmt->execute();
// Obtenir l'ensemble des résultats
$res = $stmt->get_result();

/**
 * CONSTRUIRE LE TABLEAU DE RÉPONSE
 * Boucler à travers les messages et formater pour la réponse JSON
 */
$messages = [];
while ($row = $res->fetch_assoc()) {
  // Déterminer si ce message a été envoyé par l'utilisateur actuel ou reçu
  $isSentByMe = ((int)$row["idSender"] === $me);
  
  $messages[] = [
    "idMessage"   => (int)$row["idMessage"],
    "messageText" => $row["content"],      // Le frontend s'attend à la clé "messageText"
    "createdAt"   => $row["sentAt"],       // Le frontend s'attend à la clé "createdAt"
    "isSent"      => $isSentByMe            // Booléen: vrai si envoyé par l'utilisateur actuel
  ];
}

/**
 * ENVOYER UNE RÉPONSE RÉUSSIE
 * Retourner le nom d'utilisateur et le tableau des messages
 */
echo json_encode([
  "username" => $otherName,
  "messages" => $messages
]);

/**
 * NETTOYAGE
 * Fermer la déclaration et la connexion à la base de données
 */
$stmt->close();
$mysqli->close();
