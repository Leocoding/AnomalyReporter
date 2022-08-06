<?php

 // Ouverture de session (utile pour le gestion de la navbar en fonction du contexte)
session_start();

// Gère l'affichage de la barre de navigation au format html .
class NavbarView{

    // Renvoie le code html contenant la barre de navigation.
    // La barre de fonction est différente selon le contexte :
    //   - bouton de connexion ou deconnexion en fonction de l'authentification ou non.
    //   - options disponibles selon le role de la personne connectée. 
    public static function displayNavBar(){
        
        // Gestion du cas de bouton de connexion / déconnexion avec la gestion des actions via le controller (controller.php?action=...) 
        if($_SESSION['role'] != "lambdaUser"){
            $logButton = "<div class='buttonContainer'><a href='controller.php?action=logout'>Se déconnecter</a></div>";
        } else {
            $logButton = "<div class='buttonContainer'><a href='controller.php?action=login'>Se connecter</a></div>";
        }
        // Responsable : option de management de ses ressources et de listage de ses tickets. 
        if($_SESSION['role'] == "responsable"){
            $ressourceButton = "<div class='buttonContainer'><a href='controller.php?action=manageRessources'>Ressources</a></div>";
            $ticketsButton = "<div class='buttonContainer'><a href='controller.php?action=listTickets'>Tickets</a></div>";
            $actionButton = "<div class='actionButtons'>$ressourceButton$ticketsButton</div>";
        // Administrateur : option de management des responsables.
        } elseif($_SESSION['role'] == "administrateur"){
            $responsableButton = "<div class='buttonContainer'><a href='controller.php?action=manageResponsables'>Responsables</a></div>";
            $actionButton = "<div class='actionButtons'>$responsableButton</div>";
        // Non connecté.
        } else {
            $actionButton = "<div class='actionButtons'></div>";
        }
        // Concaténation et renvoie de la barre de navigation.
        $buttons = $actionButton.$logButton;
        $html = "<div class='navbar'>$buttons</div>";
        return $html;
    }
}
?>