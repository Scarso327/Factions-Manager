<?php

class Login extends Controller {

    public function __construct() {
        parent::__construct(false, true);
    }

    public function index () {
        // If action isset then time to login!
        if ((isset($_GET['_action']))) {
            Session::remove("reason"); // Wipe this shit...
            Steam::OpenIDSteam();
        } else {
            $params = array (
                'css' => array ('login.css')
            );

            if (Session::get("reason")) {
                $params['reason'] = Session::get("reason");
            }

            Controller::$currentPage = "Login";
            Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/login/page'), $params);
        }
    }

    // Used for resyncing steam account details...
    public function resync() {
        if(Account::isLoggedIn()) {
            Steam::resync(Account::$steamid, true);
        } else {
            header("Location: ".URL);
        }
    }
}