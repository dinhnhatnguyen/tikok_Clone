<?php
// views/videos/upload.php
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        :root {
            --primary: #fe2c55;
            --dark-bg: #121212;
            --dark-surface: #1e1e1e;
            --text: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --border: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .main-content {
            width: 100%;
            max-width: 800px;
            padding: 2rem;
        }

        .upload-container {
            background-color: var(--dark-surface);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: var(--text);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 24px;
        }

        .alert {
            background-color: rgba(254, 44, 85, 0.1);
            border: 1px solid var(--primary);
            color: var(--primary);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .upload-area {
            position: relative;
            border: 2px dashed var(--border);
            border-radius: 12px;
            padding: 3rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }

        .upload-area:hover {
            border-color: var(--primary);
            background-color: rgba(254, 44, 85, 0.05);
        }

        .upload-area input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }

        .upload-prompt {
            pointer-events: none;
        }

        .upload-prompt i {
            font-size: 48px;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .upload-prompt p {
            margin: 0.5rem 0;
            color: var(--text);
        }

        .upload-prompt .small {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text);
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(254, 44, 85, 0.2);
        }

        .submit-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #e62a4d;
            transform: translateY(-1px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        #preview-container {
            display: none;
            margin-top: 1.5rem;
        }

        #video-preview {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 1rem;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--border);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
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
                        <p class="small">Up to 100 MB</p>
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