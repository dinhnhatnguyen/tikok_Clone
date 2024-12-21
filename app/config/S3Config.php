<?php

require_once __DIR__ . '/../../vendor/autoload.php'; 

use Aws\S3\S3Client;
use Dotenv\Dotenv;

class S3Config {
    private $s3Client;
    
    public function __construct() {
        // Tải file .env
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Đường dẫn đến thư mục chứa file .env
        $dotenv->load();

        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $_ENV['S3_REGION'], // Ví dụ: 'ap-southeast-1'
            'credentials' => [
                'key'    => $_ENV['S3_KEY'],    // Đọc từ .env
                'secret' => $_ENV['S3_SECRET'], // Đọc từ .env
            ]
        ]);
    }
    
    public function getS3Client() {
        return $this->s3Client;
    }
}
