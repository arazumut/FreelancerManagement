<?php
// Özel oturum dizini ayarla
$session_path = dirname(dirname(dirname(__FILE__))) . '/tmp/sessions';
if (!is_dir($session_path)) {
    mkdir($session_path, 0777, true);
}
session_save_path($session_path);

session_start();

// Kullanıcı oturum kontrolü
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Kullanıcı yetkisi kontrolü
function isAuthorized($role) {
    if(isLoggedIn() && isset($_SESSION['user_role'])) {
        return $_SESSION['user_role'] === $role;
    }
    return false;
}

// Admin kontrolü
function isAdmin() {
    return isAuthorized('admin');
}

// Freelancer kontrolü
function isFreelancer() {
    return isAuthorized('freelancer');
}

// İşveren kontrolü
function isEmployer() {
    return isAuthorized('employer');
}

// Oturum kontrolü - yetkisiz erişimi engelleme
function requireLogin() {
    if(!isLoggedIn()) {
        redirect('users/login');
    }
}

// Yetki kontrolü - yetkisiz erişimi engelleme
function requireRole($role) {
    if(!isAuthorized($role)) {
        redirect('pages/error/unauthorized');
    }
}

// Yönlendirme yardımcı fonksiyonu
function redirect($page) {
    header('Location: ' . SITE_URL . '/' . $page);
    exit;
}

// CSRF token oluştur
function generateCsrfToken() {
    if(!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF token kontrol et
function validateCsrfToken($token) {
    if(isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token) {
        // Token kullanıldıktan sonra yenisini oluştur
        unset($_SESSION['csrf_token']);
        return true;
    }
    return false;
}

// Flash mesajı oluştur
function setFlashMessage($name, $message, $type = 'success') {
    if(!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][$name] = [
        'message' => $message,
        'type' => $type
    ];
}

// Flash mesajını getir
function getFlashMessage($name) {
    if(isset($_SESSION['flash_messages'][$name])) {
        $flash = $_SESSION['flash_messages'][$name];
        unset($_SESSION['flash_messages'][$name]);
        return $flash;
    }
    return null;
}

// Tüm flash mesajlarını getir
function getAllFlashMessages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    $_SESSION['flash_messages'] = [];
    return $messages;
}

// Input sanitize etme
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}
?> 