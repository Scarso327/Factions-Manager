<?php

class Actions {

    public function insertMember($faction, $name, $steamid, $forumid, $rank, $section) {
        if (!Steam::isSteamID($steamid)) { new DisplayError("#Fe018"); exit; }
        if (Factions::getMember($faction, $steamid)) { new DisplayError("#Fe009"); exit; }

        $rank = Application::getRankIDFromName($faction, $rank);

        $query = Database::getFactory()->getConnection(DB_NAME)->prepare(
            "INSERT INTO members (faction, `name`, steamid, forumid, section, mainlevel) VALUES (:faction, :name, :steamid, :forumid, :section, :rank)"
        );

        $query->execute(array(
            ':faction' => $faction,
            ':name' => $name,
            ':steamid' => $steamid,
            ':forumid' => $forumid,
            ':section' => $section,
            ':rank' => $rank
        ));

        $rank = (Application::getRanks($faction)[$rank])->level;

        $API = new API;
        $API->internal = true;
        $API->whitelist($faction, $steamid, "main", $rank);

        if ($query->rowCount() == 1) {
            return true;
        }

        new DisplayError("#500");
        exit;
    }

    public function renameMember($faction, $steamid, $newName, $notes) {
        if (Factions::isNameTaken($faction, $newName)) { new DisplayError("#Fe019"); exit; }
        return (Member::rename($faction, $steamid, $newName));
    }

    public function blacklist($faction, $steamid, $notes, $evidence = "N/A") {
        if (!Steam::isSteamID($steamid)) { new DisplayError("#Fe018"); exit; }
        if (!Factions::getMember($faction, $steamid)) { new DisplayError("#Fe006"); exit; }

        $isBlacklisted = Application::convertBool(!Member::isState($faction, $steamid, "isBlacklisted"));
        return (Member::changeState($faction, $steamid, "isBlacklisted", $isBlacklisted));
    }

    public function suspend($faction, $steamid, $notes, $evidence = "N/A") {
        if (!Steam::isSteamID($steamid)) { new DisplayError("#Fe018"); exit; }

        $member = Factions::getMember($faction, $steamid);

        if (!$member) { new DisplayError("#Fe006"); exit; }

        $suspended = (Member::isState($faction, $steamid, "isSuspended"));

        $API = new API;
        $API->internal = true; // We're using it interally...

        // Whitelisting...
        if (!$suspended) {
            $apiRet = $API->whitelist($faction, $steamid, "main", 0);
        } else {
            $apiRet = $API->whitelist($faction, $steamid, "main", ((Application::getRanks($faction)[$member->mainlevel])->level));
        }

        if (!$apiRet) { return false; }
        
        $isSuspended = Application::convertBool(!$suspended);
        return (Member::changeState($faction, $steamid, "isSuspended", $isSuspended));
    }

    public function transfer($faction, $steamid, $newSection, $notes) {
        if (!Steam::isSteamID($steamid)) { new DisplayError("#Fe018"); exit; }

        $member = Factions::getMember($faction, $steamid);

        if (!$member) { new DisplayError("#Fe006"); exit; }
        if (!array_key_exists($newSection, Application::getSections($faction))) { new DisplayError("#Fe021"); exit; }
        if ($member->section == $newSection) { new DisplayError("#Fe022"); exit; }

        return (Member::transfer($faction, $steamid, $newSection));
    }

    public function changeRank($faction, $steamid, $newRank, $notes) {
        if (!Steam::isSteamID($steamid)) { new DisplayError("#Fe018"); exit; }

        $member = Factions::getMember($faction, $steamid);
        $newRank = Application::getRankIDFromName($faction, $newRank);

        if (!$member) { new DisplayError("#Fe006"); exit; }
        if (!array_key_exists($newRank, Application::getRanks($faction))) { new DisplayError("#Fe023"); exit; }
        if ($member->mainlevel == $newRank) { new DisplayError("#Fe024"); exit; }

        return (Member::changeLevel($faction, $steamid, $newRank));
    }

    public function archive($faction, $steamid, $notes) {
        $member = Factions::getMember($faction, $steamid);

        if (!Steam::isSteamID($steamid)) { new DisplayError("#Fe018"); exit; }
        if (!$member) { new DisplayError("#Fe006"); exit; }
        
        if (!Member::archive($faction, $steamid)) {
            return false;
        }

        $newEntry = ((object) array(
            "name" => "Leaving Rank",
            "fieldName" => "oldRnk".(Application::randomStrGen(8)), // Auto gen just in case someone uses the same fieldname...
            "value" => ((Application::getRanks($faction)[$member->mainlevel])->name)
        ));
        
        // Save the rank they left at...
        return (array($newEntry));
    }

    public function unarchive($faction, $steamid, $rank, $section, $notes) {
        if (!Steam::isSteamID($steamid)) { new DisplayError("#Fe018"); exit; }
        if (!Factions::getMember($faction, $steamid)) { new DisplayError("#Fe006"); exit; }
        
        $rank = Application::getRankIDFromName($faction, $rank);

        return (Member::unarchive($faction, $steamid, $rank, $section));
    }
}