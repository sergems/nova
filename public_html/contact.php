<?php 
// Start session for CSRF protection
session_start();

// Generate and store CSRF token in session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../includes/seo.php';
include '../includes/header.php'; 
?>

<main data-page="contact">
    <!-- Hero Section -->
    <section class="hero">
        <div class="container text-center">
            <h1 data-testid="contact-hero-title">
                Get Your Free <span class="accent">Consultation</span>
            </h1>
            <p style="font-size: 1.25rem; margin-bottom: 2rem; max-width: 800px; margin-left: auto; margin-right: auto; color: white;">
                Ready to take control of your financial future? Contact us today for a confidential consultation with our debt counselling experts.
            </p>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="section-padding" data-testid="contact-form-section">
        <div class="container">
            <div class="grid grid-2" style="gap: 4rem;">
                <!-- Contact Form -->
                <div class="contact-form">
                    <h2 data-testid="contact-form-heading">Send Us a Message</h2>
                    <form id="contact-form" data-testid="contact-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="firstName" class="form-label">First Name *</label>
                                <input type="text" id="firstName" name="firstName" class="form-input" required data-testid="input-first-name">
                            </div>
                            <div class="form-group">
                                <label for="lastName" class="form-label">Last Name *</label>
                                <input type="text" id="lastName" name="lastName" class="form-input" required data-testid="input-last-name">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-input" required data-testid="input-email">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" class="form-input" required data-testid="input-phone">
                        </div>
                        
                        <div class="form-group">
                            <label for="service" class="form-label">Service Interested In *</label>
                            <select id="service" name="service" class="form-select" required data-testid="select-service">
                                <option value="">Select a service</option>
                                <option value="debt-review-cancellation">Debt Review Cancellation</option>
                                <option value="admin-order-cancellation">Admin Order Cancellation</option>
                                <option value="prescribed-debt-removal">Prescribed Debt Removal</option>
                                <option value="judgement-removal">Judgement Removal</option>
                                <option value="trace-alert-removal">Trace Alert Removal</option>
                                <option value="garnishee-order-removal">Garnishee Order Removal</option>
                                <option value="general-consultation">General Consultation</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message" class="form-label">Message *</label>
                            <textarea id="message" name="message" class="form-textarea" rows="5" 
                                placeholder="Please describe your situation and how we can help..." 
                                required data-testid="textarea-message"></textarea>
                        </div>
                        
                        <!-- CSRF Protection -->
                        <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                        
                        <!-- Honeypot for spam protection -->
                        <div style="position: absolute; left: -9999px;">
                            <label for="website">Website (do not fill):</label>
                            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem 2rem; font-size: 1.125rem;" data-testid="button-send-message">
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    <div id="contact-info-card" style="background-color: rgba(75, 38, 97, 0.05); padding: 2rem; border-radius: var(--border-radius);">
                        <h3 data-testid="contact-info-heading">Contact Information</h3>
                        
                        <div class="contact-item">
                            <div class="icon-box">
                                <i class="fas fa-map-marker-alt" style="color: #e74c3c; font-size: 1rem;"></i>
                            </div>
                            <p data-testid="contact-address">417 Anton Lembede, 9th floor.<br>912 Sangro House<br>Durban, Kwazulu Natal<br>4001</p>
                        </div>
                        
                        <div class="contact-item">
                            <div class="icon-box">
                                <i class="fas fa-phone" style="color: #27ae60; font-size: 1rem;"></i>
                            </div>
                            <p data-testid="contact-phone"><a href="tel:+27718022762" style="color: inherit; text-decoration: none;">071 802 2762</a></p>
                        </div>
                        
                        <div class="contact-item">
                            <div class="icon-box">
                                <i class="fab fa-whatsapp" style="color: #25D366; font-size: 1rem;"></i>
                            </div>
                            <p data-testid="contact-whatsapp"><a href="https://wa.me/27718022762" target="_blank" style="color: inherit; text-decoration: none;">071 802 2762</a></p>
                        </div>
                        
                        <div class="contact-item">
                            <div class="icon-box">
                                <i class="fas fa-envelope" style="color: #3498db; font-size: 1rem;"></i>
                            </div>
                            <p data-testid="contact-email">admin@ndcl.co.za</p>
                        </div>
                        
                        <div class="contact-item">
                            <div class="icon-box">
                                <i class="fas fa-globe" style="color: #17a2b8; font-size: 1rem;"></i>
                            </div>
                            <p data-testid="contact-website">www.ndcl.co.za</p>
                        </div>
                        
                        <div class="contact-item">
                            <div class="icon-box">
                                <i class="fas fa-clock" style="color: #f39c12; font-size: 1rem;"></i>
                            </div>
                            <div>
                                <h4>Business Hours</h4>
                                <p data-testid="business-hours-weekday" style="margin: 0; margin-bottom: 0.25rem;">Monday - Friday: 8:00 AM - 5:00 PM</p>
                                <p data-testid="business-hours-saturday" style="margin: 0;">Saturday: 9:00 AM - 1:00 PM</p>
                            </div>
                        </div>
                    </div>

                    <div style="background: linear-gradient(135deg, rgba(75, 38, 97, 0.1) 0%, rgba(117, 56, 125, 0.1) 100%); padding: 2rem; border-radius: var(--border-radius);">
                        <img src="/images/bg3_1757794796235.jpg" 
                             alt="Professional consultation representing our supportive approach" 
                             class="hero-image" style="margin-bottom: 1.5rem;"
                             data-testid="contact-support-image"
                             width="400" height="300" loading="lazy">
                        <h4 style="margin-bottom: 1rem;">Why Choose NOVA?</h4>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="display: flex; align-items: center; margin-bottom: 0.75rem;" data-testid="benefit-0">
                                <i class="fas fa-check" style="color: #27ae60; margin-right: 0.75rem; font-size: 0.9rem;"></i>
                                Personalized debt solutions
                            </li>
                            <li style="display: flex; align-items: center; margin-bottom: 0.75rem;" data-testid="benefit-1">
                                <i class="fas fa-check" style="color: #27ae60; margin-right: 0.75rem; font-size: 0.9rem;"></i>
                                Experienced professional team
                            </li>
                            <li style="display: flex; align-items: center; margin-bottom: 0.75rem;" data-testid="benefit-2">
                                <i class="fas fa-check" style="color: #27ae60; margin-right: 0.75rem; font-size: 0.9rem;"></i>
                                Confidential consultations
                            </li>
                            <li style="display: flex; align-items: center;" data-testid="benefit-3">
                                <i class="fas fa-check" style="color: #27ae60; margin-right: 0.75rem; font-size: 0.9rem;"></i>
                                Dignity-preserving approach
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section-padding bg-muted" data-testid="faq-section">
        <div class="container" style="max-width: 1000px;">
            <div class="text-center">
                <h2 data-testid="faq-heading">Frequently Asked Questions</h2>
                <p>Get answers to common questions about our debt counselling services.</p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 2rem; margin-top: 3rem;">
                <div class="card">
                    <h3 style="margin-bottom: 0.75rem;">How long does the debt review cancellation process take?</h3>
                    <p style="margin: 0;">The timeline varies depending on your specific circumstances, but typically takes 4-8 weeks. We handle all paperwork and communication with credit bureaus to ensure the process is completed as quickly as possible.</p>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 0.75rem;">Is the initial consultation really free?</h3>
                    <p style="margin: 0;">Yes, absolutely. We provide a completely free, confidential consultation to assess your situation and explain your options. There's no obligation to proceed with our services.</p>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 0.75rem;">Will my information remain confidential?</h3>
                    <p style="margin: 0;">Your privacy is our priority. All information shared with us is kept strictly confidential and is only used for the purposes of providing our debt counselling services to you.</p>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 0.75rem;">What documents do I need to provide?</h3>
                    <p style="margin: 0;">The required documents depend on your specific service needs. During your consultation, we'll provide you with a detailed list of any documentation required for your particular situation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section-padding bg-primary" data-testid="contact-cta-section">
        <div class="container text-center">
            <h2 data-testid="contact-cta-heading">Don't Wait - Take Action Today</h2>
            <p style="font-size: 1.25rem; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                Every day you wait is another day of unnecessary financial stress. Contact us now and take the first step towards financial freedom.
            </p>
            <p style="font-size: 1.125rem; margin-bottom: 1.5rem; max-width: 700px; margin-left: auto; margin-right: auto;">
                Call Nova Debt Counselling for a Free Consultation on <strong>031 305 0289</strong>
            </p>
            <p style="font-size: 1.125rem; color: var(--nova-accent); font-weight: 600;" data-testid="response-time">
                We respond to all inquiries within 24 hours
            </p>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>