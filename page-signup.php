<?php
/* Template Name: Custom Signup */

if (is_user_logged_in()) {
    wp_redirect(site_url('/user-dashboard'));
    exit;
}
get_header();


function custom_register_user() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];

        $errors = new WP_Error();

        if (username_exists($username) || email_exists($email)) {
            $errors->add('user_exists', 'Username or email already exists.');
        }

        if (empty($errors->errors)) {
            $user_id = wp_create_user($username, $password, $email);
            if (!is_wp_error($user_id)) {
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                wp_redirect(site_url('/user-dashboard'));
                exit;
            } else {
                $errors->add('registration_failed', 'Registration failed.');
            }
        }

        return $errors;
    }
    return new WP_Error();
}

$errors = custom_register_user();
?>

<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">

<style>
* { box-sizing: border-box; }

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
    margin-bottom: 1.5rem;
    color: #666;
    font-size: 0.95rem;
}

.auth-form .errors {
    background: #fdecea;
    color: #b10000;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
    list-style: none;
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

.auth-form input {
    width: 100%;
    padding: 0.75rem 0.75rem 0.75rem 2.5rem;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
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
    margin-top: 0.5rem;
}

.auth-form button:hover {
    background: #00337a;
}

.auth-form .google-signup {
    margin-top: 1rem;
    text-align: center;
}

.auth-form .google-signup button {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    color: #333;
    border: 1px solid #ccc;
    padding: 0.6rem;
    width: 100%;
    font-size: 0.95rem;
    border-radius: 6px;
    gap: 0.5rem;
    cursor: pointer;
}

.auth-form .google-signup button:hover {
    background: #f7f7f7;
}

.auth-form .login-link {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 0.95rem;
}

.auth-form .login-link a {
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
        <h2>Create an Account</h2>
        <p class="subtitle">Join thousands of learners on our platform</p>

        <?php if ($errors->get_error_messages()): ?>
            <ul class="errors">
                <?php foreach ($errors->get_error_messages() as $error): ?>
                    <li><?= esc_html($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <i class="fa-regular fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-group">
                <i class="fa-regular fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit">Sign Up</button>
        </form>

        <div class="google-signup">
            <button type="button">
                <i class="fa-brands fa-google"></i> Sign up with Google
            </button>
        </div>

        <p class="login-link">
            Already have an account? <a href="<?= site_url('/login'); ?>">Login here</a>
        </p>
    </div>

    <div class="auth-illustration">
        <img src="https://images.unsplash.com/photo-1596495577886-d920f1fb7238?auto=format&fit=crop&w=1000&q=80" alt="Learning Illustration">
    </div>
</div>

<?php get_footer(); ?>
