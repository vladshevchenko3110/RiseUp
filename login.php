<?php
    session_start();

    require_once 'User.php';
    require_once 'Database.php';

    if (isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }

    

    $message = "";
    $email = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $client = new User($db);
        $message = $client->login($_POST['email'], $_POST['password']);
        

        if ($message === "Успішний вхід.") {
            header('Location: index.php');
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизація</title>
    <link rel="stylesheet" href="css/rstyles.css">
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
                <h2>Авторизація</h2>
                <?php if ($message): ?>
                    <div class="message <?= strpos($message, 'Успішний') !== false ? '' : 'error' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <label for="email">Пошта</label>
                    <input type="text" id="email" name="email" value="<?= htmlspecialchars($email) ?>">

                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" >

                    <button type="submit">Увійти</button>
                    <a href="register.php">Ще не маєте акаунту? Зареєструйтесь</a>
                </form>
            </div>

        </div>
        

        <!-- <?php include 'includes/footer.php'; ?> -->
    </div>
    
</body>
</html>