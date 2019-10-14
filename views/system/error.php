<?php
/**
 * Created by PhpStorm.
 * User: ScarsoLP
 * Date: 15/03/2018
 * Time: 10:54 PM
 */
?>
<div class = "container">
    <section class = "breadcrumbs">
        <?=Controller::buildCrumbs();?>
    </section>
    <h1><?=$this->myError;?></h1>
    <h2><?=$this->error_title;?></h2><br>
    <?=$this->error_message;?>
</div>