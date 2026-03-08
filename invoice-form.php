<?php
/**
 * Invoice Form (Create / Edit)
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$business_id = getCurrentBusinessId();

if (!$business_id) {
    redirect('setup-business.php');
}

$invoice_id = (int)($_GET['id'] ?? 0);
$deal_id_prefill = (int)($_GET['deal_id'] ?? 0);
$is_edit = $invoice_id > 0;
$page_title = $is_edit ? 'Edit Invoice' : 'Buat Invoice Baru';
$error = '';

// Default values
$invoice = [
    'client_id' => '',
    'deal_id' => $deal_id_prefill ?: '',
    'due_date' => date('Y-m-d', strtotime('+14 days')),
    'tax_percent' => 0,
    'notes' => '',
    'invoice_number' => ''
];
$items = [];

// If editing, load existing data
if ($is_edit) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM invoices WHERE id = ? AND business_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $invoice_id, $business_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $invoice = mysqli_fetch_assoc($result);
    
    if (!$invoice) {
        setFlashMessage('danger', 'Invoice tidak ditemukan.');
        redirect('invoices.php');
    }
    
    // Load items
    $stmt = mysqli_prepare($conn, "SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC");
    mysqli_stmt_bind_param($stmt, "i", $invoice_id);
    mysqli_stmt_execute($stmt);
    $items_result = mysqli_stmt_get_result($stmt);
    while ($item = mysqli_fetch_assoc($items_result)) {
        $items[] = $item;
    }
}

// If prefilling from deal
if ($deal_id_prefill && !$is_edit) {
    $stmt = mysqli_prepare($conn, "
        SELECT d.*, s.service_name, s.price as service_price, c.client_name 
        FROM deals d 
        LEFT JOIN services s ON d.service_id = s.id
        LEFT JOIN clients c ON d.client_id = c.id
        WHERE d.id = ? AND d.business_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "ii", $deal_id_prefill, $business_id);
    mysqli_stmt_execute($stmt);
    $deal_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    
    if ($deal_data) {
        $invoice['client_id'] = $deal_data['client_id'];
        $invoice['deal_id'] = $deal_id_prefill;
        
        // Pre-fill item from deal service
        $items[] = [
            'description' => $deal_data['service_name'] ?? $deal_data['deal_title'],
            'quantity' => 1,
            'unit_price' => $deal_data['final_value']
        ];
    }
}

// If no items, add an empty row
if (empty($items)) {
    $items[] = ['description' => '', 'quantity' => 1, 'unit_price' => ''];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('danger', 'Token keamanan tidak valid.');
        redirect($is_edit ? "invoice-form.php?id=$invoice_id" : 'invoice-form.php');
    }
    
    $client_id = (int)($_POST['client_id'] ?? 0);
    $deal_id = (int)($_POST['deal_id'] ?? 0) ?: null;
    $due_date = $_POST['due_date'] ?? '';
    $tax_percent = (float)($_POST['tax_percent'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    
    // Collect items
    $item_descriptions = $_POST['item_description'] ?? [];
    $item_quantities = $_POST['item_quantity'] ?? [];
    $item_prices = $_POST['item_price'] ?? [];
    
    // Validation
    if (!$client_id) {
        $error = 'Klien wajib dipilih.';
    } elseif (empty($item_descriptions) || empty(array_filter($item_descriptions))) {
        $error = 'Minimal satu item harus diisi.';
    } else {
        // Validate each item
        $valid_items = [];
        foreach ($item_descriptions as $i => $desc) {
            $desc = trim($desc);
            if (empty($desc)) continue;
            
            $qty = (int)($item_quantities[$i] ?? 1);
            $price = (float) str_replace(['Rp', '.', ' ', ','], ['', '', '', '.'], $item_prices[$i] ?? '0');
            
            if ($qty <= 0 || $price <= 0) {
                $error = 'Quantity dan harga item harus lebih dari 0.';
                break;
            }
            
            $valid_items[] = ['description' => $desc, 'quantity' => $qty, 'unit_price' => $price];
        }
        
        if (empty($valid_items) && empty($error)) {
            $error = 'Minimal satu item harus diisi dengan lengkap.';
        }
    }
    
    // Update form values for re-display if error
    $invoice['client_id'] = $client_id;
    $invoice['deal_id'] = $deal_id ?? '';
    $invoice['due_date'] = $due_date;
    $invoice['tax_percent'] = $tax_percent;
    $invoice['notes'] = $notes;
    $items = [];
    foreach ($item_descriptions as $i => $desc) {
        $items[] = [
            'description' => trim($desc),
            'quantity' => (int)($item_quantities[$i] ?? 1),
            'unit_price' => str_replace(['Rp', '.', ' ', ','], ['', '', '', '.'], $item_prices[$i] ?? '0')
        ];
    }
    if (empty($items)) {
        $items[] = ['description' => '', 'quantity' => 1, 'unit_price' => ''];
    }
    
    if (empty($error)) {
        mysqli_begin_transaction($conn);
        
        try {
            if ($is_edit) {
                // Update invoice
                $due_date_val = $due_date ?: null;
                $stmt = mysqli_prepare($conn, 
                    "UPDATE invoices SET client_id = ?, deal_id = ?, due_date = ?, tax_percent = ?, notes = ? WHERE id = ? AND business_id = ?"
                );
                mysqli_stmt_bind_param($stmt, "iisdsii", $client_id, $deal_id, $due_date_val, $tax_percent, $notes, $invoice_id, $business_id);
                mysqli_stmt_execute($stmt);
                
                // Delete old items
                $stmt = mysqli_prepare($conn, "DELETE FROM invoice_items WHERE invoice_id = ?");
                mysqli_stmt_bind_param($stmt, "i", $invoice_id);
                mysqli_stmt_execute($stmt);
                
                $target_id = $invoice_id;
            } else {
                // Generate invoice number
                $inv_number = generateInvoiceNumber($conn, $business_id);
                $due_date_val = $due_date ?: null;
                
                $stmt = mysqli_prepare($conn, 
                    "INSERT INTO invoices (business_id, deal_id, client_id, invoice_number, due_date, tax_percent, notes) VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                mysqli_stmt_bind_param($stmt, "iiissds", $business_id, $deal_id, $client_id, $inv_number, $due_date_val, $tax_percent, $notes);
                mysqli_stmt_execute($stmt);
                $target_id = mysqli_insert_id($conn);
            }
            
            // Insert items
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO invoice_items (invoice_id, description, quantity, unit_price) VALUES (?, ?, ?, ?)"
            );
            foreach ($valid_items as $item) {
                mysqli_stmt_bind_param($stmt, "isid", $target_id, $item['description'], $item['quantity'], $item['unit_price']);
                mysqli_stmt_execute($stmt);
            }
            
            mysqli_commit($conn);
            setFlashMessage('success', $is_edit ? 'Invoice berhasil diupdate.' : 'Invoice berhasil dibuat.');
            redirect("invoice-detail.php?id=$target_id");
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

// Load clients for dropdown
$stmt = mysqli_prepare($conn, "SELECT id, client_name, company FROM clients WHERE business_id = ? ORDER BY client_name ASC");
mysqli_stmt_bind_param($stmt, "i", $business_id);
mysqli_stmt_execute($stmt);
$clients = mysqli_stmt_get_result($stmt);

// Load deals for dropdown
$stmt = mysqli_prepare($conn, "SELECT d.id, d.deal_title, d.final_value, d.client_id FROM deals d WHERE d.business_id = ? ORDER BY d.created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $business_id);
mysqli_stmt_execute($stmt);
$deals = mysqli_stmt_get_result($stmt);
$deals_data = [];
while ($d = mysqli_fetch_assoc($deals)) {
    $deals_data[] = $d;
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="invoices.php">Invoice</a></li>
                            <li class="breadcrumb-item active"><?= $is_edit ? 'Edit' : 'Buat Baru' ?></li>
                        </ol>
                    </nav>
                    <h2 class="page-title mb-0"><?= e($page_title) ?></h2>
                </div>
                <a href="invoices.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= e($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="invoiceForm">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                
                <!-- Client & Deal Selection -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Klien</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="client_id" class="form-label">
                                    Klien <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="client_id" name="client_id" required>
                                    <option value="">Pilih klien...</option>
                                    <?php 
                                    mysqli_data_seek($clients, 0);
                                    while ($client = mysqli_fetch_assoc($clients)): ?>
                                        <option value="<?= $client['id'] ?>" <?= $invoice['client_id'] == $client['id'] ? 'selected' : '' ?>>
                                            <?= e($client['client_name']) ?>
                                            <?= $client['company'] ? ' (' . e($client['company']) . ')' : '' ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="deal_id" class="form-label">Deal Terkait <small class="text-muted">(opsional)</small></label>
                                <select class="form-select" id="deal_id" name="deal_id">
                                    <option value="">Tidak ada deal terkait</option>
                                    <?php foreach ($deals_data as $deal): ?>
                                        <option value="<?= $deal['id'] ?>" 
                                                data-client="<?= $deal['client_id'] ?>"
                                                <?= $invoice['deal_id'] == $deal['id'] ? 'selected' : '' ?>>
                                            <?= e($deal['deal_title']) ?> (<?= formatCurrency($deal['final_value']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Invoice Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Detail Invoice</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php if ($is_edit): ?>
                            <div class="col-md-4">
                                <label class="form-label">Nomor Invoice</label>
                                <input type="text" class="form-control" value="<?= e($invoice['invoice_number']) ?>" disabled>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-<?= $is_edit ? '4' : '6' ?>">
                                <label for="due_date" class="form-label">Jatuh Tempo</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" 
                                       value="<?= e($invoice['due_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-<?= $is_edit ? '4' : '6' ?>">
                                <label for="tax_percent" class="form-label">Pajak / PPN (%)</label>
                                <input type="number" class="form-control" id="tax_percent" name="tax_percent" 
                                       value="<?= e($invoice['tax_percent']) ?>" min="0" max="100" step="0.5"
                                       placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Invoice Items -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Item Invoice</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                            <i class="fas fa-plus me-1"></i>Tambah Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 45%">Deskripsi <span class="text-danger">*</span></th>
                                        <th style="width: 12%">Qty</th>
                                        <th style="width: 25%">Harga Satuan <span class="text-danger">*</span></th>
                                        <th style="width: 13%" class="text-end">Subtotal</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    <?php foreach ($items as $i => $item): ?>
                                    <tr class="item-row">
                                        <td>
                                            <input type="text" class="form-control" name="item_description[]" 
                                                   value="<?= e($item['description']) ?>" placeholder="Nama layanan/produk" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control item-qty" name="item_quantity[]" 
                                                   value="<?= (int)$item['quantity'] ?>" min="1" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control item-price" name="item_price[]" 
                                                   value="<?= $item['unit_price'] ? formatCurrency($item['unit_price'], false) : '' ?>" 
                                                   placeholder="0" required>
                                        </td>
                                        <td class="text-end">
                                            <span class="item-subtotal fw-bold text-muted">Rp 0</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" title="Hapus item">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                                        <td class="text-end"><strong id="totalSubtotal">Rp 0</strong></td>
                                        <td></td>
                                    </tr>
                                    <tr id="taxRow">
                                        <td colspan="3" class="text-end">Pajak (<span id="taxPercentLabel">0</span>%)</td>
                                        <td class="text-end"><span id="totalTax">Rp 0</span></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong class="fs-5">Grand Total</strong></td>
                                        <td class="text-end"><strong class="fs-5 text-primary" id="grandTotal">Rp 0</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Notes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Catatan</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="notes" rows="3" 
                                  placeholder="Catatan tambahan untuk invoice (opsional, akan tampil di invoice)"><?= e($invoice['notes'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="d-flex justify-content-between">
                    <a href="invoices.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i><?= $is_edit ? 'Update Invoice' : 'Buat Invoice' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsBody = document.getElementById('itemsBody');
    const addItemBtn = document.getElementById('addItemBtn');
    const taxInput = document.getElementById('tax_percent');
    
    // Add item row
    addItemBtn.addEventListener('click', function() {
        const row = document.createElement('tr');
        row.className = 'item-row';
        row.innerHTML = `
            <td>
                <input type="text" class="form-control" name="item_description[]" placeholder="Nama layanan/produk" required>
            </td>
            <td>
                <input type="number" class="form-control item-qty" name="item_quantity[]" value="1" min="1" required>
            </td>
            <td>
                <input type="text" class="form-control item-price" name="item_price[]" placeholder="0" required>
            </td>
            <td class="text-end">
                <span class="item-subtotal fw-bold text-muted">Rp 0</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" title="Hapus item">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        itemsBody.appendChild(row);
        bindRowEvents(row);
        row.querySelector('input').focus();
    });
    
    // Remove item
    itemsBody.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-item-btn');
        if (btn) {
            const rows = itemsBody.querySelectorAll('.item-row');
            if (rows.length > 1) {
                btn.closest('tr').remove();
                recalculate();
            } else {
                alert('Minimal harus ada satu item.');
            }
        }
    });
    
    // Format currency input
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    function parseNumber(str) {
        return parseFloat(str.replace(/[^\d]/g, '')) || 0;
    }
    
    function bindRowEvents(row) {
        const qtyInput = row.querySelector('.item-qty');
        const priceInput = row.querySelector('.item-price');
        
        [qtyInput, priceInput].forEach(input => {
            input.addEventListener('input', recalculate);
        });
        
        priceInput.addEventListener('blur', function() {
            const val = parseNumber(this.value);
            if (val > 0) {
                this.value = formatNumber(val);
            }
        });
    }
    
    function recalculate() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseInt(row.querySelector('.item-qty').value) || 0;
            const price = parseNumber(row.querySelector('.item-price').value);
            const rowTotal = qty * price;
            row.querySelector('.item-subtotal').textContent = 'Rp ' + formatNumber(rowTotal);
            subtotal += rowTotal;
        });
        
        const taxPercent = parseFloat(taxInput.value) || 0;
        const taxAmount = subtotal * (taxPercent / 100);
        const grandTotal = subtotal + taxAmount;
        
        document.getElementById('totalSubtotal').textContent = 'Rp ' + formatNumber(subtotal);
        document.getElementById('taxPercentLabel').textContent = taxPercent;
        document.getElementById('totalTax').textContent = 'Rp ' + formatNumber(Math.round(taxAmount));
        document.getElementById('grandTotal').textContent = 'Rp ' + formatNumber(Math.round(grandTotal));
        
        // Show/hide tax row
        document.getElementById('taxRow').style.display = taxPercent > 0 ? '' : 'none';
    }
    
    // Bind events to existing rows
    document.querySelectorAll('.item-row').forEach(bindRowEvents);
    
    // Tax input change
    taxInput.addEventListener('input', recalculate);
    
    // Initial calculation
    recalculate();
    
    // Filter deals by selected client
    const clientSelect = document.getElementById('client_id');
    const dealSelect = document.getElementById('deal_id');
    const allDealOptions = Array.from(dealSelect.options).slice(1); // Skip first "none" option
    
    clientSelect.addEventListener('change', function() {
        const selectedClient = this.value;
        dealSelect.innerHTML = '<option value="">Tidak ada deal terkait</option>';
        
        allDealOptions.forEach(option => {
            if (!selectedClient || option.dataset.client === selectedClient) {
                dealSelect.appendChild(option.cloneNode(true));
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
