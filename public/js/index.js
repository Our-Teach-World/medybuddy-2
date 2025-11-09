// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function () {
    // Initialize all components
    initNavbar();
    initHeroSwiper();
    initCategoriesSwiper();
    initScrollAnimations();
    initNewsletterForm();
});

// Navbar functionality
function initNavbar() {
    const navbar = document.getElementById('navbar');
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    const navLinks = document.querySelectorAll('.nav-link');

    // Hamburger menu toggle
    hamburger.addEventListener('click', function () {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    // Close mobile menu when clicking on a link
    navLinks.forEach(link => {
        link.addEventListener('click', function () {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        });
    });

    // Navbar scroll effect
    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Smooth scrolling for navigation links
    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            if (href.startsWith('#')) {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    const offsetTop = targetElement.offsetTop - 70; // Account for fixed navbar
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
}

// Hero Swiper initialization
function initHeroSwiper() {
    const heroSwiper = new Swiper('.hero-swiper', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        speed: 1000,
        on: {
            slideChange: function () {
                // Add animation classes to slide content
                const activeSlide = this.slides[this.activeIndex];
                const slideText = activeSlide.querySelector('.slide-text');
                const slideImage = activeSlide.querySelector('.slide-image');

                if (slideText) {
                    slideText.style.animation = 'none';
                    slideText.offsetHeight; // Trigger reflow
                    slideText.style.animation = 'slideInLeft 1s ease-out';
                }

                if (slideImage) {
                    slideImage.style.animation = 'none';
                    slideImage.offsetHeight; // Trigger reflow
                    slideImage.style.animation = 'slideInRight 1s ease-out';
                }
            }
        }
    });

    // Autoplay toggle functionality
    const autoplayToggle = document.getElementById('autoplay-toggle');
    let isPlaying = true;

    autoplayToggle.addEventListener('click', function () {
        if (isPlaying) {
            heroSwiper.autoplay.stop();
            this.innerHTML = '<i class="fas fa-play"></i>';
            isPlaying = false;
        } else {
            heroSwiper.autoplay.start();
            this.innerHTML = '<i class="fas fa-pause"></i>';
            isPlaying = true;
        }
    });

    // Pause autoplay on hover
    const heroSection = document.querySelector('.hero-swiper');
    heroSection.addEventListener('mouseenter', function () {
        heroSwiper.autoplay.stop();
    });

    heroSection.addEventListener('mouseleave', function () {
        if (isPlaying) {
            heroSwiper.autoplay.start();
        }
    });
}

// Categories Swiper initialization
function initCategoriesSwiper() {
    const categoriesSwiper = new Swiper('.categories-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        navigation: {
            nextEl: '.categories-next',
            prevEl: '.categories-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 30,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 30,
            },
        },
        loop: false,
        grabCursor: true,
        keyboard: {
            enabled: true,
        },
        a11y: {
            prevSlideMessage: 'Previous category',
            nextSlideMessage: 'Next category',
        }
    });

    // Add click handlers for category cards
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('click', function () {
            const categoryName = this.querySelector('h3').textContent;
            console.log(`Clicked on ${categoryName} category`);
            // Here you would typically navigate to the category page or filter doctors
        });

        // Add keyboard support
        card.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });

        // Make cards focusable
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
    });
}

// Scroll animations
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const animateElements = document.querySelectorAll('.category-card, .doctor-card, .stat-item');
    animateElements.forEach(el => {
        observer.observe(el);
    });

    // Add CSS for animations
    const style = document.createElement('style');
    style.textContent = `
        .category-card, .doctor-card, .stat-item {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        
        .animate-in {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    `;
    document.head.appendChild(style);
}

// Newsletter form functionality
function initNewsletterForm() {
    const newsletterForm = document.querySelector('.newsletter-form');

    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const emailInput = this.querySelector('input[type="email"]');
            const submitButton = this.querySelector('button');
            const email = emailInput.value.trim();

            if (!email) {
                showNotification('Please enter a valid email address', 'error');
                return;
            }

            // Disable button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';

            // Simulate API call
            setTimeout(() => {
                showNotification('Thank you for subscribing to our newsletter!', 'success');
                emailInput.value = '';
                submitButton.disabled = false;
                submitButton.innerHTML = 'Subscribe';
            }, 2000);
        });
    }
}

// Doctor card interactions
document.addEventListener('DOMContentLoaded', function () {
    const doctorCards = document.querySelectorAll('.doctor-card');

    doctorCards.forEach(card => {
        const bookButton = card.querySelector('.btn-primary');
        const viewButton = card.querySelector('.btn-secondary');

        if (bookButton) {
            bookButton.addEventListener('click', function () {
                const doctorName = card.querySelector('h3').textContent;
                showNotification(`Booking appointment with ${doctorName}...`, 'info');
                // Here you would typically open a booking modal or navigate to booking page
            });
        }

        if (viewButton) {
            viewButton.addEventListener('click', function () {
                const doctorName = card.querySelector('h3').textContent;
                showNotification(`Viewing schedule for ${doctorName}...`, 'info');
                // Here you would typically show the doctor's schedule
            });
        }
    });
});

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;

    // Add styles
    const notificationStyles = `
        .notification {
            position: fixed;
            top: 90px;
            right: 20px;
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            max-width: 400px;
        }
        
        .notification-success {
            border-left: 4px solid #28a745;
        }
        
        .notification-error {
            border-left: 4px solid #dc3545;
        }
        
        .notification-info {
            border-left: 4px solid #667eea;
        }
        
        .notification-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notification-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            margin-left: 1rem;
            color: #666;
        }
        
        .notification-close:hover {
            color: #333;
        }
    `;

    // Add styles if not already added
    if (!document.querySelector('#notification-styles')) {
        const styleSheet = document.createElement('style');
        styleSheet.id = 'notification-styles';
        styleSheet.textContent = notificationStyles;
        document.head.appendChild(styleSheet);
    }

    // Add to DOM
    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    // Close button functionality
    const closeButton = notification.querySelector('.notification-close');
    closeButton.addEventListener('click', function () {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => notification.remove(), 300);
    });

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Keyboard navigation support
document.addEventListener('keydown', function (e) {
    // ESC key to close mobile menu
    if (e.key === 'Escape') {
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('nav-menu');

        if (navMenu.classList.contains('active')) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        }
    }
});

// Performance optimization: Lazy loading for images
function initLazyLoading() {
    const images = document.querySelectorAll('img[src*="unsplash"]');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                // Here you would typically load the actual image
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

// Initialize lazy loading
document.addEventListener('DOMContentLoaded', initLazyLoading);

// Accessibility improvements
function initAccessibility() {
    // Add skip link
    const skipLink = document.createElement('a');
    skipLink.href = '#main-content';
    skipLink.textContent = 'Skip to main content';
    skipLink.className = 'skip-link';
    skipLink.style.cssText = `
        position: absolute;
        top: -40px;
        left: 6px;
        background: #667eea;
        color: white;
        padding: 8px;
        text-decoration: none;
        border-radius: 4px;
        z-index: 10001;
        transition: top 0.3s;
    `;

    skipLink.addEventListener('focus', function () {
        this.style.top = '6px';
    });

    skipLink.addEventListener('blur', function () {
        this.style.top = '-40px';
    });

    document.body.insertBefore(skipLink, document.body.firstChild);

    // Add main content landmark
    const heroSection = document.getElementById('home');
    if (heroSection) {
        heroSection.setAttribute('id', 'main-content');
        heroSection.setAttribute('role', 'main');
    }
}

// Initialize accessibility features
document.addEventListener('DOMContentLoaded', initAccessibility);

// Error handling for Swiper initialization
window.addEventListener('error', function (e) {
    if (e.message.includes('Swiper')) {
        console.warn('Swiper failed to load. Falling back to basic functionality.');
        // Implement fallback carousel functionality here if needed
    }
});

// Service Worker registration for PWA capabilities (optional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js')
            .then(function (registration) {
                console.log('ServiceWorker registration successful');
            })
            .catch(function (err) {
                console.log('ServiceWorker registration failed');
            });
    });
}