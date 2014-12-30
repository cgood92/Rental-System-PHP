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
	header("location: index.php");
	exit;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>FTC Website</title>
        <meta name="description" content="">
            
        <?php include '../../includes/head.php'; ?>
<link rel="stylesheet" type="text/css" href="../rental_style.css" />
    </head>

    <body id="home">

        <header>
            <?php include '../../includes/header.php'; ?>
        </header>
        <div class="container-fluid">
            <div class="row-fluid">
                
                <?php include '../../includes/leftnav.php'; ?>
		<div class="span9">
<a href="#" onclick="window.history.go(-2)">Return to previous page</a>
<div class="catalogForm">
<fieldset>
<?php
list ($product_id, $title, $active) = getProductInfo($_GET["idx"]);
list ($note_id, $note) = getProductNote($_GET["idx"]);
?>
	<legend><?php echo $title; ?></legend>
	<p>Special Notes: <?php echo ($note ? $note : "No notes"); ?></p>
	<p><a href="edit.php?idx=<?php echo $product_id; ?>">Edit product title, special notes, and activity</a></p>
<?php 
$status = getRentalStatus($_GET["idx"]);
if(strtoupper($status) != "RESERVED" && strtoupper($status) != "CANCELLED"){
?>
<?php
}
?>
<p>Current Status: <?php
if(strtoupper($status) == "RESERVED" || strtoupper($status) == "RENTOUT")
{
	echo "<b>" . $status . "</b>";
	echo " (<a href='../return/return2.php?idx[]=" . $_GET["idx"] . "'>Return this item</a> | <a href='../return/cancel2.php?idx[]=" . $_GET["idx"] . "'>Cancel Reservation</a>)";
	$reservesCurrentandFuture = getReservations($_GET["idx"]);
	echo "<table class='reserveView1'>";
	echo "<tr><td>Username</td><td>Start Date</td><td>End Date</td><td>Cancel/return</td></tr>";
	echo "<tr><td>Username</td><td>Start Date</td><td>End Date</td><td>Cancel/return</td></tr>";
	echo "<tr><td>Username</td><td>Start Date</td><td>End Date</td><td>Cancel/return</td></tr>";
	echo "</table>";
}
else{
	echo "<b>Available</b>";
	echo "(<a href='../reserve/reserve.php?idx[]=" . $product_id . "'>Check this item out</a>)";
}
?></p>
<p>History:
<table class="reserveHistory">
<?php
$history = getRentalHistory($_GET["idx"]);
foreach($history as $a){
	echo "<tr>";
	echo "<td>" . $a["creation_date"] . "</td>";
	echo "<td><b>" . $a["status"] . "</b></td>";
	echo "<td>Status submitted by: " . $a["employee_name"] . "</td>";
	echo "</tr>";
	echo "<tr><td COLSPAN=5 style='padding: 0 30px;'>";
	echo "Rental User: " . $a["rental_user_name"] . "<br>";
	if($a["start_date"] == "N/A")
	       echo "Start Date: " . $a["start_date"] . "<br>";
	if($a["end_date"] == "N/A")
		echo "End Date: " . $a["end_date"] . "<br>";
	echo "Order (#" . $a["rental_id"] . ") created by: " . $a["rental_created_by"] . "<br>";
	echo "Order (#" . $a["rental_id"] . ") created on: " . $a["rental_creation_date"] . "<br>";
	echo "<br>";
	echo "</td></tr>";
}
?>
</table>
</p>
</fieldset>
</div>
		</div>
            </div>

            <footer>
                <?php include '../../includes/footer.php'; ?>
            </footer>
        </div>

        <?php include '../../includes/script_files.php'; ?>

    </body>
</html>
