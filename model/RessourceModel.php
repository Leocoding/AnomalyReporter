<?php

include_once("objects/Ressource.php");

// Modèle des ressources qui permet la mise en place du MVC.
class RessourceModel {

	// Récupère l'ensemble des descriptions des anomalies relié à une ressource (par l'id) dans la base de données.
	private static function getAnomaliesListFromRessourceId($idRessource){
		try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB();
			$reqStr = 'SELECT description FROM '.Config::getDBConfig()->dbtableprefix.'anomalie WHERE ressource=:idRessource';
			$req = $dbh->prepare($reqStr);
			$req->execute(array(
				':idRessource'=>$idRessource
			)); // Exécution
			$anomalies = $req->fetchAll();
			$tab = array();
			// Itération sur la réponse de la requète pour peupler le tableau.
			foreach($anomalies as $anomalie){
				array_push($tab, $anomalie['description']);
			}
			// Fin de connexion et renvoie des descriptions d'anomalies.
			$dbh = null;
			return $tab;
		// Gestion des exceptions.
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
	}

	// Récupère une ressource à partir d'un identifiant id sous la forme
	// 	d'un objet Ressource. 
	public static function getRessourceById($id){
		try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB();
			$reqStr = 'SELECT * FROM '.Config::getDBConfig()->dbtableprefix.'ressource WHERE id=:id';
			$req = $dbh->prepare($reqStr);
			$req->execute(array(
				':id'=>$id
			)); // Exécution
			// Stockage dans un tableau associatif des valeurs de la ressource.
			$ressource = $req->fetchAll()[0];
			// Si la ressource est introuvable renvoie NULL.
			if(count($ressource)==0){
				return null;
			}
			
			// Récupération des valeurs :
			$id = $ressource["id"];
			$description = $ressource["description"];
			$localisation = $ressource["localisation"];
			$responsable = $ressource["responsable"];
			// Liste des anomalies (fonction précédente)
			$anomalies = self::getAnomaliesListFromRessourceId($id);
			
			// Création de l'objet Ressource qui est renvoyé.
			$res = new Ressource($id, $description, $localisation, $responsable, $anomalies);
			return $res;
		// Gestion des exceptions.
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
	}

	// Permet de récupérer la dernière ressource entrée en base de données sous
	//	la forme d'un objet Ressource.
	public static function getLastRessource(){
		try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB();
			// Récupère uniquement le dernier éléments ajouté dans la table des ressources
			$reqStr = 'SELECT * FROM '.Config::getDBConfig()->dbtableprefix.'ressource ORDER BY id DESC LIMIT 1';
			$req = $dbh->prepare($reqStr);
			$req->execute(); // Exécution
			// Stockage dans un tableau associatif des valeurs de la ressource.
			$ressource = $req->fetchAll()[0];
			// Si la ressource est introuvable renvoie NULL.
			if(count($ressource)==0){
				return null;
			}

			// Récupération des valeurs :
			$id = $ressource["id"];
			$description = $ressource["description"];
			$localisation = $ressource["localisation"];
			$responsable = $ressource["responsable"];
			// Liste des anomalies (fonction getAnomalieListFromRessourceId)
			$anomalies = self::getAnomaliesListFromRessourceId($id);
			
			// Création de l'objet Ressource qui est renvoyé.
			$res = new Ressource($id, $description, $localisation, $responsable, $anomalies);
			return $res;
		// Gestion des exceptions.
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
	}

	// Récupère un tableau de nb ressources à partir de l'index start dans la base de données. 
	//	L'index et le nombre nb permettent de gérer la pagination lors de l'affichage 
	//		de ses ressources à un responsable.
	public static function getRessources($start, $nb){
        try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB();
			// Récupère les informations de nb ressource à partir de start associé à un responsable
			// connecté. ($_SESSION['idResp'])
			$reqStr = 'SELECT * FROM '.Config::getDBConfig()->dbtableprefix.'ressource WHERE responsable=? LIMIT ?,?';
			$req = $dbh->prepare($reqStr);
			$req->bindParam(1, $_SESSION['idResp'], PDO::PARAM_INT);
			$req->bindParam(2, $start, PDO::PARAM_INT);
			$req->bindParam(3, $nb, PDO::PARAM_INT); //pour forcer :nb a etre un int
			$req->execute(); // Exécution
			$ressources = $req->fetchAll(); // Récupération dans un ensemble.
			$tab = array();
			// Itéaration sur notre ensemble.
			foreach($ressources as $ressource){
				$id = $ressource["id"];
				$description = $ressource["description"];
				$localisation = $ressource["localisation"];
				$responsable = $ressource["responsable"];
				$anomalies = self::getAnomaliesListFromRessourceId($id);

				// Création d'objet Ressource pour le stocker dans notre tableau.
				$res = new Ressource($id, $description, $localisation, $responsable, $anomalies);
				array_push($tab, $res);
			}
			// Fin de connexion et renvoie des Tickets.
			$dbh = null;
			return $tab;
		// Gestion des exceptions.
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
	}

	// Supprime la ressource d'identifiant id de la base de données.
	public static function removeRessource($id){
		try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB();
			$reqStr = 'DELETE FROM '.Config::getDBConfig()->dbtableprefix.'ressource WHERE id = :id';
			$req = $dbh->prepare($reqStr);
			$req->execute(array(
				':id'=>$id
			)); // Exécution
		// Gestion des exceptions.			
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
	}

	// Ajoute une ressource dans la base de données à partir d'un objet Ressource.
    public static function addRessource($ressource){
        try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB();
			$reqStr = 'INSERT INTO '.Config::getDBConfig()->dbtableprefix.'ressource (description, localisation, responsable) VALUES (:description, :localisation, :responsable)';
			$req = $dbh->prepare($reqStr);
			$req->execute(array(
                ':description' => $ressource->getDescription(),
                ':localisation' => $ressource->getLocalisation(),
				':responsable' => $ressource->getResponsable()
            )); // Exécution
		// Gestion des exceptions.
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
    }
}


?>