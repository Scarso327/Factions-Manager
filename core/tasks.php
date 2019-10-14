<?php

class Tasks {

    /*
        I've designed the task system to be simple to avoid the use of cron tasks.
        It works by running this code each time someone accesses the website. If they access it between 00:00 and 04:00 it'll check if the tasks have been ran.
        If the tasks have not been run yet it'll run the lasts listed from lines 24 to 29.
    */

    public function __construct($force = false) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT `value`, `time` FROM settings WHERE `name` = 'tasksrun' LIMIT 1");
        $query->execute();

        if ($query->rowCount() == 0) { return false; }

        $result = $query->fetch();
        $update = null;

        // If we're within 4 hours of run time and it's not been ran... Run it...
        if (((time() - strtotime($result->time)) <= 14400 && $result->value == 0) || $force) {
            $update = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE settings SET `value` = '1' WHERE `name` = 'tasksrun'");

            // Tasks to run

            // Update faction last logins...
            foreach (Application::$factions as $faction) {
                self::updateLastLogins($faction["abr"]);
            }
        } else {
            if (time() - strtotime($result->time) > 14400) {
                $update = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE settings SET `value` = '0' WHERE `name` = 'tasksrun'");
            }
        }

        if ($update != null) {
            $update->execute(); // Update run value...
        }
    }

    public function updateLastLogins($faction) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT steamid FROM members WHERE faction = :faction");
        $query->execute(array(":faction" => $faction));

        if ($query->rowCount() == 0) { return false; }

        $results = $query->fetchAll();

        foreach ($results as $result) {
            $value = Application::$factions[$faction]["lastlogin"];

            $lastSeen = Member::getLastSeen($result->steamid, $value);

            if ($lastSeen->rowCount() != 0) {
                $officer = $lastSeen->fetch();

                $update = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE members SET last_login = :lastlogin WHERE faction = :faction AND steamid = :steamid LIMIT 1");
                $update->execute(array(
                    ":faction" => $faction,
                    ":steamid" => $officer->playerid, 
                    ":lastlogin" => $officer->$value
                ));
            }
        }
    }
}