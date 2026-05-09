<?php
if (!isset($_SESSION['username'])) {
    echo '<script>window.location.href = "index.php";</script>';
    exit;
}
include("connect.php");
$username = $_SESSION['username'];
?>

<div class="about-wrapper">
    <div class="about-hero">
        <div class="hero-content">
            <h1 class="fade-in">About DigiScam</h1>
            <div class="subtitle slide-up">Redefining Digital Camera Shopping</div>
        </div>
    </div>

    <div class="about-container">
        <div class="mission-section slide-up">
            <div class="text-content">
                <p class="lead">Welcome to DigiScam, your premier destination for online shopping in the Philippines. We are committed to providing a seamless and enjoyable shopping experience for all our customers.
                Our platform connects buyers with quality products at competitive prices, all while ensuring secure transactions and reliable delivery services.</p>
            </div>
        </div>

        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up">
                <div class="card-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="card-content">
                    <h3>Secure Shopping</h3>
                    <p>Your security is our priority. We use advanced encryption to protect your personal and payment information.</p>
                </div>
                <div class="card-hover"></div>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="card-icon">
                    <i class="fas fa-award"></i>
                </div>
                <div class="card-content">
                    <h3>Quality Products</h3>
                    <p>We carefully curate our product selection to ensure you get the best quality items.</p>
                </div>
                <div class="card-hover"></div>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="card-icon">
                    <i class="fas fa-truck-fast"></i>
                </div>
                <div class="card-content">
                    <h3>Fast Delivery</h3>
                    <p>We partner with reliable courier services to ensure your purchases reach you quickly and safely.</p>
                </div>
                <div class="card-hover"></div>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="card-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="card-content">
                    <h3>Customer Support</h3>
                    <p>Our dedicated support team is always ready to assist you with any questions or concerns.</p>
                </div>
                <div class="card-hover"></div>
            </div>
        </div>

        <div class="stats-section">
            <div class="stat-card" data-aos="zoom-in">
                <div class="stat-number">1000+</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="100">
                <div class="stat-number">500+</div>
                <div class="stat-label">Products Sold</div>
            </div>
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support Available</div>
            </div>
        </div>
    </div>
</div>

<style>
.about-wrapper {
    background: #fff;
    overflow: hidden;
}

.about-hero {
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('about_banner.webp') center/cover;
    color: white;
    padding: 6rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.about-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0) 100%);
    opacity: 1;
    animation: subtleFloat 20s ease-in-out infinite;
}

.hero-content {
    position: relative;
    z-index: 1;
}

.hero-content h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    background: linear-gradient(45deg, #fff, #e0e0e0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.subtitle {
    font-size: 1.2rem;
    color: #e0e0e0;
    font-weight: 300;
    letter-spacing: 2px;
}

.about-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 2rem;
}

.mission-section {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 4rem;
}

.lead {
    font-size: 1.25rem;
    color: #333;
    line-height: 1.8;
    margin-bottom: 1.5rem;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
    margin: 4rem 0;
}

.feature-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.card-icon {
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
    color: #4a90e2;
    transition: transform 0.3s ease;
}

.feature-card:hover .card-icon {
    transform: scale(1.1);
}

.card-content h3 {
    color: #333;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.card-content p {
    color: #666;
    line-height: 1.6;
}

.card-hover {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(74, 144, 226, 0.1) 0%, rgba(74, 144, 226, 0) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.feature-card:hover .card-hover {
    opacity: 1;
}

.stats-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-top: 4rem;
    text-align: center;
}

.stat-card {
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 12px;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #4a90e2;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #666;
    font-size: 1.1rem;
}

/* Animations */
@keyframes subtleFloat {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-20px) scale(1.05); }
}

.fade-in {
    animation: fadeIn 1s ease-out;
}

.slide-up {
    animation: slideUp 1s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .about-hero {
        padding: 4rem 1rem;
        background-position: center;
    }

    .hero-content h1 {
        font-size: 2.5rem;
    }

    .features-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .stats-section {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Intersection Observer for scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

document.querySelectorAll('.feature-card, .stat-card').forEach(card => {
    observer.observe(card);
});
</script>
