<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Kullanıcı kaydı
    public function register($data) {
        // SQL sorgusu
        $this->db->query('INSERT INTO users (name, email, password, role) VALUES(:name, :email, :password, :role)');
        
        // Parametreleri bağla
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);

        // Çalıştır
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // E-posta ile kullanıcı bul
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Kayıt var mı kontrol et
        if($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // ID ile kullanıcı bul
    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        return $row;
    }

    // Kullanıcı bilgilerini güncelle
    public function updateUser($data) {
        // SQL sorgusu - şifre değişirse
        if(!empty($data['password'])) {
            $this->db->query('UPDATE users SET name = :name, email = :email, password = :password, 
                              bio = :bio, skills = :skills, portfolio = :portfolio WHERE id = :id');
            $this->db->bind(':password', $data['password']);
        } else {
            $this->db->query('UPDATE users SET name = :name, email = :email, 
                              bio = :bio, skills = :skills, portfolio = :portfolio WHERE id = :id');
        }
        
        // Parametreleri bağla
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':bio', $data['bio'] ?? '');
        $this->db->bind(':skills', $data['skills'] ?? '');
        $this->db->bind(':portfolio', $data['portfolio'] ?? '');

        // Çalıştır
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Tüm freelancer'ları getir
    public function getAllFreelancers() {
        $this->db->query('SELECT * FROM users WHERE role = "freelancer" ORDER BY name ASC');
        return $this->db->resultSet();
    }

    // Tüm işverenleri getir
    public function getAllEmployers() {
        $this->db->query('SELECT * FROM users WHERE role = "employer" ORDER BY name ASC');
        return $this->db->resultSet();
    }

    // Tüm kullanıcıları getir (admin için)
    public function getAllUsers() {
        $this->db->query('SELECT * FROM users ORDER BY name ASC');
        return $this->db->resultSet();
    }

    // Kullanıcı sil
    public function deleteUser($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Kullanıcı oturum açma
    public function login($email, $password) {
        $user = $this->findUserByEmail($email);

        if($user && password_verify($password, $user->password)) {
            return $user;
        } else {
            return false;
        }
    }

    // Kullanıcı değerlendirme ortalamasını getir
    public function getUserRating($user_id) {
        $this->db->query('SELECT AVG(rating) as average FROM reviews WHERE target_user_id = :user_id');
        $this->db->bind(':user_id', $user_id);
        $result = $this->db->single();
        return $result->average ? round($result->average, 1) : 0;
    }

    // Kullanıcı için e-posta tercihlerini oluştur
    public function createEmailPreferences($user_id) {
        $this->db->query('INSERT INTO email_preferences (user_id) VALUES(:user_id)');
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    // E-posta tercihlerini al
    public function getEmailPreferences($user_id) {
        $this->db->query('SELECT * FROM email_preferences WHERE user_id = :user_id');
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    // E-posta tercihlerini güncelle
    public function updateEmailPreferences($data) {
        $this->db->query('UPDATE email_preferences SET 
                          new_bid = :new_bid, 
                          bid_accepted = :bid_accepted, 
                          bid_rejected = :bid_rejected, 
                          new_message = :new_message, 
                          project_completed = :project_completed 
                          WHERE user_id = :user_id');
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':new_bid', $data['new_bid'] ? 1 : 0);
        $this->db->bind(':bid_accepted', $data['bid_accepted'] ? 1 : 0);
        $this->db->bind(':bid_rejected', $data['bid_rejected'] ? 1 : 0);
        $this->db->bind(':new_message', $data['new_message'] ? 1 : 0);
        $this->db->bind(':project_completed', $data['project_completed'] ? 1 : 0);

        return $this->db->execute();
    }
}
?> 