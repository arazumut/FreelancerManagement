<?php
class Users extends Controller {
    protected $userModel;
    protected $reviewModel;
    protected $projectModel;
    protected $bidModel;
    protected $contractModel;
    protected $invoiceModel;
    
    public function __construct() {
        // Kullanıcı model
        $this->userModel = $this->model('User');
        $this->reviewModel = $this->model('Review');
        $this->projectModel = $this->model('Project');
        $this->bidModel = $this->model('Bid');
        $this->contractModel = $this->model('Contract');
        $this->invoiceModel = $this->model('Invoice');
    }

    // Kullanıcı kayıt sayfası ve işlemi
    public function register() {
        // Eğer oturum açıksa ana sayfaya yönlendir
        if(isLoggedIn()) {
            redirect('');
        }

        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini temizle
            $_POST = $this->sanitizeInputArray(INPUT_POST, $_POST);
            
            // CSRF token kontrolü
            if(!$this->validateCsrfToken($_POST['csrf_token'])) {
                $this->setFlashMessage('register_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
                redirect('users/register');
            }
            
            // Form verilerini işle
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'role' => isset($_POST['role']) ? trim($_POST['role']) : 'freelancer',
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'role_err' => '',
                'csrf_token' => $this->generateCsrfToken()
            ];

            // Validasyon
            if(empty($data['name'])) {
                $data['name_err'] = 'Lütfen adınızı girin';
            }

            if(empty($data['email'])) {
                $data['email_err'] = 'Lütfen e-posta adresinizi girin';
            } elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Lütfen geçerli bir e-posta adresi girin';
            } elseif($this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Bu e-posta adresi zaten kullanılıyor';
            }

            if(empty($data['password'])) {
                $data['password_err'] = 'Lütfen şifrenizi girin';
            } elseif(strlen($data['password']) < 6) {
                $data['password_err'] = 'Şifre en az 6 karakter uzunluğunda olmalı';
            }

            if(empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Lütfen şifrenizi tekrar girin';
            } elseif($data['password'] != $data['confirm_password']) {
                $data['confirm_password_err'] = 'Şifreler eşleşmiyor';
            }

            if(!in_array($data['role'], ['freelancer', 'employer'])) {
                $data['role_err'] = 'Geçersiz kullanıcı rolü';
            }

            // Hata yoksa kullanıcıyı kaydet
            if(empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err']) && empty($data['role_err'])) {
                // Şifreyi hash'le
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                
                // Kullanıcıyı kaydet
                if($user_id = $this->userModel->register($data)) {
                    // E-posta tercihlerini oluştur
                    $this->userModel->createEmailPreferences($user_id);
                    
                    // Başarılı mesajı ve yönlendirme
                    $this->setFlashMessage('login_success', 'Kaydınız başarıyla tamamlandı. Şimdi giriş yapabilirsiniz.', 'success');
                    redirect('users/login');
                } else {
                    // Hata mesajı
                    $this->setFlashMessage('register_error', 'Bir şeyler yanlış gitti. Lütfen tekrar deneyin.', 'danger');
                }
            }

            // Form verilerini view'a gönder
            $this->view('users/register', $data);
        } else {
            // GET isteği, formu göster
            $data = [
                'name' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'role' => 'freelancer',
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'role_err' => '',
                'csrf_token' => $this->generateCsrfToken()
            ];

            // View yükle
            $this->view('users/register', $data);
        }
    }

    // Kullanıcı giriş sayfası ve işlemi
    public function login() {
        // Eğer oturum açıksa ana sayfaya yönlendir
        if(isLoggedIn()) {
            redirect('');
        }

        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini temizle
            $_POST = $this->sanitizeInputArray(INPUT_POST, $_POST);

            // CSRF token kontrolü
            if(!$this->validateCsrfToken($_POST['csrf_token'])) {
                $this->setFlashMessage('login_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
                redirect('users/login');
            }
            
            // Form verilerini işle
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'remember' => isset($_POST['remember']) ? true : false,
                'email_err' => '',
                'password_err' => '',
                'csrf_token' => $this->generateCsrfToken()
            ];

            // Validasyon
            if(empty($data['email'])) {
                $data['email_err'] = 'Lütfen e-posta adresinizi girin';
            }

            if(empty($data['password'])) {
                $data['password_err'] = 'Lütfen şifrenizi girin';
            }

            // E-posta adresinin varlığını kontrol et
            if(!empty($data['email']) && !empty($data['password'])) {
                // Kullanıcı kontrol et
                $user = $this->userModel->login($data['email'], $data['password']);

                if($user) {
                    // Oturum başlat
                    $this->createUserSession($user, $data['remember']);
                } else {
                    $data['password_err'] = 'E-posta adresi veya şifre yanlış';
                }
            }

            // Hata yoksa kullanıcıyı yönlendir (zaten createUserSession içinde yapılıyor)
            if(empty($data['email_err']) && empty($data['password_err'])) {
                // Boş bırak, createUserSession içinde yönlendirme var
            } else {
                // Form verilerini view'a gönder
                $this->view('users/login', $data);
            }
        } else {
            // GET isteği, formu göster
            $data = [
                'email' => '',
                'password' => '',
                'remember' => false,
                'email_err' => '',
                'password_err' => '',
                'csrf_token' => $this->generateCsrfToken()
            ];

            // View yükle
            $this->view('users/login', $data);
        }
    }

    // Kullanıcı oturumunu oluştur
    private function createUserSession($user, $remember = false) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;
        
        // Remember me özelliği
        if($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + (30 * 24 * 60 * 60); // 30 gün
            
            // Tarayıcıda cookie ayarla
            setcookie('remember_token', $token, $expires, '/');
            
            // Veritabanında sakla (Bu işlevi User modeli içinde oluşturmanız gerekir)
            // $this->userModel->saveRememberToken($user->id, $token, $expires);
        }
        
        // Yönlendirme
        redirect('');
    }

    // Kullanıcı oturumunu kapat
    public function logout() {
        // Oturum değişkenlerini temizle
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        
        // Remember token'ı temizle
        if(isset($_COOKIE['remember_token'])) {
            // Veritabanında token'ı sil (Bu işlevi User modeli içinde oluşturmanız gerekir)
            // $this->userModel->deleteRememberToken($_COOKIE['remember_token']);
            
            // Cookie'yi sil
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Oturumu sonlandır
        session_destroy();
        
        // Ana sayfaya yönlendir
        redirect('users/login');
    }

    // Kullanıcı profili sayfası
    public function profile($id = null) {
        // Eğer id belirtilmemişse, oturum açan kullanıcının profilini göster
        if($id === null && isLoggedIn()) {
            $id = $_SESSION['user_id'];
        } elseif($id === null && !isLoggedIn()) {
            redirect('users/login');
        }
        
        // Kullanıcı bilgilerini getir
        $user = $this->userModel->getUserById($id);
        
        // Kullanıcı yoksa 404 hatası göster
        if(!$user) {
            redirect('pages/error/notfound');
        }
        
        // Kullanıcının aldığı değerlendirmeleri getir
        $reviews = $this->reviewModel->getUserReviews($id);
        
        // Kullanıcının ortalama puanını hesapla
        $rating = $this->reviewModel->getUserAverageRating($id);
        
        // Kullanıcı freelancer ise, projelerini getir
        $projects = [];
        $bids = [];
        if($user->role == 'freelancer') {
            $bids = $this->bidModel->getUserBids($id);
        } 
        // Kullanıcı işveren ise, projelerini getir
        elseif($user->role == 'employer') {
            $projects = $this->projectModel->getUserProjects($id);
        }
        
        $data = [
            'user' => $user,
            'reviews' => $reviews,
            'rating' => $rating,
            'projects' => $projects,
            'bids' => $bids
        ];
        
        $this->view('users/profile', $data);
    }

    // Kullanıcı profil düzenleme sayfası
    public function edit() {
        // Oturum kontrolü
        if(!isLoggedIn()) {
            redirect('users/login');
        }
        
        // Kullanıcının kendi profilini mi düzenlediğini kontrol et
        $user_id = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($user_id);
        
        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini temizle
            $_POST = $this->sanitizeInputArray(INPUT_POST, $_POST);
            
            // CSRF token kontrolü
            if(!$this->validateCsrfToken($_POST['csrf_token'])) {
                $this->setFlashMessage('profile_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
                redirect('users/edit');
            }
            
            // Form verilerini işle
            $data = [
                'id' => $user_id,
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'bio' => trim($_POST['bio']),
                'skills' => trim($_POST['skills']),
                'portfolio' => trim($_POST['portfolio']),
                'current_password' => trim($_POST['current_password']),
                'new_password' => trim($_POST['new_password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_err' => '',
                'email_err' => '',
                'current_password_err' => '',
                'new_password_err' => '',
                'confirm_password_err' => '',
                'csrf_token' => $this->generateCsrfToken(),
                'user' => $user
            ];
            
            // Validasyon
            if(empty($data['name'])) {
                $data['name_err'] = 'Lütfen adınızı girin';
            }
            
            if(empty($data['email'])) {
                $data['email_err'] = 'Lütfen e-posta adresinizi girin';
            } elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Lütfen geçerli bir e-posta adresi girin';
            } elseif($data['email'] != $user->email && $this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Bu e-posta adresi zaten kullanılıyor';
            }
            
            // Şifre değişikliği yapılacaksa kontrol et
            if(!empty($data['new_password']) || !empty($data['confirm_password'])) {
                // Mevcut şifre kontrolü
                if(empty($data['current_password'])) {
                    $data['current_password_err'] = 'Lütfen mevcut şifrenizi girin';
                } elseif(!password_verify($data['current_password'], $user->password)) {
                    $data['current_password_err'] = 'Mevcut şifre yanlış';
                }
                
                // Yeni şifre kontrolü
                if(empty($data['new_password'])) {
                    $data['new_password_err'] = 'Lütfen yeni şifrenizi girin';
                } elseif(strlen($data['new_password']) < 6) {
                    $data['new_password_err'] = 'Şifre en az 6 karakter uzunluğunda olmalı';
                }
                
                // Şifre onayı kontrolü
                if(empty($data['confirm_password'])) {
                    $data['confirm_password_err'] = 'Lütfen yeni şifrenizi tekrar girin';
                } elseif($data['new_password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Şifreler eşleşmiyor';
                }
                
                // Şifre değişikliği yapılacaksa, yeni şifreyi hash'le
                if(empty($data['current_password_err']) && empty($data['new_password_err']) && empty($data['confirm_password_err'])) {
                    $data['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
                }
            }
            
            // Hata yoksa kullanıcıyı güncelle
            if(empty($data['name_err']) && empty($data['email_err']) && empty($data['current_password_err']) && empty($data['new_password_err']) && empty($data['confirm_password_err'])) {
                // Kullanıcıyı güncelle
                if($this->userModel->updateUser($data)) {
                    // Başarılı mesajı
                    $this->setFlashMessage('profile_success', 'Profiliniz başarıyla güncellendi.', 'success');
                    
                    // Oturum bilgilerini güncelle
                    $_SESSION['user_name'] = $data['name'];
                    $_SESSION['user_email'] = $data['email'];
                    
                    // Profil sayfasına yönlendir
                    redirect('users/profile');
                } else {
                    // Hata mesajı
                    $this->setFlashMessage('profile_error', 'Bir şeyler yanlış gitti. Lütfen tekrar deneyin.', 'danger');
                }
            }
            
            // Form verilerini view'a gönder
            $this->view('users/edit', $data);
        } else {
            // GET isteği, formu göster
            $data = [
                'id' => $user_id,
                'name' => $user->name,
                'email' => $user->email,
                'bio' => $user->bio,
                'skills' => $user->skills,
                'portfolio' => $user->portfolio,
                'current_password' => '',
                'new_password' => '',
                'confirm_password' => '',
                'name_err' => '',
                'email_err' => '',
                'current_password_err' => '',
                'new_password_err' => '',
                'confirm_password_err' => '',
                'csrf_token' => $this->generateCsrfToken(),
                'user' => $user
            ];
            
            // View yükle
            $this->view('users/edit', $data);
        }
    }

    // E-posta tercihlerini düzenleme
    public function emailPreferences() {
        // Oturum kontrolü
        if(!isLoggedIn()) {
            redirect('users/login');
        }
        
        // Kullanıcının kendi e-posta tercihlerini mi düzenlediğini kontrol et
        $user_id = $_SESSION['user_id'];
        
        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini temizle
            $_POST = $this->sanitizeInputArray(INPUT_POST, $_POST);
            
            // CSRF token kontrolü
            if(!$this->validateCsrfToken($_POST['csrf_token'])) {
                $this->setFlashMessage('preferences_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
                redirect('users/emailPreferences');
            }
            
            // Form verilerini işle
            $data = [
                'user_id' => $user_id,
                'new_bid' => isset($_POST['new_bid']),
                'bid_accepted' => isset($_POST['bid_accepted']),
                'bid_rejected' => isset($_POST['bid_rejected']),
                'new_message' => isset($_POST['new_message']),
                'project_completed' => isset($_POST['project_completed']),
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            // Tercihleri güncelle
            if($this->userModel->updateEmailPreferences($data)) {
                // Başarılı mesajı
                $this->setFlashMessage('preferences_success', 'E-posta tercihleriniz başarıyla güncellendi.', 'success');
                redirect('users/emailPreferences');
            } else {
                // Hata mesajı
                $this->setFlashMessage('preferences_error', 'Bir şeyler yanlış gitti. Lütfen tekrar deneyin.', 'danger');
            }
        } else {
            // GET isteği, tercihleri getir
            $preferences = $this->userModel->getEmailPreferences($user_id);
            
            if(!$preferences) {
                // Tercihler yoksa oluştur
                $this->userModel->createEmailPreferences($user_id);
                $preferences = $this->userModel->getEmailPreferences($user_id);
            }
            
            $data = [
                'preferences' => $preferences,
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            // View yükle
            $this->view('users/email_preferences', $data);
        }
    }

    // Şifremi unuttum sayfası
    public function forgotPassword() {
        // Eğer oturum açıksa ana sayfaya yönlendir
        if(isLoggedIn()) {
            redirect('');
        }
        
        // POST isteği kontrolü
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini temizle
            $_POST = $this->sanitizeInputArray(INPUT_POST, $_POST);
            
            // CSRF token kontrolü
            if(!$this->validateCsrfToken($_POST['csrf_token'])) {
                $this->setFlashMessage('forgot_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'danger');
                redirect('users/forgotPassword');
            }
            
            // Form verilerini işle
            $data = [
                'email' => trim($_POST['email']),
                'email_err' => '',
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            // Validasyon
            if(empty($data['email'])) {
                $data['email_err'] = 'Lütfen e-posta adresinizi girin';
            } elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Lütfen geçerli bir e-posta adresi girin';
            } elseif(!$this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Bu e-posta adresi ile kayıtlı kullanıcı bulunamadı';
            }
            
            // Hata yoksa şifre sıfırlama e-postası gönder
            if(empty($data['email_err'])) {
                // NOT: Gerçek bir şifre sıfırlama sistemi için ek fonksiyonlar gerekir.
                // Bu örnekte sadece başarılı mesajı gösterilecek.
                
                // Başarılı mesajı
                $this->setFlashMessage('forgot_success', 'Şifre sıfırlama talimatları e-posta adresinize gönderildi.', 'success');
                redirect('users/login');
            }
            
            // Form verilerini view'a gönder
            $this->view('users/forgot_password', $data);
        } else {
            // GET isteği, formu göster
            $data = [
                'email' => '',
                'email_err' => '',
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            // View yükle
            $this->view('users/forgot_password', $data);
        }
    }
}
?> 