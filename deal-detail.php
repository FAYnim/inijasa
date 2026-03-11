<?php
/**
 * Deal Detail Page
 * Jasaku - Platform Manajemen Bisnis Jasa
 * 
 * Menampilkan detail deal lengkap:
 * - Info deal + klien
 * - Stage management (move stage)
 * - Payment tracking (tambah/hapus pembayaran)
 * - Stage history timeline
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$page_title = 'Detail Deal';
$business_id = getCurrentBusinessId();

if (!$business_id) {
    redirect('setup-business.php');
}

// Validate deal ID
$deal_id = (int)($_GET['id'] ?? 0);
if (!$deal_id) {
    setFlashMessage('danger', 'Deal tidak ditemukan.');
    redirect('deals.php');
}

// ============================================================
// POST Action Handlers
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Verify CSRF token for all POST actions
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('danger', 'Token keamanan tidak valid. Silakan coba lagi.');
        redirect("deal-detail.php?id=$deal_id");
    }

    // --- Action: Move Stage ---
    if ($action === 'move_stage') {
        $new_stage = $_POST['new_stage'] ?? '';
        $valid_stages = ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost'];
        
        if (!in_array($new_stage, $valid_stages)) {
            setFlashMessage('danger', 'Stage tidak valid.');
            redirect("deal-detail.php?id=$deal_id");
        }
        
        // Get current deal stage
        $stmt = mysqli_prepare($conn, "SELECT current_stage FROM deals WHERE id = ? AND business_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $deal_id, $business_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $current = mysqli_fetch_assoc($result);
        
        if (!$current) {
            setFlashMessage('danger', 'Deal tidak ditemukan.');
            redirect('deals.php');
        }
        
        $old_stage = $current['current_stage'];
        
        // Block if stage is already Won or Lost (final)
        if (in_array($old_stage, ['Won', 'Lost'])) {
            setFlashMessage('warning', 'Stage sudah final (Won/Lost) dan tidak bisa diubah.');
            redirect("deal-detail.php?id=$deal_id");
        }
        
        // Block if same stage
        if ($old_stage === $new_stage) {
            setFlashMessage('info', 'Deal sudah berada di stage tersebut.');
            redirect("deal-detail.php?id=$deal_id");
        }
        
        // Begin transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Insert stage history
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO deal_stage_history (deal_id, from_stage, to_stage) VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "iss", $deal_id, $old_stage, $new_stage);
            mysqli_stmt_execute($stmt);
            
            // Update deal stage
            if (in_array($new_stage, ['Won', 'Lost'])) {
                $stmt = mysqli_prepare($conn, 
                    "UPDATE deals SET current_stage = ?, closed_at = NOW() WHERE id = ? AND business_id = ?"
                );
            } else {
                $stmt = mysqli_prepare($conn, 
                    "UPDATE deals SET current_stage = ?, closed_at = NULL WHERE id = ? AND business_id = ?"
                );
            }
            mysqli_stmt_bind_param($stmt, "sii", $new_stage, $deal_id, $business_id);
            mysqli_stmt_execute($stmt);
            
            mysqli_commit($conn);
            setFlashMessage('success', "Stage berhasil dipindahkan dari $old_stage ke $new_stage.");
        } catch (Exception $e) {
            mysqli_rollback($conn);
            setFlashMessage('danger', 'Gagal memindahkan stage.');
        }
        
        redirect("deal-detail.php?id=$deal_id");
    }

    // --- Action: Add Payment ---
    if ($action === 'add_payment') {
        $amount = (float) str_replace(['Rp', '.', ' ', ','], ['', '', '', '.'], $_POST['amount'] ?? '0');
        $payment_date = $_POST['payment_date'] ?? '';
        $method = $_POST['method'] ?? '';
        $notes = trim($_POST['notes'] ?? '');
        
        // Validate
        if ($amount <= 0) {
            setFlashMessage('danger', 'Jumlah pembayaran harus lebih dari 0.');
            redirect("deal-detail.php?id=$deal_id");
        }
        if (empty($payment_date)) {
            setFlashMessage('danger', 'Tanggal pembayaran wajib diisi.');
            redirect("deal-detail.php?id=$deal_id");
        }
        
        // Check ownership and get final_value and deal_title
        $stmt = mysqli_prepare($conn, "SELECT deal_title, final_value FROM deals WHERE id = ? AND business_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $deal_id, $business_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $deal_check = mysqli_fetch_assoc($result);
        
        if (!$deal_check) {
            setFlashMessage('danger', 'Deal tidak ditemukan.');
            redirect('deals.php');
        }
        
        // Check total paid so far
        $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount), 0) AS total_paid FROM deal_payments WHERE deal_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $deal_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $paid_so_far = (float) mysqli_fetch_assoc($result)['total_paid'];
        
        // Overpayment check
        if (($paid_so_far + $amount) > $deal_check['final_value'] + 0.01) {
            $remaining = $deal_check['final_value'] - $paid_so_far;
            setFlashMessage('danger', 'Jumlah pembayaran melebihi sisa tagihan (' . formatCurrency($remaining) . ').');
            redirect("deal-detail.php?id=$deal_id");
        }
        
        // Insert payment to transactions first
        $trans_type = 'Income';
        $trans_category = 'Deal Payment';
        $trans_title = "Pembayaran Deal: " . ($deal_check['deal_title'] ?? "ID $deal_id");
        
        $stmt_trans = mysqli_prepare($conn, 
            "INSERT INTO transactions (business_id, type, title, category, amount, transaction_date, method, notes, deal_id) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt_trans, "isssdsssi", 
            $business_id, $trans_type, $trans_title, $trans_category, $amount, $payment_date, $method, $notes, $deal_id
        );
        
        if (mysqli_stmt_execute($stmt_trans)) {
            $transaction_id = mysqli_insert_id($conn);
            
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO deal_payments (deal_id, amount, payment_date, method, notes, transaction_id) VALUES (?, ?, ?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "idsssi", $deal_id, $amount, $payment_date, $method, $notes, $transaction_id);
            mysqli_stmt_execute($stmt);
            
            setFlashMessage('success', 'Pembayaran berhasil ditambahkan.');
        } else {
            setFlashMessage('danger', 'Gagal menambahkan pembayaran.');
        }
        
        redirect("deal-detail.php?id=$deal_id");
    }

    // --- Action: Delete Payment ---
    if ($action === 'delete_payment') {
        $payment_id = (int)($_POST['payment_id'] ?? 0);
        
        // Verify payment belongs to this deal and business and get transaction_id
        $stmt = mysqli_prepare($conn, 
            "SELECT dp.id, dp.transaction_id FROM deal_payments dp 
             JOIN deals d ON dp.deal_id = d.id 
             WHERE dp.id = ? AND dp.deal_id = ? AND d.business_id = ?"
        );
        mysqli_stmt_bind_param($stmt, "iii", $payment_id, $deal_id, $business_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 0) {
            setFlashMessage('danger', 'Pembayaran tidak ditemukan.');
            redirect("deal-detail.php?id=$deal_id");
        }
        
        $payment_record = mysqli_fetch_assoc($result);
        $success = false;
        
        if ($payment_record['transaction_id']) {
            // Delete the transaction, which will cascade delete the deal_payment
            $stmt = mysqli_prepare($conn, "DELETE FROM transactions WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $payment_record['transaction_id']);
            $success = mysqli_stmt_execute($stmt);
        } else {
            // Fallback for older payments that don't have transaction_id
            $stmt = mysqli_prepare($conn, "DELETE FROM deal_payments WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $payment_id);
            $success = mysqli_stmt_execute($stmt);
        }
        
        if ($success) {
            setFlashMessage('success', 'Pembayaran berhasil dihapus.');
        } else {
            setFlashMessage('danger', 'Gagal menghapus pembayaran.');
        }
        
        redirect("deal-detail.php?id=$deal_id");
    }

    // --- Action: Add Activity Log ---
    if ($action === 'add_activity_log') {
        $note = trim($_POST['note'] ?? '');
        
        if (empty($note)) {
            setFlashMessage('danger', 'Catatan tidak boleh kosong.');
            redirect("deal-detail.php?id=$deal_id");
        }
        
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO activity_logs (business_id, deal_id, note) VALUES (?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "iis", $business_id, $deal_id, $note);
        
        if (mysqli_stmt_execute($stmt)) {
            setFlashMessage('success', 'Catatan aktivitas berhasil ditambahkan.');
        } else {
            setFlashMessage('danger', 'Gagal menambahkan catatan aktivitas.');
        }
        
        redirect("deal-detail.php?id=$deal_id");
    }
}

// ============================================================
// Fetch Data for Display
// ============================================================

// 1. Deal + Client + Service
$stmt = mysqli_prepare($conn, "
    SELECT d.*, 
           c.client_name, c.company, c.email AS client_email, c.phone AS client_phone, c.id AS cid,
           s.service_name
    FROM deals d
    LEFT JOIN clients c ON d.client_id = c.id
    LEFT JOIN services s ON d.service_id = s.id
    WHERE d.id = ? AND d.business_id = ?
");
mysqli_stmt_bind_param($stmt, "ii", $deal_id, $business_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$deal = mysqli_fetch_assoc($result);

if (!$deal) {
    setFlashMessage('danger', 'Deal tidak ditemukan.');
    redirect('deals.php');
}

$page_title = 'Detail: ' . $deal['deal_title'];

// 2. Payment summary
$stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount), 0) AS total_paid FROM deal_payments WHERE deal_id = ?");
mysqli_stmt_bind_param($stmt, "i", $deal_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total_paid = (float) mysqli_fetch_assoc($result)['total_paid'];
$remaining = $deal['final_value'] - $total_paid;

if ($total_paid <= 0) {
    $payment_status = 'Belum Bayar';
    $payment_status_class = 'danger';
} elseif ($total_paid < $deal['final_value']) {
    $payment_status = 'Partial';
    $payment_status_class = 'warning';
} else {
    $payment_status = 'Lunas';
    $payment_status_class = 'success';
}

// Calculate payment percentage for progress bar
$payment_percent = $deal['final_value'] > 0 ? min(100, ($total_paid / $deal['final_value']) * 100) : 0;

// 3. Payment list
$stmt = mysqli_prepare($conn, "SELECT * FROM deal_payments WHERE deal_id = ? ORDER BY payment_date DESC, created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $deal_id);
mysqli_stmt_execute($stmt);
$payments = mysqli_stmt_get_result($stmt);

// 4. Stage history
$stmt = mysqli_prepare($conn, "SELECT * FROM deal_stage_history WHERE deal_id = ? ORDER BY changed_at DESC");
mysqli_stmt_bind_param($stmt, "i", $deal_id);
mysqli_stmt_execute($stmt);
$stage_history = mysqli_stmt_get_result($stmt);

// 5. Activity logs
$stmt = mysqli_prepare($conn, "SELECT * FROM activity_logs WHERE deal_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $deal_id);
mysqli_stmt_execute($stmt);
$activity_logs = mysqli_stmt_get_result($stmt);

// Stage definitions
$stages = ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost'];
$stage_colors = [
    'Lead' => '#6B7280',
    'Qualified' => '#3B82F6',
    'Proposal' => '#F59E0B',
    'Negotiation' => '#8B5CF6',
    'Won' => '#10B981',
    'Lost' => '#EF4444'
];
$stage_icons = [
    'Lead' => 'fa-flag',
    'Qualified' => 'fa-check-circle',
    'Proposal' => 'fa-file-alt',
    'Negotiation' => 'fa-comments',
    'Won' => 'fa-trophy',
    'Lost' => 'fa-times-circle'
];

$current_stage_index = array_search($deal['current_stage'], $stages);
$is_final = in_array($deal['current_stage'], ['Won', 'Lost']);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="deals.php">Deals</a></li>
                    <li class="breadcrumb-item active"><?= e($deal['deal_title']) ?></li>
                </ol>
            </nav>
            <h2 class="page-title mb-0"><?= e($deal['deal_title']) ?></h2>
        </div>
        <div class="d-flex gap-2">
            <a href="deals.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <a href="invoice-form.php?deal_id=<?= $deal['id'] ?>" class="btn btn-outline-primary">
                <i class="fas fa-file-invoice me-2"></i>Buat Invoice
            </a>
            <a href="deal-form.php?id=<?= $deal['id'] ?>" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit Deal
            </a>
        </div>
    </div>
    
    <?php
    $flash = getFlashMessage();
    if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Stage Progress Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-stream me-2"></i>Pipeline Stage
                </h5>
                <span class="badge badge-<?= strtolower($deal['current_stage']) ?> px-3 py-2" style="font-size: 0.9rem;">
                    <i class="fas <?= $stage_icons[$deal['current_stage']] ?> me-1"></i>
                    <?= e($deal['current_stage']) ?>
                </span>
            </div>
            
            <!-- Progress Steps -->
            <div class="stage-progress mb-3">
                <?php 
                $linear_stages = ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won'];
                foreach ($linear_stages as $i => $stage): 
                    $is_active = ($stage === $deal['current_stage']);
                    $is_passed = ($current_stage_index > $i && $deal['current_stage'] !== 'Lost');
                    $is_lost = ($deal['current_stage'] === 'Lost');
                ?>
                <div class="stage-step <?= $is_active ? 'active' : '' ?> <?= $is_passed ? 'passed' : '' ?> <?= $is_lost && $stage === 'Won' ? 'lost' : '' ?>">
                    <div class="stage-dot" style="<?= $is_active ? 'background:' . $stage_colors[$stage] : '' ?>">
                        <?php if ($is_passed): ?>
                            <i class="fas fa-check"></i>
                        <?php elseif ($is_active): ?>
                            <i class="fas <?= $stage_icons[$stage] ?>"></i>
                        <?php endif; ?>
                    </div>
                    <span class="stage-label"><?= $stage ?></span>
                    <?php if ($i < count($linear_stages) - 1): ?>
                        <div class="stage-line <?= $is_passed ? 'passed' : '' ?>"></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($deal['current_stage'] === 'Lost'): ?>
                <div class="alert alert-danger mb-3 py-2">
                    <i class="fas fa-times-circle me-2"></i>Deal ini ditandai sebagai <strong>Lost</strong>
                    <?php if ($deal['closed_at']): ?>
                        pada <?= formatDate($deal['closed_at'], true) ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Move Stage Form -->
            <?php if (!$is_final): ?>
            <form method="POST" action="" class="d-flex align-items-center gap-2 mt-2">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="action" value="move_stage">
                <label class="form-label mb-0 me-2 text-muted">Pindah ke:</label>
                <select class="form-select form-select-sm" name="new_stage" style="width: auto; min-width: 160px;">
                    <?php foreach ($stages as $stage): ?>
                        <?php if ($stage !== $deal['current_stage']): ?>
                        <option value="<?= $stage ?>"><?= $stage ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Yakin ingin memindahkan stage deal ini?')">
                    <i class="fas fa-arrow-right me-1"></i>Pindah Stage
                </button>
            </form>
            <?php else: ?>
                <p class="text-muted mb-0 mt-2">
                    <i class="fas fa-lock me-1"></i>Stage sudah final dan tidak bisa diubah.
                </p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Deal Info + Client Info -->
    <div class="row g-4 mb-4">
        <!-- Deal Info -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-info-circle me-2"></i>Informasi Deal
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 180px;">Judul Deal</td>
                                    <td><strong><?= e($deal['deal_title']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Paket Jasa</td>
                                    <td><?= e($deal['service_name'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nilai Deal</td>
                                    <td>
                                        <?php if ($deal['discount_percent'] > 0): ?>
                                            <span class="text-decoration-line-through text-muted"><?= formatCurrency($deal['deal_value']) ?></span>
                                        <?php else: ?>
                                            <?= formatCurrency($deal['deal_value']) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($deal['discount_percent'] > 0): ?>
                                <tr>
                                    <td class="text-muted">Diskon</td>
                                    <td>
                                        <span class="badge bg-success"><?= $deal['discount_percent'] ?>%</span>
                                        <span class="text-muted ms-1">(- <?= formatCurrency($deal['deal_value'] * $deal['discount_percent'] / 100) ?>)</span>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td class="text-muted">Nilai Akhir</td>
                                    <td><strong class="text-primary fs-5"><?= formatCurrency($deal['final_value']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Expected Close</td>
                                    <td>
                                        <?php if ($deal['expected_close_date']): ?>
                                            <?= formatDate($deal['expected_close_date']) ?>
                                            <?php 
                                            $days_left = (int)((strtotime($deal['expected_close_date']) - time()) / 86400);
                                            if (!$is_final): ?>
                                                <?php if ($days_left < 0): ?>
                                                    <span class="badge bg-danger ms-1">Lewat <?= abs($days_left) ?> hari</span>
                                                <?php elseif ($days_left <= 7): ?>
                                                    <span class="badge bg-warning ms-1"><?= $days_left ?> hari lagi</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Dibuat</td>
                                    <td><?= formatDate($deal['created_at'], true) ?></td>
                                </tr>
                                <?php if ($deal['closed_at']): ?>
                                <tr>
                                    <td class="text-muted">Tanggal Ditutup</td>
                                    <td><?= formatDate($deal['closed_at'], true) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($deal['notes']): ?>
                                <tr>
                                    <td class="text-muted">Catatan</td>
                                    <td><?= nl2br(e($deal['notes'])) ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Client Info -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-user me-2"></i>Informasi Klien
                    </h5>
                    <div class="text-center mb-3">
                        <div class="client-avatar-lg mx-auto mb-2">
                            <?= strtoupper(substr($deal['client_name'] ?? '?', 0, 1)) ?>
                        </div>
                        <h6 class="mb-0"><?= e($deal['client_name']) ?></h6>
                        <?php if ($deal['company']): ?>
                            <small class="text-muted"><?= e($deal['company']) ?></small>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <div class="client-details">
                        <?php if ($deal['client_email']): ?>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-envelope text-muted me-2" style="width: 20px;"></i>
                            <a href="mailto:<?= e($deal['client_email']) ?>" class="text-decoration-none">
                                <?= e($deal['client_email']) ?>
                            </a>
                        </div>
                        <?php endif; ?>
                        <?php if ($deal['client_phone']): ?>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-phone text-muted me-2" style="width: 20px;"></i>
                            <a href="tel:<?= e($deal['client_phone']) ?>" class="text-decoration-none">
                                <?= e($deal['client_phone']) ?>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="d-grid mt-3">
                        <a href="client-form.php?id=<?= $deal['cid'] ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i>Lihat Detail Klien
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2"></i>Pembayaran
                    <span class="badge bg-<?= $payment_status_class ?> ms-2"><?= $payment_status ?></span>
                </h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="fas fa-plus me-1"></i>Tambah Pembayaran
                </button>
            </div>
            
            <!-- Payment Summary Cards -->
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="payment-summary-card">
                        <small class="text-muted">Total Tagihan</small>
                        <div class="fw-bold"><?= formatCurrency($deal['final_value']) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="payment-summary-card bg-success bg-opacity-10">
                        <small class="text-muted">Sudah Dibayar</small>
                        <div class="fw-bold text-success"><?= formatCurrency($total_paid) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="payment-summary-card <?= $remaining > 0 ? 'bg-danger bg-opacity-10' : 'bg-success bg-opacity-10' ?>">
                        <small class="text-muted">Sisa Tagihan</small>
                        <div class="fw-bold <?= $remaining > 0 ? 'text-danger' : 'text-success' ?>"><?= formatCurrency(max(0, $remaining)) ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Progress Bar -->
            <div class="progress mb-4" style="height: 8px;">
                <div class="progress-bar bg-success" role="progressbar" 
                     style="width: <?= $payment_percent ?>%"
                     aria-valuenow="<?= $payment_percent ?>" 
                     aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            
            <!-- Payment Table -->
            <?php if (mysqli_num_rows($payments) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Metode</th>
                            <th>Catatan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = mysqli_fetch_assoc($payments)): ?>
                        <tr>
                            <td><?= formatDate($payment['payment_date']) ?></td>
                            <td><strong class="text-success"><?= formatCurrency($payment['amount']) ?></strong></td>
                            <td>
                                <span class="badge bg-light text-dark"><?= e($payment['method'] ?? '-') ?></span>
                            </td>
                            <td>
                                <small class="text-muted"><?= e($payment['notes'] ?? '-') ?></small>
                            </td>
                            <td class="text-end">
                                <form method="POST" action="" class="d-inline" 
                                      onsubmit="return confirm('Yakin ingin menghapus pembayaran ini?')">
                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                    <input type="hidden" name="action" value="delete_payment">
                                    <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-receipt fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">Belum ada pembayaran untuk deal ini.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Stage History -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-history me-2"></i>Riwayat Stage
            </h5>
            
            <?php if (mysqli_num_rows($stage_history) > 0): ?>
            <div class="stage-timeline">
                <?php while ($history = mysqli_fetch_assoc($stage_history)): ?>
                <div class="timeline-item">
                    <div class="timeline-dot" style="background: <?= $stage_colors[$history['to_stage']] ?? '#6B7280' ?>"></div>
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <?php if ($history['from_stage']): ?>
                                    <span class="badge badge-<?= strtolower($history['from_stage']) ?>"><?= e($history['from_stage']) ?></span>
                                    <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                <?php else: ?>
                                    <span class="text-muted">Baru dibuat</span>
                                    <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                <?php endif; ?>
                                <span class="badge badge-<?= strtolower($history['to_stage']) ?>"><?= e($history['to_stage']) ?></span>
                            </div>
                            <small class="text-muted"><?= formatDate($history['changed_at'], true) ?></small>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-stream fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">Belum ada riwayat perpindahan stage.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Activity Log -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-clipboard-list me-2"></i>Catatan Aktivitas
            </h5>
            
            <form method="POST" action="" class="mb-4">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="action" value="add_activity_log">
                <div class="mb-2">
                    <textarea class="form-control bg-light" name="note" rows="2" placeholder="Tulis catatan atau log aktivitas baru..." required></textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-paper-plane me-1"></i>Kirim Catatan
                    </button>
                </div>
            </form>
            
            <?php if (mysqli_num_rows($activity_logs) > 0): ?>
            <div class="activity-timeline">
                <?php while ($log = mysqli_fetch_assoc($activity_logs)): ?>
                <div class="d-flex mb-3 border-bottom pb-3">
                    <div class="flex-shrink-0 mt-1">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fas fa-comment fa-sm"></i>
                        </div>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">
                            <i class="far fa-clock me-1"></i><?= formatDate($log['created_at'], true) ?>
                        </div>
                        <div class="text-dark bg-light p-3 rounded">
                            <?= nl2br(e($log['note'])) ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-4 border rounded bg-light">
                <i class="fas fa-comments fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">Belum ada catatan aktivitas.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="action" value="add_payment">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Pembayaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 mb-3">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Sisa tagihan: <strong><?= formatCurrency(max(0, $remaining)) ?></strong>
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">
                            Jumlah <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="payment_amount" name="amount" 
                               placeholder="Rp 1.000.000" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">
                            Tanggal Pembayaran <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Metode Pembayaran</label>
                        <select class="form-select" id="payment_method" name="method">
                            <option value="">Pilih metode...</option>
                            <option value="Transfer">Transfer Bank</option>
                            <option value="Cash">Cash</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="payment_notes" name="notes" rows="2" 
                                  placeholder="Catatan pembayaran (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Stage Progress Bar */
.stage-progress {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0;
    overflow-x: auto;
}

.stage-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    flex: 1;
    min-width: 80px;
}

.stage-dot {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #E5E7EB;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
    z-index: 2;
    transition: all 0.3s ease;
}

.stage-step.passed .stage-dot {
    background: #10B981;
}

.stage-step.active .stage-dot {
    box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.2);
    transform: scale(1.1);
}

.stage-step.lost .stage-dot {
    background: #EF4444;
}

.stage-label {
    font-size: 0.75rem;
    font-weight: 500;
    color: #9CA3AF;
    margin-top: 0.5rem;
    text-align: center;
}

.stage-step.passed .stage-label,
.stage-step.active .stage-label {
    color: #374151;
    font-weight: 600;
}

.stage-line {
    position: absolute;
    top: 18px;
    left: calc(50% + 18px);
    right: calc(-50% + 18px);
    height: 3px;
    background: #E5E7EB;
    z-index: 1;
}

.stage-line.passed {
    background: #10B981;
}

/* Client Avatar */
.client-avatar-lg {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
}

/* Payment Summary Cards */
.payment-summary-card {
    background: #F9FAFB;
    border-radius: 10px;
    padding: 1rem;
    text-align: center;
}

.payment-summary-card .fw-bold {
    font-size: 1.1rem;
    margin-top: 0.25rem;
}

/* Stage Timeline */
.stage-timeline {
    position: relative;
    padding-left: 24px;
}

.stage-timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #E5E7EB;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.25rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -20px;
    top: 6px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #6B7280;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #E5E7EB;
}

.timeline-content {
    padding: 0.5rem 0.75rem;
    background: #F9FAFB;
    border-radius: 8px;
}

/* Stage Badges (reuse from deals.php) */
.badge-lead { background-color: #6B7280; color: white; }
.badge-qualified { background-color: #3B82F6; color: white; }
.badge-proposal { background-color: #F59E0B; color: white; }
.badge-negotiation { background-color: #8B5CF6; color: white; }
.badge-won { background-color: #10B981; color: white; }
.badge-lost { background-color: #EF4444; color: white; }

/* Responsive */
@media (max-width: 768px) {
    .stage-progress {
        overflow-x: auto;
        padding-bottom: 1rem;
    }
    
    .stage-step {
        min-width: 60px;
    }
    
    .stage-label {
        font-size: 0.65rem;
    }
}
</style>

<script>
// Format currency input for payment amount
document.getElementById('payment_amount')?.addEventListener('blur', function() {
    let value = this.value.replace(/[^\d]/g, '');
    if (value) {
        this.value = new Intl.NumberFormat('id-ID').format(value);
    }
});

// Initialize tooltips
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el);
});
</script>

<?php include 'includes/footer.php'; ?>
