<?php
/**
 * Admin Controller
 * Yönetici paneli işlemleri
 */
class Admin extends Controller {
    private $userModel;
    private $projectModel;
    private $bidModel;
    private $contractModel;
    private $messageModel;

    public function __construct() {
        // Modelleri yükle
        $this->userModel = $this->model('User');
        $this->projectModel = $this->model('Project');
        $this->bidModel = $this->model('Bid');
        $this->contractModel = $this->model('Contract');
        $this->messageModel = $this->model('Message');
    }

    // Admin dashboard
    public function index() {
        // Admin yetki kontrolü
        if(!isLoggedIn() || !isAdmin()) {
            redirect('pages/error/unauthorized');
        }
        
        // İstatistikleri getir
        $totalUsers = $this->userModel->getTotalUsers();
        $totalProjects = $this->projectModel->getTotalProjects();
        $totalBids = $this->bidModel->getTotalBids();
        $totalContracts = $this->contractModel->getTotalContracts();
        
        // Son kayıtları getir
        $recentUsers = $this->userModel->getRecentUsers(5);
        $recentProjects = $this->projectModel->getRecentProjects(5);
        
        // Verileri görünüme aktar
        $data = [
            'title' => 'Yönetici Paneli',
            'total_users' => $totalUsers,
            'total_projects' => $totalProjects,
            'total_bids' => $totalBids,
            'total_contracts' => $totalContracts,
            'recent_users' => $recentUsers,
            'recent_projects' => $recentProjects
        ];
        
        $this->view('admin/dashboard', $data);
    }
    
    // Kullanıcı yönetimi
    public function users() {
        // Admin yetki kontrolü
        if(!isLoggedIn() || !isAdmin()) {
            redirect('pages/error/unauthorized');
        }
        
        // Kullanıcıları getir
        $users = $this->userModel->getAllUsers();
        
        // Verileri görünüme aktar
        $data = [
            'title' => 'Kullanıcı Yönetimi',
            'users' => $users
        ];
        
        $this->view('admin/users', $data);
    }
    
    // Kullanıcı düzenleme
    public function editUser($id = null) {
        // Admin yetki kontrolü
        if(!isLoggedIn() || !isAdmin()) {
            redirect('pages/error/unauthorized');
        }
        
        // ID kontrolü
        if($id === null) {
            redirect('admin/users');
        }
        
        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini temizle
            $_POST = $this->sanitizeInputArray(INPUT_POST, $_POST);
            
            // CSRF token kontrolü
            if(!$this->validateCsrfToken($_POST['csrf_token'])) {
                $this->setFlashMessage('admin_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
                redirect('admin/editUser/' . $id);
            }
            
            // Form verilerini al
            $data = [
                'id' => $id,
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'role' => trim($_POST['role']),
                'status' => isset($_POST['status']) ? 'active' : 'inactive',
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            // Kullanıcıyı güncelle
            if($this->userModel->updateUserByAdmin($data)) {
                $this->setFlashMessage('admin_success', 'Kullanıcı başarıyla güncellendi.', 'success');
                redirect('admin/users');
            } else {
                $this->setFlashMessage('admin_error', 'Kullanıcı güncellenirken bir hata oluştu.', 'danger');
                $this->view('admin/edit_user', $data);
            }
        } else {
            // Kullanıcıyı getir
            $user = $this->userModel->getUserById($id);
            
            // Kullanıcı bulunamadıysa
            if(!$user) {
                redirect('pages/error/notfound');
            }
            
            // Veri hazırla
            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            $this->view('admin/edit_user', $data);
        }
    }
    
    // Kullanıcı silme
    public function deleteUser($id = null) {
        // Admin yetki kontrolü
        if(!isLoggedIn() || !isAdmin()) {
            redirect('pages/error/unauthorized');
        }
        
        // ID kontrolü
        if($id === null) {
            redirect('admin/users');
        }
        
        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            redirect('admin/users');
        }
        
        // CSRF token kontrolü
        if(!$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->setFlashMessage('admin_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
            redirect('admin/users');
        }
        
        // Kullanıcıyı sil
        if($this->userModel->deleteUser($id)) {
            $this->setFlashMessage('admin_success', 'Kullanıcı başarıyla silindi.', 'success');
        } else {
            $this->setFlashMessage('admin_error', 'Kullanıcı silinirken bir hata oluştu.', 'danger');
        }
        
        redirect('admin/users');
    }
    
    // Proje yönetimi
    public function projects() {
        // Admin yetki kontrolü
        if(!isLoggedIn() || !isAdmin()) {
            redirect('pages/error/unauthorized');
        }
        
        // Projeleri getir
        $projects = $this->projectModel->getAllProjects();
        
        // Verileri görünüme aktar
        $data = [
            'title' => 'Proje Yönetimi',
            'projects' => $projects
        ];
        
        $this->view('admin/projects', $data);
    }
    
    // Projeyi düzenleme
    public function editProject($id = null) {
        // Admin yetki kontrolü
        if(!isLoggedIn() || !isAdmin()) {
            redirect('pages/error/unauthorized');
        }
        
        // ID kontrolü
        if($id === null) {
            redirect('admin/projects');
        }
        
        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini temizle
            $_POST = $this->sanitizeInputArray(INPUT_POST, $_POST);
            
            // CSRF token kontrolü
            if(!$this->validateCsrfToken($_POST['csrf_token'])) {
                $this->setFlashMessage('admin_error', 'Güvenlik doğrulaması başarısız oldu.', 'danger');
                redirect('admin/editProject/' . $id);
            }
            
            // Form verilerini al
            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'budget' => trim($_POST['budget']),
                'deadline' => trim($_POST['deadline']),
                'status' => trim($_POST['status']),
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            // Projeyi güncelle
            if($this->projectModel->updateProjectByAdmin($data)) {
                $this->setFlashMessage('admin_success', 'Proje başarıyla güncellendi.', 'success');
                redirect('admin/projects');
            } else {
                $this->setFlashMessage('admin_error', 'Proje güncellenirken bir hata oluştu.', 'danger');
                $this->view('admin/edit_project', $data);
            }
        } else {
            // Projeyi getir
            $project = $this->projectModel->getProjectById($id);
            
            // Proje bulunamadıysa
            if(!$project) {
                redirect('pages/error/notfound');
            }
            
            // Veri hazırla
            $data = [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'budget' => $project->budget,
                'deadline' => $project->deadline,
                'status' => $project->status,
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            $this->view('admin/edit_project', $data);
        }
    }
    
    // Proje silme
    public function deleteProject($id = null) {
        // Admin yetki kontrolü
        if(!isLoggedIn() || !isAdmin()) {
            redirect('pages/error/unauthorized');
        }
        
        // ID kontrolü
        if($id === null) {
            redirect('admin/projects');
        }
        
        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            redirect('admin/projects');
        }
        
        // CSRF token kontrolü
        if(!$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->setFlashMessage('admin_error', 'Güvenlik doğrulaması başarısız oldu.', 'danger');
            redirect('admin/projects');
        }
        
        // Projeyi sil
        if($this->projectModel->deleteProject($id)) {
            $this->setFlashMessage('admin_success', 'Proje başarıyla silindi.', 'success');
        } else {
            $this->setFlashMessage('admin_error', 'Proje silinirken bir hata oluştu.', 'danger');
        }
        
        redirect('admin/projects');
    }
}
?> 