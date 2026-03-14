<?php
/**
 * Invoice PDF / Print View
 * IniJasa - Platform Manajemen Bisnis Jasa
 * 
 * Halaman print-optimized tanpa sidebar/topbar.
 * User bisa Ctrl+P / klik tombol cetak untuk save as PDF.
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$business_id = getCurrentBusinessId();

if (!$business_id) {
    redirect('setup-business.php');
}

$invoice_id = (int)($_GET['id'] ?? 0);
if (!$invoice_id) {
    redirect('invoices.php');
}

// Fetch invoice with all related data
$stmt = mysqli_prepare($conn, "
    SELECT inv.*, 
           c.client_name, c.company, c.email AS client_email, c.phone AS client_phone, c.address AS client_address,
           b.business_name, b.phone AS biz_phone, b.email AS biz_email, b.address AS biz_address, b.logo_path, b.category AS biz_category
    FROM invoices inv
    LEFT JOIN clients c ON inv.client_id = c.id
    LEFT JOIN businesses b ON inv.business_id = b.id
    WHERE inv.id = ? AND inv.business_id = ?
");
mysqli_stmt_bind_param($stmt, "ii", $invoice_id, $business_id);
mysqli_stmt_execute($stmt);
$invoice = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$invoice) {
    redirect('invoices.php');
}

// Fetch items
$stmt = mysqli_prepare($conn, "SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC");
mysqli_stmt_bind_param($stmt, "i", $invoice_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);

$items = [];
$subtotal = 0;
while ($item = mysqli_fetch_assoc($items_result)) {
    $item['line_total'] = $item['quantity'] * $item['unit_price'];
    $subtotal += $item['line_total'];
    $items[] = $item;
}
$tax_amount = $subtotal * ($invoice['tax_percent'] / 100);
$grand_total = $subtotal + $tax_amount;

$status_colors = [
    'Draft' => '#6B7280',
    'Sent' => '#0dcaf0',
    'Paid' => '#059669',
    'Overdue' => '#EF4444'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= e($invoice['invoice_number']) ?> - <?= e($invoice['business_name']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #1F2937;
            background: #f3f4f6;
            line-height: 1.6;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            padding: 3rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            border-radius: 12px;
        }
        
        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #F3F4F6;
        }
        
        .brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .brand-logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: cover;
        }
        
        .brand-logo-placeholder {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: #FF6B35;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .brand-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0A2342;
        }
        
        .invoice-title-block {
            text-align: right;
        }
        
        .invoice-title {
            font-size: 2rem;
            font-weight: 700;
            color: #FF6B35;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .invoice-number {
            font-size: 1rem;
            color: #6B7280;
            margin-top: 0.25rem;
        }
        
        .invoice-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2.5rem;
        }
        
        .info-section h4 {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #9CA3AF;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .info-section .name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 0.25rem;
        }
        
        .info-section p {
            font-size: 0.85rem;
            color: #6B7280;
            margin: 0.15rem 0;
        }
        
        /* Date Info */
        .date-info {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            padding: 1rem 1.5rem;
            background: #F9FAFB;
            border-radius: 8px;
        }
        
        .date-info .date-item {
            flex: 1;
        }
        
        .date-info label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #9CA3AF;
            font-weight: 600;
        }
        
        .date-info .value {
            font-size: 0.95rem;
            font-weight: 500;
            color: #1F2937;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        
        .items-table thead th {
            background: #0A2342;
            color: white;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }
        
        .items-table thead th:first-child {
            border-radius: 8px 0 0 0;
        }
        
        .items-table thead th:last-child {
            border-radius: 0 8px 0 0;
        }
        
        .items-table tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #F3F4F6;
            font-size: 0.9rem;
        }
        
        .items-table tbody tr:last-child td {
            border-bottom: 2px solid #E5E7EB;
        }
        
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        
        /* Totals */
        .totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 2rem;
        }
        
        .totals-table {
            width: 280px;
        }
        
        .totals-table .row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            font-size: 0.9rem;
        }
        
        .totals-table .row.total {
            border-top: 2px solid #0A2342;
            padding-top: 0.75rem;
            margin-top: 0.25rem;
        }
        
        .totals-table .row.total .label,
        .totals-table .row.total .value {
            font-size: 1.15rem;
            font-weight: 700;
            color: #FF6B35;
        }
        
        .totals-table .label {
            color: #6B7280;
        }
        
        .totals-table .value {
            font-weight: 600;
            color: #1F2937;
        }
        
        /* Notes */
        .invoice-notes {
            padding: 1rem 1.5rem;
            background: #FFFBEB;
            border-left: 3px solid #F59E0B;
            border-radius: 0 8px 8px 0;
            margin-bottom: 2rem;
        }
        
        .invoice-notes h4 {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #92400E;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .invoice-notes p {
            font-size: 0.85rem;
            color: #78350F;
            margin: 0;
        }
        
        /* Footer */
        .invoice-footer {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid #E5E7EB;
            color: #9CA3AF;
            font-size: 0.8rem;
        }
        
        /* Action Buttons (hidden on print) */
        .action-bar {
            max-width: 800px;
            margin: 0 auto 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .action-bar .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }
        
        .btn-back {
            background: #F3F4F6;
            color: #4B5563;
        }
        
        .btn-back:hover { background: #E5E7EB; }
        
        .btn-print {
            background: #FF6B35;
            color: white;
        }
        
        .btn-print:hover { background: #E55A2A; }
        
        /* Print styles */
        @media print {
            body {
                background: white;
            }
            
            .action-bar {
                display: none !important;
            }
            
            .invoice-container {
                margin: 0;
                padding: 1.5rem;
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }
            
            .invoice-status {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .items-table thead th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .date-info {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .invoice-notes {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        
        @page {
            size: A4;
            margin: 1.5cm;
        }
    </style>
</head>
<body>

<!-- Action Bar (hidden on print) -->
<div class="action-bar">
    <a href="invoice-detail.php?id=<?= $invoice_id ?>" class="btn btn-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Detail
    </a>
    <button class="btn btn-print" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak / Save as PDF
    </button>
</div>

<div class="invoice-container">
    <!-- Header -->
    <div class="invoice-header">
        <div class="brand">
            <?php if (!empty($invoice['logo_path']) && file_exists($invoice['logo_path'])): ?>
                <img src="<?= e($invoice['logo_path']) ?>" alt="Logo" class="brand-logo">
            <?php else: ?>
                <div class="brand-logo-placeholder">
                    <?= strtoupper(substr($invoice['business_name'], 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div>
                <div class="brand-name"><?= e($invoice['business_name']) ?></div>
                <?php if ($invoice['biz_category']): ?>
                    <div style="font-size: 0.8rem; color: #9CA3AF;"><?= e($invoice['biz_category']) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="invoice-title-block">
            <div class="invoice-title">Invoice</div>
            <div class="invoice-number"><?= e($invoice['invoice_number']) ?></div>
            <div class="invoice-status" style="background: <?= $status_colors[$invoice['status']] ?? '#6B7280' ?>">
                <?= e($invoice['status']) ?>
            </div>
        </div>
    </div>
    
    <!-- Date Info -->
    <div class="date-info">
        <div class="date-item">
            <label>Tanggal Invoice</label>
            <div class="value"><?= formatDate($invoice['created_at']) ?></div>
        </div>
        <div class="date-item">
            <label>Jatuh Tempo</label>
            <div class="value"><?= $invoice['due_date'] ? formatDate($invoice['due_date']) : '-' ?></div>
        </div>
        <div class="date-item">
            <label>Status</label>
            <div class="value"><?= e($invoice['status']) ?></div>
        </div>
    </div>
    
    <!-- From/To -->
    <div class="info-grid">
        <div class="info-section">
            <h4>Dari</h4>
            <div class="name"><?= e($invoice['business_name']) ?></div>
            <?php if ($invoice['biz_address']): ?>
                <p><?= nl2br(e($invoice['biz_address'])) ?></p>
            <?php endif; ?>
            <?php if ($invoice['biz_phone']): ?>
                <p><i class="fas fa-phone" style="width:14px;font-size:0.7rem;color:#9CA3AF;"></i> <?= e($invoice['biz_phone']) ?></p>
            <?php endif; ?>
            <?php if ($invoice['biz_email']): ?>
                <p><i class="fas fa-envelope" style="width:14px;font-size:0.7rem;color:#9CA3AF;"></i> <?= e($invoice['biz_email']) ?></p>
            <?php endif; ?>
        </div>
        <div class="info-section">
            <h4>Kepada</h4>
            <div class="name"><?= e($invoice['client_name']) ?></div>
            <?php if ($invoice['company']): ?>
                <p><?= e($invoice['company']) ?></p>
            <?php endif; ?>
            <?php if ($invoice['client_address']): ?>
                <p><?= nl2br(e($invoice['client_address'])) ?></p>
            <?php endif; ?>
            <?php if ($invoice['client_phone']): ?>
                <p><i class="fas fa-phone" style="width:14px;font-size:0.7rem;color:#9CA3AF;"></i> <?= e($invoice['client_phone']) ?></p>
            <?php endif; ?>
            <?php if ($invoice['client_email']): ?>
                <p><i class="fas fa-envelope" style="width:14px;font-size:0.7rem;color:#9CA3AF;"></i> <?= e($invoice['client_email']) ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Items -->
    <table class="items-table">
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
            <?php foreach ($items as $i => $item): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($item['description']) ?></td>
                <td class="text-center"><?= $item['quantity'] ?></td>
                <td class="text-end"><?= formatCurrency($item['unit_price']) ?></td>
                <td class="text-end"><?= formatCurrency($item['line_total']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Totals -->
    <div class="totals">
        <div class="totals-table">
            <div class="row">
                <span class="label">Subtotal</span>
                <span class="value"><?= formatCurrency($subtotal) ?></span>
            </div>
            <?php if ($invoice['tax_percent'] > 0): ?>
            <div class="row">
                <span class="label">Pajak (<?= $invoice['tax_percent'] ?>%)</span>
                <span class="value"><?= formatCurrency($tax_amount) ?></span>
            </div>
            <?php endif; ?>
            <div class="row total">
                <span class="label">Grand Total</span>
                <span class="value"><?= formatCurrency($grand_total) ?></span>
            </div>
        </div>
    </div>
    
    <!-- Notes -->
    <?php if ($invoice['notes']): ?>
    <div class="invoice-notes">
        <h4>Catatan</h4>
        <p><?= nl2br(e($invoice['notes'])) ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Footer -->
    <div class="invoice-footer">
        <p>Terima kasih atas kepercayaan Anda. | <?= e($invoice['business_name']) ?></p>
    </div>
</div>

</body>
</html>
