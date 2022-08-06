<?php

include_once("model/ResponsableModel.php");

// Gère l'affichage des éléments concernant les responsables :
//  - formulaire d'ajout d'un nouveau responsable
//  - liste des responsables existants
class ResponsableView{

    // Renvoie le code html contenant les éléments de la page responsable affichés à l'administrateur
    //  avec un formulaire de création et la liste de tous les responsables existants grâce 
    //  à la liste de responsables rl envoyée en entrée sous la forme d'une liste d'objet Responsable.
    // L'action manageResponsable indique au controlleur que l'on ne fait pas d'action spécifique
    //  mais que l'on affiche tout de même l'ensemble des informations. (formulaire et liste de responsables)
    public static function displayResponsableView($rl, $actualPage){
        // Formulaire d'ajout d'un nouveau responsable.
        $form = self::displayResponsableForm();
        // Liste des responsables existants.
        $list =  self::displayResponsableList($rl);

        // Pagination
        $decreasedPage =  max($actualPage - 1, 1);
        $increasedPage = $actualPage + 1;

        // Code complémentaire entourant les éléments concernant les responsables.
        $html = "<div id='responsableContainer' class='contentContainer'>
        <div class='formAddContainer'>
        <h3>Création d'un nouveau responsable</h3>
        $form
        </div>
        <div class='listGlobalContainer'>
        <h3>Liste des responsables</h3>
        <div class='pageController'>
        <a class='button prev' href='controller.php?action=manageResponsables&page=$decreasedPage'></a>
        <a class='numPage'>$actualPage</a>\n
        <a class='button next' href='controller.php?action=manageResponsables&page=$increasedPage'></a>
        </div>
        $list
        </div>
        </div>";
        return $html;
    }

    // Renvoie le code html contenant le formulaire d'ajout d'un nouveau responsable :
    //  - un nom, un prenom, un mot de passe
    // Le formulaire ajoute un nouveau responsable grâce à l'action addResponsable.
    // Le controller se charge de l'ajout en base de données.
    public static function displayResponsableForm(){
        $html ="<div id='responsableFormContainer' class='formContainer'>
            <form method='POST' action='controller.php?action=addResponsable'>
            <label for='nomResponsable'>Nom :</label>
            <input type='text' id='nomResponsable' name='nomResponsable' placeholder='Nom' required>
            <label for='prenomResponsable'>Prénom :</label>
            <input type='text' id='prenomResponsable' name='prenomResponsable' placeholder='Prenom' required>
            <label for='password'>Mot de passe :</label>
            <input type='password' id='password' name='password' placeholder='Mot de passe' required>
            <input type='submit' name='submitBtn' value='Creer'>
            </form>
            </div>";


        return $html;
    }

    // Renvoie le code html contenant une boite d'éléments (div) par responsable 
    //  le tout dans un conteneur (classe listContainer). Il faut envoyer la liste de
    //  tous les responsables en entrée. (rl).
    // Une boite de responsables contient :
    //  - son identifiant, son nom, son prénom, son nom d'utilisateur et un bouton de suppression.
    public static function displayResponsableList($rl){
        $html = "<div class='listContainer'>\n";

        // Itération sur la liste de responsables rl en récupérant les différentes informations.
        for ($i = 0; $i < count($rl); $i++) {
            $id = $rl[$i]->getIdentifiant();
            $nom = $rl[$i]->getNom();
            $prenom = $rl[$i]->getPrenom();
            $username = $rl[$i]->getUsername();

            $html .= "<div class='listElement'>
                <div class='infos'>
                    <h4>Informations</h4>
                                
                    <div class='infosContainer'>
                                
                        <div>
                        <p>ID</p>
                        <p>$id</p>
                        </div>

                        <div>
                        <p>Nom</p>
                        <p>$nom</p>
                        </div>

                        <div>
                        <p>Prenom</p>
                        <p>$prenom</p>
                        </div>

                        <div>
                        <p>Username</p>
                        <p>$username</p>
                        </div>
                    </div>
                </div>            
                
                <div class='actions'>
                    <div class='action'>
                    <p>Supprimer</p>
                    <a class='actionButton removeButton' href='controller.php?action=removeResponsable&id=$id'></a>
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