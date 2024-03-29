<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
$db = getDB();
if (isset($_POST["join"])) {
    $score = getScore();
    //prevent user from joining expired or paid out comps
    $stmt = $db->prepare("select fee, participants, reward, from competitions where id = :id && expires > current_timestamp && paid_out = 0");
    $r = $stmt->execute([":id" => $_POST["cid"]]);
    if ($r) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $fee = (int)$result["fee"];
            if ($score >= $fee) {
                $stmt = $db->prepare("INSERT INTO usercompetitions(competition_id, user_id) VALUES(:cid, :uid)");
                $r = $stmt->execute([":cid" => $_POST["cid"], ":uid" => get_user_id()]);
                if ($r) {
                    flash("Successfully join competition", "success");
                    // continue from here i need to update the competition table and increase the particiapant and reward by 1 also the distribution
                    // of the reward will take place in this so i need to be careful with the table
                    // checked the score already so just insert the participants and reward into competition
                    $participant = $result["participants"] + 1;
                    $reward = $result["reward"] + 1;
                    $stmt = $db->prepare("UPDATE Competitions set participants = :p, reward = :r where id = :cid");
                    $r = $stmt ->execute([":p" => $participant, ":r" => $reward, ":cid" => $_POST["cid"]]);

                    die(header("Location: #"));
                }

                else {
                    flash("There was a problem joining the competition: " . var_export($stmt->errorInfo(), true), "danger");
                }
            }
            else {
                flash("You can't afford to join this competition, try again later", "warning");
            }

        }
        else {
            flash("Competition is unavailable", "warning");
        }
    }
    else {
        flash("Competition is unavailable", "warning");
    }
    // to update the participants



}
$stmt = $db->prepare("SELECT c.*, UC.user_id as reg FROM competitions c LEFT JOIN (SELECT * FROM usercompetitions where user_id = :id) as UC on c.id = UC.competition_id WHERE c.expires > current_timestamp AND paid_out = 0 ORDER BY expires ASC");
$r = $stmt->execute([":id" => get_user_id()]);
if ($r) {
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else {
    flash("There was a problem looking up competitions: " . var_export($stmt->errorInfo(), true), "danger");
}
?>

<h3>Competitions</h3>
<?php if (isset($results) && count($results)): ?>
<?php foreach ($results as $r): ?>
    <div>name: <?php safer_echo($r["name"]); ?></div>
    <div>Participants: <?php safer_echo($r["participants"]); ?></div>
    <div>Required Score: <?php safer_echo($r["min_score"]); ?></div>
    <div>Reward: <?php safer_echo($r["reward"]); ?></div>
    <div>1 st place: <?php safer_echo($r["first_place_per"]); ?></div>
    <div>2 nd place: <?php safer_echo($r["second_place_per"]); ?></div>
    <div>3 rd place: <?php safer_echo($r["third_place_per"]); ?></div>
    <div>expires: <?php safer_echo($r["expires"]); ?></div>

<?php if ($r["reg"] != get_user_id()): ?>
<form method="POST">
    <input type="hidden" name="cid" value="<?php safer_echo($r["id"]); ?>"/>
    <input type="submit" name="join" class="btn btn-primary"
           value="Join (Cost: <?php safer_echo($r["fee"]); ?>)"/>
</form>
<?php else: ?>
Already Registered
            <div> <br></div>
<?php endif; ?>
<?php endforeach; ?>
<?php else: ?>
No competitions available right now
<?php endif; ?>
<?php require(__DIR__ . "/partials/flash.php");
