<?php

require_once ROOT."openid.php";

class Steam {

    // Starts the OpenID Steam stuff for loggin...
    public static function OpenIDSteam () {

        // If they're already logged in, why relog them in?
        if (Account::isLoggedIn()) { 
            Session::set("reason", "Already Logged In");
            header("Location: ".URL."login");
            exit; 
        }

        Session::start(); // Ensure we've got a session open...

        try {
            $openid = new LightOpenID(URL);
            if(!$openid->mode) {
                $openid->identity = 'https://steamcommunity.com/openid';
                header('Location: ' . $openid->authUrl());
            } elseif ($openid->mode == 'cancel') {
                self::OnFailedLogin();
            } else {
                if($openid->validate()) { 
                    $id = $openid->identity;
                    $steamid = str_replace("https://steamcommunity.com/openid/id/","",$id);
                    self::OnSuccesfulLogin($steamid);
                    exit;
                }
                self::OnFailedLogin();
                exit;
            }
        } catch(ErrorException $e) {
            self::OnFailedLogin($e->getMessage());
            exit;
        }
    }

    public static function isSteamID($steamid) {
        return (Is_Numeric($steamid) && (strlen($steamid) == 17));
    }

    // We've logged in!
    private static function OnSuccesfulLogin($steamid) {
        self::resync($steamid, true);
    }

    public static function resync($steamid, $redirect = false) {
        $steamauth = require ROOT.'settings.php';
        $url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$steamauth['steam-key']."&steamids=".$steamid; 
        $json_object = file_get_contents($url);
        if($json_object === false) {
            self::OnFailedLogin("Steam Failed...");
        } else {
            $json_decoded = json_decode($json_object);
            foreach ($json_decoded->response->players as $player)
            {  
                // Check our faction levels...
                $faction = null;

                foreach (Application::$factions as $faction) {
                    if (Factions::isMember($faction["abr"], $steamid)) { $faction = $faction["abr"]; break; };
                };

                // If they don't have access we'll give them access if they're in a faction...
                if (!(Accounts::IsUser($steamid)) && ($faction != null)) {
                    $entry = Factions::isMember($faction, $steamid);

                    if (!$entry) { self::OnFailedLogin("No User"); exit; } // What?
                    if (!(Accounts::createSteam($player->personaname, $steamid))) { self::OnFailedLogin("Creation Failed"); exit; } // If it fails to create then it'll become "No Access"...
                } else {
                    if (!(Accounts::IsUser($steamid))) { self::OnFailedLogin("No Access"); exit; }
                    if (!(Accounts::IsAdmin($steamid)) && ($faction == null)) { self::OnFailedLogin("No Access"); exit; }
                }

                $token = Accounts::setToken($steamid); // Get our remember token...
                if (!$token) { self::OnFailedLogin("Token Creation Fail..."); exit; }

                $steaminfo = array(
                    'steam-name' => $player->personaname,
                    'steam-pfp' => $player->avatar,
                    'steam-pfp-medium' => $player->avatarmedium,
                    'steam-pfp-full' => $player->avatarfull
                );

                Session::set("steaminfo", $steaminfo);
                Session::set("steamid", $steamid); // Set our steamid
                setcookie("steam_id", $steamid, time()+3600 * 24 * 365, "/"); // Set the cookie!
                setcookie("remember_token", $token, time()+3600 * 24 * 365, "/"); // Set the cookie 2!
                Accounts::updateSteam($steamid, $steaminfo);

                if($redirect) {
                    header("Location: ".URL);
                }
            }
        }
    }

    public static function getSteamInfo ($steamid) {
        $steamauth = require ROOT.'settings.php';
        $url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$steamauth['steam-key']."&steamids=".$steamid; 
        $json_object= file_get_contents($url);
        $json_decoded = json_decode($json_object);
        foreach ($json_decoded->response->players as $player) { return $player; }
    }


    private static function OnFailedLogin($reason = "") {
        Account::logout(false);
        Session::start();
        Session::set("reason", $reason);
        header("Location: ".URL."login");
    }

    public static function getSteamName ($steamid) {
        $steamauth = require ROOT.'settings.php';
        $url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$steamauth['steam-key']."&steamids=".$steamid; 
        $json_object= file_get_contents($url);
        $json_decoded = json_decode($json_object);
        foreach ($json_decoded->response->players as $player)
        {
            return $player->personaname;
        }
    }
}