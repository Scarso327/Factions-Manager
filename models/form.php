<?php

class Form {

    public function getForm ($faction, $formID) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM forms WHERE id = :id AND (faction = :faction or faction = '') LIMIT 1");
        $query->execute(array(":id" => $formID, ":faction" => $faction));
        
        if ($query->rowCount() == 0) { return false; } 
        return $query->fetch();
    }

    public function getFields ($formID) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM fields WHERE form = :id");
        $query->execute(array(":id" => $formID));
        
        if ($query->rowCount() == 0) { return false; } 
        return $query->fetchAll();
    }
}