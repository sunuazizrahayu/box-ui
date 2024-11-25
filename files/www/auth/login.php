<?php
session_start();

// Load credentials
$credentials = include 'credentials.php';
$stored_username = $credentials['username'];
$stored_hashed_password = $credentials['hashed_password'];

$config_file = $_SERVER['DOCUMENT_ROOT'].'/auth/config.json';

if (!file_exists($config_file)) {
    die('Error: Configuration file not found.');
}

$config = json_decode(file_get_contents($config_file), true);

// Define the LOGIN_ENABLED constant based on the JSON file
define('LOGIN_ENABLED', $config['LOGIN_ENABLED']);

// Check if login is disabled
$login_disabled = !LOGIN_ENABLED;

// If login is disabled, set a session flag or a message variable
if ($login_disabled) {
    $_SESSION['login_disabled'] = true;
}

// Check if the user is already logged in and redirect accordingly
if (isset($_SESSION['user_id'])) {
    $redirect_to = isset($_SESSION['redirect_to']) ? $_SESSION['redirect_to'] : '/';
    unset($_SESSION['redirect_to']);
    header("Location: $redirect_to");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate credentials
    if ($username === $stored_username && password_verify($password, $stored_hashed_password)) {
        $_SESSION['user_id'] = session_id();
        $_SESSION['username'] = $username;
        $redirect_to = isset($_SESSION['redirect_to']) ? $_SESSION['redirect_to'] : '/';
        unset($_SESSION['redirect_to']);
        header("Location: $redirect_to");
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}

$base_url = '/theme/'
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Box UI - Login</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?=$base_url ?>plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=$base_url ?>dist/css/adminlte.min.css?v=3.2.0">
    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="/webui/assets/img/icon.png">
</head>
<body class="hold-transition login-page dark-mode">
<div class="login-box">
    <div class="login-logo">
        <img src="/webui/assets/img/logo.png" class="img-fluid">
    </div>
    <?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" style="color: #fff; opacity: 1" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?=$error ?>
    </div>
    <?php endif ?>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Welcome to Box UI</p>

            <form method="post" action="login.php">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Username" name="username">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password" name="password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                </div>

                <p class="mt-3 text-center">
                    Made by <a href="https://github.com/sunuazizrahayu" target="_blank">@sunuazizrahayu</a>
                </p>
            </form>
        </div>
    </div>
</div>
<!-- jQuery -->
<script src="<?=$base_url ?>plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?=$base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=$base_url ?>dist/js/adminlte.min.js?v=3.2.0"></script>
</body>
</html>