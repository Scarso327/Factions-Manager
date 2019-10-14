<section class = "login">
    <div class = "loginBox">
        <div class = "header">
            <h2>Login</h2>
        </div>
        <div class = "body">
            <form method="GET" action="<?=URL?>login/">
                <?php
                if (Account::isLoggedIn()) {
                    ?>
                    <div class = "notify">Already Logged In</div>
                    <?php
                } else {
                    ?>
                    <input type="hidden" name="_action" value="login">

                    <?php
                    if (isset($this->reason)) {
                        ?>
                        <div class = "notify">Login Failed <?php if($this->reason != "") {echo "with reason: ".$this->reason; }?></div>
                        <?php
                    }
                    ?>

                    <button type="submit">
                        <!--- <span class="fas fa-steam"></span>  Disabled Because it didn't work... --->
                        <span>Login with Steam</span>              
                    </button>
                    <?php
                }
                ?>
            </form>
        </div>
    </div>
</section>