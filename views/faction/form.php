<div class = "container">
    <section class = "breadcrumbs">
        <div>
            <?=Controller::buildCrumbs();?>
        </div>
    </section>
    <section class = "form">
        <?=Forms::buildForm($this->form, $this->fields);?>
    </section>
</div>