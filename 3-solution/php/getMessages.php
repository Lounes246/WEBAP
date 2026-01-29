<?php
/**
 * getMessages.php
 *
 * Ce script récupère tous les messages privés entre l'utilisateur courant et un autre utilisateur.
 * Il est utilisé pour afficher l'historique de la conversation directe.
 *
 * Paramètres GET attendus :
 *   - trainerId (int) : l'ID de l'utilisateur avec lequel on échange
 *
 * Retourne du JSON contenant :
 *   - username : nom de l'autre utilisateur
 *   - messages : tableau d'objets message (id, texte, horodatage, indicateur d'expéditeur)
 */

// Démarre la session pour accéder aux informations de l'utilisateur
session_start();
// Définit l'en-tête de réponse en JSON (UTF-8)
header('Content-Type: application/json; charset=utf-8');

// Vérification d'authentification : l'utilisateur est-il connecté ?
if (!isset($_SESSION['id'])) {
  http_response_code(401); // 401 Unauthorized
  echo json_encode(["username" => "", "messages" => []]); // Réponse JSON vide
  exit; // Arrêt du script
}

// Vérification des paramètres : trainerId doit être présent
if (!isset($_GET["trainerId"])) {
  http_response_code(400); // 400 Bad Request
  echo json_encode(["username" => "", "messages" => []]);
  exit;
}

// Récupère les IDs : utilisateur courant et autre utilisateur
$me = (int)$_SESSION['id'];
$other = (int)$_GET["trainerId"];

// Vérifie que l'ID fourni est valide (> 0)
if ($other <= 0) {
  http_response_code(400);
  echo json_encode(["username" => "", "messages" => []]);
  exit;
}

// Inclure les identifiants de connexion à la base de données
require_once(__DIR__ . "/db_credentials.php");

// Création de la connexion MySQLi
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
// Vérifier la connexion
if ($mysqli->connect_errno) {
  http_response_code(500); // 500 Server Error
  echo json_encode(["username" => "", "messages" => []]);
  exit;
}
// Forcer l'encodage en UTF-8
$mysqli->set_charset("utf8mb4");

// Récupérer le nom d'utilisateur de l'autre participant
$u = $mysqli->prepare("SELECT username FROM trainers WHERE idTrainer = ?");
$u->bind_param("i", $other); // Lier l'ID de l'autre utilisateur
$u->execute(); // Exécuter la requête
$u->bind_result($otherName); // Lier la colonne résultat à la variable
$u->fetch(); // Récupérer la valeur
$u->close(); // Fermer la requête
// Si aucun nom trouvé, utiliser une chaîne vide
if (!$otherName) $otherName = "";

// Requête : récupérer les messages échangés entre les deux utilisateurs
$sql = "
SELECT idMessage, idSender, idReceiver, content, sentAt
FROM messages
WHERE (idSender = ? AND idReceiver = ?)
   OR (idSender = ? AND idReceiver = ?)
ORDER BY idMessage ASC
LIMIT 300
"; // Limite pour éviter de charger trop de données

$stmt = $mysqli->prepare($sql); // Préparer la requête
// Lier les paramètres (moi->autre) et (autre->moi)
$stmt->bind_param("iiii", $me, $other, $other, $me);
$stmt->execute(); // Exécuter
$res = $stmt->get_result(); // Obtenir le résultat

// Construire le tableau des messages à retourner
$messages = [];
while ($row = $res->fetch_assoc()) {
  // Déterminer si le message a été envoyé par l'utilisateur courant
  $isSentByMe = ((int)$row["idSender"] === $me);

  // Ajouter l'objet message au tableau (adapter les clés attendues par le front)
  $messages[] = [
    "idMessage"   => (int)$row["idMessage"],
    "messageText" => $row["content"], // Le front attend 'messageText'
    "createdAt"   => $row["sentAt"],  // Le front attend 'createdAt'
    "isSent"      => $isSentByMe
  ];
}

// Envoyer la réponse JSON contenant le nom d'utilisateur et les messages
echo json_encode([
  "username" => $otherName,
  "messages" => $messages
]);

// Nettoyage : fermer la requête et la connexion
$stmt->close();
$mysqli->close();

