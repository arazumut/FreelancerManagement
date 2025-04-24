<?php
class Projects extends Controller {
    protected $projectModel;
    protected $userModel;
    protected $bidModel;
    protected $contractModel;
    protected $reviewModel;
    protected $notificationModel;
    
    public function __construct() {
        // Modeller
        $this->projectModel = $this->model('Project');
        $this->userModel = $this->model('User');
        $this->bidModel = $this->model('Bid');
        $this->contractModel = $this->model('Contract');
        $this->reviewModel = $this->model('Review');
        $this->notificationModel = $this->model('Notification');
    }

    // Tüm projeleri listele
    public function index() {
        // Filtreleri uygula
        $filter = [
            'category' => isset($_GET['category']) ? (int)$_GET['category'] : null,
            'min_budget' => isset($_GET['min_budget']) ? (float)$_GET['min_budget'] : null,
            'max_budget' => isset($_GET['max_budget']) ? (float)$_GET['max_budget'] : null,
            'status' => isset($_GET['status']) ? $_GET['status'] : 'active'
        ];

        // Filtre uygulanacak mı?
        if($filter['category']) {
            $projects = $this->projectModel->getProjectsByCategory($filter['category']);
        } elseif($filter['min_budget'] && $filter['max_budget']) {
            $projects = $this->projectModel->getProjectsByBudget($filter['min_budget'], $filter['max_budget']);
        } elseif($filter['status'] == 'all') {
            $projects = $this->projectModel->getAllProjects();
        } else {
            $projects = $this->projectModel->getActiveProjects();
        }

        // Kategorileri getir
        $categories = $this->projectModel->getAllCategories();
        
        // İstatistikleri getir
        $stats = [
            'project_count' => $this->projectModel->getProjectCount(),
            'active_project_count' => $this->projectModel->getActiveProjectCount(),
            'freelancer_count' => count($this->userModel->getAllFreelancers()),
            'employer_count' => count($this->userModel->getAllEmployers())
        ];

        // View'a gönder
        $data = [
            'title' => 'Projeler',
            'projects' => $projects,
            'categories' => $categories,
            'filter' => $filter,
            'stats' => $stats
        ];

        $this->view('projects/index', $data);
    }

    // Proje detayı göster
    public function showProject($id = null) {
        // ID yoksa projelere yönlendir
        if($id === null) {
            redirect('projects');
        }

        // Projeyi getir
        $project = $this->projectModel->getProjectById($id);

        // Proje yoksa 404 hatası göster
        if(!$project) {
            redirect('pages/error/notfound');
        }

        // Proje sahibi kullanıcı bilgilerini getir
        $employer = $this->userModel->getUserById($project->user_id);

        // Projenin tekliflerini getir (eğer işveren veya adminse)
        $bids = [];
        if(isLoggedIn() && ($_SESSION['user_id'] == $project->user_id || isAdmin())) {
            $bids = $this->bidModel->getProjectBids($project->id);
        }

        // Kullanıcı daha önce teklif vermiş mi kontrol et
        $userHasBid = false;
        if(isLoggedIn() && isFreelancer()) {
            $userHasBid = $this->bidModel->checkUserBid($project->id, $_SESSION['user_id']);
        }

        // Sözleşme var mı kontrol et
        $contract = $this->contractModel->getContractByProjectId($project->id);

        // View'a gönder
        $data = [
            'title' => $project->title,
            'project' => $project,
            'employer' => $employer,
            'bids' => $bids,
            'userHasBid' => $userHasBid,
            'contract' => $contract,
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view('projects/view', $data);
    }

    // Slug ile proje göster
    public function detail($slug = null) {
        // Slug yoksa projelere yönlendir
        if($slug === null) {
            redirect('projects');
        }

        // Projeyi getir
        $project = $this->projectModel->getProjectBySlug($slug);

        // Proje yoksa 404 hatası göster
        if(!$project) {
            redirect('pages/error/notfound');
        }

        // ID ile showProject methoduna yönlendir
        $this->showProject($project->id);
    }

    // Yeni proje ekle
    public function add() {
        // Oturum ve yetki kontrolü
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        if(!isEmployer() && !isAdmin()) {
            redirect('pages/error/unauthorized');
        }

        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini temizle
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // CSRF token kontrolü
            if(!$this->validateCsrfToken($_POST['csrf_token'])) {
                $this->setFlashMessage('project_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
                redirect('projects/add');
            }
            
            // Form verilerini işle
            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'category_id' => (int)$_POST['category_id'],
                'min_budget' => (float)$_POST['min_budget'],
                'max_budget' => (float)$_POST['max_budget'],
                'deadline' => $_POST['deadline'],
                'user_id' => $_SESSION['user_id'],
                'title_err' => '',
                'description_err' => '',
                'category_id_err' => '',
                'min_budget_err' => '',
                'max_budget_err' => '',
                'deadline_err' => '',
                'categories' => $this->projectModel->getAllCategories(),
                'csrf_token' => $this->generateCsrfToken()
            ];

            // Validasyon
            if(empty($data['title'])) {
                $data['title_err'] = 'Lütfen proje başlığını girin';
            } elseif(strlen($data['title']) < 5) {
                $data['title_err'] = 'Başlık en az 5 karakter olmalıdır';
            }

            if(empty($data['description'])) {
                $data['description_err'] = 'Lütfen proje açıklamasını girin';
            } elseif(strlen($data['description']) < 10) {
                $data['description_err'] = 'Açıklama en az 10 karakter olmalıdır';
            }

            if(empty($data['category_id'])) {
                $data['category_id_err'] = 'Lütfen bir kategori seçin';
            } else {
                // Kategori var mı kontrol et
                $category = $this->projectModel->getCategoryById($data['category_id']);
                if(!$category) {
                    $data['category_id_err'] = 'Geçersiz kategori';
                }
            }

            if(empty($data['min_budget'])) {
                $data['min_budget_err'] = 'Lütfen minimum bütçe girin';
            } elseif($data['min_budget'] <= 0) {
                $data['min_budget_err'] = 'Minimum bütçe 0\'dan büyük olmalıdır';
            }

            if(empty($data['max_budget'])) {
                $data['max_budget_err'] = 'Lütfen maksimum bütçe girin';
            } elseif($data['max_budget'] <= 0) {
                $data['max_budget_err'] = 'Maksimum bütçe 0\'dan büyük olmalıdır';
            } elseif($data['max_budget'] < $data['min_budget']) {
                $data['max_budget_err'] = 'Maksimum bütçe minimum bütçeden küçük olamaz';
            }

            if(empty($data['deadline'])) {
                $data['deadline_err'] = 'Lütfen son teslim tarihi girin';
            } else {
                // Tarih formatını kontrol et
                $deadline = date('Y-m-d', strtotime($data['deadline']));
                if($deadline === false || $deadline < date('Y-m-d')) {
                    $data['deadline_err'] = 'Geçerli bir tarih girin (bugün veya daha sonrası)';
                } else {
                    $data['deadline'] = $deadline;
                }
            }

            // Hata yoksa projeyi ekle
            if(empty($data['title_err']) && empty($data['description_err']) && empty($data['category_id_err']) && empty($data['min_budget_err']) && empty($data['max_budget_err']) && empty($data['deadline_err'])) {
                // Projeyi ekle
                if($project_id = $this->projectModel->addProject($data)) {
                    // Başarılı mesajı
                    $this->setFlashMessage('project_success', 'Proje başarıyla eklendi.', 'success');
                    
                    // Proje detayına yönlendir
                    redirect('projects/view/' . $project_id);
                } else {
                    // Hata mesajı
                    $this->setFlashMessage('project_error', 'Bir şeyler yanlış gitti. Lütfen tekrar deneyin.', 'danger');
                }
            }

            // Form verilerini view'a gönder
            $this->view('projects/add', $data);
        } else {
            // GET isteği, formu göster
            $data = [
                'title' => '',
                'description' => '',
                'category_id' => '',
                'min_budget' => '',
                'max_budget' => '',
                'deadline' => '',
                'title_err' => '',
                'description_err' => '',
                'category_id_err' => '',
                'min_budget_err' => '',
                'max_budget_err' => '',
                'deadline_err' => '',
                'categories' => $this->projectModel->getAllCategories(),
                'csrf_token' => $this->generateCsrfToken()
            ];

            // View yükle
            $this->view('projects/add', $data);
        }
    }

    // Proje düzenle
    public function edit($id = null) {
        // ID yoksa projelere yönlendir
        if($id === null) {
            redirect('projects');
        }

        // Projeyi getir
        $project = $this->projectModel->getProjectById($id);

        // Proje yoksa 404 hatası göster
        if(!$project) {
            redirect('pages/error/notfound');
        }

        // Oturum ve yetki kontrolü
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        // Kullanıcı projeni sahibi mi veya admin mi kontrol et
        if($_SESSION['user_id'] != $project->user_id && !isAdmin()) {
            redirect('pages/error/unauthorized');
        }

        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini temizle
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // CSRF token kontrolü
            if(!$this->validateCsrfToken($_POST['csrf_token'])) {
                $this->setFlashMessage('project_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
                redirect('projects/edit/' . $id);
            }
            
            // Form verilerini işle
            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'category_id' => (int)$_POST['category_id'],
                'min_budget' => (float)$_POST['min_budget'],
                'max_budget' => (float)$_POST['max_budget'],
                'deadline' => $_POST['deadline'],
                'status' => $_POST['status'],
                'title_err' => '',
                'description_err' => '',
                'category_id_err' => '',
                'min_budget_err' => '',
                'max_budget_err' => '',
                'deadline_err' => '',
                'status_err' => '',
                'categories' => $this->projectModel->getAllCategories(),
                'project' => $project,
                'csrf_token' => $this->generateCsrfToken()
            ];

            // Validasyon
            if(empty($data['title'])) {
                $data['title_err'] = 'Lütfen proje başlığını girin';
            } elseif(strlen($data['title']) < 5) {
                $data['title_err'] = 'Başlık en az 5 karakter olmalıdır';
            }

            if(empty($data['description'])) {
                $data['description_err'] = 'Lütfen proje açıklamasını girin';
            } elseif(strlen($data['description']) < 10) {
                $data['description_err'] = 'Açıklama en az 10 karakter olmalıdır';
            }

            if(empty($data['category_id'])) {
                $data['category_id_err'] = 'Lütfen bir kategori seçin';
            } else {
                // Kategori var mı kontrol et
                $category = $this->projectModel->getCategoryById($data['category_id']);
                if(!$category) {
                    $data['category_id_err'] = 'Geçersiz kategori';
                }
            }

            if(empty($data['min_budget'])) {
                $data['min_budget_err'] = 'Lütfen minimum bütçe girin';
            } elseif($data['min_budget'] <= 0) {
                $data['min_budget_err'] = 'Minimum bütçe 0\'dan büyük olmalıdır';
            }

            if(empty($data['max_budget'])) {
                $data['max_budget_err'] = 'Lütfen maksimum bütçe girin';
            } elseif($data['max_budget'] <= 0) {
                $data['max_budget_err'] = 'Maksimum bütçe 0\'dan büyük olmalıdır';
            } elseif($data['max_budget'] < $data['min_budget']) {
                $data['max_budget_err'] = 'Maksimum bütçe minimum bütçeden küçük olamaz';
            }

            if(empty($data['deadline'])) {
                $data['deadline_err'] = 'Lütfen son teslim tarihi girin';
            } else {
                // Tarih formatını kontrol et
                $deadline = date('Y-m-d', strtotime($data['deadline']));
                if($deadline === false) {
                    $data['deadline_err'] = 'Geçerli bir tarih girin';
                } else {
                    $data['deadline'] = $deadline;
                }
            }

            if(empty($data['status'])) {
                $data['status_err'] = 'Lütfen durum seçin';
            } elseif(!in_array($data['status'], ['active', 'completed', 'canceled'])) {
                $data['status_err'] = 'Geçersiz durum';
            }

            // Hata yoksa projeyi güncelle
            if(empty($data['title_err']) && empty($data['description_err']) && empty($data['category_id_err']) && empty($data['min_budget_err']) && empty($data['max_budget_err']) && empty($data['deadline_err']) && empty($data['status_err'])) {
                // Projeyi güncelle
                if($this->projectModel->updateProject($data)) {
                    // Başarılı mesajı
                    $this->setFlashMessage('project_success', 'Proje başarıyla güncellendi.', 'success');
                    
                    // Proje detayına yönlendir
                    redirect('projects/view/' . $id);
                } else {
                    // Hata mesajı
                    $this->setFlashMessage('project_error', 'Bir şeyler yanlış gitti. Lütfen tekrar deneyin.', 'danger');
                }
            }

            // Form verilerini view'a gönder
            $this->view('projects/edit', $data);
        } else {
            // GET isteği, formu doldur
            $data = [
                'id' => $id,
                'title' => $project->title,
                'description' => $project->description,
                'category_id' => $project->category_id,
                'min_budget' => $project->min_budget,
                'max_budget' => $project->max_budget,
                'deadline' => $project->deadline,
                'status' => $project->status,
                'title_err' => '',
                'description_err' => '',
                'category_id_err' => '',
                'min_budget_err' => '',
                'max_budget_err' => '',
                'deadline_err' => '',
                'status_err' => '',
                'categories' => $this->projectModel->getAllCategories(),
                'project' => $project,
                'csrf_token' => $this->generateCsrfToken()
            ];

            // View yükle
            $this->view('projects/edit', $data);
        }
    }

    // Proje sil
    public function delete($id = null) {
        // ID yoksa projelere yönlendir
        if($id === null) {
            redirect('projects');
        }

        // POST istek kontrolü
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            redirect('projects');
        }

        // CSRF token kontrolü
        if(!$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->setFlashMessage('project_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
            redirect('projects/view/' . $id);
        }

        // Projeyi getir
        $project = $this->projectModel->getProjectById($id);

        // Proje yoksa 404 hatası göster
        if(!$project) {
            redirect('pages/error/notfound');
        }

        // Oturum ve yetki kontrolü
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        // Kullanıcı projenin sahibi mi veya admin mi kontrol et
        if($_SESSION['user_id'] != $project->user_id && !isAdmin()) {
            redirect('pages/error/unauthorized');
        }

        // Aktif sözleşmesi olan proje silinemez
        $contract = $this->contractModel->getContractByProjectId($id);
        if($contract && ($contract->status == 'active' || $contract->status == 'delivered' || $contract->status == 'revision')) {
            $this->setFlashMessage('project_error', 'Aktif sözleşmesi olan proje silinemez.', 'danger');
            redirect('projects/view/' . $id);
        }

        // Projeyi sil
        if($this->projectModel->deleteProject($id)) {
            // Başarılı mesajı
            $this->setFlashMessage('project_success', 'Proje başarıyla silindi.', 'success');
            redirect('projects');
        } else {
            // Hata mesajı
            $this->setFlashMessage('project_error', 'Bir şeyler yanlış gitti. Lütfen tekrar deneyin.', 'danger');
            redirect('projects/view/' . $id);
        }
    }

    // Kullanıcının projelerini listele
    public function myProjects() {
        // Oturum kontrolü
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        // İşveren kontrolü
        if(!isEmployer() && !isAdmin()) {
            redirect('pages/error/unauthorized');
        }

        // Kullanıcının projelerini getir
        $projects = $this->projectModel->getUserProjects($_SESSION['user_id']);

        // View'a gönder
        $data = [
            'title' => 'Projelerim',
            'projects' => $projects
        ];

        $this->view('projects/my_projects', $data);
    }
}
?> 