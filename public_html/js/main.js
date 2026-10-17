/**
 * Nova Debt Counselling - Main JavaScript
 * Handles mobile navigation, form validation, and interactive features
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Navigation Toggle
    const mobileToggle = document.getElementById('mobile-toggle');
    const navMenu = document.getElementById('nav-menu');
    
    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', function() {
            const isExpanded = navMenu.classList.toggle('active');
            mobileToggle.setAttribute('aria-expanded', String(isExpanded));
            
            // Animate hamburger menu
            const hamburgers = mobileToggle.querySelectorAll('.hamburger');
            hamburgers.forEach((line, index) => {
                line.style.transform = navMenu.classList.contains('active') 
                    ? `rotate(${index === 0 ? '45deg' : index === 1 ? '0deg' : '-45deg'}) translate(${index === 1 ? '-10px' : '0'}, ${index === 0 ? '6px' : index === 2 ? '-6px' : '0'})`
                    : 'none';
                line.style.opacity = navMenu.classList.contains('active') && index === 1 ? '0' : '1';
            });
        });
        
        // Close mobile menu when clicking on a link
        const navLinks = navMenu.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                mobileToggle.setAttribute('aria-expanded', 'false');
                const hamburgers = mobileToggle.querySelectorAll('.hamburger');
                hamburgers.forEach(line => {
                    line.style.transform = 'none';
                    line.style.opacity = '1';
                });
            });
        });
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Form validation and submission
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        // Add real-time validation
        const inputs = contactForm.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', validateField);
            input.addEventListener('input', clearError);
        });
        
        contactForm.addEventListener('submit', handleFormSubmit);
    }
    
    // Navbar background on scroll
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
            } else {
                navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
                navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            }
        });
    }
    
    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
            }
        });
    }, observerOptions);
    
    // Observe cards and sections
    document.querySelectorAll('.card, .testimonial-card, .process-step').forEach(el => {
        observer.observe(el);
    });
});

/**
 * Validate individual form field
 */
function validateField(event) {
    const field = event.target;
    const value = field.value.trim();
    const fieldName = field.name;
    
    clearError(field);
    
    let errorMessage = '';
    
    switch (fieldName) {
        case 'firstName':
        case 'lastName':
            if (!value) {
                errorMessage = `${fieldName === 'firstName' ? 'First' : 'Last'} name is required`;
            }
            break;
            
        case 'email':
            if (!value) {
                errorMessage = 'Email address is required';
            } else if (!isValidEmail(value)) {
                errorMessage = 'Please enter a valid email address';
            }
            break;
            
        case 'phone':
            if (!value) {
                errorMessage = 'Phone number is required';
            } else if (!isValidPhone(value)) {
                errorMessage = 'Please enter a valid phone number';
            }
            break;
            
        case 'phoneOptional':
            // Phone is optional, but validate format if provided
            if (value && !isValidPhone(value)) {
                errorMessage = 'Please enter a valid phone number';
            }
            break;
            
        case 'service':
            if (!value) {
                errorMessage = 'Please select a service';
            }
            break;
            
        case 'message':
            if (!value) {
                errorMessage = 'Message is required';
            } else if (value.length < 10) {
                errorMessage = 'Message must be at least 10 characters long';
            }
            break;
    }
    
    if (errorMessage) {
        showFieldError(field, errorMessage);
        return false;
    }
    
    return true;
}

/**
 * Clear error state from field
 */
function clearError(field) {
    if (typeof field === 'object' && field.target) {
        field = field.target;
    }
    
    field.classList.remove('error');
    const errorElement = field.parentNode.querySelector('.form-error');
    if (errorElement) {
        errorElement.remove();
    }
}

/**
 * Show error message for field
 */
function showFieldError(field, message) {
    field.classList.add('error');
    
    // Remove existing error message
    const existingError = field.parentNode.querySelector('.form-error');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message
    const errorElement = document.createElement('div');
    errorElement.className = 'form-error';
    errorElement.textContent = message;
    field.parentNode.appendChild(errorElement);
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone format (basic validation)
 */
function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[\d\s\-\(\)]{10,}$/;
    return phoneRegex.test(phone);
}

/**
 * Handle contact form submission
 */
async function handleFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.textContent;
    
    // Validate all fields
    const fields = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;
    
    fields.forEach(field => {
        if (!validateField({ target: field })) {
            isValid = false;
        }
    });
    
    // Check non-required fields too
    const allFields = form.querySelectorAll('input, textarea, select');
    allFields.forEach(field => {
        if (field.value.trim() && !validateField({ target: field })) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        showAlert('Please correct the errors below.', 'error');
        return;
    }
    
    // Show loading state
    submitButton.disabled = true;
    submitButton.textContent = 'Sending...';
    submitButton.classList.add('loading');
    
    try {
        // Prepare form data
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Submit form
        const response = await fetch('/submit_contact.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            // Success
            showAlert('Thank you for your message! We\'ll get back to you within 24 hours.', 'success');
            form.reset();
            
            // Redirect to thank you page after a delay
            setTimeout(() => {
                window.location.href = '/thank-you.php';
            }, 2000);
        } else {
            // Error from server
            showAlert(result.message || 'There was an error sending your message. Please try again.', 'error');
        }
    } catch (error) {
        showAlert('There was an error sending your message. Please try again or contact us directly.', 'error');
    } finally {
        // Reset button state
        submitButton.disabled = false;
        submitButton.textContent = originalButtonText;
        submitButton.classList.remove('loading');
    }
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    // Insert at top of form or page
    const form = document.getElementById('contact-form');
    if (form) {
        form.insertBefore(alert, form.firstChild);
    } else {
        document.body.insertBefore(alert, document.body.firstChild);
    }
    
    // Auto-remove success messages after 5 seconds
    if (type === 'success') {
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
}

/**
 * Generate CSRF token for form security
 */
function generateCSRFToken() {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}

/**
 * Utility function to get URL parameter
 */
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    const results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

/**
 * Initialize any page-specific functionality
 */
function initPageSpecific() {
    const currentPage = document.body.getAttribute('data-page');
    
    switch (currentPage) {
        case 'contact':
            // Contact page specific initialization
            break;
        case 'services':
            // Services page specific initialization
            break;
        default:
            break;
    }
}
