<?php
require_once __DIR__ . '/../../vendor/autoload.php'; 

use Aws\S3\S3Client;
use Dotenv\Dotenv;

class S3Config {
    private $s3Client;
    
    public function __construct() {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => 'ap-southeast-1',
            'credentials' => [
                'key'    => $_ENV['S3_KEY'],
                'secret' => $_ENV['S3_SECRET'],
            ]
        ]);
    }
    
    public function getS3Client() {
        return $this->s3Client;
    }
}