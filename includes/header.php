<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<?php
session_start();

?>
<script>
        function confirmLogout(event) {
            event.preventDefault();
            document.getElementById("logoutModal").style.display = "block";
        }

        function logoutYes() {
            window.location.href = 'logout.php';
        }

        function logoutCancel() {
            document.getElementById("logoutModal").style.display = "none";
        }
</script>
<header>

    <div id="logoutModal" class="modal_exit">
        <div class="modal-content_exit">
            <p>Ви бажаєте вийти?</p>
            <div class="modal-buttons_exit">
            <button id="confirmYes" onclick="logoutYes()">Так</button>
            <button id="confirmCancel" onclick="logoutCancel()">Скасувати</button>
            </div>
        </div>
    </div>
    <a href="index.php" class="logo"><i class="fas fa-fire"></i>RiseUp</a>
    <nav>
        <ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="dropdown">
                <a href="#" class="dropbtn">Все для челенджів<i class="fas fa-chevron-down"></i> </a>
                <div class="dropdown-content">
                    <a href="createchallenge.php">Створити челендж</a>
                    <a href="receivedchallenge.php">Отримані челенджі</a>
                    <a href="sentchallenge.php">Надіслані челенджі</a>
                    <a href="acceptedchallenge.php">Прийняті челенджі</a>
                </div>
            </div>
            <a href="statistics.php">Статистика та моніторинг</a>
            <a href="#" onclick="confirmLogout(event)">Вийти</a>
        <?php else: ?>
            <a href="login.php">Увійти</a>
        <?php endif; ?>
        </ul>
    </nav>
</header>