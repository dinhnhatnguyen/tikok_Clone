<?php



class User {
    private $db;
    private $table_name = "users";

    public function __construct($db) {
        $this->db = $db;
    }

    // public function create($data) {
    //     $sql = "INSERT INTO " . $this->table_name . " 
    //             (username, email, password, full_name, avatar_url) 
    //             VALUES (:username, :email, :password, :full_name, :avatar_url)";
                
    //     $stmt = $this->db->prepare($sql);
        
    //     return $stmt->execute([
    //         ':username' => htmlspecialchars(strip_tags($data['username'])),
    //         ':email' => htmlspecialchars(strip_tags($data['email'])),
    //         ':password' => $data['password'],
    //         ':full_name' => htmlspecialchars(strip_tags($data['full_name'])),
    //         ':avatar_url' => $data['avatar_url']
    //     ]);
    // }
    public function create($data) {
        try {
            $sql = "INSERT INTO " . $this->table_name . " 
                    (username, email, password, full_name, avatar_url) 
                    VALUES (:username, :email, :password, :full_name, :avatar_url)";
                    
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':username' => htmlspecialchars(strip_tags($data['username'])),
                ':email' => htmlspecialchars(strip_tags($data['email'])),
                ':password' => $data['password'],
                ':full_name' => htmlspecialchars(strip_tags($data['full_name'])),
                ':avatar_url' => $data['avatar_url']
            ]);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    // Đăng ký người dùng
    public function register($username, $email, $password, $fullName = null, $avatarUrl = null) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (username, email, password, full_name, avatar_url) VALUES (:username, :email, :password, :fullName, :avatarUrl)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':fullName' => $fullName,
            ':avatarUrl' => $avatarUrl
        ]);
    }

    // Đăng nhập người dùng
    public function login($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email AND is_active = true";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Trả về thông tin người dùng nếu đăng nhập thành công
        }
        return false; // Thất bại
    }

    // Theo dõi người dùng khác
    public function follow($followerId, $followedId) {
        if ($followerId === $followedId) {
            return false; // Không thể tự theo dõi chính mình
        }

        $sql = "INSERT INTO follows (follower_id, followed_id) VALUES (:followerId, :followedId) ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':followerId' => $followerId,
            ':followedId' => $followedId
        ]);
    }

    // Bỏ theo dõi người dùng khác
    public function unfollow($followerId, $followedId) {
        $sql = "DELETE FROM follows WHERE follower_id = :followerId AND followed_id = :followedId";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':followerId' => $followerId,
            ':followedId' => $followedId
        ]);
    }

    // Kiểm tra trạng thái theo dõi
    public function isFollowing($followerId, $followedId) {
        $sql = "SELECT 1 FROM follows WHERE follower_id = :followerId AND followed_id = :followedId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':followerId' => $followerId,
            ':followedId' => $followedId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Lấy danh sách người theo dõi
    public function getFollowers($userId) {
        $sql = "SELECT users.* FROM follows INNER JOIN users ON follows.follower_id = users.id WHERE follows.followed_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách người đang theo dõi
    public function getFollowing($userId) {
        $sql = "SELECT users.* FROM follows INNER JOIN users ON follows.followed_id = users.id WHERE follows.follower_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tìm người dùng theo tên đăng nhập
    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tìm người dùng theo ID
    public function findById($userId) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin người dùng
    public function update($userId, $updates) {
        $set = [];
        foreach ($updates as $key => $value) {
            $set[] = "$key = :$key";
        }
        $setSql = implode(', ', $set);
        
        $sql = "UPDATE users SET $setSql WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $updates['id'] = $userId;
        return $stmt->execute($updates);
    }

    // Lấy thông tin người dùng (profile)
    public function getProfile($username) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy video của người dùng
    public function getUserVideos($userId) {
        $sql = "SELECT * FROM videos WHERE user_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thống kê người dùng
    public function getUserStats($userId) {
        // Thống kê có thể bao gồm số lượng video, người theo dõi, v.v.
        // Ví dụ đơn giản:
        return [
            'video_count' => $this->countUserVideos($userId),
            // Thêm các thống kê khác nếu cần
        ];
    }

    // Đếm số lượng video của người dùng
    private function countUserVideos($userId) {
        $sql = "SELECT COUNT(*) FROM videos WHERE user_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchColumn();
    }

    // Thay đổi theo dõi
    public function toggleFollow($followerId, $followedId) {
        if ($this->isFollowing($followerId, $followedId)) {
            return $this->unfollow($followerId, $followedId);
        } else {
            return $this->follow($followerId, $followedId);
        }
    }
}

// Ví dụ sử dụng:
// $db = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
// $user = new User($db);
// $user->register('john_doe', 'john@example.com', 'securepassword');
