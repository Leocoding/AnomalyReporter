<?php

include_once("RessourceView.php");
include_once("ResponsableView.php");
include_once("TicketView.php");
include_once("view/NavbarView.php");

// Gère l'affichage du formulaire d'authentification au format html.
class AuthView{

    // Renvoie le code html contenant le formulaire d'authentification.
    // Le formulaire est gérer par le controller. On envoie dans l'URL ($_GET['action'])
    //  l'action à effectuer pour la bonne gestion de l'authentification.
    public static function displayAuthView(){
        $html ="<div id='formAuthContainer' class='formContainer'>
            <form id='authForm' method='POST' action='controller.php?action=auth'> 
                <label for='authUser'>User :</label>
                <input type='text' id='authUser' name='authUser' placeholder='Utilisateur' required>
                <label for='authPass'>Pass :</label>
                <input type='password' id='authPass' name='authPass' placeholder='Mot de passe' required>
                <input type='submit' name='submitBtn' value='Connexion'>
            </form>
            </div>";         
        return $html;

    }

}

?>
