<?php 
require_once '../includes/seo.php';
include '../includes/header.php'; 
?>

<main id="main-content" data-page="services">
    <!-- Hero Section -->
    <section class="hero">
        <div class="container text-center">
            <h1 data-testid="services-hero-title">
                Professional <span class="accent">Debt Solutions</span>
            </h1>
            <p style="font-size: 1.25rem; margin-bottom: 2rem; max-width: 800px; margin-left: auto; margin-right: auto; color: white;">
                Comprehensive debt counselling services designed to help you regain financial control and achieve lasting freedom from debt burdens.
            </p>
            <a href="/contact.php" class="btn btn-secondary" data-testid="button-get-help-now">
                Get Help Now
            </a>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="section-padding bg-muted" data-testid="services-grid-section">
        <div class="container">
            <div class="text-center">
                <h2 data-testid="services-grid-heading">Our Comprehensive Services</h2>
                <p>Each service is designed with your dignity and financial recovery in mind, backed by years of expertise and legal knowledge.</p>
            </div>

            <div class="services-grid">
                <div class="card" data-testid="detailed-service-debt-review-cancellation">
                    <div class="card-icon"><svg class="icon icon-clipboard-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24"><use href="/icons.svg#clipboard-check"></use></svg></div>
                    <h3>Debt Review Cancellation</h3>
                    <p>Professional assistance to cancel your debt review when circumstances change, ensuring a smooth transition back to normal credit standing. Our team handles all paperwork and legal requirements.</p>
                    <div style="margin-top: 1.5rem;">
                        <h4 style="margin-bottom: 1rem;">Key Benefits:</h4>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Legal compliance with National Credit Act</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Complete paperwork handling</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Credit record restoration</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Expert legal guidance</span>
                        </div>
                    </div>
                </div>

                <div class="card" data-testid="detailed-service-admin-order-cancellation">
                    <div class="card-icon"><svg class="icon icon-file-text" aria-hidden="true" focusable="false" viewBox="0 0 24 24"><use href="/icons.svg#file-text"></use></svg></div>
                    <h3>Admin Order Cancellation</h3>
                    <p>Expert guidance through the legal process of cancelling administration orders that may no longer serve your best interests. We ensure proper court procedures are followed.</p>
                    <div style="margin-top: 1.5rem;">
                        <h4 style="margin-bottom: 1rem;">Key Benefits:</h4>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Court application preparation</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Legal representation</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Document verification</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Follow-up services</span>
                        </div>
                    </div>
                </div>

                <div class="card" data-testid="detailed-service-prescribed-debt-removal">
                    <div class="card-icon"><svg class="icon icon-eraser" aria-hidden="true" focusable="false" viewBox="0 0 24 24"><use href="/icons.svg#eraser"></use></svg></div>
                    <h3>Prescribed Debt Removal</h3>
                    <p>Legal removal of prescribed debts from your credit record, helping you start fresh with a clean financial slate. We identify and remove time-barred debts effectively.</p>
                    <div style="margin-top: 1.5rem;">
                        <h4 style="margin-bottom: 1rem;">Key Benefits:</h4>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Credit record analysis</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Prescription verification</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Credit bureau liaison</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Permanent removal</span>
                        </div>
                    </div>
                </div>

                <div class="card" data-testid="detailed-service-judgement-removal">
                    <div class="card-icon">⚖️</div>
                    <h3>Judgement Removal</h3>
                    <p>Strategic legal approach to remove court judgements that may be impacting your credit profile and financial opportunities. Our experts handle complex legal procedures.</p>
                    <div style="margin-top: 1.5rem;">
                        <h4 style="margin-bottom: 1rem;">Key Benefits:</h4>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Legal assessment</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Court applications</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Judgment satisfaction</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Credit restoration</span>
                        </div>
                    </div>
                </div>

                <div class="card" data-testid="detailed-service-trace-alert-removal">
                    <div class="card-icon"><svg class="icon icon-search" aria-hidden="true" focusable="false" viewBox="0 0 24 24"><use href="/icons.svg#search"></use></svg></div>
                    <h3>Trace Alert Removal</h3>
                    <p>Professional assistance in removing trace alerts that may be preventing you from accessing credit and financial services. We work directly with credit bureaus.</p>
                    <div style="margin-top: 1.5rem;">
                        <h4 style="margin-bottom: 1rem;">Key Benefits:</h4>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Credit bureau communication</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Documentation verification</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Alert removal process</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Status monitoring</span>
                        </div>
                    </div>
                </div>

                <div class="card" data-testid="detailed-service-garnishee-order-removal">
                    <div class="card-icon"><svg class="icon icon-banknote" aria-hidden="true" focusable="false" viewBox="0 0 24 24"><use href="/icons.svg#banknote"></use></svg></div>
                    <h3>Garnishee Order Removal</h3>
                    <p>Legal representation to remove garnishee orders, helping you regain control of your salary and financial resources. We negotiate with creditors on your behalf.</p>
                    <div style="margin-top: 1.5rem;">
                        <h4 style="margin-bottom: 1rem;">Key Benefits:</h4>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Legal representation</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Creditor negotiation</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Court applications</span>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#check"></use></svg>
                            <span style="font-size: 0.9rem;">Financial planning advice</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="section-padding" data-testid="process-section">
        <div class="container">
            <div class="text-center">
                <h2 data-testid="process-heading">Our Proven Process</h2>
                <p>We follow a structured, transparent approach to ensure the best outcomes for every client.</p>
            </div>

            <div class="process-grid">
                <div class="process-step" data-testid="process-step-0">
                    <div class="process-number">01</div>
                    <h3>Initial Consultation</h3>
                    <p>Free confidential assessment of your financial situation and debt counselling needs.</p>
                </div>

                <div class="process-step" data-testid="process-step-1">
                    <div class="process-number">02</div>
                    <h3>Strategy Development</h3>
                    <p>Customized action plan tailored to your specific circumstances and goals.</p>
                </div>

                <div class="process-step" data-testid="process-step-2">
                    <div class="process-number">03</div>
                    <h3>Legal Process</h3>
                    <p>Professional handling of all legal requirements, documentation, and procedures.</p>
                </div>

                <div class="process-step" data-testid="process-step-3">
                    <div class="process-number">04</div>
                    <h3>Resolution & Follow-up</h3>
                    <p>Successful completion of your case with ongoing support and monitoring.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="section-padding bg-muted" data-testid="why-choose-section">
        <div class="container">
            <div class="grid grid-2" style="align-items: center; gap: 4rem;">
                <div>
                    <img src="/images/bg7_1757794796236.jpg" 
                         alt="Professional consultation representing our supportive approach" 
                         class="hero-image"
                         data-testid="why-choose-image"
                         width="500" height="400" loading="lazy">
                </div>
                <div>
                    <h2 data-testid="why-choose-heading">Why Choose Nova Debt Counselling?</h2>
                    
                    <div style="margin-bottom: 2rem;">
                        <div style="display: flex; align-items: flex-start; margin-bottom: 1.5rem;">
                            <div style="width: 3rem; height: 3rem; background-color: rgba(75, 38, 97, 0.1); border-radius: var(--border-radius); display: flex; align-items: center; justify-content: center; margin-right: 1rem; flex-shrink: 0;">
                                <svg class="icon icon-shield-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24"><use href="/icons.svg#shield-check"></use></svg>
                            </div>
                            <div>
                                <h3 style="margin-bottom: 0.5rem;">Experienced Professionals</h3>
                                <p>Years of industry experience with a deep understanding of debt counselling regulations and procedures.</p>
                            </div>
                        </div>

                        <div style="display: flex; align-items: flex-start; margin-bottom: 1.5rem;">
                            <div style="width: 3rem; height: 3rem; background-color: rgba(75, 38, 97, 0.1); border-radius: var(--border-radius); display: flex; align-items: center; justify-content: center; margin-right: 1rem; flex-shrink: 0;">
                                <svg class="icon icon-users" aria-hidden="true" focusable="false" viewBox="0 0 24 24"><use href="/icons.svg#users"></use></svg>
                            </div>
                            <div>
                                <h3 style="margin-bottom: 0.5rem;">Dignity-Focused Approach</h3>
                                <p>We treat every client with respect and maintain your dignity throughout the entire process.</p>
                            </div>
                        </div>

                        <div style="display: flex; align-items: flex-start; margin-bottom: 1.5rem;">
                            <div style="width: 3rem; height: 3rem; background-color: rgba(75, 38, 97, 0.1); border-radius: var(--border-radius); display: flex; align-items: center; justify-content: center; margin-right: 1rem; flex-shrink: 0;">
                                <svg class="icon icon-clock" aria-hidden="true" focusable="false" viewBox="0 0 24 24"><use href="/icons.svg#clock"></use></svg>
                            </div>
                            <div>
                                <h3 style="margin-bottom: 0.5rem;">Efficient Service</h3>
                                <p>Streamlined processes and advanced technology ensure quick and efficient resolution of your debt issues.</p>
                            </div>
                        </div>

                        <a href="/contact.php" class="btn btn-primary" data-testid="button-start-process">
                            Start Your Process Today
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section-padding bg-primary" data-testid="services-cta-section">
        <div class="container text-center">
            <h2 data-testid="services-cta-heading">Need Help with Your Debt Situation?</h2>
            <p style="font-size: 1.25rem; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                Don't wait any longer. Contact us today for a free, confidential consultation and take the first step towards financial freedom.
            </p>
            <a href="/contact.php" class="btn btn-secondary" data-testid="button-free-consultation">
                Get Free Consultation
            </a>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>