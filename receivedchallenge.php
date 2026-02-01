<?php

session_start();
require_once 'Database.php';
require_once 'Challenge.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$challenge = new Challenge($db);
$receivedChallenges = $challenge->getReceivedChallenges($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отримані челенджі</title>
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
           
        <main class="content" style="margin-top:10px">    
            <section class="challenge-list_received">
                <h2 class="challenge-list-title">Отримані челенджі</h2>

                <div class="challenge-list-scrollable">
                    <?php if (empty($receivedChallenges)): ?>
                        <p>Ви ще не отримали жодного челенджу</p>
                    <?php else: ?>
                        <?php foreach ($receivedChallenges as $c): ?>
                            <div class="challenge-item_received"
                                data-title="<?= htmlspecialchars($c['title']) ?>"
                                data-description="<?= htmlspecialchars($c['description']) ?>"
                                data-sender="<?= htmlspecialchars($c['name'] . ' ' . $c['surname']) ?>"
                                data-email="<?= htmlspecialchars($c['email']) ?>"
                                data-id="<?= $c['id_challenge'] ?>">
                                <h3><span><?= htmlspecialchars($c['title']) ?></span></h3>
                                <p><strong>Від:</strong><span><?= htmlspecialchars($c['name']) . ' ' . htmlspecialchars($c['surname']) ?> (<?= htmlspecialchars($c['email']) ?>)</span> </p>
                                <p><strong>Дата отримання:</strong><span><?= date('d.m.Y H:i', strtotime($c['creation_date'])) ?></span> </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
            
        </main>
   
        <?php include 'includes/footer.php'; ?>
    </div>
    
    <div class="modal_received" id="modal">
    <div class="modal-content_received" id="modal-cont">
        <span class="modal-close_received" id="closeModal">&times;</span>

        <p><strong>Ім’я та прізвище відправника:</strong> <span id="modalSender"></span></p>
        <p><strong>Email відправника:</strong> <span id="modalEmail"></span></p>
        <p><strong>Назва челенджу:</strong> <span id="modalTitle"></span></p>
        <p><strong>Опис:</strong> <span id="modalDescription"></span></p>

        <form method="post" class="modal-actions" action="process_challenge_action.php">
            <input type="hidden" name="challenge_id" id="modalChallengeId">
            <button type="submit" name="action" value="accept" class="accept-btn">Прийняти</button>
            <button type="submit" name="action" value="reject" class="reject-btn">Відхилити</button>
        </form>
    </div>
</div>
    
    <?php if (!empty($_SESSION['message'])): ?>
    <div class="message-box_received" id="messageBox">
        <?= htmlspecialchars($_SESSION['message']) ?>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <script>
        const modal = document.getElementById('modal');
        const closeModalBtn = document.getElementById('closeModal');
        const challengeItems = document.querySelectorAll('.challenge-item_received');

        challengeItems.forEach(item => {
            item.addEventListener('click', () => {
                document.getElementById('modalTitle').textContent = item.dataset.title;
                document.getElementById('modalDescription').textContent = item.dataset.description;
                document.getElementById('modalSender').textContent = item.dataset.sender;
                document.getElementById('modalEmail').textContent = item.dataset.email;
                document.getElementById('modalChallengeId').value = item.dataset.id;
                modal.style.display = 'flex';


                
            });
        });

        closeModalBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        const messageBox = document.getElementById('messageBox');
        if (messageBox) {
            setTimeout(() => {
                messageBox.style.opacity = '0';
                setTimeout(() => messageBox.remove(), 500);
            }, 3000);
        }

        
    </script>
   
</body>
</html>