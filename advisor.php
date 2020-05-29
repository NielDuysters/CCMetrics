<?php
    include_once('php/data.php');
    include_once('php/advisor.php')
?>

<!DOCTYPE html>
<html>
    <head>
        <?php
            // Including head-contents (files, scripts, metadata,...)
            echo file_get_contents("partials/head.html");
        ?>

        <style>
            #advisor-info {
                font-size: 18px;
            }
            #advisor-info span {font-weight: bold;}
            #advisor-info div {
                margin-top: 15px;
            }

            #add-menu { display: none; }
        </style>
    </head>
    <body>
        <div id="container">
            <div id="dashboard">
                <img src="media/images/logo.png" id="logo">
                <div id="left-nav">
                    <div id="opunit-select">
                        <span>Selected OP UNIT</span>
                        <span id="selected-opunit">
                            <?php
                                // Retrieving name of current opunit
                                foreach ($operational_units as $opunit) {
                                    if ($opunit->id == $_GET['opunit']) {
                                        echo $opunit->name;
                                        break;
                                    }
                                }

                            ?>
                        </span>

                        <div id="opunits">
                            <ul>
                                <?php
                                    // Inserting all Operational Units in top navigation bar
                                   foreach ($operational_units as $opunit) {
                                       echo "<li><a class='opunit' id='".$opunit->id."' href='?opunit=".$opunit->id."'>".$opunit->name."</a></li>";
                                   }
                                   ?>
                            </ul>
                        </div>
                    </div>

                    <?php
                        // Outputting al dashboard-options
                        echo str_replace("%opunit%", $_GET['opunit'], file_get_contents("partials/dashboard-options.html"));
                    ?>
                </div>

                <div id="content">
                    <h2><?php echo ucwords(strtolower($firstname)) . " " . ucwords(strtolower($lastname)) ?></h2>


                    <div id="advisor-info">
                        <?php
                            echo "<div><span>Mdw. Nummer:</span> ".$ID." </div>";
                            echo "<div><span>Division:</span> ".$division_name." </div>";
                            echo "<div><span>Employer:</span> ".$employer." </div>";
                            echo "<div><span>Startdate:</span> ".$startdate." </div>";
                            echo "<div><span>Customer:</span> Luminus</div>";
                            echo "<div><span>Function-description:</span> ".$functiondescription." </div>";
                            echo "<div><span>Manager:</span> <a href='advisor.php?opunit=".$_GET['opunit']."&id=".$manager_id."'>".ucwords(strtolower($manager_firstname)) . " " . ucwords(strtolower($manager_lastname)) ."</a> </div>";

                            echo "<div><span>CRM's:</span> ";
                            foreach ($crms as $crm) {
                                 echo "<br><b>" . $crm[0] . ":</b> " . $crm[1];
                             }
                            echo "</div>";

                            echo "
                            <div id='crm-add-container'>
                                <button id='open-add-menu'>Voeg CRM's toe</button>

                                <form action='php/advisor.php' method='POST' id='add-menu'>
                                    <div id='item-container'>
                                        <input type='text' name='advisor_id' value='".$ID."' style='visibility:hidden;'>
                                        <input type='text' name='opunit' value='".$_GET['opunit']."' style='visibility:hidden;'>
                                        <div>
                                            <label>CRM Name</label><input type='text' name='crm_name[]' value=''>
                                            <label>CRM Login</label><input type='text' name='crm_login[]' value=''> <button class='add'>+</button>
                                        </div>
                                    </div>

                                    <input type='submit' name='btnSave' value='save'>
                                </form>
                            </div>";
                        ?>
                    </div>
                </div>

            </div>
        </div>

        <script>

            document.getElementsByClassName('add')[0].addEventListener("click", add_inputs);
            document.getElementById('open-add-menu').addEventListener("click", open_menu);


            function open_menu() {
                document.getElementById("add-menu").style.display = "block";
            }

            function add_inputs() {
                let item_container = document.getElementById("item-container");

                item_container.innerHTML += `
                <div>
                    <label>CRM Name</label><input type='text' name='crm_name[]' value=''>
                    <label>CRM Login</label><input type='text' name='crm_login[]' value=''> <button class='add'>+</button>
                </div>
                `;

                let add_btns = document.getElementsByClassName('add');
                for (i = 0; i < add_btns.length; i++) {
                    add_btns[i].addEventListener("click", add_inputs);
                }
            }



        </script>
    </body>
</html>
