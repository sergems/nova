<?php 
require_once '../includes/seo.php';
include '../includes/header.php'; 
?>

<main id="main-content" data-page="thank-you">
    <!-- Hero Section -->
    <section class="hero">
        <div class="container text-center">
            <h1>Thank You for Contacting Us!</h1>
            <p style="font-size: 1.25rem; margin-bottom: 2rem; max-width: 800px; margin-left: auto; margin-right: auto; color: white;">
                Your message has been received successfully. We'll get back to you within 24 hours with a personalized response.
            </p>
        </div>
    </section>

    <!-- Success Message Section -->
    <section class="section-padding">
        <div class="container text-center" style="max-width: 800px;">
            <div style="background-color: #d1fae5; color: #065f46; padding: 2rem; border-radius: var(--border-radius); margin-bottom: 3rem; border: 1px solid #a7f3d0;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">✅</div>
                <h2 style="color: #065f46; margin-bottom: 1rem;">Message Sent Successfully!</h2>
                <p style="margin: 0; color: #065f46;">
                    Thank you for reaching out to Nova Debt Counselling. Our team of experienced professionals will review your inquiry and respond within 24 hours.
                </p>
            </div>

            <div style="margin-bottom: 3rem;">
                <h3>What Happens Next?</h3>
                <div class="grid grid-3" style="margin-top: 2rem;">
                    <div>
                        <div style="font-size: 3rem; color: var(--nova-primary); margin-bottom: 1rem;">1️⃣</div>
                        <h4>Review</h4>
                        <p>Our experts will carefully review your inquiry and assess your specific situation.</p>
                    </div>
                    <div>
                        <div style="font-size: 3rem; color: var(--nova-primary); margin-bottom: 1rem;">2️⃣</div>
                        <h4>Response</h4>
                        <p>We'll contact you within 24 hours with personalized advice and next steps.</p>
                    </div>
                    <div>
                        <div style="font-size: 3rem; color: var(--nova-primary); margin-bottom: 1rem;">3️⃣</div>
                        <h4>Solution</h4>
                        <p>We'll work together to develop a solution that restores your financial freedom.</p>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 3rem;">
                <h3>In the Meantime</h3>
                <p>Feel free to explore our website to learn more about our services and approach:</p>
                <div class="hero-buttons" style="margin-top: 2rem;">
                    <a href="/services.php" class="btn btn-primary">
                        Learn About Our Services
                    </a>
                    <a href="/about.php" class="btn btn-secondary">
                        About Nova Debt Counselling
                    </a>
                </div>
            </div>

            <div style="background-color: var(--muted); padding: 2rem; border-radius: var(--border-radius);">
                <h4>Need Immediate Assistance?</h4>
                <p>If you have an urgent matter, please contact us directly:</p>
                <p style="font-weight: 600; margin: 0;">
                    <a href="mailto:admin@ndcl.co.za" style="color: var(--nova-primary);">admin@ndcl.co.za</a>
                </p>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>