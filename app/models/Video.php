<?php

class Video
{
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

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Lấy tất cả video
    public function getAll()
    {
        $sql = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithUserInfo()
    {
        $sql = "SELECT v.*, u.username, u.avatar_url 
                FROM " . $this->table_name . " v
                JOIN users u ON v.user_id = u.id
                ORDER BY v.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tạo video mới
    public function create()
    {
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
    public function incrementViewCount($videoId)
    {
        $sql = "UPDATE " . $this->table_name . " SET view_count = view_count + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $videoId]);
    }

    // Thích video
    public function likeVideo($videoId, $userId)
    {
        $sql = "INSERT INTO likes (video_id, user_id) VALUES (:video_id, :user_id) ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':video_id' => $videoId, ':user_id' => $userId]);
    }

    // Bình luận video
    public function commentVideo($videoId, $userId, $content)
    {
        $sql = "INSERT INTO comments (video_id, user_id, content, created_at) VALUES (:video_id, :user_id, :content, :created_at)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':video_id' => $videoId,
            ':user_id' => $userId,
            ':content' => htmlspecialchars(strip_tags($content)),
            ':created_at' => date('Y-m-d H:i:s')
        ]);
    }




    public function search($query, $page = 1, $limit = 10)
    {
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




    public function delete($videoId, $userId)
    {
        try {
            // First verify the video belongs to the user
            $sql = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $videoId,
                ':user_id' => $userId
            ]);

            $video = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$video) {
                return false;
            }

            // Delete from database
            $sql = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':id' => $videoId,
                ':user_id' => $userId
            ]);

            if ($result) {
                // Note: You might want to also delete related records (comments, likes, etc.)
                $this->deleteRelatedRecords($videoId);
            }

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error deleting video: " . $e->getMessage());
        }
    }

    private function deleteRelatedRecords($videoId)
    {
        // Delete related comments
        $sql = "DELETE FROM comments WHERE video_id = :video_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':video_id' => $videoId]);

        // Delete related likes
        $sql = "DELETE FROM likes WHERE video_id = :video_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':video_id' => $videoId]);
    }



    public function update($videoId, $userId, $data)
    {
        try {
            // First verify the video belongs to the user
            $sql = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $videoId,
                ':user_id' => $userId
            ]);

            $video = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$video) {
                return false;
            }

            // Build update query dynamically based on provided data
            $updateFields = [];
            $params = [':id' => $videoId, ':user_id' => $userId];

            // Handle each updatable field
            if (isset($data['title'])) {
                $updateFields[] = "title = :title";
                $params[':title'] = htmlspecialchars(strip_tags($data['title']));
            }
            if (isset($data['description'])) {
                $updateFields[] = "description = :description";
                $params[':description'] = htmlspecialchars(strip_tags($data['description']));
            }
            if (isset($data['s3_url'])) {
                $updateFields[] = "s3_url = :s3_url";
                $params[':s3_url'] = htmlspecialchars(strip_tags($data['s3_url']));
            }
            if (isset($data['thumbnail_url'])) {
                $updateFields[] = "thumbnail_url = :thumbnail_url";
                $params[':thumbnail_url'] = htmlspecialchars(strip_tags($data['thumbnail_url']));
            }

            $updateFields[] = "updated_at = :updated_at";
            $params[':updated_at'] = date('Y-m-d H:i:s');

            if (empty($updateFields)) {
                return false;
            }

            $sql = "UPDATE " . $this->table_name . " 
                    SET " . implode(", ", $updateFields) . " 
                    WHERE id = :id AND user_id = :user_id";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Error updating video: " . $e->getMessage());
        }
    }


    public function getById($videoId)
    {
        $sql = "SELECT v.*, u.username 
                FROM " . $this->table_name . " v
                JOIN users u ON v.user_id = u.id 
                WHERE v.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $videoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
