<?php
require_once __DIR__ . '/../../vendor/autoload.php'; 
    class S3Helper {
        private $s3Client;

        function __construct($s3Client) {
            $this-> s3Client = $s3Client;
        }


        function fileUpload($folder, $file) {
            $bucket = $_ENV['S3_BUCKET'];
        
            $result = $this->s3Client->putObject([
                'Bucket' => $bucket,
                'Key'    => $folder . $file['name'],  
                'SourceFile' => $file['tmp_name'],
                'ACL'    => 'public-read'
            ]);
        
            return $result['ObjectURL'];
        }
        
    }