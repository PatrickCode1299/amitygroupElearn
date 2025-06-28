<?php
/* Template Name: Custom Login */
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $creds = [
        'user_login'    => sanitize_text_field($_POST['username']),
        'user_password' => $_POST['password'],
        'remember'      => isset($_POST['remember'])
    ];
    $user = wp_signon($creds, false);
    if (is_wp_error($user)) {
        $message = $user->get_error_message();
    } else {
        wp_redirect(site_url('/user-dashboard'));
        exit;
    }
}

$signup_url = site_url('/signup');

get_header();
?>


<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">

<style>
* {
    box-sizing: border-box;
}

.auth-wrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: stretch;
    min-height: 90vh;
    padding: 2rem;
    gap: 2rem;
    background: #f0f4f8;
}

.auth-form, .auth-illustration {
    flex: 1 1 420px;
    background: #ffffff;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    max-width: 500px;
    overflow: hidden;
}

.auth-form h2 {
    color: #004aad;
    font-size: 2rem;
    margin-bottom: 0.5rem;
    text-align: center;
}

.auth-form p.subtitle {
    text-align: center;
    margin-bottom: 2rem;
    color: #666;
    font-size: 0.95rem;
}

.auth-form .message {
    margin-bottom: 1rem;
    color: #e00;
    text-align: center;
}

.auth-form label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
}

.auth-form .input-group {
    position: relative;
    margin-bottom: 1.5rem;
}

.auth-form .input-group i {
    position: absolute;
    top: 50%;
    left: 12px;
    transform: translateY(-50%);
    color: #888;
}

.auth-form input[type="text"],
.auth-form input[type="password"] {
    width: 100%;
    padding: 0.75rem 0.75rem 0.75rem 2.5rem;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
    display: block;
}

.auth-form input[type="checkbox"] {
    margin-right: 0.5rem;
}

.auth-form button {
    width: 100%;
    padding: 0.75rem;
    background: #004aad;
    color: white;
    font-weight: bold;
    font-size: 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.auth-form button:hover {
    background: #00337a;
}

.auth-form .signup {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 0.95rem;
}

.auth-form .signup a {
    color: #004aad;
    text-decoration: underline;
}

.auth-illustration {
    display: flex;
    justify-content: center;
    align-items: center;
    background: #eaf1fb;
    border-radius: 12px;
    overflow: hidden;
    padding: 0;
}

.auth-illustration img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 12px;
}
</style>

<div class="auth-wrapper">
    <div class="auth-form">
        <h2>Welcome Back</h2>
        <p class="subtitle">Log in to access your personalized courses and progress</p>

        <?php if ($message): ?>
            <p class="message"><?= esc_html($message) ?></p>
        <?php endif; ?>

        <form method="post">
            <div class="input-group">
                <i class="fa-regular fa-user"></i>
                <input type="text" name="username" placeholder="Username or Email" required>
            </div>

            <div class="input-group">
                <i class="fa-regular fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <label><input type="checkbox" name="remember"> Remember Me</label>

            <button type="submit">Log In</button>
        </form>

        <p class="signup">
            Don't have an account? <a href="<?= esc_url($signup_url) ?>">Sign up here</a>
        </p>
    </div>

    <div class="auth-illustration">
        <img src="https://i.pinimg.com/736x/46/b8/59/46b859188fcedf524746e395e52d6279.jpg" alt="E-learning Illustration">
    </div>
</div>

<?php get_footer(); ?>
