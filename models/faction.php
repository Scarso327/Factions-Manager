<?php

class Factions {

    public static function getMember($faction, $steamid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM members WHERE faction = :faction AND steamid = :steamid limit 1");
        $query->execute(array(
            ':faction' => $faction,
            ":steamid" => $steamid
        ));
        
        if ($query->rowCount() == 0) { return false; }
        return $query->fetch();
    }

    // Used as it checks the archived state...
    public static function isMember($faction, $steamid, $archive = 0) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM members WHERE faction = :faction AND steamid = :steamid AND isArchive = :archive limit 1");
        $query->execute(array(
            ':faction' => $faction,
            ":steamid" => $steamid,
            ':archive' => $archive
        ));
        
        if ($query->rowCount() == 0) { return false; }
        return true;
    }

    public static function isNameTaken($faction, $name) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM members WHERE faction = :faction AND `name` = :name limit 1");
        $query->execute(array(
            ':faction' => $faction,
            ":steamid" => $name
        ));
        
        if ($query->rowCount() == 0) { return false; }
        return true;
    }

    public static function getFactionMembers($faction, $archive = 0) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM members WHERE faction = :faction AND isArchive = :archive");
        $query->execute(array(
            ':faction' => $faction,
            ':archive' => $archive
        ));

        if ($query->rowCount() == 0) { return false; }

        return $query->fetchAll();
    }

    public static function getFactionMembersBySection ($faction, $section) {
        $members = self::getFactionMembers($faction);

        $return = array();

        foreach ($members as $member) {
            if ($member->section == $section) {
                array_push($return, $member);
            }
        }

        return $return;
    }

    public static function getFactionMembersByRank($faction, $rank) {
        $members = self::getFactionMembers($faction);

        $return = array();

        foreach ($members as $member) {
            if ($member->mainlevel == $rank) {
                array_push($return, $member);
            }
        }

        return $return;
    }

    public static function getActiveFactionMembers ($faction) {
        $members = self::getFactionMembers($faction);

        $return = array();

        foreach ($members as $member) {
            if (strtotime($member->last_login) >= strtotime("-1 week") || $member->section == "Reserves") {
                array_push($return, $member);
            }
        }

        return $return;
    }

    public static function orderRanks($rank1, $rank2) { return $rank1->level < $rank1->level; }

    public static function orderMembers($faction, $member1, $member2) {
        $ranks = Application::getRanks($faction);

        $m1Rank = $ranks[$member1->mainlevel]->level;
        $m2Rank = $ranks[$member2->mainlevel]->level;

        if ($m1Rank != $m2Rank) {
            return $m1Rank < $m2Rank;
        } else {
            return strtotime($member1->last_rank_change) > strtotime($member2->last_rank_change);
        }
    }
}