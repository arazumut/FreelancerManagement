<?php
class Review {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Değerlendirme ekle
    public function addReview($data) {
        $this->db->query('INSERT INTO reviews (user_id, target_user_id, project_id, rating, comment) 
                          VALUES(:user_id, :target_user_id, :project_id, :rating, :comment)');
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':target_user_id', $data['target_user_id']);
        $this->db->bind(':project_id', $data['project_id']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':comment', $data['comment']);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Kullanıcının bir projeyi değerlendirip değerlendirmediğini kontrol et
    public function checkUserReview($user_id, $target_user_id, $project_id) {
        $this->db->query('SELECT * FROM reviews 
                          WHERE user_id = :user_id 
                          AND target_user_id = :target_user_id 
                          AND project_id = :project_id');
        
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':target_user_id', $target_user_id);
        $this->db->bind(':project_id', $project_id);
        
        $this->db->execute();
        
        // Eğer satır sayısı 0'dan büyükse, kullanıcı zaten değerlendirme yapmış
        return $this->db->rowCount() > 0;
    }

    // Kullanıcının aldığı tüm değerlendirmeleri getir
    public function getUserReviews($target_user_id) {
        $this->db->query('SELECT r.*, u.name as reviewer_name, p.title as project_title, p.slug as project_slug 
                          FROM reviews r 
                          JOIN users u ON r.user_id = u.id 
                          JOIN projects p ON r.project_id = p.id 
                          WHERE r.target_user_id = :target_user_id 
                          ORDER BY r.created_at DESC');
        
        $this->db->bind(':target_user_id', $target_user_id);
        
        return $this->db->resultSet();
    }

    // Kullanıcının yaptığı tüm değerlendirmeleri getir
    public function getReviewsByUser($user_id) {
        $this->db->query('SELECT r.*, u.name as target_name, p.title as project_title, p.slug as project_slug 
                          FROM reviews r 
                          JOIN users u ON r.target_user_id = u.id 
                          JOIN projects p ON r.project_id = p.id 
                          WHERE r.user_id = :user_id 
                          ORDER BY r.created_at DESC');
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->resultSet();
    }

    // Projeye yapılan tüm değerlendirmeleri getir
    public function getProjectReviews($project_id) {
        $this->db->query('SELECT r.*, u.name as reviewer_name, t.name as target_name 
                          FROM reviews r 
                          JOIN users u ON r.user_id = u.id 
                          JOIN users t ON r.target_user_id = t.id 
                          WHERE r.project_id = :project_id 
                          ORDER BY r.created_at DESC');
        
        $this->db->bind(':project_id', $project_id);
        
        return $this->db->resultSet();
    }

    // ID'ye göre değerlendirme getir
    public function getReviewById($id) {
        $this->db->query('SELECT r.*, u.name as reviewer_name, t.name as target_name, p.title as project_title 
                          FROM reviews r 
                          JOIN users u ON r.user_id = u.id 
                          JOIN users t ON r.target_user_id = t.id 
                          JOIN projects p ON r.project_id = p.id 
                          WHERE r.id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    // Değerlendirme güncelle
    public function updateReview($data) {
        $this->db->query('UPDATE reviews 
                          SET rating = :rating, 
                              comment = :comment
                          WHERE id = :id AND user_id = :user_id');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':comment', $data['comment']);
        
        return $this->db->execute();
    }

    // Değerlendirme sil
    public function deleteReview($id, $user_id) {
        $this->db->query('DELETE FROM reviews WHERE id = :id AND user_id = :user_id');
        
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->execute();
    }

    // Admin için değerlendirme sil
    public function adminDeleteReview($id) {
        $this->db->query('DELETE FROM reviews WHERE id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    // Kullanıcının ortalama puanını getir
    public function getUserAverageRating($user_id) {
        $this->db->query('SELECT AVG(rating) as average FROM reviews WHERE target_user_id = :user_id');
        
        $this->db->bind(':user_id', $user_id);
        
        $result = $this->db->single();
        
        return $result->average ? round($result->average, 1) : 0;
    }

    // Tüm değerlendirmeleri getir (Admin için)
    public function getAllReviews() {
        $this->db->query('SELECT r.*, u.name as reviewer_name, t.name as target_name, p.title as project_title 
                          FROM reviews r 
                          JOIN users u ON r.user_id = u.id 
                          JOIN users t ON r.target_user_id = t.id 
                          JOIN projects p ON r.project_id = p.id 
                          ORDER BY r.created_at DESC');
        
        return $this->db->resultSet();
    }

    // Değerlendirme sayısını getir
    public function getReviewCount() {
        $this->db->query('SELECT COUNT(*) as count FROM reviews');
        $result = $this->db->single();
        return $result->count;
    }
}
?> 