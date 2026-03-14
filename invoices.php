<?php
/**
 * Invoice List Page
 * IniJasa - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$page_title = 'Invoice';
$business_id = getCurrentBusinessId();

if (!$business_id) {
    redirect('setup-business');
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('danger', 'Token keamanan tidak valid.');
        redirect('invoices');
    }
    
    $delete_id = (int)($_POST['invoice_id'] ?? 0);
    $stmt = mysqli_prepare($conn, "DELETE FROM invoices WHERE id = ? AND business_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $delete_id, $business_id);
    
    if (mysqli_stmt_execute($stmt) && mysqli_affected_rows($conn) > 0) {
        setFlashMessage('success', 'Invoice berhasil dihapus.');
    } else {
        setFlashMessage('danger', 'Gagal menghapus invoice.');
    }
    redirect('invoices');
}

// Filters
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT inv.*, c.client_name, c.company,
          (SELECT COALESCE(SUM(ii.quantity * ii.unit_price), 0) FROM invoice_items ii WHERE ii.invoice_id = inv.id) AS subtotal
          FROM invoices inv
          LEFT JOIN clients c ON inv.client_id = c.id
          WHERE inv.business_id = ?";
$params = [$business_id];
$types = "i";

if ($status_filter) {
    $query .= " AND inv.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($search) {
    $query .= " AND (inv.invoice_number LIKE ? OR c.client_name LIKE ? OR c.company LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$query .= " ORDER BY inv.created_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$invoices = mysqli_stmt_get_result($stmt);

// Summary stats
$stats_stmt = mysqli_prepare($conn, "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'Draft' THEN 1 ELSE 0 END) as draft_count,
        SUM(CASE WHEN status = 'Sent' THEN 1 ELSE 0 END) as sent_count,
        SUM(CASE WHEN status = 'Paid' THEN 1 ELSE 0 END) as paid_count,
        SUM(CASE WHEN status = 'Overdue' THEN 1 ELSE 0 END) as overdue_count
    FROM invoices WHERE business_id = ?
");
mysqli_stmt_bind_param($stats_stmt, "i", $business_id);
mysqli_stmt_execute($stats_stmt);
$stats = mysqli_fetch_assoc(mysqli_stmt_get_result($stats_stmt));

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-1">Invoice</h2>
            <p class="text-muted mb-0">Kelola invoice dan penawaran untuk klien</p>
        </div>
        <a href="invoice-form" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Buat Invoice Baru
        </a>
    </div>
    
    <?php
    $flash = getFlashMessage();
    if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-secondary bg-opacity-10">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="metric-icon bg-secondary" style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <h4 class="mb-0"><?= $stats['draft_count'] ?? 0 ?></h4>
                            <small class="text-muted">Draft</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;background:#0dcaf0;">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div>
                            <h4 class="mb-0"><?= $stats['sent_count'] ?? 0 ?></h4>
                            <small class="text-muted">Sent</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;background:#059669;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h4 class="mb-0"><?= $stats['paid_count'] ?? 0 ?></h4>
                            <small class="text-muted">Paid</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-danger bg-opacity-10">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;background:#EF4444;">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div>
                            <h4 class="mb-0"><?= $stats['overdue_count'] ?? 0 ?></h4>
                            <small class="text-muted">Overdue</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Cari nomor invoice atau nama klien..."
                           value="<?= e($search) ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="Draft" <?= $status_filter === 'Draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="Sent" <?= $status_filter === 'Sent' ? 'selected' : '' ?>>Sent</option>
                        <option value="Paid" <?= $status_filter === 'Paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="Overdue" <?= $status_filter === 'Overdue' ? 'selected' : '' ?>>Overdue</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Cari
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="invoices" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Invoice Table -->
    <div class="card">
        <div class="card-body">
            <?php if (mysqli_num_rows($invoices) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Klien</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($inv = mysqli_fetch_assoc($invoices)): 
                            $total = $inv['subtotal'];
                            if ($inv['tax_percent'] > 0) {
                                $total += $total * ($inv['tax_percent'] / 100);
                            }
                            
                            $status_classes = [
                                'Draft' => 'secondary',
                                'Sent' => 'info',
                                'Paid' => 'success',
                                'Overdue' => 'danger'
                            ];
                            $status_class = $status_classes[$inv['status']] ?? 'secondary';
                        ?>
                        <tr>
                            <td>
                                <a href="invoice-detail?id=<?= $inv['id'] ?>" class="fw-bold text-decoration-none">
                                    <?= e($inv['invoice_number']) ?>
                                </a>
                            </td>
                            <td>
                                <strong><?= e($inv['client_name']) ?></strong>
                                <?php if ($inv['company']): ?>
                                    <br><small class="text-muted"><?= e($inv['company']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <strong><?= formatCurrency($total) ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-<?= $status_class ?>"><?= e($inv['status']) ?></span>
                            </td>
                            <td>
                                <small><?= formatDate($inv['created_at']) ?></small>
                            </td>
                            <td>
                                <?php if ($inv['due_date']): ?>
                                    <small><?= formatDate($inv['due_date']) ?></small>
                                    <?php 
                                    $days_left = (int)((strtotime($inv['due_date']) - time()) / 86400);
                                    if ($inv['status'] !== 'Paid' && $days_left < 0): ?>
                                        <br><span class="badge bg-danger">Lewat <?= abs($days_left) ?> hari</span>
                                    <?php elseif ($inv['status'] !== 'Paid' && $days_left <= 3): ?>
                                        <br><span class="badge bg-warning"><?= $days_left ?> hari lagi</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <small class="text-muted">-</small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="invoice-detail?id=<?= $inv['id'] ?>" class="btn btn-outline-primary" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="invoice-form?id=<?= $inv['id'] ?>" class="btn btn-outline-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus invoice ini?')">
                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="invoice_id" value="<?= $inv['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">
                    <?php if ($search || $status_filter): ?>
                        Tidak ada invoice yang sesuai dengan pencarian.
                    <?php else: ?>
                        Belum ada invoice. Buat invoice pertama Anda!
                    <?php endif; ?>
                </p>
                <?php if ($search || $status_filter): ?>
                    <a href="invoices" class="btn btn-secondary">Reset Filter</a>
                <?php else: ?>
                    <a href="invoice-form" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Buat Invoice Baru
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
