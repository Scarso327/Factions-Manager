<?php
/**
 * Created by PhpStorm.
 * User: ScarsoLP
 * Date: 15/03/2018
 * Time: 10:41 PM
 */
class DisplayError
{
    public function __construct($myError, $needReDirect = false)
    {
        $errors = Errors;
        $error = require $errors;

        if (!(isset($error[$myError]))) { $myError = "#Fe003"; }

        $error_title = $error[$myError][0];
        $error_message = $error[$myError][1];

        Controller::addCrumb(array("Error", ""));

        if($needReDirect) {
            $myError = str_replace("#", "", $myError);
            header("Location: ".URL."e/".$myError);
        } else {
            View::$isError = true;
            Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/system/error'), array(
                    'myError' => $myError,
                    'error_title' => $error_title,
                    'error_message' => $error_message
                )
            );
        }
    }
}