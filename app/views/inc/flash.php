<?php
// Tüm flash mesajlarını göster
if(isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])) {
    foreach($_SESSION['flash_messages'] as $name => $message) {
        echo '<div class="alert alert-' . $message['type'] . ' alert-dismissible fade show" role="alert" data-aos="fade-down" data-aos-duration="800">';
        echo $message['message'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
    unset($_SESSION['flash_messages']);
}

if(isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert" data-aos="fade-down" data-aos-duration="800" data-aos-delay="100">';
    echo '<ul class="mb-0">';
    foreach($_SESSION['form_errors'] as $error) {
        echo '<li>' . $error . '</li>';
    }
    echo '</ul>';
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['form_errors']);
}
?> 