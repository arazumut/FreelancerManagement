<?php
class Contract {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Sözleşme oluştur
    public function createContract($data) {
        $this->db->query('INSERT INTO contracts (project_id, freelancer_id, employer_id, status) 
                          VALUES(:project_id, :freelancer_id, :employer_id, :status)');
        
        $this->db->bind(':project_id', $data['project_id']);
        $this->db->bind(':freelancer_id', $data['freelancer_id']);
        $this->db->bind(':employer_id', $data['employer_id']);
        $this->db->bind(':status', 'active');
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // ID'ye göre sözleşme getir
    public function getContractById($id) {
        $this->db->query('SELECT c.*, p.title as project_title, p.description as project_description, 
                          p.min_budget, p.max_budget, p.deadline,
                          f.name as freelancer_name, f.email as freelancer_email,
                          e.name as employer_name, e.email as employer_email,
                          b.amount as bid_amount, b.delivery_time
                          FROM contracts c
                          JOIN projects p ON c.project_id = p.id
                          JOIN users f ON c.freelancer_id = f.id
                          JOIN users e ON c.employer_id = e.id
                          LEFT JOIN bids b ON c.project_id = b.project_id AND c.freelancer_id = b.user_id
                          WHERE c.id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    // Proje ID'ye göre sözleşme getir
    public function getContractByProjectId($project_id) {
        $this->db->query('SELECT c.*, p.title as project_title,
                          f.name as freelancer_name, f.email as freelancer_email,
                          e.name as employer_name, e.email as employer_email,
                          b.amount as bid_amount, b.delivery_time
                          FROM contracts c
                          JOIN projects p ON c.project_id = p.id
                          JOIN users f ON c.freelancer_id = f.id
                          JOIN users e ON c.employer_id = e.id
                          LEFT JOIN bids b ON c.project_id = b.project_id AND c.freelancer_id = b.user_id
                          WHERE c.project_id = :project_id');
        
        $this->db->bind(':project_id', $project_id);
        
        return $this->db->single();
    }

    // Kullanıcı ID'ye göre sözleşmeleri getir (Freelancer veya İşveren)
    public function getUserContracts($user_id) {
        $this->db->query('SELECT c.*, p.title as project_title, p.slug as project_slug,
                          f.name as freelancer_name, f.email as freelancer_email,
                          e.name as employer_name, e.email as employer_email,
                          b.amount as bid_amount, b.delivery_time
                          FROM contracts c
                          JOIN projects p ON c.project_id = p.id
                          JOIN users f ON c.freelancer_id = f.id
                          JOIN users e ON c.employer_id = e.id
                          LEFT JOIN bids b ON c.project_id = b.project_id AND c.freelancer_id = b.user_id
                          WHERE c.freelancer_id = :user_id OR c.employer_id = :user_id
                          ORDER BY c.started_at DESC');
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->resultSet();
    }

    // Freelancer ID'ye göre sözleşmeleri getir
    public function getFreelancerContracts($freelancer_id) {
        $this->db->query('SELECT c.*, p.title as project_title, p.slug as project_slug,
                          e.name as employer_name, e.email as employer_email,
                          b.amount as bid_amount, b.delivery_time
                          FROM contracts c
                          JOIN projects p ON c.project_id = p.id
                          JOIN users e ON c.employer_id = e.id
                          LEFT JOIN bids b ON c.project_id = b.project_id AND c.freelancer_id = b.user_id
                          WHERE c.freelancer_id = :freelancer_id
                          ORDER BY c.started_at DESC');
        
        $this->db->bind(':freelancer_id', $freelancer_id);
        
        return $this->db->resultSet();
    }

    // İşveren ID'ye göre sözleşmeleri getir
    public function getEmployerContracts($employer_id) {
        $this->db->query('SELECT c.*, p.title as project_title, p.slug as project_slug,
                          f.name as freelancer_name, f.email as freelancer_email,
                          b.amount as bid_amount, b.delivery_time
                          FROM contracts c
                          JOIN projects p ON c.project_id = p.id
                          JOIN users f ON c.freelancer_id = f.id
                          LEFT JOIN bids b ON c.project_id = b.project_id AND c.freelancer_id = b.user_id
                          WHERE c.employer_id = :employer_id
                          ORDER BY c.started_at DESC');
        
        $this->db->bind(':employer_id', $employer_id);
        
        return $this->db->resultSet();
    }

    // Sözleşme durumunu güncelle
    public function updateContractStatus($id, $status) {
        // Eğer durum "completed" ise, tamamlanma tarihini ayarla
        if($status == 'completed') {
            $this->db->query('UPDATE contracts SET status = :status, completed_at = CURRENT_TIMESTAMP WHERE id = :id');
        } else {
            $this->db->query('UPDATE contracts SET status = :status WHERE id = :id');
        }
        
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        
        return $this->db->execute();
    }

    // Tüm sözleşmeleri getir (Admin için)
    public function getAllContracts() {
        $this->db->query('SELECT c.*, p.title as project_title,
                          f.name as freelancer_name,
                          e.name as employer_name,
                          b.amount as bid_amount
                          FROM contracts c
                          JOIN projects p ON c.project_id = p.id
                          JOIN users f ON c.freelancer_id = f.id
                          JOIN users e ON c.employer_id = e.id
                          LEFT JOIN bids b ON c.project_id = b.project_id AND c.freelancer_id = b.user_id
                          ORDER BY c.started_at DESC');
        
        return $this->db->resultSet();
    }

    // Sözleşme sayısını getir
    public function getContractCount() {
        $this->db->query('SELECT COUNT(*) as count FROM contracts');
        $result = $this->db->single();
        return $result->count;
    }

    // Aktif sözleşme sayısını getir
    public function getActiveContractCount() {
        $this->db->query('SELECT COUNT(*) as count FROM contracts WHERE status = "active"');
        $result = $this->db->single();
        return $result->count;
    }

    // Tamamlanan sözleşme sayısını getir
    public function getCompletedContractCount() {
        $this->db->query('SELECT COUNT(*) as count FROM contracts WHERE status = "completed"');
        $result = $this->db->single();
        return $result->count;
    }

    // Sözleşme sil (Admin için)
    public function deleteContract($id) {
        $this->db->query('DELETE FROM contracts WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
?> 