<?php

require_once __DIR__ . '/../models/Video.php';
require_once __DIR__ . '/../models/Video.php';
require_once __DIR__ . '/../config/S3helper.php';
require_once __DIR__ . '/../models/User.php';



class VideoController
{
    private $video;
    private $s3Client;
    private $s3Helper;
    private $user;
    private $db;

    public function __construct($db, $s3)
    {
        $this->video = new Video($db);
        $this->s3Client = $s3;
        $this->s3Helper = new S3Helper($s3);
        $this->user = new User($db);
        $this->db;
    }

    public function index()
    {
        $videos = $this->video->getAllWithUserInfo();
        $followings = [];
        if (isset($_SESSION['user_id'])) {
            $followings = $this->user->getFollowing($_SESSION['user_id']);
        }

        require_once __DIR__ . '/../views/videos/index.php';
    }


    public function upload()
    {
        // Nếu là GET request, hiển thị form upload
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require_once __DIR__ . '/../views/videos/upload.php';
            return;
        }

        // Xử lý POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!isset($_SESSION['user_id'])) {
                    throw new Exception('Please login to upload videos');
                }

                $this->validateUpload($_FILES['video'] ?? null);

                // Upload video lên S3 
                $videoUrl = $this->s3Helper->fileUpload('videos/', $_FILES['video']);

                // Generate và upload thumbnail
                $thumbnailPath = $this->generateThumbnail($_FILES['video']['tmp_name']);

                try {
                    $thumbnailUrl = $this->s3Helper->fileUpload('thumbnails/', [
                        'name' => basename($thumbnailPath),
                        'tmp_name' => $thumbnailPath
                    ]);
                } finally {
                    // Luôn cleanup file thumbnail dù upload thành công hay thất bại
                    if (file_exists($thumbnailPath)) {
                        unlink($thumbnailPath);
                    }
                }

                // Lưu thông tin vào database
                $this->video->title = $_POST['title'] ?? $_FILES['video']['name'];
                $this->video->description = $_POST['description'] ?? '';
                $this->video->user_id = $_SESSION['user_id'];
                $this->video->filename = $_FILES['video']['name'];
                $this->video->s3_url = $videoUrl;
                $this->video->thumbnail_url = $thumbnailUrl;

                if (!$this->video->create()) {
                    throw new Exception('Failed to save video information');
                }

                // Xóa file thumbnail tạm
                if (file_exists($thumbnailPath)) {
                    unlink($thumbnailPath);
                }

                // Redirect về trang chủ với thông báo thành công
                header('Location: index.php?controller=video&action=index&success=1');
                exit;
            } catch (Exception $e) {
                // Redirect lại trang upload với thông báo lỗi
                header('Location: index.php?controller=video&action=upload&error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }
    private function validateUpload($file)
    {
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
    private function generateThumbnail($videoPath)
    {
        // Tạo thư mục temp nếu chưa có
        $tempDir = __DIR__ . '/../storage/temp';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $thumbnailPath = $tempDir . '/' . uniqid() . '.jpg';

        // Escape shell arguments để tránh command injection
        $videoPath = escapeshellarg($videoPath);
        $thumbnailPath = escapeshellarg($thumbnailPath);

        // Generate thumbnail using ffmpeg
        $command = "ffmpeg -i {$videoPath} -ss 00:00:01 -vframes 1 {$thumbnailPath} 2>&1";

        exec($command, $output, $returnCode);

        // Check if thumbnail was generated successfully
        if ($returnCode !== 0 || !file_exists(trim($thumbnailPath, "'"))) {
            throw new Exception('Failed to generate thumbnail: ' . implode("\n", $output));
        }

        return trim($thumbnailPath, "'");
    }

    public function feed()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $videos = $this->video->getFeed($page, $limit);
        header('Content-Type: application/json');
        echo json_encode($videos);
    }


    public function delete($videoId)
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login to delete videos';
            header('Location: index.php?action=login');
            exit;
        }

        try {
            // Delete the video
            $result = $this->video->delete($videoId, $_SESSION['user_id']);

            if ($result) {
                // Delete from S3 if needed
                // if (isset($video['s3_url'])) {
                //     $this->s3Helper->deleteFile($video['s3_url']);
                // }
                // if (isset($video['thumbnail_url'])) {
                //     $this->s3Helper->deleteFile($video['thumbnail_url']);
                // }

                $_SESSION['success'] = 'Video deleted successfully';
            } else {
                $_SESSION['error'] = 'Failed to delete video';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        // Redirect back to profile
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }


    public function edit($videoId)
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login to edit videos';
            header('Location: index.php?action=login');
            exit;
        }

        $video = $this->video->getById($videoId);

        if (!$video || $video['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'You do not have permission to edit this video';
            header('Location: index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $updateData = [
                    'title' => $_POST['title'] ?? $video['title'],
                    'description' => $_POST['description'] ?? $video['description']
                ];

                // Handle new video file upload if provided
                if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
                    $this->validateUpload($_FILES['video']);

                    // Upload new video and get URL
                    $newVideoUrl = $this->s3Helper->fileUpload('videos/', $_FILES['video']);
                    $updateData['s3_url'] = $newVideoUrl;

                    // Generate and upload new thumbnail
                    $thumbnailPath = $this->generateThumbnail($_FILES['video']['tmp_name']);
                    try {
                        $newThumbnailUrl = $this->s3Helper->fileUpload('thumbnails/', [
                            'name' => basename($thumbnailPath),
                            'tmp_name' => $thumbnailPath
                        ]);
                        $updateData['thumbnail_url'] = $newThumbnailUrl;
                    } finally {
                        if (file_exists($thumbnailPath)) {
                            unlink($thumbnailPath);
                        }
                    }

                    // Delete old files from S3
                    if (isset($video['s3_url'])) {
                        $this->s3Helper->deleteFile($video['s3_url']);
                    }
                    if (isset($video['thumbnail_url'])) {
                        $this->s3Helper->deleteFile($video['thumbnail_url']);
                    }
                }

                if ($this->video->update($videoId, $_SESSION['user_id'], $updateData)) {
                    $_SESSION['success'] = 'Video updated successfully';
                    header('Location: index.php?action=profile');
                    exit;
                } else {
                    throw new Exception('Failed to update video');
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }

        // Show edit form
        require_once __DIR__ . '/../views/videos/edit.php';
    }


    public function incrementViewCount($videoId)
    {
        if ($videoId) {
            $this->video->incrementViewCount($videoId);
        }
    }
}
