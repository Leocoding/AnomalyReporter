<?php

include_once("RessourceView.php");
include_once("ResponsableView.php");
include_once("TicketView.php");
include_once("view/NavbarView.php");

// Gère l'affichage de l'application au format html.
class AppView{

    // Renvoie le code html contenant la structure de base d'un fichier html
    //   et le code html $content inclus dans la page.
    public static function showApp($content){
        $htmlPage = "<!DOCTYPE html>\n
        <html lang='fr'>\n
        <head>\n
        <title>Gestionnaire de maintenance</title>\n
        <link rel='stylesheet' href='css/style.css'>\n
        <link rel='icon' href='img/icon.png'>
        <link rel='apple-touch-icon' href='img/icon.png'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>\n
        <script src='js/qrcode.js'></script>
        </head>\n
        <body>\n
        <h1><a id='main-title' href='/'>Gestionnaire de maintenance</a></h1>\n";
        
        // Ajout de la barre de navigation.
        $htmlPage .= NavbarView::displayNavBar();
        
        // ajout du contenu $content dans le code.
        $htmlPage .="<div id='globalContentContainer'>\n
        $content\n
        </div>
        <script src='js/main.js'></script>
        </body>\n
        </html>\n";
        return $htmlPage;
    }
}

?>
