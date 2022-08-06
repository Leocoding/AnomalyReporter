<?php

include_once("model/TicketModel.php");

// Gère l'affichage des éléments concernant les tickets au format html :
//  - La liste intéractive des tickets. (fermeture possible des tickets)
class TicketView{

    // Renvoie le code html contenant la liste des tickets d'un responsable grâce à la liste
    //  de Tickets tickets envoyé en entrée sous la forme d'une liste d'objets Ticket.
    // L'action listeTickets indique au controlleur que l'on ne fait pas d'action spécifique
    //  mais que l'on affiche la liste des  tickets à son responsable. La liste pour un réponsable
    //  spécifique est calculée en amont par le controlleur (paramètre tickets).
    public static function displayTicketView($tickets, $actualPage){

        // Récupération de la liste au format html.
        $list =  self::displayTicketList($tickets);

        // Pagination
        $decreasedPage =  max($actualPage - 1, 1);
        $increasedPage = $actualPage + 1;

        // Code complémentaire entourant la liste de tickets.
        $html = "<div id='ticketContainer' class='contentContainer'>
        <div class='listGlobalContainer'>
        <h3>Liste des tickets</h3>
        <div class='pageController'>
        <a class='button prev' href='controller.php?action=listTickets&page=$decreasedPage'></a>
        <a class='numPage'>$actualPage</a>\n
        <a class='button next' href='controller.php?action=listTickets&page=$increasedPage'></a>
        </div>
        $list
        </div>
        </div>";
        return $html;
    }

    // Renvoie le code html contenant la liste des tickets en fonction d'un responsable (liste donnée en paramètre) :
    //  - un identifiant, une ressource associée, une date de création, un status (ouvert || fermé), une description.
    // La fermeture de ticket se fait par l'intermédaire de l'action closeTicket avec le controlleur.
    public static function displayTicketList($tickets){
        $html = "<div class='listContainer'>\n";

        // Itération sur la liste de tickets tickets en récupérant les différentes informations.
        for ($i = 0; $i < count($tickets); $i++) {
            $id = $tickets[$i]->getIdentifiant();
            $description = $tickets[$i]->getDescription();
            $ressource = $tickets[$i]->getRessource();
            $date = $tickets[$i]->getDate();
            $status = $tickets[$i]->getStatus();

            $html .= "<div class='listElement'>
                        <div class='infos'>
                            <h4>Informations</h4>
                            
                            <div class='infosContainer'>
                            
                            <div>
                            <p>ID</p>
                            <p>$id</p>
                            </div>

                            <div>
                            <p>Ressource</p>
                            <p>$ressource</p>
                            </div>

                            <div>
                            <p>Date</p>
                            <p>$date</p>
                            </div>

                            <div>
                            <p>Status</p>
                            <p>$status</p>
                            </div>

                            </div>
                        </div>
                        <div class='description longText'>
                            <h4>Description du problème</h4>
                            <p>$description</p>
                        </div>";
            if ($status == "ouvert") {
                $html .= "<div class='actions'>
                            <div class='action'>
                            <p>Fermer</p>
                            <a class='actionButton closeButton' href='controller.php?action=closeTicket&id=$id'></a>
                            </div>
                        </div>";
            }

            $html .= "</div>";
        }
        $html .= "</div>";
        return $html;
    }

}

?>