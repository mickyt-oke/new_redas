import 'bootstrap';
import Chart from 'chart.js/auto';
import '@fortawesome/fontawesome-free/css/all.css';

/* ============================================================
   NIS REDAS — Main Application JS v2.0
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Init by page type ── */
    if (document.body.classList.contains('welcome-page'))  initWelcomePage();
    if (document.body.classList.contains('auth-page'))     initAuthPage();
    if (document.body.classList.contains('redas-dashboard')) initDashboard();

    /* Legacy support */
    if (document.body.classList.contains('modern-welcome-page')) initWelcomePage();

    initGlobal();
});

/* ════════════════════════════════════════
   GLOBAL — runs on every page
═════════════════════════════════════════ */
function initGlobal() {
    /* Bootstrap tooltips */
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el =>
        new bootstrap.Tooltip(el)
    );

    /* Bootstrap popovers */
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el =>
        new bootstrap.Popover(el)
    );

    /* Legacy sidebar toggle (#wrapper.toggled pattern) */
    const wrapper = document.getElementById('wrapper');
    const menuToggle = document.getElementById('menu-toggle');
    if (wrapper && menuToggle) {
        menuToggle.addEventListener('click', () => wrapper.classList.toggle('toggled'));
    }

    /* REDAS sidebar toggle (new layout) */
    const sidebar = document.querySelector('.redas-sidebar');
    const main    = document.querySelector('.redas-main');

    document.querySelectorAll('.topbar-toggle, .sidebar-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar?.classList.toggle('mobile-open');
                document.querySelector('.sidebar-overlay')?.classList.toggle('active');
            } else {
                sidebar?.classList.toggle('collapsed');
                main?.classList.toggle('sidebar-collapsed');
                const icon = btn.querySelector('i');
                if (icon) icon.classList.toggle('fa-bars');
            }
        });
    });

    /* Sidebar overlay click (mobile close) */
    document.querySelector('.sidebar-overlay')?.addEventListener('click', () => {
        sidebar?.classList.remove('mobile-open');
        document.querySelector('.sidebar-overlay')?.classList.remove('active');
    });

    /* Active sidebar link */
    const path = window.location.pathname;
    document.querySelectorAll('.sidebar-link').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href !== '#' && path.endsWith(href)) {
            link.classList.add('active');
        }
    });

    /* Smooth scroll for anchor links */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* Escape key closes mobile sidebar */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            sidebar?.classList.remove('mobile-open');
            document.querySelector('.sidebar-overlay')?.classList.remove('active');
        }
    });

    /* Bootstrap form validation */
    document.querySelectorAll('.needs-validation').forEach(form => {
        form.addEventListener('submit', e => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}

/* ════════════════════════════════════════
   WELCOME PAGE
═════════════════════════════════════════ */
function initWelcomePage() {
    /* Navbar scroll effect */
    const nav = document.querySelector('.welcome-nav');
    if (nav) {
        const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 40);
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* Scroll-reveal via IntersectionObserver */
    const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal').forEach(el => io.observe(el));

    /* Animated stat counters in hero */
    document.querySelectorAll('.hero-stat-num[data-count]').forEach(el => {
        animateCounter(el, +el.dataset.count, 2000);
    });

    /* Progress bars in preview widget */
    setTimeout(() => {
        document.querySelectorAll('.preview-bar-fill[data-width]').forEach(el => {
            el.style.width = el.dataset.width;
        });
    }, 600);
}

/* ════════════════════════════════════════
   AUTH PAGE
═════════════════════════════════════════ */
function initAuthPage() {
    /* Role card selection */
    const roleOptions = document.querySelectorAll('.auth-role-option');
    roleOptions.forEach(opt => {
        opt.addEventListener('change', () => {
            roleOptions.forEach(o => o.nextElementSibling?.classList.remove('selected'));
            opt.nextElementSibling?.classList.add('selected');
        });
    });

    /* Password toggle */
    document.querySelectorAll('.pw-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.previousElementSibling;
            if (!input) return;
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            btn.querySelector('i')?.classList.toggle('fa-eye', isText);
            btn.querySelector('i')?.classList.toggle('fa-eye-slash', !isText);
        });
    });

    /* Login button loading state */
    const loginForm = document.getElementById('loginForm');
    const loginBtn  = document.getElementById('loginBtn');
    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', () => {
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i>Authenticating…';
        });
    }

    /* Registration button loading state */
    const registerForm = document.getElementById('registerForm');
    const registerBtn  = document.getElementById('registerBtn');
    if (registerForm && registerBtn) {
        registerForm.addEventListener('submit', () => {
            registerBtn.disabled = true;
            registerBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i>Creating account…';
        });
    }   

    /* Show/hide password validation rules on focus */
    const pwInput = document.getElementById('password');
    const pwRules = document.getElementById('passwordRules');
    if (pwInput && pwRules) {
        pwInput.addEventListener('focus', () => pwRules.classList.add('visible'));
        pwInput.addEventListener('blur', () => pwRules.classList.remove('visible'));
    }   

    /* Show/hide confirm password match status */
    const confirmInput = document.getElementById('password_confirmation');
    const matchStatus  = document.getElementById('matchStatus');
    if (confirmInput && matchStatus) {
        confirmInput.addEventListener('input', () => {
            const pwVal = pwInput?.value || '';
            const confVal = confirmInput.value;
            if (!confVal) {
                matchStatus.textContent = '';
                matchStatus.className = '';
            } else if (pwVal === confVal) {
                matchStatus.textContent = 'Passwords match';
                matchStatus.className = 'text-success';
            } else {
                matchStatus.textContent = 'Passwords do not match';
                matchStatus.className = 'text-danger';
            }
        });
    }   
    

    /* Shake animation on error */
    if (document.querySelector('.alert-danger')) {
        document.querySelector('.auth-right-inner')?.classList.add('shake');
        setTimeout(() => document.querySelector('.auth-right-inner')?.classList.remove('shake'), 600);
    }

    /* Password strength meter (basic example) */
    const strengthBar = document.getElementById('passwordStrength');
    if (pwInput && strengthBar) {
        pwInput.addEventListener('input', () => {
            const val = pwInput.value;
            let strength = 0;
            if (val.length >= 8) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[a-z]/.test(val)) strength++;
            if (/\d/.test(val)) strength++;
            if (/[@$!%*?&]/.test(val)) strength++;

            const colors = ['#dc2626', '#f97316', '#eab308', '#22c55e', '#16a34a'];
            strengthBar.style.width = `${(strength / 5) * 100}%`;
            strengthBar.style.backgroundColor = colors[strength - 1] || '#e5e7eb';
        });
    }       

    /* Show/hide password rules on focus */
    const pwInputReg = document.getElementById('password');
    const pwRulesReg = document.getElementById('passwordRules');
    if (pwInputReg && pwRulesReg) {
        pwInputReg.addEventListener('focus', () => pwRulesReg.classList.add('visible'));
        pwInputReg.addEventListener('blur', () => pwRulesReg.classList.remove('visible'));
    }       

    /* Show/hide confirm password match status */
    const confirmInputReg = document.getElementById('password_confirmation');
    const matchStatusReg  = document.getElementById('matchStatus');
    if (confirmInputReg && matchStatusReg) {
        confirmInputReg.addEventListener('input', () => {
            const pwVal = pwInputReg?.value || '';
            const confVal = confirmInputReg.value;
            if (!confVal) {
                matchStatusReg.textContent = '';
                matchStatusReg.className = '';
            } else if (pwVal === confVal) {
                matchStatusReg.textContent = 'Passwords match';
                matchStatusReg.className = 'text-success';
            } else {
                matchStatusReg.textContent = 'Passwords do not match';
                matchStatusReg.className = 'text-danger';
            }
        });
    }       

    
}

/* ════════════════════════════════════════
   DASHBOARD
═════════════════════════════════════════ */
function initDashboard() {

    /* ── Stat counters ── */
    document.querySelectorAll('[data-count]').forEach(el => {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    animateCounter(el, +el.dataset.count, 1200);
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        observer.observe(el);
    });

    /* ── Progress bars ── */
    const barObserver = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.style.width = e.target.dataset.width || '0%';
                barObserver.unobserve(e.target);
            }
        });
    }, { threshold: 0.3 });

    document.querySelectorAll('.progress-bar-nis[data-width]').forEach(el => {
        el.style.width = '0%';
        el.style.transition = 'width 1s cubic-bezier(0.4,0,0.2,1)';
        barObserver.observe(el);
    });

    /* ── Card reveal ── */
    const cardObserver = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.style.opacity = '1';
                e.target.style.transform = 'translateY(0)';
                cardObserver.unobserve(e.target);
            }
        });
    }, { threshold: 0.05 });

    document.querySelectorAll('.redas-card, .stat-card').forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(16px)';
        el.style.transition = `opacity 0.4s ease ${i * 0.06}s, transform 0.4s ease ${i * 0.06}s`;
        cardObserver.observe(el);
    });

    /* ── Charts ── */
    initCharts();

    /* ── Notifications dropdown ── */
    const notifBtn = document.getElementById('notifBtn');
    const notifPanel = document.getElementById('notifPanel');
    if (notifBtn && notifPanel) {
        notifBtn.addEventListener('click', e => {
            e.stopPropagation();
            notifPanel.classList.toggle('open');
        });
        document.addEventListener('click', () => notifPanel.classList.remove('open'));
    }

    /* ── User dropdown ── */
    const userBtn = document.getElementById('userMenuBtn');
    const userDrop = document.getElementById('userMenuDrop');
    if (userBtn && userDrop) {
        userBtn.addEventListener('click', e => {
            e.stopPropagation();
            userDrop.classList.toggle('open');
        });
        document.addEventListener('click', () => userDrop.classList.remove('open'));
    }

    /* ── Approve / Reject quick actions ── */
    document.querySelectorAll('[data-action="approve"]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const row = btn.closest('tr, .approval-item');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';
            // Replace with real AJAX call as needed
            await delay(600);
            row?.classList.add('approved-row');
            const badge = row?.querySelector('.status-badge');
            if (badge) { badge.className = 'status-badge badge-approved'; badge.textContent = 'Approved'; }
            btn.closest('.approval-actions')?.remove();
            showToast('Report approved successfully', 'success');
        });
    });

    document.querySelectorAll('[data-action="reject"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            document.getElementById('rejectTargetId')?.setAttribute('value', id);
            modal.show();
        });
    });

    /* ── Table search ── */
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.toLowerCase();
            document.querySelectorAll('.searchable-table tbody tr').forEach(tr => {
                tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    /* ── Filter dropdowns ── */
    document.querySelectorAll('[data-filter-target]').forEach(sel => {
        sel.addEventListener('change', () => {
            const tableId = sel.dataset.filterTarget;
            const col     = +sel.dataset.filterCol;
            const val     = sel.value.toLowerCase();
            document.querySelectorAll(`#${tableId} tbody tr`).forEach(tr => {
                const cell = tr.cells[col]?.textContent.toLowerCase() || '';
                tr.style.display = (!val || cell.includes(val)) ? '' : 'none';
            });
        });
    });

    /* ── Date period selector ── */
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.period-btns')?.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });
}

/* ════════════════════════════════════════
   CHARTS
═════════════════════════════════════════ */
function initCharts() {
    const NIS_GREEN  = '#006633';
    const NIS_LIGHT  = '#2d9e61';
    const NIS_GOLD   = '#c5922a';
    const NIS_RED    = '#dc2626';
    const NIS_BLUE   = '#1d4ed8';
    const NIS_PURPLE = '#7c3aed';

    const defaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { font: { family: 'Inter, sans-serif', size: 12 } } },
            tooltip: { cornerRadius: 8, padding: 10 },
        },
    };

    /* Submissions trend (line) */
    const trendCtx = document.getElementById('submissionTrend');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Submitted',
                        data: [42, 38, 55, 61, 57, 70, 68, 74, 66, 80, 78, 85],
                        borderColor: NIS_GREEN,
                        backgroundColor: 'rgba(0,102,51,0.08)',
                        fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: NIS_GREEN,
                    },
                    {
                        label: 'Approved',
                        data: [38, 33, 48, 55, 50, 62, 60, 67, 58, 72, 70, 76],
                        borderColor: NIS_GOLD,
                        backgroundColor: 'rgba(197,146,42,0.06)',
                        fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: NIS_GOLD,
                    },
                ],
            },
            options: {
                ...defaults,
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { precision: 0 } },
                    x: { grid: { display: false } },
                },
            },
        });
    }

    /* Status doughnut */
    const doughCtx = document.getElementById('statusChart');
    if (doughCtx) {
        new Chart(doughCtx, {
            type: 'doughnut',
            data: {
                labels: ['Approved', 'Pending', 'Rejected', 'Draft'],
                datasets: [{
                    data: [68, 18, 9, 5],
                    backgroundColor: [NIS_GREEN, NIS_GOLD, NIS_RED, '#94a3b8'],
                    borderWidth: 0,
                    hoverOffset: 6,
                }],
            },
            options: {
                ...defaults,
                cutout: '68%',
                plugins: { ...defaults.plugins, legend: { position: 'bottom' } },
            },
        });
    }

    /* Formation compliance (bar) */
    const compliCtx = document.getElementById('complianceChart');
    if (compliCtx) {
        new Chart(compliCtx, {
            type: 'bar',
            data: {
                labels: ['Zone A', 'Zone B', 'Zone C', 'Zone D', 'Zone E', 'Zone F', 'Zone G', 'Zone H'],
                datasets: [{
                    label: 'Compliance %',
                    data: [92, 78, 85, 61, 95, 70, 88, 73],
                    backgroundColor: (ctx) => {
                        const v = ctx.raw;
                        return v >= 90 ? NIS_GREEN : v >= 75 ? NIS_GOLD : NIS_RED;
                    },
                    borderRadius: 6,
                }],
            },
            options: {
                ...defaults,
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { grid: { display: false } },
                },
                plugins: { ...defaults.plugins, legend: { display: false } },
            },
        });
    }

    /* Monthly returns by directorate (bar) */
    const dirCtx = document.getElementById('directorateChart');
    if (dirCtx) {
        new Chart(dirCtx, {
            type: 'bar',
            data: {
                labels: ['HRM', 'F&A', 'Border Mgt', 'Migration', 'POTD', 'Visa', 'PRS', 'IC', 'ICT', 'Works'],
                datasets: [{
                    label: 'Reports Submitted',
                    data: [14, 12, 18, 16, 22, 15, 11, 13, 9, 8],
                    backgroundColor: NIS_LIGHT,
                    borderRadius: 6,
                }],
            },
            options: {
                ...defaults,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                },
                plugins: { ...defaults.plugins, legend: { display: false } },
                indexAxis: 'y',
            },
        });
    }

    /* Monthly report mini chart (user dashboard) */
    const miniCtx = document.getElementById('miniTrend');
    if (miniCtx) {
        new Chart(miniCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    data: [1, 1, 1, 2, 2, 3],
                    borderColor: NIS_GREEN,
                    backgroundColor: 'rgba(0,102,51,0.1)',
                    fill: true, tension: 0.4, pointRadius: 3, borderWidth: 2,
                }],
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                scales: { x: { display: false }, y: { display: false, beginAtZero: true } },
            },
        });
    }
}

/* ════════════════════════════════════════
   UTILITIES
═════════════════════════════════════════ */

function animateCounter(el, target, duration = 1000) {
    const start = performance.now();
    const from  = 0;
    const easeOut = t => 1 - Math.pow(1 - t, 3);

    const update = (now) => {
        const p = Math.min((now - start) / duration, 1);
        el.textContent = Math.round(from + (target - from) * easeOut(p)).toLocaleString();
        if (p < 1) requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
}

function delay(ms) { return new Promise(res => setTimeout(res, ms)); }

function showToast(message, type = 'info') {
    const colors = { success: '#006633', danger: '#dc2626', info: '#1d4ed8', warning: '#d97706' };
    const icons  = { success: 'fa-check-circle', danger: 'fa-times-circle', info: 'fa-info-circle', warning: 'fa-exclamation-triangle' };

    const toast = document.createElement('div');
    toast.style.cssText = `
        position:fixed; bottom:24px; right:24px; z-index:9999;
        background:${colors[type] || colors.info}; color:white;
        padding:12px 20px; border-radius:10px;
        font-size:.875rem; font-weight:600;
        display:flex; align-items:center; gap:10px;
        box-shadow:0 8px 24px rgba(0,0,0,0.2);
        animation:fadeInUp .3s ease;
        max-width:320px;
    `;
    toast.innerHTML = `<i class="fas ${icons[type] || icons.info}"></i><span>${message}</span>`;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity .3s'; setTimeout(() => toast.remove(), 300); }, 4000);
}

/* Export for inline scripts */
window.REDAS = { showToast, animateCounter };
