<?php
/*
    File: settings.php

    Author: Jack "Scarso" Farhall

    Description / Purpose:
    Holds & returns all system configuraiton.
*/
return array(
    'site-name' => 'Faction Database', // This is the name of our website. E.g. Microsoft or Apple or Steam.
    'version' => '1', // Holds framework version. Don't change this unless you know what you're doing.
    'db-type' => 'mysql', // We assume you're using the same type for both databases...
    'db-host' => array('localhost','localhost'), // Key 0 = Life Server Database, Key 1 = Web Server Database
    'db-name' => array('altislife','factions_manager'), // Key 0 = Life Server Database, Key 1 = Web Server Database
    'db-user' => array('root','root'), // Key 0 = Life Server Database, Key 1 = Web Server Database
    'db-pass' => array('',''), // Key 0 = Life Server Database, Key 1 = Web Server Database
    'db-set' => 'utf8', // We assume you're using the same set for both databases...
    // The forums url is designed to work with IPB as it puts the forumid-name in the url. If you have any other forum software you maybe required to alter code.
    'forums-url' => '', // If blank no stats links will appear Format: https://example.com/forums/{forumid} (forumid will be replaced with their forumid-name...)
    'stats-url' => '', // If blank no stats links will appear Format: https://example.com/stats/{steamid} (Steamid will be replaced with their steamid...)
    'steam-key' => 'XXXXXXX'
);
?>