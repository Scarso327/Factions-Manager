<?php

class Admin extends Controller {

    public function __construct() {
        parent::__construct(true);

        if (!Accounts::IsAdmin(Account::$steamid)) {
            new DisplayError("#403");
            exit;
        };
        
        Controller::$currentPage = "Admin";
        Controller::addCrumb(array("Admin", "admin/"));
    }

    public function index () {
        $params = array ();

        Controller::$subPage = "Home";
        Controller::addCrumb(array("Home", "admin/"));
        Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/admin/home'), $params);
    }

    public function factions () {
        Controller::$subPage = "Factions";
        Controller::addCrumb(array("Factions", "admin/factions/"));
        Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/admin/pages/factions'), array ());
    }

    public function sync () {
        new Tasks(true);
        header("Location: ".URL."admin/");
    }

    public function whitelist ($faction = "apc") {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT steamid, mainlevel FROM members WHERE faction = :faction AND isSuspended = '0' AND isLOA = '0' AND isHoliday = '0' AND isArchive = '0'");
        $query->execute(array(
            ':faction' => $faction
        ));

        $results = $query->fetchAll();

        if (count($results) >= 1) {
            $ranks = Application::getRanks($faction);

            $API = new API;
            $API->internal = true;

            foreach ($results as $result) {
                $API->whitelist($faction, $result->steamid, "main", ($ranks[$result->mainlevel])->level);
            }
        }

        header("Location: ".URL."admin/");
    }

    public function dewhitelist ($column = "coplevel") {
        $column = Filter::XSSFilter($column);

        $db = Database::getFactory(true)->getConnection(DB_NAME_LIFE, array(DB_HOST_LIFE, DB_USER_LIFE, DB_PASS_LIFE), true);

        if ($db) {
            $db = $db->prepare("UPDATE ".SETTING["db-player-table"]." SET ".$column." = '0'");
            $db->execute();
        }

        header("Location: ".URL."admin/");
    }
}