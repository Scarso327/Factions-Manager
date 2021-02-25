<div class = "container">
    <section class = "breadcrumbs">
        <?=Controller::buildCrumbs();?>
    </section>
    <section class = "roster">
        <?php
        $faction = Faction::$var;
        $members = $this->unit_members;
        ?>
        <table id = "roster">
            <tr class = "first">
                <th COLSPAN = 7 >Details</th>
                <th COLSPAN = 2 >Miscellaneous</th>
            </tr>
            <tr class = "second">   
                <!--- Details --->
                <th>ID #</th>
                <th>Name</th>
                <th>Rank</th>
                <th>Steam ID</th>
                <th>Unit Rank</th>
                <th>Unit Join Date</th>
                <th title="Date of Last Promotion / Demotion">Unit Rank Date</th>
                <th COLSPAN = 2></th>
            </tr>
            <?php
            if ($members) {
                usort($members, function ($member1, $member2) use ($faction) {
                    return Units::orderMembers($faction, $member1, $member2);
                });

                foreach ($members as $member) {
                    if (!Units::canDoUnit($member->rank, $faction, "unit_hide_roster")) {
                        ?>
                        <tr id = "<?=$member->steamid;?>">
                            <td><?=Member::getCustomID($faction, $member)?></td>
                            <td><?=$member->name;?></td>
                            <td><?=Application::getRanks($faction)[$member->rank]->sName?></td>
                            <td><?=$member->steamid;?></td>
                            <td><?=$this->unit_ranks[$member->unit_rank]->name;?></td>
                            <td><?=date("d/m/Y", strtotime($member->joindate))?></td>
                            <td><?=date("d/m/Y", strtotime($member->rankdate))?></td>
                            <td class = "manage"><a href="<?=URL.($faction)."/".$member->steamid."/units";?>">Manage</a></td>
                            <td class = "manage"><a href="">Remove</a></td>
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
    </section>
</div>