<?php
// views/users/profile.php
?>

<div class="profile-container">
    <div class="profile-header">
        <img src="<?= htmlspecialchars($user['avatar_url'] ?? '/assets/default-avatar.png') ?>" 
             alt="<?= htmlspecialchars($user['username']) ?>" 
             class="profile-avatar">
             
        <div class="profile-info">
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <h2><?= htmlspecialchars($user['full_name']) ?></h2>
            
            <?php if ($user['id'] === $_SESSION['user_id']): ?>
                <a href="/profile/edit" class="edit-profile-btn">Edit Profile</a>
            <?php else: ?>
                <button class="follow-btn <?= $isFollowing ? 'following' : '' ?>"
                        data-user-id="<?= $user['id'] ?>">
                    <?= $isFollowing ? 'Following' : 'Follow' ?>
                </button>
            <?php endif; ?>
            
            <div class="profile-stats">
                <div class="stat">
                    <span class="count"><?= number_format($user['followers_count']) ?></span>
                    <span class="label">Followers</span>
                </div>
                <div class="stat">
                    <span class="count"><?= number_format($user['following_count']) ?></span>
                    <span class="label">Following</span>
                </div>
                <div class="stat">
                    <span class="count"><?= number_format($user['videos_count']) ?></span>
                    <span class="label">Videos</span>
                </div>
            </div>
            
            <?php if ($user['bio']): ?>
                <p class="profile-bio"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="profile-content">
        <div class="video-grid">
            <?php foreach ($videos as $video): ?>
                <div class="video-thumbnail">
                    <a href="/video/<?= $video['id'] ?>">
                        <img src="<?= htmlspecialchars($video['thumbnail_url']) ?>" 
                             alt="<?= htmlspecialchars($video['title']) ?>">
                        <div class="video-stats">
                            <span><i class="fas fa-heart"></i> <?= number_format($video['likes_count']) ?></span>
                        </div>
                    </a>
                    </div>
            <?php endforeach; ?>
        </div>
    </div>


    <!-- Tabs for different content types -->
    <div class="profile-tabs">
        <button class="tab-btn active" data-tab="videos">
            <i class="fas fa-video"></i> Videos
        </button>
        <button class="tab-btn" data-tab="liked">
            <i class="fas fa-heart"></i> Liked
        </button>
        <?php if ($user['id'] === $_SESSION['user_id']): ?>
            <button class="tab-btn" data-tab="private">
                <i class="fas fa-lock"></i> Private
            </button>
        <?php endif; ?>
    </div>

    <!-- Tab content containers -->
    <div class="tab-content" id="videos-content">
        <div class="video-grid">
            <?php if (empty($videos)): ?>
                <div class="empty-state">
                    <i class="fas fa-video"></i>
                    <p>No videos yet</p>
                    <?php if ($user['id'] === $_SESSION['user_id']): ?>
                        <a href="/upload" class="upload-btn">Upload a video</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($videos as $video): ?>
                    <div class="video-item">
                        <div class="video-thumbnail" data-video-id="<?= $video['id'] ?>">
                            <img src="<?= htmlspecialchars($video['thumbnail_url']) ?>" 
                                 alt="<?= htmlspecialchars($video['title']) ?>">
                            <div class="video-duration"><?= formatDuration($video['duration']) ?></div>
                            <div class="video-overlay">
                                <div class="video-stats">
                                    <span><i class="fas fa-heart"></i> <?= number_format($video['likes_count']) ?></span>
                                    <span><i class="fas fa-comment"></i> <?= number_format($video['comments_count']) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="video-info">
                            <h3><?= htmlspecialchars($video['title']) ?></h3>
                            <p><?= formatDate($video['created_at']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="tab-content hidden" id="liked-content">
        <div class="video-grid">
            <?php if (empty($likedVideos)): ?>
                <div class="empty-state">
                    <i class="fas fa-heart"></i>
                    <p>No liked videos yet</p>
                </div>
            <?php else: ?>
                <?php foreach ($likedVideos as $video): ?>
                    <!-- Similar structure to videos grid -->
                    <div class="video-item">
                        <!-- Video thumbnail and info -->
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($user['id'] === $_SESSION['user_id']): ?>
        <div class="tab-content hidden" id="private-content">
            <div class="video-grid">
                <?php if (empty($privateVideos)): ?>
                    <div class="empty-state">
                        <i class="fas fa-lock"></i>
                        <p>No private videos</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($privateVideos as $video): ?>
                        <!-- Similar structure to videos grid -->
                        <div class="video-item">
                            <!-- Video thumbnail and info -->
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>    

</div>

<!-- Share Profile Modal -->
<div class="modal" id="shareModal">
    <div class="modal-content">
        <h3>Share Profile</h3>
        <div class="share-options">
            <button class="share-btn" data-platform="facebook">
                <i class="fab fa-facebook"></i> Facebook
            </button>
            <button class="share-btn" data-platform="twitter">
                <i class="fab fa-twitter"></i> Twitter
            </button>
            <button class="share-btn" data-platform="whatsapp">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </button>
            <button class="share-btn" data-platform="telegram">
                <i class="fab fa-telegram"></i> Telegram
            </button>
        </div>
        <div class="copy-link">
            <input type="text" value="<?= $profileUrl ?>" readonly>
            <button id="copyLink">Copy Link</button>
        </div>
        <button class="modal-close">&times;</button>
    </div>
</div>

<style>
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.profile-header {
    display: flex;
    align-items: center;
    margin-bottom: 40px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 30px;
}

.profile-info {
    flex: 1;
}

.profile-info h1 {
    font-size: 24px;
    margin: 0 0 5px;
}

.profile-info h2 {
    font-size: 16px;
    color: #666;
    margin: 0 0 15px;
}

.profile-stats {
    display: flex;
    gap: 30px;
    margin: 20px 0;
}

.stat {
    text-align: center;
}

.stat .count {
    display: block;
    font-size: 18px;
    font-weight: bold;
}

.stat .label {
    color: #666;
    font-size: 14px;
}

.profile-bio {
    margin: 15px 0;
    color: #333;
    white-space: pre-line;
}

.profile-tabs {
    display: flex;
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
}

.tab-btn {
    padding: 15px 30px;
    border: none;
    background: none;
    cursor: pointer;
    font-size: 16px;
    color: #666;
    border-bottom: 2px solid transparent;
}

.tab-btn.active {
    color: var(--primary-color);
    border-bottom-color: var(--primary-color);
}

.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

.video-item {
    position: relative;
    cursor: pointer;
}

.video-thumbnail {
    position: relative;
    padding-top: 177.77%; /* 16:9 Aspect Ratio */
    background: #f0f0f0;
    border-radius: 8px;
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

.video-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 10px;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    color: white;
}

.video-stats {
    display: flex;
    gap: 15px;
    font-size: 14px;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #666;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 10px;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal-content {
    position: relative;
    background: white;
    max-width: 500px;
    margin: 100px auto;
    padding: 20px;
    border-radius: 8px;
}

.share-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin: 20px 0;
}

.share-btn {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}

.copy-link {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.copy-link input {
    flex: 1;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
</style>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show selected tab content
            const contentId = this.dataset.tab + '-content';
            document.getElementById(contentId).classList.remove('hidden');
        });
    });

    // Follow button functionality
    const followBtn = document.querySelector('.follow-btn');
    if (followBtn) {
        followBtn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            fetch(`/api/follow/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.classList.toggle('following');
                    this.textContent = this.classList.contains('following') ? 'Following' : 'Follow';
                    
                    // Update followers count
                    const followersCount = document.querySelector('.followers-count');
                    followersCount.textContent = data.followers_count;
                }
            });
        });
    }

    // Share functionality
    const shareModal = document.getElementById('shareModal');
    const shareBtn = document.querySelector('.share-btn');
    const closeBtn = document.querySelector('.modal-close');
    
    shareBtn?.addEventListener('click', () => shareModal.style.display = 'block');
    closeBtn?.addEventListener('click', () => shareModal.style.display = 'none');
    
    // Copy link functionality
    const copyBtn = document.getElementById('copyLink');
    copyBtn?.addEventListener('click', function() {
        const input = this.previousElementSibling;
        input.select();
        document.execCommand('copy');
        
        this.textContent = 'Copied!';
        setTimeout(() => this.textContent = 'Copy Link', 2000);
    });
});
</script>

<?php
// Helper functions
function formatDuration($seconds) {
    return sprintf("%d:%02d", floor($seconds/60), $seconds % 60);
}

function formatDate($date) {
    $timestamp = strtotime($date);
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        return floor($diff/60) . 'm ago';
    } elseif ($diff < 86400) {
        return floor($diff/3600) . 'h ago';
    } elseif ($diff < 604800) {
        return floor($diff/86400) . 'd ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}
?>