<?php
    include_once('php/data.php');
    include_once('php/metric.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <?php
            // Including head-contents (files, scripts, metadata,...)
            echo file_get_contents("partials/head.html");
        ?>

        <style>
            #metric-info {
                font-size: 18px;
            }
            #metric-info span {font-weight: bold;}
            #metric-info div {
                margin-top: 15px;
            }

            .checkbox {
                display: inline-block;
                margin-left: 15px;
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
                    <h2>Metric aanpassen</h2>

                    <div id="metric-info" data-dd-id="<?php echo $dd_id; ?>" data-opunit-id="<?php echo $_GET['opunit']; ?>" data-customer-id="<?php echo $_GET['customer']; ?>">

                        <label>View data for month</label>
                        <select name='month'>
                            <option>Select month</option>
                            <?php
                                $i = 1;
                                foreach ($months as $month) {
                                    echo "<option value='".$i."'>".$month."</option>";

                                    $i++;
                                }
                            ?>
                        </select>

                        <?php
                            if ($is_waived == 1) {
                                $is_waived = "checked";
                            }
                            if ($na == 1) {
                                $na = "checked";
                            }


                            echo "<div><span>Metric:</span> ".$definition." </div>";
                            echo "<div><span>Displayname:</span> <input type='text' value='".$displayname."' name='displayname'>";

                            echo "
                                <select name='allDisplaynames'>
                                    <option value=''>&nbsp;</option>
                                ";

                                foreach ($allDisplaynames as $dn => $val) {
                                    if ($val != NULL && val != "") {
                                        echo "<option>".$val."</option>";
                                    }
                                }

                            echo "
                                </select></div>
                            ";

                            echo "<div><span>Target:</span> <input type='text' value='' name='target'></div>";
                            echo "<div><span>Expression:</span> <input type='text' value='' name='expr'></div>";
                            echo "<div><span>Waived:</span> <input type='checkbox' name='is_waived'></div>";
                            echo "<div><span>Not applicable:</span> <input type='checkbox' name='na'></div>";
                            echo "<div><span>No data:</span> <input type='checkbox' name='no_data'></div>";
                            echo "<div><span>Level:</span>
                                <select name='level'>
                                    <option value='0'>None</option>
                                    <option value='1'>Starter</option>
                                    <option value='2'>Regular</option>
                                    <option value='3'>Advanced</option>
                                </select>
                            </div>";

                            echo "<br>";
                            echo "<label>Save data for month</label>";
                            $i = 1;
                            foreach ($months as $month) {
                                echo "<input type='checkbox' name='month[]' value='".$i."' class='checkbox'><label class='month-label'>".$month."</label'>";

                                $i++;
                            }
                        ?>


                        <br><br>
                        <button id="save">Save</button>
                    </div>

                </div>

            </div>
        </div>

        <script>

        document.getElementById("save").addEventListener('click', save_data);
        document.getElementsByName("month")[0].addEventListener('change', get_data);
        document.getElementsByName("allDisplaynames")[0].addEventListener('change', change_dn);

        function change_dn(e) {
            document.getElementsByName("displayname")[0].value = e.target.value;
            get_data();
        }

        function get_data() {
            let month = document.getElementsByName("month")[0].value;
            let definition = document.getElementById("metric-info").getAttribute("data-definition-id");
            let opunit = document.getElementById("metric-info").getAttribute("data-opunit-id");
            let customer = document.getElementById("metric-info").getAttribute("data-customer-id");
            let dd_id = document.getElementById("metric-info").getAttribute("data-dd-id");
            let displayname = document.getElementsByName("displayname")[0].value;


            let url = "php/metric.php?getdata=true&month=" + month + "&dd=" + dd_id + "&opunit=" + opunit + "&displayname=" + displayname;

            $.ajax({
                type: 'GET',
                url: url,
                data: $(this).serialize(),
                success: function(response) {
                    console.log(response);
                    let data = response.split(',');

                    //let dd_id = data[0];
                    //let definition = data[1];
                    let displayname = data[0];
                    let target = data[1];
                    let expr = data[2];
                    let is_waived = data[3];
                    let na = data[4];
                    let no_data = data[5];
                    let level = data[6];


                    document.getElementsByName("target")[0].value = target;
                    document.getElementsByName("expr")[0].value = expr;



                    //alert(response);

                    document.getElementsByName("is_waived")[0].checked = false;
                    document.getElementsByName("na")[0].checked = false;
                    document.getElementsByName("no_data")[0].checked = false;

                    if (is_waived == "1") {
                        document.getElementsByName("is_waived")[0].checked = true;
                    }
                    if (na == "1") {
                        document.getElementsByName("na")[0].checked = true;
                    }
                    if (no_data == "1") {
                        document.getElementsByName("no_data")[0].checked = true;
                    }

                    document.getElementsByName("level")[0].selectedIndex = level;
                }
            });
        }

        function save_data(e) {

            //let row_to_edit = e.target.parentElement.getAttribute("data-id");

            let definition = document.getElementById("metric-info").getAttribute("data-definition-id");
            let opsunit = document.getElementById("metric-info").getAttribute("data-opunit-id");
            let customer = document.getElementById("metric-info").getAttribute("data-customer-id");
            let month = document.getElementsByName("month[]");
            let dd_id = document.getElementById("metric-info").getAttribute("data-dd-id");

            var checked_months = [];
            $("input[name='month[]']").each(function() {
                if ($(this).is(":checked")) {
                    checked_months.push($(this).val());
                }
            });

            checked_months_str = "";
            for (i = 0; i < checked_months.length; i++) {
                checked_months_str += checked_months[i] + ",";
            }

            if (checked_months_str.length > 0) {
                checked_months_str = checked_months_str.substring(0, checked_months_str.length -1);
            }

            //alert(checked_months_str)

            let target = document.getElementsByName("target")[0].value;
            let expr = document.getElementsByName("expr")[0].value;
            let is_waived = document.getElementsByName("is_waived")[0].checked;
            let na = document.getElementsByName("na")[0].checked;
            let no_data = document.getElementsByName("no_data")[0].checked;
            let displayname = document.getElementsByName("displayname")[0].value;
            let level = document.getElementsByName("level")[0].value;

            if (is_waived == true) {
                is_waived = 1;
            }
            if (na == true) {
                na = 1;
            }
            if (no_data == true) {
                no_data = 1;
            }


            let url = "php/metric.php?save=true&target=" + target + "&expr=" + expr + "&waived=" + is_waived + "&na=" + na + "&no_data=" + no_data + "&month=" + checked_months_str + "&opunit=" + opsunit + "&dd=" + dd_id + "&displayname=" + displayname + "&level=" + level;

            $.ajax({
                type: 'POST',
                url: url,
                data: $(this).serialize(),
                success: function(response) {
                    console.log(response);
                }
            });
        }
        </script>
    </body>
</html>
