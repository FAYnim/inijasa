<!-- Business Switcher Modal -->
<div class="modal fade" id="businessSwitcherModal" tabindex="-1" aria-labelledby="businessSwitcherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="businessSwitcherModalLabel">
                    <i class="fas fa-briefcase me-2"></i>Pilih Bisnis
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <!-- Search Box (shown when businesses >= 5) -->
                <div id="businessSearchContainer" class="mb-3" style="display:none;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="businessSearch"
                               placeholder="Cari bisnis..." autocomplete="off">
                    </div>
                </div>

                <!-- Business List -->
                <div id="businessList" class="bs-business-list">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Memuat bisnis...</p>
                    </div>
                </div>

                <!-- Empty State (only 1 business) -->
                <div id="businessEmptyState" class="text-center py-4" style="display:none;">
                    <i class="fas fa-briefcase fa-3x text-muted mb-3 d-block"></i>
                    <h6 class="text-muted">Anda belum memiliki bisnis lain</h6>
                    <p class="text-muted small mb-0">Buat bisnis baru untuk mengelola lebih banyak usaha</p>
                </div>

                <!-- No Search Results -->
                <div id="businessNoResults" class="text-center py-4" style="display:none;">
                    <i class="fas fa-search fa-2x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">Tidak ada bisnis ditemukan</p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-primary w-100" id="btnCreateBusiness">
                    <i class="fas fa-plus me-2"></i>Buat Bisnis Baru
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.bs-business-list {
    max-height: 380px;
    overflow-y: auto;
}

.bs-business-item {
    padding: 0.875rem 1rem;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    margin-bottom: 0.625rem;
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
    position: relative;
}

.bs-business-item:last-child { margin-bottom: 0; }

.bs-business-item:hover {
    background: #F9FAFB;
    border-color: var(--primary-color);
    box-shadow: 0 2px 6px rgba(255,107,53,0.1);
}

.bs-business-item.active {
    border-color: var(--primary-color);
    background: #FFF5F3;
}

.bs-business-item .bs-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.bs-business-item .bs-name {
    font-weight: 600;
    color: #1F2937;
    margin-bottom: 0.125rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}

.bs-business-item .bs-category {
    font-size: 0.8125rem;
    color: #6B7280;
}

.bs-active-badge {
    font-size: 0.6875rem;
    background: var(--primary-color);
    color: white;
    padding: 0.2rem 0.45rem;
    border-radius: 4px;
    font-weight: 600;
    white-space: nowrap;
}

.bs-check {
    color: var(--primary-color);
    font-size: 1.1rem;
}
</style>
