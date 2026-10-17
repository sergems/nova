<?php 
require_once '../includes/seo.php';
include '../includes/header.php'; 
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 data-testid="hero-title">
                        Professional <span class="accent">Debt Solutions</span> You Can Trust
                    </h1>
                    <p data-testid="hero-description">
                        Nova Debt Counselling provides expert debt counselling services with a focus on preserving your dignity while achieving financial freedom. Our experienced team delivers personalized solutions for your unique situation.
                    </p>
                    <div class="hero-buttons">
                        <a href="/contact.php" class="btn btn-primary" data-testid="button-consultation">
                            Get Free Consultation
                        </a>
                        <a href="/services.php" class="btn btn-secondary" data-testid="button-services">
                            Our Services
                        </a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="/images/bg1_1757778869630.jpg" 
                         alt="Professional consultation representing trust and accessibility" 
                         data-testid="hero-image"
                         width="600" height="400" fetchpriority="high">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="section-padding bg-muted" data-testid="services-section">
        <div class="container">
            <div class="text-center">
                <h2 data-testid="services-heading">Comprehensive Debt Solutions</h2>
                <p>We offer a complete range of debt counselling services designed to help you regain financial control and peace of mind.</p>
            </div>

            <div class="services-grid">
                <div class="card" data-testid="service-card-debt-review-cancellation">
                    <div class="card-icon"><i class="fas fa-clipboard-list" style="font-size: 2rem; color: #3498db;"></i></div>
                    <h3 data-testid="service-title-debt-review-cancellation">Debt Review Cancellation</h3>
                    <p data-testid="service-description-debt-review-cancellation">
                        Professional assistance to cancel your debt review when circumstances change, ensuring a smooth transition back to normal credit standing.
                    </p>
                    <a href="/services.php" class="btn btn-outline" data-testid="button-learn-more-debt-review-cancellation">
                        Learn More →
                    </a>
                </div>

                <div class="card" data-testid="service-card-admin-order-cancellation">
                    <div class="card-icon"><i class="fas fa-file-alt" style="font-size: 2rem; color: #27ae60;"></i></div>
                    <h3 data-testid="service-title-admin-order-cancellation">Admin Order Cancellation</h3>
                    <p data-testid="service-description-admin-order-cancellation">
                        Expert guidance through the legal process of cancelling administration orders that may no longer serve your best interests.
                    </p>
                    <a href="/services.php" class="btn btn-outline" data-testid="button-learn-more-admin-order-cancellation">
                        Learn More →
                    </a>
                </div>

                <div class="card" data-testid="service-card-prescribed-debt-removal">
                    <div class="card-icon"><i class="fas fa-broom" style="font-size: 2rem; color: #9b59b6;"></i></div>
                    <h3 data-testid="service-title-prescribed-debt-removal">Prescribed Debt Removal</h3>
                    <p data-testid="service-description-prescribed-debt-removal">
                        Legal removal of prescribed debts from your credit record, helping you start fresh with a clean financial slate.
                    </p>
                    <a href="/services.php" class="btn btn-outline" data-testid="button-learn-more-prescribed-debt-removal">
                        Learn More →
                    </a>
                </div>

                <div class="card" data-testid="service-card-judgement-removal">
                    <div class="card-icon"><i class="fas fa-balance-scale" style="font-size: 2rem; color: #f39c12;"></i></div>
                    <h3 data-testid="service-title-judgement-removal">Judgement Removal</h3>
                    <p data-testid="service-description-judgement-removal">
                        Strategic legal approach to remove court judgements that may be impacting your credit profile and financial opportunities.
                    </p>
                    <a href="/services.php" class="btn btn-outline" data-testid="button-learn-more-judgement-removal">
                        Learn More →
                    </a>
                </div>

                <div class="card" data-testid="service-card-trace-alert-removal">
                    <div class="card-icon"><i class="fas fa-search" style="font-size: 2rem; color: #17a2b8;"></i></div>
                    <h3 data-testid="service-title-trace-alert-removal">Trace Alert Removal</h3>
                    <p data-testid="service-description-trace-alert-removal">
                        Professional assistance in removing trace alerts that may be preventing you from accessing credit and financial services.
                    </p>
                    <a href="/services.php" class="btn btn-outline" data-testid="button-learn-more-trace-alert-removal">
                        Learn More →
                    </a>
                </div>

                <div class="card" data-testid="service-card-garnishee-order-removal">
                    <div class="card-icon"><i class="fas fa-money-bill-wave" style="font-size: 2rem; color: #27ae60;"></i></div>
                    <h3 data-testid="service-title-garnishee-order-removal">Garnishee Order Removal</h3>
                    <p data-testid="service-description-garnishee-order-removal">
                        Legal representation to remove garnishee orders, helping you regain control of your salary and financial resources.
                    </p>
                    <a href="/services.php" class="btn btn-outline" data-testid="button-learn-more-garnishee-order-removal">
                        Learn More →
                    </a>
                </div>
            </div>

            <div class="text-center" style="margin-top: 3rem;">
                <a href="/services.php" class="btn btn-primary" data-testid="button-view-all-services">
                    View All Services
                </a>
            </div>
        </div>
    </section>

    <!-- About Preview Section -->
    <section class="section-padding" data-testid="about-preview-section">
        <div class="container">
            <div class="grid grid-2" style="align-items: center; gap: 4rem;">
                <div>
                    <img src="/images/bg7_1757765316803.jpg" 
                         alt="Professional business meeting discussing financial solutions" 
                         class="hero-image"
                         data-testid="about-image"
                         width="500" height="400" loading="lazy">
                </div>
                <div>
                    <h2 data-testid="about-heading">Leading Debt Counselling in Africa</h2>
                    
                    <div style="margin-bottom: 2rem;">
                        <h3 style="color: var(--nova-primary); margin-bottom: 1rem;">Our Vision</h3>
                        <p>
                            To be the leading service providing company in Africa and be considered amongst the best in the World. We envision developing Nova Debt Counselling into a brand renowned for excellent returns and superior service.
                        </p>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <h3 style="color: var(--nova-primary); margin-bottom: 1rem;">Our Mission</h3>
                        <p>
                            To be the best debt counselling company that will be fair in all collection efforts and perform our duties within the guidelines set out in the code of conduct.
                        </p>
                    </div>

                    <a href="/about.php" class="btn btn-secondary" data-testid="button-learn-more-about">
                        Learn More About Us
                    </a>
                </div>
            </div>
        </div>
    </section>


    <!-- CTA Section -->
    <section class="section-padding bg-primary" data-testid="cta-section">
        <div class="container text-center">
            <h2 data-testid="cta-heading">Ready to Take Control of Your Financial Future?</h2>
            <p style="font-size: 1.25rem; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                Don't let debt control your life. Contact Nova Debt Counselling today for a confidential consultation and start your journey to financial freedom.
            </p>
            <p style="font-size: 1.125rem; margin-bottom: 2rem; max-width: 700px; margin-left: auto; margin-right: auto;">
                Still Have Some Questions Left? Call Nova Debt Counselling for a Free Consultation on <strong>071 802 2762</strong>
            </p>
            <div class="hero-buttons">
                <a href="/contact.php" class="btn btn-secondary" data-testid="button-contact-now">
                    Contact Us Now
                </a>
                <a href="/services.php" class="btn btn-outline" data-testid="button-view-services" style="color: white; border-color: white;">
                    View Our Services
                </a>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>