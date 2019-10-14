<?php

class Home extends Controller {

    public function __construct() {
        parent::__construct(false);

        Controller::$currentPage = "Dashboard";
        Controller::addCrumb(array("Dashboard", ""));
    }

    public function index () {
        $params = array ();
        
        Controller::$subPage = "Home";
        Controller::addCrumb(array("Home", ""));
        Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/dash/body'), $params);
    }

    public function public ($faction = null, $print = null) {
        if ($faction == null) {
            new DisplayError("#404");
        } else {
            $members = Factions::getFactionMembers($faction);

            if ($print == null) {
                Controller::$subPage = strtoupper($faction)." Roster";
                Controller::addCrumb(array(strtoupper($faction)." Roster", "home/public/".$faction));

                $params = array (
                    "members" => $members,
                    "faction" => $faction,
                    "public" => true,
                    "archive" => false
                );
                
                Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/faction/roster'), $params);
            } else {
                switch ($print) {
                    case "json":
                        header('Content-Type: application/json');
                        echo json_encode($members, JSON_PRETTY_PRINT);
                        break;
                    default:
                        new DisplayError("#404");
                        exit;
                }
            }
        }
    }

    public function theme () {
        if (isset($_COOKIE['dark-theme'])) {
            setcookie("dark-theme", null, -1, "/");
        } else {
            setcookie("dark-theme", true, time() + (10 * 365 * 24 * 60 * 60), "/");
        }
        
        header("Location: ".URL);
    }
}