<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

$action = $_GET['action'] ?? '';
$db = Database::getConnection();

// 1. Create Booking
if ($action === 'create_booking') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please log in to book a service.', 'redirect' => '/login.php']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input)) {
        $input = $_POST;
    }

    $currentUser = getCurrentUser();
    $customerId = $currentUser['id'];

    $categoryId = (int)($input['category_id'] ?? 1);
    $providerId = !empty($input['provider_id']) ? (int)$input['provider_id'] : null;
    $serviceId = !empty($input['service_id']) ? (int)$input['service_id'] : null;
    $urgency = ($input['urgency'] ?? 'standard') === 'urgent_asap' ? 'urgent_asap' : 'standard';
    $scheduledDate = $input['scheduled_date'] ?? date('Y-m-d');
    $scheduledSlot = $input['scheduled_slot'] ?? 'Morning (8:00 AM - 11:00 AM)';
    
    $province = $input['province'] ?? 'Bagmati';
    $district = $input['district'] ?? 'Kathmandu';
    $municipality = $input['municipality'] ?? 'Kathmandu Metropolitan City';
    $wardNo = (int)($input['ward_no'] ?? 4);
    $streetAddress = $input['street_address'] ?? 'Kathmandu';
    $landmark = $input['landmark'] ?? '';
    $problemDescription = $input['problem_description'] ?? 'General maintenance service requested.';
    $estimatedAmount = (float)($input['estimated_amount'] ?? 500.00);
    $paymentMethod = in_array($input['payment_method'] ?? '', ['cash', 'esewa', 'khalti']) ? $input['payment_method'] : 'cash';

    // Generate unique booking code
    $bookingCode = 'SEWA-' . date('Y') . '-' . rand(1000, 9999);

    try {
        $stmt = $db->prepare("
            INSERT INTO bookings (
                booking_code, customer_id, provider_id, category_id, service_id,
                status, urgency, scheduled_date, scheduled_slot,
                province, district, municipality, ward_no,
                street_address, landmark, problem_description,
                estimated_amount, final_amount, payment_method, payment_status, created_at
            ) VALUES (
                :code, :cust_id, :prov_id, :cat_id, :srv_id,
                'pending', :urgency, :s_date, :s_slot,
                :prov, :dist, :muni, :ward,
                :street, :landmark, :problem,
                :est_amt, :final_amt, :pay_method, 'unpaid', NOW()
            )
        ");

        $stmt->execute([
            'code' => $bookingCode,
            'cust_id' => $customerId,
            'prov_id' => $providerId,
            'cat_id' => $categoryId,
            'srv_id' => $serviceId,
            'urgency' => $urgency,
            's_date' => $scheduledDate,
            's_slot' => $scheduledSlot,
            'prov' => $province,
            'dist' => $district,
            'muni' => $municipality,
            'ward' => $wardNo,
            'street' => $streetAddress,
            'landmark' => $landmark,
            'problem' => $problemDescription,
            'est_amt' => $estimatedAmount,
            'final_amt' => $estimatedAmount,
            'pay_method' => $paymentMethod
        ]);

        $newBookingId = $db->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Booking created successfully!',
            'booking_id' => $newBookingId,
            'booking_code' => $bookingCode
        ]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

// 2. Accept Booking (Provider action)
if ($action === 'accept_booking') {
    if (!isLoggedIn() || !isProvider()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $bookingId = (int)($input['booking_id'] ?? 0);
    $providerProfileId = $_SESSION['user']['provider_profile']['id'] ?? null;

    if (!$bookingId || !$providerProfileId) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking parameters']);
        exit;
    }

    $stmt = $db->prepare("UPDATE bookings SET status = 'accepted', provider_id = :prov_id WHERE id = :booking_id AND status = 'pending'");
    $stmt->execute(['prov_id' => $providerProfileId, 'booking_id' => $bookingId]);

    echo json_encode(['success' => true, 'message' => 'Booking accepted! On-the-way notice sent to customer.']);
    exit;
}

// 3. Reject Booking (Provider action)
if ($action === 'reject_booking') {
    if (!isLoggedIn() || !isProvider()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $bookingId = (int)($input['booking_id'] ?? 0);

    $stmt = $db->prepare("UPDATE bookings SET provider_id = NULL, status = 'pending' WHERE id = :booking_id");
    $stmt->execute(['booking_id' => $bookingId]);

    echo json_encode(['success' => true, 'message' => 'Booking declined. Request returned to queue.']);
    exit;
}

// 4. Update Status (in_progress, completed)
if ($action === 'update_status') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $bookingId = (int)($input['booking_id'] ?? 0);
    $newStatus = $input['status'] ?? '';

    if (!in_array($newStatus, ['accepted', 'in_progress', 'completed', 'cancelled'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }

    $stmt = $db->prepare("UPDATE bookings SET status = :status WHERE id = :booking_id");
    $stmt->execute(['status' => $newStatus, 'booking_id' => $bookingId]);

    // If completed, increment provider jobs_completed
    if ($newStatus === 'completed') {
        $bStmt = $db->prepare("SELECT provider_id FROM bookings WHERE id = :id");
        $bStmt->execute(['id' => $bookingId]);
        $b = $bStmt->fetch();
        if ($b && $b['provider_id']) {
            $db->prepare("UPDATE provider_profiles SET jobs_completed = jobs_completed + 1 WHERE id = :pid")->execute(['pid' => $b['provider_id']]);
        }
    }

    echo json_encode(['success' => true, 'message' => "Booking status updated to {$newStatus}."]);
    exit;
}

// 5. Confirm Payment Made (Customer Action)
if ($action === 'confirm_payment') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $bookingId = (int)($input['booking_id'] ?? 0);
    $paymentMethod = $input['payment_method'] ?? 'cash';

    $stmt = $db->prepare("UPDATE bookings SET payment_status = 'paid', payment_method = :method, status = 'completed' WHERE id = :booking_id");
    $stmt->execute(['method' => $paymentMethod, 'booking_id' => $bookingId]);

    echo json_encode(['success' => true, 'message' => 'Payment confirmed successfully! Thank you for using SewaSathi.']);
    exit;
}

// 6. Cancel Booking
if ($action === 'cancel_booking') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $bookingId = (int)($input['booking_id'] ?? 0);
    $reason = $input['reason'] ?? 'Customer requested cancellation';

    $stmt = $db->prepare("UPDATE bookings SET status = 'cancelled', cancellation_reason = :reason WHERE id = :booking_id");
    $stmt->execute(['reason' => $reason, 'booking_id' => $bookingId]);

    echo json_encode(['success' => true, 'message' => 'Booking has been cancelled.']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
