<?php
// login.php
require_once 'includes/auth.php';

// In a real scenario, these would be in a .env or config file
$admin_password_hash = '$2y$10$s4SdKljxK.KwcuxHSyK5z.aCBUSMTFQ7r/08mZQwoK0u/0X9EhLX6'; // Default: admin123

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);
    
    $password = $_POST['password'] ?? '';
    
    // For this demo, let's use a simple check if the hash is the placeholder
    // In production, the user should replace the hash above.
    if (password_verify($password, $admin_password_hash)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid password.';
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Araw-Araw Sakit</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-container glass">
        <h1>Admin Login</h1>
        <p class="subtitle">Enter your password to manage the pain.</p>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required autofocus>
            </div>
            <button type="submit" class="btn-primary">Aksesin</button>
        </form>
    </div>
</body>
</html>
