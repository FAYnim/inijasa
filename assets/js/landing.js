// Jasaku Landing Page - Minimal JavaScript for Enhanced UX

document.addEventListener('DOMContentLoaded', function() {
    
    // ====================================
    // Smooth Scroll for Anchor Links
    // ====================================
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            
            // Skip if href is just "#"
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                e.preventDefault();
                
                // Get navbar height for offset
                const navbarHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = targetElement.offsetTop - navbarHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                // Close mobile menu if open
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    navbarCollapse.classList.remove('show');
                }
            }
        });
    });
    
    // ====================================
    // Navbar Scroll Effect
    // ====================================
    const navbar = document.querySelector('.navbar');
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        // Add shadow on scroll
        if (currentScroll > 50) {
            navbar.style.boxShadow = '0 2px 12px rgba(10, 35, 66, 0.1)';
        } else {
            navbar.style.boxShadow = 'none';
        }
        
        lastScroll = currentScroll;
    });
    
    // ====================================
    // Scroll Animation Observer
    // ====================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.animation = entry.target.getAttribute('data-animate') === 'fade-left' 
                    ? 'fadeInLeft 0.8s ease forwards' 
                    : 'fadeInUp 0.6s ease forwards';
                
                // Apply delay if specified
                const delay = entry.target.getAttribute('data-delay');
                if (delay) {
                    entry.target.style.animationDelay = `${parseInt(delay) / 1000}s`;
                }
                
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe all animated elements
    const animatedElements = document.querySelectorAll('[data-animate]');
    animatedElements.forEach(element => {
        observer.observe(element);
    });
    
    // ====================================
    // Performance: Lazy Loading Images (if needed in future)
    // ====================================
    if ('loading' in HTMLImageElement.prototype) {
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            img.src = img.dataset.src;
        });
    }
    
});

// ====================================
// Optional: Track CTA Clicks (for analytics)
// ====================================
function trackCTAClick(ctaName) {
    // Placeholder for analytics integration
    console.log(`CTA Clicked: ${ctaName}`);
    
    // Example: Google Analytics integration
    // if (typeof gtag !== 'undefined') {
    //     gtag('event', 'click', {
    //         'event_category': 'CTA',
    //         'event_label': ctaName
    //     });
    // }
}

// Add click tracking to CTA buttons
document.querySelectorAll('[href="/auth/register.php"]').forEach(button => {
    button.addEventListener('click', function() {
        trackCTAClick('Register CTA');
    });
});
