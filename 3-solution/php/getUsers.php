<?php
/**
 * getUsers.php
 * 
 * Ce script récupère tous les utilisateurs de la base de données sauf l'utilisateur actuellement connecté.
 * Utilisé pour remplir le menu déroulant "Nouveau message" dans l'interface de messagerie.
 * 
 * Retourne un tableau JSON de tous les autres utilisateurs avec leur ID et nom d'utilisateur
 */

// Démarrer la session pour accéder aux informations de l'utilisateur
session_start();
// Définir l'en-tête de réponse en JSON avec encodage UTF-8
header('Content-Type: application/json; charset=utf-8');

/**
 * VÉRIFICATION DE L'AUTHENTIFICATION
 * Vérifier que l'utilisateur est connecté en vérifiant l'ID de session
 */
if (!isset($_SESSION['id'])) {
  http_response_code(401);  // HTTP 401: Non autorisé
  echo json_encode(["success"=>false, "users"=>[]]);
  exit;
}

/**
 * CONNEXION À LA BASE DE DONNÉES
 * Inclure les identifiants de la base de données et établir la connexion
 */
require_once(__DIR__ . "/db_credentials.php");

// Obtenir l'ID de l'utilisateur actuel de la session
$me = (int)$_SESSION['id'];

// Créer une nouvelle connexion MySQL
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);
// Vérifier si la connexion a échoué
if ($mysqli->connect_errno) {
  http_response_code(500);  // HTTP 500: Erreur serveur
  echo json_encode(["success"=>false, "users"=>[]]);
  exit;
}
// Définir l'ensemble de caractères en UTF-8 pour une manipulation correcte des caractères Unicode
$mysqli->set_charset("utf8mb4");

/**
 * PRÉPARER LA DÉCLARATION SELECT
 * Requête pour obtenir tous les utilisateurs sauf l'utilisateur actuellement connecté
 * Triés alphabétiquement par nom d'utilisateur
 */
$stmt = $mysqli->prepare("SELECT idTrainer, username FROM trainers WHERE idTrainer <> ? ORDER BY username ASC");
// Lier l'ID de l'utilisateur actuel pour l'empêcher d'apparaître dans la liste
$stmt->bind_param("i", $me);
// Exécuter la requête
$stmt->execute();
// Obtenir l'ensemble de résultats
$res = $stmt->get_result();

/**
 * CONSTRUIRE LE TABLEAU DE RÉPONSE
 * Parcourir les résultats et les formater en tant que tableau prêt pour JSON
 */
$users = [];
while ($row = $res->fetch_assoc()) {
  // Convertir les IDs en entiers pour assurer un formatage JSON correct
  $users[] = [
    "idTrainer" => (int)$row["idTrainer"], 
    "username" => $row["username"]
  ];
}

/**
 * ENVOYER UNE RÉPONSE RÉUSSIE
 * Retourner un tableau de tous les autres utilisateurs
 */
echo json_encode(["success"=>true, "users"=>$users]);

/**
 * NETTOYAGE
 * Fermer l'instruction et la connexion à la base de données
 */
$stmt->close();
$mysqli->close();
