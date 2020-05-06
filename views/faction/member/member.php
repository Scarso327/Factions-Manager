<div class = "container">
    <section class = "breadcrumbs buttons">
        <div>
            <?=Controller::buildCrumbs();?>
        </div>
        <div class = "info-box">
            <p><span title="Join Date"><?=date('d/m/Y', strtotime($this->member->joindate));?></span> / <span title="Last Rank Change"><?=date('d/m/Y', strtotime($this->member->last_rank_change));?></span> / <span title="Last Login Date"><?=date('d/m/Y H:i:s', strtotime($this->member->last_login));?></span></p>
        </div>
    </section>
    <section class = "overview">
        <div class = "steam-pfp-box status <?=Member::getActivity($this->member)?>">
            <img class = "steam-pfp" src="<?=$this->steam->avatarfull;?>" height="128" width="128">
        </div>
        <div>
            <br><br>
            <span><?=$this->member->name;?></span>
            <?php
            if ($this->member->isArchive != 1) {
                echo Application::getRanks(Faction::$var)[$this->member->mainlevel]->name;
                echo '<span class = "station">'.(Member::getCustomID(Faction::$var, $this->member)).'</span>';
            } else {
                echo "Archived Member";
            }
            ?>
        </div>
        <div class = "action">
            <?php
            if (SETTING["stats-url"] != "") {
                ?>
                <a href="<?=SETTING["stats-url"].$this->member->steamid;?>" target="_blank" style="background-color: #28a645;">Player Stats</a>
                <?php
            }
            ?>
            <a href="https://steamcommunity.com/profiles/<?=$this->member->steamid;?>" target="_blank" style="background-color: #333333;">Steam Profile</a>
            <?php
            if (SETTING["forums-url"] != "") {
                ?>
                 <a href="<?=SETTING["forums-url"].$this->member->forumid;?>-<?=$this->member->name;?>/" target="_blank" style="background-color: #007aff;">Forum Profile</a>
                <?php
            }
            ?>
        </div>
    </section>
        <section class = "body<?php if ($this->powers) { if (count($this->powers) <= 3) { ?> vertically<?php } }?>">
        <?php
        if ($this->powers) {
            ?>
            <div class = "actions">
                <?php
                foreach ($this->powers as $power) {
                    if (array_key_exists($power->form, View::$forms)) {
                        $form = View::$forms[$power->form];
                        ?>
                        <a <?php if ($form->modal == 1) { 
                            ?> 
                            onclick="showModal(this)"
                            <?php 
                        } else { 
                            ?> 
                            href="" 
                            <?php 
                        } ?> style="background-color: #<?=$power->colour;?>;" data-id='<?=$power->form;?>' data-name='<?=$form->name;?>'><?=$power->name;?></a>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        }
        ?>
        <div class = "main roster">
            <form autocomplete="off" method="GET" action="<?=URL.Faction::$var.'/'.$this->member->steamid;?>">
                <select name="type" id = "typeDropdown">
                    <option value="member">Forms</option>
                    <option value="actioner">Submitted Forms</option>
                </select>

                <div style = "display: flex;">
                    <input style = "margin-right: 5px;" type="date" id="start" name="start-time" value="<?=$this->history['dates'][0];?>" min="2016-01-01" max="<?=date('Y-m-d')?>">
                    <input type="date" id="start" name="end-time" value="<?=$this->history['dates'][1];?>" min="2016-01-01" max="<?=date('Y-m-d')?>">
                </div>

                <button style = "width: 100%; margin: 0px;" type="submit">Search History</button>
            </form>
            <?php
            if ($this->history['type'] != "Forms") {
                ?>
                <div id = "dropdownScript">
                    <script>
                        document.getElementById("typeDropdown").value = '<?=$this->history['type'];?>';
                        var target = document.getElementById('dropdownScript');
                        target.remove( target.childNodes[0] );
                    </script>
                </div>
                <?php
            }
            ?>
            <table id = "myFormsTable">
                <?php include("tables/history.php"); ?>
            </table>
        </div>
    </section>
    <?php include("histroyModal.php"); ?>
</div>