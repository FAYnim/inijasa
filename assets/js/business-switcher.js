/**
 * Business Switcher
 * Handles business switching, creation, and modal interactions
 */
(function () {
    'use strict';

    const CONFIG = {
        API_BASE: 'api/',
        SEARCH_THRESHOLD: 5,
        DEBOUNCE_MS: 250
    };

    const CATEGORY_ICONS = {
        'Kreatif/Desain': 'fas fa-palette',
        'Konsultan':      'fas fa-user-tie',
        'Kebersihan':     'fas fa-broom',
        'Perbaikan':      'fas fa-tools',
        'Lainnya':        'fas fa-briefcase'
    };

    let allBusinesses       = [];
    let currentBusinessId   = null;
    let limitInfo           = {};

    // ── DOM refs ──────────────────────────────────────────────
    const switcherModal      = document.getElementById('businessSwitcherModal');
    const createModal        = document.getElementById('createBusinessModal');
    const businessList       = document.getElementById('businessList');
    const searchContainer    = document.getElementById('businessSearchContainer');
    const searchInput        = document.getElementById('businessSearch');
    const emptyState         = document.getElementById('businessEmptyState');
    const noResultsState     = document.getElementById('businessNoResults');
    const createForm         = document.getElementById('createBusinessForm');
    const createErrorBox     = document.getElementById('createBusinessError');
    const createErrorMessage = document.getElementById('createBusinessErrorMessage');
    const btnCreateBusiness  = document.getElementById('btnCreateBusiness');
    const btnSubmitBusiness  = document.getElementById('btnSubmitBusiness');

    // ── Init ──────────────────────────────────────────────────
    function init() {
        if (switcherModal) {
            switcherModal.addEventListener('show.bs.modal', loadBusinesses);
        }

        if (searchInput) {
            searchInput.addEventListener('input', debounce(handleSearch, CONFIG.DEBOUNCE_MS));
        }

        if (createForm) {
            createForm.addEventListener('submit', handleCreateBusiness);
        }

        // "Buat Bisnis Baru" button opens create modal and hides switcher
        if (btnCreateBusiness) {
            btnCreateBusiness.addEventListener('click', function () {
                const inst = bootstrap.Modal.getInstance(switcherModal);
                if (inst) inst.hide();
                // Small delay so Bootstrap transitions don't collide
                setTimeout(function () {
                    new bootstrap.Modal(createModal).show();
                }, 300);
            });
        }

        // When create modal closes, re-open switcher modal
        if (createModal) {
            createModal.addEventListener('hidden.bs.modal', function () {
                if (createForm) {
                    createForm.reset();
                    createForm.classList.remove('was-validated');
                }
                if (createErrorBox) createErrorBox.classList.add('d-none');
                new bootstrap.Modal(switcherModal).show();
            });
        }
    }

    // ── Load businesses ───────────────────────────────────────
    async function loadBusinesses() {
        showLoading();
        try {
            const res  = await fetch(getApiBase() + 'business-list.php');
            const data = await res.json();
            if (!data.success) throw new Error(data.message || 'Load failed');

            allBusinesses     = data.data.businesses;
            currentBusinessId = data.data.current_business_id;
            limitInfo         = data.data.limit_info;

            renderBusinesses(allBusinesses);
            updateCreateButton();
        } catch (err) {
            showListError('Gagal memuat daftar bisnis. Silakan coba lagi.');
            console.error(err);
        }
    }

    // ── Render list ───────────────────────────────────────────
    function renderBusinesses(list) {
        // Search visibility
        if (searchContainer) {
            searchContainer.style.display = allBusinesses.length >= CONFIG.SEARCH_THRESHOLD ? 'block' : 'none';
        }

        if (allBusinesses.length <= 1) {
            show(emptyState);
            hide(businessList);
            hide(noResultsState);
            return;
        }

        if (list.length === 0) {
            show(noResultsState);
            hide(businessList);
            hide(emptyState);
            return;
        }

        hide(emptyState);
        hide(noResultsState);
        show(businessList);

        businessList.innerHTML = list.map(function (b) {
            const isActive = String(b.id) === String(currentBusinessId);
            const icon     = CATEGORY_ICONS[b.category] || 'fas fa-briefcase';
            return `
            <div class="bs-business-item ${isActive ? 'active' : ''}" data-business-id="${b.id}" role="button" tabindex="0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bs-icon"><i class="${icon}"></i></div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="bs-name">${esc(b.business_name)}</div>
                        <div class="bs-category">${esc(b.category)}</div>
                    </div>
                    ${isActive
                        ? '<span class="bs-active-badge">Aktif</span>'
                        : '<i class="fas fa-chevron-right text-muted"></i>'}
                </div>
            </div>`;
        }).join('');

        businessList.querySelectorAll('.bs-business-item').forEach(function (el) {
            el.addEventListener('click', handleItemClick);
            el.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') handleItemClick.call(this, e);
            });
        });
    }

    // ── Switch business ───────────────────────────────────────
    async function handleItemClick() {
        const businessId = parseInt(this.dataset.businessId, 10);
        if (businessId === parseInt(currentBusinessId, 10)) {
            bootstrap.Modal.getInstance(switcherModal).hide();
            return;
        }

        this.style.opacity       = '0.5';
        this.style.pointerEvents = 'none';

        try {
            const res  = await fetch(getApiBase() + 'business-switch.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.CSRF_TOKEN || '' },
                body:    JSON.stringify({ business_id: businessId })
            });
            const data = await res.json();
            if (!data.success) throw new Error(data.message);

            // Update header name immediately
            const nameEl = document.getElementById('currentBusinessName');
            if (nameEl) nameEl.textContent = data.data.business_name;

            bootstrap.Modal.getInstance(switcherModal).hide();
            showToast('Bisnis beralih ke ' + data.data.business_name, 'success');

            setTimeout(function () { window.location.reload(); }, 600);
        } catch (err) {
            showToast(err.message || 'Gagal beralih bisnis', 'danger');
            this.style.opacity       = '1';
            this.style.pointerEvents = 'auto';
        }
    }

    // ── Search ────────────────────────────────────────────────
    function handleSearch(e) {
        const q = e.target.value.toLowerCase().trim();
        if (!q) {
            renderBusinesses(allBusinesses);
            return;
        }
        renderBusinesses(allBusinesses.filter(function (b) {
            return b.business_name.toLowerCase().includes(q) ||
                   b.category.toLowerCase().includes(q);
        }));
    }

    // ── Create business ───────────────────────────────────────
    async function handleCreateBusiness(e) {
        e.preventDefault();
        if (!createForm.checkValidity()) {
            createForm.classList.add('was-validated');
            return;
        }

        createErrorBox.classList.add('d-none');
        btnSubmitBusiness.disabled   = true;
        btnSubmitBusiness.innerHTML  = '<span class="spinner-border spinner-border-sm me-2"></span>Membuat...';

        const fd = new FormData(createForm);
        const payload = {
            business_name: fd.get('business_name'),
            category:      fd.get('category'),
            description:   fd.get('description'),
            phone:         fd.get('phone'),
            email:         fd.get('email'),
            set_as_active: fd.get('set_as_active') ? true : false
        };

        try {
            const res  = await fetch(getApiBase() + 'business-create.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.CSRF_TOKEN || '' },
                body:    JSON.stringify(payload)
            });
            const data = await res.json();
            if (!data.success) throw new Error(data.message);

            bootstrap.Modal.getInstance(createModal).hide();
            showToast('Bisnis "' + data.data.business_name + '" berhasil dibuat!', 'success');

            if (payload.set_as_active) {
                const nameEl = document.getElementById('currentBusinessName');
                if (nameEl) nameEl.textContent = data.data.business_name;
                setTimeout(function () { window.location.reload(); }, 600);
            }
        } catch (err) {
            createErrorMessage.textContent = err.message || 'Terjadi kesalahan.';
            createErrorBox.classList.remove('d-none');
        } finally {
            btnSubmitBusiness.disabled  = false;
            btnSubmitBusiness.innerHTML = '<i class="fas fa-save me-2"></i>Buat Bisnis';
        }
    }

    // ── Helpers ───────────────────────────────────────────────
    function updateCreateButton() {
        if (!btnCreateBusiness) return;
        if (!limitInfo.can_create) {
            btnCreateBusiness.disabled     = true;
            btnCreateBusiness.innerHTML    =
                `<i class="fas fa-ban me-2"></i>Limit Bisnis Tercapai (${limitInfo.current_count}/${limitInfo.limit})`;
        } else {
            btnCreateBusiness.disabled     = false;
            btnCreateBusiness.innerHTML    = '<i class="fas fa-plus me-2"></i>Buat Bisnis Baru';
        }
    }

    function showLoading() {
        if (!businessList) return;
        businessList.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2 mb-0">Memuat bisnis...</p>
            </div>`;
        show(businessList);
        hide(emptyState);
        hide(noResultsState);
    }

    function showListError(msg) {
        if (!businessList) return;
        businessList.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>${esc(msg)}
            </div>`;
    }

    function getApiBase() {
        // Determine relative path from current page depth to api/ folder
        const depth = (window.location.pathname.match(/\//g) || []).length - 1;
        // pages are at root, so api/ is one level up relative to app base path
        // Just use absolute path relative to domain root
        const parts  = window.location.pathname.split('/');
        // Support both old and new branded folder names during migration.
        const idx = Math.max(parts.indexOf('inijasa'), parts.indexOf('jasaku'));
        if (idx !== -1) {
            return parts.slice(0, idx + 1).join('/') + '/api/';
        }
        return 'api/';
    }

    function showToast(message, type) {
        const container = getOrCreateToastContainer();
        const id        = 'toast-' + Date.now();
        const bgClass   = type === 'success' ? 'bg-success' : 'bg-danger';
        const icon      = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        const toastEl = document.createElement('div');
        toastEl.id        = id;
        toastEl.className = `toast align-items-center text-white ${bgClass} border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${icon} me-2"></i>${esc(message)}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast" aria-label="Close"></button>
            </div>`;

        container.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', function () { toastEl.remove(); });
    }

    function getOrCreateToastContainer() {
        let c = document.getElementById('bsToastContainer');
        if (!c) {
            c = document.createElement('div');
            c.id        = 'bsToastContainer';
            c.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            c.style.zIndex = '1100';
            document.body.appendChild(c);
        }
        return c;
    }

    function esc(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function show(el) { if (el) el.style.display = 'block'; }
    function hide(el) { if (el) el.style.display = 'none'; }

    function debounce(fn, wait) {
        let timer;
        return function () {
            clearTimeout(timer);
            timer = setTimeout(fn.bind(this, ...arguments), wait);
        };
    }

    // ── Bootstrap ─────────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
