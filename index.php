<?php

session_start();

if (file_exists('model.php')) {
    require_once 'model.php';
} else {
    header('location: /ftc/errordocs/500.php');
    exit;
}

if (file_exists('../library/validation.php')) {
    require_once '../library/validation.php';
} else {
    header('location: /ftc/errordocs/500.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>FTC Website</title>
        <meta name="description" content="">
            
        <?php include '../includes/head.php'; ?>
<link rel="stylesheet" type="text/css" href="rental_style.css" />
    </head>

    <body id="home">

        <header>
            <?php include '../includes/header.php'; ?>
        </header>
        <div class="container-fluid">
            <div class="row-fluid">
                
                <?php include '../includes/leftnav.php'; ?>
		<div class="span9">
<div id="serviceMenu">
<ul>
<li><a href="catalog/">Product Rental Catalog</a>
	<ul>
	<li><a href="catalog/add.php">Add a product to the catalog</a>
	<li><a href="catalog/">View/Edit product catalog</a>
	</ul>
<li><a href="reserve/index.php">View all reservations</a>
<li><a href="user/index.php">View All Rental Users</a>
</ul>
</div>
<div id="all_products">
</div>
		</div>
            </div>

            <footer>
                <?php include '../includes/footer.php'; ?>
            </footer>
        </div>

        <?php include '../includes/script_files.php'; ?>

    </body>
</html>
