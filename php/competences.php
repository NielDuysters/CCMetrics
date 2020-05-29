<?php
    include_once('config.php');

    // Retrieving all definitions matching criteria
    //$opunit = $_GET['opunit'];



    $conn = new mysqli(HOST, USER, PASS, DTBS);

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

    $sql = "SELECT dd.ID, comp.competence, def.definition, hr.value, compv.value FROM definitiondetails dd INNER JOIN competences comp ON comp.id=dd.competence_id INNER JOIN definitions def on def.id=dd.definition_id INNER JOIN hrcompetences hr ON hr.id=dd.hrcompetence_id INNER JOIN competencevalues compv ON compv.id=dd.competencevalues_id" . $do_where . implode(" AND ", $where);
    $stmt = $conn->prepare($sql);

    if (sizeof($binds) > 0) {
        $stmt->bind_param($types, ...$binds);
    }

    $stmt->execute();
    $stmt->bind_result($id, $competence, $definition, $hrcompetence, $competencevalue);

    while ($stmt->fetch()) {
        echo "
        <tr>
            <td>".$competence."</td>
            <td>".$definition."</td>
            <td>".$hrcompetence."</td>
            <td>".$competencevalue."</td>
        </tr>
        ";
    }

    echo " {".$col_to_show."}";

    $stmt->close();


    $conn->close();
