<?php

    include_once('config.php');
    include_once('data.php');

    $binds = [];
    $where = [];
    $types = "";
    $do_where = "";

    if (!empty($_GET['opunit'])) {
        $where[] = "opsunit_id=?";
        $binds[] = $_GET['opunit'];
        $types .= "i";
    }
    if (!empty($_GET['customer'])) {
        $where[] = "customer_id=?";
        $binds[] = $_GET['customer'];
        $types .= "i";
    }
    if (!empty($_GET['competence'])) {
        $where[] = "competence_id=?";
        $binds[] = $_GET['competence'];
        $types .= "i";
    }

    if (sizeof($where) > 0) {
        $do_where = " WHERE ";
    }

    $conn = new mysqli(HOST, USER, PASS, DTBS);
    $binds[] = strToId("functiespecifieke competentie", "competences", "competence");
    $binds[] = strToId("generieke competentie", "competences", "competence");

    $types .= "ii";

    $sql = "SELECT DISTINCT dd.id, def.id, def.definition, def.displayname, c.name, dd.customer_id FROM definitiondetails dd INNER JOIN definitions def ON def.id=dd.definition_id INNER JOIN customers c ON c.id=dd.customer_id". $do_where . implode(" AND ", $where) . " AND (dd.competence_id=? OR dd.competence_id=?)";
    $stmt = $conn->prepare($sql);

    if (sizeof($binds) > 0) {
        $stmt->bind_param($types, ...$binds);
    }

    $stmt->execute();
    $stmt->bind_result($dd_id ,$def_id, $definition, $displayname, $customer, $customer_id);

    while ($stmt->fetch()) {
        echo "
        <tr>
            <td>".$def_id."</td>
            <td>".$definition."</td>
            <td>".$displayname."</td>
            <td>".$customer."</td>
            <td><a href='metric.php?opunit=".$_GET['opunit']."&dd=".$dd_id."&definition=".$def_id."&customer=".$customer_id."'>Details</a></td>
        </tr>
        ";
    }

    $stmt->close();
    $conn->close();
