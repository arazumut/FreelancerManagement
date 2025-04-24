<?php
class Bid {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Teklif ekle
    public function addBid($data) {
        $this->db->query('INSERT INTO bids (project_id, user_id, description, amount, delivery_time) VALUES(:project_id, :user_id, :description, :amount, :delivery_time)');
        
        $this->db->bind(':project_id', $data['project_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':delivery_time', $data['delivery_time']);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Kullanıcının bir projeye teklif verip vermediğini kontrol et
    public function checkUserBid($project_id, $user_id) {
        $this->db->query('SELECT * FROM bids WHERE project_id = :project_id AND user_id = :user_id');
        
        $this->db->bind(':project_id', $project_id);
        $this->db->bind(':user_id', $user_id);
        
        $this->db->execute();
        
        // Eğer satır sayısı 0'dan büyükse, kullanıcı zaten teklif vermiş
        return $this->db->rowCount() > 0;
    }

    // Projenin tüm tekliflerini getir
    public function getProjectBids($project_id) {
        $this->db->query('SELECT b.*, u.name as freelancer_name, u.email as freelancer_email 
                          FROM bids b 
                          JOIN users u ON b.user_id = u.id 
                          WHERE b.project_id = :project_id 
                          ORDER BY b.created_at DESC');
        
        $this->db->bind(':project_id', $project_id);
        
        return $this->db->resultSet();
    }

    // Kullanıcının tüm tekliflerini getir
    public function getUserBids($user_id) {
        $this->db->query('SELECT b.*, p.title as project_title, p.slug as project_slug, p.status as project_status, u.name as employer_name 
                          FROM bids b 
                          JOIN projects p ON b.project_id = p.id 
                          JOIN users u ON p.user_id = u.id 
                          WHERE b.user_id = :user_id 
                          ORDER BY b.created_at DESC');
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->resultSet();
    }

    // ID'ye göre teklif getir
    public function getBidById($id) {
        $this->db->query('SELECT b.*, p.title as project_title, p.user_id as employer_id, u.name as freelancer_name, u.email as freelancer_email 
                          FROM bids b 
                          JOIN projects p ON b.project_id = p.id 
                          JOIN users u ON b.user_id = u.id 
                          WHERE b.id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    // Teklif güncelle
    public function updateBid($data) {
        $this->db->query('UPDATE bids 
                          SET description = :description, 
                              amount = :amount, 
                              delivery_time = :delivery_time, 
                              updated_at = CURRENT_TIMESTAMP 
                          WHERE id = :id AND status = "pending"');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':delivery_time', $data['delivery_time']);
        
        return $this->db->execute();
    }

    // Teklif durumunu güncelle
    public function updateBidStatus($id, $status) {
        $this->db->query('UPDATE bids SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        
        return $this->db->execute();
    }

    // Teklif sil
    public function deleteBid($id) {
        $this->db->query('DELETE FROM bids WHERE id = :id AND status = "pending"');
        
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    // Proje sahibinin tüm teklifleri reddetmesi
    public function rejectAllBids($project_id, $except_bid_id = null) {
        if($except_bid_id) {
            $this->db->query('UPDATE bids SET status = "rejected", updated_at = CURRENT_TIMESTAMP WHERE project_id = :project_id AND id != :except_bid_id AND status = "pending"');
            $this->db->bind(':except_bid_id', $except_bid_id);
        } else {
            $this->db->query('UPDATE bids SET status = "rejected", updated_at = CURRENT_TIMESTAMP WHERE project_id = :project_id AND status = "pending"');
        }
        
        $this->db->bind(':project_id', $project_id);
        
        return $this->db->execute();
    }

    // Teklif sayısını getir
    public function getBidCount() {
        $this->db->query('SELECT COUNT(*) as count FROM bids');
        $result = $this->db->single();
        return $result->count;
    }

    // Bekleyen teklif sayısını getir
    public function getPendingBidCount() {
        $this->db->query('SELECT COUNT(*) as count FROM bids WHERE status = "pending"');
        $result = $this->db->single();
        return $result->count;
    }

    // Kabul edilen teklif sayısını getir
    public function getAcceptedBidCount() {
        $this->db->query('SELECT COUNT(*) as count FROM bids WHERE status = "accepted"');
        $result = $this->db->single();
        return $result->count;
    }

    // Reddedilen teklif sayısını getir
    public function getRejectedBidCount() {
        $this->db->query('SELECT COUNT(*) as count FROM bids WHERE status = "rejected"');
        $result = $this->db->single();
        return $result->count;
    }

    // Tüm teklifleri getir (Admin paneli için)
    public function getAllBids() {
        $this->db->query('SELECT b.*, p.title as project_title, p.slug as project_slug, 
                          u.name as freelancer_name, e.name as employer_name
                          FROM bids b 
                          JOIN projects p ON b.project_id = p.id 
                          JOIN users u ON b.user_id = u.id 
                          JOIN users e ON p.user_id = e.id
                          ORDER BY b.created_at DESC');
        return $this->db->resultSet();
    }
    
    // Admin tarafından teklif güncelleme
    public function updateBidByAdmin($data) {
        $this->db->query('UPDATE bids 
                          SET description = :description, 
                              amount = :amount, 
                              delivery_time = :delivery_time,
                              status = :status,
                              updated_at = CURRENT_TIMESTAMP 
                          WHERE id = :id');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':delivery_time', $data['delivery_time']);
        $this->db->bind(':status', $data['status']);
        
        return $this->db->execute();
    }
    
    // Toplam teklif sayısını getir
    public function getTotalBids() {
        $this->db->query('SELECT COUNT(*) as total FROM bids');
        $result = $this->db->single();
        return $result->total;
    }
    
    // Durum bazında teklif sayısını getir
    public function getBidCountByStatus($status) {
        $this->db->query('SELECT COUNT(*) as total FROM bids WHERE status = :status');
        $this->db->bind(':status', $status);
        $result = $this->db->single();
        return $result->total;
    }
    
    // Son teklifleri getir
    public function getRecentBids($limit = 5) {
        $this->db->query('SELECT b.*, p.title as project_title, p.slug as project_slug, 
                          u.name as freelancer_name, e.name as employer_name
                          FROM bids b 
                          JOIN projects p ON b.project_id = p.id 
                          JOIN users u ON b.user_id = u.id 
                          JOIN users e ON p.user_id = e.id
                          ORDER BY b.created_at DESC
                          LIMIT :limit');
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
}
?> 