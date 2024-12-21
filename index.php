<?php
session_start();
require_once __DIR__ . '/app/config/Database.php';
require_once __DIR__ . '/app/config/S3Config.php';
require_once __DIR__ . '/app/controllers/VideoController.php';
require_once __DIR__ . '/app/controllers/UserController.php';

$db = (new Database())->getConnection();
$s3Config = new S3Config();
$s3Client = $s3Config->getS3Client();

// Khởi tạo controller
$videoController = new VideoController($db, $s3Client);
$userController = new UserController($db, $s3Client);

// Xác định hành động từ URL
$action = $_GET['action'] ?? 'index';

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
    case 'profile':
        $userController->profile($_SESSION['username'] ?? null);
        break;
    default:
        $videoController->index();
        break;
}