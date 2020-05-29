<?php
    session_start();

    include_once('../php/classes.php');
    include_once('../php/config.php');
    include_once('../php/data.php');


    if (!isset($_SESSION['auth_user'])) {
        header("Location: login.php?opunit=" . $_GET['opunit']);
        exit(0);
    }

    
