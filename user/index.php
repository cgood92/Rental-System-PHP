<?php

session_start();

if (file_exists('../model.php')) {
    require_once '../model.php';
} else {
    header('location: /ftc/errordocs/500.php');
    exit;
}

if (file_exists('../../library/validation.php')) {
    require_once '../../library/validation.php';
} else {
    header('location: /../ftc/errordocs/500.php');
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
$sql = getAllRentalUsers();
if($sql){
	echo "<table class='catalogTable1'>";
	echo "<thead><tr><td>Name</td><td>Email</td><td>Phone</td></tr></thead>";
	echo "<tbody>";
	foreach($sql as $a)
	{
		echo "<tr class='" . ($i % 2 == 0 ? "1" : "2") . "'>";
		echo "<td><a href='update.php?user_idx=" . $a["user_id"] . "'>" . $a["lname"] . ", " . $a["fname"] . "</a></td>";
		echo "<td>" . $a["email"] . "</td>";
		echo "<td>" . $a["phone"] . "</td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
}
else{
	echo "There are currently no registered users for the rental system in the database right now";
}
?>
		</div>
            </div>

            <footer>
                <?php include '../../includes/footer.php'; ?>
            </footer>
        </div>

        <?php include '../../includes/script_files.php'; ?>

    </body>
</html>
