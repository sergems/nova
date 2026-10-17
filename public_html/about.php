<?php 
require_once '../includes/seo.php';
include '../includes/header.php'; 
?>

<main id="main-content" data-page="about">
    <!-- Hero Section -->
    <section class="hero">
        <div class="container text-center">
            <h1 data-testid="about-hero-title">
                Leading Debt Counselling <span class="accent">in Africa</span>
            </h1>
            <p style="font-size: 1.25rem; margin-bottom: 2rem; max-width: 800px; margin-left: auto; margin-right: auto; color: white;">
                Nova Debt Counselling is a dynamic, focused company with years of experience in diverse financial sectors, combining expertise with a passion for excellent client service.
            </p>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="section-padding" data-testid="mission-vision-section">
        <div class="container">
            <div class="grid grid-2" style="align-items: center; gap: 4rem;">
                <div>
                    <img src="/images/bg7_1757765316803.jpg" 
                         alt="Professional business meeting discussing financial solutions" 
                         class="hero-image"
                         data-testid="mission-vision-image"
                         width="500" height="400" loading="lazy">
                </div>
                <div>
                    <div style="margin-bottom: 3rem;">
                        <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                            <svg class="icon icon-target" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#target"></use></svg>
                            <h2>Our Vision</h2>
                        </div>
                        <p style="font-size: 1.125rem; line-height: 1.7;">
                            To be the leading service providing company in Africa and be considered amongst the best in the World. Our vision is to develop Nova Debt Counselling into a brand renowned for excellent returns, superior service, not only to our clients but debtors as well.
                        </p>
                    </div>

                    <div>
                        <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                            <svg class="icon icon-eye" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem;"><use href="/icons.svg#eye"></use></svg>
                            <h2>Our Mission</h2>
                        </div>
                        <p style="font-size: 1.125rem; line-height: 1.7;">
                            To be the best debt counselling company that will be fair in all collection efforts and perform our duties within the guidelines set out in the code of conduct. We will recover debts for our clients from their debtors whilst leaving your debtors' dignity intact.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="section-padding bg-muted" data-testid="values-section">
        <div class="container">
            <div class="text-center">
                <h2 data-testid="values-heading">Our Core Values</h2>
                <p>These fundamental principles guide every decision we make and every interaction we have with our clients.</p>
            </div>

            <div class="grid grid-4">
                <div class="text-center" data-testid="value-0">
                    <div style="width: 5rem; height: 5rem; background-color: rgba(75, 38, 97, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                        <svg class="icon icon-heart" aria-hidden="true" focusable="false" viewBox="0 0 24 24"><use href="/icons.svg#heart"></use></svg>
                    </div>
                    <h3>Dignity & Respect</h3>
                    <p>We treat every client with dignity and respect, understanding that debt issues are personal and sensitive matters.</p>
                </div>

                <div class="text-center" data-testid="value-1">
                    <div style="width: 5rem; height: 5rem; background-color: rgba(75, 38, 97, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                        <svg class="icon icon-trophy" aria-hidden="true" focusable="false" viewBox="0 0 24 24"><use href="/icons.svg#trophy"></use></svg>
                    </div>
                    <h3>Professional Excellence</h3>
                    <p>Our team maintains the highest standards of professional service and ethical conduct in all client interactions.</p>
                </div>

                <div class="text-center" data-testid="value-2">
                    <div style="width: 5rem; height: 5rem; background-color: rgba(75, 38, 97, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                        <svg class="icon icon-users" aria-hidden="true" focusable="false" viewBox="0 0 24 24"><use href="/icons.svg#users"></use></svg>
                    </div>
                    <h3>Client-Centered Approach</h3>
                    <p>Every solution is tailored to the individual client's needs, circumstances, and financial goals.</p>
                </div>

                <div class="text-center" data-testid="value-3">
                    <div style="width: 5rem; height: 5rem; background-color: rgba(75, 38, 97, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                        <svg class="icon icon-chart-no-axes-combined" aria-hidden="true" focusable="false" viewBox="0 0 24 24"><use href="/icons.svg#chart-no-axes-combined"></use></svg>
                    </div>
                    <h3>Sustainable Results</h3>
                    <p>We focus on long-term financial health and sustainable debt solutions that benefit our clients' futures.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Founder Section -->
    <section class="section-padding" data-testid="founder-section">
        <div class="container">
            <div style="background: linear-gradient(135deg, rgba(75, 38, 97, 0.05) 0%, rgba(117, 56, 125, 0.05) 100%); padding: 3rem; border-radius: var(--border-radius);">
                <div class="grid grid-3" style="align-items: center;">
                    <div>
                        <img src="/images/bg8_1757794796232.jpg" 
                             alt="Qibi Patience - Founder and Director" 
                             class="hero-image" style="max-width: 300px; margin: 0 auto;"
                             data-testid="founder-image"
                             width="300" height="400" loading="lazy">
                    </div>
                    <div style="grid-column: span 2;">
                        <h3 style="font-size: 2.25rem; margin-bottom: 1rem;" data-testid="founder-name">
                            Qibi Patience and Honoré Mpongo
                        </h3>
                        <p style="font-size: 1.25rem; color: var(--nova-primary); font-weight: 600; margin-bottom: 1rem;">Founders & Directors</p>
                        <p style="font-size: 1.125rem; line-height: 1.7; margin-bottom: 1.5rem;">
                            The business is managed by Qibi Patience and Honoré Mpongo, who serve as the founders and directors overseeing the company's development. With extensive experience in the related industry and business management, they bring passion for excellent client service and deep understanding of the financial sector.
                        </p>
                        <p style="margin-bottom: 1.5rem;">
                            Under their leadership, Nova Debt Counselling has grown into a service-focused company with strong management and staff members. As our portfolio develops, so do our capabilities and services, driving our investments to exceptional performance through relationships, collaboration, and stability.
                        </p>
                        <div class="flex" style="flex-wrap: wrap; gap: 1rem;">
                            <div style="background-color: rgba(255, 255, 255, 0.5); padding: 0.75rem 1rem; border-radius: var(--border-radius);">
                                <span style="font-size: 0.875rem; color: var(--muted-foreground);">Experience:</span>
                                <span style="font-weight: 600; margin-left: 0.5rem;">15+ Years</span>
                            </div>
                            <div style="background-color: rgba(255, 255, 255, 0.5); padding: 0.75rem 1rem; border-radius: var(--border-radius);">
                                <span style="font-size: 0.875rem; color: var(--muted-foreground);">Specialization:</span>
                                <span style="font-weight: 600; margin-left: 0.5rem;">Debt Counselling</span>
                            </div>
                            <div style="background-color: rgba(255, 255, 255, 0.5); padding: 0.75rem 1rem; border-radius: var(--border-radius);">
                                <span style="font-size: 0.875rem; color: var(--muted-foreground);">Focus:</span>
                                <span style="font-weight: 600; margin-left: 0.5rem;">Client Success</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What Makes Us Different -->
    <section class="section-padding bg-muted" data-testid="differentiators-section">
        <div class="container">
            <div class="text-center">
                <h2 data-testid="differentiators-heading">What Makes Us Different</h2>
                <p>Our unique approach and specialized expertise set us apart in the debt counselling industry.</p>
            </div>

            <div class="grid grid-2" style="align-items: center; gap: 4rem;">
                <div>
                    <img src="/images/bg5_1757765316803.jpg" 
                         alt="Professional team collaboration representing our expertise" 
                         class="hero-image"
                         data-testid="differentiators-image"
                         width="500" height="400" loading="lazy">
                </div>
                <div>
                    <div>
                        <div style="display: flex; align-items: flex-start; margin-bottom: 1rem;" data-testid="differentiator-0">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem; margin-top: 0.125rem;"><use href="/icons.svg#check"></use></svg>
                            <p style="font-size: 1.125rem;">Optimized counselling strategy and timing of action</p>
                        </div>
                        <div style="display: flex; align-items: flex-start; margin-bottom: 1rem;" data-testid="differentiator-1">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem; margin-top: 0.125rem;"><use href="/icons.svg#check"></use></svg>
                            <p style="font-size: 1.125rem;">Advanced technology and tracing abilities</p>
                        </div>
                        <div style="display: flex; align-items: flex-start; margin-bottom: 1rem;" data-testid="differentiator-2">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem; margin-top: 0.125rem;"><use href="/icons.svg#check"></use></svg>
                            <p style="font-size: 1.125rem;">Qualified and trained call centre staff</p>
                        </div>
                        <div style="display: flex; align-items: flex-start; margin-bottom: 1rem;" data-testid="differentiator-3">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem; margin-top: 0.125rem;"><use href="/icons.svg#check"></use></svg>
                            <p style="font-size: 1.125rem;">Well qualified and experienced management team</p>
                        </div>
                        <div style="display: flex; align-items: flex-start; margin-bottom: 1rem;" data-testid="differentiator-4">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem; margin-top: 0.125rem;"><use href="/icons.svg#check"></use></svg>
                            <p style="font-size: 1.125rem;">Industry-specific training and expertise</p>
                        </div>
                        <div style="display: flex; align-items: flex-start; margin-bottom: 1rem;" data-testid="differentiator-5">
                            <svg class="icon icon-check" aria-hidden="true" focusable="false" viewBox="0 0 24 24" style="margin-right: 0.75rem; margin-top: 0.125rem;"><use href="/icons.svg#check"></use></svg>
                            <p style="font-size: 1.125rem;">Comprehensive end-to-end debt counselling services</p>
                        </div>
                    </div>
                    <div style="margin-top: 2rem;">
                        <p>
                            Although Nova Debt Counselling places its focus primarily on counselling specific personal and business finance, it is the policy of our company to deliver our services to anyone. Our training and experience is industry specific, ensuring the highest quality of service.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Achievements Section -->
    <section class="section-padding bg-primary" data-testid="achievements-section">
        <div class="container">
            <div class="text-center">
                <h2 data-testid="achievements-heading">Our Track Record</h2>
                <p style="color: rgba(255, 255, 255, 0.9);">Numbers that reflect our commitment to excellence and client success.</p>
            </div>

            <div class="grid grid-4">
                <div class="text-center" data-testid="achievement-1">
                    <div style="font-size: 4rem; font-weight: 800; color: var(--nova-accent); margin-bottom: 0.5rem;">
                        15+
                    </div>
                    <div style="color: rgba(255, 255, 255, 0.9); font-size: 1.125rem;">
                        Years Experience
                    </div>
                </div>

                <div class="text-center" data-testid="achievement-2">
                    <div style="font-size: 4rem; font-weight: 800; color: var(--nova-accent); margin-bottom: 0.5rem;">
                        95%
                    </div>
                    <div style="color: rgba(255, 255, 255, 0.9); font-size: 1.125rem;">
                        Success Rate
                    </div>
                </div>

                <div class="text-center" data-testid="achievement-3">
                    <div style="font-size: 4rem; font-weight: 800; color: var(--nova-accent); margin-bottom: 0.5rem;">
                        24/7
                    </div>
                    <div style="color: rgba(255, 255, 255, 0.9); font-size: 1.125rem;">
                        Support Available
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section-padding" data-testid="about-cta-section">
        <div class="container text-center">
            <h2 data-testid="about-cta-heading">Ready to Work with the Leading Debt Counselling Company?</h2>
            <p style="font-size: 1.25rem; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                Experience the NOVA difference. Contact us today to learn how our expertise, dignity-focused approach, and proven track record can help you achieve financial freedom.
            </p>
            <div class="hero-buttons">
                <a href="/contact.php" class="btn btn-primary" data-testid="button-contact-expert">
                    Contact Our Experts
                </a>
                <a href="/services.php" class="btn btn-secondary" data-testid="button-explore-services">
                    Explore Our Services
                </a>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>