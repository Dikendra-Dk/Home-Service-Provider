<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please log in to submit a review']);
    exit;
}

$db = Database::getConnection();
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$bookingId = (int)($input['booking_id'] ?? 0);
$rating = (int)($input['rating'] ?? 5);
$comment = trim($input['comment'] ?? '');
$currentUser = getCurrentUser();

if ($bookingId <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid rating (1-5 stars)']);
    exit;
}

// Fetch booking to verify ownership and provider
$stmt = $db->prepare("SELECT * FROM bookings WHERE id = :id AND customer_id = :cid");
$stmt->execute(['id' => $bookingId, 'cid' => $currentUser['id']]);
$booking = $stmt->fetch();

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking not found or not owned by you']);
    exit;
}

if (!$booking['provider_id']) {
    echo json_encode(['success' => false, 'message' => 'No provider assigned to this booking']);
    exit;
}

try {
    // Insert review
    $rStmt = $db->prepare("
        INSERT INTO reviews (booking_id, customer_id, provider_id, rating, comment, created_at)
        VALUES (:bid, :cid, :pid, :rating, :comment, NOW())
        ON DUPLICATE KEY UPDATE rating = :u_rating, comment = :u_comment
    ");
    $rStmt->execute([
        'bid' => $bookingId,
        'cid' => $currentUser['id'],
        'pid' => $booking['provider_id'],
        'rating' => $rating,
        'comment' => $comment,
        'u_rating' => $rating,
        'u_comment' => $comment
    ]);

    // Recalculate average rating & review count for the provider
    $avgStmt = $db->prepare("SELECT AVG(rating) as new_avg, COUNT(id) as total_revs FROM reviews WHERE provider_id = :pid");
    $avgStmt->execute(['pid' => $booking['provider_id']]);
    $stats = $avgStmt->fetch();

    $newAvg = round((float)($stats['new_avg'] ?? 5.0), 2);
    $totalRevs = (int)($stats['total_revs'] ?? 1);

    $upStmt = $db->prepare("UPDATE provider_profiles SET rating_avg = :avg, review_count = :cnt WHERE id = :pid");
    $upStmt->execute(['avg' => $newAvg, 'cnt' => $totalRevs, 'pid' => $booking['provider_id']]);

    echo json_encode([
        'success' => true,
        'message' => 'Review submitted successfully! Thank you for rating the service.',
        'rating_avg' => $newAvg,
        'review_count' => $totalRevs
    ]);
    exit;
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error saving review: ' . $e->getMessage()]);
    exit;
}
