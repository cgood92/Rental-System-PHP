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

if(!$_GET["title"])
{
	header("location: add.php?error=notitle");
	exit;
}
else
{
	$add = addProduct($_GET["title"], $_SESSION["sysid"], $_GET["note"]);
	if($add < 0)
	{
		header("location: add.php?error=2");
		exit;
	}
	else
	{
		header("location: index.php?success=1");
		exit;
	}
}
?>
