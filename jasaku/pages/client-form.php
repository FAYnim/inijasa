<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Klien';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$businessId = getCurrentBusinessId();
$clientId = $_GET['id'] ?? null;
$client = $clientId ? getClientById($clientId) : null;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientName = trim($_POST['client_name'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $source = $_POST['source'] ?? 'Direct';
    
    if (empty($clientName)) {
        $error = 'Nama klien wajib diisi';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } else {
        $data = [
            'client_name' => $clientName,
            'company' => $company,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'notes' => $notes,
            'source' => $source
        ];
        
        if ($client) {
            $result = updateClient($clientId, $data);
            if ($result['success']) {
                $success = 'Klien berhasil diupdate';
                $client = getClientById($clientId);
            } else {
                $error = $result['message'];
            }
        } else {
            $result = createClient($businessId, $data);
            if ($result['success']) {
                header('Location: clients.php');
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
        <h2><?= $client ? 'Edit' : 'Tambah' ?> Klien</h2>
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
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="client_name" class="form-label">Nama Klien <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="client_name" name="client_name" required value="<?= sanitize($client['client_name'] ?? $_POST['client_name'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="company" class="form-label">Perusahaan</label>
                            <input type="text" class="form-control" id="company" name="company" value="<?= sanitize($client['company'] ?? $_POST['company'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= sanitize($client['email'] ?? $_POST['email'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= sanitize($client['phone'] ?? $_POST['phone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="source" class="form-label">Sumber</label>
                            <select class="form-select" id="source" name="source">
                                <option value="Referral" <?= ($client['source'] ?? $_POST['source'] ?? 'Direct') === 'Referral' ? 'selected' : '' ?>>Referral</option>
                                <option value="Social Media" <?= ($client['source'] ?? '') === 'Social Media' ? 'selected' : '' ?>>Social Media</option>
                                <option value="Direct" <?= ($client['source'] ?? 'Direct') === 'Direct' ? 'selected' : '' ?>>Direct</option>
                                <option value="Website" <?= ($client['source'] ?? '') === 'Website' ? 'selected' : '' ?>>Website</option>
                                <option value="Lainnya" <?= ($client['source'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?= sanitize($client['address'] ?? $_POST['address'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?= sanitize($client['notes'] ?? $_POST['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <a href="clients.php" class="btn btn-outline-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
