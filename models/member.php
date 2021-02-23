<?php

class Member {

    public static function archive($faction, $steamid) {
        API::$internal = true;
        API::whitelist($faction, $steamid, "main", 0);

        $lowestRank = Application::$factions[$faction]["defaultRank"];

        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE members SET section = 'Archive', mainlevel = :rank, isSuspended = 0, isBlacklisted = 0, isLOA = 0, isHoliday = 0, isArchive = 1, isBlocked = 0 WHERE faction = :faction AND steamid = :steamid LIMIT 1");
        $query->execute(array(
            ':rank' => $lowestRank,
            ':faction' => $faction,
            ':steamid' => $steamid
        ));

        if ($query->rowCount() == 1) {
            return true;
        }

        return false;
    }

    public static function unarchive($faction, $steamid, $rank, $section) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE members SET section = :section, mainlevel = :rank, isArchive = 0 WHERE faction = :faction AND steamid = :steamid LIMIT 1");
        $query->execute(array(
            ':section' => $section,
            ':rank' => $rank,
            ':faction' => $faction,
            ':steamid' => $steamid
        ));

        if ($query->rowCount() == 1) {
            $rank = (Application::getRanks($faction)[$rank])->level;

            API::$internal = true;
            API::whitelist($faction, $steamid, "main", $rank);

            return true;
        }

        return false;
    }

    public static function changeLevel($faction, $steamid, $newRank) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE members SET mainlevel = :rank, last_rank_change = CURRENT_TIMESTAMP() WHERE faction = :faction AND steamid = :steamid LIMIT 1");
        $query->execute(array(
            ':rank' => $newRank,
            ':faction' => $faction,
            ':steamid' => $steamid
        ));

        if ($query->rowCount() == 1) {
            $rank = (Application::getRanks($faction)[$newRank])->level;

            API::$internal = true;
            API::whitelist($faction, $steamid, "main", $rank);

            return true;
        }

        return false;
    }

    public static function rename($faction, $steamid, $newName) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE members SET `name` = :name WHERE faction = :faction AND steamid = :steamid LIMIT 1");
        $query->execute(array(
            ':name' => $newName,
            ':faction' => $faction,
            ':steamid' => $steamid
        ));

        if ($query->rowCount() == 1) {
            return true;
        }

        return false;
    }

    public static function transfer($faction, $steamid, $newSection) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE members SET `section` = :section WHERE faction = :faction AND steamid = :steamid LIMIT 1");
        $query->execute(array(
            ':section' => $newSection,
            ':faction' => $faction,
            ':steamid' => $steamid
        ));

        if ($query->rowCount() == 1) {
            return true;
        }

        return false;
    }
    
    public static function isState($faction, $steamid, $state) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM members WHERE faction = :faction AND steamid = :steamid AND ".$state." = 1 limit 1");
        $query->execute(array(
            ':faction' => $faction,
            ":steamid" => $steamid
        ));
        
        if ($query->rowCount() == 0) { return false; }
        return true;
    }

    public static function changeState($faction, $steamid, $state, $value) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("UPDATE members SET ".$state." = :value WHERE faction = :faction AND steamid = :steamid LIMIT 1");
        $query->execute(array(
            ':faction' => $faction,
            ':steamid' => $steamid,
            ':value' => $value
        ));

        if ($query->rowCount() == 1) {
            return true;
        }

        return false;
    }

    public static function getCustomID($faction, $member) {
        $forumID = $member->forumid;
        $constab = $member->section;

        // Is it less than 4 in length? Add 0s to the start...
        $len = strlen($forumID);
        if ($len < 4) {
            for ($x = 0; $x < (4 - $len); $x++) {
                $forumID = "0".$forumID;
            } 
        }

        $sec = Application::getSections($faction)[$constab];

        $prefix = ($sec->prefix == 1) ? $sec->shortName : "";

        // Return it!
        return $prefix.$forumID;
    }

    public static function getActivity ($member) {
        $activity = "Active";
        if ($member->isArchive == 1 && $member->isBlacklisted == 0) {
            $activity = "Archived";
        } else {
            if ($member->isBlacklisted == 1) {
                $activity = "Blacklisted";
            } else {
                if ($member->isSuspended == 1) {
                    $activity = "Suspended";
                } else {
                    if ($member->isLOA == 1) {
                        $activity = "LOA";
                    } else {
                        if ($member->isHoliday == 1) {
                            $activity = "Holiday";
                        } else {
                            if ($member->section == "Reserves") {
                                $activity = "Reserve";
                            } else {
                                if (strtotime($member->last_login) < strtotime("-1 week")) {
                                    $activity = "Inactive";
                                }
                            }
                        }
                    }
                }
            }
        }
        return $activity;
    }

    public static function getLastSeen($steamid, $value = "lastcopseen") {
        $thisPlayer = Database::getFactory(true)->getConnection(DB_NAME_LIFE, array(DB_HOST_LIFE, DB_USER_LIFE, DB_PASS_LIFE))->prepare(
            "SELECT playerid, ".$value." FROM ".SETTING["db-player-table"]." WHERE playerid = :steamid LIMIT 1"
        );
        $thisPlayer->execute(array(":steamid" => $steamid));
        return $thisPlayer;
    }
}