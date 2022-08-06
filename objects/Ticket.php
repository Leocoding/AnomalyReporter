<?php

    // Représentation Objet d'un Ticket
    // Un ticket est composé d'un identifiant, d'une description, 
    //  d'une ressource associée (par son id), d'une date de création et d'un status. (ouvert ou fermé) 
    class Ticket{

        // Attributs

        private $identifiant;
        private $description;
        private $ressource;
        private $date;
        private $status;

        // Constructeur

        public function __construct($identifiant, $description, $ressource, $date, $status){
            $this->identifiant = $identifiant;
            $this->description = $description;
            $this->ressource = $ressource;
            $this->date = $date;
            $this->status = $status;
        }

        // Méthodes (getter)

        public function getIdentifiant(){
            return $this->identifiant;
        }

        public function getDescription(){
            return $this->description;
        }

        public function getRessource(){
            return $this->ressource;
        }

        public function getDate(){
            return $this->date;
        }

        public function getStatus(){
            return $this->status;
        }
    }
?>