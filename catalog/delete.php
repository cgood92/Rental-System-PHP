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
if(!$_GET["idx"])
{
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
<?php
if($_GET["error"])
{
	echo "<span class='error'>There was an error processing your request.</span>";
}
?>

<a href="index.php">Return to Catalog</a>
<form action="delete2.php" method="GET" class="catalogForm">
<fieldset><legend>Delete product</legend>
<div class='warningDelete'>
<p><b>Are you sure you want to delete the following product(s)?</b></p>
<ul>
<?php
foreach($_GET["idx"] as $a){
	list($idx, $title, $active) = getProductInfo($a);
	echo "<li><input type='hidden' name='idx[]' value='" . $idx . "'>" . $title . "</li>";
}
?>
</ul>
<input type='submit' value='Proceed to Delete'>
</div>
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
