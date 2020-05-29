<?php
    session_start();

    include_once('../php/classes.php');
    include_once('../php/config.php');
    include_once('../php/data.php');

    if (isset($_SESSION['auth_user'])) {
        header("Location: index.php?opunit=" . $_GET['opunit']);
        exit(0);
    }

    $conn = new mysqli(HOST, USER, PASS, DTBS);

    if (isset($_POST['btnLogin'])) {
        $user = $_POST['user'];
        $given_password = $_POST['pass'];

        $stmt = $conn->prepare("SELECT pass, manager_id FROM login WHERE user=?");
        $stmt->bind_param("s", $user_);
        $user_ = $user;
        $stmt->execute();
        $stmt->bind_result($pass, $manager_id);
        $stmt->fetch();
        $stmt->close();

        if ($given_password == $pass) {
            $_SESSION['auth_user'] = $manager_id;
            header("Location: index.php?opunit=" . $_GET['opunit'] . "&manager=" . $manager_id);
            exit(0);
        } else {

        }
    }
