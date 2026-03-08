<?php
/**
 * Invoice Detail Page
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$page_title = 'Detail Invoice';
$business_id = getCurrentBusinessId();

if (!$business_id) {
    redirect('setup-business.php');
}

$invoice_id = (int)($_GET['id'] ?? 0);
if (!$invoice_id) {
    setFlashMessage('danger', 'Invoice tidak ditemukan.');
    redirect('invoices.php');
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('danger', 'Token keamanan tidak valid.');
        redirect("invoice-detail.php?id=$invoice_id");
    }
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_status') {
        $new_status = $_POST['new_status'] ?? '';
        $valid_statuses = ['Draft', 'Sent', 'Paid', 'Overdue'];
        
        if (!in_array($new_status, $valid_statuses)) {
            setFlashMessage('danger', 'Status tidak valid.');
            redirect("invoice-detail.php?id=$invoice_id");
        }
        
        $stmt = mysqli_prepare($conn, "UPDATE invoices SET status = ? WHERE id = ? AND business_id = ?");
        mysqli_stmt_bind_param($stmt, "sii", $new_status, $invoice_id, $business_id);
        
        if (mysqli_stmt_execute($stmt)) {
            setFlashMessage('success', "Status invoice berhasil diubah menjadi $new_status.");
        } else {
            setFlashMessage('danger', 'Gagal mengubah status.');
        }
        redirect("invoice-detail.php?id=$invoice_id");
    }
    
    if ($action === 'delete') {
        $stmt = mysqli_prepare($conn, "DELETE FROM invoices WHERE id = ? AND business_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $invoice_id, $business_id);
        
        if (mysqli_stmt_execute($stmt) && mysqli_affected_rows($conn) > 0) {
            setFlashMessage('success', 'Invoice berhasil dihapus.');
            redirect('invoices.php');
        } else {
            setFlashMessage('danger', 'Gagal menghapus invoice.');
            redirect("invoice-detail.php?id=$invoice_id");
        }
    }
}

// Fetch invoice with client and deal info
$stmt = mysqli_prepare($conn, "
    SELECT inv.*, 
           c.client_name, c.company, c.email AS client_email, c.phone AS client_phone, c.address AS client_address,
           d.deal_title, d.final_value AS deal_value,
           b.business_name, b.phone AS biz_phone, b.email AS biz_email, b.address AS biz_address, b.logo_path
    FROM invoices inv
    LEFT JOIN clients c ON inv.client_id = c.id
    LEFT JOIN deals d ON inv.deal_id = d.id
    LEFT JOIN businesses b ON inv.business_id = b.id
    WHERE inv.id = ? AND inv.business_id = ?
");
mysqli_stmt_bind_param($stmt, "ii", $invoice_id, $business_id);
mysqli_stmt_execute($stmt);
$invoice = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$invoice) {
    setFlashMessage('danger', 'Invoice tidak ditemukan.');
    redirect('invoices.php');
}

$page_title = 'Invoice: ' . $invoice['invoice_number'];

// Fetch items
$stmt = mysqli_prepare($conn, "SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC");
mysqli_stmt_bind_param($stmt, "i", $invoice_id);
mysqli_stmt_execute($stmt);
$items = mysqli_stmt_get_result($stmt);

// Calculate totals
$subtotal = 0;
$items_list = [];
while ($item = mysqli_fetch_assoc($items)) {
    $item['line_total'] = $item['quantity'] * $item['unit_price'];
    $subtotal += $item['line_total'];
    $items_list[] = $item;
}
$tax_amount = $subtotal * ($invoice['tax_percent'] / 100);
$grand_total = $subtotal + $tax_amount;

$status_classes = [
    'Draft' => 'secondary',
    'Sent' => 'info',
    'Paid' => 'success',
    'Overdue' => 'danger'
];
$status_icons = [
    'Draft' => 'fa-file-alt',
    'Sent' => 'fa-paper-plane',
    'Paid' => 'fa-check-circle',
    'Overdue' => 'fa-exclamation-circle'
];
$status_class = $status_classes[$invoice['status']] ?? 'secondary';
$status_icon = $status_icons[$invoice['status']] ?? 'fa-file-alt';

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="invoices.php">Invoice</a></li>
                    <li class="breadcrumb-item active"><?= e($invoice['invoice_number']) ?></li>
                </ol>
            </nav>
            <h2 class="page-title mb-0">
                <?= e($invoice['invoice_number']) ?>
                <span class="badge bg-<?= $status_class ?> ms-2" style="font-size: 0.5em; vertical-align: middle;">
                    <i class="fas <?= $status_icon ?> me-1"></i><?= e($invoice['status']) ?>
                </span>
            </h2>
        </div>
        <div class="d-flex gap-2">
            <a href="invoices.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <a href="invoice-pdf.php?id=<?= $invoice_id ?>" class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-print me-2"></i>Cetak
            </a>
            <a href="invoice-form.php?id=<?= $invoice_id ?>" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit
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
    
    <div class="row g-4">
        <!-- Invoice Content -->
        <div class="col-lg-8">
            <!-- Business & Client Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Dari</h6>
                            <h5 class="mb-1"><?= e($invoice['business_name']) ?></h5>
                            <?php if ($invoice['biz_address']): ?>
                                <p class="mb-1 text-muted"><small><?= nl2br(e($invoice['biz_address'])) ?></small></p>
                            <?php endif; ?>
                            <?php if ($invoice['biz_phone']): ?>
                                <p class="mb-0 text-muted"><small><i class="fas fa-phone me-1"></i><?= e($invoice['biz_phone']) ?></small></p>
                            <?php endif; ?>
                            <?php if ($invoice['biz_email']): ?>
                                <p class="mb-0 text-muted"><small><i class="fas fa-envelope me-1"></i><?= e($invoice['biz_email']) ?></small></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Kepada</h6>
                            <h5 class="mb-1"><?= e($invoice['client_name']) ?></h5>
                            <?php if ($invoice['company']): ?>
                                <p class="mb-1 text-muted"><small><?= e($invoice['company']) ?></small></p>
                            <?php endif; ?>
                            <?php if ($invoice['client_address']): ?>
                                <p class="mb-1 text-muted"><small><?= nl2br(e($invoice['client_address'])) ?></small></p>
                            <?php endif; ?>
                            <?php if ($invoice['client_phone']): ?>
                                <p class="mb-0 text-muted"><small><i class="fas fa-phone me-1"></i><?= e($invoice['client_phone']) ?></small></p>
                            <?php endif; ?>
                            <?php if ($invoice['client_email']): ?>
                                <p class="mb-0 text-muted"><small><i class="fas fa-envelope me-1"></i><?= e($invoice['client_email']) ?></small></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Items Table -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-list me-2"></i>Item Invoice
                    </h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 45%">Deskripsi</th>
                                    <th class="text-center" style="width: 10%">Qty</th>
                                    <th class="text-end" style="width: 20%">Harga Satuan</th>
                                    <th class="text-end" style="width: 20%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items_list as $i => $item): ?>
                                <tr>
                                    <td class="text-muted"><?= $i + 1 ?></td>
                                    <td><?= e($item['description']) ?></td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end"><?= formatCurrency($item['unit_price']) ?></td>
                                    <td class="text-end"><strong><?= formatCurrency($item['line_total']) ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end border-0"><strong>Subtotal</strong></td>
                                    <td class="text-end border-0"><strong><?= formatCurrency($subtotal) ?></strong></td>
                                </tr>
                                <?php if ($invoice['tax_percent'] > 0): ?>
                                <tr>
                                    <td colspan="4" class="text-end border-0">Pajak (<?= $invoice['tax_percent'] ?>%)</td>
                                    <td class="text-end border-0"><?= formatCurrency($tax_amount) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td colspan="4" class="text-end border-0">
                                        <strong class="fs-5">Grand Total</strong>
                                    </td>
                                    <td class="text-end border-0">
                                        <strong class="fs-5 text-primary"><?= formatCurrency($grand_total) ?></strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            <?php if ($invoice['notes']): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-2">
                        <i class="fas fa-sticky-note me-2"></i>Catatan
                    </h5>
                    <p class="text-muted mb-0"><?= nl2br(e($invoice['notes'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Invoice Meta -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-info-circle me-2"></i>Informasi Invoice
                    </h5>
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted">No. Invoice</td>
                            <td class="text-end"><strong><?= e($invoice['invoice_number']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Buat</td>
                            <td class="text-end"><?= formatDate($invoice['created_at']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jatuh Tempo</td>
                            <td class="text-end">
                                <?php if ($invoice['due_date']): ?>
                                    <?= formatDate($invoice['due_date']) ?>
                                    <?php 
                                    $days_left = (int)((strtotime($invoice['due_date']) - time()) / 86400);
                                    if ($invoice['status'] !== 'Paid' && $days_left < 0): ?>
                                        <br><span class="badge bg-danger">Lewat <?= abs($days_left) ?> hari</span>
                                    <?php elseif ($invoice['status'] !== 'Paid' && $days_left <= 7): ?>
                                        <br><span class="badge bg-warning"><?= $days_left ?> hari lagi</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Grand Total</td>
                            <td class="text-end"><strong class="text-primary"><?= formatCurrency($grand_total) ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Status Management -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-exchange-alt me-2"></i>Ubah Status
                    </h5>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="text-muted">Status saat ini:</span>
                        <span class="badge bg-<?= $status_class ?> px-3 py-2">
                            <i class="fas <?= $status_icon ?> me-1"></i><?= e($invoice['status']) ?>
                        </span>
                    </div>
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <input type="hidden" name="action" value="update_status">
                        <select class="form-select form-select-sm" name="new_status">
                            <?php 
                            $all_statuses = ['Draft', 'Sent', 'Paid', 'Overdue'];
                            foreach ($all_statuses as $s): 
                                if ($s !== $invoice['status']): ?>
                                <option value="<?= $s ?>"><?= $s ?></option>
                            <?php endif; endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Yakin ubah status invoice?')">
                            <i class="fas fa-check me-1"></i>Ubah
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Deal Link -->
            <?php if ($invoice['deal_id']): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-handshake me-2"></i>Deal Terkait
                    </h5>
                    <p class="mb-1"><strong><?= e($invoice['deal_title']) ?></strong></p>
                    <p class="text-muted mb-3"><?= formatCurrency($invoice['deal_value']) ?></p>
                    <a href="deal-detail.php?id=<?= $invoice['deal_id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-external-link-alt me-1"></i>Lihat Detail Deal
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-cog me-2"></i>Aksi
                    </h5>
                    <div class="d-grid gap-2">
                        <a href="invoice-pdf.php?id=<?= $invoice_id ?>" class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-print me-2"></i>Cetak / Download PDF
                        </a>
                        <a href="invoice-form.php?id=<?= $invoice_id ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-edit me-2"></i>Edit Invoice
                        </a>
                        <form method="POST" onsubmit="return confirm('Yakin ingin menghapus invoice ini? Tindakan ini tidak bisa dibatalkan.')">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-2"></i>Hapus Invoice
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
