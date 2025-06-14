<footer>
    <div class="footer-grid">
        <div class="column">
            <h4>Site Map</h4>
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/training-services">Training Services</a></li>
                <li><a href="/about">About</a></li>
                <li><a href="/consulting">Consulting</a></li>
                <li><a href="/contact">Contact</a></li>
            </ul>
        </div>
        <div class="column">
            <h4>Training Services</h4>
            <ul>
                <li><a href="#">Organizational Workforce Development</a></li>
                <li><a href="#">Corporate IT Competency</a></li>
                <li><a href="#">Supply Chain & Cost Management</a></li>
                <li><a href="#">Healthcare Coaching</a></li>
                <li><a href="#">Education Professional Development</a></li>
                <li><a href="#">Diversity Awareness Training</a></li>
                <li><a href="#">Biotech & Government Certifications</a></li>
            </ul>
        </div>
     <div class="column">
        <h4>Follow Us</h4>
        <div class="social-icons clean-icons">
            <a href="https://web.facebook.com/profile.php?id=100068896428786" target="_blank" aria-label="Facebook">
                <i class="fa-brands fa-facebook"></i>
            </a>
            <a href="#" target="_blank" aria-label="Instagram">
                <i class="fa-brands fa-instagram"></i>
            </a>
            <a href="#" target="_blank" aria-label="Twitter">
                <i class="fa-brands fa-x-twitter"></i>
            </a>
        </div>
    </div>

    <div style="text-align: center; margin-top: 2rem;">
        <p>&copy; <?php echo date('Y'); ?>  amitygrouptrainers.org</p>
    </div>
</footer>
<style>
    footer {
    background-color: #002c6f;
    color: white;
    padding: 3rem 2rem;
    font-size: 0.95rem;
}

footer .footer-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 2rem;
}

footer .column {
    flex: 1;
    min-width: 200px;
}

footer h4 {
    font-size: 1.1rem;
    margin-bottom: 1rem;
    color: #ffffff;
}

footer ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

footer ul li {
    margin-bottom: 0.5rem;
}

footer ul li a {
    color: #cce0ff;
    text-decoration: none;
}

footer ul li a:hover {
    text-decoration: underline;
}

footer .social-icons a {
    color: #cce0ff;
    margin-right: 10px;
    text-decoration: none;
    font-size: 1.2rem;
}
</style>
<?php wp_footer(); ?>
</body>
</html>
