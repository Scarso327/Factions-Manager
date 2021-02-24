<?php

class API extends Controller {

    public static $internal = false; // Indicates whether we require internal "feedback"...

    public function __construct() {
        header('Content-Type: application/json');
    }

    public function index () {
        info();
    }

    public function info () {
        $info = array("version" => API_VER);

        if (self::$internal) { return $info; }
        self::return($info);
    }

    public function responses ($faction) {
        if (!self::$internal) { self::auth($faction); } // Only required if external...

        $responses = Logs::getResponse($_POST['id']);

        if (!$responses) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "response-not-found"));
            exit;
        }

        if (self::$internal) { return true; }
        self::return(array(
            "result" => "success", 
            "responses" => $responses
        ));
    }

    public function whitelist ($faction, $steamid = null, $type = null, $level = -1) {
        if (!self::$internal) { self::auth($faction); } // Only required if external...

        if ($steamid == null || $type == null || $level == -1) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "invalid-data"));
            exit;
        }
        
        if (!Steam::isSteamID($steamid)) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "steamid-validation-fail"));
            exit;
        }

        // Get the column we need to update...
        switch ($type) {
            case "main":
                $type = Application::$factions[$faction]["rank"];
                break;
            default:
                if (self::$internal) { return false; }
                self::return(array("result" => "fail", "reason" => "unknown-type"));
                exit;
        }

        // Get DB...
        $db = Database::getFactory(true)->getConnection(DB_NAME_LIFE, array(DB_HOST_LIFE, DB_USER_LIFE, DB_PASS_LIFE), true);

        if (!$db) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "db-connection-failed"));
            exit;
        }

        $db = $db->prepare("UPDATE ".SETTING["db-player-table"]." SET ".$type." = :newlevel WHERE playerid = :steamid LIMIT 1");
        $db->execute(array(
            ":newlevel" => $level,
            ":steamid" => $steamid
        ));

        if ($db->errorCode() != "0000") {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => $db->errorInfo()[2]));
            exit;
        }

        if ($db->rowCount() == 0) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "no-rows-updated"));
            exit;
        }

        if (self::$internal) { return true; }
        self::return(array("result" => "success", "responses" => array("steamid" => $steamid, "column" => $type, "level" => $level)));
    }
    
    public function unit () {
        if (!isset($_POST['faction'])) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "faction-not-set"));
            exit;
        }
  
        $faction = $_POST['faction'];

        if (!self::$internal) { self::auth($faction); } // Only required if external...

        $staff = Factions::getMember($faction, Account::$steamid);
        if ($staff == null) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "admin-not-found"));
            exit;
        }

        if (!Units::canChangeRank($staff, $faction)) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "no-permission"));
            exit;
        }

        if (!isset($_POST['steamid']) || !isset($_POST['unit_id']) || !isset($_POST['rank_id'])) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "invalid-data"));
            exit;
        }

        $steamid = $_POST['steamid'];
        $unit_id = $_POST['unit_id'];
        $rank_id = $_POST['rank_id'];

        if (!Steam::isSteamID($steamid)) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "steamid-validation-fail"));
            exit;
        }

        $unit = Units::getUnit($faction, $unit_id);

        if (!$unit) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "unit-doesnt-exist"));
            exit;
        }

        $unit = $unit["unit"];

        if ($unit->db_col == "") {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "unit-doesnt-have-col"));
            exit;
        }

        $member = Factions::getMember($faction, $steamid);
        if (!$member) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "member-doesnt-exist"));
            exit;
        }

        $rank = Units::getUnitRank($unit_id, $rank_id);
        if (!$rank) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "rank-id-doesnt-exist"));
            exit;
        }

        $fields = array(
            "steamid" => array (
                "name" => "Level",
                "fieldName" => "steamid",
                "value" => $steamid
            ),
            "newlevel" => array (
                "name" => "Rank",
                "fieldName" => "newlevel",
                "value" => $rank->name
            )
        );

        if (!Logs::log($faction, $fields, Account::$steamid, "Unit", "Rank Changed", 0)) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "log-failed"));
            exit;
        }

        if (!Member::setUnitRank($member->id, $unit_id, $rank_id)) {
            if (!Member::addUnitRank($member->id, $unit_id, $rank_id)) {
                if (self::$internal) { return false; }
                self::return(array("result" => "fail", "reason" => "local-set-rank-failed"));
                exit;
            }
        }

        $db = Database::getFactory(true)->getConnection(DB_NAME_LIFE, array(DB_HOST_LIFE, DB_USER_LIFE, DB_PASS_LIFE), true);

        if (!$db) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "db-connection-failed"));
            exit;
        }

        $db = $db->prepare("UPDATE ".SETTING["db-player-table"]." SET ".$unit->db_col." = :newlevel WHERE playerid = :steamid LIMIT 1");
        $db->execute(array(
            ":newlevel" => $rank->level,
            ":steamid" => $steamid
        ));

        if ($db->errorCode() != "0000") {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => $db->errorInfo()[2]));
            exit;
        }

        if ($db->rowCount() == 0) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "no-rows-updated"));
            exit;
        }
        
        if (self::$internal) { return true; }
        self::return(array("result" => "success", "responses" => array("steamid" => $steamid, "unit_id" => $unit_id, "rank" => $rank->name)));
    }

    public function toggleTheme () {
        if (isset($_COOKIE['dark-theme'])) {
            setcookie("dark-theme", null, -1, "/");
        } else {
            setcookie("dark-theme", true, time() + (10 * 365 * 24 * 60 * 60), "/");
        }

        if (self::$internal) { return true; } // Wtf...
        self::return(array("result" => "success"));
    }

    public function toggleStaff () {
        if (isset($_COOKIE['show-staff'])) {
            setcookie("show-staff", null, -1, "/");
        } else {
            setcookie("show-staff", true, time() + (10 * 365 * 24 * 60 * 60), "/");
        }

        if (self::$internal) { return true; } // Wtf...
        self::return(array("result" => "success"));
    }

    private function auth ($faction) {
        parent::__construct(true);

        if (!Factions::isMember($faction, Account::$steamid)) {
            if (!self::auth($faction)) {
                self::return(array("result" => "fail", "reason" => "auth-failed"));
                exit;
            }
        };

        return true;
    }

    private function return ($json) {
        echo json_encode($json, JSON_PRETTY_PRINT);
    }

    // Created just in case... Likely won't use...
    private function makeExternalAPICall($api, $method = "POST") {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'authorization: '.EXT_API_KEY
        ));
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_URL, $api);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
}