<?php

class Accounts {

    // Returns are:
    //      True: If we have a client entry...
    //      False: If we dont have a client entry...
    public static function IsUser($steamid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT steamid FROM accounts WHERE steamid = :steamid limit 1");
        $query->execute(array(":steamid" => $steamid));
        
        if ($query->rowCount() == 0) { return false; } 
        return true;
    }

    public static function IsAdmin ($steamid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT isAdmin FROM accounts WHERE steamid = :steamid limit 1");
        $query->execute(array(":steamid" => $steamid));
        
        if ($query->rowCount() == 0) { return false; } // NOT AN ADMIN!

        $user = $query->fetch();
        if ($user->isAdmin > 0) {
            return $user->isAdmin;
        } else {
            return false;
        }
    }

    // Used to create a database entry if our steam account is not already created.
    public static function createSteam($name = null, $steamid = null) {
        if($name != null && $steamid != null) {
            $query = Database::getFactory()->getConnection(DB_NAME)->prepare("INSERT INTO accounts (`name`, steamID) VALUES (:name, :steamID)");
            $query->execute(array(':name' => $name, ':steamID' => $steamid));
            if ($query->rowCount() == 1) { return true; }
        }

        return false;
    }

    // Updates relevent steam stuff...
    public static function updateSteam ($steamid, $steaminfo) {
        $statement = "UPDATE accounts
                      SET steamName = :steamName, steamid = :steamid, steampfp = :steampfp, steampfpmed = :steampfpmed, steampfplarge = :steampfplarge
                      WHERE steamid = :steamid LIMIT 1";
        $db = Database::getFactory()->getConnection(DB_NAME);
        $query = $db->prepare($statement);
        $query->execute(array(
            ':steamid' => $steamid, 
            ':steamName' => $steaminfo["steam-name"],
            ':steampfp' => $steaminfo["steam-pfp"],
            ':steampfpmed' => $steaminfo["steam-pfp-medium"],
            ':steampfplarge' => $steaminfo["steam-pfp-full"]
        ));
        if($query->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public static function updateAccess ($steamid, $adminlevel) {
        $statement = "UPDATE accounts SET isAdmin = :isAdmin WHERE steamid = :steamid LIMIT 1";
        $db = Database::getFactory()->getConnection(DB_NAME);
        $query = $db->prepare($statement);
        $query->execute(array(
            ':steamid' => $steamid, 
            ':isAdmin' => $adminlevel
        ));
        if($query->rowCount() > 0) {
            return true;
        }
        return false;
    }

    /*
    ** Thanks Kevin! (Checks our cookie against the database...)
    */
    public static function checkToken($steamid, $token) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT steamid FROM accounts WHERE steamid = :steamid AND remember_token = :token limit 1");
        $query->execute(array(":steamid" => $steamid, ":token" => $token));
        
        if ($query->rowCount() == 0) { return false; } 
        return true;
    }

    /*
    ** Thanks Kevin! (Sets our remember token...)
    */
    public static function setToken($steamid) {
        if (!(self::IsUser($steamid))) { return false; }

        // Create the token!
        $token = Application::randomStrGen(64);

        // Update our database!
        $statement = "UPDATE accounts SET remember_token = :token WHERE steamid = :steamid";
        $db = Database::getFactory()->getConnection(DB_NAME);
        $query = $db->prepare($statement);
        $query->execute(array(
            ':steamid' => $steamid, 
            ':token' => $token
        ));

        return $token;
    }
}