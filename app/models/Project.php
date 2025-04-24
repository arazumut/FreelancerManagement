<?php
class Project {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Tüm projeleri getir
    public function getAllProjects() {
        $this->db->query('SELECT p.*, c.name as category_name, u.name as employer_name 
                          FROM projects p 
                          JOIN categories c ON p.category_id = c.id 
                          JOIN users u ON p.user_id = u.id 
                          ORDER BY p.created_at DESC');
        
        return $this->db->resultSet();
    }

    // Aktif projeleri getir
    public function getActiveProjects() {
        $this->db->query('SELECT p.*, c.name as category_name, u.name as employer_name 
                          FROM projects p 
                          JOIN categories c ON p.category_id = c.id 
                          JOIN users u ON p.user_id = u.id 
                          WHERE p.status = "active" 
                          ORDER BY p.created_at DESC');
        
        return $this->db->resultSet();
    }

    // Kullanıcının projelerini getir
    public function getUserProjects($user_id) {
        $this->db->query('SELECT p.*, c.name as category_name 
                          FROM projects p 
                          JOIN categories c ON p.category_id = c.id 
                          WHERE p.user_id = :user_id 
                          ORDER BY p.created_at DESC');
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->resultSet();
    }

    // ID'ye göre proje getir
    public function getProjectById($id) {
        $this->db->query('SELECT p.*, c.name as category_name, u.name as employer_name, u.email as employer_email 
                          FROM projects p 
                          JOIN categories c ON p.category_id = c.id 
                          JOIN users u ON p.user_id = u.id 
                          WHERE p.id = :id');
        
        $this->db->bind(':id', $id);
        
        $project = $this->db->single();
        
        return $project;
    }

    // Slug'a göre proje getir
    public function getProjectBySlug($slug) {
        $this->db->query('SELECT p.*, c.name as category_name, u.name as employer_name, u.email as employer_email 
                          FROM projects p 
                          JOIN categories c ON p.category_id = c.id 
                          JOIN users u ON p.user_id = u.id 
                          WHERE p.slug = :slug');
        
        $this->db->bind(':slug', $slug);
        
        $project = $this->db->single();
        
        return $project;
    }

    // Proje ekle
    public function addProject($data) {
        // Slug oluştur
        $slug = $this->createSlug($data['title']);
        
        $this->db->query('INSERT INTO projects (user_id, title, slug, description, category_id, min_budget, max_budget, deadline, status) 
                          VALUES(:user_id, :title, :slug, :description, :category_id, :min_budget, :max_budget, :deadline, :status)');
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $slug);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':min_budget', $data['min_budget']);
        $this->db->bind(':max_budget', $data['max_budget']);
        $this->db->bind(':deadline', $data['deadline']);
        $this->db->bind(':status', 'active');
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Proje güncelle
    public function updateProject($data) {
        $this->db->query('UPDATE projects 
                          SET title = :title, 
                              description = :description, 
                              category_id = :category_id, 
                              min_budget = :min_budget, 
                              max_budget = :max_budget, 
                              deadline = :deadline, 
                              status = :status, 
                              updated_at = CURRENT_TIMESTAMP 
                          WHERE id = :id');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':min_budget', $data['min_budget']);
        $this->db->bind(':max_budget', $data['max_budget']);
        $this->db->bind(':deadline', $data['deadline']);
        $this->db->bind(':status', $data['status']);
        
        return $this->db->execute();
    }

    // Proje durumunu güncelle
    public function updateProjectStatus($id, $status) {
        $this->db->query('UPDATE projects SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        
        return $this->db->execute();
    }

    // Proje sil
    public function deleteProject($id) {
        $this->db->query('DELETE FROM projects WHERE id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    // Kategori bazlı projeleri getir
    public function getProjectsByCategory($category_id) {
        $this->db->query('SELECT p.*, c.name as category_name, u.name as employer_name 
                          FROM projects p 
                          JOIN categories c ON p.category_id = c.id 
                          JOIN users u ON p.user_id = u.id 
                          WHERE p.category_id = :category_id AND p.status = "active" 
                          ORDER BY p.created_at DESC');
        
        $this->db->bind(':category_id', $category_id);
        
        return $this->db->resultSet();
    }

    // Bütçe aralığına göre projeleri getir
    public function getProjectsByBudget($min, $max) {
        $this->db->query('SELECT p.*, c.name as category_name, u.name as employer_name 
                          FROM projects p 
                          JOIN categories c ON p.category_id = c.id 
                          JOIN users u ON p.user_id = u.id 
                          WHERE p.min_budget >= :min AND p.max_budget <= :max AND p.status = "active" 
                          ORDER BY p.created_at DESC');
        
        $this->db->bind(':min', $min);
        $this->db->bind(':max', $max);
        
        return $this->db->resultSet();
    }

    // Slug oluştur
    private function createSlug($title) {
        // Türkçe karakterleri değiştir
        $turkishFrom = array('ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ş', 'Ş', 'ö', 'Ö', 'ç', 'Ç');
        $turkishTo   = array('i', 'i', 'g', 'g', 'u', 'u', 's', 's', 'o', 'o', 'c', 'c');
        $title = str_replace($turkishFrom, $turkishTo, $title);
        
        // Slug oluştur
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
        // Benzer slug var mı kontrol et
        $this->db->query('SELECT COUNT(*) as count FROM projects WHERE slug LIKE :slug');
        $this->db->bind(':slug', $slug . '%');
        $result = $this->db->single();
        
        // Eğer benzer slug varsa sonuna numara ekle
        if($result->count > 0) {
            $slug = $slug . '-' . ($result->count + 1);
        }
        
        return $slug;
    }

    // Tüm kategorileri getir
    public function getAllCategories() {
        $this->db->query('SELECT * FROM categories ORDER BY name ASC');
        return $this->db->resultSet();
    }

    // ID ile kategori getir
    public function getCategoryById($id) {
        $this->db->query('SELECT * FROM categories WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Proje sayısını getir
    public function getProjectCount() {
        $this->db->query('SELECT COUNT(*) as count FROM projects');
        $result = $this->db->single();
        return $result->count;
    }

    // Aktif proje sayısını getir
    public function getActiveProjectCount() {
        $this->db->query('SELECT COUNT(*) as count FROM projects WHERE status = "active"');
        $result = $this->db->single();
        return $result->count;
    }

    // Tamamlanan proje sayısını getir
    public function getCompletedProjectCount() {
        $this->db->query('SELECT COUNT(*) as count FROM projects WHERE status = "completed"');
        $result = $this->db->single();
        return $result->count;
    }

    // Admin tarafından projeyi güncelle
    public function updateProjectByAdmin($data) {
        $this->db->query('UPDATE projects 
                          SET title = :title, 
                              description = :description, 
                              budget = :budget, 
                              deadline = :deadline, 
                              status = :status, 
                              updated_at = CURRENT_TIMESTAMP 
                          WHERE id = :id');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':budget', $data['budget']);
        $this->db->bind(':deadline', $data['deadline']);
        $this->db->bind(':status', $data['status']);
        
        return $this->db->execute();
    }
    
    // Toplam proje sayısını getir
    public function getTotalProjects() {
        $this->db->query('SELECT COUNT(*) as total FROM projects');
        $result = $this->db->single();
        return $result->total;
    }
    
    // Son eklenen projeleri getir
    public function getRecentProjects($limit = 5) {
        $this->db->query('SELECT p.*, c.name as category_name, u.name as employer_name 
                          FROM projects p 
                          JOIN categories c ON p.category_id = c.id 
                          JOIN users u ON p.user_id = u.id 
                          ORDER BY p.created_at DESC
                          LIMIT :limit');
        
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
    
    // Durum bazında projeleri getir
    public function getProjectsByStatus($status) {
        $this->db->query('SELECT p.*, c.name as category_name, u.name as employer_name 
                          FROM projects p 
                          JOIN categories c ON p.category_id = c.id 
                          JOIN users u ON p.user_id = u.id 
                          WHERE p.status = :status
                          ORDER BY p.created_at DESC');
        
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }
    
    // Durum bazında proje sayısı getir
    public function getProjectCountByStatus($status) {
        $this->db->query('SELECT COUNT(*) as total FROM projects WHERE status = :status');
        $this->db->bind(':status', $status);
        $result = $this->db->single();
        return $result->total;
    }
}
?> 