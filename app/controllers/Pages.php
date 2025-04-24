<?php
class Pages extends Controller {
    protected $projectModel;
    protected $userModel;
    
    public function __construct() {
        // Modeller
        $this->projectModel = $this->model('Project');
        $this->userModel = $this->model('User');
    }

    // Ana sayfa
    public function index() {
        // Son eklenen aktif projeleri getir
        $projects = $this->projectModel->getActiveProjects();
        
        // En fazla 6 proje göster
        $projects = array_slice($projects, 0, 6);
        
        // En iyi puanlı freelancer'ları getir
        $freelancers = $this->userModel->getAllFreelancers();
        
        // Her freelancer için puanı hesapla
        foreach ($freelancers as $freelancer) {
            $freelancer->rating = $this->userModel->getUserRating($freelancer->id);
        }
        
        // Puanına göre sırala
        usort($freelancers, function($a, $b) {
            return $b->rating <=> $a->rating;
        });
        
        // En fazla 4 freelancer göster
        $freelancers = array_slice($freelancers, 0, 4);
        
        // İstatistikler
        $stats = [
            'project_count' => $this->projectModel->getProjectCount(),
            'active_project_count' => $this->projectModel->getActiveProjectCount(),
            'freelancer_count' => count($this->userModel->getAllFreelancers()),
            'employer_count' => count($this->userModel->getAllEmployers())
        ];
        
        $data = [
            'title' => 'Freelancer Platformu',
            'projects' => $projects,
            'freelancers' => $freelancers,
            'stats' => $stats
        ];

        $this->view('pages/index', $data);
    }

    // Hakkımızda sayfası
    public function about() {
        $data = [
            'title' => 'Hakkımızda',
            'description' => 'Freelancer Platformu, freelancer ve işverenler için bir buluşma noktasıdır. Projelerinizi yayınlayabilir veya mevcut projelere teklif verebilirsiniz.'
        ];

        $this->view('pages/about', $data);
    }

    // İletişim sayfası
    public function contact() {
        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini temizle
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Form verilerini işle
            $data = [
                'title' => 'İletişim',
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'subject' => trim($_POST['subject']),
                'message' => trim($_POST['message']),
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            // Form kontrolü (demo olarak sadece başarılı mesajı gösterelim)
            // Gerçek uygulamada e-posta gönderme işlemleri eklenebilir
            $this->setFlashMessage('contact_success', 'Mesajınız bize ulaştı! En kısa sürede size dönüş yapacağız.', 'success');
            
            // View'a gönder
            $this->view('pages/contact', $data);
        } else {
            // GET isteği, formu göster
            $data = [
                'title' => 'İletişim',
                'name' => '',
                'email' => '',
                'subject' => '',
                'message' => '',
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            $this->view('pages/contact', $data);
        }
    }

    // Gizlilik politikası sayfası
    public function privacy() {
        $data = [
            'title' => 'Gizlilik Politikası'
        ];

        $this->view('pages/privacy', $data);
    }

    // Kullanım şartları sayfası
    public function terms() {
        $data = [
            'title' => 'Kullanım Şartları'
        ];

        $this->view('pages/terms', $data);
    }

    // Hata sayfası
    public function error($type = 'notfound') {
        switch($type) {
            case 'unauthorized':
                $message = 'Bu sayfaya erişim yetkiniz yok.';
                $code = 403;
                break;
            case 'forbidden':
                $message = 'Bu işlemi yapmaya yetkiniz yok.';
                $code = 403;
                break;
            default:
                $message = 'Sayfa bulunamadı.';
                $code = 404;
        }
        
        $data = [
            'title' => 'Hata ' . $code,
            'message' => $message,
            'code' => $code
        ];

        $this->view('pages/error', $data);
    }
}
?> 