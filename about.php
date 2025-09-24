<?php
/* Template Name: About Page */
get_header();
?>

<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
  }

  /* Hero */
  .about-hero {
    max-width: 1200px;
    margin: 5rem auto 3rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 2rem;
    padding: 2rem 1rem;
  }
  .about-hero-content {
    flex: 1 1 500px;
    color:white;
  }
  .about-hero-content h1 {
    font-size: 2.8rem;
    margin-bottom: 1rem;
    color: #fff;
  }
  .about-hero-content p {
    font-size: 1.2rem;
    line-height: 1.8;
    margin-bottom: 1.5rem;
    color: #fff;
  }
  .about-hero img {
    flex: 1 1 500px;
    width: 100%;
    border-radius: 16px;
    object-fit: cover;
    box-shadow: 0 6px 22px rgba(0,0,0,0.15);
  }

  /* Section blocks */
  .about-block {
    display: flex;
    align-items: center;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 1rem;
  }
  .about-block img {
    flex: 1 1 50%;
    width: 100%;
    border-radius: 14px;
    object-fit: cover;
    box-shadow: 0 4px 14px rgba(0,0,0,0.1);
  }
  .about-block .text {
    flex: 1 1 50%;
  }
  .about-block h2 {
    font-size: 2rem;
    color: #004aad;
    margin-bottom: 1rem;
  }
  .about-block p {
    line-height: 1.8;
    margin-bottom: 1rem;
    color: #222;
  }
  .about-block:nth-child(even) {
    flex-direction: row-reverse;
  }

  /* Background colors */
  .about-bg-light {
    background: #f9f9f9;
  }
  .about-bg-white {
    background: #fff;
  }

  /* Stats */
  .stats-section {
    background: #004aad;
    color: #fff;
    padding: 4rem 1rem;
    text-align: center;
  }
  .stats-grid {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
  }
  .stats-grid h3 {
    font-size: 2rem;
    margin-bottom: 0.3rem;
  }
  .stats-grid p {
    font-size: 1.1rem;
    opacity: 0.9;
  }

  /* CTA */
  .about-cta {
    text-align: center;
    padding: 4rem 1rem;
    background: #f5f9ff;
  }
  .about-cta h2 {
    font-size: 2rem;
    color: #004aad;
    margin-bottom: 1rem;
  }
  .about-cta p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    color: #444;
  }
  .about-cta a {
    background: #004aad;
    color: #fff;
    padding: 14px 30px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.3s ease;
  }
  .about-cta a:hover {
    background: #00347a;
  }

  @media (max-width: 768px) {
    .about-hero, .about-block {
      flex-direction: column;
    }
    .about-hero-content, .about-block .text {
      text-align: center;
    }
  }
</style>

<!-- Hero Section -->
<div class="about-hero">
  <div class="about-hero-content">
    <h1>About AmityGroup</h1>
    <p>For over two decades, AmityGroup has been empowering individuals and organizations through innovative training. From IT and technology to workplace and staff development, we’ve become a household name in professional education.</p>
    <p>Our journey is defined by impact, expertise, and the drive to shape the future of learning.</p>
  </div>
  <img src="https://images.unsplash.com/photo-1596495577886-d920f1fb7238?auto=format&fit=crop&w=1200&q=80" alt="AmityGroup Team Collaboration">
</div>

<!-- Who We Are -->
<div class="about-block about-bg-light">
  <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80" alt="Who We Are">
  <div class="text">
    <h2>Who We Are</h2>
    <p>We are a diverse team of educators, trainers, and professionals passionate about lifelong learning. With a foundation of trust and innovation, AmityGroup has grown into a leader in delivering professional and technology training worldwide.</p>
    <p>Our people are our strength, and we’re united by a vision to help learners unlock their full potential.</p>
  </div>
</div>

<!-- What We Do -->
<div class="about-block about-bg-white">
  <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1200&q=80" alt="What We Do">
  <div class="text">
    <h2>What We Do</h2>
    <p>We provide training for individuals aiming to upskill, change careers, or start entrepreneurial ventures. For organizations, our tailored programs foster growth, productivity, and workplace excellence.</p>
    <p>Our solutions combine in-person workshops, corporate seminars, and flexible e-learning options accessible worldwide.</p>
  </div>
</div>


<!-- Our Expertise -->
<div class="about-block about-bg-light">
  <img src="https://images.unsplash.com/photo-1758270705317-3ef6142d306f?q=80&w=1031&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Our Expertise">
  <div class="text">
    <h2>Our Expertise</h2>
    <p>Our leadership team includes PhD professors and former IT professionals from leading U.S. companies. With academic rigor and real-world insights, our programs cover IT, management, entrepreneurship, and more.</p>
    <p>Every course is designed to ensure learners gain relevant, impactful skills for the modern workplace.</p>
  </div>
</div>

<!-- Our Future -->
<div class="about-block about-bg-white">
  <img src="https://images.unsplash.com/photo-1501504905252-473c47e087f8?q=80&w=774&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Future of Learning">
  <div class="text">
    <h2>Our Future</h2>
    <p>We are embracing a new era with our e-learning platform — powered by interactivity, AI-driven personalization, and global reach. This is the future of learning, and we’re proud to lead the way.</p>
    <p>At AmityGroup, we’re building not just skills, but futures.</p>
  </div>
</div>

<!-- Stats Section -->
<section class="stats-section">
  <div class="stats-grid">
    <div><h3>250+</h3><p>Total Courses</p></div>
    <div><h3>300+</h3><p>Expert Instructors</p></div>
    <div><h3>35k+</h3><p>Students Trained</p></div>
    <div><h3>42k+</h3><p>Certificates Awarded</p></div>
  </div>
</section>

<!-- CTA -->
<div class="about-cta">
  <h2>Ready to Start Your Learning Journey?</h2>
  <p>Join thousands of learners who trust AmityGroup to help them achieve their goals. Explore our courses and grow with us today.</p>
  <a href="<?php echo home_url('/courses'); ?>">Explore Courses</a>
</div>

<?php get_footer(); ?>
