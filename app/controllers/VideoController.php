<?php

// Bao gồm tệp Video.php để sử dụng lớp Video
require_once __DIR__ . '/../models/Video.php'; 
require_once __DIR__ . '/../models/Video.php'; 
require_once __DIR__ . '/../config/S3helper.php'; 


class VideoController {
    private $video;
    private $s3Client;
    private $s3Helper;
    
    public function __construct($db, $s3) {
        $this->video = new Video($db);
        $this->s3Client = $s3;
        $this->s3Helper = new S3Helper($s3);
    }
    
    public function index() {
        $videos = $this->video->getAllWithUserInfo();
        $user = 
        require_once __DIR__ . '/../views/videos/index.php';
    }
    
    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateUpload($_FILES['video'] ?? null);
                
                // Generate thumbnail
                $thumbnailPath = $this->generateThumbnail($_FILES['video']['tmp_name']);
                
                // Upload video
                $videoUrl = $this->s3Helper->fileUpload('videos/', $_FILES['video']);
                
                // Upload thumbnail
                $thumbnailUrl = $this->s3Helper->fileUpload('thumbnails/', ['name' => basename($thumbnailPath), 'tmp_name' => $thumbnailPath]);
                
                // Save to database with user info
                $this->video->title = $_POST['title'] ?? $_FILES['video']['name'];
                $this->video->description = $_POST['description'] ?? '';
                $this->video->user_id = $_SESSION['user_id']; // Assuming session exists
                $this->video->filename = $_FILES['video']['name'];
                $this->video->s3_url = $videoUrl;
                $this->video->thumbnail_url = $thumbnailUrl;
                
                if ($this->video->create()) {
                    header("Location: index.php?action=index");
                    exit;
                }
            } catch (Exception $e) {
                return ['error' => $e->getMessage()];
            }
        }
        require_once __DIR__ . '/../views/videos/upload.php';
    }

    private function validateUpload($file) {
        if (!$file) {
            throw new Exception('No file was uploaded.');
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Upload error code: ' . $file['error']);
        }
        
        // Validate file size (e.g., 100MB limit)
        if ($file['size'] > 100 * 1024 * 1024) {
            throw new Exception('File size exceeds limit.');
        }
        
        // Validate file type
        $allowedTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type.');
        }
    }
    
    private function generateThumbnail($videoPath) {
        // Using FFmpeg to generate thumbnail
        $thumbnailPath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
        $command = "ffmpeg -i {$videoPath} -ss 00:00:01 -vframes 1 {$thumbnailPath}";
        exec($command);
        return $thumbnailPath;
    }
    
    public function feed() {
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $videos = $this->video->getFeed($page, $limit);
        header('Content-Type: application/json');
        echo json_encode($videos);
    }
}
// class VideoController {
//     private $video;
//     private $s3Client;
//     private $s3Helper;
    
//     public function __construct($db, $s3) {
//         $this->video = new Video($db);
//         $this->s3Client = $s3;
//         $this->s3Helper = new S3Helper($s3);
//     }
    
//     public function index() {
//         $videos = $this->video->getAll();
//         require_once __DIR__ . '/../views/videos/index.php';
//     }
    
//     public function upload() {
//         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//             try {
//                 $this->validateUpload($_FILES['video'] ?? null);
                
//                 // Generate thumbnail
//                 $thumbnailPath = $this->generateThumbnail($_FILES['video']['tmp_name']);
                
//                 // Upload video
//                 $videoUrl = $this->s3Helper->fileUpload(
//                     'videos/',
//                     $_FILES['video']
//                 );
                
//                 // Upload thumbnail
//                 $thumbnailUrl = $this->s3Helper->fileUpload(
//                     'thumbnails/',
//                     ['name' => basename($thumbnailPath), 'tmp_name' => $thumbnailPath]
//                 );
                
//                 // Save to database with user info
//                 $this->video->title = $_POST['title'] ?? $_FILES['video']['name'];
//                 $this->video->description = $_POST['description'] ?? '';
//                 $this->video->user_id = $_SESSION['user_id']; // Assuming session exists
//                 $this->video->filename = $_FILES['video']['name'];
//                 $this->video->s3_url = $videoUrl;
//                 $this->video->thumbnail_url = $thumbnailUrl;
                
//                 if ($this->video->create()) {
//                     header("Location: index.php?action=index");
//                     exit;
//                 }
//             } catch (Exception $e) {
//                 return ['error' => $e->getMessage()];
//             }
//         }
//         require_once __DIR__ . '/../views/videos/upload.php';
//     }


//     private function validateUpload($file) {
//         if (!$file) {
//             throw new Exception('No file was uploaded.');
//         }
        
//         if ($file['error'] !== UPLOAD_ERR_OK) {
//             throw new Exception('Upload error code: ' . $file['error']);
//         }
        
//         // Validate file size (e.g., 100MB limit)
//         if ($file['size'] > 100 * 1024 * 1024) {
//             throw new Exception('File size exceeds limit.');
//         }
        
//         // Validate file type
//         $allowedTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo'];
//         if (!in_array($file['type'], $allowedTypes)) {
//             throw new Exception('Invalid file type.');
//         }
//     }
    
//     private function generateThumbnail($videoPath) {
//         // Using FFmpeg to generate thumbnail
//         $thumbnailPath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
//         $command = "ffmpeg -i {$videoPath} -ss 00:00:01 -vframes 1 {$thumbnailPath}";
//         exec($command);
//         return $thumbnailPath;
//     }
    
//     public function feed() {
//         $page = $_GET['page'] ?? 1;
//         $limit = 10;
//         $videos = $this->video->getFeed($page, $limit);
//         header('Content-Type: application/json');
//         echo json_encode($videos);
//     }
// }