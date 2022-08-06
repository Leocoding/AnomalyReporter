<?php

    // Représentation Objet d'un Responsable
    // Un responsable est composé d'un identifiant, d'un nom, 
    //  d'un prenom, d'un mot de passe et d'un nom d'utilisateur. 
    class Responsable {

        // Attributs
        
        private $identifiant;
        private $nom;
        private $prenom;
        private $password;
        private $username;

        // Constructeur

        public function __construct($identifiant, $nom, $prenom, $password, $username){
            $this->identifiant = $identifiant;
            $this->nom = $nom;
            $this->prenom = $prenom;
            $this->password = $password;
            $this->username = $username;
        }

        // Méthodes (getter)

        public function getIdentifiant(){
            return $this->identifiant;
        }

        public function getNom(){
            return $this->nom;
        }

        public function getPrenom(){
            return $this->prenom;
        }

        public function getUsername() {
            return $this->username;
        }

        public function getPassword() {
            return $this->password;
        }

    }
?>