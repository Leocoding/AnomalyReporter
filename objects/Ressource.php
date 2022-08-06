<?php

    // Représentation Objet d'une Ressource
    // Une ressource est composée d'un identifiant, d'une description, 
    //  d'une localisation (salle de classe, couloir, ...), 
    //  d'un responsable associé (par son id) et d'une liste d'anomalies connues. 
    class Ressource {

        // Attributs

        private $identifiant;
        private $description;
        private $localisation;
        private $responsable;
        private $anomalies;

        // Constructeur

        public function __construct($identifiant, $description, $localisation, $responsable, $anomalies){
            $this->identifiant = $identifiant;
            $this->description = $description;
            $this->localisation = $localisation;
            $this->responsable = $responsable;
            $this->anomalies = $anomalies;
        }

        // Méthodes (getter)

        public function getIdentifiant(){
            return $this->identifiant;
        }

        public function getDescription(){
            return $this->description;
        }

        public function getLocalisation(){
            return $this->localisation;
        }

        public function getResponsable(){
            return $this->responsable;
        }

        public function getAnomalies(){
            return $this->anomalies;
        }
    }
?>