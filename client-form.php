<?php
/**
 * Client Form Page (Create/Edit Client)
 * IniJasa - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$business_id = getCurrentBusinessId();
if (!$business_id) {
    redirect('setup-business.php');
}

$client_id = $_GET['id'] ?? null;
$is_edit = !empty($client_id);
$page_title = $is_edit ? 'Edit Klien' : 'Tambah Klien Baru';

$error = '';
$client = null;

// Load client data if editing
if ($is_edit) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM clients WHERE id = ? AND business_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $client_id, $business_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $client = mysqli_fetch_assoc($result);
    
    if (!$client) {
        setFlashMessage('danger', 'Klien tidak ditemukan.');
        redirect('clients.php');
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'save_client';
    
    if ($action === 'add_activity_log' && $is_edit) {
        $note = trim($_POST['note'] ?? '');
        if (empty($note)) {
            setFlashMessage('danger', 'Catatan tidak boleh kosong.');
            redirect("client-form.php?id=$client_id");
        }
        $stmt = mysqli_prepare($conn, "INSERT INTO activity_logs (business_id, client_id, note) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iis", $business_id, $client_id, $note);
        if (mysqli_stmt_execute($stmt)) {
            setFlashMessage('success', 'Catatan aktivitas berhasil ditambahkan.');
        } else {
            setFlashMessage('danger', 'Gagal menambahkan catatan aktivitas.');
        }
        redirect("client-form.php?id=$client_id");
    }

    $client_name = trim($_POST['client_name'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $source = $_POST['source'] ?? '';
    
    // Validation
    if (empty($client_name)) {
        $error = 'Nama klien wajib diisi.';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        if ($is_edit) {
            // Update client
            $stmt = mysqli_prepare($conn, 
                "UPDATE clients SET 
                    client_name = ?, 
                    company = ?, 
                    email = ?, 
                    phone = ?, 
                    address = ?, 
                    notes = ?, 
                    source = ?
                 WHERE id = ? AND business_id = ?"
            );
            mysqli_stmt_bind_param($stmt, "sssssssii", 
                $client_name, $company, $email, $phone, $address, $notes, $source,
                $client_id, $business_id
            );
            
            if (mysqli_stmt_execute($stmt)) {
                setFlashMessage('success', 'Klien berhasil diupdate.');
                redirect('clients.php');
            } else {
                $error = 'Gagal mengupdate klien.';
            }
        } else {
            // Insert new client
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO clients (business_id, client_name, company, email, phone, address, notes, source) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "isssssss", 
                $business_id, $client_name, $company, $email, $phone, $address, $notes, $source
            );
            
            if (mysqli_stmt_execute($stmt)) {
                setFlashMessage('success', 'Klien berhasil ditambahkan.');
                redirect('clients.php');
            } else {
                $error = 'Gagal menambahkan klien.';
            }
        }
    }
}

// Load activity logs if editing
$activity_logs = [];
if ($is_edit) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM activity_logs WHERE client_id = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, "i", $client_id);
    mysqli_stmt_execute($stmt);
    $activity_logs = mysqli_stmt_get_result($stmt);
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
                    <p class="text-muted mb-0">Lengkapi informasi klien</p>
                </div>
                <a href="clients.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>
            
            <!-- Client Form -->
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="client_name" class="form-label">
                                    Nama Klien <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="client_name" 
                                       name="client_name" 
                                       placeholder="Nama lengkap klien"
                                       value="<?= e($client['client_name'] ?? '') ?>"
                                       required>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="company" class="form-label">Perusahaan</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="company" 
                                       name="company" 
                                       placeholder="Nama perusahaan (opsional)"
                                       value="<?= e($client['company'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="email@example.com"
                                       value="<?= e($client['email'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       placeholder="08123456789"
                                       value="<?= e($client['phone'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control" 
                                          id="address" 
                                          name="address" 
                                          rows="3"
                                          placeholder="Alamat lengkap klien"><?= e($client['address'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="source" class="form-label">Sumber</label>
                                <select class="form-select" id="source" name="source">
                                    <option value="">Pilih sumber...</option>
                                    <option value="Referral" <?= ($client['source'] ?? '') === 'Referral' ? 'selected' : '' ?>>Referral</option>
                                    <option value="Social Media" <?= ($client['source'] ?? '') === 'Social Media' ? 'selected' : '' ?>>Social Media</option>
                                    <option value="Direct" <?= ($client['source'] ?? '') === 'Direct' ? 'selected' : '' ?>>Direct</option>
                                    <option value="Website" <?= ($client['source'] ?? '') === 'Website' ? 'selected' : '' ?>>Website</option>
                                    <option value="Lainnya" <?= ($client['source'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">Catatan</label>
                                <textarea class="form-control" 
                                          id="notes" 
                                          name="notes" 
                                          rows="4"
                                          placeholder="Catatan tambahan tentang klien ini..."><?= e($client['notes'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="clients.php" class="btn btn-secondary">Batal</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        <?= $is_edit ? 'Update Klien' : 'Simpan Klien' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if ($is_edit): ?>
            <div class="card mt-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-clipboard-list me-2"></i>Catatan Aktivitas
                    </h5>
                    
                    <form method="POST" action="" class="mb-4">
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
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
