<?php

$response = ["status"=>400, "message"=>"Invalid request"];
if(isset($_POST["score"])){
    session_start();
    require(__DIR__ . "/../lib/helpers.php");
    $user = get_user_id();
    $score = $_POST["score"];
    //TODO save in DB
    $sql = "INSERT INTO scores (user_id, score) VALUES (?,?)";
    //init a statement "object"
    $db = getDB();
    //prepare the pdo
    $stmt = $db->prepare($sql);
    //bind the values to pass in (sanitizes)
    //executes everything
    $retVal = $stmt->execute([$user, $score]);

    //$retVal = sql_query($db, $pdo);
    if($retVal){
        $response["status"]= 200;
        $response["message"] = "Recorded score of $score for user $user";
    }
    else{
        //echo sql_error_info($db);
        $response["message"] = var_export($stmt->errorInfo(),true);
    }
    
}
echo json_encode($response);
?>
