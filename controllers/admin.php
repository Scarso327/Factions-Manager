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
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT steamid, mainlevel FROM ".$faction." WHERE isSuspended = '0' AND isLOA = '0' AND isHoliday = '0' AND isArchive = '0'");
        $query->execute();

        if ($query->rowCount() == 0) { return false; }

        $results = $query->fetchAll();

        foreach ($results as $result) {
            Database::changeLiveRank($result->steamid, $result->mainlevel, "coplevel");
        }
        
        header("Location: ".URL."admin/");
    }

    public function dewhitelist ($faction = "coplevel") {
        Database::wipeRanks($faction);
        header("Location: ".URL."admin/");
    }
}