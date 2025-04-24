<?php
// Hata raporlama
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Config Dosyasını Yükle
require_once 'config/config.php';

// Helpers Yükle
require_once 'helpers/session_helper.php';

// Core Kütüphanelerini Otomatik Yükle
spl_autoload_register(function($className) {
    require_once 'core/' . $className . '.php';
});
?> 