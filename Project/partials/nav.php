<link rel="stylesheet" href="static/css/styles.css">
<?php
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav>
<ul class="nav">
    <li><a href="home.php">Home</a></li>
    <?php if (!is_logged_in()): ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    <?php endif; ?>
    <?php if(HAS_ROLE("Admin")): ?>
        <li><a href = "create_table_scores.php"> Create Score</a></li>
        <li><a href = "test_list_scores.php"> List Score</a></li>
        <li><a href = "test_create_scorehistory.php"> Create Score History </a></li>
        <li><a href = "test_list_scorehistory.php"> List score History </a> </li>
    <?php endif; ?>
    <?php if (is_logged_in()): ?>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="pong.html">PlayGame</a></li>
    <?php endif; ?>
</ul>
</nav>
