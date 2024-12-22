<?php
session_start();
require_once __DIR__ . '/app/config/Database.php';
require_once __DIR__ . '/app/config/S3Config.php';
require_once __DIR__ . '/app/controllers/VideoController.php';
require_once __DIR__ . '/app/controllers/UserController.php';

// Khởi tạo kết nối dữ liệu
$db = (new Database())->getConnection();

// Khởi tạo những class để xử lý file với AWS S3
$s3Config = new S3Config();
$s3Client = $s3Config->getS3Client();

// Khởi tạo controller
$videoController = new VideoController($db, $s3Client);
$userController = new UserController($db, $s3Client);

// Xác định hành động từ URL
$action = $_GET['action'] ?? 'index';
$user_id = $_GET['user_id'] ?? null;
$username = $_GET['username'] ?? null;




// Gọi hàm tương ứng
switch ($action) {
    case 'upload':
        $videoController->upload();
        break;
    case 'feed':
        $videoController->feed();
        break;
    case 'register':
        $userController->register();
        break;
    case 'login':
        $userController->login();
        break;
    case 'logout':
        $userController->logout();
        break;
    case 'follow':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if (!$user_id) {
            $_SESSION['error'] = 'User ID is required';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $userController->follow($user_id);
        header('Location: ' . $_SERVER['HTTP_REFERER']); // Quay lại trang trước đó
        exit;
        break;
    case 'profile':
        $userController->profile($_SESSION['username'] ?? null);
        break;

    case 'delete':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $videoId = $_GET['id'] ?? null;
        if (!$videoId) {
            $_SESSION['error'] = 'Video ID is required';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $videoController->delete($videoId);
        break;
    case 'edit':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $videoId = $_GET['id'] ?? null;
        if (!$videoId) {
            $_SESSION['error'] = 'Video ID is required';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $videoController->edit($videoId);
        break;

    case 'incrementViewCount':
        $videoId = $_GET['video_id'] ?? null;
        if ($videoId) {
            $videoController->incrementViewCount($videoId);
        }
        break;
    default:
        $videoController->index();
        break;
}
