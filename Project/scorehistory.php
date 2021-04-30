<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
$id = get_user_id();

// fetching
$result = [];
if(isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT user_id,score,Users.username FROM scores as Scores JOIN Users on Scores.user_id = Users.id where Scores.user_id = :id LIMIT 10");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>
<?php if (count($result) > 0): ?>
    <?php foreach ($result as $r): ?>
        <div style=" alignment: center ">
            <div>Score: <?php safer_echo($r["score"]); ?> </div>
        </div>
        <div style=" alignment: center ">
            <div>Owner: <?php safer_echo($r["username"]); ?></div>
        </div>
        <br>
    <?php endforeach; ?>
<?php else: ?>
    <p>No results</p>
<?php endif; ?>
<?php require(__DIR__ . "/partials/flash.php");
