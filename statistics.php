<?php

session_start();
require_once 'Database.php';
require_once 'Challenge.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$challenge = new Challenge($db);
$acceptedChallenges = $challenge->getAcceptedChallengesForStats($_SESSION['user_id']);

$sentChallenges = $challenge->getSentChallengesForStats($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика та моніторинг</title>
    <link rel="stylesheet" href="css/rstyles.css">
    <link rel="icon" type="image/x-icon" href="images/fire.ico">
    <style>
        .challenge-columns {
            display: flex;
            gap: 0px;
            justify-content: space-around;
            flex-wrap: wrap;
            
        }
        .challenge-column {
            flex: 1;
            max-width: 700px;
            
        }
    </style>
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
            <section class="challenge-statistics">
                <?php
                    $userId = $_SESSION['user_id'];
                    $stats = $challenge->getUserStatistics($userId);
                ?>

                <section class="statistics">
                    <h2>Статистика користувача</h2>
                    <p><strong>Ім’я:</strong><span><?= htmlspecialchars($stats['name']) ?></span></p>
                    <p><strong>Прізвище:</strong><span><?= htmlspecialchars($stats['surname']) ?></span></p>
                    <p><strong>Email:</strong><span><?= htmlspecialchars($stats['email']) ?></span></p>
                    <p><strong>Виконані челенджі:</strong> <span><?= $stats['done'] ?></span></p>
                    <p><strong>Невиконані челенджі:</strong> <span><?= $stats['failed'] ?></span></p>
                    <p><strong>Надіслані челенджі:</strong> <span><?= $stats['sent'] ?></span></p>
                </section>

            </section>
            <div class="challenge-columns">
            
                <section class="challenge-column">
                    <section class="challenge-list_received">
                        <h2 class="challenge-list-title">Завершені прийняті челенджі</h2>
                        <div class="challenge-list-scrollable">
                            <?php if (empty($acceptedChallenges)): ?>
                                <p>У вас ще немає прийнятих челенджів</p>
                            <?php else: ?>
                                <?php foreach ($acceptedChallenges as $c): ?>
                                    <div class="challenge-item_received"
                                        data-title="<?= htmlspecialchars($c['title']) ?>"
                                        data-description="<?= htmlspecialchars($c['description']) ?>"
                                        data-sender="<?= htmlspecialchars($c['name'] . ' ' . $c['surname']) ?>"
                                        data-email="<?= htmlspecialchars($c['email']) ?>"
                                        data-id="<?= $c['id_challenge'] ?>"
                                        data-status="<?= htmlspecialchars($c['status_name']) ?>"
                                        data-proof="<?= htmlspecialchars($c['proof'] ?? '') ?>"
                                        data-file="<?= htmlspecialchars($c['proof_file'] ?? '') ?>">
                                        <h3><span><?= htmlspecialchars($c['title']) ?></span></h3>
                                        <p><strong>Від:</strong><span><?= htmlspecialchars($c['name']) . ' ' . htmlspecialchars($c['surname']) ?> (<?= htmlspecialchars($c['email']) ?>)</span> </p>
                                        <p><strong>Статус:</strong><span><?= htmlspecialchars($c['status_name']) ?></span> </p>
                                        <p><strong>Дата отримання:</strong><span><?= date('d.m.Y H:i', strtotime($c['creation_date'])) ?></span> </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    </section>
                    
                </section>

                <section class="challenge-column">
                
                    <section class="challenge-list_received">
                        <h2 class="challenge-list-title">Завершені надіслані челенджі</h2>
                        <div class="challenge-list-scrollable">
                            <?php if (empty($sentChallenges)): ?>
                                <p>Ви ще не надіслали жодного челенджу</p>
                            <?php else: ?>
                                <?php foreach ($sentChallenges as $c): ?>
                                    <div class="challenge-item_received"
                                        data-title="<?= htmlspecialchars($c['title']) ?>"
                                        data-description="<?= htmlspecialchars($c['description']) ?>"
                                        data-sender="<?= htmlspecialchars($c['receiver_name'] . ' ' . $c['receiver_surname']) ?>"
                                        data-email="<?= htmlspecialchars($c['receiver_email']) ?>"
                                        data-id="<?= $c['id_challenge'] ?>"
                                        data-status="<?= htmlspecialchars($c['status_name']) ?>"
                                        data-proof="<?= htmlspecialchars($c['proof'] ?? '') ?>"
                                        data-file="<?= htmlspecialchars($c['proof_file'] ?? '') ?>">
                                        <h3><span><?= htmlspecialchars($c['title']) ?></span></h3>
                                        <p><strong>Отримувач:</strong><span><?= htmlspecialchars($c['receiver_name']) . ' ' . htmlspecialchars($c['receiver_surname']) ?> (<?= htmlspecialchars($c['receiver_email']) ?>)</span> </p>
                                        <p><strong>Статус:</strong><span><?= htmlspecialchars($c['status_name']) ?></span></p>
                                        <p><strong>Дата відправлення:</strong><span><?= date('d.m.Y H:i', strtotime($c['creation_date'])) ?></span> </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </section>
                    
                </section>
            </div>
            
        </main>
        <div class="modal_received" id="modal" style="display:none">
            <div class="modal-content_accepted">
                <span class="modal-close_received" id="closeModal">&times;</span>

                <p><strong>Ім’я та прізвище:</strong> <span id="modalSender"></span></p>
                <p><strong>Email:</strong> <span id="modalEmail"></span></p>
                <p><strong>Назва челенджу:</strong> <span id="modalTitle"></span></p>
                <p><strong>Опис:</strong> <span id="modalDescription"></span></p>
                <p><strong>Статус челенджу:</strong> <span id="modalStatus"></span></p>
                <p><strong>Доказ:</strong> <span id="modalProof"></span></p>
                <p><strong>Фото/відео:</strong> </p><span id="modalFileLink"></span>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
    </div>
    <div class="modal_received" style="align-items: center;" id="mediaModal">
        <div class="modal-content_accepted" >
            <span class="modal-close_received" id="closeMediaModal">&times;</span>
            <div id="mediaContainer"></div>
        </div>
    </div>                               
    <script>
        const modal = document.getElementById('modal');
        const closeModalBtn = document.getElementById('closeModal');
        const challengeItems = document.querySelectorAll('.challenge-item_received');
        
        const mediaModal = document.getElementById('mediaModal');
        const closeMediaModal = document.getElementById('closeMediaModal');
        const mediaContainer = document.getElementById('mediaContainer');

        challengeItems.forEach(item => {
            item.addEventListener('click', () => {

                const proof = item.dataset.proof?.trim();
                const file = item.dataset.file?.trim();

                document.getElementById('modalTitle').textContent = item.dataset.title;
                document.getElementById('modalDescription').textContent = item.dataset.description;
                document.getElementById('modalSender').textContent = item.dataset.sender;
                document.getElementById('modalEmail').textContent = item.dataset.email;
                document.getElementById('modalStatus').textContent = item.dataset.status;  
                
                const proofTextElem = document.getElementById('modalProof');
                proofTextElem.textContent = proof ? proof : '—';

                const fileLinkElem = document.getElementById('modalFileLink');
                fileLinkElem.innerHTML = '';

                if (file) {
                    fileLinkElem.innerHTML = `<button id="openMediaBtn" class="custom-file-upload" >Переглянути</button>`;
                } else {
                    fileLinkElem.textContent = 'Фото або відео не додано';
                }
                modal.style.display = 'flex';

                setTimeout(() => {
                const openMediaBtn = document.getElementById('openMediaBtn');
                if (openMediaBtn) {
                    openMediaBtn.addEventListener('click', (e) => {
                        e.stopPropagation(); 
                        mediaContainer.innerHTML = '';
                        const ext = file.split('.').pop().toLowerCase();
                        if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                            mediaContainer.innerHTML = `<img src="${file}" style="max-width: 100%; max-height: 500px;">`;
                        } else if (['mp4', 'webm'].includes(ext)) {
                            mediaContainer.innerHTML = `<video controls src="${file}" style="max-width: 100%; max-height: 500px;"></video>`;
                        } else {
                            mediaContainer.innerHTML = '<p>Формат медіа не підтримується</p>';
                        }
                        mediaModal.style.display = 'flex';
                    });
                }
            }, 0);
            });
        });

        closeMediaModal.addEventListener('click', () => {
            mediaModal.style.display = 'none';
        });

        closeModalBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    </script>
   
</body>
</html>