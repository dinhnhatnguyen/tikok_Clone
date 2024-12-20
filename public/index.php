<?php


define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/app/config/Database.php';
require_once BASE_PATH . '/app/config/S3Config.php';
require_once BASE_PATH . '/app/models/Video.php';
require_once BASE_PATH . '/app/controllers/VideoController.php';

$database = new Database();
$db = $database->getConnection();

$s3Config = new S3Config();
$s3Client = $s3Config->getS3Client();

$controller = new VideoController($db, $s3Client);

$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$controller->$action();