<?php
// PHPMailer ile e-posta gönderimi
// Not: Bu fonksiyonları kullanmak için PHPMailer kütüphanesini yüklemeniz gerekir
// composer require phpmailer/phpmailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * E-posta gönderme işlevi
 * @param string $to Alıcı e-posta adresi
 * @param string $subject E-posta konusu
 * @param string $body E-posta içeriği (HTML)
 * @param string $altBody Alternatif metin içerik
 * @return bool Gönderim başarılı mı?
 */
function sendEmail($to, $subject, $body, $altBody = '') {
    // E-posta tercihi kontrolü
    if (!isEmailEnabled()) {
        return false;
    }

    // PHPMailer kullanılabilir mi kontrol et
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        // PHPMailer yüklü değilse basit mail() fonksiyonu ile gönder
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Freelancer Platform <noreply@example.com>' . "\r\n";
        
        return mail($to, $subject, $body, $headers);
    }

    // PHPMailer ile gönder
    try {
        $mail = new PHPMailer(true);
        
        // SMTP ayarları
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // SMTP sunucusu
        $mail->SMTPAuth = true;
        $mail->Username = 'user@example.com'; // SMTP kullanıcı adı
        $mail->Password = 'password'; // SMTP şifre
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Alıcı ve içerik
        $mail->setFrom('noreply@example.com', 'Freelancer Platform');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);
        
        return $mail->send();
    } catch (Exception $e) {
        error_log('E-posta gönderme hatası: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Yeni teklif bildirimi gönder
 * @param object $bid Teklif verisi
 * @return bool Gönderim başarılı mı?
 */
function sendNewBidNotification($bid) {
    // Kullanıcının e-posta tercihlerini kontrol et
    $userModel = new User();
    $preferences = $userModel->getEmailPreferences($bid->employer_id);
    
    if (!$preferences || !$preferences->new_bid) {
        return false;
    }
    
    $to = $bid->employer_email;
    $subject = 'Yeni Teklif Alındı - ' . $bid->project_title;
    
    $body = '<h1>Yeni Teklif</h1>';
    $body .= '<p>' . $bid->freelancer_name . ' projeniz için teklif verdi.</p>';
    $body .= '<p><strong>Proje:</strong> ' . $bid->project_title . '</p>';
    $body .= '<p><strong>Teklif Tutarı:</strong> ' . $bid->amount . ' TL</p>';
    $body .= '<p><strong>Teslim Süresi:</strong> ' . $bid->delivery_time . ' gün</p>';
    $body .= '<p><strong>Açıklama:</strong> ' . $bid->description . '</p>';
    $body .= '<p><a href="' . SITE_URL . '/projects/view/' . $bid->project_id . '">Projeyi Görüntüle</a></p>';
    
    return sendEmail($to, $subject, $body);
}

/**
 * Teklif kabul edildi bildirimi gönder
 * @param object $bid Teklif verisi
 * @return bool Gönderim başarılı mı?
 */
function sendBidAcceptedNotification($bid) {
    // Kullanıcının e-posta tercihlerini kontrol et
    $userModel = new User();
    $preferences = $userModel->getEmailPreferences($bid->user_id);
    
    if (!$preferences || !$preferences->bid_accepted) {
        return false;
    }
    
    $to = $bid->freelancer_email;
    $subject = 'Teklifiniz Kabul Edildi - ' . $bid->project_title;
    
    $body = '<h1>Tebrikler!</h1>';
    $body .= '<p>' . $bid->project_title . ' projesi için verdiğiniz teklif kabul edildi.</p>';
    $body .= '<p>Proje detaylarını görüntülemek ve işe başlamak için aşağıdaki bağlantıya tıklayın:</p>';
    $body .= '<p><a href="' . SITE_URL . '/projects/view/' . $bid->project_id . '">Projeyi Görüntüle</a></p>';
    
    return sendEmail($to, $subject, $body);
}

/**
 * Teklif reddedildi bildirimi gönder
 * @param object $bid Teklif verisi
 * @return bool Gönderim başarılı mı?
 */
function sendBidRejectedNotification($bid) {
    // Kullanıcının e-posta tercihlerini kontrol et
    $userModel = new User();
    $preferences = $userModel->getEmailPreferences($bid->user_id);
    
    if (!$preferences || !$preferences->bid_rejected) {
        return false;
    }
    
    $to = $bid->freelancer_email;
    $subject = 'Teklifiniz Reddedildi - ' . $bid->project_title;
    
    $body = '<h1>Bilgilendirme</h1>';
    $body .= '<p>' . $bid->project_title . ' projesi için verdiğiniz teklif reddedildi.</p>';
    $body .= '<p>Diğer projelere göz atmak için platformumuzu ziyaret etmeye devam edin.</p>';
    $body .= '<p><a href="' . SITE_URL . '/projects">Projeleri Keşfet</a></p>';
    
    return sendEmail($to, $subject, $body);
}

/**
 * Proje tamamlandı bildirimi gönder
 * @param object $contract Sözleşme verisi
 * @return bool Gönderim başarılı mı?
 */
function sendProjectCompletedNotification($contract) {
    // Kullanıcının e-posta tercihlerini kontrol et
    $userModel = new User();
    $preferences = $userModel->getEmailPreferences($contract->employer_id);
    
    if (!$preferences || !$preferences->project_completed) {
        return false;
    }
    
    $to = $contract->employer_email;
    $subject = 'Proje Tamamlandı - ' . $contract->project_title;
    
    $body = '<h1>Proje Tamamlandı</h1>';
    $body .= '<p>' . $contract->freelancer_name . ' tarafından ' . $contract->project_title . ' projesi tamamlandı.</p>';
    $body .= '<p>Projeyi incelemek ve onaylamak için aşağıdaki bağlantıya tıklayın:</p>';
    $body .= '<p><a href="' . SITE_URL . '/contracts/view/' . $contract->id . '">Sözleşmeyi Görüntüle</a></p>';
    
    return sendEmail($to, $subject, $body);
}

/**
 * E-posta gönderimi etkin mi kontrol et
 * @return bool E-posta gönderimi etkin mi?
 */
function isEmailEnabled() {
    // Burada e-posta gönderiminin etkin olup olmadığı kontrol edilebilir
    // Örneğin, bir konfigürasyon değişkeni ile
    return true; // Varsayılan olarak etkin
}
?> 