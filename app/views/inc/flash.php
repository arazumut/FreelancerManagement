<?php
// Tüm flash mesajlarını göster
if(isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])) {
    foreach($_SESSION['flash_messages'] as $name => $message) {
        echo '<div class="alert alert-' . $message['type'] . ' alert-dismissible fade show" role="alert">';
        echo $message['message'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        // Mesajı gösterdikten sonra sil
        unset($_SESSION['flash_messages'][$name]);
    }
}
?> 