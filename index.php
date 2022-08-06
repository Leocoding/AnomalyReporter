<?php

// Si le fichier config.json n'existe pas, on affiche cette page
//  contenant un formulaire avec les valeurs de configurations suivantes :
//      - username admin, password admin
//      - nom de base, hote base, utilisateur base, password base, préfixe pour les tables. (évite les problèmes de tables déjà éxistentes)
// Sinon, redirection automatique sur l'application de maintenance.
$filename = 'config.json';
if(file_exists($filename) && (json_decode(file_get_contents($filename)) != null)) {
    header('Location: controller.php');
} else { ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Configuration</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='css/style.css'>
    <link rel='icon' href='img/icon.png'>
    <link rel='apple-touch-icon' href='img/icon.png'>
</head>
<body>
    <h1>CONFIGURATION</h1>
    <!-- Formulaire en méthode POST avec champs obligatoires et fichier cible étant createConfig.php -->
    <form id="configForm" method="POST" action="config/createConfig.php">
        <fieldset>
            <legend>Configuration du compte Administrateur</legend>
            <div><label for="adminuser">Utilisateur admin : </label><input type="text" name="adminuser" id="adminuser" placeholder="Nom d'utilisateur Admin" required/></div>
            <div><label for="adminpass">Mot de passe admin : </label><input type="password" name="adminpass" id="adminpass" placeholder="Mot de passe Admin" required/></div>
        </fieldset>
        <fieldset>
            <legend>Configuration de la base de données</legend>
            <div><label for="dbname">Nom de la base : </label><input type="text" name="dbname" id="dbname" placeholder="Nom base" required/></div>
            <div><label for="dbhost">Hôte de la base : </label><input type="text" name="dbhost" id="dbhost" placeholder="Hôte base" required/></div> 
            <div><label for="dbuser">Utilisateur pour la base : </label><input type="text" name="dbuser" id="dbuser" placeholder="Utilisateur base" required/></div> 
            <div><label for="dbpass">Mot de passe pour la base : </label><input type="password" name="dbpass" id="dbpass" placeholder="Mot de passe base" required/></div> 
            <div><label for="dbtableprefix">Préfixe pour les tables : </label><input type="text" name="dbtableprefix" id="dbtableprefix" placeholder="préfixe nom des tables" required/></div>
        </fieldset>
        <input type="submit" name="btnSubmit" value="Envoyer"/>
    </form>

</body>
</html>
<?php } ?>