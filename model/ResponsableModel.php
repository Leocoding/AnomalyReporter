<?php

include_once("objects/Responsable.php");

// Modèle des responsables qui permet la mise en place du MVC.
class ResponsableModel {

	// Récupère un tableau de nb responsables à partir de l'index start dans la base de données. 
	//	L'index et le nombre permettent de gérer la pagination lors de l'affichage 
	//		des responsables à l'administrateur.
	public static function getResponsables($start, $nb) {
        try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB();
			$reqStr = 'SELECT id,nom,prenom,utilisateur FROM '.Config::getDBConfig()->dbtableprefix.'responsable LIMIT ?,?';
			$req = $dbh->prepare($reqStr);
			$req->bindParam(1, $start, PDO::PARAM_INT); //pour forcer :start a etre un int
			$req->bindParam(2, $nb, PDO::PARAM_INT); //pour forcer :nb a etre un int
			$req->execute(); // Exécution de la requete
			$responsables = $req->fetchAll(); // Récupération dans un ensemble.
			$tab = array();
			// Itération pour peupler le tableau
			foreach($responsables as $responsable){
				$id = $responsable["id"];
				$nom = $responsable["nom"];
				$prenom = $responsable["prenom"];
				$username = $responsable["utilisateur"];
				// Création d'objet Responsable pour le stocker dans notre tableau.
				$respo = new Responsable($id, $nom, $prenom, "", $username);
				array_push($tab, $respo);
			}
			// Fin de connexion et renvoie des Responsables.
			$dbh = null;
			return $tab;
		// Gestion des exceptions.
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
	}


	// Renvoie une chaine de caractère contenant le prénom et le nom d'un responsable 
	//	en fonction de son id envoyé en entrée. 
	public static function getResponsableNameById($idResp) {
		try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB();
			$reqStr = 'SELECT nom,prenom FROM '.Config::getDBConfig()->dbtableprefix.'responsable WHERE id = ?';
			$req = $dbh->prepare($reqStr);
			$req->bindParam(1, $idResp, PDO::PARAM_INT);
			$req->execute(); // Exécution
			$names = $req->fetch(); // Récupération de la ligne
			// Chaine de la forme "prenom nom"
			$nomResp = $names["prenom"]." ".$names["nom"];
			// Fin de connexion et renvoie des Responsables.
			$dbh = null;
			return $nomResp;
		// Gestion des exceptions.
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
	}

	// Supprime le responsable d'identifiant id de la base de données.
	public static function removeResponsable($id){
		try {
			// Connexion DB et préparation de la requète paramétrée.
            $dbh = DB::getDB();
			$reqStr = 'DELETE FROM '.Config::getDBConfig()->dbtableprefix.'responsable WHERE id = ?';
			$req = $dbh->prepare($reqStr);
			$req->bindParam(1, $id, PDO::PARAM_INT);
			$req->execute(); // Exécution
			$dbh = null;
		// Gestion des exceptions.	
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
	}

	// Ajoute un nouveau responsable dans la base de données à partir d'un objet Responsable.
    public static function addResponsable($responsable){
        try {
			// Connexion DB et préparation de la requête paramétrée.
            $dbh = DB::getDB();
			$reqStr = 'INSERT INTO '.Config::getDBConfig()->dbtableprefix.'responsable (nom, prenom, utilisateur, password) VALUES (:nom, :prenom, :utilisateur, SHA2(:password, 256));';
			$req = $dbh->prepare($reqStr);
			$req->execute(array(
                ':nom' => $responsable->getNom(),
                ':prenom'=> $responsable->getPrenom(),
				':utilisateur'=> $responsable->getUsername(),
				':password'=> $responsable->getPassword()
            )); // Exécution
			$dbh = null;
		// Gestion des exceptions.
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }
    }


	// Renvoie le nom d'utilisateur pour s'authentifier en tant que responsable.
    //  Il s'agit d'une concaténation entre les trois premières lettres du nom suivies 
    //      des trois premières du prénom. Si il y a moins de trois lettres à l'un ou
    //      l'autre des composant, ces parties sont tronquées.
	// 	Si le nom d'utilisateur généré est déjà present dans la base (homonymes ou 3 lettres du nom et prenom identique)
	//		récupère l'identifiant qui doit etre attribué (via l'auto_increment) et l'ajoute à la fin du nom d'utilisateur.	
	public static function generateUsername($nom, $prenom){

		// generation via 3 premieres lettres du nom et 3 premieres lettres du prenom
		$username = strtolower(substr($nom, 0, min(strlen($nom), 3)).substr($prenom, 0, min(strlen($prenom), 3)));

		try {
			$dbh = DB::getDB();
			$reqStr = 'SELECT COUNT(*) AS count,id FROM '.Config::getDBConfig()->dbtableprefix.'responsable WHERE utilisateur = :username;';
			$req = $dbh->prepare($reqStr);
			$req->execute(array(
				':username' => $username,
			));
			$res = $req->fetchAll()[0];
			// Teste si un nom d'utilisateur identique existe deja
			if($res['count'] > 0){

				// Recupere le next id de l'auto_increment qui est unique (meme si utilisateur supprimé)
				$reqStr = 'SELECT AUTO_INCREMENT AS NEXTID FROM information_schema.tables WHERE table_name = \''.Config::getDBConfig()->dbtableprefix.'responsable\' AND table_schema = DATABASE();';
				$req = $dbh->prepare($reqStr);
				$req->execute();
				$res = $req->fetchAll()[0];
				$nextId = $res['NEXTID'];
				$username .= $nextId;
				
			}
        } catch (PDOException $e) {
            echo "Erreur !: ".$e->getMessage()."<br/>";
        }


		return $username;
	}
}
?>