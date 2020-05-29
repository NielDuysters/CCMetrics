<?php

    include_once('../classes.php');
    include_once('../config.php');
    include_once('../data.php');

    $conn = new mysqli(HOST, USER, PASS, DTBS);

    $binds = [];
    $where = [];
    $types = "";
    $do_where = "";



    if (!empty($_GET['manager'])) {
        $where[] = "am.manager_id=?";
        $binds[] = $_GET['manager'];
        $types .= "i";
    }
    /*
    if (!empty($_GET['customer'])) {
        $where[] = "adv.employerdivision=?";
        $binds[] = $_GET['division'];
        $types .= "i";
    }*/

    if (sizeof($where) > 0) {
        $do_where = " AND ";
    }


    $sql = "SELECT adv.ID, adv.firstname, adv.lastname FROM advisors adv INNER JOIN advisor_manager am ON am.advisor_id=adv.id" . $do_where . implode(" AND ", $where);
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Statement failed (" . $stmt->errno . "): " . $conn->error);
    }


    if (sizeof($binds) > 0) {
        $stmt->bind_param($types, ...$binds);
    }


    // Output data in table format
    $stmt->execute();
    $stmt->bind_result($ID, $firstname, $lastname);
    while ($stmt->fetch()) {

        echo "
        <tr>
            <td>".$ID."</td>
            <td>".ucwords(strtolower($firstname))."</td>
            <td>".ucwords(strtolower($lastname))."</td>
            <td><a href='hrafspraken/buckets.php?opunit=".$_GET['opunit']."&advisor=".$ID."'>Details</a></td>
        </tr>
        ";
    }

    $conn->close();
