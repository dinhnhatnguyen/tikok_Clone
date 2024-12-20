<!DOCTYPE html>
<html>
<head>
    <title>Video List</title>
</head>
<body>
    <h1>Videos</h1>
    <a href="index.php?action=upload">Upload New Video</a>
    
    <div class="videos">
        <?php while($row = $videos->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="video">
                <h3><?php echo $row['title']; ?></h3>
                <video width="320" height="240" controls>
                    <source src="<?php echo $row['s3_url']; ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <p>Uploaded: <?php echo $row['created_at']; ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>