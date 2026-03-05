<?php
/**
 * Deal Form Page (Create/Edit Deal)
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$business_id = getCurrentBusinessId();
if (!$business_id) {
    redirect('setup-business.php');
}

$deal_id = $_GET['id'] ?? null;
$is_edit = !empty($deal_id);
$page_title = $is_edit ? 'Edit Deal' : 'Buat Deal Baru';

$error = '';
$deal = null;

// Load deal data if editing
if ($is_edit) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM deals WHERE id = ? AND business_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $deal_id, $business_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $deal = mysqli_fetch_assoc($result);
    
    if (!$deal) {
        setFlashMessage('danger', 'Deal tidak ditemukan.');
        redirect('deals.php');
    }
}

// Get clients for dropdown
$clients = mysqli_query($conn, "SELECT id, client_name, company FROM clients WHERE business_id = $business_id ORDER BY client_name");

// Get service packages for dropdown
$services = mysqli_query($conn, "SELECT id, service_name, price FROM services WHERE business_id = $business_id AND status = 'Active' AND is_deleted = 0 ORDER BY service_name");

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'] ?? '';
    $service_id = $_POST['service_id'] ?? null;
    $deal_title = trim($_POST['deal_title'] ?? '');
    $deal_value = str_replace(['Rp', '.', ' '], '', $_POST['deal_value'] ?? '0');
    $discount_percent = $_POST['discount_percent'] ?? 0;
    $current_stage = $_POST['current_stage'] ?? 'Lead';
    $expected_close_date = $_POST['expected_close_date'] ?? null;
    $notes = trim($_POST['notes'] ?? '');
    
    // Calculate final value
    $final_value = $deal_value - ($deal_value * $discount_percent / 100);
    
    // Validation
    if (empty($client_id)) {
        $error = 'Klien wajib dipilih.';
    } elseif (empty($deal_title)) {
        $error = 'Judul deal wajib diisi.';
    } elseif ($deal_value <= 0) {
        $error = 'Nilai deal harus lebih dari 0.';
    } else {
        if ($is_edit) {
            // Update deal
            $stmt = mysqli_prepare($conn, 
                "UPDATE deals SET 
                    client_id = ?, 
                    service_id = ?, 
                    deal_title = ?, 
                    deal_value = ?, 
                    discount_percent = ?, 
                    final_value = ?, 
                    current_stage = ?, 
                    expected_close_date = ?, 
                    notes = ?
                 WHERE id = ? AND business_id = ?"
            );
            mysqli_stmt_bind_param($stmt, "iisdddsssii", 
                $client_id, $service_id, $deal_title, $deal_value, 
                $discount_percent, $final_value, $current_stage, $expected_close_date, $notes,
                $deal_id, $business_id
            );
            
            if (mysqli_stmt_execute($stmt)) {
                setFlashMessage('success', 'Deal berhasil diupdate.');
                redirect('deals.php');
            } else {
                $error = 'Gagal mengupdate deal.';
            }
        } else {
            // Insert new deal
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO deals (business_id, client_id, service_id, deal_title, deal_value, discount_percent, final_value, current_stage, expected_close_date, notes) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "iiisdddss", 
                $business_id, $client_id, $service_id, $deal_title, $deal_value, 
                $discount_percent, $final_value, $current_stage, $expected_close_date, $notes
            );
            
            if (mysqli_stmt_execute($stmt)) {
                setFlashMessage('success', 'Deal berhasil dibuat.');
                redirect('deals.php');
            } else {
                $error = 'Gagal membuat deal.';
            }
        }
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1"><?= $page_title ?></h2>
                    <p class="text-muted mb-0">Lengkapi informasi deal</p>
                </div>
                <a href="deals.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>
            
            <!-- Deal Form -->
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="client_id" class="form-label">
                                    Klien <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="client_id" name="client_id" required>
                                    <option value="">Pilih klien...</option>
                                    <?php mysqli_data_seek($clients, 0); ?>
                                    <?php while ($client = mysqli_fetch_assoc($clients)): ?>
                                        <option value="<?= $client['id'] ?>" 
                                                <?= ($deal['client_id'] ?? '') == $client['id'] ? 'selected' : '' ?>>
                                            <?= e($client['client_name']) ?>
                                            <?= $client['company'] ? ' - ' . e($client['company']) : '' ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <div class="form-text">
                                    <a href="client-form.php" target="_blank">+ Tambah klien baru</a>
                                </div>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="deal_title" class="form-label">
                                    Judul Deal <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="deal_title" 
                                       name="deal_title" 
                                       placeholder="Contoh: Website Company Profile"
                                       value="<?= e($deal['deal_title'] ?? '') ?>"
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="service_id" class="form-label">Paket Jasa</label>
                                <select class="form-select" id="service_id" name="service_id">
                                    <option value="">Pilih paket (opsional)...</option>
                                    <?php mysqli_data_seek($services, 0); ?>
                                    <?php while ($service = mysqli_fetch_assoc($services)): ?>
                                        <option value="<?= $service['id'] ?>" 
                                                data-price="<?= $service['price'] ?>"
                                                <?= ($deal['service_id'] ?? '') == $service['id'] ? 'selected' : '' ?>>
                                            <?= e($service['service_name']) ?> (<?= formatCurrency($service['price']) ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="deal_value" class="form-label">
                                    Nilai Deal <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="deal_value" 
                                       name="deal_value" 
                                       placeholder="Rp 10.000.000"
                                       value="<?= isset($deal['deal_value']) ? formatCurrency($deal['deal_value'], false) : '' ?>"
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="discount_percent" class="form-label">Diskon (%)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="discount_percent" 
                                       name="discount_percent" 
                                       min="0" 
                                       max="100" 
                                       step="0.01"
                                       value="<?= $deal['discount_percent'] ?? 0 ?>"
                                       onchange="calculateFinalValue()">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nilai Akhir</label>
                                <div class="form-control bg-light" id="final_value_display">
                                    <?= isset($deal['final_value']) ? formatCurrency($deal['final_value']) : 'Rp 0' ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="current_stage" class="form-label">Stage</label>
                                <select class="form-select" id="current_stage" name="current_stage">
                                    <?php 
                                    $stages = ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost'];
                                    foreach ($stages as $stage): 
                                    ?>
                                        <option value="<?= $stage ?>" <?= ($deal['current_stage'] ?? 'Lead') === $stage ? 'selected' : '' ?>>
                                            <?= $stage ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="expected_close_date" class="form-label">Expected Close Date</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="expected_close_date" 
                                       name="expected_close_date"
                                       value="<?= $deal['expected_close_date'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">Catatan</label>
                                <textarea class="form-control" 
                                          id="notes" 
                                          name="notes" 
                                          rows="4"
                                          placeholder="Catatan tambahan tentang deal ini..."><?= e($deal['notes'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="deals.php" class="btn btn-secondary">Batal</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        <?= $is_edit ? 'Update Deal' : 'Buat Deal' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-fill deal value from service package
document.getElementById('service_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const price = selectedOption.getAttribute('data-price');
    
    if (price) {
        const formatted = new Intl.NumberFormat('id-ID').format(price);
        document.getElementById('deal_value').value = formatted;
        calculateFinalValue();
    }
});

// Format currency input
document.getElementById('deal_value').addEventListener('blur', function() {
    let value = this.value.replace(/[^\d]/g, '');
    if (value) {
        this.value = new Intl.NumberFormat('id-ID').format(value);
        calculateFinalValue();
    }
});

// Calculate final value
function calculateFinalValue() {
    const dealValue = parseFloat(document.getElementById('deal_value').value.replace(/[^\d]/g, '')) || 0;
    const discount = parseFloat(document.getElementById('discount_percent').value) || 0;
    const finalValue = dealValue - (dealValue * discount / 100);
    
    document.getElementById('final_value_display').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(finalValue);
}

// Calculate on discount change
document.getElementById('discount_percent').addEventListener('input', calculateFinalValue);
</script>

<?php include 'includes/footer.php'; ?>
