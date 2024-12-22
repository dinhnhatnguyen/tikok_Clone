<?php
// Kiểm tra xem đang xem profile của chính mình không
$isOwnProfile = isset($_SESSION['user_id']) && $user['id'] == $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/user/profile.css">
</head>

<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="<?= htmlspecialchars($user['avatar_url'] ?? '/assets/default-avatar.png') ?>"
                alt="<?= htmlspecialchars($user['username']) ?>"
                class="profile-avatar">

            <div class="profile-info">
                <h1><?= htmlspecialchars($user['username']) ?></h1>
                <h2><?= htmlspecialchars($user['full_name']) ?></h2>

                <div class="button-group">
                    <button class="edit-profile-btn">Edit Profile</button>
                    <a href="/" class=" btn edit-profile-btn">Back to home page</a>
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

        <div class="video-grid">
            <?php if (empty($videos)): ?>
                <div class="empty-state">
                    <i class="fas fa-video"></i>
                    <p>No videos yet</p>
                </div>
            <?php else: ?>
                <?php foreach ($videos as $video): ?>
                    <div class="video-item">
                        <div class="video-thumbnail">
                            <img src="<?php echo htmlspecialchars($video['thumbnail_url']); ?>" alt="Video thumbnail">

                            <?php if ($isOwnProfile): ?>
                                <div class="video-actions">
                                    <a href="index.php?action=edit&id=<?php echo $video['id']; ?>"
                                        class="edit-btn"
                                        title="Edit video">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?action=delete&id=<?php echo $video['id']; ?>"
                                        class="delete-btn"
                                        title="Delete video"
                                        onclick="return confirm('Are you sure you want to delete this video?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="video-overlay">
                                <h3><?php echo htmlspecialchars($video['title']); ?></h3>
                                <div class="video-stats">
                                    <span><i class="fas fa-heart"></i> 124.5K</span>
                                    <span><i class="fas fa-comment"></i> 1.2K</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>