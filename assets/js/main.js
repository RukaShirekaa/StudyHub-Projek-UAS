// ============================================
// STUDYHUB — Main JavaScript v2.0
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    // Theme
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        updateThemeIcon(true);
    }

    // Init interactions
    autoInjectAnimations();
    initScrollReveal();
    initRippleEffect();
    initNumberCounters();
    initModalSystem();

    // Auto-close sidebar on mobile when a menu item is clicked
    const menuItems = document.querySelectorAll('.sidebar-menu .menu-item');
    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                closeMobileSidebar();
            }
        });
    });
});

// --- Theme Toggle ---
function toggleTheme() {
    const isDark = document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateThemeIcon(isDark);
}

function updateThemeIcon(isDark) {
    const btn = document.getElementById('themeToggleBtn');
    if (btn) {
        btn.innerHTML = isDark ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
    }
}

// --- Sidebar Toggle ---
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const overlay = document.getElementById('sidebarOverlay');
    const isMobile = window.innerWidth <= 768;

    if (!sidebar || !mainContent) return;

    if (isMobile) {
        const isOpen = sidebar.classList.toggle('mobile-open');
        if (overlay) {
            if (isOpen) {
                overlay.classList.add('active');
            } else {
                overlay.classList.remove('active');
            }
        }
    } else {
        const isCollapsed = sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        localStorage.setItem('sidebar', isCollapsed ? 'collapsed' : 'expanded');
    }
}

function closeMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.remove('mobile-open');
    if (overlay) overlay.classList.remove('active');
}

// Close sidebar on window resize to desktop
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        closeMobileSidebar();
    }
});

// --- Scroll Reveal (Intersection Observer) ---
function initScrollReveal() {
    const revealElements = document.querySelectorAll('.reveal, .animate-on-scroll');
    if (!revealElements.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    revealElements.forEach(el => observer.observe(el));
}

// --- Ripple Effect on Buttons ---
function initRippleEffect() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-primary, .btn-danger, .btn-success');
        if (!btn) return;

        const ripple = document.createElement('span');
        ripple.classList.add('ripple');
        const rect = btn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
        ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
        btn.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    });
}

// --- Number Counter Animation ---
function initNumberCounters() {
    const counters = document.querySelectorAll('[data-counter]');
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(el => observer.observe(el));
}

function animateCounter(el) {
    const target = parseFloat(el.getAttribute('data-counter'));
    const isFloat = target % 1 !== 0;
    const duration = 800;
    const startTime = performance.now();

    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // ease-out quad
        const eased = 1 - (1 - progress) * (1 - progress);
        const current = eased * target;

        el.textContent = isFloat ? current.toFixed(1) : Math.floor(current);

        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            el.textContent = isFloat ? target.toFixed(1) : target;
        }
    }

    requestAnimationFrame(update);
}

// --- Modal System ---
function initModalSystem() {
    // Close modal on overlay click
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-overlay')) {
            closeModal(e.target.id);
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal-overlay.active');
            openModals.forEach(modal => closeModal(modal.id));
        }
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Legacy modal support (inline style display toggle)
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        if (modal.classList.contains('modal-overlay')) {
            if (modal.classList.contains('active')) {
                closeModal(modalId);
            } else {
                openModal(modalId);
            }
        } else {
            modal.style.display = modal.style.display === 'none' || modal.style.display === '' ? 'block' : 'none';
        }
    }
}

// --- Toast Notifications ---
function showToast(message, type = 'info', duration = 3000) {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
    const colors = { success: 'var(--accent-emerald)', error: 'var(--accent-rose)', info: 'var(--primary)' };

    toast.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}" style="color:${colors[type] || colors.info}; font-size:1.1rem;"></i> ${message}`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// --- Global UI Micro-Animations ---
function autoInjectAnimations() {
    const animatedSelectors = [
        '.card', 
        '.form-group-modern', 
        '.greeting-card', 
        '.content-area table tbody tr', 
        '.stats-grid-4 > div',
        '.course-grid > div'
    ];
    
    let globalDelayIndex = 0;
    
    document.querySelectorAll(animatedSelectors.join(', ')).forEach((el) => {
        if (el.classList.contains('animate-in') || el.classList.contains('reveal')) return;
        
        // If element is far down, use scroll reveal instead
        if (el.getBoundingClientRect().top > window.innerHeight) {
            el.classList.add('reveal');
        } else {
            // Apply immediate stagger animation
            el.style.opacity = '0';
            el.style.animation = `fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards`;
            el.style.animationDelay = `${(globalDelayIndex * 0.08)}s`;
            globalDelayIndex++;
        }
    });

    // Add interactive icon hover effects to cards
    document.querySelectorAll('.card').forEach(card => {
        const icon = card.querySelector('.icon-bg i');
        if (icon) {
            card.addEventListener('mouseenter', () => {
                icon.style.transform = 'scale(1.2) rotate(5deg)';
                icon.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
            });
            card.addEventListener('mouseleave', () => {
                icon.style.transform = 'scale(1) rotate(0deg)';
            });
        }
    });
}
// Modal Handling
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
        document.body.style.overflow = '';
    }
});

// --- Global Delete Confirmation ---
function confirmDelete(url, message) {
    const textEl = document.getElementById('deleteConfirmText');
    const btnEl = document.getElementById('deleteConfirmBtn');
    if (textEl && btnEl) {
        textEl.innerText = message || 'Apakah Anda yakin ingin menghapus ini?';
        btnEl.href = url;
        openModal('deleteConfirmModal');
    }
}
