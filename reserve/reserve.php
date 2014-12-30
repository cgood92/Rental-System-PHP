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
<link rel="stylesheet" type="text/css" href="../rental_style.css" />
<script type="text/javascript">

</script>

    </head>

    <body id="home">

        <header>
            <?php include '../../includes/header.php'; ?>
        </header>
        <div class="container-fluid">
            <div class="row-fluid">
                
                <?php include '../../includes/leftnav.php'; ?>
		<div class="span9">
<div><a href="index.php">Return to Rental Index</a></div>
<form action="reserve2.php" method="GET" class="catalogForm">
<fieldset><legend>Reserving <?php 
echo "<u>" . count($_GET["idx"]) . "</u> item" . (count($_GET["idx"]) > 1 ? "s" : "");
?>...</legend>
<?php
foreach($_GET["idx"] as $a)
{
	list($idx, $title, $active) = getProductInfo($a);
	list($note_idx, $note) = getProductNote($a);
	echo "<p><span class='productLabel'>Product:</span> <span class='titleText'>" . $title . "</span></p>\n";
	echo "\t<input type='hidden' name='idx[]' value='" . $a . "'>\n";
	if($note)
	{
		echo "\t<ul class='arrowlist'><li><span class='noteLabel'>Notes:</span> <span class='noteText'>" . $note . "</span></li></ul>\n";
	}
}
?>
Requestor BYU-I Email: <input type="email" name="email" id="email" placeholder="sample0000@byui.edu" required><br>
<p>Start Date: <input type="date" name="sdate" required></p>
<p>Return Date: <input type="date" name="rdate" required></p>
<p>Delivery Mode: <select name="delivery" id="delivery">
<?php
$delivery = getDeliveryModes();
foreach($delivery as $a)
{
	echo "\n<option value='" . $a["idx"] . "' " . ($a["code"] == "NEEDSDELIVER" ? "id='OTHER'" : "") . ">" . $a["meaning"] . "</option>";
}
?>
<input id="location" name="location" type="text" placeholder="Location" />
			</select></p>
<p>Notes:<br>
<textarea name="note" style="width: 75%; min-height: 100px;" placeholder="Optional notes about special delivery, instruction, or other requests...">
</textarea>
</p>
<p><input type="submit" value="Reserve item(s)"></p>

</fieldset>
</form>


		</div>
            </div>

            <footer>
                <?php include '../../includes/footer.php'; ?>
            </footer>
        </div>

        <?php include '../../includes/script_files.php'; ?>

    </body>
</html>
