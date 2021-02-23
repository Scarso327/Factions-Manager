<?php

// Builds our pages
class View {

    public static $forms = array();
    public static $isError = false;

    public function __construct($files, $data = null) {
        $title = Controller::$currentPage;
        $this->css = null;
        $this->java = null;
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }

        echo "
        <html lang='en-GB'>
            <head>
                <title>".$title." - ".SETTING["site-name"]."</title>
                <meta name='Viewport' content='width=device-width, initial-scale=1'>
                <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js'></script>
                <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.8.1/css/all.css' integrity='sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf' crossorigin='anonymous'>
                <link rel='stylesheet' href='".URL."css/style.css?v=".time()."' type='text/css' />
                ";
                if($this->css != null) {
                    foreach ($this->css as $css) {
                        ?>
                        <link rel="stylesheet" href="<?php echo URL; ?>css/<?=$css;?>?v=<?=time();?>" type="text/css" />
                        <?php
                    }
                }

                echo "
                <link rel='stylesheet' href='".URL."css/dark-theme-override.css?v=".time()."' type='text/css' />
                <script src='".URL."js/app.js'></script>
                ";
                

                if($this->java != null) {
                    foreach ($this->java as $java) {
                        ?>
                        <script src='<?=$java;?>'></script>
                        <?php
                    }
                }
            echo "
            </head>
            <body id='main-body' "; if (Application::$isDark) { echo "class='dark'"; } echo ">
            ";
                if(is_array($files) && (count($files) > 0)) {
                    foreach($files as $filename) { 
                        if(file_exists($filename . '.php')) {
                            require $filename . '.php';
                        } else {
                            echo $filename;
                        }
                    }
                } else {
                    $filename = $files;
                    if(file_exists($filename . '.php')) {
                        require $filename . '.php';
                    } else {
                        echo $filename;
                    }
                }

                if (!self::$isError) {
                    // Modals...
                    $modals = array();

                    foreach (self::$forms as $form) {
                        if ($form->modal == 1) { array_push($modals, $form); }
                    }

                    if ($modals) {
                        ?>
                        <div id="modal" class="modal">
                            <div class="modal-body">
                                <div class="modal-header">
                                    <h3 id="modal-title-h3">Modal View</h3>
                                    <i class="fas fa-times-circle" id="modal-close"></i>
                                </div>
                                <div class="modal-content form">
                                    <?php
                                    foreach($modals as $modal) {
                                        $fields = Form::getFields($modal->id);

                                        if ($fields) {
                                            ?>
                                            <div id="<?=$modal->id;?>-modal" class="modal-section">
                                                <?=Forms::buildForm($modal, $fields);?>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <script>
                            var modal = document.getElementById("modal");
                            var modalTitle = document.getElementById("modal-title-h3");
                            var modalSections = document.getElementsByClassName("modal-section");
                            var info = document.getElementById("log-info");

                            document.getElementById("modal-close").onclick = function() { closeModal(); };

                            window.onclick = function(event) { 
                                if (event.target == modal) { 
                                    closeModal ();
                                }
                            }

                            function showModal(btn) {
                                var formID = btn.getAttribute("data-id");
                                var formName = btn.getAttribute("data-name");
                                var modalSection = document.getElementById(formID+"-modal");
                                
                                if (modalSection != null) {
                                    modalSection.style.display = "block";
                                }

                                modalTitle.innerHTML = formName;

                                modal.style.display = "block";
                            }

                            function closeModal () {
                                modal.style.display = "none";

                                for (var i = 0; i < modalSections.length; i++) {
                                    modalSections[i].style.display = "none";
                                }
                            }
                        </script>
                        <?php
                    }
                }
            echo "
            </body>
        </html>
        ";
    }

    // Called from the Navbar.php file to check if a button should be given the 'active' CSS tag.
    public static function ButtonActive($page) {
        if(Controller::$currentPage == $page || Controller::$subPage == $page) {
            return "active";
        }
    }

    public static function getLanguage($faction, $key) {
        $fullKey = $faction.$key;

        if (array_key_exists($fullKey, LAN_OVERRIDE)) {
            return LAN_OVERRIDE[$fullKey];
        }

        return LAN_OVERRIDE["default".$key];
    }

    public static function addForm($form) {
        self::$forms[$form->id] = $form;
    }
}
?>