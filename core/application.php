<?php

class Application {

    private $controller = null;
    private $action = null;
    private $params = array();

    public static $isDark = true;
    public static $factions = array ();

    public function __construct() {
        $system = new System;
        self::$factions = $system->getFactions();

        self::URLSetup();
        
        self::$isDark = false;
        if (isset($_COOKIE['dark-theme'])) {
            self::$isDark = true;
        }
        
        if (isset($_GET['logout'])) { Account::logout(); } // Logout...

        switch (true) {

            case ($this->controller):

                // If the controller is equal to E it's an error redirect...
                if ($this->controller == "e") {
                    new DisplayError('#'.$this->action);
                    exit;
                }

                if (in_array($this->controller, array_column(self::$factions, "abr"))) {
                    require_once ROOT.'controllers/faction.php';
                    $this->controller = new Faction ($this->controller);

                    self::actionHandle();
                    exit;
                }
                
                if (file_exists(ROOT.'controllers/'.$this->controller.'.php')) {

                    require_once ROOT.'controllers/'.$this->controller.'.php';
                    $this->controller = new $this->controller;

                    self::actionHandle();
                    exit;
                } else {
                    new DisplayError("#404");
                }

                break;

            default:
                $this->controller = new Home;
                self::actionHandle();
        }

        new Tasks;
    }

    public function URLSetup () {
        if (isset($_GET['url'])) {
            $url = trim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            $this->controller = isset($url[0]) ? $url[0] : null;
            $this->action = isset($url[1]) ? $url[1] : null;
            unset($url[0], $url[1]);
            $this->params = array_values($url);
        }
    }

    public static function convertBool($bool) {
        return ($bool ? 1 : 0);
    }

    public static function randomStrGen($length = 64) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        
        $str = '';

        for ($i = 0; $i < $length; $i++) {
            $str .= $characters[rand(0, $charactersLength - 1)];
        }

        return $str;
    }

    public function actionHandle () {
        if($this->action) {
            if (Steam::isSteamID($this->action)) {
                $subpage = "";
                if (array_key_exists(0, $this->params)) { $subpage = $this->params[0]; }

                $this->controller->index($this->action, $subpage);
            } else {
                if(method_exists($this->controller, $this->action)) {
                    if (!empty($this->params)) {
                        call_user_func_array(array($this->controller, $this->action), $this->params);
                    } else {
                        $this->controller->{$this->action}();
                    }
                } else {
                    new DisplayError("#404");
                }
            }
        } else {
            $this->controller->index();
        }
    }

    public static function getSections ($faction) {
        return (self::$factions[$faction]["sections"]);
    }

    public static function getRanks ($faction) {
        return (self::$factions[$faction]["ranks"]);
    }

    // Usefulfor doing what the name says...
    public static function getRankIDFromName ($faction, $name) {
        $factionArr = self::$factions[$faction];

        foreach ($factionArr["ranks"] as $rank) {
            if ($rank->name == $name) {
                return $rank->id;
            }
        }

        return $factionArr["defaultRank"]; // Fail safe...
    }
}