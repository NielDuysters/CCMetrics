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
                    <h2>HR Afspraken - Buckets voor <span id="advisor_name"></span> </h2>

                    <div class="select" style="width: 275px;">
                        <input type="hidden" class="value" value="" name="quarter">
                        <span class="name">Quarter</span>
                        <div class="selected">
                            <span>Select quarter</span>
                            <div class="arrow arrow-down"></div>
                        </div>
                        <div class="options">
                            <div class="option" data-value="0">&nbsp;</div>
                            <div class="option" data-value="1">Q1</div>
                            <div class="option" data-value="2">Q2</div>
                            <div class="option" data-value="3">Q3</div>
                            <div class="option" data-value="4">Q4</div>
                        </div>
                    </div>



                    <!-- buckets for this advisor -->
                    <table id="buckets" data-advisor-id="<?php echo $_GET['advisor'] ?>">
                        <thead>
                            <tr>
                                <th>HR Agreement</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody id="data"></tbody>
                    </table>

                </div>
            </div>
        </div>

        <script>
            $(".select .value").on('change', function() {
                update_table();
            });


            function get_name() {
                let opunit = get_op_unit();
                let advisor = document.getElementById("buckets").getAttribute("data-advisor-id");

                let url = "php/hrafspraken/buckets.php?get_name=true&opunit=" + opunit + "&advisor=" + advisor;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    success: function(response) {
                        document.getElementById("advisor_name").innerHTML = response;
                    }
                });

            }

            function update_table() {
                let opunit = get_op_unit();
                let quarter = document.getElementsByName("quarter")[0].value;
                let advisor = document.getElementById("buckets").getAttribute("data-advisor-id");

                let url = "php/hrafspraken/buckets.php?opunit=" + opunit + "&quarter=" + quarter + "&advisor=" + advisor;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    success: function(response) {
                        document.getElementById("data").innerHTML = response;

                        let editable_cells = document.getElementsByClassName("editable");
                        for (i = 0; i < editable_cells.length; i++) {
                            editable_cells[i].addEventListener('input', save_data);
                        }
                    }
                });
            }

            function save_data(e) {
                let opunit = get_op_unit();
                let dd = e.target.parentElement.getAttribute("data-dd-id");
                let advisor = document.getElementById("buckets").getAttribute("data-advisor-id");
                let quarter = document.getElementsByName("quarter")[0].value;
                let score = e.target.innerHTML;

                let allowed_scores = [1,3,5];
                let valid = false;
                for (i = 0; i < allowed_scores.length; i++) {
                    if (score == allowed_scores[i]) {
                        valid = true;
                    }
                }

                if (!valid && score != "<br>") {
                    e.target.style.backgroundColor = "#FF0000";
                    return;
                } else {
                    e.target.style.backgroundColor = "transparent";
                }

                let url = "php/hrafspraken/buckets.php?save=true&opunit=" + opunit + "&quarter=" + quarter + "&dd=" + dd + "&advisor=" + advisor + "&score=" + score;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    success: function(response) {

                    }
                });

            }



            $(document).ready(function() {
                get_name();
                update_table();
            });
        </script>
    </body>
</html>
