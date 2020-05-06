<?php

class API extends Controller {

    public static $internal = false; // Indicates whether we require internal "feedback"...

    public function __construct() {
        header('Content-Type: application/json');
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

        $db = $db->prepare("UPDATE players SET ".$type." = :newlevel WHERE playerid = :steamid LIMIT 1");
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
    
    public function toggleTheme () {
        if (isset($_COOKIE['dark-theme'])) {
            setcookie("dark-theme", null, -1, "/");
        } else {
            setcookie("dark-theme", true, time() + (10 * 365 * 24 * 60 * 60), "/");
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