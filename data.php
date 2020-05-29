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
                    <h2>Upload data</h2>

                    <form action="php/data_upload.php?opunit=<?php echo $_GET['opunit'] ?>" method="post" enctype="multipart/form-data">
                        <div>
                            <label>Upload advisors</label>
                            <input type="file" name="file_advisors" id="file">
                            <input type="submit" name="btnUploadAdvisors" value="Upload">
                        </div>
                        <div>
                            <label>Upload managers</label>
                            <input type="file" name="file_managers" id="file">
                            <input type="submit" name="btnUploadManagers" value="Upload">
                        </div>
                        <div>
                            <label>Upload customer - advisor</label>
                            <input type="file" name="file_customer_advisor" id="file">
                            <input type="submit" name="btnUploadCustomerAdvisor" value="Upload">
                        </div>
                        <div>
                            <label>Upload CRMs</label>
                            <input type="file" name="file_crm" id="file">
                            <input type="submit" name="btnUploadCrms" value="Upload">
                        </div>


                        <br><br>
                        <div>
                            <label>Upload general data</label>
                            <input type="file" name="file2" id="file">
                            <input type="submit" name="btnUploadData" value="Upload">
                        </div>


                        <div>
                            <label>Upload competences</label>
                            <input type="file" name="fileaaa2" id="file">
                            <input type="submit" name="btnUploadDataaa" value="Upload">
                        </div>
                        <div>
                            <label>Upload HR Afspraken</label>
                            <input type="file" name="fileaaa2" id="file">
                            <input type="submit" name="btnUploadDataaaa" value="Upload">
                        </div>
                        <div>
                            <label>Upload Bradford</label>
                            <input type="file" name="fileaaa2" id="file">
                            <input type="submit" name="btnUploadDataaaa" value="Upload">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            let select_elements = document.getElementsByTagName("select");
            for (i = 0; i < select_elements.length; i++) {
                select_elements[i].addEventListener("change", update_table);
            }
            document.getElementsByName("division")[0].addEventListener("change", function() {
                if (document.getElementsByName("division")[0].value == "") {
                    document.getElementsByName("customer")[0].disabled = true;
                    return;
                } else {
                    document.getElementsByName("customer")[0].disabled = false;
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
                        alert(response);
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
                        let str = "<option value=''>Select customer</option>";
                        str += response;

                        document.getElementsByName("customer")[0].innerHTML = str;
                    }
                });
            }

            $(document).ready(function() {update_table();});
        </script>
    </body>
</html>
