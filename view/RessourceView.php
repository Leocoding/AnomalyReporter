<?php

// Ouverture de session (Récupération de l'identifiant du responsable à la création de la ressource)
session_start();

include_once("model/RessourceModel.php");

// Gère l'affichage des éléments concernant les ressources :
//  - formulaire d'ajout d'une nouvelle ressource
//  - liste des ressources existantes
class RessourceView{

    // Renvoie le code html contenant les éléments de la page des ressources affichées au responsable
    //  associé, avec un formulaire de création de ressources et la liste de toutes ses ressources existantes grâce 
    //  à la liste de ressources rl envoyée en entrée sous la forme d'une liste d'objets Ressource.
    // L'action manageRessource indique au controlleur que l'on ne fait pas d'action spécifique
    //  mais que l'on affiche tout de même l'ensemble des informations. (formulaire et liste des ressources)
    public static function displayRessourceView($rl, $actualPage){
        // Formulaire de création d'une nouvelle ressource. 
        $form = self::displayRessourceForm();
        // Liste des ressources existants.
        $list =  self::displayRessourceList($rl);

        // Pagination
        $decreasedPage =  max($actualPage - 1, 1);
        $increasedPage = $actualPage + 1;

        // Code complémentaire entourant les éléments concernant les ressources.
        $html = "<div id='ressourceContainer' class='contentContainer'>
        <div class='formAddContainer'>
        <h3>Création d'une nouvelle ressource</h3>
        $form
        </div>
        <div class='listGlobalContainer'>
        <h3>Liste des ressources</h3>
        <div class='pageController'>
        <a class='button prev' href='controller.php?action=manageRessources&page=$decreasedPage'></a>
        <a class='numPage'>$actualPage</a>\n
        <a class='button next' href='controller.php?action=manageRessources&page=$increasedPage'></a>
        </div>
        $list
        </div>
        </div>";
        return $html;
    }

    // Renvoie le code html contenant le formulaire d'ajout d'une nouvelle ressource :
    //  - un nom, une localisation et un repsponsable associé (le responsable qui crée la ressource)
    // Le formulaire ajoute une nouvelle ressource grâce à l'action addRessource.
    // Le controller se charge de l'ajout en base de données.
    public static function displayRessourceForm(){
        $html ="<div id='ressourceFormContainer' class='formContainer'>
            <form id='formRessource' method='POST' action='controller.php?action=addRessource' onsubmit='event.preventDefault(); verificationMaxLength(this);'>

            <label for='descRessource'>Description :</label>
            <input type='text' id='descRessource' class='longText' name='descRessource' placeholder='Description de la ressource' required>
            <label for='localRessource'>Localisation :</label>
            <input type='text' id='localRessource' class='longText' name='localRessource' placeholder='Localisation de la ressource' required>
            <input type='hidden' name='idResponsable' value='".$_SESSION['idResp']."'>
            <input type='submit' name='submitBtn' value='Creer'>

            </form>
            </div>";


        return $html;
    }

    // Renvoie le code html contenant une boite d'éléments (div) par ressources 
    //  le tout dans un conteneur (classe listElement). Il faut envoyer la liste de
    //  toutes les ressources en entrée. (rl).
    // Une boite  de ressources contient :
    //  - son identifiant, son responsable associé, sa localisation, sa decription et un bouton de suppression.
    public static function displayRessourceList($rl){
        $html = "<div class='listContainer'>\n";

        // Itération sur la liste de ressources rl en récupérant les différentes informations.
        for ($i = 0; $i < count($rl); $i++) {
            $id = $rl[$i]->getIdentifiant();
            $desc = $rl[$i]->getDescription();
            $loc = $rl[$i]->getLocalisation();
            $resp = ResponsableModel::getResponsableNameById($rl[$i]->getResponsable());

            $html .= "<div class='listElement'>
            <div class='infos'>
                <h4>Informations</h4>          
                <div class='infosContainer'>
                            
                    <div>
                    <p>ID</p>
                    <p>$id</p>
                    </div>

                    <div>
                    <p>Responsable</p>
                    <p>$resp</p>
                    </div>
                </div>
            </div>
            <div class='localisation longText'>
                <h4>Localisation de la ressource</h4>
                <p>$loc</p>
            </div>
            <div class='description longText'>
                <h4>Description de la ressource</h4>
                <p>$desc</p>
            </div>
            <div class='actions'>
                <div class='action'>
                <p>Supprimer</p>
                <a class='actionButton removeButton' href='controller.php?action=removeRessource&id=$id'></a>
                </div>

                <div class='action'>
                <p>Imprimer</p>
                <a class='actionButton printButton' href='javascript: void(0);' onclick='printLabel($id,\"".$_SERVER['HTTP_HOST']."\")'></a>
                </div>
            </div>
        </div>";


        }

        // Fermeture de la div initiale et renvoi du code html.
        $html .= "</div>";
        return $html;
    }


}

?>