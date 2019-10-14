<div class = "container stats">
    <section class = "breadcrumbs" <?php if (!Application::$isDark) { ?>style = "border-bottom: 1px solid #ddd; margin-bottom: 10px;"<?php } ?>>
        <?=Controller::buildCrumbs();?>
    </section>
    <section class = "general-stats roster">
        <table class = "stats-table">
            <tr class = "first">   
                <th COLSPAN=2><?=View::getLanguage(Faction::$var, "-stats-section-title");?></th>
            </tr>
            <?php
            foreach ($this->sections as $section) {
                if ($section->system == 0) {
                    echo '
                    <tr>
                        <td>'.$section->name.'</td>
                        <td>'.count(Factions::getFactionMembersBySection(Faction::$var, $section->name)).'</td>
                    </tr>
                    ';
                }
            }
            ?>
        </table>
        <?php
        $activeMembers = count(Factions::getActiveFactionMembers(Faction::$var));
        $totalMembers = count(Factions::getFactionMembers(Faction::$var));
        ?>
        <table class = "stats-table">
            <tr class = "first">   
                <th COLSPAN=2>Activity</th>
            </tr>
            <tr>
                <td>Active Members</td>
                <td><?=$activeMembers;?></td>
            </tr>
            <tr>
                <td>Inactive Members</td>
                <td><?=$totalMembers - $activeMembers;?></td>
            </tr>
            <tr>
                <td>Total Members</td>
                <td><?=$totalMembers;?></td>
            </tr>
        </table>
    </section>
</div>