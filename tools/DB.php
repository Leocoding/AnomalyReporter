<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include_once("$root/config/Config.php");

    // Interface de communication entre notre MVC et la base de données
    class DB{

        // Renvoie une connexion ouverte sur la base de données selon les informations du fichier json.
        public static function getDB(){
            try {
                // Tentative de connexion à la base de données avec les informations de configuration du fichier json.
                $dbh = new PDO("mysql:host=".Config::getDBConfig()->dbhost.";dbname=".Config::getDBConfig()->dbname, Config::getDBConfig()->dbuser, Config::getDBConfig()->dbpass);
            // Gestion des exceptions.
            } catch (PDOException $e) {
                echo "Erreur !: ".$e->getMessage()."<br/>";
            }
            // Renvoie d'une connexion ouverte sur la base de données.
            return $dbh;
        }

        // Récupère le préfixe des tables par l'intermédaire 
        //  de la classe de gestion du fichier de configuration json.
        public static function getTablePrefix(){
            return Config::getDBConfig()->dbtableprefix;
        }

        // Prépare et exécute une requête à partir d'une connexion
        //  établie avec la base de données et une chaine contenant
        //  la requête à exécuter.
        private static function createRequest($dbh, $reqStr){
            try {
                $req = $dbh->prepare($reqStr);
                $req->execute();
            } catch (PDOException $e) {
                echo "Erreur !: ".$e->getMessage();
            }
        }

        // Créer l'ensemble des tables de l'application.
        public static function createTables(){
            // Connexion base de données.
            $dbh = self::getDB();

            //Récupération en variable du préfixe des tables. (nommage des tables dans les requêtes)
            $tableprefix = Config::getDBConfig()->dbtableprefix;


            // Créations des différentes tables de l'application via la fonction createRequest

            $reqStrRespo = "CREATE TABLE IF NOT EXISTS ".$tableprefix."responsable (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                nom VARCHAR(30), 
                prenom VARCHAR(30),
                utilisateur VARCHAR(30), 
                password VARCHAR(255)
            );";
            self::createRequest($dbh, $reqStrRespo);

            $reqStrRessource = "CREATE TABLE IF NOT EXISTS ".$tableprefix."ressource (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                description VARCHAR(100),
                localisation VARCHAR(50),
                responsable INTEGER,
                FOREIGN KEY (responsable)
                    REFERENCES ".$tableprefix."responsable(id)
                    ON DELETE CASCADE
            );";
            self::createRequest($dbh, $reqStrRessource);
            $reqStrTicket = "CREATE TABLE IF NOT EXISTS ".$tableprefix."ticket (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                description VARCHAR(100),
                ressource INTEGER,
                date DATE,
                status ENUM('ouvert', 'ferme'),
                FOREIGN KEY (ressource)
                    REFERENCES ".$tableprefix."ressource(id)
                    ON DELETE CASCADE
            );";
            self::createRequest($dbh, $reqStrTicket);

            $reqStrAnomalie = "CREATE VIEW IF NOT EXISTS ".$tableprefix."anomalie AS (
                SELECT
                    description,
                    ressource
                FROM
                    ".$tableprefix."ticket
                GROUP BY description, ressource
            );";
            self::createRequest($dbh, $reqStrAnomalie);
        }
    }
?>