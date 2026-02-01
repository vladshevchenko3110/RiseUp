<?php

session_start();
require_once 'Database.php';
require_once 'Challenge.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$challenge = new Challenge($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senderId = $_SESSION['user_id'];
    $email = trim($_POST['email']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);    
    $receiverId = $challenge->getUserIdByEmail($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Поле має відповідати формату email";
    } elseif (strlen($email) > 256) {
        $message = "Неможливо ввести більше 256 символів";
    } elseif ($receiverId == $senderId) {
        $message = "Ви не можете надіслати челендж самі собі";
    } else {
        
        if (!$receiverId) {
            $message = "Користувача з таким email не знайдено";
        } elseif (strlen($title) < 1) {
            $message = "Будь ласка, введіть назву челенджу";
        } elseif (strlen($description) < 10) {
            $message = "Будь ласка, введіть детальніший опис челенджу";
        } else {
            if ($challenge->sendChallenge($senderId, $receiverId, $title, $description)) {
                $message = "Челендж успішно надіслано";
                $email = '';
                $title = '';
                $description = '';
            } else {
                $message = "Виникла помилка при надсиланні";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Створення челенджу</title>
    <link rel="stylesheet" href="css/rstyles.css">
    <link rel="icon" type="image/x-icon" href="images/fire.ico">
</head>
<body>
    <div class="fire-background">
        <?php for ($i = 0; $i < 100; $i++): ?>
            <div class="fire-spark" style="
                left: <?= rand(0, 100) ?>vw;
                animation-delay: <?= rand(0, 3) ?>s;
                animation-duration: <?= rand(6, 14) ?>s;
                background: radial-gradient(circle, rgba(255,<?= rand(50,150) ?>,0,1) 0%, rgba(255,0,0,0.6) 100%);
                width: <?= rand(3, 6) ?>px;
                height: <?= rand(3, 6) ?>px;
            "></div>
        <?php endfor; ?>
    </div>
    <div class="wrapper">
        <?php include 'includes/header.php'; ?>
           
        <main class="content">        

            <section class="form-container1">
                <h2>Створити челендж</h2>

                <?php if ($message): ?>
                    <div class="message <?= strpos($message, 'успішно') !== false ? '' : 'error' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <label for="email">Електронна пошта отримувача</label>
                    <input type="text" name="email" maxlength="256"  
                        title="Поле має відповідати формату email" 
                        value="<?= htmlspecialchars($email ?? '') ?>" 
                        oninput="validateEmail(this)" />

                    <label for="title">Назва челенджу</label>
                    <input type="text" name="title" maxlength="64" 
                        value="<?= htmlspecialchars($title ?? '') ?>" 
                        oninput="this.value = this.value.slice(0, 64)" />

                    <label for="description">Опис челенджу</label>
                    <textarea name="description" maxlength="1000" rows="8" 
                            oninput="this.value = this.value.slice(0, 1000)"><?= htmlspecialchars($description ?? '') ?></textarea>

                    <button type="submit">Надіслати челендж</button>
                </form>
            </section>
        </main>
   
        <?php include 'includes/footer.php'; ?>
    </div>
    
   
</body>
</html>