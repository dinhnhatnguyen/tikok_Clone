<?php
// views/users/login.php
?>
<div class="auth-container">
    <h2>Login to TikTok</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <form action="/login" method="POST" class="auth-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        
        <button type="submit" class="submit-btn">Login</button>
        
        <p class="auth-links">
            Don't have an account? <a href="index.php?action=register">Sign up</a>
        </p>
    </form>
</div>




<?php
// views/users/register.php
?>
<div class="auth-container">
    <h2>Create Account</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <form action="/register" method="POST" enctype="multipart/form-data" class="auth-form">
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
        
        <button type="submit" class="submit-btn">Sign Up</button>
        
        <p class="auth-links">
            Already have an account? <a href="/login">Login</a>
        </p>
    </form>
</div>