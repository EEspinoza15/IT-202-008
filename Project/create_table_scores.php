<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

 <form method="POST"> 
<!--
	<label>Name</label>
	<input name="name" placeholder="Name"/>
	<label>State</label>
	<select name="state">
		<option value="0">Incubating</option>
		<option value="1">Hatching</option>
		<option value="2">Hatched</option>
		<option value="3">Expired</option>
	</select>
	<label>Base Rate</label>
	<input type="number" min="1" name="base_rate"/>
	<label>Mod Min</label>
	<input type="number" min="1" name="mod_min"/>
	<label>Mod Max</label>
	<input type="number" min="1" name="mod_max"/>
	<input type="submit" name="save" value="Create"/>
-->

	<label>scores</label>
	<input type="number" min="0" name="score"/>
	<input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	/*
	$name = $_POST["name"];
	$state = $_POST["state"];
	$br = $_POST["base_rate"];
	$min = $_POST["mod_min"];
	$max = $_POST["mod_max"];
	$nst = date('Y-m-d H:i:s');//calc
	*/
	//commented out the above
	
	//$id = get_id();
	$user = get_user_id();
	$score=$_POST["score"];
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO scores (user_id,score) VALUES(:user,:score)");
	$r = $stmt->execute([
		":user"=>$user,
		":score"=>$score,
	]);
	if($r){
		flash("Created successfully with id: " . $db->lastInsertId());
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating: " . var_export($e, true));
	}
}
?>
<?php require(__DIR__ . "/partials/flash.php");
