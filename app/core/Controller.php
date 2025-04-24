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
            return true;
        }
        return false;
    }
}
?> 