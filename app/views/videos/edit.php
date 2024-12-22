<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Video</title>
    <link rel="stylesheet" href="/public/css/video/edit.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Edit Video</h2>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?action=edit&id=<?= $video['id'] ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title"
                            value="<?= htmlspecialchars($video['title']) ?>" required
                            placeholder="Give your video a title">
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"
                            rows="3" placeholder="Tell viewers about your video"><?= htmlspecialchars($video['description']) ?></textarea>
                    </div>


                    <div class="form-group">
                        <label class="form-label">Current Video</label>
                        <div class="video-preview">
                            <video controls>
                                <source src="<?= htmlspecialchars($video['s3_url']) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                    <div class="overlay" id="loadingOverlay">
                        <div class="spinner" id="loadingSpinner"></div>
                    </div>

                    <div class="form-group">
                        <label for="video" class="form-label">Replace Video (optional)</label>
                        <input type="file" class="form-control" id="video" name="video" accept="video/*">
                        <span class="text-muted">Leave empty to keep the current video</span>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Video</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php?action=profile'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
            overlay.style.display = 'none'; // Đảm bảo overlay ẩn khi tải trang

            document.querySelector('form').addEventListener('submit', function() {
                overlay.style.display = 'flex'; // Hiển thị overlay khi submit
            });
        });
    </script>
</body>

</html>