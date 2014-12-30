<?php

session_start();

if (file_exists('../model.php')) {
    require_once '../model.php';
} else {
    header('location: /ftc/errordocs/500.php?nomodel');
    exit;
}

if (file_exists('../../library/validation.php')) {
    require_once '../../library/validation.php';
} else {
    header('location: /ftc/errordocs/500.php?novalid');
    exit;
}

updateUser($_GET["idx"]
	, $_GET["email"]
	, $_GET["fname"]
	, $_GET["lname"]
	, $_GET["phone"]);

    header('location: index.php?updateUser=1');
    exit;
?>
