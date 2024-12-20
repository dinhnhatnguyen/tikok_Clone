<?php
class VideoController {
    private $video;
    private $s3Client;
    
    public function __construct($db, $s3) {
        $this->video = new Video($db);
        $this->s3Client = $s3;
    }
    
    public function index() {
        $videos = $this->video->getAll();
        require_once __DIR__ . '/../views/videos/index.php';
    }
    
    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!isset($_FILES['video'])) {
                    throw new Exception('No file was uploaded.');
                }
    
                $file = $_FILES['video'];
    
                // Kiểm tra xem tệp có được chọn hay không
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('Upload error code: ' . $file['error']);
                }
    
                // Kiểm tra xem tệp có được chọn hay không
                if (empty($file['tmp_name'])) {
                    throw new Exception('No file was uploaded or the file is empty.');
                }
    
                $bucket = $_ENV['S3_BUCKET'];
                
                // Upload to S3
                $result = $this->s3Client->putObject([
                    'Bucket' => $bucket,
                    'Key'    => 'videos/' . uniqid() . '_' . $file['name'],
                    'SourceFile' => $file['tmp_name'],
                    'ACL'    => 'public-read'
                ]);
                
                // Save to database
                $this->video->title = $file['name'];
                $this->video->s3_url = $result['ObjectURL'];
                
                if ($this->video->create()) {
                    header("Location: index.php?action=index");
                }
            } catch (Exception $e) {
                echo "Upload error: " . $e->getMessage();
            }
        }
        require_once __DIR__ . '/../views/videos/upload.php';
    }
}