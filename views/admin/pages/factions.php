<div class = "container">
    <section class = "breadcrumbs buttons">
        <div>
            <?=Controller::buildCrumbs();?>
        </div>
        <div class = "info-box">
            <p><span>Total Factions: <?=count(Application::$factions);?></span></p>
        </div>
    </section>
</div>