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
                    <a class="nav-link" href="index.php?action=upload">
                        <i class="fa-solid fa-square-plus"></i>
                        Upload video
                    </a>
                </div>

                <!-- Following Section / Login Section -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="following-section mt-4">
                        <h6 class="px-3 text-muted">Following Accounts</h6>
                        <div class="following-list">
                            <?php
                            $user = new User($db);
                            $following = $user->getFollowing($_SESSION['user_id']);
                            foreach ($following as $followedUser):
                            ?>
                            <a href="index.php?action=profile&username=<?php echo htmlspecialchars($followedUser['username']); ?>" 
                               class="nav-link d-flex align-items-center px-3 py-2">
                                <img src="<?php echo htmlspecialchars($followedUser['avatar_url'] ?? '/public/assets/default-avatar.png'); ?>" 
                                     alt="<?php echo htmlspecialchars($followedUser['username']); ?>"
                                     class="rounded-circle me-2"
                                     style="width: 32px; height: 32px; object-fit: cover;">
                                <span><?php echo htmlspecialchars($followedUser['username']); ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
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
                    playsinline
                ></video>

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
                            <button class="follow-button" onclick="followUser(<?php echo $video['user_id']; ?>)">Follow</button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <p class="video-description mb-0">
                        <?php echo htmlspecialchars($video['description']); ?>
                    </p>
                </div>
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
            }, { threshold: 0.5 });

            document.querySelectorAll('.video-container').forEach(container => {
                observer.observe(container);
            });
        });

        // Follow user function
        function followUser(userId) {
            fetch(`index.php?action=follow&id=${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide the follow button after successful follow
                    event.target.style.display = 'none';
                    // Optionally reload the following list
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
            
        }
    </script>
</body>
</html>













<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #fe2c55;
            --dark-bg: #121212;
            --dark-surface: #1a1a1a;
            --dark-surface-2: #232323;
            --text-primary: #ffffff;
            --text-secondary: #989898;
            --hover-bg: rgba(254, 44, 85, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: var(--dark-bg);
        }

        .profile-header {
            display: flex;
            align-items: flex-start;
            gap: 40px;
            padding: 30px;
            background: var(--dark-surface);
            border-radius: 16px;
            margin-bottom: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary);
            box-shadow: 0 4px 12px rgba(254, 44, 85, 0.3);
        }

        .profile-info {
            flex: 1;
        }

        .profile-info h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 8px;
            color: var(--text-primary);
        }

        .profile-info h2 {
            font-size: 18px;
            color: var(--text-secondary);
            margin: 0 0 20px;
        }

        .button-group {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .edit-profile-btn, .follow-btn, .share-profile-btn {
            padding: 12px 32px;
            border-radius: 24px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .edit-profile-btn {
            background-color: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .edit-profile-btn:hover {
            background-color: var(--hover-bg);
        }

        .follow-btn {
            background-color: var(--primary);
            color: white;
        }

        .follow-btn.following {
            background-color: transparent;
            border: 2px solid var(--text-secondary);
            color: var(--text-secondary);
        }

        .share-profile-btn {
            background-color: transparent;
            border: 2px solid var(--text-secondary);
            color: var(--text-secondary);
            padding: 12px;
            border-radius: 50%;
        }

        .share-profile-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .profile-stats {
            display: flex;
            gap: 40px;
            margin: 30px 0;
            padding: 20px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat {
            text-align: center;
        }

        .stat .count {
            display: block;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .stat .label {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .profile-bio {
            color: var(--text-secondary);
            line-height: 1.6;
            margin: 20px 0;
        }

        .profile-tabs {
            display: flex;
            gap: 8px;
            padding: 0 20px;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .tab-btn {
            padding: 16px 32px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 16px;
            color: var(--text-secondary);
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .tab-btn:hover {
            color: var(--text-primary);
        }

        .tab-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab-btn i {
            margin-right: 8px;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 24px;
            padding: 20px;
        }

        .video-item {
            background: var(--dark-surface-2);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .video-item:hover {
            transform: translateY(-4px);
        }

        .video-thumbnail {
            position: relative;
            padding-top: 177.77%;
            background: var(--dark-surface);
            overflow: hidden;
        }

        .video-thumbnail img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-duration {
            position: absolute;
            bottom: 8px;
            right: 8px;
            padding: 4px 8px;
            background: rgba(0, 0, 0, 0.75);
            color: white;
            border-radius: 4px;
            font-size: 12px;
        }

        .video-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 16px;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .video-item:hover .video-overlay {
            opacity: 1;
        }

        .video-stats {
            display: flex;
            gap: 16px;
            color: white;
            font-size: 14px;
        }

        .video-stats i {
            color: var(--primary);
        }

        .video-info {
            padding: 16px;
        }

        .video-info h3 {
            margin: 0 0 8px;
            font-size: 16px;
            color: var(--text-primary);
        }

        .video-info p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 48px;
            color: var(--primary);
            margin-bottom: 16px;
        }

        .empty-state p {
            margin: 8px 0 24px;
        }

        .upload-btn {
            display: inline-block;
            padding: 12px 32px;
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 24px;
            font-weight: 600;
            transition: opacity 0.2s ease;
        }

        .upload-btn:hover {
            opacity: 0.9;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background: var(--dark-surface);
            max-width: 500px;
            margin: 100px auto;
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .modal-content h3 {
            margin: 0 0 24px;
            font-size: 24px;
        }

        .share-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin: 24px 0;
        }

        .share-btn {
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            background: var(--dark-surface-2);
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .share-btn:hover {
            background: var(--hover-bg);
            border-color: var(--primary);
            color: var(--primary);
        }

        .share-btn i {
            margin-right: 8px;
        }

        .copy-link {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .copy-link input {
            flex: 1;
            padding: 12px;
            background: var(--dark-surface-2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--text-primary);
        }

        #copyLink {
            padding: 12px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: opacity 0.2s ease;
        }

        #copyLink:hover {
            opacity: 0.9;
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            transition: color 0.2s ease;
        }

        .modal-close:hover {
            color: var(--primary);
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="/api/placeholder/150/150" alt="Profile Avatar" class="profile-avatar">
            <div class="profile-info">
                <h1>@johndoe</h1>
                <h2>John Doe</h2>
                
                <div class="button-group">
                    <button class="edit-profile-btn">Edit Profile</button>
                    <button class="share-profile-btn">
                        <i class="fas fa-share"></i>
                    </button>
                </div>
                
                <div class="profile-stats">
                    <div class="stat">
                        <span class="count">1.2M</span>
                        <span class="label">Followers</span>
                    </div>
                    <div class="stat">
                        <span class="count">824</span>
                        <span class="label">Following</span>
                    </div>
                    <div class="stat">
                        <span class="count">2.1M</span>
                        <span class="label">Likes</span>
                    </div>
                </div>
                
                <p class="profile-bio">🎥 Content Creator | 🎮 Gamer<br>✨ Making awesome videos daily<br>📧 business@johndoe.com</p>
            </div>
        </div>

        <div class="profile-tabs">
            <button class="tab-btn active" data-tab="videos">
                <i class="fas fa-video"></i> Videos
            </button>
            <button class="tab-btn" data-tab="liked">
                <i class="fas fa-heart"></i> Liked
            </button>
            <button class="tab-btn" data-tab="private">
                <i class="fas fa-lock"></i> Private
            </button>
        </div>

        <div class="tab-content active" id="videos-content">
            <div class="video-grid">
                <!-- Video Items -->
                <div class="video-item">
                    <div class="video-thumbnail">
                        <img src="/api/placeholder/250/444" alt="Video Thumbnail">
                        <div class="video-duration">3:24</div>
                        <div class="video-overlay">
                            <div class="video-stats">
                                <span><i class="fas fa-heart"></i> 124.5K</span>
                                <span><i class="fas fa-comment"></i> 1.2K</span>
                            </div>
                        </div>
                    </div>
                    <div class="video-info">
                        <h3>Amazing Gaming Moments</h3>
                        <p>2 days ago</p>
                    </div>
                </div>
                <!-- Repeat video items -->
            </div>
        </div>

        <div class="tab-content hidden" id="liked-content">
            <div class="empty-state">
                <i class="fas fa-heart"></i>
                <p>No liked videos yet</p>
            </div>
        </div>

        <div class="tab-content hidden" id="private-content">
            <div class="empty-state">
                <i class="fas fa-lock"></i>
                <p>No private videos</p>
                <a href="#" class="upload-btn">Upload a video</a>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div class="modal" id="shareModal">
        <div class="modal-content">