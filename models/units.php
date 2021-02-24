<?php

class Units {

    public static function getUnits($faction_var) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM units WHERE (faction = :faction) AND active = 1");
        $query->execute(array(":faction" => $faction_var));

        if ($query->rowCount() == 0) { return false; }
        return $query->fetchAll();
    }

    public static function getUnitRanks($unit_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM units_ranks WHERE unit_id = :id AND active = 1");
        $query->execute(array(":id" => $unit_id));

        if ($query->rowCount() == 0) { return false; }

        $ranks = array();

        foreach ($query->fetchAll() as $rank) {
            $ranks[$rank->id] = $rank;
        };

        return $ranks;
    }

    public static function getUnit($faction_var, $unit_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM units WHERE (faction = :faction) AND (id = :id) AND active = 1");
        $query->execute(array(":faction" => $faction_var, ":id" => $unit_id));

        if ($query->rowCount() == 0) { return false; }

        $unit = array(
            "unit" => $query->fetch(),
            "ranks" => self::getUnitRanks($unit_id)
        );

        return $unit;
    }

    public static function getUnitMembers($unit_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT members.id AS id, members.name AS `name`, members.steamid AS steamid, members.forumid AS forumid, members.section AS section, members.mainlevel AS `rank`, units_members.rank_id AS `unit_rank`, units_members.rankdate AS `rankdate`, units_members.joindate AS `joindate` FROM members INNER JOIN units_members WHERE members.id = units_members.member_id AND units_members.unit_id = :unit_id");
        $query->execute(array(":unit_id" => $unit_id));

        if ($query->rowCount() == 0) { return false; }
        return $query->fetchAll();
    }

    public static function getUnitRank($unit_id, $rank_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM units_ranks WHERE id = :rank_id AND unit_id = :unit_id LIMIT 1");
        $query->execute(array(":unit_id" => $unit_id, ":rank_id" => $rank_id));

        if ($query->rowCount() == 0) { return false; }
        return $query->fetch();
    }

    public static function orderMembers($faction, $member1, $member2) {
        $ranks = Application::getRanks($faction);

        $m1Rank = $ranks[$member1->rank]->level;
        $m2Rank = $ranks[$member2->rank]->level;

        if ($m1Rank != $m2Rank) {
            return $m1Rank < $m2Rank;
        } else {
            return strtotime($member1->last_rank_change) > strtotime($member2->last_rank_change);
        }
    }

    public static function canChangeRank($member, $faction) {
        if (!Account::isLoggedIn()) { return false; } // Must be logged in...
        
        $rank = Application::getRanks($faction)[$member->mainlevel];

        $value = "unit_promote";
        if (!property_exists($rank, $value)) { return false; }
        if ((Application::getRanks($faction)[$member->mainlevel]->$value) == 0) { return false; }

        return true;
    }
}