<?php
session_start();
require_once 'Database.php';
require_once 'Challenge.php';

if (!isset($_POST['challenge_id'], $_POST['action'])) {
    header('Location: sentchallenge.php');
    exit();
}

$challengeId = (int) $_POST['challenge_id'];
$action = $_POST['action'];

$challenge = new Challenge($db);

if ($action === 'confirm') {
    $challenge->updateChallengeStatus($challengeId, 5);
    $_SESSION['message'] = "Челендж підтверджено як виконаний";
} elseif ($action === 'reject') {
    $challenge->updateChallengeStatus($challengeId, 6);
    $_SESSION['message'] = "Докази відхилено, челендж позначено як не виконаний";
}

header('Location: sentchallenge.php');
exit();