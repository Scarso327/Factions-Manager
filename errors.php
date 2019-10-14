<?php
/**
 * Created by PhpStorm.
 * User: ScarsoLP
 * Date: 15/03/2018
* Time: 10:41 PM
*/
return array(
    // Your errors (Any custom errors you wish to create make below here using the template below)
    // The ID must ALWAYS begin with a hastag as it's needed for custom links etc.
    // '#ID' => array('TITLE','DESCRIPTION'),
    // Fe Errors (Custom Framework Errors)
    '#Fe001' => array('BAD REDIRECT','We don\'t have such a redirect outlined in our system! <br>Please return <a href="'.URL.'">home!</a>'),
    '#Fe002' => array('BAD Controller','The Controller you\'re trying to access exists in both the Application and a plugin! <br>Please return <a href="'.URL.'">home!</a>'),
    '#Fe003' => array('UNKNOWN ERROR','An error has occurred that the system is unable to fix. <br>Please return <a href="'.URL.'">home!</a>'),
    '#Fe004' => array('DATABASE FAILURE','An error has occurred when trying to preform an action using the database. <br>Please return <a href="'.URL.'">home!</a>'),
    '#Fe005' => array('SETTING FAILURE','No factions have been defined in the database. Please contact the web master to resolve this issue...'),
    '#Fe006' => array('MEMBER NOT FOUND','This steamid is not currently a member within this faction...'),
    '#Fe007' => array('INSUFFICENT PERMISSIONS','You don\'t have the required permissions to perform this action...'),
    '#Fe008' => array('UNABLE TO PERFORM ACTION','This faction member is currently archived and so you\'ve been unable to perform the specificed action...'),
    '#Fe009' => array('MEMBER ALREADY ADDED','This member already exists and so was not able to be added to the database...'),
    '#Fe010' => array('ALREADY ON LOA / HOLIDAY','This member is already on LOA / Holiday and so can\'t be added to another...'),
    '#Fe011' => array('SUSPENDED','You\'re currently suspended from your duties and so can\'t access the database...'),
    '#Fe012' => array('VOTING IS ACTIVE','You\'re unable to preform this action while voting is unlocked...'),
    '#Fe013' => array('VOTING IS LOCKED','You\'re unable to preform this action while voting is locked...'),
    '#Fe014' => array('SUSPENDED','This action can\'t be preformed as this member is currently suspended...'),
    '#Fe015' => array('LOCKED STATION','This action can\'t be preformed until this officer is removed from Academy or MI5 from the Specialised Unit page...'),
    '#Fe016' => array('FIELD MISSING','An error seems to have occured with the creation of this form...'),
    '#Fe017' => array('FIELD EMPTY','A required field has no data...'),
    '#Fe018' => array('INVALID STEAMID','A steamid was entered that failed the regex test...'),
    '#Fe019' => array('NAME ALREADY TAKEN','This name is already taken by someone within this factions history...'),
    '#Fe020' => array('REQUIRES PREDEFINED STEAMID','This form requires a valid predefined steamid...'),
    '#Fe021' => array('SECTION NOT FOUND','The section you\'ve selected does not exist...'),
    '#Fe022' => array('ALREADY IN SECTION','This person is already in this section...'),
    '#Fe023' => array('RANK NOT FOUND','This rank does not exist within this section...'),
    '#Fe024' => array('AREADLY AT THIS RANK','This person is already at this rank...'),
    // Normal Errors
    '#400' => array('BAD REQUEST','We have received a request that does not quite look right! <br>Please refresh your browser or return <a href="'.URL.'">home!</a>'),
    '#401' => array('AUTHENTICATION REQUIRED','We require authentication to be allow to preform this task! <br>Please return <a href="'.URL.'">home!</a>'),
    '#403' => array('ACCESS FORBIDDEN','You don\'t have the required access to access this page or preform that task! <br>Please return <a href="'.URL.'">home!</a>'),
    '#404' => array('PAGE NOT FOUND','The page you have requested cannot be found on our site! <br>If you believe this to be an error please report it to the web master. <br>If you believe it is not an error, return <a href="'.URL.'">home!</a>'),
    '#500' => array('INTERNAL SERVER ERROR', 'Oh no! We have experienced an error when processing your request! <br>If you are able to please report it to the web master. <br><a href="'.URL.'">Safety</a>')
);