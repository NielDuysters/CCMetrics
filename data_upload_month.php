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
                    <h2>Upload data month</h2>

                    <div>
                        <label>Select file</label>
                        <input type="file" name="file" id="file">
                        <input type="submit" name="btnUpload" value="Upload">
                    </div>

                    <div>
                        <button id="save">Save to database</button>
                    </div>


                    <!-- temp_table -->
                    <table id="temp">
                        <thead>
                            <tr>
                                <th>Mdw</th>
                                <th>CRM</th>
                                <th>Displayname</th>
                                <th>Period_month</th>
                                <th>Data_field</th>
                                <th>Data_field_extra</th>
                                <th>Omschrijving</th>
                                <th>Customer</th>
                                <th>FW_Opunit</th>
                                <th>FW_Division</th>
                            </tr>

                        </thead>
                        <tbody id="data"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            document.getElementsByName("btnUpload")[0].addEventListener('click', upload_data);
            document.getElementById("save").addEventListener('click', persist)

            function upload_data() {
                var file_data = $('#file').prop('files')[0];
                let btn = document.getElementsByName("btnUpload")[0];
                var form_data = new FormData();
                form_data.append('file', file_data);
                form_data.append('btnUpload', btn);

                let url = 'php/upload_data_month.php?opunit=' + get_op_unit();

                $.ajax({
                    url: url,
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function(response){
                        document.getElementById("data").innerHTML = response;
                        get_data();
                    }
                 });
            }

            function get_data() {
                let url = "php/upload_data_month.php?get_data=true&opunit=" + get_op_unit();

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
                let row_to_edit = e.target.parentElement.getAttribute("data-row");
                let col_to_edit = e.target.getAttribute("data-col");
                let value = e.target.innerHTML;

                let url = "php/upload_data_month.php?save=true&row=" + row_to_edit + "&col=" + col_to_edit + "&value=" + value;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log(response);
                    }
                });
            }

            function persist() {
                let url = "php/upload_data_month.php?opunit="+get_op_unit()+"&persist=true";

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    success: function(response) {

                    }
                });
            }

            $(document).ready(function() { get_data(); });
        </script>
    </body>
</html>
