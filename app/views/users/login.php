<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TikTok</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/user/login.css">
    
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <img src="/public/assets/logo-removebg.png" alt="TikTok">
        </div>
        <h2>Login to TikTok</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=login" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <button type="submit" class="submit-btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
            
            <p class="auth-links">
                Don't have an account? <a href="index.php?action=register">Sign up</a>
            </p>
        </form>
    </div>
</body>
</html>