<?php
/**
 * Transaction Form Page (Create/Edit Income or Expense)
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$business_id = getCurrentBusinessId();
if (!$business_id) {
    redirect('setup-business.php');
}

// Handle delete action (POST + CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($csrf_token)) {
        setFlashMessage('danger', 'Token CSRF tidak valid. Silakan coba lagi.');
        redirect('finance.php');
    }
    
    $trans_id = (int)$_POST['id'];
    $type = $_POST['type'] ?? 'income';
    
    // Verify ownership (prepared statement)
    $check_stmt = mysqli_prepare($conn, "SELECT id FROM transactions WHERE id = ? AND business_id = ?");
    mysqli_stmt_bind_param($check_stmt, "ii", $trans_id, $business_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        $del_stmt = mysqli_prepare($conn, "DELETE FROM transactions WHERE id = ?");
        mysqli_stmt_bind_param($del_stmt, "i", $trans_id);
        mysqli_stmt_execute($del_stmt);
        setFlashMessage('success', 'Transaksi berhasil dihapus.');
    } else {
        setFlashMessage('danger', 'Transaksi tidak ditemukan.');
    }
    
    redirect("finance.php?tab=$type");
}

$trans_id = $_GET['id'] ?? null;
$is_edit = !empty($trans_id);
$type = $_GET['type'] ?? 'income'; // income or expense
$type = in_array($type, ['income', 'expense']) ? $type : 'income';

$page_title = $is_edit ? 'Edit Transaksi' : 'Tambah Transaksi ' . ($type === 'income' ? 'Pemasukan' : 'Pengeluaran');

$error = '';
$transaction = null;

// Load transaction data if editing
if ($is_edit) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM transactions WHERE id = ? AND business_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $trans_id, $business_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $transaction = mysqli_fetch_assoc($result);
    
    if (!$transaction) {
        setFlashMessage('danger', 'Transaksi tidak ditemukan.');
        redirect('finance.php');
    }
    
    // Set type based on transaction type
    $type = strtolower($transaction['type']);
}

// Get won deals for income category
$won_deals = [];
if ($type === 'income') {
    $won_deals_stmt = mysqli_prepare($conn, 
        "SELECT d.id, d.deal_title, c.client_name 
         FROM deals d 
         JOIN clients c ON d.client_id = c.id 
         WHERE d.business_id = ? AND d.current_stage = 'Won'
         ORDER BY d.updated_at DESC"
    );
    mysqli_stmt_bind_param($won_deals_stmt, "i", $business_id);
    mysqli_stmt_execute($won_deals_stmt);
    $won_deals_result = mysqli_stmt_get_result($won_deals_stmt);
    while ($deal = mysqli_fetch_assoc($won_deals_result)) {
        $won_deals[] = $deal;
    }
}

// Category options
$income_categories = ['Deal Payment', 'Lainnya'];
$expense_categories = ['Operasional', 'Marketing', 'Tools', 'Lainnya'];
$categories = $type === 'income' ? $income_categories : $expense_categories;

// Payment methods
$payment_methods = ['Transfer', 'Cash', 'QRIS', 'Lainnya'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = $_POST['category'] ?? '';
    $amount = trim($_POST['amount'] ?? '');
    $transaction_date = $_POST['transaction_date'] ?? '';
    $method = $_POST['method'] ?? '';
    $notes = trim($_POST['notes'] ?? '');
    $deal_id = !empty($_POST['deal_id']) ? (int)$_POST['deal_id'] : null;
    
    // Validation
    if (empty($title)) {
        $error = 'Judul transaksi wajib diisi.';
    } elseif (empty($category)) {
        $error = 'Kategori wajib dipilih.';
    } elseif (empty($amount) || !is_numeric($amount) || $amount <= 0) {
        $error = 'Jumlah harus berupa angka valid dan lebih dari 0.';
    } elseif (empty($transaction_date)) {
        $error = 'Tanggal transaksi wajib diisi.';
    } else {
        $trans_type = $type === 'income' ? 'Income' : 'Expense';
        
        if ($is_edit) {
            // Update transaction
            $stmt = mysqli_prepare($conn, 
                "UPDATE transactions SET 
                    title = ?, 
                    category = ?, 
                    amount = ?, 
                    transaction_date = ?,
                    method = ?,
                    notes = ?,
                    deal_id = ?
                 WHERE id = ? AND business_id = ?"
            );
            mysqli_stmt_bind_param($stmt, "ssdssssii", 
                $title, $category, $amount, $transaction_date, $method, $notes, $deal_id,
                $trans_id, $business_id
            );
            
            if (mysqli_stmt_execute($stmt)) {
                setFlashMessage('success', 'Transaksi berhasil diupdate.');
                redirect("finance.php?tab=$type");
            } else {
                $error = 'Gagal mengupdate transaksi.';
            }
        } else {
            // Insert new transaction
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO transactions (business_id, type, title, category, amount, transaction_date, method, notes, deal_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "isssdsssi", 
                $business_id, $trans_type, $title, $category, $amount, $transaction_date, $method, $notes, $deal_id
            );
            
            if (mysqli_stmt_execute($stmt)) {
                setFlashMessage('success', 'Transaksi berhasil ditambahkan.');
                redirect("finance.php?tab=$type");
            } else {
                $error = 'Gagal menambahkan transaksi.';
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
                    <p class="text-muted mb-0">Catat transaksi keuangan bisnis Anda</p>
                </div>
                <a href="finance.php?tab=<?= $type ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>
            
            <!-- Transaction Form -->
            <div class="card">
                <div class="card-header bg-<?= $type === 'income' ? 'success' : 'danger' ?> bg-opacity-10">
                    <h5 class="mb-0">
                        <i class="fas fa-arrow-<?= $type === 'income' ? 'up' : 'down' ?> me-2"></i>
                        Form Transaksi <?= $type === 'income' ? 'Pemasukan' : 'Pengeluaran' ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <input type="hidden" name="type" value="<?= $type ?>">
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label">
                                    Judul Transaksi <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="title" 
                                       name="title" 
                                       placeholder="<?= $type === 'income' ? 'Contoh: Pembayaran DP Project Website' : 'Contoh: Pembelian Lisensi Software' ?>"
                                       value="<?= e($transaction['title'] ?? '') ?>"
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">
                                    Kategori <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Pilih kategori...</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat ?>" <?= ($transaction['category'] ?? '') === $cat ? 'selected' : '' ?>>
                                            <?= $cat ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">
                                    Jumlah <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="amount" 
                                           name="amount" 
                                           placeholder="0"
                                           step="0.01"
                                           min="0.01"
                                           value="<?= $transaction['amount'] ?? '' ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="transaction_date" class="form-label">
                                    Tanggal Transaksi <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="transaction_date" 
                                       name="transaction_date" 
                                       value="<?= $transaction['transaction_date'] ?? date('Y-m-d') ?>"
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="method" class="form-label">Metode Pembayaran</label>
                                <select class="form-select" id="method" name="method">
                                    <option value="">Pilih metode...</option>
                                    <?php foreach ($payment_methods as $pm): ?>
                                        <option value="<?= $pm ?>" <?= ($transaction['method'] ?? '') === $pm ? 'selected' : '' ?>>
                                            <?= $pm ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <?php if ($type === 'income' && count($won_deals) > 0): ?>
                            <div class="col-md-12 mb-3">
                                <label for="deal_id" class="form-label">
                                    Deal Terkait <small class="text-muted">(Opsional)</small>
                                </label>
                                <select class="form-select" id="deal_id" name="deal_id">
                                    <option value="">Tidak terkait dengan deal...</option>
                                    <?php foreach ($won_deals as $deal): ?>
                                        <option value="<?= $deal['id'] ?>" <?= ($transaction['deal_id'] ?? '') == $deal['id'] ? 'selected' : '' ?>>
                                            <?= e($deal['deal_title']) ?> - <?= e($deal['client_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">
                                    Pilih deal yang sudah Won jika transaksi ini merupakan pembayaran dari deal tersebut
                                </small>
                            </div>
                            <?php endif; ?>
                            
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">Catatan</label>
                                <textarea class="form-control" 
                                          id="notes" 
                                          name="notes" 
                                          rows="4"
                                          placeholder="Catatan tambahan tentang transaksi ini..."><?= e($transaction['notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between">
                            <a href="finance.php?tab=<?= $type ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-<?= $type === 'income' ? 'success' : 'danger' ?>">
                                <i class="fas fa-save me-2"></i>
                                <?= $is_edit ? 'Update Transaksi' : 'Simpan Transaksi' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if ($is_edit): ?>
            <!-- Additional Info Card -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title mb-3">Informasi Tambahan</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Dibuat pada:</small>
                            <p class="mb-0"><?= formatDate($transaction['created_at']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Terakhir diupdate:</small>
                            <p class="mb-0"><?= formatDate($transaction['updated_at']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
