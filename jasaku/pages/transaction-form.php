<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Transaksi';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$businessId = getCurrentBusinessId();
$transactionId = $_GET['id'] ?? null;
$defaultType = $_GET['type'] ?? 'Income';

// Income categories
$incomeCategories = ['Deal Payment', 'Lainnya'];
// Expense categories
$expenseCategories = ['Operasional', 'Marketing', 'Tools', 'Lainnya'];

// Get existing transaction if editing
$transaction = null;
if ($transactionId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM transactions WHERE id = ? AND business_id = ?");
    $stmt->bind_param("ii", $transactionId, $businessId);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();
    $stmt->close();
    
    if ($transaction) {
        $defaultType = $transaction['type'];
    }
}

// Get deals for linking
$deals = getDeals($businessId);
$wonDeals = array_filter($deals, function($d) { return $d['current_stage'] === 'Won'; });

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'Income';
    $title = trim($_POST['title'] ?? '');
    $category = $_POST['category'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $transactionDate = $_POST['transaction_date'] ?? date('Y-m-d');
    $method = $_POST['method'] ?? '';
    $notes = trim($_POST['notes'] ?? '');
    $dealId = $_POST['deal_id'] ?? null;
    
    if (empty($title)) {
        $error = 'Judul wajib diisi';
    } elseif ($amount <= 0) {
        $error = 'Jumlah harus lebih dari 0';
    } else {
        $data = [
            'type' => $type,
            'title' => $title,
            'category' => $category,
            'amount' => $amount,
            'transaction_date' => $transactionDate,
            'method' => $method,
            'notes' => $notes,
            'deal_id' => $dealId
        ];
        
        if ($transaction) {
            $db = getDB();
            $stmt = $db->prepare("UPDATE transactions SET 
                                  type = ?, title = ?, category = ?, amount = ?, 
                                  transaction_date = ?, method = ?, notes = ?, deal_id = ? 
                                  WHERE id = ?");
            $stmt->bind_param("sssdsss", $type, $title, $category, $amount, $transactionDate, $method, $notes, $dealId, $transactionId);
            
            if ($stmt->execute()) {
                $success = 'Transaksi berhasil diupdate';
                $transaction = array_merge($transaction, $data);
            } else {
                $error = 'Gagal update transaksi';
            }
            $stmt->close();
        } else {
            $result = createTransaction($businessId, $data);
            if ($result['success']) {
                header('Location: finance.php');
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>

<div class="main-content">
    <div class="page-header">
        <h2><?= $transaction ? 'Edit' : 'Tambah' ?> Transaksi</h2>
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
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="type" class="form-label">Jenis Transaksi</label>
                            <select class="form-select" id="type" name="type" onchange="updateCategories()">
                                <option value="Income" <?= $defaultType === 'Income' ? 'selected' : '' ?>>Pemasukan</option>
                                <option value="Expense" <?= $defaultType === 'Expense' ? 'selected' : '' ?>>Pengeluaran</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required value="<?= sanitize($transaction['title'] ?? $_POST['title'] ?? '') ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Kategori</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($incomeCategories as $cat): ?>
                                    <option value="<?= $cat ?>" class="income-cat" <?= ($transaction['category'] ?? $_POST['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                    <?php endforeach; ?>
                                    <?php foreach ($expenseCategories as $cat): ?>
                                    <option value="<?= $cat ?>" class="expense-cat" <?= ($transaction['category'] ?? $_POST['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="amount" name="amount" required min="0" step="0.01" value="<?= sanitize($transaction['amount'] ?? $_POST['amount'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="transaction_date" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="transaction_date" name="transaction_date" value="<?= sanitize($transaction['transaction_date'] ?? $_POST['transaction_date'] ?? date('Y-m-d')) ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="method" class="form-label">Metode Pembayaran</label>
                                <select class="form-select" id="method" name="method">
                                    <option value="">Pilih Metode</option>
                                    <option value="Transfer" <?= ($transaction['method'] ?? $_POST['method'] ?? '') === 'Transfer' ? 'selected' : '' ?>>Transfer</option>
                                    <option value="Cash" <?= ($transaction['method'] ?? $_POST['method'] ?? '') === 'Cash' ? 'selected' : '' ?>>Cash</option>
                                    <option value="QRIS" <?= ($transaction['method'] ?? $_POST['method'] ?? '') === 'QRIS' ? 'selected' : '' ?>>QRIS</option>
                                    <option value="Lainnya" <?= ($transaction['method'] ?? $_POST['method'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>
                        </div>
                        
                        <?php if ($defaultType === 'Income' && count($wonDeals) > 0): ?>
                        <div class="mb-3">
                            <label for="deal_id" class="form-label">Link ke Deal (Opsional)</label>
                            <select class="form-select" id="deal_id" name="deal_id">
                                <option value="">Pilih Deal</option>
                                <?php foreach ($wonDeals as $deal): ?>
                                <option value="<?= $deal['id'] ?>" <?= ($transaction['deal_id'] ?? $_POST['deal_id'] ?? '') == $deal['id'] ? 'selected' : '' ?>>
                                    <?= sanitize($deal['deal_title']) ?> - <?= formatCurrency($deal['final_value']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?= sanitize($transaction['notes'] ?? $_POST['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <a href="finance.php" class="btn btn-outline-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateCategories() {
    const type = document.getElementById('type').value;
    const categorySelect = document.getElementById('category');
    const options = categorySelect.querySelectorAll('option');
    
    options.forEach(opt => {
        if (opt.classList.contains('income-cat') || opt.classList.contains('expense-cat')) {
            opt.style.display = 'none';
        }
    });
    
    const visibleClass = type === 'Income' ? 'income-cat' : 'expense-cat';
    options.forEach(opt => {
        if (opt.classList.contains(visibleClass)) {
            opt.style.display = '';
        }
    });
    
    // Reset selection
    categorySelect.value = '';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCategories();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
