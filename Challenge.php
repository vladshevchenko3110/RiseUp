<?php
require_once 'Database.php';

class Challenge {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUserIdByEmail($email) {
        $stmt = $this->db->prepare("SELECT id_user FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn(); 
    }

    public function sendChallenge($senderId, $receiverId, $title, $description) {
        $stmt = $this->db->prepare("
            INSERT INTO Challenges (id_sender, id_receiver, id_status, title, description) 
            VALUES (?, ?, 1, ?, ?) ");
        return $stmt->execute([$senderId, $receiverId, $title, $description]);
    }

    public function getReceivedChallenges($receiverId) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name, u.surname, u.email
            FROM Challenges c
            JOIN Users u ON c.id_sender = u.id_user
            WHERE c.id_receiver = :receiverId AND c.id_status = 1
            ORDER BY c.creation_date DESC
        ");
        $stmt->execute(['receiverId' => $receiverId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateChallengeStatus($challengeId, $newStatusId) {
        $stmt = $this->db->prepare("UPDATE Challenges SET id_status = :status WHERE id_challenge = :id");
        return $stmt->execute(['status' => $newStatusId, 'id' => $challengeId]);
    }

    public function getAcceptedChallenges($receiverId) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name, u.surname, u.email, s.status_name
            FROM Challenges c
            JOIN Users u ON c.id_sender = u.id_user
            JOIN Status s ON c.id_status = s.id_status
            WHERE c.id_receiver = :receiverId AND c.id_status = 2
            ORDER BY c.creation_date DESC
        ");
        $stmt->execute(['receiverId' => $receiverId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSentChallenges($senderId) {
        $stmt = $this->db->prepare("
            SELECT 
                c.*, 
                u.name, 
                u.surname, 
                u.email, 
                s.status_name,
                p.text AS proof,
                p.file_path AS proof_file
            FROM Challenges c
            JOIN Users u ON c.id_receiver = u.id_user
            JOIN Status s ON c.id_status = s.id_status
            LEFT JOIN Proofs p ON c.id_challenge = p.id_challenge
            WHERE c.id_sender = :senderId AND c.id_status IN (1, 2, 4)
            ORDER BY c.creation_date DESC
        ");
        $stmt->execute(['senderId' => $senderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserStatistics($userId) {
        $stmtUser = $this->db->prepare("SELECT name, surname, email FROM Users WHERE id_user = ?");
        $stmtUser->execute([$userId]);
        $userData = $stmtUser->fetch(PDO::FETCH_ASSOC);
    
        $stmtDone = $this->db->prepare("SELECT COUNT(*) FROM Challenges WHERE id_receiver = ? AND id_status = 5");
        $stmtDone->execute([$userId]);
        $done = $stmtDone->fetchColumn();
    
        $stmtFailed = $this->db->prepare("SELECT COUNT(*) FROM Challenges WHERE id_receiver = ? AND id_status = 6");
        $stmtFailed->execute([$userId]);
        $failed = $stmtFailed->fetchColumn();
    
        $stmtSent = $this->db->prepare("SELECT COUNT(*) FROM Challenges WHERE id_sender = ?");
        $stmtSent->execute([$userId]);
        $sent = $stmtSent->fetchColumn();
    
        return [
            'name' => $userData['name'],
            'surname' => $userData['surname'],
            'email' => $userData['email'],
            'done' => $done,
            'failed' => $failed,
            'sent' => $sent
        ];
    }

    public function getAcceptedChallengesForStats($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                c.*, 
                u.name, 
                u.surname, 
                u.email, 
                s.status_name AS status_name,
                p.text AS proof,
                p.file_path AS proof_file
            FROM Challenges c
            JOIN Users u ON c.id_sender = u.id_user
            JOIN Status s ON c.id_status = s.id_status
            LEFT JOIN Proofs p ON c.id_challenge = p.id_challenge
            WHERE c.id_receiver = ? AND c.id_status IN (5,6)
            ORDER BY c.creation_date DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSentChallengesForStats($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                c.*, 
                u.name AS receiver_name, 
                u.surname AS receiver_surname, 
                u.email AS receiver_email, 
                s.status_name AS status_name,
                p.text AS proof,
                p.file_path AS proof_file
            FROM Challenges c
            JOIN Users u ON c.id_receiver = u.id_user
            JOIN Status s ON c.id_status = s.id_status
            LEFT JOIN Proofs p ON c.id_challenge = p.id_challenge
            WHERE c.id_sender = ? AND c.id_status IN (3,5,6)
            ORDER BY c.creation_date DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>