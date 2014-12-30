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

if(!isset($_GET["idx"])){
	header("location: ../reserve/index.php?empty");
	exit;
}


$idx = "";
foreach($_GET["idx"] as $a){
	$status = strtoupper(getRentalStatus($a));
	if($status == "CANCELLED" || $status == "Returned" || $status = ""){
		$idx .= "&idx[]=" . $a;
	}
	else{	
		returnItem($a, $_SESSION["sysid"]);
	}
}
if($idx){
	header("location: ../reserve/index.php?notReserved=1" . $idx);
	exit;
}


header('location: ../reserve/index.php');
exit;

?>
