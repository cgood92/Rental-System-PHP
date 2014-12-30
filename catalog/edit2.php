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
if(!$_GET["idx"] || !$_GET["title"])
{
	header("location: edit.php?emptyinfo=1&idx=" . (int)$_GET["idx"]);
	exit;
}

if(updateProduct($_GET["idx"]
	, $_GET["title"]
	, $_GET["active"]
	, $_GET["note"]
	, $_GET["note_idx"]
	, $_SESSION["sysid"]
	) > 0)
{
	header("location: index.php?update=1");
	exit;
}
else
{
	header("location: edit.php?idx=" . (int)$_GET["idx"] . "&error=223");
	exit;
}
?>
