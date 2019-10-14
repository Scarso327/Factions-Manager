<tr class = "first">
    <th COLSPAN = 4>Details</th>
    <th>Miscellaneous</th>
</tr>
<tr class = "second">   
    <!--- Details --->
    <th>Date</th>
    <th><?=View::getLanguage($this->history['type'], "-member-log-title");?> <?=View::getLanguage(Faction::$var, "-member-title");?></th>
    <th>Action</th>
    <th>Status</th>
    <th></th>
</tr>
<?php
if ($this->history["logs"]) {
    foreach($this->history["logs"] as $log) {
        ?>
        <tr id = "log-<?=$log->id;?>">
            <td><?=date("d/m/Y", strtotime($log->timestamp))?></td>
            <td><?php 
                $type = "actioner";
                if ($this->history['type'] == "actioner") { $type = "member"; };

                if ($log->$type == "SYSTEM") {
                    echo "SYSTEM";
                } else {
                    $actioner = Factions::getMember(Faction::$var, $log->$type);
                    if ($actioner) {
                        echo '<a href="'.URL.Faction::$var.'/'.$actioner->steamid.'">'.$actioner->name.'</a>';
                    } else {
                        echo (Steam::getSteamName($log->$type));
                    }
                }
            ?></td>
            <?php
            foreach(array($log->action, $log->status) as $row) {
                ?> <td class="formType <?=str_replace(" ", "", $row);?>"><?=$row;?></td> <?php
            }
            ?>
            <td class = "manage"><button onclick="showHModal(this)" data-id='<?=$log->id;?>'>View</button></td>
        </tr>
        <?php
    }
} else {
    ?>
    <tr>
        <td COLSPAN = 5>No History</td>
    </tr>
    <?php
}
?>