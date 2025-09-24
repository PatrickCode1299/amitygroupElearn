<?php
/* Template Name: Veterans Registration */
get_header();

$us_states = [
  "Alabama","Alaska","Arizona","Arkansas","California","Colorado","Connecticut","Delaware","Florida","Georgia",
  "Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Maryland",
  "Massachusetts","Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire",
  "New Jersey","New Mexico","New York","North Carolina","North Dakota","Ohio","Oklahoma","Oregon","Pennsylvania",
  "Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington",
  "West Virginia","Wisconsin","Wyoming"
];

$success_message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fullname'], $_POST['email'])) {
    $fullname = sanitize_text_field($_POST['fullname']);
    $email    = sanitize_email($_POST['email']);
    $phone    = sanitize_text_field($_POST['phone']);
    $city     = sanitize_text_field($_POST['city']);
    $state    = sanitize_text_field($_POST['state']);
    $startup  = sanitize_text_field($_POST['startup']);
    $grant    = sanitize_text_field($_POST['grant']);

    $username = sanitize_user(strtolower(str_replace(' ', '_', $fullname)));
    if (username_exists($username)) {
        $username .= '_' . rand(1000,9999); // ensure unique username
    }

    $password = wp_generate_password(12);

    // Check if email already exists
    $user_id = email_exists($email);

    if (!$user_id) {
        $user_id = wp_create_user($username, $password, $email);

        if (!is_wp_error($user_id)) {
            wp_update_user([
                'ID' => $user_id,
                'display_name' => $fullname
            ]);

            update_user_meta($user_id, 'phone', $phone);
            update_user_meta($user_id, 'city', $city);
            update_user_meta($user_id, 'state', $state);
            update_user_meta($user_id, 'startup_cost', $startup);
            update_user_meta($user_id, 'grant_request', $grant);

                   wp_mail(
            $email,
            "Your AmityGroup Veterans Account",
            "Hello $fullname,\n\nYour account has been created.\nLogin: $username\nPassword: $password\n\nYou can change your password anytime in your dashboard."
        );


            $success_message = "🎉 Thank you, $fullname! Your account has been created successfully.";
        } else {
            $success_message = "⚠️ User creation failed: " . $user_id->get_error_message();
        }
    } else {
        $success_message = "⚠️ An account with this email already exists.";
    }
}

?>

<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    background: #f9fbfd;
  }

  /* Hero */
  .veterans-hero {
    background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
      url('https://source.unsplash.com/1600x700/?veteran,flag,america') no-repeat center center/cover;
    color: #fff;
    text-align: center;
    padding: 7rem 2rem;
  }
  .veterans-hero h1 {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 1px;
  }
  .veterans-hero p {
    font-size: 1.3rem;
    max-width: 850px;
    margin: 0 auto;
    line-height: 1.8;
  }

  /* Intro */
  .veterans-intro {
    max-width: 1100px;
    margin: 4rem auto;
    text-align: center;
    padding: 0 1.5rem;
  }
  .veterans-intro h2 {
    font-size: 2.5rem;
    font-weight: 800;
    color: #004aad;
    margin-bottom: 1rem;
  }
  .veterans-intro p {
    font-size: 1.15rem;
    color: #555;
    line-height: 1.8;
    margin-bottom: 1.5rem;
  }

  /* Promo highlight */
  .veterans-promo {
    background: #004aad;
    color: #fff;
    text-align: center;
    padding: 3rem 1.5rem;
  }
  .veterans-promo h2 {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 1rem;
  }
  .veterans-promo p {
    font-size: 1.2rem;
    max-width: 700px;
    margin: 0 auto;
  }

  /* Steps */
  .veterans-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
    gap: 2rem;
    max-width: 1100px;
    margin: 4rem auto;
    padding: 0 1.5rem;
  }
  .veterans-step {
    background: #fff;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
  }
  .veterans-step h3 {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 1rem 0;
    color: #004aad;
  }
  .veterans-step p {
    color: #555;
    line-height: 1.6;
  }

  /* Form section */
  .veterans-form-section {
    display: flex;
    align-items: stretch;
    justify-content: center;
    gap: 2rem;
    max-width: 1200px;
    margin: 4rem auto;
    padding: 0 1rem;
    flex-wrap: wrap;
  }
  .veterans-form {
    flex: 1;
    background: #fff;
    padding: 3rem 2.5rem;
    border-radius: 15px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    min-width: 320px;
  }
  .veterans-form h3 {
    text-align: center;
    font-size: 2.2rem;
    font-weight: 800;
    margin-bottom: 2.5rem;
    color: #004aad;
  }
  .veterans-form label {
    display: block;
    font-weight: 700;
    margin-bottom: 0.6rem;
    color: #222;
  }
  .veterans-form input,
  .veterans-form select {
    width: 100%;
    padding: 15px 18px;
    border: 1.5px solid #ccc;
    border-radius: 10px;
    margin-bottom: 1.8rem;
    font-size: 1rem;
    transition: all 0.3s ease;
  }
  .veterans-form input:focus,
  .veterans-form select:focus {
    border-color: #004aad;
    box-shadow: 0 0 8px rgba(0,74,173,0.2);
    outline: none;
  }
  .veterans-form button {
    width: 100%;
    background: linear-gradient(90deg, #004aad, #006eff);
    color: #fff;
    padding: 16px;
    border: none;
    border-radius: 10px;
    font-size: 1.2rem;
    font-weight: 800;
    cursor: pointer;
  }
  .veterans-form button:hover {
    background: linear-gradient(90deg, #00347a, #0056c7);
  }
  .veterans-image {
    flex: 1;
    min-width: 320px;
  }
  .veterans-image img {
    width: 100%;
    border-radius: 15px;
    object-fit: cover;
    height: 100%;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
  }

  /* CTA banner */
  .veterans-cta {
    background: #004aad;
    color: #fff;
    text-align: center;
    padding: 3rem 1.5rem;
    margin-top: 4rem;
  }
  .veterans-cta h2 {
    font-size: 2.2rem;
    font-weight: 800;
    margin-bottom: 1rem;
  }
  .veterans-cta a {
    display: inline-block;
    background: #fff;
    color: #004aad;
    padding: 14px 26px;
    font-weight: 700;
    border-radius: 8px;
    margin-top: 1rem;
    transition: all 0.3s;
  }
  .veterans-cta a:hover {
    background: #e6e6e6;
  }
</style>

<!-- Hero -->
<section class="veterans-hero">
  <h1>Veterans Business Training & Grants</h1>
  <p>Empowering <strong>veterans</strong> with the skills, resources, and funding to build thriving businesses. 
  Be among the first <strong>50 registrants</strong> to purchase a course and secure exclusive grant support.</p>
</section>

<!-- Intro -->
<section class="veterans-intro">
  <h2>Training & Grant Opportunities</h2>
  <p>AmityGroup proudly supports veterans transitioning into entrepreneurship. Our hands-on training equips you 
  with the tools to start and grow your business, while our grant program helps reduce startup barriers. 
  We honor your service by investing in your future success.</p>
</section>

<!-- Promo Highlight -->
<section class="veterans-promo">
  <h2>🎖 Special Launch Promo</h2>
  <p>The first <strong>50 veterans</strong> who register and purchase a course will receive up to <strong>$20,000 in grants</strong> to jumpstart their business journey.</p>
</section>

<!-- Steps -->
<section class="veterans-steps">
  <div class="veterans-step">
    <h3>1. Register</h3>
    <p>Fill out the form with your details and business idea.</p>
  </div>
  <div class="veterans-step">
    <h3>2. Train</h3>
    <p>Access world-class training in business, IT, and leadership.</p>
  </div>
  <div class="veterans-step">
    <h3>3. Receive Grant</h3>
    <p>Get up to $20,000 in grant funding for your startup.</p>
  </div>
  <div class="veterans-step">
    <h3>4. Launch</h3>
    <p>Put your plan into action and grow your business with confidence.</p>
  </div>
</section>

<!-- Flex Form + Image -->
<div class="veterans-form-section">
  <div class="veterans-form">
    <h3>Register Today</h3>
    <form method="POST">
      <label for="fullname">First and Last Name</label>
      <input placeholder="First Name" type="text" id="fullname" name="fullname" required>

      <label for="email">Civilian Email Address</label>
      <input placeholder="Email: " type="email" id="email" name="email" required>

      <label for="phone">Phone Number</label>
      <input placeholder="Phone number" type="tel" id="phone" name="phone" required>

      <label for="city">City</label>
      <input type="text" id="city" name="city" required>

      <label for="state">State</label>
      <select id="state" name="state" required>
        <option value="">Select State</option>
        <?php foreach ($us_states as $state): ?>
          <option value="<?php echo esc_attr($state); ?>"><?php echo esc_html($state); ?></option>
        <?php endforeach; ?>
      </select>

      <label for="startup">Estimated Startup Costs ($)</label>
      <input type="number" id="startup" name="startup" required>

      <label for="grant">Desired Grant Amount</label>
      <select id="grant" name="grant" required>
        <option value="">Select Grant Amount</option>
        <option value="5000">$5,000</option>
        <option value="10000">$10,000</option>
        <option value="15000">$15,000</option>
        <option value="20000">$20,000</option>
      </select>

      <button type="submit">Submit Registration</button>
      <?php if (!empty($success_message)) : ?>
  <div style="background:#e6f9f0; color:#056839; padding:15px; margin-bottom:20px; border-radius:8px; font-weight:600;">
    <?php echo esc_html($success_message); ?>
  </div>
<?php endif; ?>

    </form>
  </div>
  <div class="veterans-image">
    <img src="https://images.unsplash.com/photo-1716837682689-e834ac498c15?q=80&w=870&auto=format&fit=crop" alt="Veterans Training">
  </div>
</div>

<!-- CTA -->
<section class="veterans-cta">
  <h2>Ready to Build Your Future?</h2>
  <p>Join AmityGroup’s veteran training and grant program today.</p>
  <a href="#fullname">Register Now</a>
</section>

<?php get_footer(); ?>
