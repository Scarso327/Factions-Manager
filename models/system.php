<?php

class System {

    public function getSubpages ($faction) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT `id`, `name`, `subdirectory`, `active` FROM subpages WHERE faction = :faction");
        $query->execute(array(":faction" => $faction));
        
        if ($query->rowCount() == 0) { return array(); } 

        $return = array();

        foreach ($query->fetchAll() as $nav) {
            if ($nav->active == 1 && (System::canAccessPage($nav->id))) {
                array_push (
                    $return,
                    array ($nav->subdirectory, $nav->name)
                );
            };
        }

        return $return;
    }

    public function canAccessPage($pageID) {
        if (!Account::isLoggedIn()) { return false; } // Must be logged in...

        $member = Faction::$officer;
        if ($member == null) { return false; } // Wtf??
        
        $rank = Application::getRanks(Faction::$var)[$member->mainlevel];

        $value = "page_access_" . $pageID;
        if (!property_exists($rank, $value)) { return false; }
        if ((Application::getRanks(Faction::$var)[$member->mainlevel]->$value) == 0) { return false; }

        return true;
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
                'logo' => $faction->logoFile,
                'defaultRank' => $faction->defaultRank,
                'lastlogin' => $faction->lastlogin,
                'addFormID' => $faction->additionForm,
                'dbPage' => $faction->dbPage,
                'archivePage' => $faction->archivePage,
                'searchPage' => $faction->searchPage,
                'statsPage' => $faction->statsPage,
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