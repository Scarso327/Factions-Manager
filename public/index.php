<?php
ob_start(); // For the "remember session"...

// Directories...
define("ROOT", dirname(__DIR__) . DIRECTORY_SEPARATOR);
define("WEB", ROOT . 'public' . DIRECTORY_SEPARATOR);

define('SETTING', require ROOT.'settings.php');
define('LAN_OVERRIDE', require ROOT.'lang-override.php');
define('Errors', ROOT.'errors.php');

define('URL_PROTOCOL', ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://"));
define('URL_DOMAIN', $_SERVER['HTTP_HOST']);
define('URL', URL_PROTOCOL . URL_DOMAIN . '/');

// Database Settings (For Webserver)
define('DB_TYPE', SETTING["db-type"]);
define('DB_HOST', SETTING["db-host"][1]);
define('DB_NAME', SETTING["db-name"][1]);
define('DB_USER', SETTING["db-user"][1]);
define('DB_PASS', SETTING["db-pass"][1]);
define('DB_CHARSET', SETTING["db-set"]);

// Database Settings (For Lifeserver)
define('DB_HOST_LIFE', SETTING["db-host"][0]);
define('DB_NAME_LIFE', SETTING["db-name"][0]);
define('DB_USER_LIFE', SETTING["db-user"][0]);
define('DB_PASS_LIFE', SETTING["db-pass"][0]);

// Other Settings
define('VER', '2.0.0');
define('API_VER', '1.0.0');

define('EXT_API_KEY', 'drv6hVTTkKNtDS9y8RtUsP8jAgm9YqcVDUGrN3gFuqsdYDvKrDYfktkU4wM9C9n5');

require_once "../includes.php";
Session::start();

new Application;