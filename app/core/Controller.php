<?php
/**
 * Temel Controller Sınıfı
 * Tüm controller'lar bu sınıftan türetilecek
 */
class Controller {
    // Model yükle
    protected function model($model) {
        // Model dosyasını kontrol et
        if(file_exists('../app/models/' . $model . '.php')) {
            // Model dosyasını dahil et
            require_once '../app/models/' . $model . '.php';
            // Model sınıfını örnekle ve döndür
            return new $model();
        } else {
            die('Model bulunamadı.');
        }
    }

    // View yükle
    protected function view($view, $data = []) {
        // View dosyasını kontrol et
        if(file_exists('../app/views/' . $view . '.php')) {
            // View dosyasını dahil et
            require_once '../app/views/' . $view . '.php';
        } else {
            die('View bulunamadı.');
        }
    }

    // Sayfaya yönlendir
    protected function redirect($page) {
        header('Location: ' . SITE_URL . '/' . $page);
    }

    // Flash mesaj oluştur
    protected function setFlashMessage($name, $message, $type = 'success') {
        if(!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        $_SESSION['flash_messages'][$name] = [
            'message' => $message,
            'type' => $type
        ];
    }

    // Flash mesajları getir
    protected function getFlashMessage($name) {
        if(isset($_SESSION['flash_messages'][$name])) {
            $flash = $_SESSION['flash_messages'][$name];
            unset($_SESSION['flash_messages'][$name]);
            return $flash;
        }
        return null;
    }

    // CSRF token oluştur
    protected function generateCsrfToken() {
        if(!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // CSRF token kontrol et
    protected function validateCsrfToken($token) {
        if(isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token) {
            // Token kullanıldıktan sonra yenisini oluştur
            //unset($_SESSION['csrf_token']);
            return true;
        }
        return false;
    }
    
    // Form verilerini temizle (FILTER_SANITIZE_STRING yerine)
    protected function sanitizeInputArray($type, $data) {
        $sanitizedData = [];
        
        if($type === INPUT_POST && isset($_POST)) {
            foreach($_POST as $key => $value) {
                if(is_string($value)) {
                    // String değerleri temizle
                    $sanitizedData[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                } elseif(is_array($value)) {
                    // Dizi ise, her elemanı temizle
                    $sanitizedData[$key] = [];
                    foreach($value as $arrayKey => $arrayValue) {
                        if(is_string($arrayValue)) {
                            $sanitizedData[$key][$arrayKey] = htmlspecialchars($arrayValue, ENT_QUOTES, 'UTF-8');
                        } else {
                            $sanitizedData[$key][$arrayKey] = $arrayValue;
                        }
                    }
                } else {
                    // Diğer veri tipleri
                    $sanitizedData[$key] = $value;
                }
            }
        } elseif($type === INPUT_GET && isset($_GET)) {
            foreach($_GET as $key => $value) {
                if(is_string($value)) {
                    $sanitizedData[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                } elseif(is_array($value)) {
                    $sanitizedData[$key] = [];
                    foreach($value as $arrayKey => $arrayValue) {
                        if(is_string($arrayValue)) {
                            $sanitizedData[$key][$arrayKey] = htmlspecialchars($arrayValue, ENT_QUOTES, 'UTF-8');
                        } else {
                            $sanitizedData[$key][$arrayKey] = $arrayValue;
                        }
                    }
                } else {
                    $sanitizedData[$key] = $value;
                }
            }
        }
        
        return $sanitizedData;
    }
}
?> 