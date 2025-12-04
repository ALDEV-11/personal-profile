/**
 * Personal Profile Website - Main JavaScript
 * Interactive features, animations, and form validation
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // NAVBAR FUNCTIONALITY
    // ========================================
    
    const navbar = document.getElementById('navbar');
    const navbarToggle = document.getElementById('navbarToggle');
    const navbarMenu = document.getElementById('navbarMenu');
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Sticky navbar on scroll
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Mobile menu toggle
    if (navbarToggle) {
        navbarToggle.addEventListener('click', function() {
            navbarMenu.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
    
    // Close mobile menu when clicking on a link
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navbarMenu.classList.remove('active');
            navbarToggle.classList.remove('active');
        });
    });
    
    // Active nav link on scroll
    const sections = document.querySelectorAll('section[id]');
    
    window.addEventListener('scroll', function() {
        const scrollY = window.pageYOffset;
        
        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 100;
            const sectionId = section.getAttribute('id');
            
            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${sectionId}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    });
    
    // ========================================
    // SMOOTH SCROLLING
    // ========================================
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            
            if (target) {
                const targetPosition = target.offsetTop - 70;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // ========================================
    // SCROLL ANIMATIONS (DISABLED)
    // ========================================
    
    const animateElements = document.querySelectorAll('[data-animate]');
    
    // Langsung tambahkan class 'animated' tanpa observer
    animateElements.forEach(el => {
        el.classList.add('animated');
    });
    
    // Observer dinonaktifkan
    /*
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = entry.target.dataset.delay || 0;
                setTimeout(() => {
                    entry.target.classList.add('animated');
                }, delay);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    animateElements.forEach(el => observer.observe(el));
    */
    
    // ========================================
    // SKILLS SECTION (Using Badge Levels)
    // ========================================
    // Progress bars removed, now using text-based level badges
    // (Beginner, Intermediate, Advanced)
    
    // ========================================
    // PROJECT FILTER
    // ========================================
    
    const filterButtons = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter projects
            projectCards.forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    }, 10);
                } else if (filter === 'featured' && card.classList.contains('featured')) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    }, 10);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
    
    // ========================================
    // BACK TO TOP BUTTON
    // ========================================
    
    const backToTopBtn = document.getElementById('backToTop');
    
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // ========================================
    // CONTACT FORM VALIDATION
    // ========================================
    
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const subject = document.getElementById('subject');
            const message = document.getElementById('message');
            
            let isValid = true;
            
            // Remove previous error messages
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            
            // Validate name
            if (name.value.trim() === '') {
                showError(name, 'Nama harus diisi');
                isValid = false;
            }
            
            // Validate email
            if (email.value.trim() === '') {
                showError(email, 'Email harus diisi');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                showError(email, 'Email tidak valid');
                isValid = false;
            }
            
            // Validate subject
            if (subject.value.trim() === '') {
                showError(subject, 'Subject harus diisi');
                isValid = false;
            }
            
            // Validate message
            if (message.value.trim() === '') {
                showError(message, 'Pesan harus diisi');
                isValid = false;
            } else if (message.value.trim().length < 10) {
                showError(message, 'Pesan minimal 10 karakter');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        const formInputs = contactForm.querySelectorAll('input, textarea');
        formInputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                const errorMsg = this.parentElement.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                    this.style.borderColor = '';
                }
            });
        });
    }
    
    // Helper function to show error
    function showError(input, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#EF4444';
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.style.marginTop = '0.5rem';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i> ${message}`;
        
        input.style.borderColor = '#EF4444';
        input.parentElement.appendChild(errorDiv);
    }
    
    // Helper function to validate email
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    // Helper function to validate single field
    function validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        
        // Remove existing error
        const existingError = field.parentElement.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        field.style.borderColor = '';
        
        // Validate based on field
        if (value === '') {
            showError(field, `${field.previousElementSibling.textContent.replace('*', '').trim()} harus diisi`);
        } else if (fieldName === 'email' && !isValidEmail(value)) {
            showError(field, 'Email tidak valid');
        } else if (fieldName === 'message' && value.length < 10) {
            showError(field, 'Pesan minimal 10 karakter');
        }
    }
    
    // ========================================
    // TYPING EFFECT (Optional Enhancement)
    // ========================================
    
    const heroTagline = document.querySelector('.hero-tagline');
    if (heroTagline) {
        const text = heroTagline.textContent;
        heroTagline.textContent = '';
        let i = 0;
        
        function typeWriter() {
            if (i < text.length) {
                heroTagline.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 50);
            }
        }
        
        // Start typing after page load
        setTimeout(typeWriter, 500);
    }
    
    // ========================================
    // LAZY LOADING IMAGES
    // ========================================
    
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    
    // ========================================
    // PARALLAX EFFECT ON SCROLL
    // ========================================
    
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const heroImage = document.querySelector('.hero-image');
        
        if (heroImage) {
            heroImage.style.transform = `translateY(${scrolled * 0.3}px)`;
        }
    });
    
    // ========================================
    // COPY EMAIL TO CLIPBOARD
    // ========================================
    
    const emailLinks = document.querySelectorAll('a[href^="mailto:"]');
    
    emailLinks.forEach(link => {
        link.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            const email = this.getAttribute('href').replace('mailto:', '');
            
            navigator.clipboard.writeText(email).then(() => {
                // Show tooltip
                const tooltip = document.createElement('div');
                tooltip.className = 'copy-tooltip';
                tooltip.textContent = 'Email disalin!';
                tooltip.style.cssText = `
                    position: fixed;
                    background: #10B981;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 6px;
                    font-size: 14px;
                    z-index: 9999;
                    left: ${e.pageX}px;
                    top: ${e.pageY}px;
                    pointer-events: none;
                    animation: fadeIn 0.3s ease;
                `;
                
                document.body.appendChild(tooltip);
                
                setTimeout(() => {
                    tooltip.style.opacity = '0';
                    setTimeout(() => tooltip.remove(), 300);
                }, 2000);
            });
        });
    });
    
    // ========================================
    // CONSOLE MESSAGE
    // ========================================
    
    console.log(
        '%c👋 Hello Developer!',
        'color: #4F46E5; font-size: 20px; font-weight: bold;'
    );
    console.log(
        '%cThanks for checking out my portfolio!',
        'color: #6B7280; font-size: 14px;'
    );
    console.log(
        '%cBuilt with ❤️ using PHP Native, MySQL, and Vanilla JavaScript',
        'color: #10B981; font-size: 12px;'
    );
    
    // ========================================
    // PERFORMANCE OPTIMIZATION
    // ========================================
    
    // Debounce function for scroll events
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Use debounced scroll handler
    const debouncedScrollHandler = debounce(function() {
        // Your scroll-intensive code here
    }, 100);
    
    window.addEventListener('scroll', debouncedScrollHandler);
    
});

// ========================================
// PAGE LOAD ANIMATION
// ========================================

window.addEventListener('load', function() {
    // Hide loader if exists
    const loader = document.getElementById('loader');
    if (loader) {
        loader.style.opacity = '0';
        setTimeout(() => {
            loader.style.display = 'none';
        }, 300);
    }
    
    // Add loaded class to body
    document.body.classList.add('loaded');
});

// ========================================
// PREVENT CONSOLE ERRORS ON MISSING ELEMENTS
// ========================================

// Safely query elements
function safeQuerySelector(selector) {
    try {
        return document.querySelector(selector);
    } catch (e) {
        return null;
    }
}

function safeQuerySelectorAll(selector) {
    try {
        return document.querySelectorAll(selector);
    } catch (e) {
        return [];
    }
}
