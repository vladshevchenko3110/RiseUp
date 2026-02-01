<?php
session_start();
require_once 'Database.php';
require_once 'Challenge.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$challenge = new Challenge($db);
$acceptedChallenges = $challenge->getAcceptedChallenges($_SESSION['user_id']);

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
$modalChallengeId = $_SESSION['modal_challenge_id'] ?? null;

unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['modal_challenge_id']);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Прийняті челенджі</title>
    <link rel="stylesheet" href="css/rstyles.css">
    <link rel="icon" type="image/x-icon" href="images/fire.ico">
</head>
<body>
    <div class="fire-background">
        <?php for ($i = 0; $i < 100; $i++): ?>
            <div class="fire-spark" style="
                left: <?= rand(0, 100) ?>vw;
                animation-delay: <?= rand(0, 10) ?>s;
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
                <h2 class="challenge-list-title">Прийняті челенджі</h2>

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
                                data-id="<?= $c['id_challenge'] ?>">
                                <h3><span><?= htmlspecialchars($c['title']) ?></span></h3>
                                <p><strong>Від:</strong> <span><?= htmlspecialchars($c['name']) . ' ' . htmlspecialchars($c['surname']) ?> (<?= htmlspecialchars($c['email']) ?>)</span></p>
                                <p><strong>Дата отримання:</strong><span><?= date('d.m.Y H:i', strtotime($c['creation_date'])) ?></span> </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <div class="modal_received" id="modal" style="<?= $modalChallengeId ? 'display: flex;' : '' ?>">
            <div class="modal-content_accepted" >
                <span class="modal-close_received" id="closeModal">&times;</span>
                <form method="post" action="submit_proof.php" enctype="multipart/form-data">
                    <input type="hidden" name="challenge_id" id="modalChallengeId" value="<?= htmlspecialchars($modalChallengeId ?? '') ?>">

                    <p><strong>Ім’я та прізвище відправника:</strong> <span id="modalSender"><?= htmlspecialchars($old['sender'] ?? '') ?></span></p>
                    <p><strong>Email відправника:</strong> <span id="modalEmail"><?= htmlspecialchars($old['email'] ?? '') ?></span></p>
                    <p><strong>Назва челенджу:</strong> <span id="modalTitle"><?= htmlspecialchars($old['title'] ?? '') ?></span></p>
                    <p><strong>Опис:</strong> <span id="modalDescription"><?= htmlspecialchars($old['description'] ?? '') ?></span></p>

                    <div class="label-error-wrapper">
                        <label for="proof_text">Доказ виконання:</label>
                        <?php if (!empty($errors['proof_text'])): ?>
                        <div class="error-right"><?= htmlspecialchars($errors['proof_text']) ?></div>
                        <?php endif; ?>
                    </div>
                    <textarea name="proof_text" id="proof_text"  maxlength="1000" rows="8" ><?= htmlspecialchars($old['proof_text'] ?? '') ?></textarea>
                    

                    <div class="label-error-wrapper">
                        <label for="proof_file">Фото/відео (не обов’язково):</label>
                        <?php if (!empty($errors['proof_file'])): ?>
                        <div class="error-right"><?= htmlspecialchars($errors['proof_file']) ?></div>
                        <?php endif; ?>
                    </div>
                    <label class="custom-file-upload"> Обрати файл <input type="file" name="proof_file" id="proof_file" accept=".jpg,.png,.mp4">               
                    </label><span id="file-name" style="margin-left:5px">Файл не обрано</span>               
                    

                    <div class="modal-actions">
                        <button type="submit" class="accept-btn">Надати докази</button>
                        <button type="button" class="reject-btn" id="declineBtn">Відмовитись від челенджу</button>
                    </div>
                </form>

            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
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
        const fileInput = document.getElementById('proof_file');
        const fileNameSpan = document.getElementById('file-name');

        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                fileNameSpan.textContent = this.files[0].name;
            } else {
                fileNameSpan.textContent = 'Файл не вибрано';
            }
        });

        challengeItems.forEach(item => {
            item.addEventListener('click', () => {
                fillModal(item.dataset);
                modal.style.display = 'flex';
            });
        });

        document.getElementById('declineBtn')?.addEventListener('click', () => {
            const challengeId = document.getElementById('modalChallengeId')?.value;
            if (!challengeId) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'decline_challenge.php';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'challenge_id';
            input.value = challengeId;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        });

        function fillModal(data) {
            document.getElementById('modalTitle').textContent = data.title;
            document.getElementById('modalDescription').textContent = data.description;
            document.getElementById('modalSender').textContent = data.sender;
            document.getElementById('modalEmail').textContent = data.email;
            document.getElementById('modalChallengeId').value = data.id;

            const declineForm = modal.querySelector('form[action="decline_challenge.php"] input[name="challenge_id"]');
            if (declineForm) {
                declineForm.value = data.id;
            }
        }

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

        <?php if ($modalChallengeId): ?>
        document.addEventListener('DOMContentLoaded', () => {
            const item = document.querySelector(`.challenge-item_received[data-id="<?= $modalChallengeId ?>"]`);
            if (item) {
                fillModal(item.dataset);
            }
        });
        <?php endif; ?>

        
    </script>
</body>
</html>