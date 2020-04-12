<?php

class Faction extends Controller {

    public static $var = "";
    public static $officer = null; // Contains the object for our player...

    public function __construct($faction) {
        parent::__construct(true);

        if (!Factions::isMember($faction, Account::$steamid)) {
            new DisplayError("#403");
            exit;
        };
        
        self::$var = $faction;
        self::$officer = Factions::getMember($faction, Account::$steamid);

        if (self::$officer->isSuspended == 1) {
            new DisplayError("#Fe011");
            exit;
        }

        Controller::$currentPage = strtoupper($faction);
        Controller::addCrumb(array(Controller::$currentPage, ($faction."/")));
    }

    public function index ($steamid = null) { self::database($steamid, false); }
    public function archive ($steamid = null) { self::database($steamid, true); }

    private function database ($steamid = null, $archive = false) {
        $archiveValue = 0;

        if ($archive) {
            if (!System::canAccessPage((Application::$factions[self::$var]["archivePage"]))) {
                new DisplayError("#403");
                exit;
            };

            Controller::$subPage = "Archive";
            $archiveValue = 1;
        } else {
            if (!System::canAccessPage((Application::$factions[self::$var]["dbPage"])) && ($steamid != Account::$steamid)) {
                Header("Location: ".URL.self::$var."/".Account::$steamid);
                exit;
            };

            Controller::$subPage = View::getLanguage(self::$var, "-db-short-title");
        }

        Controller::addCrumb(array((View::getLanguage(self::$var, "-db-title")), self::$var."/"));

        if ($steamid == null) {
            $params = array (
                "members" => Factions::getFactionMembers(self::$var, $archiveValue),
                "faction" => self::$var,
                "public" => false,
                "archive" => $archive
            );

            Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/faction/roster'), $params);
        } else {
            $steamid = Filter::XSSFilter($steamid); // Filter user inputs...
            $member = Factions::getMember(self::$var, $steamid);

            if ($member && Factions::isMember(self::$var, $steamid, $archiveValue)) {
                if ($member->isArchive == 1) {
                    Controller::addCrumb(array("Archive", self::$var."/arcive"));
                }

                Forms::$steamidOverride = $steamid;

                $powers = Powers::getPowers(self::$var, $member);

                if ($powers) {
                    foreach ($powers as $power) {
                        $form = Form::getForm(self::$var, $power->form);

                        if ($form) {
                            View::addForm($form);
                        }
                    }
                }

                Controller::addCrumb(array($member->name, self::$var."/member/".$steamid));
                Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/faction/member/member'), array(
                    "css" => array ("custom/view.css"),
                    "member" => $member,
                    "steam" => Steam::getSteamInfo($steamid),
                    "powers" => $powers,
                    "history" => self::getHistory($steamid)
                ));
            } else {
                new DisplayError("#Fe006");
            }
        }
    }

    private static function getHistory ($steamid) {
        $target = "member";
        $dates = array(date('Y-m-d', strtotime('-1 week')), date('Y-m-d'));

        if (isset($_GET['type'])) {
            $target = $_GET['type'];
            $dates = array($_GET['start-time'], $_GET['end-time']);
        }

        $history = array(
            "type" => $target,
            "dates" => $dates,
            "logs" => Logs::getHistory(self::$var, $steamid, $target, $dates)
        );

        return $history;
    }

    public function form($form = null, $submitted = false) {
        $formInfo = explode("-", $form);
        $form = Form::getForm(self::$var, ($formInfo[0]));

        Controller::$subPage = View::getLanguage(self::$var, "-db-short-title");
        Controller::addCrumb(array((View::getLanguage(self::$var, "-db-title")), self::$var."/")); 

        if (!$form || ($form->modal == 1 && !$submitted)) {
            new DisplayError("#404");
            exit;
        }

        if (!Form::canSubmitForm($form->id)) {
            new DisplayError("#Fe007");
            exit;
        }

        if ($form->predefinedSteamid == 1 && !$submitted) {
            if (!Steam::isSteamID($formInfo[2])) {
                new DisplayError("#Fe020");
                exit;
            }

            Forms::$steamidOverride = $formInfo[2];
        }

        $fields = Form::getFields($form->id);

        if (!$fields) {
            new DisplayError("#Fe016");
            exit;
        }

        if ($submitted) {
            Forms::onFormSubmit($form, $fields);
        } else {
            Controller::addCrumb(array($form->name, self::$var."/form/".$form->id."-".$form->name));
            Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/faction/form'), array(
                "form" => $form,
                "fields" => $fields
            ));
        }
    }

    public function stats() {
        if (!System::canAccessPage((Application::$factions[self::$var]["statsPage"]))) {
            new DisplayError("#403");
            exit;
        };

        $params = array (
            "css" => array (
                'custom/stats.css'
            ),
            "sections" => Application::getSections(self::$var)
        );
        
        Controller::$subPage = "Stats";
        Controller::addCrumb(array("Statistics", self::$var."/stats/"));
        Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/stats/stats'), $params);
    }

    public function search() {
        if (!System::canAccessPage((Application::$factions[self::$var]["searchPage"]))) {
            new DisplayError("#403");
            exit;
        };

        $steamid = "";

        if (isset($_GET['steamid'])) {
            $steamid = $_GET['steamid'];
        }

        Controller::$subPage = "Custom Search";
        Controller::addCrumb(array("Custom Search", self::$var."/search/"));
        Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/faction/search'), array (
            "css" => array ("custom/view.css"),
            "steamid" => $steamid,
            "history" => self::getHistory($steamid)
        ));
    }
}