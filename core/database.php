<?php

class Database
{
    private static $factory;
    private $database;

    public static function getFactory($forceRestart = false)
    {
        if (!self::$factory || $forceRestart) {
            self::$factory = new Database();
        }
        return self::$factory;
    }

    public function getConnection($dbname = null, $host = array(DB_HOST, DB_USER, DB_PASS), $isAPI = false) {
        if($dbname == null) {
            $dbname = DB_NAME;
        }
        try {
            $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
            $this->database = new PDO(DB_TYPE . ':host=' . $host[0] . ';dbname=' . $dbname . ';charset=' . DB_CHARSET, $host[1], $host[2], $options);
        } catch (PDOException $e) {
            if (!$isAPI) {
                Controller::buildPage(array(ROOT . 'views/system/db_fail'));
                die();
            } else {
                return false;
            }
        }
        return $this->database;
    }

    public function lastInsertId(){
        return self::$factory->database->lastInsertId();
    }
}
?>