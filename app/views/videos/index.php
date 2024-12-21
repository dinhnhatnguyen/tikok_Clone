<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../controllers/VideoController.php';
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../config/S3helper.php';


// Initialize Database and controllers
$database = new Database();
$db = $database->getConnection();



// Handle follow action
if (isset($_GET['action']) && $_GET['action'] === 'follow' && isset($_GET['userId'])) {
    $userController->follow($_GET['userId']);
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TikTok Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/public/css/video.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="/public/assets/logo.png" alt="TikTok" height="42">
            </a>
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Search accounts and videos">
            </div>
            <div class="d-flex align-items-center gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="btn btn-outline-light" href="index.php?action=profile" style="text-decoration: none;">
                        <i class="fas fa-user me-2"></i>Profile
                    </a>
                    <a class="btn btn-outline-light" href="index.php?action=logout" style="text-decoration: none;">
                        <i class="fa-solid fa-right-from-bracket"></i>Logout
                    </a>
                <?php else: ?>
                    <a class="btn btn-danger" href="index.php?action=login" style="text-decoration: none;">Log in</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main container -->
    <div class="container-fluid" style="margin-top: 60px">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="nav flex-column">
                <!-- Main Navigation Items -->
                <div class="main-nav-items">
                    <a class="nav-link active" href="#">
                        <i class="fas fa-home"></i>
                        For You
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fa-regular fa-compass"></i>
                        Explore
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fa-solid fa-user-group"></i>
                        Following
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-video"></i>
                        LIVE
                    </a>
                </div>

                <div class="nav-divider"></div>

                <!-- Secondary Navigation Items -->
                <div class="secondary-nav-items">
                    <a class="nav-link" href="#">
                        <i class="fas fa-fire"></i>
                        Trending
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-music"></i>
                        Music
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-gamepad"></i>
                        Gaming
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="nav-link" href="index.php?action=upload">
                            <i class="fa-solid fa-square-plus"></i>
                            Upload video
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Login Section -->


                <?php if (isset($_SESSION['user_id']) && isset($followings) && is_array($followings)): ?>
                    <?php if (empty($followings)): ?>
                        <div class="sidebar-login">
                            <p>You haven't followed anyone yet, start following now!</p>
                        </div>
                    <?php endif; ?>
                    <div class="sidebar-login" style="margin: 20px 0">
                        <?php foreach ($followings as $following): ?>
                            <div class="comment-header">
                                <img src="<?php echo htmlspecialchars($following['avatar_url']); ?>" class="comment-avatar" alt="User">
                                <div>
                                    <div class="comment-username"><?php echo htmlspecialchars($following['full_name']); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="sidebar-login">
                        <p>Log in to follow creators, like videos, and view comments.</p>
                        <a class="btn-login" href="index.php?action=login" style="text-decoration: none;">Log in</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main content -->
        <div class="main-content">
            <?php foreach ($videos as $video): ?>
                <div class="video-container">
                    <video
                        class="video-player"
                        src="<?php echo htmlspecialchars($video['s3_url']); ?>"
                        poster="<?php echo htmlspecialchars($video['thumbnail_url']); ?>"
                        loop
                        playsinline></video>

                    <!-- Action buttons -->
                    <div class="action-buttons">
                        <div class="action-button">
                            <div class="action-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <span class="action-count">45.4K</span>
                        </div>
                        <div class="action-button" data-bs-toggle="modal" data-bs-target="#commentsModal">
                            <div class="action-icon">
                                <i class="fas fa-comment"></i>
                            </div>
                            <span class="action-count">327</span>
                        </div>
                        <div class="action-button">
                            <div class="action-icon">
                                <i class="fas fa-bookmark"></i>
                            </div>
                            <span class="action-count">5530</span>
                        </div>
                        <div class="action-button">
                            <div class="action-icon">
                                <i class="fas fa-share"></i>
                            </div>
                            <span class="action-count">Share</span>
                        </div>
                    </div>


                    <!-- Video info -->
                    <div class="video-info">
                        <div class="user-info">
                            <img class="user-avatar" src="<?php echo htmlspecialchars($video['avatar_url']); ?>" alt="<?php echo htmlspecialchars($video['username']); ?>">
                            <span class="fw-bold"><?php echo htmlspecialchars($video['username']); ?></span>

                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $video['user_id']): ?>
                                <?php
                                $isFollowing = (new User($db))->isFollowing($_SESSION['user_id'], $video['user_id']);
                                if (!$isFollowing):
                                ?>
                                    <button class="follow-button"
                                        data-user-id="<?php echo $video['user_id']; ?>"
                                        onclick="followUser(<?php echo $video['user_id']; ?>)">
                                        Follow
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>


                        </div>
                        <h4>
                            <?php echo htmlspecialchars($video['title']); ?>
                        </h4>
                        <p class="video-description mb-0">
                            <?php echo htmlspecialchars($video['description']); ?>
                        </p>

                    </div>
                <?php endforeach; ?>
                </div>
        </div>

        <!-- Comments Modal -->
        <div class="modal fade" id="commentsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">327 comments</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Sample comments -->
                        <div class="comment-item">
                            <div class="comment-header">
                                <img src="/api/placeholder/32/32" class="comment-avatar" alt="User">
                                <div>
                                    <div class="comment-username">user123</div>
                                    <div class="comment-time">2d ago</div>
                                </div>
                            </div>
                            <div class="comment-content">
                                This is amazing! 🔥
                            </div>
                        </div>
                        <!-- More comments can be added here -->
                    </div>
                    <div class="modal-footer">
                        <div class="comment-input-container">
                            <input type="text" class="comment-input" placeholder="Add comment...">
                            <button class="post-comment-btn">Post</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle video playback
                const videos = document.querySelectorAll('.video-player');
                videos.forEach(video => {
                    video.addEventListener('click', function() {
                        if (video.paused) {
                            videos.forEach(v => v.pause());
                            video.play();
                        } else {
                            video.pause();
                        }
                    });
                });

                // Handle likes
                const likeButtons = document.querySelectorAll('.like-button');
                likeButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        button.classList.toggle('text-danger');
                    });
                });

                // Infinite scroll
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.querySelector('video').play();
                        } else {
                            entry.target.querySelector('video').pause();
                        }
                    });
                }, {
                    threshold: 0.5
                });

                document.querySelectorAll('.video-container').forEach(container => {
                    observer.observe(container);
                });


                const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;


                async function followUser(userId) {
                    if (!isLoggedIn) {
                        window.location.href = 'index.php?action=login';
                        return;
                    }

                    try {
                        const response = await fetch(`index.php?action=follow&userId=${userId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const data = await response.json();

                        if (data.error) {
                            throw new Error(data.error);
                        }

                        // Sau khi follow thành công, reload để cập nhật UI
                        window.location.reload();

                    } catch (error) {
                        console.error('Error:', error);
                        alert('Failed to update follow status. Please try again.');
                    }
                }
            });
        </script>
</body>

</html>