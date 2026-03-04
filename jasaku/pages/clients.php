<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Klien';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$businessId = getCurrentBusinessId();
$search = $_GET['search'] ?? '';
$source = $_GET['source'] ?? '';
$clients = getClients($businessId, $search, $source);

$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $result = deleteClient($_GET['delete']);
    if ($result['success']) {
        $success = 'Klien berhasil dihapus';
    } else {
        $error = $result['message'];
    }
    header('Location: clients.php');
    exit;
}
?>

<div class="main-content">
    <div class="page-header">
        <h2>Klien</h2>
        <a href="client-form.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Klien
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
    
    <div class="card">
        <div class="card-header">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control" name="search" placeholder="Cari klien..." value="<?= sanitize($search) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="source">
                        <option value="">Semua Sumber</option>
                        <option value="Referral" <?= $source === 'Referral' ? 'selected' : '' ?>>Referral</option>
                        <option value="Social Media" <?= $source === 'Social Media' ? 'selected' : '' ?>>Social Media</option>
                        <option value="Direct" <?= $source === 'Direct' ? 'selected' : '' ?>>Direct</option>
                        <option value="Website" <?= $source === 'Website' ? 'selected' : '' ?>>Website</option>
                        <option value="Lainnya" <?= $source === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <?php if (count($clients) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="clientsTable">
                        <thead>
                            <tr>
                                <th>Nama Klien</th>
                                <th>Perusahaan</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Sumber</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><strong><?= sanitize($client['client_name']) ?></strong></td>
                                <td><?= sanitize($client['company'] ?? '-') ?></td>
                                <td><?= sanitize($client['email'] ?? '-') ?></td>
                                <td><?= sanitize($client['phone'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= $client['source'] ?? 'Direct' ?></span>
                                </td>
                                <td class="actions">
                                    <a href="client-form.php?id=<?= $client['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="clients.php?delete=<?= $client['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
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
                    <i class="fas fa-users"></i>
                    <h4>Belum Ada Klien</h4>
                    <p>Tambahkan klien untuk memulai</p>
                    <a href="client-form.php" class="btn btn-primary">Tambah Klien</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
