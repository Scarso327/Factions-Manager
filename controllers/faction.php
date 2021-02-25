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

    public function index ($steamid = null, $subpage = "") { self::database($steamid, $subpage, false); }
    public function archive ($steamid = null, $subpage = "") { self::database($steamid, $subpage, true); }

    private function database ($steamid = null, $subpage = "", $archive = false) {
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
            $params = array ();

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
                
                $css = array ("custom/view.css");

                // Allows us to run custom code per page
                switch ($subpage) {
                    case '':
                        $params["history"] = self::getHistory($steamid);
                        break;
                    case 'units':
                        $params["units"] = Units::getUnits(self::$var);

                        if (!$params["units"]) {
                            new DisplayError("#404");
                            return;
                        }

                        $units = array();

                        foreach ($params["units"] as $unit) {
                            $unit = Units::getUnit(self::$var, $unit->id);

                            if ($unit) {
                                $units[$unit["unit"]->name] = $unit;
                            }
                        }

                        $params["units"] = $units;
                        array_push($css, 'custom/units.css');
                        break;
                }

                Controller::addCrumb(array($member->name, self::$var."/member/".$steamid));
                Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/faction/member/member'), array(
                    "css" => $css,
                    "member" => $member,
                    "steam" => Steam::getSteamInfo($steamid),
                    "powers" => $powers,
                    "params" => $params,
                    "subpage" => $subpage
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

    public function units($unit = null) {
        $views = array (
            ROOT . 'views/navbar'
        );
        $params = array (
            "css" => array (
                'custom/units.css'
            )
        );

        Controller::$subPage = "Units";
        Controller::addCrumb(array("Units", self::$var."/units/"));

        if ($unit == null) {
            array_push($views,  ROOT . 'views/faction/units/list');
            $params["units"] = Units::getUnits(self::$var);

            if (!$params["units"]) {
                new DisplayError("#404");
                return;
            }
        } else {
            array_push($views,  ROOT . 'views/faction/units/page');

            $unit = Filter::XSSFilter($unit);
            $params["unit"] = Units::getUnit(self::$var, $unit);

            if (!$params["unit"]) {
                new DisplayError("#404");
                return;
            }

            $params["unit_ranks"] = $params["unit"]["ranks"];
            $params["unit"] = $params["unit"]["unit"];
            $params["unit_members"] = Units::getUnitMembers($unit);

            Controller::addCrumb(array($params["unit"]->name, self::$var."/units/".$unit));
        }

        Controller::buildPage($views, $params);
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
            "sections" => Application::getSections(self::$var),
            "units" => Units::getUnits(self::$var)
        );
        
        Controller::$subPage = "Statistics";
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