<!-- Create Business Modal -->
<div class="modal fade" id="createBusinessModal" tabindex="-1" aria-labelledby="createBusinessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBusinessModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Buat Bisnis Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createBusinessForm" novalidate>
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="newBusinessName" class="form-label fw-500">
                            Nama Bisnis <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="newBusinessName" name="business_name"
                               placeholder="Contoh: IniJasa Creative Studio" required>
                        <div class="invalid-feedback">Nama bisnis wajib diisi.</div>
                    </div>

                    <div class="mb-3">
                        <label for="newBusinessCategory" class="form-label fw-500">Kategori Bisnis</label>
                        <select class="form-select" id="newBusinessCategory" name="category">
                            <option value="Kreatif/Desain">Kreatif/Desain</option>
                            <option value="Konsultan">Konsultan</option>
                            <option value="Kebersihan">Kebersihan</option>
                            <option value="Perbaikan">Perbaikan</option>
                            <option value="Lainnya" selected>Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="newBusinessDescription" class="form-label fw-500">Deskripsi <span class="text-muted fw-normal">(Opsional)</span></label>
                        <textarea class="form-control" id="newBusinessDescription" name="description"
                                  rows="2" placeholder="Deskripsikan bisnis Anda..."></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label for="newBusinessPhone" class="form-label fw-500">Telepon <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="tel" class="form-control" id="newBusinessPhone" name="phone"
                                   placeholder="08123456789">
                        </div>
                        <div class="col-sm-6">
                            <label for="newBusinessEmail" class="form-label fw-500">Email <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="email" class="form-control" id="newBusinessEmail" name="email"
                                   placeholder="info@bisnis.com">
                            <div class="invalid-feedback">Format email tidak valid.</div>
                        </div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="setAsActive" name="set_as_active" checked>
                        <label class="form-check-label" for="setAsActive">
                            Langsung beralih ke bisnis ini setelah dibuat
                        </label>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="createBusinessError" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span id="createBusinessErrorMessage"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitBusiness">
                        <i class="fas fa-save me-2"></i>Buat Bisnis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.fw-500 { font-weight: 500; }
#createBusinessModal .form-control:focus,
#createBusinessModal .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.2);
}
</style>
