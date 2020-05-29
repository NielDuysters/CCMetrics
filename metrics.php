<?php
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
                    <h2>Metrics</h2>

                    <!-- Select for customers -->
                    <!--<select name="customer">
                        <option value="">Select customer</option>

                        <?php
                            foreach ($customers_opunit as $customer) {
                                echo "<option value='" . $customer->id . "'>" . $customer->name . "</option>";
                            }
                        ?>
                    </select>-->
                    <div class="select">
                        <input type="hidden" class="value" value="" name="customer">
                        <span class="name">Customer</span>
                        <div class="selected">
                            <span>Select Customer</span>
                            <div class="arrow arrow-down"></div>
                        </div>
                        <div class="options">
                            <div class="option" data-value="">&nbsp;</div>
                            <?php
                                foreach ($customers_opunit as $customer) {
                                    echo "<div class='option' data-value='" . $customer->id . "'>" . $customer->name . "</div>";
                                }
                            ?>
                        </div>
                    </div>

                    <!-- Select for competences -->
                    <!--<select name="competence">
                        <option value="">Select competence</option>

                        <?php
                            foreach ($competences as $competence) {
                                if ($competence->competence != "" && (strtoupper($competence->competence) == "FUNCTIESPECIFIEKE COMPETENTIE" || strtoupper($competence->competence) == "GENERIEKE COMPETENTIE")) {
                                    echo "<option value='" . $competence->id . "'>" . $competence->competence . "</option>";
                                }
                            }
                        ?>
                    </select>-->
                    <div class="select" style="width: 275px;">
                        <input type="hidden" class="value" value="" name="competence">
                        <span class="name">Competence</span>
                        <div class="selected">
                            <span>Select competence</span>
                            <div class="arrow arrow-down"></div>
                        </div>
                        <div class="options">
                            <div class="option" data-value="">&nbsp;</div>
                            <?php
                            foreach ($competences as $competence) {
                                if ($competence->competence != "" && (strtoupper($competence->competence) == "FUNCTIESPECIFIEKE COMPETENTIE" || strtoupper($competence->competence) == "GENERIEKE COMPETENTIE")) {
                                    echo "<div class='option' data-value='" . $competence->id . "'>" . $competence->competence . "</div>";
                                }
                            }
                            ?>
                        </div>
                    </div>


                    <!-- Table of all definitions matching criteria/filters -->
                    <table id="metrics">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Metrics</th>
                                <th>Displayname</th>
                                <th>Customer</th>
                                <th>Details</th>
                            </tr>

                        </thead>
                        <tbody id="data"></tbody>
                    </table>
                </div>

            </div>
        </div>

        <script>
            /*let select_elements = document.getElementsByTagName("select");
            for (i = 0; i < select_elements.length; i++) {
                select_elements[i].addEventListener("change", update_table);
            }*/

            $(".select .value").on('change', function() {
                update_table();
            });

            function update_table() {
                let opunit = get_op_unit();
                let customer = document.getElementsByName("customer")[0].value;
                let competence = document.getElementsByName("competence")[0].value;


                let url = "php/metrics.php?opunit=" + opunit + "&customer=" + customer + "&competence=" + competence;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    success: function(response) {
                        document.getElementById("data").innerHTML = response;
                    }
                });
            }

            $(document).ready(function() {update_table()});

        </script>
    </body>
</html>
