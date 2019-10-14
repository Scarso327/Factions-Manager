<?php

class Session {

    // Creates a new Session to be used.
    public static function start() {
        // Ensure we don't already have a Session open before starting our new Session.
        if(Session_id() == "") {
            Session_start();
        }
    }

    // Gets data from a given key that's stored within our Session.
    public static function get($key) {
        if(isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return false;
        }
    }

    // Sets the data for a given key within our Session.
    // We'll also do our XSSFilter checks here.
    public static function set($key, $value) {
        $value = Filter::XSSFilter($value);
        $_SESSION[$key] = $value;
    }

    public static function remove($key) {
        if(isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    // Destroys our current Session deleting all it's data.
    public static function close() {
        Session_destroy();
    }
}