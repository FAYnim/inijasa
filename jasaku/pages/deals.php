<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Deals';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$businessId = getCurrentBusinessId();
$stage = $_GET['stage'] ?? '';
$deals = getDeals($businessId, $stage);

$error = '';
$success = '';
$stages = ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost'];

// Handle delete
if (isset($_GET['delete'])) {
    $result = deleteDeal($_GET['delete']);
    if ($result['success']) {
        $success = 'Deal berhasil dihapus';
    } else {
        $error = $result['message'];
    }
    header('Location: deals.php');
    exit;
}

// Group deals by stage for pipeline view
$dealsByStage = [];
foreach ($stages as $s) {
    $dealsByStage[$s] = array_filter($deals, function($d) use ($s) {
        return $d['current_stage'] === $s;
    });
}
?>

<div class="main-content">
    <div class="page-header">
        <h2>Deals</h2>
        <a href="deal-form.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Deal Baru
        </a>
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
    
    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0">Filter Stage:</label>
                </div>
                <div class="col-auto">
                    <select class="form-select" name="stage" onchange="this.form.submit()">
                        <option value="">Semua Stage</option>
                        <?php foreach ($stages as $s): ?>
                        <option value="<?= $s ?>" <?= $stage === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($stage): ?>
                <div class="col-auto">
                    <a href="deals.php" class="btn btn-outline-secondary btn-sm">Clear Filter</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <!-- Pipeline View -->
    <?php if (!$stage): ?>
    <div class="pipeline-stages">
        <?php foreach ($stages as $s): ?>
        <div class="stage-card stage-<?= $s ?>">
            <div class="stage-header">
                <span class="stage-name"><?= $s ?></span>
                <span class="stage-count"><?= count($dealsByStage[$s]) ?></span>
            </div>
            <?php foreach ($dealsByStage[$s] as $deal): ?>
            <div class="deal-item" onclick="window.location.href='deal-form.php?id=<?= $deal['id'] ?>'">
                <div class="deal-title"><?= sanitize($deal['deal_title']) ?></div>
                <div class="deal-client"><?= sanitize($deal['client_name'] ?? '-') ?></div>
                <div class="deal-value"><?= formatCurrency($deal['final_value']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Table View -->
    <div class="card">
        <div class="card-body">
            <?php if (count($deals) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="dealsTable">
                        <thead>
                            <tr>
                                <th>Judul Deal</th>
                                <th>Klien</th>
                                <th>Paket Jasa</th>
                                <th>Nilai Deal</th>
                                <th>Diskon</th>
                                <th>Nilai Akhir</th>
                                <th>Stage</th>
                                <th>Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deals as $deal): ?>
                            <tr>
                                <td>
                                    <a href="deal-form.php?id=<?= $deal['id'] ?>"><strong><?= sanitize($deal['deal_title']) ?></strong></a>
                                    <?php if ($deal['expected_close_date']): ?>
                                    <div class="text-muted small">Tenggat: <?= formatDate($deal['expected_close_date']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= sanitize($deal['client_name'] ?? '-') ?></td>
                                <td><?= sanitize($deal['package_name'] ?? '-') ?></td>
                                <td><?= formatCurrency($deal['deal_value']) ?></td>
                                <td><?= $deal['discount_percent'] > 0 ? $deal['discount_percent'] . '%' : '-' ?></td>
                                <td><strong><?= formatCurrency($deal['final_value']) ?></strong></td>
                                <td>
                                    <span class="badge badge-<?= $deal['current_stage'] ?>"><?= $deal['current_stage'] ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $deal['payment_status'] ?>"><?= $deal['payment_status'] ?></span>
                                </td>
                                <td class="actions">
                                    <a href="deal-form.php?id=<?= $deal['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (!in_array($deal['current_stage'], ['Won', 'Lost'])): ?>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php foreach (array_slice($stages, 0, 4) as $nextStage): ?>
                                            <?php if ($nextStage !== $deal['current_stage']): ?>
                                            <li><a class="dropdown-item" href="?move=<?= $deal['id'] ?>&stage=<?= $nextStage ?>">Move to <?= $nextStage ?></a></li>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-success" href="?move=<?= $deal['id'] ?>&stage=Won">Mark as Won</a></li>
                                            <li><a class="dropdown-item text-danger" href="?move=<?= $deal['id'] ?>&stage=Lost">Mark as Lost</a></li>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                    <a href="deals.php?delete=<?= $deal['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-handshake"></i>
                    <h4>Belum Ada Deal</h4>
                    <p>Mulai buat deal pertama Anda</p>
                    <a href="deal-form.php" class="btn btn-primary">Buat Deal</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Handle move stage
if (isset($_GET['move']) && isset($_GET['stage'])) {
    $dealId = $_GET['move'];
    $newStage = $_GET['stage'];
    updateDealStage($dealId, $newStage);
    header('Location: deals.php');
    exit;
}
?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
