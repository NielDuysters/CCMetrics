<?php
/*
    Author: Niel Duysters
    email: contact@ndvibes.com
*/

    include_once('php/data.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <?php
            // Including head-contents (files, scripts, metadata,...)
            echo file_get_contents("partials/head.html");
        ?>

        <style>

        </style>
    </head>
    <body>
        <div id="container">
            <div id="dashboard">
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
                    <h2>Organization</h2>

                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Firstname</th>
                                <th>Lastname</th>
                            </tr>
                        </thead>
                        <tbody id="data">
                            <tr>
                                <td>1</td>
                                <td>An</td>
                                <td>Vernamen</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Jan</td>
                                <td>Mijnennaam</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Piet</td>
                                <td>Achternaams</td>
                            </tr>

                            <tr>
                                <td>4</td>
                                <td>Joris</td>
                                <td>Benoemdens</td>
                            </tr>

                            <tr>
                                <td>5</td>
                                <td>Corneel</td>
                                <td>Bekends</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </body>
</html>
