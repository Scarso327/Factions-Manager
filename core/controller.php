<?php

// Parent Controller holding any functions we may require to call from multiple different Controllers.
class Controller {

    public static $currentPage = "Home";
    public static $subPage = "";
    public static $breadcrumbs = array();
    public static $steamid = false;

    public function __construct($auth = true) {
        Session::start(); // Start a Session to be used.
        
        if ((isset($_GET['login']))) { exit; }

        // Session = Alive...
        if (Session::get("steamid")) {
            // Cookie Revival Check...
            if (!(isset($_COOKIE['remember_token'])) || !(isset($_COOKIE['steam_id']))) {
                $token = Accounts::setToken($player->steamid);
                if (!$token) { Account::logout(true); exit; }

                setcookie("steam_id", Session::get("steamid"), time()+3600 * 24 * 365, "/");
                setcookie("remember_token", $token, time()+3600 * 24 * 365, "/");
            }
            
            // Ensure that if the remember_token changes then we stop this session...
            if (isset($_COOKIE['remember_token'])) {
                if (!(Accounts::checkToken(Session::get("steamid"), $_COOKIE['remember_token']))) {
                    Account::logout(true);
                    exit;
                }
            }
        } else {
            // Session Revival Check...
            if (isset($_COOKIE['remember_token']) && isset($_COOKIE['steam_id'])) {
                if (!(Accounts::checkToken($_COOKIE['steam_id'], $_COOKIE['remember_token']))) {
                    Account::logout(false);
                    Session::set("reason", "Session Expired");
                    header ("Location: ".URL."login");
                    exit;
                }

                Steam::resync($_COOKIE['steam_id']);
            }
        }
        
        Account::$steamid = Session::get("steamid");
        Account::$adminlevel = Accounts::IsAdmin(Account::$steamid);
        
        if ($auth) {
            // Check our login...
            if (!(Account::isLoggedIn())) {
                header ("Location: ".URL."login"); exit;
            }
            
            // Check if we even should have access...
            $failed = true;

            if (!Account::$adminlevel) {
                foreach (Application::$factions as $faction) {
                    if (Factions::isMember($faction["abr"], Account::$steamid)) { $failed = false; break; };
                };

                if ($failed) {
                    Account::logout(false); // Force session clearing...
                    Session::set("reason", "Access Removed");
                    header("Location: ".URL."login");
                }
            };
        }
    }

    // Adds our next breadcrumb to the array.
    public static function addCrumb($crumb) {
        array_push(self::$breadcrumbs, $crumb);
    }

    public static function buildCrumbs() {
        foreach (self::$breadcrumbs as $crumb) {
            echo '<a href="'.URL.$crumb[1].'">'.$crumb[0].'</a>';
        
            if ($crumb != end(self::$breadcrumbs)) {
                echo ' <span class="slash">/</span> ';
            }
        }
    }

    public static function buildPage($pages = false, $data = null) {
        new View($pages, $data);
    }
}
?>
