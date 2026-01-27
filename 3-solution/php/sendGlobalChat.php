<?php
/**
 * sendGlobalChat.php
 * 
 * Ce script reçoit et enregistre les messages de chat global dans la base de données.
 * Les messages envoyés ici sont visibles pour tous les utilisateurs dans la salle de chat publique.
 * 
 * Paramètres POST attendus:
 *   - messageText (string): Le contenu du message
 * 
 * Retourne une réponse JSON avec le statut de succès
 */

// Démarrer la session pour accéder aux informations de l'utilisateur
session_start();
// Définir l'en-tête de réponse en JSON
header('Content-Type: application/json; charset=utf-8');

/**
 * VÉRIFICATION DE L'AUTHENTIFICATION
 * Vérifier que l'utilisateur est connecté en vérifiant l'ID de session
 */
if (!isset($_SESSION['id'])) {
  http_response_code(401);  // HTTP 401: Non autorisé
  echo json_encode(["success" => false, "message" => "Non connecté"]);
  exit;
}

/**
 * VALIDATION DE L'ENTRÉE
 * Obtenir et découper le texte du message de la requête POST
 */
$messageText = isset($_POST["messageText"]) ? trim((string)$_POST["messageText"]) : "";

// Vérifier si le message est vide
if ($messageText === "") {
  http_response_code(400);  // HTTP 400: Mauvaise requête
  echo json_encode(["success" => false, "message" => "Message vide"]);
  exit;
}

// Vérifier si le message dépasse la longueur maximale (500 caractères)
if (mb_strlen($messageText) > 500) {
  http_response_code(400);  // HTTP 400: Mauvaise requête
  echo json_encode(["success" => false, "message" => "Message trop long (max 500)"]);
  exit;
}

// Obtenir l'ID de l'expéditeur de la session
$me = (int)$_SESSION['id'];

/**
 * CONNEXION À LA BASE DE DONNÉES
 * Inclure les identifiants de la base de données et établir la connexion
 */
require_once(__DIR__ . "/db_credentials.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
// Vérifier si la connexion a échoué
if ($mysqli->connect_errno) {
  http_response_code(500);  // HTTP 500: Erreur serveur
  echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données"]);
  exit;
}
// Définir l'ensemble de caractères en UTF-8 pour une manipulation correcte des caractères Unicode
$mysqli->set_charset("utf8mb4");

/**
 * PRÉPARER LA DÉCLARATION INSERT
 * Préparer la requête SQL pour insérer un message dans la table global_chat
 */
$stmt = $mysqli->prepare("INSERT INTO global_chat (idSender, messageText) VALUES (?, ?)");
// Vérifier si la préparation de l'instruction a échoué
if (!$stmt) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Erreur de préparation de la requête"]);
  $mysqli->close();
  exit;
}

/**
 * LIER LES PARAMÈTRES
 * "is" = entier (idSender), chaîne (messageText)
 */
$stmt->bind_param("is", $me, $messageText);

/**
 * EXÉCUTER LA REQUÊTE
 * Insérer le message dans la base de données
 */
if (!$stmt->execute()) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Impossible d'enregistrer le message"]);
  $stmt->close();
  $mysqli->close();
  exit;
}

/**
 * RÉPONSE DE SUCCÈS
 * Retourner le message de succès au client
 */
echo json_encode(["success" => true, "message" => "Message envoyé"]);

/**
 * NETTOYAGE
 * Fermer l'instruction et la connexion à la base de données
 */
$stmt->close();
$mysqli->close();
?>
