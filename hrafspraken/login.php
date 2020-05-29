<?php
    include_once('../php/data.php');
    include_once('../php/hrafspraken/login.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <base href="../">

        <?php
            // Including head-contents (files, scripts, metadata,...)
            echo file_get_contents("../partials/head.html");
        ?>

        <style>
            form {
                width: 100%;
                max-width: 350px;
            }
            form input {
                display: block;
            }
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
                        echo str_replace("%opunit%", $_GET['opunit'], file_get_contents("../partials/dashboard-options.html"));
                    ?>
                </div>

                <div id="content">
                    <h2>Login</h2>

                    <form method="post">
                        <input type="text" name="user" placeholder="Username" required>
                        <input type="password" name="pass" placeholder="Password" required>
                        <input type="submit" name="btnLogin" value="Login">
                    </form>

                </div>
            </div>
        </div>

        <script>

        </script>
    </body>
</html>
