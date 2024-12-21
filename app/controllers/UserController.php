<?php

require_once __DIR__ . '/../models/User.php'; 
class UserController {
    private $user;
    private $s3Helper;
    
    public function __construct($db, $s3) {
        $this->user = new User($db);
        $this->s3Helper = new S3Helper($s3);
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateRegistration($_POST);
                
                // Hash password
                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                
                // Handle avatar upload if exists
                $avatarUrl = null;
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $avatarUrl = $this->s3Helper->fileUpload('avatars/', $_FILES['avatar']);
                }
                
                // Create user
                $userData = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => $hashedPassword,
                    'full_name' => $_POST['full_name'],
                    'avatar_url' => $avatarUrl
                ];
                
                if ($this->user->create($userData)) {
                    $_SESSION['success'] = 'Registration successful! Please login.';
                    header('Location: index.php?action=login');
                    exit;
                }
            } catch (Exception $e) {
                return ['error' => $e->getMessage()];
            }
        }
        require_once __DIR__ . '/../views/users/register.php';
    }
    
    // public function login() {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         try {
    //             $user = $this->user->findByEmail($_POST['email']);
                
    //             if (!$user || !password_verify($_POST['password'], $user['password'])) {
    //                 throw new Exception('Invalid email or password');
    //             }
                
    //             // Set session
    //             $_SESSION['user_id'] = $user['id'];
    //             $_SESSION['username'] = $user['username'];
                
    //             header('Location: /');
    //             exit;
    //         } catch (Exception $e) {
    //             return ['error' => $e->getMessage()];
    //         }
    //     }
    //     require_once __DIR__ . '/../views/users/login.php';
    // }
    public function login() {
        $error = null; // Initialize error variable
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $user = $this->user->findByEmail($_POST['email']);
                
                if (!$user || !password_verify($_POST['password'], $user['password'])) {
                    $error = 'Invalid email or password';
                    require_once __DIR__ . '/../views/users/login.php';
                    return;
                }
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                header('Location: /');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/users/login.php';
                return;
            }
        }
        require_once __DIR__ . '/../views/users/login.php';
    }
    
    public function profile($username = null) {
        if (!$username) {
            // If no username provided, show logged in user's profile
            if (!isset($_SESSION['user_id'])) {
                header('Location: /login');
                exit;
            }
            $username = $_SESSION['username'];
        }
        
        $user = $this->user->getProfile($username);
        $videos = $this->user->getUserVideos($user['id']);
        $stats = $this->user->getUserStats($user['id']);
        
        require_once __DIR__ . '/../views/users/profile.php';
    }
    
    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $updates = [];
                
                // Handle avatar update
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $updates['avatar_url'] = $this->s3Helper->fileUpload('avatars/', $_FILES['avatar']);
                }
                
                // Handle other field updates
                $allowedFields = ['full_name', 'bio'];
                foreach ($allowedFields as $field) {
                    if (isset($_POST[$field])) {
                        $updates[$field] = $_POST[$field];
                    }
                }
                
                if ($this->user->update($_SESSION['user_id'], $updates)) {
                    $_SESSION['success'] = 'Profile updated successfully!';
                    header('Location: /profile');
                    exit;
                }
            } catch (Exception $e) {
                return ['error' => $e->getMessage()];
            }
        }
        
        $user = $this->user->findById($_SESSION['user_id']);
        require_once __DIR__ . '/../views/users/edit.php';
    }
    
    public function follow($userId) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        
        try {
            $result = $this->user->toggleFollow($_SESSION['user_id'], $userId);
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    private function validateRegistration($data) {
        if (empty($data['username']) || !preg_match('/^[a-zA-Z0-9_]{3,20}$/', $data['username'])) {
            throw new Exception('Invalid username format');
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        if (empty($data['password']) || strlen($data['password']) < 8) {
            throw new Exception('Password must be at least 8 characters long');
        }
        
        if ($data['password'] !== $data['password_confirm']) {
            throw new Exception('Passwords do not match');
        }
        
        // Check if username or email already exists
        if ($this->user->findByUsername($data['username'])) {
            throw new Exception('Username already taken');
        }
        
        if ($this->user->findByEmail($data['email'])) {
            throw new Exception('Email already registered');
        }
    }
}