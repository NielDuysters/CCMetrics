<?php
    include_once('config.php');

    $conn = new mysqli(HOST, USER, PASS, DTBS);


    if (isset($_GET['save'])) {
        // ?
        /*
        $stmt = $conn->prepare("SELECT dd.competence_id, dd.role_id, dd.hrcompetence_id, dd.competencevalues_id FROM metric_data_opunits mdo INNER JOIN definitiondetails dd ON dd.id=mdo.definitiondetails_id WHERE id=?");
        $stmt->bind_param("i", $definition_);
        $definition_ = $_GET['definition'];
        $stmt->execute();
        $stmt->bind_result($competence, $role, $hrcompetence, $competencevalues);
        $stmt->fetch();
        $stmt->close();
        */

        $months = explode(',', $_GET['month']);

        foreach ($months as $key => $month) {

            $stmt = $conn->prepare("REPLACE INTO metric_data_opunits (definitiondetails, displayname, opunit_id, target, expr, is_waived, not_applicable, no_data, level, month) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isissiiiis", $definitiondetails_id_, $displayname_, $opunit_id_, $target_, $expr_, $is_waived_, $na_, $no_data, $level_, $month_);
            $definitiondetails_id_ = $_GET['dd'];
            $displayname_ = $_GET['displayname'];
            $opunit_id_ = $_GET['opunit'];
            $target_ = $_GET['target'];
            $expr_ = $_GET['expr'];
            $is_waived_ = $_GET['waived'];
            $na_ = $_GET['na'];
            $no_data = $_GET['no_data'];
            $level_ = $_GET['level'];
            $month_ = $month;

            if(!$stmt->execute()) {
                echo $conn->error;
            }
            $stmt->close();
        }


        return;
    }

    if (isset($_GET['getdata'])) {

        $binds = [];
        $where = [];
        $types = "";
        $do_where = "";

        if (!empty($_GET['displayname'])) {
            $where[] = "displayname=?";
            $binds[] = $_GET['displayname'];
            $types .= "s";
        }

        $where[] = "opunit_id=?";
        $binds[] = $_GET['opunit'];
        $types .= "i";

        $where[] = "definitiondetails=?";
        $binds[] = $_GET['dd'];
        $types .= "i";

        $where[] = "month=?";
        $binds[] = $_GET['month'];
        $types .= "i";

        if (sizeof($where) > 0) {
            $do_where = " WHERE ";
        }

        $sql = "SELECT displayname, target, expr, is_waived, not_applicable, no_data, level FROM metric_data_opunits mdo " . $do_where . implode(" AND ", $where);
        $stmt = $conn->prepare($sql);

        if (sizeof($binds) > 0) {
            $stmt->bind_param($types, ...$binds);
        }



        $stmt->execute();
        $stmt->bind_result($displayname, $target, $expr, $is_waived, $na, $no_data, $level);
        $stmt->fetch();

        echo $displayname . "," . $target . "," . $expr . "," . $is_waived . "," . $na . "," . $no_data . "," . $level;
        $stmt->close();
        return;
    }

    $sql = "SELECT dd.id, dd.definition_id, def.definition FROM definitiondetails dd INNER JOIN definitions def ON def.id=dd.definition_id WHERE opsunit_id=? AND dd.definition_id=? AND dd.customer_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $opunit_, $def_, $customer_);
    $def_ = $_GET['definition'];
    $opunit_ = $_GET['opunit'];
    $customer_ = $_GET['customer'];
    $stmt->execute();
    $stmt->bind_result($dd_id, $def_id, $definition);
    $stmt->fetch();
    $stmt->close();

    $allDisplaynames = [];
    $sql = "SELECT DISTINCT displayname FROM metric_data_opunits mdo WHERE definitiondetails=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dd_);
    $dd_ = $dd_id;
    $stmt->execute();
    $stmt->bind_result($dn);

    while ($stmt->fetch()) {
        $allDisplaynames[] = $dn;
    }
    $stmt->close();


    $conn->close();
