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
?>

<!DOCTYPE html>
<html>
    <head>
        <title>FTC Website</title>
        <meta name="description" content="">
            
        <?php include '../../includes/head.php'; ?>
        <?php include '../../includes/script_files.php'; ?>
<script type="text/javascript">
$( document ).ready(function() {
	$("#reserveButton").click(function() {
		    $(this).closest("form").attr("action", "reserve.php");     
	});
	$("#returnButton").click(function() {
		    $(this).closest("form").attr("action", "../return/return2.php");       
	});
	$("#cancelButton").click(function() {
		    $(this).closest("form").attr("action", "../return/cancel2.php");       
	});
});
</script>
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
<?php
if($_GET["isReserved"]){
	foreach($_GET["idx"] as $a)
	{
		list($product_id, $title, $active) = getProductInfo($a);
		echo "<div style='color: red'>" . $title . " is already reserved... cannot place a second reservation until the first reservation is cancelled or returned.</div>";
	}
}
if($_GET["notReserved"]){
	foreach($_GET["idx"] as $a)
	{
		list($product_id, $title, $active) = getProductInfo($a);
		echo "<div style='color: red'>I hate to tell you this... but " . $title . " isn't reserved or checked out, so you cannot return or cancel this reservation.</div>";
	}
}
//Get all products that do not have a reservation
$products = seeReservedAndNonProducts();
if($products){
	echo "<form action='reserve.php' method='GET'>";
	echo "<span class='tableCaption'>Reserve an item</span>";
	echo "<table class='reserveTable1'><thead><tr><td></td><td>Item</td><td>Status</td><td>Start Date</td><td>End Date</td><td>Rentor</td><td>Delivery</td></tr></thead>";
	echo "<tbody>";
	$i = 1;
	foreach($products as $a){
		echo "<tr class='" . ($a["status"] ? "reserved" : ($i % 2 == 0 ? "row1" : "row2")) . "'>";
		echo "<td><input type='checkbox' name='idx[]' value='" . $a["product_id"] . "'></td>";
		echo "<td><a href='../catalog/view.php?idx=" . $a["product_id"] . "'>" . $a["title"] . "</a></td>";
		echo "<td>" . $a["status"] . "</td>";
		echo "<td>" . $a["start_date"] . "</td>";
		echo "<td>" . $a["end_date"] . "</td>";
		echo "<td>" . $a["name"] . "</td>";
		echo "<td>" . $a["method"] . "</td>";
		echo "<td><a href='#'>+2</a></td>";
		echo "</tr>";
		$i++;
	}
	echo "</tbody></table>";
	echo "<input type='submit' value='Reserve Item(s)' id='reserveButton'>";
	echo "<input type='submit' value='Return Item(s)' id='returnButton'>";
	echo "<input type='submit' value='Cancel Reservation(s)' id='cancelButton'>";
	echo "</form>";
}
else{
	echo "Nothing exists in the catalog 001!<br>";
}

if($_GET["test"]){
	makeNearestActive(4);
}
?>
		</div>
            </div>

            <footer>
                <?php include '../../includes/footer.php'; ?>
            </footer>
        </div>


    </body>
</html>
