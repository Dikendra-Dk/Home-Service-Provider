<?php
$pageTitle = 'Customer Dashboard';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin('/login.php');

$currentUser = getCurrentUser();
$db = Database::getConnection();

// Fetch Active Bookings (pending, accepted, in_progress)
$activeStmt = $db->prepare("
    SELECT b.*, c.name as category_name, c.icon_name, 
           pp.id as p_profile_id, pp.hourly_rate as p_rate, pp.rating_avg as p_rating,
           u.full_name as provider_name, u.phone as provider_phone, u.avatar_url as provider_avatar
    FROM bookings b
    JOIN categories c ON b.category_id = c.id
    LEFT JOIN provider_profiles pp ON b.provider_id = pp.id
    LEFT JOIN users u ON pp.user_id = u.id
    WHERE b.customer_id = :cid AND b.status IN ('pending', 'accepted', 'in_progress')
    ORDER BY b.created_at DESC
");
$activeStmt->execute(['cid' => $currentUser['id']]);
$activeBookings = $activeStmt->fetchAll();

// Fetch Completed / Past Bookings (completed, cancelled)
$historyStmt = $db->prepare("
    SELECT b.*, c.name as category_name, c.icon_name,
           pp.id as p_profile_id, u.full_name as provider_name, u.avatar_url as provider_avatar,
           r.id as review_id, r.rating as review_rating, r.comment as review_comment
    FROM bookings b
    JOIN categories c ON b.category_id = c.id
    LEFT JOIN provider_profiles pp ON b.provider_id = pp.id
    LEFT JOIN users u ON pp.user_id = u.id
    LEFT JOIN reviews r ON b.id = r.booking_id
    WHERE b.customer_id = :cid AND b.status IN ('completed', 'cancelled')
    ORDER BY b.updated_at DESC
");
$historyStmt->execute(['cid' => $currentUser['id']]);
$pastBookings = $historyStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<main class="flex-grow py-8 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header User Banner -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <img src="<?= htmlspecialchars($currentUser['avatar_url']) ?>" alt="<?= htmlspecialchars($currentUser['full_name']) ?>" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-slate-100 shadow-sm">
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900"><?= htmlspecialchars($currentUser['full_name']) ?></h1>
                    <p class="text-xs text-slate-500 font-medium flex items-center gap-2 mt-0.5">
                        <span><?= htmlspecialchars($currentUser['email']) ?></span>
                        <span>•</span>
                        <span><?= htmlspecialchars($currentUser['phone']) ?></span>
                        <span>•</span>
                        <span class="text-brand font-bold"><?= htmlspecialchars($currentUser['district']) ?>, Ward <?= htmlspecialchars((string)$currentUser['ward_no']) ?></span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="/search.php" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-extrabold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-md shadow-rose-600/20 active:scale-95 transition-all">
                    <?= icon('plus', 'w-4 h-4 text-white') ?>
                    <span>Book New Service</span>
                </a>
            </div>
        </div>

        <!-- Dashboard Tabs Nav -->
        <div class="border-b border-slate-200 mb-6">
            <nav class="flex space-x-6 text-xs font-extrabold uppercase tracking-wider" aria-label="Tabs">
                <button type="button" onclick="switchCustomerTab('active')" id="tabBtnActive" class="tab-btn pb-3.5 border-b-2 border-brand text-brand flex items-center gap-2">
                    <?= icon('clock', 'w-4 h-4') ?>
                    <span>Active Bookings (<?= count($activeBookings) ?>)</span>
                </button>
                <button type="button" onclick="switchCustomerTab('history')" id="tabBtnHistory" class="tab-btn pb-3.5 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2">
                    <?= icon('file-text', 'w-4 h-4') ?>
                    <span>Booking History (<?= count($pastBookings) ?>)</span>
                </button>
                <button type="button" onclick="switchCustomerTab('profile')" id="tabBtnProfile" class="tab-btn pb-3.5 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2">
                    <?= icon('user', 'w-4 h-4') ?>
                    <span>My Profile & Address</span>
                </button>
            </nav>
        </div>

        <!-- TAB 1: ACTIVE BOOKINGS -->
        <div id="tabContentActive" class="space-y-6">
            <?php if (!empty($activeBookings)): ?>
                <?php foreach ($activeBookings as $booking): 
                    $status = $booking['status'];
                    $statusMeta = STATUS_CONFIG[$status] ?? STATUS_CONFIG['pending'];
                ?>
                    <div class="card-soft p-6 bg-white rounded-3xl border border-slate-200/90 shadow-md space-y-6">
                        
                        <!-- Top Booking Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2.5">
                                    <span class="text-xs font-mono font-bold text-slate-400">#<?= htmlspecialchars($booking['booking_code']) ?></span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border <?= $statusMeta['badge_class'] ?>">
                                        <?= $statusMeta['label'] ?> (<?= $statusMeta['nepali_label'] ?>)
                                    </span>
                                    <?php if ($booking['urgency'] === 'urgent_asap'): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800">⚡ Urgent Dispatch</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="text-base font-extrabold text-slate-900 mt-1"><?= htmlspecialchars($booking['category_name']) ?></h3>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Estimated Amount</span>
                                <span class="text-base font-extrabold text-slate-900"><?= formatNpr($booking['estimated_amount']) ?></span>
                            </div>
                        </div>

                        <!-- Progress Step Tracker (Pending -> Accepted -> In Progress -> Completed) -->
                        <div class="py-2">
                            <div class="flex items-center justify-between relative max-w-2xl mx-auto">
                                <?php
                                $steps = [
                                    1 => ['title' => 'Pending', 'sub' => 'Dispatched'],
                                    2 => ['title' => 'Accepted', 'sub' => 'Worker Assigned'],
                                    3 => ['title' => 'In Progress', 'sub' => 'Working on-site'],
                                    4 => ['title' => 'Completed', 'sub' => 'Ready to pay']
                                ];
                                $currStep = $statusMeta['step'];
                                ?>
                                <?php foreach ($steps as $stepNum => $stepInfo): ?>
                                    <div class="flex flex-col items-center text-center z-10">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs <?= $currStep >= $stepNum ? 'bg-brand text-white shadow-md' : 'bg-slate-100 text-slate-400 border border-slate-200' ?>">
                                            <?= $currStep > $stepNum ? icon('check-circle', 'w-4 h-4 text-white') : $stepNum ?>
                                        </div>
                                        <span class="text-xs font-extrabold <?= $currStep >= $stepNum ? 'text-slate-900' : 'text-slate-400' ?> mt-1.5"><?= $stepInfo['title'] ?></span>
                                        <span class="text-[10px] text-slate-400"><?= $stepInfo['sub'] ?></span>
                                    </div>
                                    <?php if ($stepNum < 4): ?>
                                        <div class="flex-grow h-1 mx-2 -mt-6 rounded <?= $currStep > $stepNum ? 'bg-brand' : 'bg-slate-200' ?>"></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs">
                            <div class="space-y-1.5">
                                <span class="font-bold text-slate-500 uppercase text-[10px] block">Schedule & Ward Location</span>
                                <p class="text-slate-800 font-semibold flex items-center gap-1.5">
                                    <?= icon('calendar', 'w-3.5 h-3.5 text-slate-400') ?>
                                    <span><?= htmlspecialchars($booking['scheduled_date']) ?> • <?= htmlspecialchars($booking['scheduled_slot']) ?></span>
                                </p>
                                <p class="text-slate-800 font-semibold flex items-center gap-1.5">
                                    <?= icon('map-pin', 'w-3.5 h-3.5 text-brand') ?>
                                    <span><?= htmlspecialchars($booking['street_address']) ?>, Ward <?= htmlspecialchars((string)$booking['ward_no']) ?>, <?= htmlspecialchars($booking['municipality']) ?></span>
                                </p>
                            </div>

                            <div class="space-y-1.5">
                                <span class="font-bold text-slate-500 uppercase text-[10px] block">Problem Description</span>
                                <p class="text-slate-700 italic bg-white p-2.5 rounded-xl border border-slate-200">
                                    "<?= htmlspecialchars($booking['problem_description']) ?>"
                                </p>
                            </div>
                        </div>

                        <!-- Worker Contact Card if Assigned -->
                        <?php if ($booking['provider_name']): ?>
                            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <img src="<?= htmlspecialchars($booking['provider_avatar']) ?>" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-white">
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <h4 class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($booking['provider_name']) ?></h4>
                                            <span class="text-brand"><?= icon('verified', 'w-4 h-4 text-brand') ?></span>
                                        </div>
                                        <p class="text-xs text-emerald-800 font-semibold">Assigned Verified Technician</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <a href="tel:<?= htmlspecialchars($booking['provider_phone']) ?>" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-sm">
                                        <?= icon('phone', 'w-3.5 h-3.5') ?>
                                        <span>Call (<?= htmlspecialchars($booking['provider_phone']) ?>)</span>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Actions Bar -->
                        <div class="pt-2 flex items-center justify-between">
                            <span class="text-xs text-slate-400">Payment: <strong><?= strtoupper($booking['payment_method']) ?></strong> (<?= $booking['payment_status'] ?>)</span>
                            
                            <?php if ($status === 'pending'): ?>
                                <button type="button" onclick="cancelCustomerBooking(<?= $booking['id'] ?>)" class="px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                                    Cancel Booking
                                </button>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card-soft p-12 bg-white rounded-3xl text-center border border-slate-200/80 space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 text-brand flex items-center justify-center mx-auto">
                        <?= icon('file-text', 'w-7 h-7 text-brand') ?>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">No Active Service Bookings</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">You don't have any ongoing home repairs. Need a plumber, electrician, or cleaner?</p>
                    <div>
                        <a href="/search.php" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-extrabold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-md">
                            <?= icon('search', 'w-4 h-4 text-white') ?>
                            <span>Browse & Book Services</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- TAB 2: BOOKING HISTORY -->
        <div id="tabContentHistory" class="hidden space-y-4">
            <?php if (!empty($pastBookings)): ?>
                <?php foreach ($pastBookings as $past): 
                    $pStatus = $past['status'];
                    $pStatusMeta = STATUS_CONFIG[$pStatus] ?? STATUS_CONFIG['completed'];
                ?>
                    <div class="card-soft p-5 bg-white rounded-2xl border border-slate-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="space-y-1.5 min-w-0 flex-grow">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono font-bold text-slate-400">#<?= htmlspecialchars($past['booking_code']) ?></span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold border <?= $pStatusMeta['badge_class'] ?>">
                                    <?= $pStatusMeta['label'] ?>
                                </span>
                                <span class="text-xs text-slate-400">• <?= date('M j, Y', strtotime($past['scheduled_date'])) ?></span>
                            </div>
                            <h4 class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($past['category_name']) ?></h4>
                            <p class="text-xs text-slate-500"><?= htmlspecialchars($past['street_address']) ?>, Ward <?= htmlspecialchars((string)$past['ward_no']) ?></p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 shrink-0">
                            <div class="text-left sm:text-right">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Final Paid</span>
                                <span class="text-sm font-extrabold text-slate-900"><?= formatNpr($past['final_amount'] ?: $past['estimated_amount']) ?></span>
                            </div>

                            <!-- Actions: Confirm Payment / Leave Review -->
                            <?php if ($past['status'] === 'completed'): ?>
                                <?php if ($past['payment_status'] === 'unpaid'): ?>
                                    <button type="button" onclick="openPaymentConfirmModal(<?= $past['id'] ?>, '<?= formatNpr($past['final_amount'] ?: $past['estimated_amount']) ?>')" class="px-3.5 py-2 text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm">
                                        Confirm Payment Made
                                    </button>
                                <?php endif; ?>

                                <?php if (empty($past['review_id'])): ?>
                                    <button type="button" onclick="openReviewModal(<?= $past['id'] ?>, '<?= htmlspecialchars($past['provider_name'] ?: 'Technician') ?>')" class="px-3.5 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl">
                                        Leave Review ★
                                    </button>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                                        <?= icon('star', 'w-3.5 h-3.5 text-amber-500') ?>
                                        <span>Rated <?= $past['review_rating'] ?>★</span>
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-8 text-center bg-white rounded-2xl text-xs text-slate-400 border border-slate-200">
                    No past booking records found.
                </div>
            <?php endif; ?>
        </div>

        <!-- TAB 3: CUSTOMER PROFILE & ADDRESS -->
        <div id="tabContentProfile" class="hidden">
            <div class="card-soft p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/90 max-w-2xl space-y-6">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Personal Details & Primary Ward Address</h2>
                    <p class="text-xs text-slate-500">Your details are pre-filled during new bookings for faster dispatch.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Full Name</label>
                        <input type="text" value="<?= htmlspecialchars($currentUser['full_name']) ?>" disabled class="w-full rounded-xl border-slate-200 bg-slate-100 p-2.5 text-slate-700 font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Mobile Phone (+977)</label>
                        <input type="text" value="<?= htmlspecialchars($currentUser['phone']) ?>" disabled class="w-full rounded-xl border-slate-200 bg-slate-100 p-2.5 text-slate-700 font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                        <input type="email" value="<?= htmlspecialchars($currentUser['email']) ?>" disabled class="w-full rounded-xl border-slate-200 bg-slate-100 p-2.5 text-slate-700 font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Primary District</label>
                        <input type="text" value="<?= htmlspecialchars($currentUser['district']) ?>" disabled class="w-full rounded-xl border-slate-200 bg-slate-100 p-2.5 text-slate-700 font-semibold">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Municipality & Ward</label>
                        <input type="text" value="<?= htmlspecialchars($currentUser['municipality']) ?>, Ward <?= htmlspecialchars((string)$currentUser['ward_no']) ?>" disabled class="w-full rounded-xl border-slate-200 bg-slate-100 p-2.5 text-slate-700 font-semibold">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400">Account status: <strong class="text-emerald-600">Active & Verified</strong></span>
                    <a href="/logout.php" class="px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl">
                        Log Out
                    </a>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Leave Review Modal -->
<div id="customerReviewModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-5 animate-in fade-in zoom-in-95">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Rate Your Service Experience</h3>
                <p id="reviewWorkerName" class="text-xs text-slate-500">How was your service with Ram Bahadur?</p>
            </div>
            <button type="button" onclick="closeReviewModal()" class="text-slate-400 hover:text-slate-600">
                <?= icon('x', 'w-5 h-5') ?>
            </button>
        </div>

        <form id="reviewSubmitForm" onsubmit="submitCustomerReview(event)" class="space-y-4">
            <input type="hidden" id="reviewBookingId" value="">
            
            <!-- 5-Star Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Rating (1 to 5 Stars)</label>
                <div class="flex items-center gap-3 justify-center py-3 bg-slate-50 rounded-2xl border border-slate-200">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                        <button type="button" onclick="setReviewStar(<?= $s ?>)" class="star-choice-btn p-1 text-slate-300 hover:text-amber-400 transition-colors" data-star="<?= $s ?>">
                            <?= icon('star', 'w-8 h-8 fill-current') ?>
                        </button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" id="reviewRatingVal" value="5">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Feedback / Comments</label>
                <textarea id="reviewCommentText" rows="3" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50" placeholder="Was the technician polite? Did they arrive on time and resolve the problem properly?"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeReviewModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-xs font-extrabold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-md">Submit Review</button>
            </div>
        </form>
    </div>
</div>

<!-- Payment Confirmation Modal -->
<div id="paymentConfirmModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-center space-y-4 animate-in fade-in zoom-in-95">
        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
            <?= icon('wallet', 'w-6 h-6 text-emerald-600') ?>
        </div>
        <div>
            <h3 class="text-base font-extrabold text-slate-900">Confirm Payment Made</h3>
            <p id="confirmPaymentAmountText" class="text-xs text-slate-500 mt-1">Amount: रू 850.00</p>
        </div>
        <p class="text-xs text-slate-600">
            Did you complete the payment to the worker via Cash, eSewa QR, or Khalti?
        </p>
        <div class="pt-2 grid grid-cols-2 gap-2">
            <button type="button" onclick="closePaymentConfirmModal()" class="py-2.5 text-xs font-bold text-slate-600 bg-slate-100 rounded-xl">Not Yet</button>
            <button type="button" onclick="submitPaymentConfirmation()" class="py-2.5 text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md">Yes, Paid</button>
        </div>
    </div>
</div>

<script>
let activePayBookingId = null;

function switchCustomerTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('border-brand', 'text-brand');
        b.classList.add('border-transparent', 'text-slate-500');
    });
    document.getElementById('tabContentActive').classList.add('hidden');
    document.getElementById('tabContentHistory').classList.add('hidden');
    document.getElementById('tabContentProfile').classList.add('hidden');

    if (tab === 'active') {
        document.getElementById('tabBtnActive').classList.add('border-brand', 'text-brand');
        document.getElementById('tabContentActive').classList.remove('hidden');
    } else if (tab === 'history') {
        document.getElementById('tabBtnHistory').classList.add('border-brand', 'text-brand');
        document.getElementById('tabContentHistory').classList.remove('hidden');
    } else if (tab === 'profile') {
        document.getElementById('tabBtnProfile').classList.add('border-brand', 'text-brand');
        document.getElementById('tabContentProfile').classList.remove('hidden');
    }
}

function cancelCustomerBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking request?')) {
        fetch('/api/bookings.php?action=cancel_booking', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'info');
                setTimeout(() => window.location.reload(), 500);
            }
        });
    }
}

function openReviewModal(bookingId, workerName) {
    document.getElementById('reviewBookingId').value = bookingId;
    document.getElementById('reviewWorkerName').textContent = `How was your service with ${workerName}?`;
    setReviewStar(5);
    document.getElementById('customerReviewModal').classList.remove('hidden');
}

function closeReviewModal() {
    document.getElementById('customerReviewModal').classList.add('hidden');
}

function setReviewStar(rating) {
    document.getElementById('reviewRatingVal').value = rating;
    document.querySelectorAll('.star-choice-btn').forEach(btn => {
        const star = parseInt(btn.dataset.star);
        if (star <= rating) {
            btn.className = 'star-choice-btn p-1 text-amber-400 transition-colors';
        } else {
            btn.className = 'star-choice-btn p-1 text-slate-200 transition-colors';
        }
    });
}

function submitCustomerReview(e) {
    e.preventDefault();
    const bookingId = document.getElementById('reviewBookingId').value;
    const rating = document.getElementById('reviewRatingVal').value;
    const comment = document.getElementById('reviewCommentText').value.trim();

    fetch('/api/reviews.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ booking_id: bookingId, rating: rating, comment: comment })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeReviewModal();
            setTimeout(() => window.location.reload(), 600);
        } else {
            showToast(data.message, 'error');
        }
    });
}

function openPaymentConfirmModal(bookingId, amount) {
    activePayBookingId = bookingId;
    document.getElementById('confirmPaymentAmountText').textContent = `Total Amount: ${amount}`;
    document.getElementById('paymentConfirmModal').classList.remove('hidden');
}

function closePaymentConfirmModal() {
    document.getElementById('paymentConfirmModal').classList.add('hidden');
    activePayBookingId = null;
}

function submitPaymentConfirmation() {
    if (!activePayBookingId) return;

    fetch('/api/bookings.php?action=confirm_payment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ booking_id: activePayBookingId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closePaymentConfirmModal();
            setTimeout(() => window.location.reload(), 600);
        } else {
            showToast(data.message, 'error');
        }
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
