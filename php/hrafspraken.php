<?php

    include_once('config.php');
    include_once('data.php');
    $conn = new mysqli(HOST, USER, PASS, DTBS);

    if (isset($_GET['save'])) {
        $row_id = $_GET['row'];
        $bucket = "bucket" . $_GET['bucket'];
        $value = $_GET['value'];

        $allowed_table_names = [
            "bucket1_bottom",
            "bucket1_upper",
            "bucket2_bottom",
            "bucket2_upper",
            "bucket3_bottom",
            "bucket3_upper",
            "bucket4_bottom",
            "bucket4_upper",
            "bucket5_bottom",
            "bucket5_upper"
        ];

        if (!in_array($bucket, $allowed_table_names)) {
            echo "Fail";
            exit(-1);
        }

        $sql = "UPDATE definitiondetails SET ".$bucket."=? WHERE ID=?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Statement failed (" . $stmt->errno . "): " . $conn->error);
        }
        $stmt->bind_param("ii", $bucket_value, $id_);
        $bucket_value = $value;
        $id_ = $row_id;
        $stmt->execute();
        $stmt->close();

        echo "Success";
        exit(0);
    }


    $binds = [];
    $where = [];
    $types = "";
    $do_where = "";

    if (!empty($_GET['opunit'])) {
        $where[] = "opsunit_id=?";
        $binds[] = $_GET['opunit'];
        $types .= "i";
    }
    if (!empty($_GET['competence'])) {
        $where[] = "competence_id=?";
        $binds[] = $_GET['competence'];
        $types .= "i";
    }
    if (!empty($_GET['role'])) {
        $where[] = "role_id=?";
        $binds[] = $_GET['role'];
        $types .= "i";
    }

    if (sizeof($where) > 0) {
        $do_where = " WHERE ";
    }

    $sql = "select dd.id, def.definition, target, expr, bucket1_bottom, bucket1_upper, bucket2_bottom, bucket2_upper, bucket3_bottom, bucket3_upper, bucket3_bottom, bucket3_upper, bucket4_bottom, bucket4_upper, bucket5_bottom, bucket5_upper FROM definitiondetails dd INNER JOIN definitions def on def.id=dd.definition_id" . $do_where . implode(" AND ", $where) . " AND (dd.competence_id=?)";
    $binds[] = strToId("hr afspraken", "competences", "competence");

    $types .= "i";

    $stmt = $conn->prepare($sql);
    if (sizeof($binds) > 0) {
        $stmt->bind_param($types, ...$binds);
    }

    $stmt->execute();

    $stmt->bind_result($id, $definition, $target, $expr, $bucket1_bottom, $bucket1_upper, $bucket2_bottom, $bucket2_upper, $bucket3_bottom, $bucket3_upper, $bucket3_bottom, $bucket3_upper, $bucket4_bottom, $bucket4_upper, $bucket5_bottom, $bucket5_upper);

    while ($stmt->fetch()) {
        echo "
        <tr data-bucket-id='".$id."'>
            <td>".$definition."</td>
            <td class='editable' contenteditable='true' data-bucket-nr='1_bottom'>".$bucket1_bottom."</td>
            <td class='editable' contenteditable='true' data-bucket-nr='1_upper'>".$bucket1_upper."</td>
            <td class='editable' contenteditable='true' data-bucket-nr='3_bottom'>".$bucket3_bottom."</td>
            <td class='editable' contenteditable='true' data-bucket-nr='3_upper'>".$bucket3_upper."</td>
            <td class='editable' contenteditable='true' data-bucket-nr='5_bottom'>".$bucket5_bottom."</td>
            <td class='editable' contenteditable='true' data-bucket-nr='5_upper'>".$bucket5_upper."</td>
        </tr>
        ";
    }

    $stmt->close();
    $conn->close();
