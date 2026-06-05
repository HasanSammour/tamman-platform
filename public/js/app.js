// public/js/app.js - Shared JavaScript for all Tamman Platform pages

/* ========================================
   TABLE OF CONTENTS:
   1. DOM Ready Initialization
   2. Mobile Navigation Toggle
   3. Scroll Effects (Navbar, Back to Top)
   4. Scroll Reveal Animations
   5. Counter Animation for Statistics
   6. Form Validation Helpers
   7. AJAX Setup (CSRF Token)
   8. Alert Auto-dismiss
   9. Password Toggle
   10. Smooth Scroll
   11. Theme/Language Switcher (Optional)
   12. Notification System
   13. Tooltips
   14. Loading States
   ======================================== */

// ========== 1. DOM READY INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function () {
    console.log('App.js loaded');
    initMobileNav();
    initScrollEffects();
    initScrollReveal();
    initCounters();
    initAlertDismiss();
    initPasswordToggle();
    initSmoothScroll();
    initTooltips();
    initUserDropdown();
    initNotificationsDropdown();
    checkRTL();
});

// ========== 2. MOBILE NAVIGATION TOGGLE ==========
function initMobileNav() {
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');

    if (navbarToggle && navbarMenu) {
        navbarToggle.addEventListener('click', function () {
            navbarMenu.classList.toggle('active');
            const icon = navbarToggle.querySelector('i');
            if (icon) {
                if (navbarMenu.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });

        const navLinks = navbarMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function () {
                navbarMenu.classList.remove('active');
                const icon = navbarToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });
    }
}

// ========== 3. SCROLL EFFECTS ==========
function initScrollEffects() {
    const navbar = document.querySelector('.navbar');
    const backToTopBtn = document.querySelector('.back-to-top');

    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 100) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    }

    if (backToTopBtn) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });

        backToTopBtn.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
}

// ========== 4. SCROLL REVEAL ==========
function initScrollReveal() {
    const revealElements = document.querySelectorAll('.reveal');

    if (revealElements.length > 0) {
        const revealOnScroll = function () {
            for (let i = 0; i < revealElements.length; i++) {
                const windowHeight = window.innerHeight;
                const elementTop = revealElements[i].getBoundingClientRect().top;
                const elementVisible = 150;

                if (elementTop < windowHeight - elementVisible) {
                    revealElements[i].classList.add('active');
                }
            }
        };

        window.addEventListener('scroll', revealOnScroll);
        revealOnScroll();
    }
}

// ========== 5. COUNTER ANIMATION ==========
function initCounters() {
    const counters = document.querySelectorAll('.counter');

    if (counters.length > 0) {
        const animateCounter = (counter) => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = parseInt(counter.getAttribute('data-duration')) || 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.innerText = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.innerText = target;
                }
            };
            updateCounter();
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => observer.observe(counter));
    }
}

// ========== 6. FORM VALIDATION ==========
function validateEmail(email) {
    const re = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,6}[-\s\.]?[0-9]{1,6}$/;
    return re.test(phone);
}

function showFieldError(field, message) {
    const formGroup = field.closest('.form-group');
    if (formGroup) {
        const existingError = formGroup.querySelector('.invalid-feedback');
        if (existingError) existingError.remove();
        field.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.innerText = message;
        formGroup.appendChild(errorDiv);
    }
}

function clearFieldError(field) {
    const formGroup = field.closest('.form-group');
    if (formGroup) {
        const existingError = formGroup.querySelector('.invalid-feedback');
        if (existingError) existingError.remove();
        field.classList.remove('is-invalid');
    }
}

// ========== 7. AJAX SETUP ==========
function setupAJAX() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    if (csrfToken) {
        window.fetchWithCSRF = function (url, options = {}) {
            options.headers = {
                ...options.headers,
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
            return fetch(url, options);
        };

        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        }
    }
}
document.addEventListener('DOMContentLoaded', setupAJAX);

// ========== 8. ALERT AUTO-DISMISS ==========
function initAlertDismiss() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');

    alerts.forEach(alert => {
        const dismissAfter = parseInt(alert.getAttribute('data-dismiss')) || 5000;

        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) alert.remove();
            }, 300);
        }, dismissAfter);

        if (!alert.querySelector('.alert-close')) {
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '&times;';
            closeBtn.className = 'alert-close';
            closeBtn.style.cssText = `position: absolute; top: 0.5rem; right: 0.5rem; background: none; border: none; font-size: 1.25rem; cursor: pointer; color: inherit; opacity: 0.5;`;
            closeBtn.addEventListener('click', () => alert.remove());
            alert.style.position = 'relative';
            alert.appendChild(closeBtn);
        }
    });
}

// ========== 9. PASSWORD TOGGLE ==========
function initPasswordToggle() {
    const toggleButtons = document.querySelectorAll('.password-toggle');

    toggleButtons.forEach(button => {
        button.removeEventListener('click', button._toggleHandler);

        const handler = function (e) {
            e.preventDefault();
            e.stopPropagation();
            const wrapper = this.closest('.input-wrapper');
            if (wrapper) {
                const passwordInput = wrapper.querySelector('input');
                if (passwordInput) {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    } else {
                        passwordInput.type = 'password';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    }
                }
            }
        };

        button.addEventListener('click', handler);
        button._toggleHandler = handler;
    });
}

// ========== 10. SMOOTH SCROLL ==========
function initSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]:not([href="#"])');

    links.forEach(link => {
        link.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                if (history.pushState) history.pushState(null, null, targetId);
            }
        });
    });
}

// ========== 11. RTL CHECK ==========
function checkRTL() {
    const htmlElement = document.documentElement;
    const dir = htmlElement.getAttribute('dir');
    const bodyElement = document.body;
    if (dir === 'rtl' || htmlElement.lang === 'ar') {
        bodyElement.classList.add('rtl');
    }
}

// ========== 12. NOTIFICATION SYSTEM ==========
class NotificationSystem {
    constructor() {
        this.container = null;
        this.createContainer();
    }

    createContainer() {
        if (!document.querySelector('.notification-container')) {
            const container = document.createElement('div');
            container.className = 'notification-container';
            container.style.cssText = `position: fixed; top: 80px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; max-width: 350px;`;
            document.body.appendChild(container);
            this.container = container;
        } else {
            this.container = document.querySelector('.notification-container');
        }
    }

    show(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `background: white; border-radius: 8px; padding: 12px 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 12px; animation: slideInRight 0.3s ease; border-left: 4px solid ${this.getBorderColor(type)};`;

        const icon = this.getIcon(type);
        notification.innerHTML = `<i class="${icon}" style="color: ${this.getBorderColor(type)}"></i><span style="flex:1; font-size:14px;">${message}</span><button class="notification-close" style="background:none; border:none; cursor:pointer; color:#999;">&times;</button>`;

        this.container.appendChild(notification);
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => notification.remove());

        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, duration);
    }

    getBorderColor(type) {
        switch (type) {
            case 'success': return '#10b981';
            case 'error': return '#ef4444';
            case 'warning': return '#f59e0b';
            default: return '#8b5cf6';
        }
    }

    getIcon(type) {
        switch (type) {
            case 'success': return 'fas fa-check-circle';
            case 'error': return 'fas fa-exclamation-circle';
            case 'warning': return 'fas fa-exclamation-triangle';
            default: return 'fas fa-info-circle';
        }
    }
}

window.notifications = new NotificationSystem();

// ========== 13. TOOLTIPS ==========
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');

    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function (e) {
            const message = this.getAttribute('data-tooltip');
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.innerText = message;
            tooltip.style.cssText = `position: absolute; background: #333; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; white-space: nowrap; z-index: 10000; pointer-events: none;`;
            const rect = this.getBoundingClientRect();
            tooltip.style.top = `${rect.top - 30 + window.scrollY}px`;
            tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;
            document.body.appendChild(tooltip);
            this.addEventListener('mouseleave', function () { tooltip.remove(); }, { once: true });
        });
    });
}

// ========== 14. LOADING STATES ==========
function showLoading(button) {
    const originalText = button.innerHTML;
    button.classList.add('loading');
    button.disabled = true;
    button.setAttribute('data-original-text', originalText);
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
}

function hideLoading(button) {
    button.classList.remove('loading');
    button.disabled = false;
    const originalText = button.getAttribute('data-original-text');
    if (originalText) button.innerHTML = originalText;
}

window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.validateEmail = validateEmail;
window.validatePhone = validatePhone;
window.showFieldError = showFieldError;
window.clearFieldError = clearFieldError;

// ========== USER DROPDOWN ==========
function initUserDropdown() {
    const userMenuToggle = document.getElementById('topbarUserMenuToggle');
    const userDropdown = document.getElementById('topbarUserDropdown');

    if (userMenuToggle && userDropdown) {
        userMenuToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function (e) {
            if (!userMenuToggle.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && userDropdown.classList.contains('show')) {
                userDropdown.classList.remove('show');
            }
        });
    }
}

// ========== NOTIFICATIONS DROPDOWN ==========
function initNotificationsDropdown() {
    const notificationsToggle = document.getElementById('notificationsToggle');
    const notificationsDropdown = document.getElementById('notificationsDropdown');

    if (notificationsToggle && notificationsDropdown) {
        notificationsToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            notificationsDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function (e) {
            if (!notificationsToggle.contains(e.target) && !notificationsDropdown.contains(e.target)) {
                notificationsDropdown.classList.remove('show');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && notificationsDropdown.classList.contains('show')) {
                notificationsDropdown.classList.remove('show');
            }
        });
    }
}

// Add animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .back-to-top { position: fixed; bottom: 30px; right: 30px; width: 45px; height: 45px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; opacity: 0; visibility: hidden; transition: all 0.3s ease; z-index: 999; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
    .back-to-top.visible { opacity: 1; visibility: visible; }
    .back-to-top:hover { transform: translateY(-3px); box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4); }
    .btn.loading { position: relative; color: transparent !important; }
    .btn.loading::after { content: ''; position: absolute; width: 1rem; height: 1rem; top: 50%; left: 50%; transform: translate(-50%, -50%); border: 2px solid white; border-radius: 50%; border-top-color: transparent; animation: spin 0.6s linear infinite; }
`;
document.head.appendChild(style);