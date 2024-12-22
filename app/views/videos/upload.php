<?php
// views/videos/upload.php
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="/public/css/video/upload.css">
</head>

<body>
    <div class="main-content">
        <div class="upload-container">
            <h2>Upload Video</h2>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <form action="?controller=video&action=upload" method="POST" enctype="multipart/form-data" id="upload-form">
                <div class="upload-area" id="upload-area">
                    <input type="file" name="video" id="video" accept="video/*" required>
                    <div class="upload-prompt">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload or drag and drop</p>
                        <p class="small">MP4 or WebM</p>
                        <p class="small">720x1280 resolution or higher</p>
                        <p class="small">Up to 500 MB</p>
                    </div>
                </div>

                <div id="preview-container">
                    <video id="video-preview" controls></video>
                </div>

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" required placeholder="Give your video a title">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="3" placeholder="Tell viewers about your video"></textarea>
                </div>

                <button type="submit" class="submit-btn" id="submit-btn">Post Video</button>
                <button type="button" class="submit-btn" onclick="window.location.href='/'" style="margin-top: 20px;">Cancel</button>
                <div class="loading" id="loading">
                    <div class="loading-spinner"></div>
                    <p>Uploading video...</p>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('video').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const preview = document.getElementById('video-preview');
                const previewContainer = document.getElementById('preview-container');

                preview.src = URL.createObjectURL(file);
                previewContainer.style.display = 'block';

                // Update upload area text
                const uploadPrompt = document.querySelector('.upload-prompt p');
                uploadPrompt.textContent = file.name;
            }
        });

        // Form submit handling
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submit-btn');
            const loading = document.getElementById('loading');

            submitBtn.style.display = 'none';
            loading.style.display = 'block';
        });

        // Drag and drop functionality
        const uploadArea = document.getElementById('upload-area');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            uploadArea.classList.add('highlight');
        }

        function unhighlight(e) {
            uploadArea.classList.remove('highlight');
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const file = dt.files[0];

            const fileInput = document.getElementById('video');
            fileInput.files = dt.files;

            // Trigger change event
            const event = new Event('change');
            fileInput.dispatchEvent(event);
        }
    </script>
</body>

</html>