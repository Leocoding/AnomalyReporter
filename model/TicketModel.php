<?php

include_once("objects/Ticket.php");

// Modèle des tickets qui permet la mise en place du MVC.
class TicketModel {

	// Récupère un tableau de nb tickets à partir de l'index start dans la base de données. 
	//	L'index et le nombre permettent de gerer la pagination lors de l'affichage 
	//		de ses tickets à un responsable.
	public static function getTickets($start, $nb){
        try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB(); 
			// Récupère les informations de nb tickets à partir de start associé à un responsable
			// connecté. ($_SESSION['idResp'])
			$reqStr = 'SELECT id,description,ressource,date,status FROM '.Config::getDBConfig()->dbtableprefix.'ticket WHERE ressource IN 
			(SELECT id FROM '.Config::getDBConfig()->dbtableprefix.'ressource WHERE responsable=?)
				ORDER BY status LIMIT ?,?';
			$req = $dbh->prepare($reqStr);
			$req->bindParam(1, $_SESSION['idResp'], PDO::PARAM_INT);
			$req->bindParam(2, $start, PDO::PARAM_INT);
			$req->bindParam(3, $nb, PDO::PARAM_INT); // Force :nb a être un int (sinon bug avec LIMIT)
			$req->execute(); // Exécution
			$tickets = $req->fetchAll(); // Récupération dans un ensemble.
			$tab = array();
			// Itération sur notre ensemble pour peupler le tableau.
			foreach($tickets as $ticket){
				$id = $ticket["id"];
				$description = $ticket["description"];
				$ressource = $ticket["ressource"];
				$date = $ticket["date"];
                $status = $ticket["status"];
				// Création d'objet Ticket pour le stocker dans notre tableau.
				$ticket = new Ticket($id, $description, $ressource, $date, $status);
				array_push($tab, $ticket);
			}
			// Fin de connexion et renvoie des Tickets.
			$dbh = null;
			return $tab;
		// Gestion des exceptions.
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
	}

	// Supprime le ticket d'identifiant id de la base de données.
	public static function removeTicket($id){
		try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB();
			$reqStr = 'DELETE FROM '.Config::getDBConfig()->dbtableprefix.'ticket WHERE id = ?';
			$req = $dbh->prepare($reqStr);
			$req->bindParam(1, $id, PDO::PARAM_INT);
			$req->execute(); // Exécution
		// Gestion des exceptions.
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
	}

	// Ajoute un nouveau ticket dans la base de données à partir d'un objet Ticket.
    public static function addTicket($ticket){
        try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB();
			$reqStr = 'INSERT INTO '.Config::getDBConfig()->dbtableprefix.'ticket (description, ressource, date, status) VALUES (:description, :ressource, :date, :status)';
			$req = $dbh->prepare($reqStr);
			$req->execute(array(
                ':description' => $ticket->getDescription(),
                ':ressource' => $ticket->getRessource(),
                ':date' => $ticket->getDate(),
                ':status' => $ticket->getStatus() 
            )); // Exécution
		// Gestion des exceptions.
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
    }

	public static function closeTicket($id) {
		try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB();
			$reqStr = 'UPDATE '.Config::getDBConfig()->dbtableprefix.'ticket SET status="ferme" WHERE id=:id';
			$req = $dbh->prepare($reqStr);
			$req->execute(array(
				':id'=>$id
			));
		// Gestion des exceptions.
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
	}
}
?>