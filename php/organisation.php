<?php
    include_once('config.php');


    $conn = new mysqli(HOST, USER, PASS, DTBS);

    $binds = [];
    $where = [];
    $types = "i";
    $do_where = "";

    $binds[] = $_GET['opunit'];

    if (!empty($_GET['division'])) {
        $where[] = "adv.employerdivision=?";
        $binds[] = $_GET['division'];
        $types .= "i";
    }
    /*if (!empty($_GET['customer'])) {
        $where[] = "adv.employerdivision=?";
        $binds[] = $_GET['division'];
        $types .= "i";
    }*/

    if (sizeof($where) > 0) {
        $do_where = " AND ";
    }


    $sql = "SELECT adv.ID, adv.firstname, adv.lastname, adv2.firstname, adv2.lastname FROM advisors adv LEFT JOIN advisor_manager am ON am.advisor_id=adv.id LEFT JOIN advisors adv2 ON adv2.id=am.manager_id WHERE adv.EmployerDivision IN (SELECT division_id FROM operationalunits_divisions WHERE operationalunit_id=?) " . $do_where . implode(" AND ", $where);
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Statement failed (" . $stmt->errno . "): " . $conn->error);
    }


    if (sizeof($binds) > 0) {
        $stmt->bind_param($types, ...$binds);
    }


    // Output data in table format
    $stmt->execute();
    $stmt->bind_result($ID, $firstname, $lastname, $manager_firstname, $manager_lastname);
    while ($stmt->fetch()) {
        $conn2 = new mysqli(HOST, USER, PASS, DTBS);
        $stmt2 = $conn2->prepare("select count(*) from advisor_crm WHERE advisor_id=?");
        $stmt2->bind_param("i", $ID_);
        $ID_ = $ID;
        $stmt2->execute();
        $stmt2->bind_result($amount);
        $stmt2->execute();
        $stmt2->fetch();
        $stmt2->close();

        $stmt2 = $conn2->prepare("select count(*) from advisor_crm WHERE advisor_id=? AND crm_login != ''");
        $stmt2->bind_param("i", $ID_);
        $ID_ = $ID;
        $stmt2->execute();
        $stmt2->bind_result($filled);
        $stmt2->execute();
        $stmt2->fetch();

        echo "
        <tr>
            <td>".$ID."</td>
            <td>".ucwords(strtolower($firstname))."</td>
            <td>".ucwords(strtolower($lastname))."</td>
            <td>".ucwords(strtolower($manager_firstname)). " " . ucwords(strtolower($manager_lastname)). "</td>
            <td>".$filled."/".$amount."</td>
            <td><a href='advisor.php?opunit=".$_GET['opunit']."&id=".$ID."'>Details</a></td>
        </tr>
        ";

        $stmt2->close();
        $conn2->close();
    }

    $conn->close();
