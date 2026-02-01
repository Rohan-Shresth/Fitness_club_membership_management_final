<?php
require "../config/db.php";

header('Content-Type: application/json');

if (!isset($_POST['member_id']) || !is_numeric($_POST['member_id'])) {
    echo json_encode(['valid' => false, 'message' => 'Invalid member ID']);
    exit;
}

$member_id = (int)$_POST['member_id'];

try {
    $stmt = $pdo->prepare("
        SELECT id, full_name, membership_type, membership_expiry, membership_status
        FROM customers
        WHERE id = ?
    ");
    $stmt->execute([$member_id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$member) {
        echo json_encode(['valid' => false, 'message' => 'Member not found']);
        exit;
    }
    
    $today = date('Y-m-d');
    $expiry = $member['membership_expiry'];
    $status = $member['membership_status'];
    
    $valid = false;
    $message = '';
    $warning = '';
    
    if ($status === 'suspended') {
        $message = 'Membership is suspended. Please contact administration.';
    } elseif ($status === 'expired' || ($expiry && $expiry < $today)) {
        $message = 'Membership has expired on ' . date('d M Y', strtotime($expiry));
    } else {
        $valid = true;
        $message = 'Membership is active';
        
        // Check if expiring soon (within 7 days)
        if ($expiry) {
            $days_left = (strtotime($expiry) - strtotime($today)) / (60 * 60 * 24);
            if ($days_left <= 7 && $days_left > 0) {
                $warning = 'Membership expires in ' . ceil($days_left) . ' days';
            }
        }
    }
    
    echo json_encode([
        'valid' => $valid,
        'message' => $message,
        'warning' => $warning,
        'member_name' => $member['full_name'],
        'membership_type' => $member['membership_type'],
        'expiry_date' => $expiry ? date('d M Y', strtotime($expiry)) : 'N/A'
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['valid' => false, 'message' => 'Database error']);
}