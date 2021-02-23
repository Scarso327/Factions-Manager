<?php

/*
**  Protected Form Field Names:
**      - steamid : Indicates the member by steamid id e're submitting this for...
*/

/*
**  Rule On Form Custom Functions:
**      - faction : Is always the first variable passed, even if it's not used...
**      - fields : each field is then passed as a parameter in order of their apperance, even if it's not used...
*/

class Forms {

    public static $steamidOverride = ""; // Overrides any input named "steamid"...
    private static $fieldPrefix = "";
    private static $member = false;

    public static function buildForm ($form, $fields, $readonly = false) {
        $faction = $form->faction;

        if ($faction == "") {
            $faction = Faction::$var;
        }

        if (self::$steamidOverride != "") {
            self::$member = Factions::getMember($faction, self::$steamidOverride);

            if (!self::$member) {
                new DisplayError("#Fe006", true);
                exit;
            }
        }

        echo "
        <small>".$form->description."</small></br>
        <form autocomplete='off' method='".$form->method."' action='".URL.$faction."/form/".$form->id."-".str_replace(' ', '', $form->name)."/submitted/'>
            <input type='hidden' name = '_action'>
            ";

            foreach ($fields as $field) {
                self::$fieldPrefix = ""; // Reset...

                if ($readonly) { 
                    self::$fieldPrefix = " readonly"; 
                } else {
                    if ($field->required == 1) { self::$fieldPrefix = " required"; }
                }

                if ($field->hidden == 0) {
                    echo "
                    <div class='form-group'>
                        <label for='".$field->fieldName."'>".$field->name.":</label>";
                        self::{$field->type}($field, $faction, $readonly);
                        echo "
                        <small>".$field->description."</small>
                    </div>";
                } else {
                    self::{$field->type}($field, $faction, $readonly);
                }
            }

            echo "
            <div class='form-group'>
                <button type='submit'>Submit Form</button>
            </div>
        </form>
        ";
    }

    public static function onFormSubmit ($form, $fields) {
        if (!Form::canSubmitForm($form->id)) {
            new DisplayError("#Fe007");
            exit;
        }
        
        $faction = $form->faction;

        if ($faction == "") {
            $faction = Faction::$var;
        }

        $return = $form->return;
        $fieldsArr = array();

        foreach ($fields as $field) {
            $value = $_POST[$field->fieldName];

            switch (true) {
                case (!array_key_exists($field->fieldName, $_POST)):
                    new DisplayError("#Fe016");
                    exit;
                case ($value == "" && $field->required == 1):
                    new DisplayError("#Fe017");
                    exit;
                case ($value == "" && $field->required == 0):
                    $value = $field->default;
                    break;
            }

            Filter::XSSFilter($value);
            Filter::clearAll($value);

            $fieldsArr[$field->fieldName] = array(
                "name" => $field->name, 
                "fieldName" => $field->fieldName, 
                "value" => $value
            );

            $return = str_replace("{{".$field->fieldName."}}", $value, $return);
        }
        
        if ($form->customFunction != "") {
            $actions = new Actions;

            $params = array($faction);

            foreach ($fieldsArr as $field) {
                array_push($params, $field["value"]);
            }

            if (method_exists($actions, $form->customFunction)) {
                $ret = call_user_func_array(array($actions, $form->customFunction), $params);
                
                if (!$ret) {
                    new DisplayError("#500");
                    exit;
                }

                if (is_array($ret)) {
                    foreach ($ret as $field) {
                        $fieldsArr[$field->fieldName] = array(
                            "name" => $field->name, 
                            "fieldName" => $field->fieldName, 
                            "value" => $field->value
                        );
                    }
                }
            }
        }

        if ($form->submitLog == 1) {
            $level = (Form::getLowestRankWithAccess($form->id));

            // If $level is true then we'll use the lowest rank, if not use our current level...
            if ($level) {
                $level = $level->level;
            } else {
                $level = (Application::getRanks(Faction::$var)[Faction::$officer->mainlevel])->level;
            }

            if (!Logs::log($faction, $fieldsArr, Account::$steamid, $form->action, $form->status, $level)) {
                new DisplayError("#500");
                exit;
            }
        }

        Header("Location: ".URL.Faction::$var."/".$return);
    }

    // Different Fields...

    /* Generic Inputs */

    private static function Input ($field, $faction, $readonly) {
        $value = "";
        $type = "";

        if ($field->fieldName == "steamid") { $value = self::$steamidOverride; }
        if ($field->hidden == 1) { $type = "hidden"; }

        echo "<input type='".$type."' name = '".$field->fieldName."' value = '".$value."' ".$field->conditions.self::$fieldPrefix.">";
    }

    private static function Textarea ($field, $faction, $readonly) {
        echo "<textarea rows='5' cols='60' name = '".$field->fieldName."' ".$field->conditions.self::$fieldPrefix."></textarea>";
    }

    /* Global Dropdowns */

    // Lists all members of the faction excluding ourselves...
    private static function factionMembers ($field, $faction, $readonly) {
        echo "<select name = '".$field->fieldName."' ".$field->conditions.self::$fieldPrefix.">";
            foreach (Factions::getFactionMembers($faction) as $member) {
                echo '<option value="'.$rank->steamid.'">'.$member->name.'</option>';
            }
        echo "</select>";

    }

    // Lists all ranks within the faction...
    private static function factionRanks ($field, $faction, $readonly) {
        echo "<select name = '".$field->fieldName."' ".$field->conditions.self::$fieldPrefix.">";
            foreach (Application::getRanks($faction) as $rank) {
                echo '<option value="'.$rank->name.'">'.$rank->name.'</option>';
            }
        echo "</select>";
    }

    // Lists all sections within the (non-system) faction...
    private static function factionSections ($field, $faction, $readonly) {
        echo "<select name = '".$field->fieldName."' ".$field->conditions.self::$fieldPrefix.">";
            foreach (Application::getSections($faction) as $section) {
                if ($section->system == 0) {
                    echo '<option value="'.$section->name.'">'.$section->name.'</option>';
                }
            }
        echo "</select>";
    }

    /* Member Specific Dropdowns */

    // Lists all ranks except the rank the member is
    private static function memberRanks ($field, $faction, $readonly) {
        echo "<select name = '".$field->fieldName."' ".$field->conditions.self::$fieldPrefix.">";
            foreach (Application::getRanks($faction) as $rank) {
                if ($rank->id != self::$member->mainlevel) {
                    echo '<option value="'.$rank->name.'">'.$rank->name.'</option>';
                }
            }
        echo "</select>";
    }

    // Lists all sections except the one the member is in
    private static function memberSections ($field, $faction, $readonly) {
        echo "<select name = '".$field->fieldName."' ".$field->conditions.self::$fieldPrefix.">";
            foreach (Application::getSections($faction) as $section) {
                if ($section->system == 0 && ($section->name != self::$member->section)) {
                    echo '<option value="'.$section->name.'">'.$section->name.'</option>';
                }
            }
        echo "</select>";
    }

    // Like memberRanks but only the ranks above the current one
    private static function memberRanksAbove ($field, $faction, $readonly) {
        $level = (Application::getRanks($faction)[self::$member->mainlevel])->level;
        
        echo "<select name = '".$field->fieldName."' ".$field->conditions.self::$fieldPrefix.">";
            foreach (Application::getRanks($faction) as $rank) {
                if ($rank->level > $level) {
                    echo '<option value="'.$rank->name.'">'.$rank->name.'</option>';
                }
            }
        echo "</select>";
    }

    // List memberRanksAbove just only the opposite
    private static function memberRanksBelow ($field, $faction, $readonly) {
        $level = (Application::getRanks($faction)[self::$member->mainlevel])->level;
        
        echo "<select name = '".$field->fieldName."' ".$field->conditions.self::$fieldPrefix.">";
            foreach (Application::getRanks($faction) as $rank) {
                if ($rank->level < $level) {
                    echo '<option value="'.$rank->name.'">'.$rank->name.'</option>';
                }
            }
        echo "</select>";
    }
}