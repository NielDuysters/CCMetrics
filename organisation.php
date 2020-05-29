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
            table {
                margin-top: 35px;
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
                        echo str_replace("%opunit%", $_GET['opunit'], file_get_contents("partials/dashboard-options.html"));
                    ?>
                </div>

                <div id="content">
                    <h2>Organisation</h2>

                    <!-- Select for division -->
                    <!--<select name="division">
                        <option value="">Select division</option>
                        <?php
                            foreach ($divisions as $division) {
                                echo "<option value='".$division->id."'>".$division->name."</option>";
                            }
                        ?>
                    </select>-->
                    <div class="select">
                        <input type="hidden" class="value" value="" name="division">
                        <span class="name">Division</span>
                        <div class="selected">
                            <span>Select division</span>
                            <div class="arrow arrow-down"></div>
                        </div>
                        <div class="options">
                            <div class="option" data-value="">&nbsp;</div>
                            <?php
                                foreach ($divisions as $division) {
                                    echo "<div class='option' data-value='".$division->id."'>".$division->name."</div>";
                                }
                            ?>
                        </div>
                    </div>

                    <!-- Select for customers -->
                    <!--
                    <select name="customer" disabled>
                        <option value="">Select customer</option>
                    </select>
                    -->
                    <div class="select">
                        <input type="hidden" class="value" value="" name="customer">
                        <span class="name">Customer</span>
                        <div class="selected">
                            <span>Select Customer</span>
                            <div class="arrow arrow-down"></div>
                        </div>
                        <div class="options" id="customer-options">
                        </div>
                    </div>

                    <!-- Select for month-->
                    <!--<select name="month">
                        <option value=''>Select month</option>
                        <option value='4'>April</option>
                    </select>-->
                    <div class="select">
                        <input type="hidden" class="value" value="" name="month">
                        <span class="name">Month</span>
                        <div class="selected">
                            <span>Select Month</span>
                            <div class="arrow arrow-down"></div>
                        </div>
                        <div class="options">
                            <div class="option" data-value='1'>January</div>
                            <div class="option" data-value='2'>February</div>
                            <div class="option" data-value='3'>March</div>
                        </div>
                    </div>


                    <!-- Table of all advisors matching criteria/filters -->
                    <table id="advisors">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Manager</th>
                                <th>CRM_Links</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="data"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            //let select_elements = document.querySelector(".select .value");
            /*for (i = 0; i < select_elements.length; i++) {
                select_elements[i].addEventListener("change", update_table);
            }*/

            $(".select .value").on('change', function() {
                update_table();
            });

            //document.getElementsByName("division")[0].addEventListener("change", function() {
                $("input[name='division']").on('change', function() {

                if (document.getElementsByName("division")[0].value == "") {
                    //document.getElementsByName("customer")[0].disabled = true;
                    return;
                } else {
                    //document.getElementsByName("customer")[0].disabled = false;
                    update_customers();
                }
            });

            function update_table() {
                let opunit = get_op_unit();
                let division = document.getElementsByName("division")[0].value;
                let customer = document.getElementsByName("customer")[0].value;

                let url = "php/organisation.php?opunit=" + opunit + "&division=" + division;
                if (customer != "") {
                    url += "&customer=" + customer;
                }

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    success: function(response) {
                        document.getElementById("data").innerHTML = response;
                    }
                });
            }

            function update_customers() {
                let division = document.getElementsByName("division")[0].value;
                let url = "php/data.php?division=" + division + "&echo=customers_select_list";

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    success: function(response) {
                        let str = "<div class='option' data-value=''>&nbsp;</div>";
                        str += response;

                        document.getElementById("customer-options").innerHTML = str;
                        set_option_update_effects(document.getElementById("customer-options"));
                    }
                });
            }

            $(document).ready(function() {update_table();});
        </script>
    </body>
</html>
