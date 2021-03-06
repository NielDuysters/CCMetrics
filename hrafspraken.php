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
                        echo str_replace("%opunit%", $_GET['opunit'], file_get_contents("partials/dashboard-options.html"));
                    ?>
                </div>

                <div id="content">
                    <h2>HR Afspraken</h2>

                    <!-- Select for competences -->
                    <select name="competence">
                        <option value="">Select competence</option>

                        <?php
                            foreach ($competences as $competence) {
                                if ($competence->competence != "" && (strtoupper($competence->competence) == "HR AFSPRAKEN")) {
                                    echo "<option value='" . $competence->id . "'>" . $competence->competence . "</option>";
                                }
                            }
                        ?>
                    </select>

                    <!-- Select for roles -->
                    <select name="role">
                        <option value="">Select role</option>

                        <?php
                            foreach ($roles as $role) {
                                if ($role->name != "") {
                                    echo "<option value='" . $role->id . "'>" . $role->name . "</option>";
                                }
                            }
                        ?>
                    </select>

                    <!-- Select for months -->
                    <select name="month">
                        <option value="<?php echo str_to_month(date('F')) ?>"><?php echo date('F') . " (Current month)" ?></option>

                        <?php
                            foreach ($months_in_buckets as $month) {
                                echo "<option value='" . str_to_month($month) . "'>" . $month . "</option>";
                            }
                        ?>
                    </select>

                    <!-- Table of all buckets -->
                    <table id="buckets">
                        <thead>
                            <tr>
                                <th>Metrics</th>
                                <!--<th>Displayname</th>-->
                                <th colspan='2'>Bucket 1</th>
                                <th colspan='2'>Bucket 3</th>
                                <th colspan='2'>Bucket 5</th>
                            </tr>

                        </thead>
                        <tbody id="data"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            let select_elements = document.getElementsByTagName("select");
            for (i = 0; i < select_elements.length; i++) {
                select_elements[i].addEventListener("change", update_table);
            }

            function update_table() {
                let opunit = get_op_unit();
                let competence = document.getElementsByName("competence")[0].value;
                let competence_sel = document.getElementsByName("competence")[0].options[document.getElementsByName("competence")[0].selectedIndex].innerHTML;
                let role = document.getElementsByName("role")[0].value;





                let url = "php/hrafspraken.php?opunit=" + opunit + "&competence=" + competence + "&role=" + role;

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

                        if (competence_sel == "HR Afspraken") {
                            let disable_a = document.querySelectorAll("[data-bucket-nr='2']");
                            for (i = 0; i < disable_a.length; i++) {
                                disable_a[i].setAttribute("contenteditable", "false");
                                disable_a[i].style.backgroundColor = "#a4a4a4";
                            }

                            let disable_b = document.querySelectorAll("[data-bucket-nr='4']");
                            for (i = 0; i < disable_a.length; i++) {
                                disable_b[i].setAttribute("contenteditable", "false");
                                disable_b[i].style.backgroundColor = "#a4a4a4";
                            }
                        }


                        $("td.editable").hover(function() {
                            $("td.editable").css("background-color", "transparent");
                            if ($(this).index() % 2 != 0) {
                                $(this).css("background-color", "#F2F2F2");
                                $(this).next().css("background-color", "#F2F2F2");
                            } else {
                                $(this).css("background-color", "#F2F2F2");
                                $(this).prev().css("background-color", "#F2F2F2");
                            }
                        });

                    }
                });



            }

            function save_data(e) {
                let row_to_edit = e.target.parentElement.getAttribute("data-bucket-id");
                let bucket_to_edit = e.target.getAttribute("data-bucket-nr");
                let value = e.target.innerHTML;

                let url = "php/hrafspraken.php?save=true&row=" + row_to_edit + "&bucket=" + bucket_to_edit + "&value=" + value;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log(response);
                    }
                });
            }


            $(document).ready(function() {
                update_table();
            });
        </script>
    </body>
</html>
