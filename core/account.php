<?php

class Account {

    public static $steamid = null;
    public static $adminlevel = false;

    // Create the new "Account Session"
    public function __construct($steamid) {
        self::$steamid = $steamid;
        self::$adminlevel = Accounts::IsAdmin($steamid);
    }

    public static function isLoggedIn() {
        if (self::$steamid != null) { return true; } // If the steamid is not null, we're logged in...
        return false;
    }

    public static function logout($redirect = true) {
        // Session Handling...
        Session::start();
        Session::close();

        // Cookie Clearing...
        unset($_COOKIE['steam_id']);
        unset($_COOKIE['remember_token']);
        setcookie("steam_id", null, -1, "/");
        setcookie("remember_token", null, -1, "/");

        if ($redirect) {
            header("Location: ".URL."login/"); // Redirect us to login page...
        }
    }
}