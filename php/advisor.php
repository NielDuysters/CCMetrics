<?php
    include_once('config.php');

    $conn = new mysqli(HOST, USER, PASS, DTBS);

    if (isset($_POST['btnSave'])) {

        for ($i = 0; $i < sizeof($_POST['crm_name']); $i++) {
            $stmt = $conn->prepare("INSERT INTO advisor_crm (advisor_id, crm_name, crm_login) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $id_, $name_, $login_);
            $id_ = $_POST['advisor_id'];
            $name_ = $_POST['crm_name'][$i];
            $login_ = $_POST['crm_login'][$i];
            $stmt->execute();
            $stmt->close();
        }

        $conn->close();

        header('Location: ../advisor.php?opunit='.$_POST['opunit'].'&id='.$_POST['advisor_id']);
        exit(1);
    }

    $crms = [];

    $stmt = $conn->prepare("SELECT adv.ID, adv.firstname, adv.lastname, divv.name, adv.employer, adv.startdate, adv.functioncode, adv.functiondescription, adv2.id, adv2.firstname, adv2.lastname FROM advisors adv LEFT JOIN advisor_manager am ON am.advisor_id=adv.id LEFT JOIN advisors adv2 ON adv2.id=am.manager_id LEFT JOIN divisions divv ON divv.id=adv.employerdivision WHERE adv.ID=?");
    $stmt->bind_param("i", $id_);
    $id_ = $_GET['id'];
    $stmt->execute();
    $stmt->bind_result($ID, $firstname, $lastname, $division_name, $employer, $startdate, $functioncode, $functiondescription, $manager_id, $manager_firstname, $manager_lastname);
    $stmt->fetch();
    $stmt->close();

    //$stmt = $conn->prepare("SELECT crm.name FROM advisor_crm ac INNER JOIN crms crm ON ac.crm_id=crm.id WHERE advisor_id=?");
    $stmt = $conn->prepare("SELECT crms.name, ac.crm_login FROM advisor_crm ac INNER JOIN crms crms ON crms.id=ac.crm_id WHERE advisor_id=?");
    $stmt->bind_param("i", $advisor_);
    $advisor_ = $_GET['id'];
    $stmt->execute();
    $stmt->bind_result($crm_name, $crm_login);

    while ($stmt->fetch()) {
        $crms[] = array($crm_name, $crm_login);
    }

    $stmt->close();
    $conn->close();
