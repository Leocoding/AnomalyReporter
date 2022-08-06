<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include_once("$root/tools/DB.php");

// Récupération des valeurs du formulaire (index.php)
//  contenue dans $_POST et échappement des caractères spéciaux.
$dbname = htmlspecialchars($_POST['dbname']);
$dbhost = htmlspecialchars($_POST['dbhost']);
$dbuser = htmlspecialchars($_POST['dbuser']);
$dbpass = htmlspecialchars($_POST['dbpass']);
$dbtableprefix = htmlspecialchars($_POST['dbtableprefix']);
$adminuser = htmlspecialchars($_POST['adminuser']);
$adminpass = htmlspecialchars($_POST['adminpass']);

// Hachage du mot de passe admin avec l'algorithme BCRYPT.
$hash = password_hash($adminpass, PASSWORD_BCRYPT);

// Création d'un tableau associatif contenant toutes les configurations.
$json_content = Array
(
    'admin' => Array(
        'adminuser' => $adminuser,
        'adminpass' => $hash
    ),
    'db' => Array(
        'dbname' => $dbname,
        'dbhost' => $dbhost,
        'dbuser' => $dbuser,
        'dbpass' => $dbpass,
        'dbtableprefix' => $dbtableprefix
    )
);

// Ouverture d'un flux en écriture sur le fichier config.json.
$fp = fopen("$root/config.json", 'w');
// Ecriture et encode du tableau au format JSON.
fwrite($fp, json_encode($json_content, JSON_PRETTY_PRINT));
fclose($fp); // Fermeture du flux

// Création des différentes tables selon les informations 
// de connexion fournis pour la base de données.
DB::createTables();
// Redirection
header("Location: /controller.php");
?>