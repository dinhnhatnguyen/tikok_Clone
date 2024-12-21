<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - TikTok</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/user/register.css">

</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <img src="/public/assets/logo-removebg.png" alt="TikTok">
        </div>
        <h2>Create Account</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=register" method="POST" enctype="multipart/form-data" class="auth-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
                <small>3-20 characters, letters, numbers and underscore only</small>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" id="full_name" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
                <small>Minimum 8 characters</small>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" name="password_confirm" id="password_confirm" required>
            </div>
            
            <div class="form-group">
                <label for="avatar">Profile Picture (Optional)</label>
                <input type="file" name="avatar" id="avatar" accept="image/*">
            </div>
            
            <button type="submit" class="submit-btn">
                <i class="fas fa-user-plus"></i> Sign Up
            </button>
            
            <p class="auth-links">
                Already have an account? <a href="index.php?action=login">Login</a>
            </p>
        </form>
    </div>
</body>
</html>