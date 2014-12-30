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

if(!$_GET["idx"]){
	header("location: index.php?noidx");
	exit;
}

$counter = 0;
foreach($_GET["idx"] as $a){
	if(deleteProduct($a) < 0)
		$counter++;
}

header("location: index.php?deleteSuccess=1&counter=" . $counter);
exit;

?>
