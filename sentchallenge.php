<?php
session_start();
require_once 'Database.php';
require_once 'Challenge.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$challenge = new Challenge($db);

$sentChallenges = $challenge->getSentChallenges($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Надіслані челенджі</title>
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
                <h2 class="challenge-list-title">Надіслані челенджі</h2>

                <div class="challenge-list-scrollable">
                    <?php if (empty($sentChallenges)): ?>
                        <p>Ви ще не надіслали жодного челенджу</p>
                    <?php else: ?>
                        <?php foreach ($sentChallenges as $c): ?>
                            <div class="challenge-item_received"
                                data-title="<?= htmlspecialchars($c['title']) ?>"
                                data-description="<?= htmlspecialchars($c['description']) ?>"
                                data-receiver="<?= htmlspecialchars($c['name'] . ' ' . $c['surname']) ?>"
                                data-email="<?= htmlspecialchars($c['email']) ?>"
                                data-id="<?= $c['id_challenge'] ?>"
                                data-status-id="<?= $c['id_status'] ?>"
                                data-status-name="<?= htmlspecialchars($c['status_name']) ?>"
                                data-proof="<?= htmlspecialchars($c['proof'] ?? '') ?>"
                                data-file="<?= htmlspecialchars($c['proof_file'] ?? '') ?>">
                                <h3><span><?= htmlspecialchars($c['title']) ?></span></h3>
                                <p><strong>Для:</strong><span><?= htmlspecialchars($c['name']) . ' ' . htmlspecialchars($c['surname']) ?> (<?= htmlspecialchars($c['email']) ?>)</span> </p>
                                <p><strong>Статус:</strong><span><?= htmlspecialchars($c['status_name']) ?></span> </p>
                                <p><strong>Дата відправлення:</strong><span><?= date('d.m.Y H:i', strtotime($c['creation_date'])) ?></span> </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>

    <div class="modal_received" id="modal">
        <div class="modal-content_accepted" id="modal-cont">
            <span class="modal-close_received" id="closeModal">&times;</span>

            <p><strong>Ім’я та прізвище отримувача:</strong> <span id="modalReceiver"></span></p>
            <p><strong>Email отримувача:</strong> <span id="modalEmail"></span></p>
            <p><strong>Назва челенджу:</strong> <span id="modalTitle"></span></p>
            <p><strong>Опис:</strong> <span id="modalDescription"></span></p>
            <p><strong>Статус:</strong> <span id="modalStatus"></span></p>

            <p><strong>Доказ:</strong> <span id="modalProofText"></span></p>
            <p><strong>Фото/відео:</strong> </p><span id="modalFileLink"></span>

            <form method="post" class="modal-actions" action="process_sent_action.php" id="proofForm">
                <input type="hidden" name="challenge_id" id="modalChallengeId">
                <button type="submit" name="action" value="confirm" class="accept-btn" id="acceptBtn" style="display:none;">Прийняти докази</button>
                <button type="submit" name="action" value="reject" class="reject-btn" id="rejectBtn" style="display:none;">Відхилити докази</button>
            </form>
        </div>
    </div>
    <div class="modal_received" style="align-items: center;" id="mediaModal">
        <div class="modal-content_accepted" >
            <span class="modal-close_received" id="closeMediaModal">&times;</span>
            <div id="mediaContainer"></div>
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
        const acceptBtn = document.getElementById('acceptBtn');
        const rejectBtn = document.getElementById('rejectBtn');
        const mediaModal = document.getElementById('mediaModal');
        const closeMediaModal = document.getElementById('closeMediaModal');
        const mediaContainer = document.getElementById('mediaContainer');

        challengeItems.forEach(item => {
            item.addEventListener('click', () => {
                const proof = item.dataset.proof?.trim();
                const file = item.dataset.file?.trim();

                document.getElementById('modalReceiver').textContent = item.dataset.receiver;
                document.getElementById('modalEmail').textContent = item.dataset.email;
                document.getElementById('modalTitle').textContent = item.dataset.title;
                document.getElementById('modalDescription').textContent = item.dataset.description;
                document.getElementById('modalStatus').textContent = item.dataset.statusName;
                document.getElementById('modalChallengeId').value = item.dataset.id;

                const proofTextElem = document.getElementById('modalProofText');
                proofTextElem.textContent = proof ? proof : '—';

                const fileLinkElem = document.getElementById('modalFileLink');
                fileLinkElem.innerHTML = '';

                if (file) {
                    fileLinkElem.innerHTML = `<button id="openMediaBtn" class="custom-file-upload" >Переглянути</button>`;
                } else {
                    fileLinkElem.textContent = 'Фото або відео не додано';
                }

                if (item.dataset.statusId === '4') {
                    acceptBtn.style.display = 'inline-block';
                    rejectBtn.style.display = 'inline-block';
                } else {
                    acceptBtn.style.display = 'none';
                    rejectBtn.style.display = 'none';
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

        closeModalBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        closeMediaModal.addEventListener('click', () => {
            mediaModal.style.display = 'none';
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