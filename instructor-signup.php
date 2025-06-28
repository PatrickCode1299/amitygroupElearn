<?php
/* Template Name: Instructor Signup */
get_header();

if (is_user_logged_in()) {
    wp_redirect(home_url('/instructor-dashboard'));
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup_instructor_nonce'])) {
    if (!wp_verify_nonce($_POST['signup_instructor_nonce'], 'signup_instructor')) {
        $errors[] = 'Nonce verification failed.';
    }

    $username = sanitize_user($_POST['username'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
        $errors[] = 'All fields are required.';
    }
    if (!is_email($email)) {
        $errors[] = 'Invalid email address.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match.';
    }
    if (username_exists($username)) {
        $errors[] = 'Username already exists.';
    }
    if (email_exists($email)) {
        $errors[] = 'Email already exists.';
    }

    if (empty($errors)) {
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            $errors[] = $user_id->get_error_message();
        } else {
            $user = new WP_User($user_id);
            $user->set_role('instructor_pending');
            $success = true;
        }
    }
}
?>

<style>
  .signup-container {
    max-width: 900px;
    margin: 3rem auto;
    background: #e6f0ff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgb(0 74 173 / 0.15);
    display: flex;
    gap: 2rem;
    padding: 2rem;
    flex-wrap: wrap;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  .signup-form {
    flex: 1 1 350px;
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgb(0 74 173 / 0.2);
  }
  .signup-form h2 {
    color: #004aad;
    margin-bottom: 1.5rem;
    text-align: center;
  }
  .signup-form label {
    display: block;
    margin: 0.8rem 0 0.3rem;
    font-weight: 600;
    color: #004aad;
  }
  .signup-form input {
    width: 100%;
    padding: 10px 12px 10px 36px;
    font-size: 1rem;
    border: 1.8px solid #004aad;
    border-radius: 6px;
    outline-offset: 2px;
    transition: border-color 0.3s;
    box-sizing: border-box;
  }
  .signup-form input:focus {
    border-color: #0066ff;
    box-shadow: 0 0 5px #0066ffaa;
  }
  .input-icon {
    position: relative;
  }
  .input-icon svg {
    position: absolute;
    top: 50%;
    left: 10px;
    width: 18px;
    height: 18px;
    fill: #004aad;
    transform: translateY(-50%);
    pointer-events: none;
  }
  .signup-form button {
    background: #004aad;
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    width: 100%;
    margin-top: 1.5rem;
    transition: background 0.3s ease;
  }
  .signup-form button:hover {
    background: #003b7a;
  }
  .error-list {
    color: #b30000;
    margin-bottom: 1rem;
    list-style-type: disc;
    padding-left: 20px;
  }
  .success-message {
    color: #007700;
    font-weight: 600;
    text-align: center;
    margin-bottom: 1rem;
  }
  .signup-illustration {
    flex: 1 1 400px;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #004aad;
    border-radius: 10px;
    padding: 1rem;
  }
.signup-illustration img {
  width: 100%;
  height: auto;
  display: block;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 74, 173, 0.5);
  object-fit: cover;
}
  .company-footer {
    text-align: center;
    color: #004aad;
    font-weight: 700;
    margin-top: 2rem;
    font-size: 1.1rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  @media (max-width: 720px) {
  .signup-container {
    flex-direction: column;
    padding: 1rem;
  }
  .signup-illustration {
    display: none; 
  }
}

</style>

<div class="signup-container">
    <div class="signup-form" role="form" aria-labelledby="signup-header">
        <h2 id="signup-header">Signup as an Instructor</h2>

        <?php if ($success): ?>
            <p class="success-message" role="alert">Signup successful! Please wait for admin approval.</p>
        <?php else: ?>
            <?php if ($errors): ?>
                <ul class="error-list" role="alert">
                    <?php foreach ($errors as $e) echo "<li>$e</li>"; ?>
                </ul>
            <?php endif; ?>

            <form method="post" novalidate>
                <?php wp_nonce_field('signup_instructor','signup_instructor_nonce'); ?>

                <div class="input-icon">
                    <label for="username">Username</label>
                    <input id="username" type="text" name="username" placeholder="Choose a username" required autocomplete="username">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                      <path d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12zM4 20c0-2.7 5.4-4 8-4s8 1.3 8 4v2H4v-2z"/>
                    </svg>
                </div>

                <div class="input-icon">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" placeholder="Your email address" required autocomplete="email">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                      <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                </div>

                <div class="input-icon">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Enter password" required autocomplete="new-password">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                      <path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM6 10v-2a6 6 0 0 1 12 0v2h1a1 1 0 0 1 1 1v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a1 1 0 0 1 1-1h1z"/>
                    </svg>
                </div>

                <div class="input-icon">
                    <label for="password_confirm">Confirm Password</label>
                    <input id="password_confirm" type="password" name="password_confirm" placeholder="Confirm your password" required autocomplete="new-password">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                      <path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM6 10v-2a6 6 0 0 1 12 0v2h1a1 1 0 0 1 1 1v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a1 1 0 0 1 1-1h1z"/>
                    </svg>
                </div>

                <button type="submit">Sign Up</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="signup-illustration" aria-hidden="true">
        <img  src="https://assets.designtemplate.io/images/Interactive%20E-Learning%20Environment%20Vector%20Illustration-HD.webp" alt="E-learning illustration showing online education" />
    </div>
</div>


<?php get_footer(); ?>
