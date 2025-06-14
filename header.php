<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">
    
      <link
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css"
      >
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<style>
/* Basic Reset */
/* Header */
.site-header {
  position: sticky;
  top: 0;
  z-index: 999;
  background-color: rgba(0, 74, 173, 0.95); /* modern blur bg */
  backdrop-filter: blur(6px);
  color: white;
  padding: 1rem 0;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
  border-top-left-radius: 0;
  border-top-right-radius: 0;
  margin: 0; /* full width on mobile */
}

/* Apply margin & border radius only on larger screens */
@media (min-width: 992px) {
  .site-header {
    margin: 0 40px;
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
  }
}

/* Container stays centered and padded */
.site-header .container {
  width: 95%;
  max-width: 1300px;
  margin: auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 1rem;
}
er */
.site-header .container {
  width: 95%;
  max-width: 1300px;
  margin: auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 1rem;
}

/* Logo */
.logo a {
  color: white;
  font-size: 2rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-decoration: none;
}

/* Navigation */
.main-nav ul {
  list-style: none;
  display: flex;
  gap: 2.5rem;
  align-items: center;
  font-size: 1.1rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.main-nav ul li a {
  color: white;
  text-decoration: none;
  position: relative;
  padding-bottom: 0.2rem;
  transition: color 0.3s ease;
}

.main-nav ul li a::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: 0;
  width: 0%;
  height: 2px;
  background: #ffcc00;
  transition: width 0.3s ease;
}

.main-nav ul li a:hover::after {
  width: 100%;
}

/* CTA Button */
.main-nav ul li a.cta {
  background: #ffcc00;
  color: #004aad;
  padding: 0.5rem 1.2rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.main-nav ul li a.cta:hover {
  background: #e6b800;
  color: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Hamburger Button */
#menu-toggle {
  display: none;
  font-size: 2rem;
  color: white;
  background: none;
  border: none;
  cursor: pointer;
}

/* Mobile Styles */
@media (max-width: 768px) {
  #menu-toggle {
    display: block;
  }

  .main-nav ul {
    display: none;
    flex-direction: column;
    background: #004aad;
    position: absolute;
    top: 60px;
    right: 0;
    width: 220px;
    padding: 1rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    border-radius: 8px 0 0 8px;
  }

  .main-nav ul.show {
    display: flex;
  }

  .main-nav ul li {
    margin-bottom: 1rem;
  }

  .main-nav ul li:last-child {
    margin-bottom: 0;
  }
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  AOS.init({ duration: 800, once: true });

  const toggleBtn = document.getElementById('menu-toggle');
  const menu = document.getElementById('menu');
  toggleBtn?.addEventListener('click', () => menu.classList.toggle('show'));

  const header = document.querySelector('.site-header');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 0) header.classList.add('scrolled');
    else header.classList.remove('scrolled');
  });
});
</script>


    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="site-header">
  <div class="container">
    <div class="logo">
      <a href="<?php echo home_url(); ?>">AmityGroupTrainers</a>
    </div>
    <nav class="main-nav">
      <button id="menu-toggle" aria-label="Toggle menu">&#9776;</button>
      <ul id="menu">
        <li><a href="<?php echo home_url(); ?>">Home</a></li>
        <li><a href="#">Courses</a></li>
        <li><a href="#">About</a></li>
        <li><a href="#">Contact</a></li>
        <li><a href="#" class="cta">Login</a></li>
      </ul>
    </nav>
  </div>
</header>

