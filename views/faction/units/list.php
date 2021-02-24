<div class = "container">
    <section class = "breadcrumbs">
        <?=Controller::buildCrumbs();?>
    </section>
</div>
<div class = "container unit-container">
    <section class = "unit-cards">
        <?php
        foreach ($this->units as $unit) {
            ?>
            <a href="<?=URL.(Faction::$var)."/units/".$unit->id;?>" class="unit-card">
                <img src="<?=URL."img/units/".$unit->sName;?>.png"/>
                <h3><?=$unit->name;?></h3>
            </a>
            <?php
        }
        ?>
    </section>
</div>