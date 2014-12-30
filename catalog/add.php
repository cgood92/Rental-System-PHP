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
if($_GET["success"])
{
	echo "<span class='success'>Item was added to catalog successfully!  Add another below, or <a href='index.php'>click here</a> to return.</span>";
}
if($_GET["error"])
{
	echo "<span class='error'>There was an error processing your request.</span>";
}
?>
<div><a href="index.php">Return to Catalog</a></div>
<form action="add2.php" method="GET" class="catalogForm">
<fieldset><legend>Add a new product</legend>
<table class="forms right">
	<tr><td><label for="title">Title:</label></td><td><input type="text" name="title" id="title" required></td></tr>
	<tr><td><label for="note">Special Notes:<br>(Optional)</label></td><td><textarea name="note" id="note" rows=5 style="width: 90%"></textarea></td></tr>
	<tr><td COLSPAN=2><input type="submit" value="Add item"></td></tr>
</table>
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
