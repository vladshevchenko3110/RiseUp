<?php
session_start();
require_once 'Database.php';
require_once 'Challenge.php';

if (!isset($_POST['challenge_id'], $_POST['action'])) {
    header('Location: received_challenges.php');
    exit();
}

$challengeId = (int) $_POST['challenge_id'];
$action = $_POST['action'];

$challenge = new Challenge($db);

if ($action === 'accept') {
    $challenge->updateChallengeStatus($challengeId, 2); 
    $_SESSION['message'] = "Челендж прийнято";
} elseif ($action === 'reject') {
    $challenge->updateChallengeStatus($challengeId, 3); 
    $_SESSION['message'] = "Челендж відхилено";
}

header('Location: receivedchallenge.php');
exit();