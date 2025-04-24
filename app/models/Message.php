<?php
class Message {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Mesaj gönder
    public function sendMessage($data) {
        $this->db->query('INSERT INTO messages (sender_id, receiver_id, subject, message) 
                          VALUES(:sender_id, :receiver_id, :subject, :message)');
        
        $this->db->bind(':sender_id', $data['sender_id']);
        $this->db->bind(':receiver_id', $data['receiver_id']);
        $this->db->bind(':subject', $data['subject']);
        $this->db->bind(':message', $data['message']);
        
        return $this->db->execute();
    }

    // Alınan mesajları getir
    public function getInboxMessages($user_id) {
        $this->db->query('SELECT m.*, u.name as sender_name 
                          FROM messages m 
                          JOIN users u ON m.sender_id = u.id 
                          WHERE m.receiver_id = :user_id 
                          ORDER BY m.created_at DESC');
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->resultSet();
    }

    // Gönderilen mesajları getir
    public function getSentMessages($user_id) {
        $this->db->query('SELECT m.*, u.name as receiver_name 
                          FROM messages m 
                          JOIN users u ON m.receiver_id = u.id 
                          WHERE m.sender_id = :user_id 
                          ORDER BY m.created_at DESC');
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->resultSet();
    }

    // ID'ye göre mesaj getir
    public function getMessageById($id) {
        $this->db->query('SELECT m.*, 
                          s.name as sender_name, s.email as sender_email,
                          r.name as receiver_name, r.email as receiver_email
                          FROM messages m 
                          JOIN users s ON m.sender_id = s.id 
                          JOIN users r ON m.receiver_id = r.id 
                          WHERE m.id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    // Mesajı okundu olarak işaretle
    public function markAsRead($id) {
        $this->db->query('UPDATE messages SET is_read = 1 WHERE id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    // Mesajı sil
    public function deleteMessage($id) {
        $this->db->query('DELETE FROM messages WHERE id = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    // Okunmamış mesaj sayısını getir
    public function getUnreadMessageCount($user_id) {
        $this->db->query('SELECT COUNT(*) as count FROM messages WHERE receiver_id = :user_id AND is_read = 0');
        
        $this->db->bind(':user_id', $user_id);
        
        $result = $this->db->single();
        return $result->count;
    }
    
    // Admin için tüm mesajları getir
    public function getAllMessages() {
        $this->db->query('SELECT m.*, 
                          s.name as sender_name, 
                          r.name as receiver_name
                          FROM messages m 
                          JOIN users s ON m.sender_id = s.id 
                          JOIN users r ON m.receiver_id = r.id 
                          ORDER BY m.created_at DESC');
        
        return $this->db->resultSet();
    }
    
    // Toplam mesaj sayısını getir
    public function getTotalMessages() {
        $this->db->query('SELECT COUNT(*) as total FROM messages');
        $result = $this->db->single();
        return $result->total;
    }
    
    // Son mesajları getir
    public function getRecentMessages($limit = 5) {
        $this->db->query('SELECT m.*, 
                          s.name as sender_name, 
                          r.name as receiver_name
                          FROM messages m 
                          JOIN users s ON m.sender_id = s.id 
                          JOIN users r ON m.receiver_id = r.id 
                          ORDER BY m.created_at DESC
                          LIMIT :limit');
        
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
}
?> 