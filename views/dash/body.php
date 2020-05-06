<div class = "container">
    <section class = "breadcrumbs buttons">
        <div>
            <?=Controller::buildCrumbs();?>
        </div>
        <div class = "button-list">
            Dark Theme
            <a theme-toggle class = "theme-toggle<?php if (Application::$isDark) { echo " active"; }?>"><span <?php if (Application::$isDark) { echo "class=\"fas fa-times-circle\";"; } else { echo "class=\"fas fa-check-circle\";"; } ?>></span></a>
        </div>
    </section>
</div>