<?php

session_start();

include_once("tools/DB.php");
include_once("view/AppView.php");
include_once("view/UserView.php");
include_once("view/DefaultView.php");
include_once("view/RessourceView.php");
include_once("view/AuthView.php");
include_once("view/QrCodeView.php");
include_once("config/Config.php");

// Test de l'existance d'un fichier de configuration.
$filename = 'config.json';
if(!file_exists($filename) || (json_decode(file_get_contents($filename)) == null)) {
    // Redirection vers le formulaire correspondant si ce fichier n'existe pas.
    header('Location: /');
} else {

    // Role par défaut si pas authentifié en tant que admin ou responsable.
    if(!isset($_SESSION['role'])){
        $_SESSION['role'] = "lambdaUser";
    }

    // Gestion de la pagination.
    if(!isset($_GET['page'])){
        $page = 1;
    } else {
        $page = max(1,htmlspecialchars($_GET['page']));
    }

    $nbParPage = 10; // Nombre d'éléments sur une page
    $html = ""; // variable contenant le code html à afficher. (dynamique)

    // Gestion du role responsable. (Connecté en tant que responsable)
    if($_SESSION['role'] == 'responsable'){
        // Si une action est demandée dans l'URL ($_GET) on effectue les traitements demandés.
        if(isset($_GET['action'])){
            $action = htmlspecialchars($_GET['action']);
            $message = "";
            // Cas : ajout d'une ressource.
            if ($action == "addRessource") {
                if(isset($_POST['submitBtn'])){
                    // Récupération des informations du formulaire. ($_POST)
                    $desc = htmlspecialchars($_POST['descRessource']);
                    $localisation = htmlspecialchars($_POST['localRessource']);
                    $idResponsable = intval(htmlspecialchars($_POST['idResponsable']));
                    // Ajout d'une nouvelle ressource dans le modèle des ressources puis redirection avec message de succés.
                    RessourceModel::addRessource(new Ressource(null, $desc, $localisation, $idResponsable, null));
                    header('Location: controller.php?action=manageRessources&infoMsg=addComplete');
                } else {
                    header('Location: /');
                }
            // Cas : suppression d'une ressource.
            } elseif ($action == "removeRessource") {
                // Récupération de l'identifiant et suppresion du modèle puis redirection avec message de succés.
                $idRessource = htmlspecialchars($_GET['id']);
                RessourceModel::removeRessource($idRessource);
                header('Location: controller.php?action=manageRessources&infoMsg=removeComplete');
            // Affichage simple de la page de gestion des ressources.
            } elseif ($action == "manageRessources") {
                // Calcule de la liste bornée (pagination) des ressources associées au responsable connecté
                //  et récupération du code html à afficher en fonction de cette liste.
                $rl = RessourceModel::getRessources(($page-1)*$nbParPage,$nbParPage);
                $html1 = RessourceView::displayRessourceView($rl, $page);
                $html1 .= QrCodeView::getQRCodeAutoPrintPage();
                $message = "";

                // Gestion du message de retour d'action.
                if(isset($_GET['infoMsg'])){
                    $infoMsg = htmlspecialchars($_GET['infoMsg']);
                    switch ($infoMsg) {
                        case 'removeComplete':
                            $message = "<p class='MessageValid'>La ressource a bien été supprimée</p>";
                            break;
                        case 'addComplete':
                            // Récupération de la dernioère ressource pour facilité la génération du QR-Code associé. (id dans l'URL)
                            $res = RessourceModel::getLastRessource();
                            $id = $res->getIdentifiant();
                            // classe dontprint permet de ne pas imprimer les p lors de l'impression d'une etiquette
                            $message = "<p class='dontprint MessageValid'>La ressource a bien été créée</p>
                            <p class='dontprint MessageValid'>Cliquez sur le bouton pour imprimer son etiquette</p>
                            <a class='actionButton printButton dontprint' href='javascript: void(0);' onclick='printLabel($id,\"".$_SERVER['HTTP_HOST']."\")'></a>";
                            break;
                        default:
                            $message = "";
                            break;
                    }
                }
                // Contenu html à afficher.
                $html = $message.$html1;
            // Affichage simple de la liste des tickets du responsable connecté.
            } elseif ($action == "listTickets") {
                $tickets = TicketModel::getTickets(($page-1)*$nbParPage,$nbParPage);
                $html1 = TicketView::displayTicketView($tickets, $page);
                if(isset($_GET['ticketMsg'])){
                    $msg = htmlspecialchars($_GET['ticketMsg']);
                    if ($msg == "closeTicketComplete") {
                        $message = "<p class='MessageValid'>Le ticket a bien été fermé</p>";
                    }
                }
                $html = $message.$html1;
            // Fermeture d'un ticket dont le problème est réglé
            } elseif ($action == "closeTicket") {
                // Récupération de l'identifiant du ticket et mise à jour du modèle puis redirection avec message de succès. 
                $idTicket = htmlspecialchars($_GET['id']);
                TicketModel::closeTicket($idTicket);
                header('Location: controller.php?action=listTickets&ticketMsg=closeTicketComplete');
            }
        }

    // Gestion du role administrateur. (Connecté en tant qu'administrateur)
    } elseif($_SESSION['role'] == 'administrateur'){
        // Si une action est demandé dans l'URL ($_GET) on effectue les traitements demandés.
        if(isset($_GET['action'])){
            $action = htmlspecialchars($_GET['action']);
            $message = "";
            // Cas :ajout d'un responsable.
            if ($action == "addResponsable"){
                if(isset($_POST['submitBtn'])){
                    // Récupération des informations du formulaire. ($_POST)
                    $nom = htmlspecialchars($_POST['nomResponsable']);
                    $prenom = htmlspecialchars($_POST['prenomResponsable']);
                    $password = htmlspecialchars($_POST['password']);

                    // Genere un nom d'utilisateur (pour palier a des usernames identiques meme si nom et prenom differents)
                    $username = ResponsableModel::generateUsername($nom, $prenom);
                    // Ajout d'un nouveau responsable dans le modèle des responsables puis redirection avec message de succès.
                    ResponsableModel::addResponsable(new Responsable(null, $nom, $prenom, $password,$username));
                    header('Location: controller.php?action=manageResponsables&infoMsg=addComplete');
                } else {
                    header('Location: /');
                }
            // Cas : suppression d'un responsable.
            } elseif ($action == "removeResponsable") {
                // Récupération de l'identifiant et suppresion du modèle puis redirection avec message de succès.
                $idResponsable = htmlspecialchars($_GET['id']);
                ResponsableModel::removeResponsable($idResponsable);
                $message = "<p>Le responsable a bien été supprimé</p>";
                header('Location: controller.php?action=manageResponsables&infoMsg=removeComplete');
            // Affichage simple de la page de gestion des responsables.
            } elseif ($action == "manageResponsables") {
                // Calcule de la liste bornée (pagination) de tous les responsables existants
                //  et récupération du code html à afficher en fonction de cette liste.
                $rl = ResponsableModel::getResponsables(($page-1)*$nbParPage,$nbParPage);
                $html1 = ResponsableView::displayResponsableView($rl, $page);
                // Gestion du message de retour d'action.
                if(isset($_GET['infoMsg'])){
                    $infoMsg = htmlspecialchars($_GET['infoMsg']);
                    switch ($infoMsg) {
                        case 'removeComplete':
                            $message = "<p class='MessageValid'>Le responsable a bien été supprimé</p>";
                            break;

                        case 'addComplete':
                            $message = "<p class='MessageValid'>Le responsable a bien été créé</p>";
                            break;
                                    
                        default:
                            $message = "";
                            break;
                    }
                } else {
                    $message = "";
                }
                // Contenu html à afficher.
                $html = $message.$html1;
            }
            $html = $message.$html1;
        }
    }

    // Traitement des actions sans restriction de role.
    if(isset($_GET['action'])){
        $action = htmlspecialchars($_GET['action']);
        // Authentification 
        if($action == "auth"){
            // Récupération des informations du formulaire de connexion
            $user = htmlspecialchars($_POST['authUser']);
            $pass = htmlspecialchars($_POST['authPass']);
            // Si le test des valeurs de nom d'utilisateur et vérification du mot de passe avec le mot de passe haché 
            //  contenu dans le fichier de configuration est bon :
            if((strtolower($user) == Config::getAdminConfig()->adminuser) && password_verify($pass, Config::getAdminConfig()->adminpass)){
                // Definie comme administrateur puis redirigé.
                $_SESSION['role'] = "administrateur";
                header('Location: controller.php');
            } else {
                try {
                    // Connection Base de données et récupération du nombre de ligne avec les 
                    //  informations données dans le formulaire. Si il existe 1 ligne on connecte 
                    //  en tant que responsable sinon, on redirige avec un message d'erreur. 
                    $dbh = DB::getDB();
                    $reqStr = 'SELECT COUNT(*) AS count,id FROM '.Config::getDBConfig()->dbtableprefix.'responsable WHERE utilisateur = :user AND password = SHA2(:pass, 256);';
                    $req = $dbh->prepare($reqStr);
                    $req->execute(array(
                        ':user' => strtolower($user),
                        ':pass' => $pass
                    ));
                    $res = $req->fetchAll()[0];
                    // Compte inexistant
                    if($res['count'] == 0){
                        $dbh = null;
                        header('Location: controller.php?action=login&infoMsg=errorAuth');
                    // Compte valide : on met à jour les informations de sessions et on redirige.
                    } else {
                        // Role et identifiant au sens d'id responsable en base de données
                        $_SESSION['role'] = "responsable";
                        $_SESSION['idResp'] = $res['id'];
                        $dbh = null;
                        // Pour éviter l'attaque du type session fixation 
                        //  on regénére l'identifiant de session. (cf cours)
                        session_regenerate_id(true);
                        header('Location: controller.php');
                    }
                // Gestion des Exceptions
                } catch (PDOException $e) {
                    echo "Erreur !: ".$e->getMessage()."<br/>";
                }
            }
        // Création d'un nouveau ticket
        } elseif($action=="createTicket"){
            if(isset($_POST['submitBtn'])){
                // Récupération des données du formulaire. ($_POST)
                $desc = htmlspecialchars($_POST['descAnomalie']);
                $date = date("Y-m-d");
                $idRessource = htmlspecialchars($_POST['idRessource']);
                // Ajout d'un nouveau ticket au modèle des tickets.
                TicketModel::addTicket(new Ticket(null, $desc, $idRessource, $date, "Ouvert"));
                $html = "<p class='MessageValid'>Le ticket a bien été créé</p>
                        <a class='MessageValid' href='controller.php'>Cliquez ici pour revenir à l'accueil</a>";
            } else {
                header('Location: /');
            }
        // Demande de connexion à un compte (responsable ou administrateur)
        } elseif ($action == "login"){
            // Traitement des messages de renvoie du formulaire de connexion.
            if(isset($_GET['infoMsg'])){
                $infoMsg = htmlspecialchars($_GET['infoMsg']);
                switch ($infoMsg) {
                case 'errorAuth':
                    $message = "<p class='MessageError'>Vérifiez vos informations de connexion!</p>";
                    break;     
                default:
                    $message = "";
                    break;
                }
            } else {
                $message = "";
            }
            // Ajout de l'affichage du formulaire d'authentification au contenu html.
            $html = $message.AuthView::displayAuthView();
        // Deconnexion du compte
        } elseif ($action == "logout"){
            // Suppressions relative à la session et redirection à la page principale.
            session_unset();
            session_destroy();
            header('Location: controller.php');
        }
    // Demande d'accés au formulaire de rapport d'erreur.
    } elseif (isset($_GET['id'])){
        // L'identifiant représente la ressource demandée (accessible via le QR-Code ou directement en tapant l'URL)
        $html = UserView::displayUserForm(htmlspecialchars($_GET['id']));
    // Affichage par défaut de l'application.
    } else {
        $html = DefaultView::displayDefaultView();
    }

    // Affichage de la page html complète avec le contenu html généré selon la situation.
    echo AppView::showApp($html);

}?>