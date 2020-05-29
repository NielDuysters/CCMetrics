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
                    <h2>Competences</h2>

                    <!-- Select for opunit -->
                    <!--<select name="opunit" disabled>
                        <?php

                            $set_option = "";

                            foreach ($operational_units as $opunit) {
                                if ($opunit->id == $_GET['opunit']) {
                                    $set_option = "selected='selected'";
                                } else {
                                    $set_option = "";
                                }

                                echo "<option value='".$opunit->id."' ".$set_option.">$opunit->name</option>";
                            }
                        ?>
                    </select>-->
                    <div class="select disabled active">
                        <input type="hidden" class="value" value="<?php echo $opunit->id; ?>" name="opunit">
                        <span class="name">OP UNIT</span>
                        <div class="selected">
                            <span><?php echo $opunit->name; ?></span>
                            <div class="arrow arrow-down"></div>
                        </div>
                        <div class="options">
                            <div class="option" data-value="">&nbsp;</div>
                            <?php
                                foreach ($operational_units as $opunit) {
                                    if ($opunit->id == $_GET['opunit']) {
                                        echo "<div class='option' data-value='".$opunit->id."'>".$opunit->name."</div>";
                                    }
                                }
                            ?>
                        </div>
                    </div>

                    <!-- Select for competences -->
                    <!--<select name="competence">
                        <option value="">Select competence</option>

                        <?php
                            foreach ($competences as $competence) {
                                if ($competence->competence != "") {
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
                                    if ($competence->competence != "") {
                                        echo "<div class='option' data-value='".$competence->id."'>".$competence->competence."</div>";
                                    }
                                }
                            ?>
                        </div>
                    </div>

                    <!-- Select for roles -->
                    <!--<select name="role">
                        <option value="">Select role</option>

                        <?php
                            foreach ($roles as $role) {
                                if ($role->name != "") {
                                    echo "<option value='" . $role->id . "'>" . $role->name . "</option>";
                                }
                            }
                        ?>
                    </select>-->
                    <div class="select" style="width: 275px;">
                        <input type="hidden" class="value" value="" name="role">
                        <span class="name">Role</span>
                        <div class="selected">
                            <span>Select role</span>
                            <div class="arrow arrow-down"></div>
                        </div>
                        <div class="options">
                            <div class="option" data-value="">&nbsp;</div>
                            <?php
                                foreach ($roles as $role) {
                                    if ($role->name != "") {
                                        echo "<div class='option' data-value='".$role->id."'>".$role->name."</div>";
                                    }
                                }
                            ?>
                        </div>
                    </div>

                    <!-- Table of all definitions matching criteria/filters -->
                    <table id="definitions">
                        <thead>
                            <tr>
                                <th>Competences</th>
                                <th>Metrics</th>
                                <th>HR Competence</th>
                                <th>Values</th>
                                <!--<th id="th_dynamic"></th>-->
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

            function update_table() {
                let opunit = get_op_unit();
                let competence = document.getElementsByName("competence")[0].value;
                let role = document.getElementsByName("role")[0].value;


                let url = "php/competences.php?opunit=" + opunit + "&competence=" + competence + "&role=" + role;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    success: function(response) {
                        document.getElementById("data").innerHTML = response.replace(/ *\{[^)]*\} */g, "");

                        let col = response.match(/\{(.*)\}/i)[1];
                        if (col != "" && col != null) {
                            if (col == ",hrcomp.value") {
                                document.getElementById("th_dynamic").innerHTML = "HR Competence";
                            } else if (col == ",compv.value") {
                                document.getElementById("th_dynamic").innerHTML = "Values";
                            }
                        } else {
                            document.getElementById("th_dynamic").innerHTML = "HR Competence / Values";
                        }
                    }
                });
            }



            $(document).ready(function() {update_table();});
        </script>
    </body>
</html>
