<?php
    include_once('../php/data.php');
    include_once('../php/hrafspraken/index.php');
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
            td.editable:nth-child(odd) {
                border-right: solid 1px #a4a4a4a4;
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
                    <h2>HR Afspraken - Jouw Advisors</h2>

                    <!--
                    <div class="select" style="width: 275px;">
                        <input type="hidden" class="value" value="" name="month">
                        <span class="name">Quarter</span>
                        <div class="selected">
                            <span>Select quarter</span>
                            <div class="arrow arrow-down"></div>
                        </div>
                        <div class="options">
                            <div class="option" data-value="">&nbsp;</div>
                            <div class="option" data-value="1">Q1</div>
                            <div class="option" data-value="1">Q2</div>
                            <div class="option" data-value="1">Q3</div>
                            <div class="option" data-value="1">Q4</div>
                        </div>
                    </div>
                -->


                    <!-- Table of all advisors of this manager -->
                    <table id="advisors" data-manager-id="<?php echo $_SESSION['auth_user'] ?>">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Buckets</th>
                            </tr>
                        </thead>
                        <tbody id="data"></tbody>
                    </table>

                </div>
            </div>
        </div>

        <script>
            function update_table() {
                let opunit = get_op_unit();
                let manager = document.getElementById("advisors").getAttribute("data-manager-id");

                let url = "php/hrafspraken/advisors.php?opunit=" + opunit + "&manager=" + manager;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    success: function(response) {
                        document.getElementById("data").innerHTML = response;
                    }
                });
            }

            $(document).ready(function() {
                update_table();
            });
        </script>
    </body>
</html>
