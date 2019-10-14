<nav class = "navbar">
    <nav class = "top">
        <div class = "container">
            <ul class = "buttons">
                <?php
                $subButtons = array ();
                $isFactionPage = false;
                $steamid = Account::$steamid;

                ?>
                <li <?php if (View::ButtonActive("Dashboard")) { echo 'class = "active"'; } ?>>
                    <a href="<?=URL?>">
                        Dashboard
                    </a>
                </li>
                <?php
                array_push($subButtons, array('','Home'));
                
                foreach (Application::$factions as $faction) {
                    if (Factions::isMember($faction["abr"], $steamid)) {
                        ?>
                        <li <?php if (View::ButtonActive(strtoupper($faction["abr"]))) { echo 'class = "active"'; $isFactionPage = true; $subButtons = (System::getSubpages($faction["abr"])); } ?>>
                            <a href="<?=URL.$faction["abr"]?>/">
                                <?=strtoupper($faction["abr"]);?>
                            </a>
                        </li>
                        <?php
                    };

                    if (!$isFactionPage) {
                        array_push($subButtons, array("home/public/".$faction["abr"],strtoupper($faction["abr"])." Roster"));
                    }
                };
                
                $adminLevel = Accounts::IsAdmin($steamid);
                if ($adminLevel) {
                    ?>
                    <li <?php 
                        if (View::ButtonActive("Admin")) { 
                            echo 'class = "active"'; 

                            $subButtons = array(array('admin/','Home'), array('admin/factions/','Factions'));
                        } ?>>
                        <a href="<?=URL."admin/"?>">
                            Admin
                        </a>
                    </li>
                    <?php
                }
                ?>
                <div class = "profile">
                    <?php
                    if (Account::isLoggedIn()) {
                        ?>
                            <li>
                                <?php
                                $url = URL;

                                if (class_exists("Faction")) {
                                    $url = URL.Faction::$var;
                                } else {
                                    foreach (Application::$factions as $faction) {
                                        if (Factions::isMember($faction["abr"], $steamid)) { $url = URL.$faction["abr"]; break; };
                                    };
                                }

                                $url = $url."/".$steamid;

                                ?>
                                <a href="<?=$url?>">
                                    <img class = "steam-pfp" src="<?=Session::get("steaminfo")["steam-pfp"];?>" alt="PFP" height="32" width="32">
                                    <span><?=Session::get("steaminfo")["steam-name"];?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?=URL.'?logout'?>">
                                    <span class="fas fa-sign-out-alt"></span> 
                                </a>
                            </li>
                        <?php
                    } else {
                        ?>
                        <li <?php if (View::ButtonActive("Login")) {  echo 'class = "active"'; } ?>>
                            <a href="<?=URL.'login';?>">Login <span class="fas fa-sign-in-alt"></span></a>
                        </li>
                        <?php
                    }
                    ?>
                </div>
            </ul>
        </div>
    </nav>
    <nav class = "bottom">
        <div class = "container">
            <ul class = "buttons">
                <?php
                foreach ($subButtons as $button) {
                    ?>
                    <li <?php if (View::ButtonActive($button[1])) { echo 'class = "active"'; } ?>>
                        <a href="<?=URL.$button[0]?>">
                            <?=$button[1]?>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </nav>
</nav>