<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized admin access']);
    exit;
}

$db = Database::getConnection();
$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

if ($action === 'verify_provider') {
    $providerId = (int)($input['provider_id'] ?? 0);
    $decision = $input['decision'] ?? 'approved'; // 'approved' or 'rejected'
    $remarks = trim($input['remarks'] ?? '');

    if ($providerId <= 0 || !in_array($decision, ['approved', 'rejected'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid verification parameters']);
        exit;
    }

    $isVerified = ($decision === 'approved') ? 1 : 0;

    $stmt = $db->prepare("
        UPDATE provider_profiles 
        SET is_verified = :is_v, verification_status = :v_stat, verification_remarks = :rem, updated_at = NOW()
        WHERE id = :pid
    ");
    $stmt->execute([
        'is_v' => $isVerified,
        'v_stat' => $decision,
        'rem' => $remarks ?: ($decision === 'approved' ? 'Verified CTEVT & Citizenship document verified by admin.' : 'Document rejected due to illegible scan or missing details.'),
        'pid' => $providerId
    ]);

    echo json_encode([
        'success' => true,
        'message' => $decision === 'approved' ? 'Provider successfully verified and approved!' : 'Provider verification rejected with remarks.',
        'status' => $decision
    ]);
    exit;
}

if ($action === 'toggle_user_status') {
    $userId = (int)($input['user_id'] ?? 0);
    $status = $input['status'] ?? 'active';

    if ($userId <= 0 || !in_array($status, ['active', 'suspended', 'inactive'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        exit;
    }

    $stmt = $db->prepare("UPDATE users SET status = :status WHERE id = :uid");
    $stmt->execute(['status' => $status, 'uid' => $userId]);

    echo json_encode(['success' => true, 'message' => "User status changed to {$status}."]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
