<?php
require_once 'Database.php';


class User {
    private $db;

    public function __construct($db) {
        $this->db = $db; 
    }

    public function register($name, $surname, $email, $password) {        

        // Валідація імені
        if (!preg_match('/^[a-zA-Zа-яА-ЯёЁіІїЇєЄґҐ]{2,50}$/u', $name)) {
            return "Ім’я повинне містити лише літери та бути від 2 до 50 символів";
        }

        // Валідація прізвища
        if (!preg_match('/^[a-zA-Zа-яА-ЯёЁіІїЇєЄґҐ]{2,50}$/u', $surname)) {
            return "Прізвище повинне містити лише літери та бути від 2 до 50 символів";
        }
        // Валідація email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Поле має відповідати формату email";
        }
        if (preg_match('/[А-Яа-яЁёЇїІіЄєҐґ]/u', $email)) {
            return "Email повинен містити лише латинські літери";
        }
        if (strlen($email) > 256) {
            return "Email не може містити більше 256 символів";
        }        

        // Валідація пароля
        if (strlen($password) < 8) {
            return "Пароль має містити щонайменше 8 символів";
        }
        if (strlen($password) > 64) {
            return "Пароль не може містити більше 64 символів";
        }

        // Перевірка, чи існує емейл
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $existingClient = $stmt->fetch();

        if ($existingClient) {
            return "Користувач з такою поштою вже існує";
        }

        // Хешування пароля
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Додавання клієнта
        $stmt = $this->db->prepare("INSERT INTO Users (name, surname, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $surname, $email, $hashedPassword]);

        $userId = $this->db->lastInsertId();

        // Створення сесії
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;

        return "Реєстрація успішна.";
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $client = $stmt->fetch();

        if (!$client || !password_verify($password, $client['password'])) {
            return "Невірна пошта або пароль.";
        }

        $_SESSION['user_id'] = $client['id_user'];
        $_SESSION['user_name'] = $client['name'];
        return "Успішний вхід.";
    }

    public function logout() {
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}
