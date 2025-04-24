<?php
class Notification {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Bildirim ekle
    public function addNotification($data) {
        $this->db->query('INSERT INTO notifications (user_id, message, type, related_id) 
                          VALUES(:user_id, :message, :type, :related_id)');
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':message', $data['message']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':related_id', $data['related_id']);
        
        return $this->db->execute();
    }

    // Kullanıcının bildirimlerini getir
    public function getUserNotifications($user_id) {
        $this->db->query('SELECT * FROM notifications 
                          WHERE user_id = :user_id 
                          ORDER BY created_at DESC');
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->resultSet();
    }

    // Kullanıcının okunmamış bildirimlerini getir
    public function getUserUnreadNotifications($user_id) {
        $this->db->query('SELECT * FROM notifications 
                          WHERE user_id = :user_id AND is_read = 0 
                          ORDER BY created_at DESC');
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->resultSet();
    }

    // Bildirim ID'sine göre bildirim getir
    public function getNotificationById($id) {
        $this->db->query('SELECT * FROM notifications WHERE id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    // Bildirim okundu olarak işaretle
    public function markAsRead($id) {
        $this->db->query('UPDATE notifications SET is_read = 1 WHERE id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    // Kullanıcının tüm bildirimlerini okundu olarak işaretle
    public function markAllAsRead($user_id) {
        $this->db->query('UPDATE notifications SET is_read = 1 WHERE user_id = :user_id');
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->execute();
    }

    // Bildirim sil
    public function deleteNotification($id) {
        $this->db->query('DELETE FROM notifications WHERE id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    // Kullanıcının tüm bildirimlerini sil
    public function deleteAllNotifications($user_id) {
        $this->db->query('DELETE FROM notifications WHERE user_id = :user_id');
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->execute();
    }

    // Kullanıcının okunmamış bildirim sayısını getir
    public function getUnreadCount($user_id) {
        $this->db->query('SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND is_read = 0');
        
        $this->db->bind(':user_id', $user_id);
        
        $result = $this->db->single();
        
        return $result->count;
    }

    // Yeni teklif bildirimi oluştur
    public function newBidNotification($project_id, $employer_id, $freelancer_name) {
        $project = $this->getProjectTitle($project_id);
        
        $data = [
            'user_id' => $employer_id,
            'message' => $freelancer_name . ' "' . $project->title . '" projeniz için yeni bir teklif verdi.',
            'type' => 'new_bid',
            'related_id' => $project_id
        ];
        
        return $this->addNotification($data);
    }

    // Teklif kabul edildi bildirimi oluştur
    public function bidAcceptedNotification($project_id, $freelancer_id, $employer_name) {
        $project = $this->getProjectTitle($project_id);
        
        $data = [
            'user_id' => $freelancer_id,
            'message' => $employer_name . ' "' . $project->title . '" projesi için teklifinizi kabul etti.',
            'type' => 'bid_accepted',
            'related_id' => $project_id
        ];
        
        return $this->addNotification($data);
    }

    // Teklif reddedildi bildirimi oluştur
    public function bidRejectedNotification($project_id, $freelancer_id, $employer_name) {
        $project = $this->getProjectTitle($project_id);
        
        $data = [
            'user_id' => $freelancer_id,
            'message' => $employer_name . ' "' . $project->title . '" projesi için teklifinizi reddetti.',
            'type' => 'bid_rejected',
            'related_id' => $project_id
        ];
        
        return $this->addNotification($data);
    }

    // Proje tamamlandı bildirimi oluştur
    public function projectCompletedNotification($project_id, $employer_id, $freelancer_name) {
        $project = $this->getProjectTitle($project_id);
        
        $data = [
            'user_id' => $employer_id,
            'message' => $freelancer_name . ' "' . $project->title . '" projesini tamamladı.',
            'type' => 'project_completed',
            'related_id' => $project_id
        ];
        
        return $this->addNotification($data);
    }

    // Proje revize bildirimi oluştur
    public function projectRevisionNotification($project_id, $freelancer_id, $employer_name) {
        $project = $this->getProjectTitle($project_id);
        
        $data = [
            'user_id' => $freelancer_id,
            'message' => $employer_name . ' "' . $project->title . '" projesi için revizyon talep etti.',
            'type' => 'project_revision',
            'related_id' => $project_id
        ];
        
        return $this->addNotification($data);
    }

    // Ödeme alındı bildirimi oluştur
    public function paymentReceivedNotification($project_id, $freelancer_id, $amount) {
        $project = $this->getProjectTitle($project_id);
        
        $data = [
            'user_id' => $freelancer_id,
            'message' => $project->title . ' projesi için ' . $amount . ' TL ödeme alındı.',
            'type' => 'payment_received',
            'related_id' => $project_id
        ];
        
        return $this->addNotification($data);
    }

    // Değerlendirme bildirimi oluştur
    public function reviewAddedNotification($project_id, $target_user_id, $reviewer_name) {
        $project = $this->getProjectTitle($project_id);
        
        $data = [
            'user_id' => $target_user_id,
            'message' => $reviewer_name . ' "' . $project->title . '" projesi için sizi değerlendirdi.',
            'type' => 'review_added',
            'related_id' => $project_id
        ];
        
        return $this->addNotification($data);
    }

    // Proje başlığını getir (yardımcı fonksiyon)
    private function getProjectTitle($project_id) {
        $this->db->query('SELECT title FROM projects WHERE id = :id');
        $this->db->bind(':id', $project_id);
        return $this->db->single();
    }
}
?> 