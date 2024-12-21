<?php
// views/videos/upload.php
?>
<div class="main-content">
    <div class="upload-container">
        <h2>Upload Video</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form action="/upload" method="POST" enctype="multipart/form-data">
            <div class="upload-area">
                <input type="file" name="video" id="video" accept="video/*" required>
                <div class="upload-prompt">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Click to upload or drag and drop</p>
                    <p class="small">MP4 or WebM</p>
                    <p class="small">720x1280 resolution or higher</p>
                    <p class="small">Up to 100 MB</p>
                </div>
            </div>
            
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="3"></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Post</button>
        </form>
    </div>
</div>