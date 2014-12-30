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


reserveProducts(
	$_GET["idx"]
	, $_GET["sdate"]
	, $_GET["rdate"]
	, $_GET["delivery"]
	, $_GET["location"]
	, $_GET["email"]
	, $_GET["note"]
	, $_SESSION["sysid"]
);

$user_idx = isUserCreated($_GET["email"]);
if($user_idx == 0)
{
	header('location: index.php?reservedSuccess=1');
	exit;
}
else{
	header('location: ../user/update.php?newuser=1&user_idx=' . $user_idx);
	exit;
}


?>
