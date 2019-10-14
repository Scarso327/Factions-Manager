<div class = "container">
    <section class = "breadcrumbs">
        <?=Controller::buildCrumbs();?>
    </section>
    <section>
        <div class = "roster">
            <form autocomplete="off" method="GET" action="<?=URL.Faction::$var.'/search/';?>">
                <input type="input" name = "steamid" minlength = "17" maxlength = "17" placeholder="Steam ID..." value = "<?=$this->steamid;?>" required>

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
                <?php include("member/tables/history.php"); ?>
            </table>
        </div>
    </section>
    <?php include("member/histroyModal.php"); ?>
</div>