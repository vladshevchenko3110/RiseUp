<?php
session_start();
require_once 'Database.php';
require_once 'Challenge.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['challenge_id'])) {
    $challengeId = $_POST['challenge_id'];

    $challenge = new Challenge($db);
    $challenge->updateChallengeStatus($challengeId, 6); 

    $_SESSION['message'] = "Ви відмовились від челенджу.";
    
}

header('Location: acceptedchallenge.php');
exit();