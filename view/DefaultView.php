<?php

// Gère l'affichage par défaut si personne n'est connecté au format html.
class DefaultView{

    // Renvoie le code html contenant l'affichage par défaut.
    public static function displayDefaultView(){
        $html = "<div id='defaultView' class='contentContainer'>
                    <h1>Bienvenue</h1>
                    <p>Bienvenue sur ce gestionnaire de maintenance.</p>
                    <p>Veuillez vous connectez pour administrer ou bien scannez un QRCode de ressource pour signaler une anomalie.
                </div>";
        return $html;
    }
}

?>