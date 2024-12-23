<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Video</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            min-height: 100vh;
        }

        .card {
            background-color: var(--dark-surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--border);
            padding: 1.5rem;
        }

        .card-header h2 {
            margin: 0;
            font-weight: 600;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control,
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 2px var(--primary);
        }

        .form-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #e62a4d;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            color: var(--text);
        }

        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .video-preview {
            border-radius: 12px;
            overflow: hidden;
            margin: 1rem 0;
            background-color: rgba(0, 0, 0, 0.2);
        }

        .video-preview video {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
        }

        .alert {
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--text-secondary);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .text-muted {
            color: var(--text-secondary) !important;
            font-size: 0.875rem;
        }

        .custom-file-upload {
            display: inline-block;
            padding: 0.75rem 1rem;
            cursor: pointer;
            background-color: rgba(254, 44, 85, 0.1);
            /* Lighter shade of #fe2c55 */
            border-radius: 8px;
            border: 1px solid rgba(254, 44, 85, 0.3);
            /* Semi-transparent border with primary color */
            width: 100%;
            transition: all 0.3s ease;
        }

        .custom-file-upload:hover {
            background-color: rgba(254, 44, 85, 0.15);
            border-color: rgba(254, 44, 85, 0.5);
        }

        .form-control[type="file"] {
            color: #fe2c55;
            /* Primary color for text */
        }

        .form-control[type="file"]::file-selector-button {
            background-color: #fe2c55;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 1rem;
            transition: all 0.3s ease;
        }

        .form-control[type="file"]::file-selector-button:hover {
            background-color: #e62a4d;
            /* Slightly darker shade for hover */
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-edit me-2"></i>Edit Video</h2>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= htmlspecialchars($_SESSION['error']) ?>
                                <?php unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <form action="index.php?action=edit&id=<?= $video['id'] ?>" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading me-2"></i>Title
                                </label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="<?= htmlspecialchars($video['title']) ?>" required
                                    placeholder="Give your video a title">
                            </div>

                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-2"></i>Description
                                </label>
                                <textarea class="form-control" id="description" name="description"
                                    rows="4" placeholder="Tell viewers about your video"><?= htmlspecialchars($video['description']) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-play-circle me-2"></i>Current Video
                                </label>
                                <div class="video-preview">
                                    <video controls class="w-100">
                                        <source src="<?= htmlspecialchars($video['s3_url']) ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="video" class="form-label">
                                    <i class="fas fa-upload me-2"></i>Replace Video (optional)
                                </label>
                                <div class="custom-file-upload">
                                    <input type="file" class="form-control" id="video" name="video" accept="video/*">
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Leave empty to keep the current video
                                </small>
                            </div>

                            <div class="d-grid gap-3 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Video
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php?action=profile'">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('video').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const videoPreview = document.querySelector('.video-preview video');
                videoPreview.src = URL.createObjectURL(this.files[0]);
                videoPreview.load();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'none';

            document.querySelector('form').addEventListener('submit', function() {
                overlay.style.display = 'flex';
            });
        });
    </script>
</body>

</html>