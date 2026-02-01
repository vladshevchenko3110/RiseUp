<?php
session_start();
require_once 'User.php';
require_once 'Database.php';
    if (isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }

    $message = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $client = new User($db);
        $message = $client->register(
            $_POST['name'], 
            $_POST['surname'],
            $_POST['email'], 
            $_POST['password']            
        );

        if ($message === "Реєстрація успішна.") {
            header("Location: index.php");
            exit;
        }
    
    }
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реєстрація</title>
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
        <div class="content">
            <div class="form-container">
                <h2>Реєстрація</h2>
                <?php if ($message): ?>
                    <div class="message <?= strpos($message, 'успішна') !== false ? '' : 'error' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <label for="name">Ім'я</label>
                    <input type="text" id="name" name="name" 
                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

                    <label for="surname">Прізвище</label>
                    <input type="text" id="surname" name="surname" 
                        value="<?= htmlspecialchars($_POST['surname'] ?? '') ?>">

                    <label for="email">Пошта</label>
                    <input type="text" id="email" name="email" 
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>                    

                    <button type="submit">Зареєструватись</button>
                    <a href="login.php">Вже маєте акаунт? Увійдіть</a>
                </form>
            </div>
        </div>       

        <!-- <?php include 'includes/footer.php'; ?> -->
    </div>
    
</body>
</html>

