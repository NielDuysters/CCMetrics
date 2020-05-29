<?php

    include_once('../classes.php');
    include_once('../config.php');
    include_once('../data.php');

    $conn = new mysqli(HOST, USER, PASS, DTBS);

    if (!empty($_GET['get_name'])) {
        $stmt = $conn->prepare("SELECT firstname, lastname FROM advisors WHERE ID=?");
        $stmt->bind_param("i", $id_);
        $id_ = $_GET['advisor'];
        $stmt->execute();
        $stmt->bind_result($firstname, $lastname);
        $stmt->fetch();
        $stmt->close();

        echo ucwords(strtolower($firstname)). " " . ucwords(strtolower($lastname));
        return;
    }



    if (!empty($_GET['save'])) {
        $dd = $_GET['dd'];
        $quarter = $_GET['quarter'];
        $advisor = $_GET['advisor'];
        $score = $_GET['score'];

        $months = quarterToMonths($quarter);

        foreach ($months as $m => $month) {
            $stmt = $conn->prepare("REPLACE INTO hragreements_advisors (definitiondetails, advisor_id, score, month) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiii", $dd_, $advisor_, $score_, $month_);
            $dd_ = $dd;
            $advisor_ = $advisor;
            $score_ = $score;
            $month_ = $month;
            $stmt->execute();
            $stmt->close();
        }

        return;
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


    $where[] = "competence_id=?";
    $binds[] = strToId("hr afspraken", "competences", "competence");
    $types .= "i";

    if (sizeof($where) > 0) {
        $do_where = " WHERE ";
    }





    $sql = "select DISTINCT dd.ID, dd.definition_id, def.definition FROM definitiondetails dd INNER JOIN definitions def ON def.id=dd.definition_id " . $do_where . implode(" AND ", $where);


    $stmt = $conn->prepare($sql);
    if (sizeof($binds) > 0) {
        $stmt->bind_param($types, ...$binds);
    }
    //$stmt->bind_param("ii", $opunit_, $competence_);
    $opunit_ = $_GET['opunit'];
    $competence_ = strToId("hr afspraken", "competences", "competence");

    $stmt->execute();

    //$stmt->bind_result($id, $definition, $target, $expr, $bucket1_bottom, $bucket1_upper, $bucket2_bottom, $bucket2_upper, $bucket3_bottom, $bucket3_upper, $bucket3_bottom, $bucket3_upper, $bucket4_bottom, $bucket4_upper, $bucket5_bottom, $bucket5_upper);
    $stmt->bind_result($dd_id, $definition_id, $definition);

    while ($stmt->fetch()) {

        $conn2 = new mysqli(HOST, USER, PASS, DTBS);
        $stmt2 = $conn2->prepare("SELECT score FROM hragreements_advisors WHERE month=? AND advisor_id=? AND definitiondetails=?");
        $stmt2->bind_param("iii", $month_, $advisor_, $dd_);
        $month_ = quarterToMonths($_GET['quarter'])[0];
        $advisor_ = $_GET['advisor'];
        $dd_ = $dd_id;
        $stmt2->execute();
        $stmt2->bind_result($score);
        $stmt2->fetch();
        $stmt2->close();
        $conn2->close();

        if ($score == 0) {
            $score = "";
        }

        echo "
        <tr data-dd-id='".$dd_id."'>
            <td>".$definition."</td>
            <td class='editable' contenteditable='true' data-score-nr='1'>".$score."</td>

        </tr>
        ";
    }

    $stmt->close();
    $conn->close();
