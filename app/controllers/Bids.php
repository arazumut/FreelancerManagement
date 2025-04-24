<?php
class Bids extends Controller {
    protected $bidModel;
    protected $projectModel;
    protected $userModel;
    protected $contractModel;
    protected $notificationModel;
    
    public function __construct() {
        // Modeller
        $this->bidModel = $this->model('Bid');
        $this->projectModel = $this->model('Project');
        $this->userModel = $this->model('User');
        $this->contractModel = $this->model('Contract');
        $this->notificationModel = $this->model('Notification');
    }

    // Teklif ekle
    public function add($project_id = null) {
        // Proje ID'si yoksa projelere yönlendir
        if($project_id === null) {
            redirect('projects');
        }

        // Projeyi getir
        $project = $this->projectModel->getProjectById($project_id);

        // Proje yoksa 404 hatası göster
        if(!$project) {
            redirect('pages/error/notfound');
        }

        // Oturum ve yetki kontrolü
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        if(!isFreelancer()) {
            redirect('pages/error/unauthorized');
        }

        // Kendi projesine teklif veremez
        if($_SESSION['user_id'] == $project->user_id) {
            $this->setFlashMessage('bid_error', 'Kendi projenize teklif veremezsiniz.', 'danger');
            redirect('projects/view/' . $project_id);
        }

        // Proje aktif mi kontrol et
        if($project->status != 'active') {
            $this->setFlashMessage('bid_error', 'Bu proje için teklif verme süresi dolmuş.', 'danger');
            redirect('projects/view/' . $project_id);
        }

        // Kullanıcı daha önce teklif vermiş mi kontrol et
        if($this->bidModel->checkUserBid($project_id, $_SESSION['user_id'])) {
            $this->setFlashMessage('bid_error', 'Bu proje için zaten teklif vermişsiniz.', 'danger');
            redirect('projects/view/' . $project_id);
        }

        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini temizle
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // CSRF token kontrolü
            if(!$this->validateCsrfToken($_POST['csrf_token'])) {
                $this->setFlashMessage('bid_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
                redirect('bids/add/' . $project_id);
            }
            
            // Form verilerini işle
            $data = [
                'project_id' => $project_id,
                'user_id' => $_SESSION['user_id'],
                'description' => trim($_POST['description']),
                'amount' => (float)$_POST['amount'],
                'delivery_time' => (int)$_POST['delivery_time'],
                'description_err' => '',
                'amount_err' => '',
                'delivery_time_err' => '',
                'project' => $project,
                'csrf_token' => $this->generateCsrfToken()
            ];

            // Validasyon
            if(empty($data['description'])) {
                $data['description_err'] = 'Lütfen teklif açıklamasını girin';
            } elseif(strlen($data['description']) < 10) {
                $data['description_err'] = 'Açıklama en az 10 karakter olmalıdır';
            }

            if(empty($data['amount'])) {
                $data['amount_err'] = 'Lütfen teklif tutarını girin';
            } elseif($data['amount'] <= 0) {
                $data['amount_err'] = 'Teklif tutarı 0\'dan büyük olmalıdır';
            } elseif($data['amount'] < $project->min_budget || $data['amount'] > $project->max_budget) {
                $data['amount_err'] = 'Teklif tutarı, proje bütçe aralığında olmalıdır (' . $project->min_budget . ' - ' . $project->max_budget . ')';
            }

            if(empty($data['delivery_time'])) {
                $data['delivery_time_err'] = 'Lütfen teslim süresini girin';
            } elseif($data['delivery_time'] <= 0) {
                $data['delivery_time_err'] = 'Teslim süresi 0\'dan büyük olmalıdır';
            }

            // Hata yoksa teklifi ekle
            if(empty($data['description_err']) && empty($data['amount_err']) && empty($data['delivery_time_err'])) {
                // Teklifi ekle
                if($bid_id = $this->bidModel->addBid($data)) {
                    // Bildirim oluştur
                    $user = $this->userModel->getUserById($_SESSION['user_id']);
                    $this->notificationModel->newBidNotification($project_id, $project->user_id, $user->name);
                    
                    // E-posta gönder (opsiyonel)
                    if(function_exists('sendNewBidNotification')) {
                        // Teklif bilgilerini getir
                        $bid = $this->bidModel->getBidById($bid_id);
                        sendNewBidNotification($bid);
                    }
                    
                    // Başarılı mesajı
                    $this->setFlashMessage('bid_success', 'Teklifiniz başarıyla gönderildi.', 'success');
                    redirect('projects/view/' . $project_id);
                } else {
                    // Hata mesajı
                    $this->setFlashMessage('bid_error', 'Bir şeyler yanlış gitti. Lütfen tekrar deneyin.', 'danger');
                }
            }

            // Form verilerini view'a gönder
            $this->view('bids/add', $data);
        } else {
            // GET isteği, formu göster
            $data = [
                'project_id' => $project_id,
                'user_id' => $_SESSION['user_id'],
                'description' => '',
                'amount' => $project->min_budget,
                'delivery_time' => '',
                'description_err' => '',
                'amount_err' => '',
                'delivery_time_err' => '',
                'project' => $project,
                'csrf_token' => $this->generateCsrfToken()
            ];

            // View yükle
            $this->view('bids/add', $data);
        }
    }

    // Teklif düzenle
    public function edit($id = null) {
        // ID yoksa tekliflere yönlendir
        if($id === null) {
            redirect('bids/my');
        }

        // Teklifi getir
        $bid = $this->bidModel->getBidById($id);

        // Teklif yoksa 404 hatası göster
        if(!$bid) {
            redirect('pages/error/notfound');
        }

        // Oturum ve yetki kontrolü
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        // Kullanıcı teklifin sahibi mi kontrol et
        if($_SESSION['user_id'] != $bid->user_id && !isAdmin()) {
            redirect('pages/error/unauthorized');
        }

        // Teklif durumu kontrol et (sadece beklemedeki teklifler düzenlenebilir)
        if($bid->status != 'pending') {
            $this->setFlashMessage('bid_error', 'Sadece beklemedeki teklifler düzenlenebilir.', 'danger');
            redirect('bids/my');
        }

        // Projeyi getir
        $project = $this->projectModel->getProjectById($bid->project_id);

        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini temizle
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // CSRF token kontrolü
            if(!$this->validateCsrfToken($_POST['csrf_token'])) {
                $this->setFlashMessage('bid_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
                redirect('bids/edit/' . $id);
            }
            
            // Form verilerini işle
            $data = [
                'id' => $id,
                'description' => trim($_POST['description']),
                'amount' => (float)$_POST['amount'],
                'delivery_time' => (int)$_POST['delivery_time'],
                'description_err' => '',
                'amount_err' => '',
                'delivery_time_err' => '',
                'bid' => $bid,
                'project' => $project,
                'csrf_token' => $this->generateCsrfToken()
            ];

            // Validasyon
            if(empty($data['description'])) {
                $data['description_err'] = 'Lütfen teklif açıklamasını girin';
            } elseif(strlen($data['description']) < 10) {
                $data['description_err'] = 'Açıklama en az 10 karakter olmalıdır';
            }

            if(empty($data['amount'])) {
                $data['amount_err'] = 'Lütfen teklif tutarını girin';
            } elseif($data['amount'] <= 0) {
                $data['amount_err'] = 'Teklif tutarı 0\'dan büyük olmalıdır';
            } elseif($data['amount'] < $project->min_budget || $data['amount'] > $project->max_budget) {
                $data['amount_err'] = 'Teklif tutarı, proje bütçe aralığında olmalıdır (' . $project->min_budget . ' - ' . $project->max_budget . ')';
            }

            if(empty($data['delivery_time'])) {
                $data['delivery_time_err'] = 'Lütfen teslim süresini girin';
            } elseif($data['delivery_time'] <= 0) {
                $data['delivery_time_err'] = 'Teslim süresi 0\'dan büyük olmalıdır';
            }

            // Hata yoksa teklifi güncelle
            if(empty($data['description_err']) && empty($data['amount_err']) && empty($data['delivery_time_err'])) {
                // Teklifi güncelle
                if($this->bidModel->updateBid($data)) {
                    // Başarılı mesajı
                    $this->setFlashMessage('bid_success', 'Teklifiniz başarıyla güncellendi.', 'success');
                    redirect('bids/my');
                } else {
                    // Hata mesajı
                    $this->setFlashMessage('bid_error', 'Bir şeyler yanlış gitti. Lütfen tekrar deneyin.', 'danger');
                }
            }

            // Form verilerini view'a gönder
            $this->view('bids/edit', $data);
        } else {
            // GET isteği, formu doldur
            $data = [
                'id' => $id,
                'description' => $bid->description,
                'amount' => $bid->amount,
                'delivery_time' => $bid->delivery_time,
                'description_err' => '',
                'amount_err' => '',
                'delivery_time_err' => '',
                'bid' => $bid,
                'project' => $project,
                'csrf_token' => $this->generateCsrfToken()
            ];

            // View yükle
            $this->view('bids/edit', $data);
        }
    }

    // Teklif sil
    public function delete($id = null) {
        // ID yoksa tekliflere yönlendir
        if($id === null) {
            redirect('bids/my');
        }

        // POST istek kontrolü
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            redirect('bids/my');
        }

        // CSRF token kontrolü
        if(!$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->setFlashMessage('bid_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
            redirect('bids/my');
        }

        // Teklifi getir
        $bid = $this->bidModel->getBidById($id);

        // Teklif yoksa 404 hatası göster
        if(!$bid) {
            redirect('pages/error/notfound');
        }

        // Oturum ve yetki kontrolü
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        // Kullanıcı teklifin sahibi mi kontrol et
        if($_SESSION['user_id'] != $bid->user_id && !isAdmin()) {
            redirect('pages/error/unauthorized');
        }

        // Teklif durumu kontrol et (sadece beklemedeki teklifler silinebilir)
        if($bid->status != 'pending') {
            $this->setFlashMessage('bid_error', 'Sadece beklemedeki teklifler silinebilir.', 'danger');
            redirect('bids/my');
        }

        // Teklifi sil
        if($this->bidModel->deleteBid($id)) {
            // Başarılı mesajı
            $this->setFlashMessage('bid_success', 'Teklifiniz başarıyla silindi.', 'success');
            redirect('bids/my');
        } else {
            // Hata mesajı
            $this->setFlashMessage('bid_error', 'Bir şeyler yanlış gitti. Lütfen tekrar deneyin.', 'danger');
            redirect('bids/my');
        }
    }

    // Kullanıcının tekliflerini listele
    public function my() {
        // Oturum kontrolü
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        // Freelancer kontrolü
        if(!isFreelancer() && !isAdmin()) {
            redirect('pages/error/unauthorized');
        }

        // Kullanıcının tekliflerini getir
        $bids = $this->bidModel->getUserBids($_SESSION['user_id']);

        // View'a gönder
        $data = [
            'title' => 'Tekliflerim',
            'bids' => $bids
        ];

        $this->view('bids/my', $data);
    }

    // Teklifi kabul et (işveren tarafından)
    public function accept($id = null) {
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
            $this->setFlashMessage('bid_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
            redirect('projects');
        }

        // Teklifi getir
        $bid = $this->bidModel->getBidById($id);

        // Teklif yoksa 404 hatası göster
        if(!$bid) {
            redirect('pages/error/notfound');
        }

        // Oturum ve yetki kontrolü
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        // Kullanıcı projenin sahibi mi kontrol et
        if($_SESSION['user_id'] != $bid->employer_id && !isAdmin()) {
            redirect('pages/error/unauthorized');
        }

        // Teklif durumu kontrol et (sadece beklemedeki teklifler kabul edilebilir)
        if($bid->status != 'pending') {
            $this->setFlashMessage('bid_error', 'Sadece beklemedeki teklifler kabul edilebilir.', 'danger');
            redirect('projects/view/' . $bid->project_id);
        }

        // Projenin durumunu kontrol et
        $project = $this->projectModel->getProjectById($bid->project_id);
        if($project->status != 'active') {
            $this->setFlashMessage('bid_error', 'Sadece aktif projelerde teklif kabul edilebilir.', 'danger');
            redirect('projects/view/' . $bid->project_id);
        }

        // İşlem başlat
        // 1. Teklifi kabul et
        if($this->bidModel->updateBidStatus($id, 'accepted')) {
            // 2. Diğer teklifleri reddet
            $this->bidModel->rejectAllBids($bid->project_id, $id);
            
            // 3. Sözleşme oluştur
            $contract_data = [
                'project_id' => $bid->project_id,
                'freelancer_id' => $bid->user_id,
                'employer_id' => $bid->employer_id
            ];
            
            if($contract_id = $this->contractModel->createContract($contract_data)) {
                // 4. Bildirim oluştur
                $user = $this->userModel->getUserById($_SESSION['user_id']);
                $this->notificationModel->bidAcceptedNotification($bid->project_id, $bid->user_id, $user->name);
                
                // 5. E-posta gönder (opsiyonel)
                if(function_exists('sendBidAcceptedNotification')) {
                    sendBidAcceptedNotification($bid);
                }
                
                // Başarılı mesajı
                $this->setFlashMessage('bid_success', 'Teklif kabul edildi ve sözleşme oluşturuldu.', 'success');
                redirect('contracts/view/' . $contract_id);
            } else {
                // Hata mesajı
                $this->setFlashMessage('bid_error', 'Sözleşme oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.', 'danger');
                redirect('projects/view/' . $bid->project_id);
            }
        } else {
            // Hata mesajı
            $this->setFlashMessage('bid_error', 'Bir şeyler yanlış gitti. Lütfen tekrar deneyin.', 'danger');
            redirect('projects/view/' . $bid->project_id);
        }
    }

    // Teklifi reddet (işveren tarafından)
    public function reject($id = null) {
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
            $this->setFlashMessage('bid_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
            redirect('projects');
        }

        // Teklifi getir
        $bid = $this->bidModel->getBidById($id);

        // Teklif yoksa 404 hatası göster
        if(!$bid) {
            redirect('pages/error/notfound');
        }

        // Oturum ve yetki kontrolü
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        // Kullanıcı projenin sahibi mi kontrol et
        if($_SESSION['user_id'] != $bid->employer_id && !isAdmin()) {
            redirect('pages/error/unauthorized');
        }

        // Teklif durumu kontrol et (sadece beklemedeki teklifler reddedilebilir)
        if($bid->status != 'pending') {
            $this->setFlashMessage('bid_error', 'Sadece beklemedeki teklifler reddedilebilir.', 'danger');
            redirect('projects/view/' . $bid->project_id);
        }

        // Teklifi reddet
        if($this->bidModel->updateBidStatus($id, 'rejected')) {
            // Bildirim oluştur
            $user = $this->userModel->getUserById($_SESSION['user_id']);
            $this->notificationModel->bidRejectedNotification($bid->project_id, $bid->user_id, $user->name);
            
            // E-posta gönder (opsiyonel)
            if(function_exists('sendBidRejectedNotification')) {
                sendBidRejectedNotification($bid);
            }
            
            // Başarılı mesajı
            $this->setFlashMessage('bid_success', 'Teklif reddedildi.', 'success');
            redirect('projects/view/' . $bid->project_id);
        } else {
            // Hata mesajı
            $this->setFlashMessage('bid_error', 'Bir şeyler yanlış gitti. Lütfen tekrar deneyin.', 'danger');
            redirect('projects/view/' . $bid->project_id);
        }
    }
}
?> 