<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

$action = $_GET['action'] ?? '';
$db = Database::getConnection();

if (!isLoggedIn() || !isProvider()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized provider access']);
    exit;
}

$currentUser = getCurrentUser();
$providerProfile = $currentUser['provider_profile'] ?? null;

if (!$providerProfile) {
    // Attempt fetch
    $stmt = $db->prepare("SELECT * FROM provider_profiles WHERE user_id = :uid");
    $stmt->execute(['uid' => $currentUser['id']]);
    $providerProfile = $stmt->fetch();
}

if ($action === 'toggle_availability') {
    $input = json_decode(file_get_contents('php://input'), true);
    $newStatus = isset($input['is_available']) ? (int)$input['is_available'] : ($providerProfile['is_available'] ? 0 : 1);

    $stmt = $db->prepare("UPDATE provider_profiles SET is_available = :status WHERE user_id = :uid");
    $stmt->execute(['status' => $newStatus, 'uid' => $currentUser['id']]);

    // Update session
    $_SESSION['user']['provider_profile']['is_available'] = $newStatus;

    echo json_encode([
        'success' => true,
        'is_available' => (bool)$newStatus,
        'message' => $newStatus ? 'You are now marked Available for jobs in your ward!' : 'You are marked Busy. No new requests will be sent.'
    ]);
    exit;
}

if ($action === 'update_profile') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input)) $input = $_POST;

    $bio = $input['bio'] ?? '';
    $tagline = $input['tagline'] ?? '';
    $hourlyRate = (float)($input['hourly_rate'] ?? 450.00);
    $experienceYears = (int)($input['experience_years'] ?? 1);
    $serviceWards = isset($input['service_wards']) ? json_encode($input['service_wards']) : null;

    $stmt = $db->prepare("
        UPDATE provider_profiles 
        SET bio = :bio, tagline = :tagline, hourly_rate = :rate, experience_years = :exp, service_wards = :wards
        WHERE user_id = :uid
    ");
    $stmt->execute([
        'bio' => $bio,
        'tagline' => $tagline,
        'rate' => $hourlyRate,
        'exp' => $experienceYears,
        'wards' => $serviceWards,
        'uid' => $currentUser['id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Provider profile updated successfully!']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
