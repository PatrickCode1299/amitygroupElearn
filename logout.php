<?php
/* Template Name: Logout */
get_header();

// Log out user and redirect to home or login
wp_logout();

// Optional: redirect somewhere after logout
wp_redirect(home_url('/login')); 
exit;

get_footer();
