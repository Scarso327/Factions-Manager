<?php

class Logs {

    /*
    ** Creates a new "log" or "Form" as they were known before v2
    */
    public function log ($faction, $fields, $actioner, $action, $status) {
        $member = $fields["steamid"]["value"];

        if ($faction == "" || $member == "" || $actioner == "" || $action == "" || $status == "" || !Factions::getMember($faction, $member)) {
            return false;
        }

        $db = Database::getFactory()->getConnection(DB_NAME);

        $query = $db->prepare(
            "INSERT INTO logs (faction, member, actioner, `action`, `status`) VALUES (:faction, :member, :actioner, :action, :status)"
        );
        
        $query->execute(array(
            ':faction' => $faction,
            ':member' => $member, 
            ':actioner' => $actioner,
            ':action' => $action,
            ':status' => $status
        ));

        if ($query->rowCount() == 1) {
            $logID = Database::lastInsertId();

            foreach ($fields as $field) {
                // If it's not an account indicator (steamid), add it to responses...
                if ($field["fieldName"] != "steamid") {
                    $query = $db->prepare(
                        "INSERT INTO responses (logid, `name`, `value`) VALUES (:id, :name, :value)"
                    );
                    
                    $query->execute(array(
                        ':id' => $logID,
                        ':name' => $field["name"], 
                        ':value' => $field["value"]
                    ));

                    if ($query->rowCount() != 1) {
                        $query = $db->prepare("DELETE FROM logs WHERE id = :id");
                        $query->execute(array(':id' => $logID));

                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
    
    /*
    ** Gets logs for given target...
    */
    public function getHistory ($faction, $steamid, $target = "member", $dates = array()) {
        if (!(count($dates) > 0)) {
            $dates = array(date('Y-m-d', strtotime('-1 week')), date('Y-m-d'));
        }

        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM logs WHERE faction = :faction AND ".$target." = :member AND (DATE(timestamp) > :start AND DATE(timestamp) <= :end) AND hidden = 0");
        $query->execute(array(":faction" => $faction, ":member" => $steamid, ":start" => $dates[0], ":end" => $dates[1]));

        if ($query->rowCount() == 0) { return false; }
        return $query->fetchAll();
    }

    /*
    ** Read it...
    */
    public function getResponse($logid) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM responses WHERE logid = :id");
        $query->execute(array(":id" => $logid));

        if ($query->rowCount() == 0) { return false; }
        return $query->fetchAll();
    }
}