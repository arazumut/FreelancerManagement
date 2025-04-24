<?php
class Invoice {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Fatura oluştur
    public function createInvoice($data) {
        $this->db->query('INSERT INTO invoices (contract_id, amount, status) 
                          VALUES(:contract_id, :amount, :status)');
        
        $this->db->bind(':contract_id', $data['contract_id']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':status', 'pending');
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // ID'ye göre fatura getir
    public function getInvoiceById($id) {
        $this->db->query('SELECT i.*, c.project_id, c.freelancer_id, c.employer_id,
                          p.title as project_title,
                          f.name as freelancer_name, f.email as freelancer_email,
                          e.name as employer_name, e.email as employer_email
                          FROM invoices i
                          JOIN contracts c ON i.contract_id = c.id
                          JOIN projects p ON c.project_id = p.id
                          JOIN users f ON c.freelancer_id = f.id
                          JOIN users e ON c.employer_id = e.id
                          WHERE i.id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    // Sözleşme ID'ye göre fatura getir
    public function getInvoiceByContractId($contract_id) {
        $this->db->query('SELECT i.*, c.project_id, c.freelancer_id, c.employer_id,
                          p.title as project_title,
                          f.name as freelancer_name, f.email as freelancer_email,
                          e.name as employer_name, e.email as employer_email
                          FROM invoices i
                          JOIN contracts c ON i.contract_id = c.id
                          JOIN projects p ON c.project_id = p.id
                          JOIN users f ON c.freelancer_id = f.id
                          JOIN users e ON c.employer_id = e.id
                          WHERE i.contract_id = :contract_id');
        
        $this->db->bind(':contract_id', $contract_id);
        
        return $this->db->single();
    }

    // Kullanıcının tüm faturalarını getir (Freelancer veya İşveren)
    public function getUserInvoices($user_id) {
        $this->db->query('SELECT i.*, c.project_id, p.title as project_title,
                          f.name as freelancer_name,
                          e.name as employer_name
                          FROM invoices i
                          JOIN contracts c ON i.contract_id = c.id
                          JOIN projects p ON c.project_id = p.id
                          JOIN users f ON c.freelancer_id = f.id
                          JOIN users e ON c.employer_id = e.id
                          WHERE c.freelancer_id = :user_id OR c.employer_id = :user_id
                          ORDER BY i.created_at DESC');
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->resultSet();
    }

    // Freelancer'ın faturalarını getir
    public function getFreelancerInvoices($freelancer_id) {
        $this->db->query('SELECT i.*, c.project_id, p.title as project_title,
                          e.name as employer_name
                          FROM invoices i
                          JOIN contracts c ON i.contract_id = c.id
                          JOIN projects p ON c.project_id = p.id
                          JOIN users e ON c.employer_id = e.id
                          WHERE c.freelancer_id = :freelancer_id
                          ORDER BY i.created_at DESC');
        
        $this->db->bind(':freelancer_id', $freelancer_id);
        
        return $this->db->resultSet();
    }

    // İşverenin faturalarını getir
    public function getEmployerInvoices($employer_id) {
        $this->db->query('SELECT i.*, c.project_id, p.title as project_title,
                          f.name as freelancer_name
                          FROM invoices i
                          JOIN contracts c ON i.contract_id = c.id
                          JOIN projects p ON c.project_id = p.id
                          JOIN users f ON c.freelancer_id = f.id
                          WHERE c.employer_id = :employer_id
                          ORDER BY i.created_at DESC');
        
        $this->db->bind(':employer_id', $employer_id);
        
        return $this->db->resultSet();
    }

    // Fatura durumunu güncelle
    public function updateInvoiceStatus($id, $status, $payment_method = null) {
        if($status == 'paid') {
            $this->db->query('UPDATE invoices 
                              SET status = :status, 
                                  payment_method = :payment_method, 
                                  payment_date = CURRENT_TIMESTAMP 
                              WHERE id = :id');
            
            $this->db->bind(':payment_method', $payment_method);
        } else {
            $this->db->query('UPDATE invoices 
                              SET status = :status 
                              WHERE id = :id');
        }
        
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        
        return $this->db->execute();
    }

    // Tüm faturaları getir (Admin için)
    public function getAllInvoices() {
        $this->db->query('SELECT i.*, c.project_id, p.title as project_title,
                          f.name as freelancer_name,
                          e.name as employer_name
                          FROM invoices i
                          JOIN contracts c ON i.contract_id = c.id
                          JOIN projects p ON c.project_id = p.id
                          JOIN users f ON c.freelancer_id = f.id
                          JOIN users e ON c.employer_id = e.id
                          ORDER BY i.created_at DESC');
        
        return $this->db->resultSet();
    }

    // Fatura sil (Admin için)
    public function deleteInvoice($id) {
        $this->db->query('DELETE FROM invoices WHERE id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    // Fatura sayısını getir
    public function getInvoiceCount() {
        $this->db->query('SELECT COUNT(*) as count FROM invoices');
        $result = $this->db->single();
        return $result->count;
    }

    // Bekleyen fatura sayısını getir
    public function getPendingInvoiceCount() {
        $this->db->query('SELECT COUNT(*) as count FROM invoices WHERE status = "pending"');
        $result = $this->db->single();
        return $result->count;
    }

    // Ödenmiş fatura sayısını getir
    public function getPaidInvoiceCount() {
        $this->db->query('SELECT COUNT(*) as count FROM invoices WHERE status = "paid"');
        $result = $this->db->single();
        return $result->count;
    }

    // Toplam geliri getir
    public function getTotalRevenue() {
        $this->db->query('SELECT SUM(amount) as total FROM invoices WHERE status = "paid"');
        $result = $this->db->single();
        return $result->total ? $result->total : 0;
    }

    // Freelancer'ın toplam kazancını getir
    public function getFreelancerTotalEarnings($freelancer_id) {
        $this->db->query('SELECT SUM(i.amount) as total 
                          FROM invoices i 
                          JOIN contracts c ON i.contract_id = c.id 
                          WHERE c.freelancer_id = :freelancer_id AND i.status = "paid"');
                          
        $this->db->bind(':freelancer_id', $freelancer_id);
        
        $result = $this->db->single();
        
        return $result->total ? $result->total : 0;
    }

    // İşverenin toplam harcamasını getir
    public function getEmployerTotalSpending($employer_id) {
        $this->db->query('SELECT SUM(i.amount) as total 
                          FROM invoices i 
                          JOIN contracts c ON i.contract_id = c.id 
                          WHERE c.employer_id = :employer_id AND i.status = "paid"');
                          
        $this->db->bind(':employer_id', $employer_id);
        
        $result = $this->db->single();
        
        return $result->total ? $result->total : 0;
    }
}
?> 