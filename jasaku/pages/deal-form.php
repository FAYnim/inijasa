<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Deal';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$businessId = getCurrentBusinessId();
$dealId = $_GET['id'] ?? null;
$deal = $dealId ? getDealById($dealId) : null;

$clients = getClients($businessId);
$services = getServicePackages($businessId);
$stages = ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost'];

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dealTitle = trim($_POST['deal_title'] ?? '');
    $clientId = $_POST['client_id'] ?? '';
    $servicePackageId = $_POST['service_package_id'] ?? null;
    $dealValue = floatval($_POST['deal_value'] ?? 0);
    $discountPercent = floatval($_POST['discount_percent'] ?? 0);
    $expectedCloseDate = $_POST['expected_close_date'] ?? null;
    $notes = trim($_POST['notes'] ?? '');
    $paymentStatus = $_POST['payment_status'] ?? 'Pending';
    $currentStage = $_POST['current_stage'] ?? 'Lead';
    
    if (empty($dealTitle)) {
        $error = 'Judul deal wajib diisi';
    } elseif (empty($clientId)) {
        $error = 'Klien wajib dipilih';
    } elseif ($dealValue < 0) {
        $error = 'Nilai deal tidak valid';
    } elseif ($discountPercent < 0 || $discountPercent > 100) {
        $error = 'Diskon harus antara 0-100%';
    } else {
        $data = [
            'deal_title' => $dealTitle,
            'client_id' => $clientId,
            'service_package_id' => $servicePackageId,
            'deal_value' => $dealValue,
            'discount_percent' => $discountPercent,
            'expected_close_date' => $expectedCloseDate,
            'notes' => $notes,
            'payment_status' => $paymentStatus
        ];
        
        if ($deal) {
            $result = updateDeal($dealId, $data);
            if ($result['success']) {
                // Update stage if changed
                if ($currentStage !== $deal['current_stage']) {
                    updateDealStage($dealId, $currentStage);
                }
                $success = 'Deal berhasil diupdate';
                $deal = getDealById($dealId);
            } else {
                $error = $result['message'];
            }
        } else {
            $result = createDeal($businessId, $data);
            if ($result['success']) {
                header('Location: deals.php');
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Handle add payment
if (isset($_POST['add_payment']) && $deal) {
    $paymentAmount = floatval($_POST['payment_amount'] ?? 0);
    $paymentDate = $_POST['payment_date'] ?? date('Y-m-d');
    $paymentMethod = $_POST['payment_method'] ?? 'Transfer';
    $paymentNotes = $_POST['payment_notes'] ?? '';
    
    if ($paymentAmount > 0) {
        $paymentData = [
            'amount' => $paymentAmount,
            'payment_date' => $paymentDate,
            'method' => $paymentMethod,
            'notes' => $paymentNotes
        ];
        addDealPayment($dealId, $paymentData);
        $deal = getDealById($dealId);
    }
}

// Get payments if viewing existing deal
$payments = $deal ? getDealPayments($dealId) : [];
?>

<div class="main-content">
    <div class="page-header">
        <h2><?= $deal ? 'Edit' : 'Tambah' ?> Deal</h2>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= sanitize($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= sanitize($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="deal_title" class="form-label">Judul Deal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="deal_title" name="deal_title" required value="<?= sanitize($deal['deal_title'] ?? $_POST['deal_title'] ?? '') ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_id" class="form-label">Klien <span class="text-danger">*</span></label>
                                <select class="form-select" id="client_id" name="client_id" required>
                                    <option value="">Pilih Klien</option>
                                    <?php foreach ($clients as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($deal['client_id'] ?? $_POST['client_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                        <?= sanitize($c['client_name']) ?><?= $c['company'] ? ' (' . sanitize($c['company']) . ')' : '' ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="service_package_id" class="form-label">Paket Jasa</label>
                                <select class="form-select" id="service_package_id" name="service_package_id" data-price="<?= $deal['service_price'] ?? '' ?>">
                                    <option value="">Pilih Paket</option>
                                    <?php foreach ($services as $s): ?>
                                    <option value="<?= $s['id'] ?>" data-price="<?= $s['price'] ?>" <?= ($deal['service_package_id'] ?? $_POST['service_package_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                                        <?= sanitize($s['package_name']) ?> - <?= formatCurrency($s['price']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="deal_value" class="form-label">Nilai Deal (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="deal_value" name="deal_value" required min="0" step="0.01" value="<?= sanitize($deal['deal_value'] ?? $_POST['deal_value'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="discount_percent" class="form-label">Diskon (%)</label>
                                <input type="number" class="form-control" id="discount_percent" name="discount_percent" min="0" max="100" step="0.01" value="<?= sanitize($deal['discount_percent'] ?? $_POST['discount_percent'] ?? '0') ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nilai Akhir</label>
                                <div class="form-control-plaintext fw-bold text-primary" id="final_value_display">
                                    <?= formatCurrency(($deal['final_value'] ?? 0)) ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="current_stage" class="form-label">Stage</label>
                                <select class="form-select" id="current_stage" name="current_stage">
                                    <?php foreach ($stages as $s): ?>
                                    <option value="<?= $s ?>" <?= ($deal['current_stage'] ?? $_POST['current_stage'] ?? 'Lead') === $s ? 'selected' : '' ?>><?= $s ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="expected_close_date" class="form-label">Tanggal Tutup (Expected)</label>
                                <input type="date" class="form-control" id="expected_close_date" name="expected_close_date" value="<?= sanitize($deal['expected_close_date'] ?? $_POST['expected_close_date'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <?php if ($deal): ?>
                        <div class="mb-3">
                            <label for="payment_status" class="form-label">Status Pembayaran</label>
                            <select class="form-select" id="payment_status" name="payment_status">
                                <option value="Pending" <?= ($deal['payment_status'] ?? 'Pending') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Partial" <?= ($deal['payment_status'] ?? '') === 'Partial' ? 'selected' : '' ?>>Partial</option>
                                <option value="Paid" <?= ($deal['payment_status'] ?? '') === 'Paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="Cancelled" <?= ($deal['payment_status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?= sanitize($deal['notes'] ?? $_POST['notes'] ?? '') ?></textarea>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="deals.php" class="btn btn-outline-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <?php if ($deal): ?>
        <div class="col-lg-4">
            <!-- Payment History -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-credit-card me-2"></i>Pembayaran
                </div>
                <div class="card-body">
                    <?php if (count($payments) > 0): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($payments as $payment): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold"><?= formatCurrency($payment['amount']) ?></div>
                                    <small class="text-muted"><?= formatDate($payment['payment_date']) ?> - <?= $payment['method'] ?></small>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted text-center">Belum ada pembayaran</p>
                    <?php endif; ?>
                    
                    <!-- Add Payment Form -->
                    <hr>
                    <h6 class="mb-3">Tambah Pembayaran</h6>
                    <form method="POST" action="">
                        <input type="hidden" name="add_payment" value="1">
                        <div class="mb-2">
                            <input type="number" class="form-control" name="payment_amount" placeholder="Jumlah" min="0" step="0.01" required>
                        </div>
                        <div class="mb-2">
                            <input type="date" class="form-control" name="payment_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-2">
                            <select class="form-select" name="payment_method">
                                <option value="Transfer">Transfer</option>
                                <option value="Cash">Cash</option>
                                <option value="QRIS">QRIS</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="payment_notes" placeholder="Catatan (opsional)">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-plus me-1"></i> Tambah
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Deal Summary -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i>Ringkasan
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>Nilai Deal</td>
                            <td class="text-end"><?= formatCurrency($deal['deal_value']) ?></td>
                        </tr>
                        <tr>
                            <td>Diskon</td>
                            <td class="text-end"><?= $deal['discount_percent'] ?>%</td>
                        </tr>
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td class="text-end text-primary"><?= formatCurrency($deal['final_value']) ?></td>
                        </tr>
                        <tr>
                            <td>Sudah Dibayar</td>
                            <td class="text-end text-success"><?= formatCurrency(array_sum(array_column($payments, 'amount'))) ?></td>
                        </tr>
                        <tr>
                            <td>Sisa</td>
                            <td class="text-end text-danger"><?= formatCurrency($deal['final_value'] - array_sum(array_column($payments, 'amount'))) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
