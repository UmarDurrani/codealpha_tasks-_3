<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (login_user($email, $password)) {
        $_SESSION['success'] = 'Login successful!';
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password';
    }
}
?>
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <i class="fas fa-tasks fa-3x"></i>
                    <h1>Welcome to ProjectFlow</h1>
                    <p>Sign in to your account</p>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>
                
                <div class="auth-footer">
                    <p>Don't have an account? <a href="register.php">Create one here</a></p>
                </div>
            </div>
        </div>
<?php require_once 'includes/footer.php'; ?>