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
<?php
list($user_id, $fname, $lname, $email, $phone) = getRentalUser($_GET["user_idx"]);
?>
<div><a href="index.php">Return to Rental Index</a></div>
<form action="update2.php" method="GET" class="catalogForm">
<fieldset><legend>Update User Info</legend>
<?php
if($_GET["newuser"]){
	echo "<div style='color: blue'>Looks like this is a new user.  Please fill in their information below</div>";
}
?>
Requestor BYU-I Email: <input type="text" name="email" id="email" value="<?php echo $email; ?>"><br>
<p>First Name: <input type="text" name="fname" value="<?php echo $fname; ?>"></p>
<p>Last Name: <input type="text" name="lname" value="<?php echo $lname; ?>"></p>
<p>Phone: <input type="text" name="phone" id="phone" value="<?php echo $phone; ?>"></p>
<p><input type="submit" value="Update User"></p>
<input type="hidden" name="idx" value="<?php echo $_GET["user_idx"]; ?>">
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
