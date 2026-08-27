<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

$action = $_GET['action'] ?? '';

if ($action === 'set_session_location') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!empty($input)) {
        $_SESSION['selected_district'] = $input['district'] ?? 'Kathmandu';
        $_SESSION['selected_municipality'] = $input['municipality'] ?? 'Kathmandu Metropolitan City';
        $_SESSION['selected_ward'] = (int)($input['ward_no'] ?? 4);

        echo json_encode([
            'success' => true,
            'district' => $_SESSION['selected_district'],
            'municipality' => $_SESSION['selected_municipality'],
            'ward_no' => $_SESSION['selected_ward']
        ]);
        exit;
    }
}

if ($action === 'get_locations') {
    echo json_encode([
        'success' => true,
        'data' => getNepaliLocations()
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
