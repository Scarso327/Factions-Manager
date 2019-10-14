<?php

class System {

    public function getSubpages ($faction) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT `name`, `subdirectory`, `active` FROM subpages WHERE faction = :faction");
        $query->execute(array(":faction" => $faction));
        
        if ($query->rowCount() == 0) { return array(); } 

        $return = array();

        foreach ($query->fetchAll() as $nav) {
            if ($nav->active == 1) {
                array_push (
                    $return,
                    array ($nav->subdirectory, $nav->name)
                );
            };
        }

        return $return;
    }

    public function getFactions () {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM factions");
        $query->execute();
        
        if ($query->rowCount() == 0) { new DisplayError('#Fe005'); exit; } 

        $return = array();

        foreach ($query->fetchAll() as $faction) {
            $return[$faction->sys] = array(
                'abr' => $faction->sys,
                'name' => $faction->name,
                'rank' => $faction->rank,
                'defaultRank' => $faction->defaultRank,
                'lastlogin' => $faction->lastlogin,
                'addFormID' => $faction->additionForm,
                'sections' => (self::getSections($faction->sys)),
                'ranks' => (self::getRanks($faction->sys))
            );
        }

        return $return;
    }

    private function getSections ($faction) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM sections WHERE faction = :faction");
        $query->execute(array(":faction" => $faction));
        
        if ($query->rowCount() == 0) { return false; }
        $ret = array();
            
        foreach ($query->fetchAll() as $sec) {
            $ret[$sec->name] = $sec;
        }

        return $ret;
    }

    private function getRanks ($faction) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM ranks WHERE faction = :faction");
        $query->execute(array(":faction" => $faction));
        
        if ($query->rowCount() == 0) { return false; }
        $ret = array();
            
        foreach ($query->fetchAll() as $rank) {
            $ret[$rank->id] = $rank;
        }

        return $ret;
    }
}