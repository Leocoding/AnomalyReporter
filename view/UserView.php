<?php

include_once('model/RessourceModel.php');
include_once('model/ResponsableModel.php');

// Gère l'affichage du formulaire de rapport d'anomalie : 
//  - Un message d'erreur si la ressource n'existe pas.
//  - un formulaire de rapport si cette dernière existe bien.
class UserView{

    // Renvoie le code html d'un formulaire de rapport d'erreur associé à une 
    //  ressource envoyée en entrée. (id)
    // L'action createTicket permet d'indiquer au controlleur qu'il faut créer un nouveau ticket 
    //  avec les infromations fournis dans ce formulaire. ($_POST)
    public static function displayUserForm($id){

        // Récupération de la ressource sous forme d'objet avec un id.
        $ressource = RessourceModel::getRessourceById($id);

        // Ressource inéxistante
        if(is_null($ressource)) {
            $html = "<h2>ERREUR</h2>";
            $html .= "<h3>La ressource n'existe pas!</h3>";
        // Affichage du formulaire
        } else {
            $html ="<div id='userFormContainer' class='contentContainer'>
                <form id='createTicket' method='POST' action='controller.php?action=createTicket' onsubmit='event.preventDefault(); verificationMaxLength(this);'>
                <div id='infoRessourceTicket'>
                    <h3>Informations ressource</h3>
                    <p>Responsable : ".ResponsableModel::getResponsableNameById($ressource->getResponsable())."</p>
                    <p>Ressource : ".$ressource->getDescription()."</p>
                    <p>Localisation : ".$ressource->getLocalisation()."</p>
                </div>
                <div id='descAnomalieContainer'>
                    <label for='descAnomalie'>Description de l'anomalie</label>
                    <textarea id='descAnomalie' class='longText' name='descAnomalie' form='createTicket' placeholder=\"Description de l'anomalie\" required></textarea>
                    <input type='hidden' name='idRessource' value='".$id."'>
                    <input type='submit' name='submitBtn' value='Creer'>    
                </div>

                <div id='anomaliesListGlobal'>
                <h3>Anomalies connues pour cette ressource : </h3>
                <p>Cliquez sur une anomalie de la liste ci-dessous pour l'utiliser dans votre formulaire.</p>";
                

                $anomalies = $ressource->getAnomalies();

                if(count($anomalies) == 0){
                    $html .= "<p class='noAnomalie'>Aucune anomalie connue pour cette ressource!</p>";
                } else {

                    $html .= "<div id='anomaliesList'>";

                    // Itération sur la liste d'anomalies de l'objet pour créer des anomalies cliquables (onclick) afin
                    //  d'ajouter celle choisie dans la liste pré-éxistante directement dans le formulaire de nouvelle anomalie.
                    foreach($anomalies as $anomalie){
                        // Rajout d'une image à gauche de la description pour copier le texte dans le textarea
                        $html.=("<p onclick='document.getElementById(\"descAnomalie\").value = this.textContent'>".$anomalie."</p>");
                    }

                    $html .= "</div>";
                }

            $html.="</div></form></div>";
        }

        return $html;
    }

}

?>