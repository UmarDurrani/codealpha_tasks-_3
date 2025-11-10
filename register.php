<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $result = register_user($name, $email, $password);
        if ($result === true) {
            $_SESSION['success'] = 'Registration successful! Please login.';
            header('Location: login.php');
            exit;
        } else {
            $error = $result;
        }
    }
}
?>
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <i class="fas fa-user-plus fa-3x"></i>
                    <h1>Create Account</h1>
                    <p>Join ProjectFlow today</p>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Sign in here</a></p>
                </div>
            </div>
        </div>
<?php require_once 'includes/footer.php'; ?>