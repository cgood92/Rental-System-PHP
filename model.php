<?php

if (file_exists('../connections/ftc-pdo.php')) {
    require_once '../connections/ftc-pdo.php';
} 

if (file_exists('../library/validation.php')) {
    require_once '../library/validation.php';
} 

function getAllProducts(){
	global $dbconn;

	$sql = "SELECT * 
		FROM rental_products 
		WHERE 1
		ORDER BY active DESC
		, title ASC";

	$stmt = $dbconn->prepare($sql);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();

	return $result;
}

function addProduct($title, $creator, $note = NULL){
	global $dbconn;

	$dbconn->beginTransaction();
	try {
	$sql = "INSERT INTO rental_products
		(`title`
		, active
		, created_by)
		VALUES
		(:title
		, 1
		, :creator)";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':title', $title);
	$stmt->bindValue(':creator', $creator);
	$stmt->execute();
	$stmt->closeCursor();

	//Get last insert ID for note
	$product_id = $dbconn->lastInsertID();

	}
	catch(PDOException $e)
	{
		//echo $e->getMessage();
		return -1;
	}

	try
	{
	$sql = "INSERT INTO rental_product_notes
		(product_id
		, note
		, created_by)
		VALUES
		(:product_id
		, :note
		, :creator)";
	echo "$sql\n$product_id\n$note\n$creator";
	$stmt2 = $dbconn->prepare($sql);
	$stmt2->bindValue(':product_id', (int) $product_id);
	$stmt2->bindValue(':note', $note);
	$stmt2->bindValue(':creator', $creator);
	$stmt2->execute();
	$stmt2->closeCursor();	
	}
	catch(PDOException $e)
	{
		//echo $e->getMessage();
		return -2;
	}
	$dbconn->commit();

	return 1;
}


function getProductInfo($idx){
	global $dbconn;

	$sql = "SELECT product_id, `title`, active FROM rental_products WHERE product_id = :idx";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':idx', (int)$idx);
	$stmt->execute();
	$results = $stmt->fetch();
	$stmt->closeCursor();
	return $results;
}

function getProductNote($idx){
	global $dbconn;

	$sql = "SELECT rental_note_id, note FROM rental_product_notes WHERE product_id = :idx";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':idx', (int)$idx);
	$stmt->execute();
	$results = $stmt->fetch();
	$stmt->closeCursor();
	return $results;
}

function updateProduct($idx, $title, $active, $note, $note_idx, $creator){
	global $dbconn;

	$dbconn->beginTransaction();
	//Update Product
	try{
	$sql = "UPDATE rental_products 
		SET `title` = :title
		, active = :active 
		WHERE product_id=:idx 
		LIMIT 1;";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':idx', (int)$idx);
	$stmt->bindValue(':title', $title);
	$stmt->bindValue(':active', (int)$active);
	$stmt->execute();
	$stmt->closeCursor();
	}
	catch(PDOException $e)
	{
		//echo $e->getMessage();
		return -1;
	}

	//Does note exist? If not, create it
	//Note exists, so update it
	if($note_idx)
	{
		//Update note
		try{
		$sql = "UPDATE rental_product_notes 
			SET `note` = :note
			WHERE product_id=:idx
			AND rental_note_id=:note_idx
			LIMIT 1;";
		$stmt = $dbconn->prepare($sql);
		$stmt->bindValue(':idx', (int)$idx);
		$stmt->bindValue(':note', $note);
		$stmt->bindValue(':note_idx', (int)$note_idx);
		$stmt->execute();
		$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			//echo $e->getMessage();
			return -1;
		}
	}
	elseif($note)
	{
		//Insert note
		try{
			$sql = "INSERT INTO rental_product_notes
				VALUES(
					NULL
					, :product_idx
					, :note
					, :created_by
					, CURRENT_TIMESTAMP()
				);";
		$stmt = $dbconn->prepare($sql);
		$stmt->bindValue(':product_idx', (int)$idx);
		$stmt->bindValue(':note', $note);
		$stmt->bindValue(':created_by', (int)$creator);
		$stmt->execute();
		$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			//echo $e->getMessage();
			return -1;
		}
	}

	$dbconn->commit();

	return 1;
}

function showTable($table)
{
	global $dbconn;

	$sql = "SELECT * FROM $table";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':table', $table);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();

	print_r($result);
}

function seeReservedAndNonProducts(){
	global $dbconn;

	$sql = "
		SELECT 
			rp.product_id
			, rp.title
			, r.user_id
			, CONCAT(ru.lname, ', ', ru.fname) AS name
			, DATE_FORMAT(ri.start_date, '%b %e, %Y (%a)') AS start_date
			, DATE_FORMAT(ri.end_date, '%b %e, %Y (%a)') AS end_date
			, (SELECT common_lookup_meaning FROM common_lookup
				WHERE common_lookup_id = ris.status
				AND common_lookup_column = 'STATUS'
				AND common_lookup_table = 'RENTAL_ITEM_STATUS') AS status
			, (SELECT common_lookup_meaning FROM common_lookup
				WHERE common_lookup_id = rid.method
				AND common_lookup_column = 'METHOD'
				AND common_lookup_table = 'RENTAL_ITEM_DELIVERY') AS method
			, rid.location
			, rsr.note
			FROM rental_products rp
				LEFT JOIN rental_item ri ON rp.product_id = ri.product_id AND ri.rental_item_id IN 
					(SELECT rental_item_id 
					FROM rental_item_status ris 
					WHERE ris.active = 1
					AND ris.rental_item_id = ri.rental_item_id)
				LEFT JOIN rental r ON r.rental_id = ri.rental_id
				LEFT JOIN rental_users ru ON ru.user_id = r.user_id
				LEFT JOIN rental_item_status ris ON ris.rental_item_id = ri.rental_item_id AND ris.active=1
				LEFT JOIN rental_item_delivery rid ON rid.rental_item_id = ri.rental_item_id
				LEFT JOIN rental_special_requests rsr ON rsr.rental_id = r.rental_id
				ORDER BY status ASC
				, rp.title ASC
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();
	return $result;
}

function getAllRentalUsers(){
	global $dbconn;

	$sql = "SELECT user_id
			, IFNULL(fname, '<em>&#60;First Name&#62;</em>') AS fname
			, IFNULL(lname, '<em>&#60;Last Name&#62;</em>') AS lname
			, email
			, IFNULL(phone, 'No Phone Listed') AS phone
		FROM rental_users
		WHERE 1
		ORDER BY lname ASC, fname ASC
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();
	return $result;
}

function getRentalUser($user_id){
	global $dbconn;

	$sql = "SELECT user_id
			, fname
			, lname
			, email
			, phone
		FROM rental_users
		WHERE user_id = :user_id
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':user_id', (int)$user_id);
	$stmt->execute();
	$result = $stmt->fetch();
	$stmt->closeCursor();
	return $result;
}

function getAllNonReservedProducts(){
	global $dbconn;

	$sql = "
SELECT rp.title
, rp.product_id
FROM rental_products rp
WHERE rp.product_id NOT IN
	(SELECT ri.product_id
	FROM rental_item ri
	LEFT JOIN rental_item_status ris ON ri.rental_item_id = ris.rental_item_id
    WHERE ri.product_id = rp.product_id
    AND ris.status != (SELECT common_lookup_id FROM common_lookup WHERE common_lookup_table='RENTAL_ITEM' AND common_lookup_code='RCLOSED')
    AND ris.status != (SELECT common_lookup_id FROM common_lookup WHERE common_lookup_table='RENTAL_ITEM' AND common_lookup_code='CANCELLED')
	);
		
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();
	return $result;
}

function getDeliveryModes(){
	global $dbconn;

	$sql = "SELECT common_lookup_id as idx
		, common_lookup_meaning as meaning
		, common_lookup_code as code
		FROM common_lookup
		WHERE COMMON_LOOKUP_TABLE = 'RENTAL_ITEM_DELIVERY'
		AND COMMON_LOOKUP_COLUMN = 'METHOD'
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();
	return $result;
}

function reserveProducts(
	$idxArray
	, $start_date
	, $end_date
	, $delivery
	, $location
	, $email
	, $note
	, $emp_sysid)
{
	global $dbconn;

	$dbconn->beginTransaction();

	//Validate user
	$sql = "SELECT user_id FROM rental_users
	       WHERE email= :email";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':email', $email);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();

	if($result){
		$user_id = $result[0]["user_id"];
	}
	//User does not exist, so create it
	else{
		$sql = "INSERT INTO rental_users
			(email
			, created_by)
			VALUES
			(:email
			, :created_by);";
		$stmt = $dbconn->prepare($sql);
		$stmt->bindValue(':email', $email);
		$stmt->bindValue(':created_by', $emp_sysid);
		$stmt->execute();
		$stmt->closeCursor();

		//Return that means send to create_user page
		$return = -3;
		$user_id = $dbconn->lastInsertID();
	}

	//Insert into rental table (parent)
	try {
		$sql = "INSERT INTO rental
			(user_id
			, created_by)
			VALUES
			(:user_id
			, :created_by);
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':user_id', $user_id);
	$stmt->bindValue(':created_by', $emp_sysid);
	$stmt->execute();
	$stmt->closeCursor();

	//Get last insert ID for note
	$rental_id = $dbconn->lastInsertID();
	}
	catch(PDOException $e)
	{
		//echo $e->getMessage();
		return -1;
	}

	foreach($idxArray as $a)
	{
		//Insert into rental_item (sibling)
		try {
			$sql = "INSERT INTO rental_item
				(rental_id
				, product_id
				, start_date
				, end_date
				, created_by)
				VALUES 
				(:rental_id
				, :product_id
				, :start_date
				, :end_date
				, :created_by);
			";
			$sql_test = str_replace(array(
				":rental_id"
				, ":product_id"
				, ":start_date"
				, ":end_date"
				, ":created_by"
			), array($rental_id, $a, $start_date, $end_date, $emp_sysid), $sql);
		$stmt = $dbconn->prepare($sql);
		$stmt->bindValue(':rental_id', $rental_id);
		$stmt->bindValue(':product_id', $a);
		$stmt->bindValue(':start_date', $start_date);
		$stmt->bindValue(':end_date', $end_date);
		$stmt->bindValue(':created_by', $emp_sysid);
		$stmt->execute();
		$stmt->closeCursor();

		//Get last insert ID for note
		$rental_item_id = $dbconn->lastInsertID();

		}
		catch(PDOException $e)
		{
			//echo $e->getMessage();
			return -1;
		}

		//Rental Item Status -------------------------------------------
		insertNewStatus($a, $rental_item_id, $emp_sysid);


		//Delivery
		try {
			$sql = "INSERT INTO rental_item_delivery
				(rental_item_id
				, rental_id
				, method
				, location
				, created_by)
				VALUES 
				(:rental_item_id
				, :rental_id
				, :method
				, :location
				, :created_by);
			";
			$sql_test = str_replace(array(
				":rental_item_id"
				, ":rental_id"
				, ":method"
				, ":location"
				, ":created_by"
				), array($rental_item_id, $rental_id, $delivery, $location, $emp_sysid), $sql);
		$stmt = $dbconn->prepare($sql);
		$stmt->bindValue(':rental_item_id', $rental_item_id);
		$stmt->bindValue(':rental_id', $rental_id);
		$stmt->bindValue(':method', $delivery);
		if($location == "")
			$location = "NULL";
		$stmt->bindValue(':location', $location);
		$stmt->bindValue(':created_by', $emp_sysid);
		$stmt->execute();
		$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			//echo $e->getMessage();
			return -1;
		}
	}
	//Special Requests
	try {
		$sql = "INSERT INTO rental_special_requests
			(rental_id
			, note
			, created_by)
			VALUES
			(:request_id
			, :rental_id
			, :note
			, :created_by);
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':rental_id', $rental_id);
	$stmt->bindValue(':note', $note);
	$stmt->bindValue(':created_by', $emp_sysid);
	$stmt->execute();
	$stmt->closeCursor();
	}
	catch(PDOException $e)
	{
		//echo $e->getMessage();
		return -1;
	}

	$dbconn->commit();
	return 1;
}

function isUserCreated($email){
	global $dbconn;

	$sql = "SELECT user_id, fname, lname FROM rental_users WHERE email= :email
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':email', $email);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();
	if($result[0]["fname"] || $result[0]["lname"]){
		return 0;
	}
	else{
		return $result[0]["user_id"];
	}
}

function getRentalStatus($idx){
	global $dbconn;

	$sql = "
		SELECT cl.common_lookup_meaning 
		FROM rental_item_status ris
		INNER JOIN common_lookup cl ON cl.common_lookup_id = ris.status
		WHERE ris.product_id = :product_id
		ORDER BY creation_date DESC
		LIMIT 1
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':product_id', $idx);
	$stmt->execute();
	$result = $stmt->fetch();
	$stmt->closeCursor();
	return $result[0];
}

function getRentalHistory($idx){
	global $dbconn;

	$sql = "
		SELECT cl.common_lookup_meaning AS `status`
		, CONCAT(e.last_name, ', ', e.first_name) AS employee_name
		, IFNULL(DATE_FORMAT(ris.creation_date, '%M %e, %Y, %r'), 'N/A') AS creation_date
		, r.user_id 
                , IFNULL(CONCAT(ru.lname, ', ', ru.fname), '<NO NAME>') AS rental_user_name
		, IFNULL(DATE_FORMAT(ri.start_date, '%M %e, %Y'), 'N/A') AS start_date
		, IFNULL(DATE_FORMAT(ri.end_date, '%M %e, %Y'), 'N/A') AS end_date
		, IFNULL(DATE_FORMAT(r.creation_date, '%M %e, %Y, %r'), 'NULL') AS rental_creation_date
		, CONCAT(e2.last_name, ', ', e2.first_name) AS rental_created_by
		, r.rental_id
		FROM rental_item_status ris
		INNER JOIN rental_item ri ON ri.rental_item_id = ris.rental_item_id
		INNER JOIN rental r ON r.rental_id = ri.rental_id
                INNER JOIN rental_users ru ON ru.user_id = r.user_id
		INNER JOIN common_lookup cl ON cl.common_lookup_id = ris.status 
		INNER JOIN system_user su ON su.system_user_id = ris.created_by
		INNER JOIN employee e ON e.employee_id = su.employee_id
		INNER JOIN system_user su2 ON su2.system_user_id = r.created_by
		INNER JOIN employee e2 ON e2.employee_id = su2.employee_id
		WHERE ris.product_id = :product_id
		ORDER BY ris.creation_date DESC 
		LIMIT 25				
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':product_id', $idx);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();
	return $result;
} 
function updateUser($user_id, $email, $fname, $lname, $phone){
	global $dbconn;

	$sql = "UPDATE rental_users
		SET email= :email
		, fname= :fname
		, lname= :lname
		, phone= :phone
		WHERE user_id= :user_id
		LIMIT 1
		";
	$stmt = $dbconn->prepare($sql);
	//$sql_test = str_replace(array(":user_id", ":email", ":fname", ":lname", ":phone"), array($user_id, $email, $fname, $lname, $phone), $sql);
	//echo "\n" . $sql_test;
	$stmt->bindValue(':user_id', $user_id);
	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':fname', $fname);
	$stmt->bindValue(':lname', $lname);
	$stmt->bindValue(':phone', $phone);
	$stmt->execute();
	$stmt->closeCursor();
	return 1;
}

function cancelItem($idx, $sysid){
	global $dbconn;

	setInactiveStatus($idx);
	$sql = "
		INSERT INTO rental_item_status
		(status_id
		, active
		, rental_item_id
		, product_id
		, status
		, created_by
		, creation_date)
		VALUES(
			NULL
			, 0 
			, (SELECT rental_item_id
			  FROM rental_item
			  WHERE product_id = :idx
			  ORDER BY rental_item_id DESC
			  LIMIT 1)
			, :idx
			, (SELECT common_lookup_id 
			  FROM common_lookup
			  WHERE common_lookup_table = 'RENTAL_ITEM_STATUS'
			  AND common_lookup_column = 'STATUS'
			  AND common_lookup_code = 'CANCELLED')
			, :created_by
			, NULL
		);
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':idx', $idx);
	$stmt->bindValue(':created_by', $sysid);
	$stmt->execute();
	$stmt->closeCursor();
	return 1;
}

function returnItem($idx, $sysid){
	global $dbconn;

	setInactiveStatus($idx);
	$sql = "
		INSERT INTO rental_item_status
		(status_id
		, active
		, rental_item_id
		, product_id
		, status
		, created_by
		, creation_date)
		VALUES(
			NULL
			, 0 
			, (SELECT rental_item_id
			  FROM rental_item
			  WHERE product_id = :idx
			  ORDER BY rental_item_id DESC
			  LIMIT 1)
			, :idx
			, (SELECT common_lookup_id 
			  FROM common_lookup
			  WHERE common_lookup_table = 'RENTAL_ITEM_STATUS'
			  AND common_lookup_column = 'STATUS'
			  AND common_lookup_code = 'RETURNED')
			, :created_by
			, NULL
		);
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':idx', $idx);
	$stmt->bindValue(':created_by', $sysid);
	$stmt->execute();
	$stmt->closeCursor();
	return 1;
}

function setInactiveStatus($product_id){
	global $dbconn;
		try {
			$sql = "
				UPDATE rental_item_status SET
				active = 0
				WHERE product_id = :product_id
				AND active = 1
			";
			$stmt = $dbconn->prepare($sql);
			$stmt->bindValue(':product_id', $product_id);
			$stmt->execute();
			$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			//echo $e->getMessage();
			return -1;
		}
		
	return 1;
}

function deleteProduct($product_id){
	global $dbconn;
		try {
			$sql = "
				DELETE FROM rental_products
				WHERE product_id = :product_id
				LIMIT 1 
				";
			$stmt = $dbconn->prepare($sql);
			$stmt->bindValue(':product_id', $product_id);
			$stmt->execute();
			$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			//echo $e->getMessage();
			return -1;
		}
		
	return 1;
}

function insertNewStatus($product_id, $rental_item_id, $emp_sysid){
	global $dbconn;
		try {
			$sql2 = "
				SELECT status_id FROM rental_item_status
				WHERE product_id = :product_id
				AND active=1
				";
			$stmt = $dbconn->prepare($sql2);
			$stmt->bindValue(':product_id', $product_id);
			$stmt->execute();
			if($stmt->fetchAll()){
				$active = 0;
			}
			else{
				setInactiveStatus($product_id);
				$active = 1;
			}

			$sql = "INSERT INTO rental_item_status
				(`active`
				,rental_item_id
				, product_id
				, status
				, created_by)
				VALUES 
				(
				:active
				, :rental_item_id
				, :product_id
				, (SELECT common_lookup_id FROM common_lookup
				WHERE common_lookup_table='RENTAL_ITEM_STATUS'
				AND common_lookup_column = 'STATUS'
				AND common_lookup_code = 'RESERVED')
				, :created_by);
			";
			$sql_test = str_replace(array(":rental_item_id", ":created_by", ":product_id"), array($rental_item_id, $emp_sysid, $product_id), $sql);
			echo $sql_test . "\n";
			$stmt = $dbconn->prepare($sql);
			$stmt->bindValue(':rental_item_id', $rental_item_id);
			$stmt->bindValue(':created_by', $emp_sysid);
			$stmt->bindValue(':product_id', $product_id);
			$stmt->bindValue(':active', $active);
			$stmt->execute();
			$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			//echo $e->getMessage();
			return -1;
		}
}

function getReservations($idx){
	global $dbconn;

	$sql = "
		SELECT cl.common_lookup_meaning AS `status`
		, CONCAT(e.last_name, ', ', e.first_name) AS employee_name
		, IFNULL(DATE_FORMAT(ris.creation_date, '%M %e, %Y, %r'), 'N/A') AS creation_date
		, r.user_id 
                , IFNULL(CONCAT(ru.lname, ', ', ru.fname), '<NO NAME>') AS rental_user_name
		, IFNULL(DATE_FORMAT(ri.start_date, '%M %e, %Y'), 'N/A') AS start_date
		, IFNULL(DATE_FORMAT(ri.end_date, '%M %e, %Y'), 'N/A') AS end_date
		, IFNULL(DATE_FORMAT(r.creation_date, '%M %e, %Y, %r'), 'NULL') AS rental_creation_date
		, CONCAT(e2.last_name, ', ', e2.first_name) AS rental_created_by
		, r.rental_id
		FROM rental_item_status ris
		INNER JOIN rental_item ri ON ri.rental_item_id = ris.rental_item_id
		INNER JOIN rental r ON r.rental_id = ri.rental_id
                INNER JOIN rental_users ru ON ru.user_id = r.user_id
		INNER JOIN common_lookup cl ON cl.common_lookup_id = ris.status 
		INNER JOIN system_user su ON su.system_user_id = ris.created_by
		INNER JOIN employee e ON e.employee_id = su.employee_id
		INNER JOIN system_user su2 ON su2.system_user_id = r.created_by
		INNER JOIN employee e2 ON e2.employee_id = su2.employee_id
		WHERE ris.product_id = :product_id
                AND ri.start_date >= UTC_DATE()
                AND cl.common_lookup_meaning NOT IN ('Cancelled', 'Returned')
		ORDER BY ris.creation_date DESC 
		LIMIT 25		
		";
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':product_id', $idx);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();
	return $result;
} 

function makeNearestActive($product_id){
	global $dbconn;

	setInactiveStatus($idx);
	$sql = "
SET @rental_item_id = (SELECT rental_item_id FROM rental_item WHERE product_id = :product_id AND start_date > UTC_DATE() ORDER BY start_date ASC LIMIT 1);
		UPDATE rental_item_status
		SET active = 
			CASE
			WHEN rental_item_id = @rental_item_id THEN 1
			ELSE 0
		END
		WHERE product_id = :product_id;		
		";
	$sql_test = str_replace(array(":product_id"), array($product_id), $sql);
	echo $sql_test;
	exit;
	$stmt = $dbconn->prepare($sql);
	$stmt->bindValue(':product_id', $product_id);
	$stmt->execute();
	$stmt->closeCursor();
	return 1;
}

?>
