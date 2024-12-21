<?php

class Video {
    private $db;
    private $table_name = "videos";

    public $id;
    public $title;
    public $description;
    public $user_id;
    public $filename;
    public $s3_url;
    public $thumbnail_url;
    public $view_count;
    public $is_featured;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy tất cả video
    public function getAll() {
        $sql = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithUserInfo() {
        $sql = "SELECT v.*, u.username, u.avatar_url 
                FROM " . $this->table_name . " v
                JOIN users u ON v.user_id = u.id
                ORDER BY v.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tạo video mới
    public function create() {
        $sql = "INSERT INTO " . $this->table_name . " (title, description, user_id, filename, s3_url, thumbnail_url, created_at) 
                VALUES (:title, :description, :user_id, :filename, :s3_url, :thumbnail_url, :created_at)";
        $stmt = $this->db->prepare($sql);

        $this->created_at = date('Y-m-d H:i:s');

        return $stmt->execute([
            ':title' => htmlspecialchars(strip_tags($this->title)),
            ':description' => htmlspecialchars(strip_tags($this->description)),
            ':user_id' => $this->user_id,
            ':filename' => htmlspecialchars(strip_tags($this->filename)),
            ':s3_url' => htmlspecialchars(strip_tags($this->s3_url)),
            ':thumbnail_url' => htmlspecialchars(strip_tags($this->thumbnail_url)),
            ':created_at' => $this->created_at
        ]);
    }

    // Tăng lượt xem video
    public function incrementViewCount($videoId) {
        $sql = "UPDATE " . $this->table_name . " SET view_count = view_count + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $videoId]);
    }

    // Thích video
    public function likeVideo($videoId, $userId) {
        $sql = "INSERT INTO likes (video_id, user_id) VALUES (:video_id, :user_id) ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':video_id' => $videoId, ':user_id' => $userId]);
    }

    // Bình luận video
    public function commentVideo($videoId, $userId, $content) {
        $sql = "INSERT INTO comments (video_id, user_id, content, created_at) VALUES (:video_id, :user_id, :content, :created_at)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':video_id' => $videoId,
            ':user_id' => $userId,
            ':content' => htmlspecialchars(strip_tags($content)),
            ':created_at' => date('Y-m-d H:i:s')
        ]);
    }



    public function getFeed($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT v.*, u.username, u.avatar_url, 
                (SELECT COUNT(*) FROM likes WHERE video_id = v.id) as likes_count,
                (SELECT COUNT(*) FROM comments WHERE video_id = v.id) as comments_count
                FROM " . $this->table_name . " v
                JOIN users u ON v.user_id = u.id
                ORDER BY v.created_at DESC 
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function search($query, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT v.*, u.username 
                FROM " . $this->table_name . " v
                JOIN users u ON v.user_id = u.id
                WHERE v.title LIKE :query 
                OR v.description LIKE :query
                ORDER BY v.created_at DESC
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':query', "%{$query}%");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
