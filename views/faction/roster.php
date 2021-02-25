<?php $faction = $this->faction; ?>
<div class = "container">
    <section class = "breadcrumbs buttons">
        <div>
            <?=Controller::buildCrumbs();?>
            <?php
            if ($this->archive) {
                ?>
                <span class="slash">/</span>
                <a href="<?=URL.$faction."/archive";?>">Archive</a>
                <?php
            } else {
                ?>
                <span class="slash">/</span>
                <select id = "constabList" onchange="FilterTable()">
                    <option value="all">All Members</option>
                    <?php
                    foreach (Application::getSections($faction) as $section) {
                        if ($section->system == 0) {
                            echo '<option value="'.$section->name.'">'.$section->name.'</option>';
                        }
                    }
                    ?>
                </select>
                <?php
                }
            ?>
        </div>
        <div class = "button-list">
            <?php
            if (!$this->archive && !$this->public) {
                echo '<a staff-toggle class="staff-toggle button">'.((isset($_COOKIE["show-staff"])) ? "Hide" : "Show").' Staff</a>';
                $form = Form::getForm($faction, (Application::$factions[$faction]["addFormID"]));

                if ($form && (Form::canSubmitForm($form->id))) {
                    ?>
                    <a class = "button" href="<?=URL.$faction?>/form/<?=$form->id;?>-<?=$form->name;?>">Add Member</a>
                    <?php
                }
            }
            ?>
        </div>
    </section>
    <section class = "search">
        <input type="text" id = "searchRoster" onkeyup="FilterTable()" placeholder="Search (Name, Steam ID, Forum ID)">
    </section>
    <section class = "roster">
        <?php $members = $this->members; ?>
        <table id = "roster">
            <tr class = "first">
                <th COLSPAN = 7 >Details</th>
                <?php
                if (!$this->public || SETTING["forums-url"] != "") {
                    ?>
                    <th COLSPAN = 2 >Miscellaneous</th>
                    <?php
                }
                ?>
            </tr>
            <tr class = "second">   
                <!--- Details --->
                <th>ID #</th>
                <th>Name</th>
                <th>Rank</th>
                <th>Steam ID</th>
                <th>Section</th>
                <th>Status</th>
                <th>Join Date</th>
                <th title="Date of Last Promotion / Demotion">Rank Date</th>
                <?php
                if (!$this->public || SETTING["forums-url"] != "") {
                    ?>
                    <th COLSPAN = 2></th>
                    <?php
                }
                ?>
            </tr>
            <?php
            if ($members) {
                usort($members, function ($member1, $member2) use ($faction) {
                    return Factions::orderMembers($faction, $member1, $member2);
                });

                foreach ($members as $member) {
                    if (($member->section == "Staff" && (!$this->archive && !$this->public)) || $member->section != "Staff") {
                        $activity = Member::getActivity($member);

                        $page = "";
                        if ($this->archive) { $page = "archive/"; }

                        ?>
                        <tr id = "<?=$member->section;?>" <?=(($member->section == "Staff") ? 'hidden-staff' : '');?> <?=((!isset($_COOKIE['show-staff']) && $member->section == "Staff") ? 'class="hide"' : '');?>>
                            <td><?=Member::getCustomID($faction, $member)?></td>
                            <td><?=$member->name;?></td>
                            <td><?=Application::getRanks($faction)[$member->mainlevel]->sName?></td>
                            <td><?=$member->steamid;?></td>
                            <td><?=$member->section;?></td>
                            <td class = "status <?=$activity?>" title="<?=date('d/m/Y H:i:s', strtotime($member->last_login));?>"><?=$activity?></td>
                            <td><?=date("d/m/Y", strtotime($member->joindate))?></td>
                            <td><?=date("d/m/Y", strtotime($member->last_rank_change))?></td>
                            <?php
                            if (SETTING["stats-url"] != "") {
                                ?>
                                <td class = "stats"><a href="<?=SETTING["stats-url"].$member->steamid?>" target="_blank">Stats</a></td>
                                <?php
                            }
                            ?>
                            <?php
                            if ($this->public) {
                                if (SETTING["forums-url"] != "") {
                                    ?>
                                    <td class = "manage" style="background-color: #007aff;"><a href="<?=SETTING["forums-url"].$member->forumid;?>-<?=$member->name;?>/" target="_blank">Forum</a></td>
                                    <?php
                                }
                            } else {
                                ?>
                                <td class = "manage"><a href="<?=URL.($faction)."/".$page.$member->steamid?>">Manage</a></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <?php
                    }
                }
            } else {
                ?>
                <tr>
                    <td COLSPAN = 9>No Members</td>
                </tr>
                <?php
            }
            ?>
        </table>
        <script>
            function FilterTable() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("searchRoster");
                search = input.value.toUpperCase();
                table = document.getElementById("roster");
                tr = table.getElementsByTagName("tr");

                for (i = 0; i < tr.length; i++) {
                    var id = tr[i].id;
                    var curConstab = document.getElementById("constabList").value;
                    td = tr[i].getElementsByTagName("td")[0];

                    if (td) {
                        if (id != curConstab && curConstab != "all") {
                            tr[i].style.display = "none";
                        } else {
                            var forumid = tr[i].getElementsByTagName("td")[0].textContent.toUpperCase().indexOf(search) > -1;
                            var name = tr[i].getElementsByTagName("td")[1].textContent.toUpperCase().indexOf(search) > -1;
                            var steamid = tr[i].getElementsByTagName("td")[3].textContent.toUpperCase().indexOf(search) > -1;
                            if (forumid || name || steamid) {
                                tr[i].style.display = "";
                            } else {
                                tr[i].style.display = "none";
                            }
                        }
                    } 
                }
            }
            
            FilterTable();
        </script>
    </section>
</div>