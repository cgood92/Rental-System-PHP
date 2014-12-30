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
<form action='delete.php' method='GET'>
<?php
$products = getAllProducts();
if($products){
	echo "<table class='catalogTable1'><thead><tr><td></td><td>Title</td><td>Active</td></tr></thead>";
	echo "<tbody>";
	$i = 1;
	foreach($products as $a){
		echo "<tr class='" . (!$a["active"] ? "inactive" : "row") . ($i % 2 == 0 ? "1" : "2") . "'>";
		echo "<td style='width: 15px;'><input type='checkbox' name='idx[]' value='" . $a["product_id"] . "'></td>";
		echo "<td><a href='edit.php?idx=" . $a["product_id"] . "'>" . $a["title"] . "</a></td><td>" . ($a["active"] ? "Yes" : "No") . "</td></tr>";
		$i++;
	}
	echo "<tr class='tableFooter'><td COLSPAN=7><input type='submit' value='Delete Item(s)'></td></tr>";
	echo "</tbody></table>";
}
else{
	echo "Nothing exists in the catalog!<br>";
}
?>
</form>
<a href="add.php">Add a new product</a>
		</div>
            </div>

            <footer>
                <?php include '../../includes/footer.php'; ?>
            </footer>
        </div>

        <?php include '../../includes/script_files.php'; ?>

    </body>
</html>
