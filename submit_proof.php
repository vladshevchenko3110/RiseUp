<?php
session_start();
require_once 'Database.php';
require_once 'Challenge.php';

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $challengeId = $_POST['challenge_id'];
    $proofText = trim($_POST['proof_text']);
    $file = $_FILES['proof_file'] ?? null;

    $old = [
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'sender' => $_POST['sender'] ?? '',
        'email' => $_POST['email'] ?? '',
        'proof_text' => $proofText
    ];

    if (empty($proofText)) {
        $errors['proof_text'] = 'Надайте текстові докази';
    } elseif (strlen($proofText) > 1000) {
        $errors['proof_text'] = 'Текст не повинен перевищувати 1000 символів';
    }

    $filePath = null;
    if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed = ['image/jpeg', 'image/png', 'video/mp4'];
        if (!in_array($file['type'], $allowed)) {
            $errors['proof_file'] = 'Підтримуються лише .jpg, .png, .mp4';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filePath = 'uploads/' . uniqid() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], $filePath);
        }
    }

    if ($errors) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $old;
        $_SESSION['modal_challenge_id'] = $challengeId;
        header('Location: acceptedchallenge.php');
        exit();
    }

    $stmt = $db->prepare("INSERT INTO Proofs (id_challenge, text, file_path) VALUES (?, ?, ?)");
    $stmt->execute([$challengeId, $proofText, $filePath]);

    $challenge = new Challenge($db);
    $challenge->updateChallengeStatus($challengeId, 4);

    $_SESSION['message'] = "Докази надіслано автору челенджу";
    header('Location: acceptedchallenge.php');
    exit();
}