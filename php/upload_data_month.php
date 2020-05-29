<?php
    session_start();

    ini_set('display_errors', true);

    include_once('classes.php');
    include_once('config.php');
    include_once('data.php');

    // Open database connection
    $conn = new mysqli(HOST, USER, PASS, DTBS);
    if ($conn->connect_error) {
        die("Connection to database failed: " . $conn->connect_error);
    }

    if (!empty($_GET['get_data'])) {
        if (isset($_SESSION['temp'])) {
            foreach ($_SESSION['temp'] as $arr => $value) {
                echo "<tr data-row='".$arr."'>";
                foreach ($value as $a => $val) {
                    echo "<td class='editable' contenteditable='true' data-col='".$a."'>".$val."</td>";
                }
                echo "</tr>";
            }
        }
    }

    if (isset($_POST['btnUpload'])) {
        ini_set('auto_detect_line_endings', true);
        $file_name = $_FILES['file']['tmp_name'];
        $file = fopen($file_name, 'r');

        if ($_FILES['file']['size'] <= 0) {
            echo "Error";
            return;
        }

        $_SESSION['temp'] = [];


        while ( ($column = fgetcsv($file, 10000, ";")) != false) {
            $advisor_id = $column[0];
            $crm = $column[1];
            $display_name = $column[2];
            $period_month = $column[3];
            $data_field = $column[4];
            $data_field_extra = $column[5];
            $description = $column[6];
            $customer = $column[7];
            $opunit = $column[8];
            $division = $column[9];

            $arr = array($advisor_id, $crm, $display_name, $period_month, $data_field, $data_field_extra, $description, $customer, $opunit, $division);
            $_SESSION['temp'][] = $arr;
        }

    }


    if (!empty($_GET['save'])) {
        $row = $_GET['row'];
        $col = $_GET['col'];
        $value = $_GET['value'];

        $_SESSION['temp'][$row][$col] = $value;
        echo "lol";
    }

    if (!empty($_GET['persist'])) {
        foreach ($_SESSION['temp'] as $key => $row) {
            $stmt = $conn->prepare("REPLACE INTO metric_data_advisors (advisor_id, crm_id, definitiondetails, data, data_extra, month) VALUES (?, ?, 1, ?, ?, ?)");
            $stmt->bind_param("iissi", $advisor_, $crm_, $data_, $data_extra_, $month_);
            $advisor_ = $row[0];
            $crm_ = $row[1];
            $data_ = $row[4];
            $data_extra = $row[5];
            $month_ = $row[3];
            $stmt->execute();
            $stmt->close();
        }

        $_SESSION['temp'] = [];
    }
