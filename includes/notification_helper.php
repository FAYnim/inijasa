<?php
/**
 * Notification Helper Functions
 * Generates automated in-app notifications
 */

/**
 * Generate daily notifications for a business
 * Limited to run at most once per 30 minutes per session to save resources.
 */
function generate_daily_notifications($conn, $business_id) {
    if (!isset($_SESSION['last_notif_gen']) || time() - $_SESSION['last_notif_gen'] > 1800) {
        
        // 1. Check for stale deals (> 14 days no update)
        $stale_days = 14;
        $stale_stmt = mysqli_prepare($conn, "
            SELECT d.id, d.deal_title 
            FROM deals d
            LEFT JOIN notifications n ON n.related_id = d.id AND n.type = 'deal_stale' AND n.is_read = 0
            WHERE d.business_id = ? 
            AND d.current_stage NOT IN ('Won', 'Lost')
            AND d.updated_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            AND n.id IS NULL
        ");
        
        if ($stale_stmt) {
            mysqli_stmt_bind_param($stale_stmt, "ii", $business_id, $stale_days);
            mysqli_stmt_execute($stale_stmt);
            $result = mysqli_stmt_get_result($stale_stmt);
            
            while ($row = mysqli_fetch_assoc($result)) {
                $message = "Deal '" . $row['deal_title'] . "' belum ada aktivitas selama lebih dari $stale_days hari.";
                insert_notification($conn, $business_id, 'deal_stale', $message, $row['id']);
            }
            mysqli_stmt_close($stale_stmt);
        }
        
        // 2. Check for expected close date passed
        $close_stmt = mysqli_prepare($conn, "
            SELECT d.id, d.deal_title, d.expected_close_date
            FROM deals d
            LEFT JOIN notifications n ON n.related_id = d.id AND n.type = 'close_date_passed' AND n.is_read = 0
            WHERE d.business_id = ? 
            AND d.current_stage NOT IN ('Won', 'Lost')
            AND d.expected_close_date < CURDATE()
            AND n.id IS NULL
        ");
        
        if ($close_stmt) {
            mysqli_stmt_bind_param($close_stmt, "i", $business_id);
            mysqli_stmt_execute($close_stmt);
            $result = mysqli_stmt_get_result($close_stmt);
            
            while ($row = mysqli_fetch_assoc($result)) {
                $date_str = date('d/m/Y', strtotime($row['expected_close_date']));
                $message = "Estimasi penutupan deal '" . $row['deal_title'] . "' ($date_str) sudah lewat.";
                insert_notification($conn, $business_id, 'close_date_passed', $message, $row['id']);
            }
            mysqli_stmt_close($close_stmt);
        }
        
        // 3. Check for overdue invoices
        $overdue_stmt = mysqli_prepare($conn, "
            SELECT i.id, i.invoice_number, i.due_date
            FROM invoices i
            LEFT JOIN notifications n ON n.related_id = i.id AND n.type = 'overdue_payment' AND n.is_read = 0
            WHERE i.business_id = ? 
            AND i.status != 'Paid'
            AND i.due_date < CURDATE()
            AND n.id IS NULL
        ");
        
        if ($overdue_stmt) {
            mysqli_stmt_bind_param($overdue_stmt, "i", $business_id);
            mysqli_stmt_execute($overdue_stmt);
            $result = mysqli_stmt_get_result($overdue_stmt);
            
            while ($row = mysqli_fetch_assoc($result)) {
                $message = "Invoice " . $row['invoice_number'] . " telah melewati batas waktu pembayaran.";
                insert_notification($conn, $business_id, 'overdue_payment', $message, $row['id']);
            }
            mysqli_stmt_close($overdue_stmt);
        }
        
        $_SESSION['last_notif_gen'] = time();
    }
}

/**
 * Helper to insert a single notification
 */
function insert_notification($conn, $business_id, $type, $message, $related_id) {
    // Check if duplicate message exists for the exact same related item (avoid spam)
    $check_stmt = mysqli_prepare($conn, "
        SELECT id FROM notifications 
        WHERE business_id = ? AND type = ? AND related_id = ? AND is_read = 0
    ");
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "isi", $business_id, $type, $related_id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        $exists = (mysqli_num_rows($result) > 0);
        mysqli_stmt_close($check_stmt);
        
        if ($exists) {
            return; // Duplicate unread notification already exists
        }
    }
    
    $stmt = mysqli_prepare($conn, "
        INSERT INTO notifications (business_id, type, message, related_id)
        VALUES (?, ?, ?, ?)
    ");
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issi", $business_id, $type, $message, $related_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

/**
 * Get unread notifications for a business
 */
function get_unread_notifications($conn, $business_id, $limit = 10) {
    $notifications = [];
    $stmt = mysqli_prepare($conn, "
        SELECT * FROM notifications
        WHERE business_id = ? AND is_read = 0
        ORDER BY created_at DESC
        LIMIT ?
    ");
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $business_id, $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $notifications[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
    
    return $notifications;
}

/**
 * Get count of unread notifications
 */
function get_unread_notification_count($conn, $business_id) {
    $count = 0;
    $stmt = mysqli_prepare($conn, "
        SELECT COUNT(*) as count FROM notifications
        WHERE business_id = ? AND is_read = 0
    ");
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $business_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $count = $row['count'];
        }
        mysqli_stmt_close($stmt);
    }
    
    return $count;
}
