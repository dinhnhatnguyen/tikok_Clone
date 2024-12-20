<?php
class Video {
    private $conn;
    private $table_name = "videos";
    
    public $id;
    public $title;
    public $s3_url;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET title=:title, s3_url=:s3_url, created_at=:created_at";
        
        $stmt = $this->conn->prepare($query);
        
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->s3_url = htmlspecialchars(strip_tags($this->s3_url));
        $this->created_at = date('Y-m-d H:i:s');
        
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":s3_url", $this->s3_url);
        $stmt->bindParam(":created_at", $this->created_at);
        
        return $stmt->execute();
    }
}