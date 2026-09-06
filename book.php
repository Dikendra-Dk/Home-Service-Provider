<?php
$pageTitle = 'Book Home Service';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/auth.php';

// Require customer login to book
if (!isLoggedIn()) {
    $_SESSION['flash_error'] = 'Please log in to complete your service booking.';
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: /login.php");
    exit;
}

$currentUser = getCurrentUser();
$db = Database::getConnection();

$selectedCatId = (int)($_GET['category'] ?? 1);
$selectedProvId = !empty($_GET['provider_id']) ? (int)$_GET['provider_id'] : null;

// Fetch all categories
$categories = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

// Fetch specific category
$catStmt = $db->prepare("SELECT * FROM categories WHERE id = :id");
$catStmt->execute(['id' => $selectedCatId]);
$activeCategory = $catStmt->fetch() ?: $categories[0];

// Fetch sub-services for this category
$srvStmt = $db->prepare("SELECT * FROM services WHERE category_id = :cid ORDER BY price ASC");
$srvStmt->execute(['cid' => $activeCategory['id']]);
$subServices = $srvStmt->fetchAll();

// Fetch selected provider if specified
$selectedProvider = null;
if ($selectedProvId) {
    $pStmt = $db->prepare("SELECT pp.*, u.full_name, u.avatar_url, u.district, u.ward_no FROM provider_profiles pp JOIN users u ON pp.user_id = u.id WHERE pp.id = :pid");
    $pStmt->execute(['pid' => $selectedProvId]);
    $selectedProvider = $pStmt->fetch();
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="flex-grow py-8 bg-slate-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Step Progress Header -->
        <div class="mb-8 text-center">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Book <?= htmlspecialchars($activeCategory['name']) ?> Service
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Verified technician dispatched directly to your home in Nepal</p>

            <!-- 3-Step Indicator Bar -->
            <div class="mt-6 flex items-center justify-center max-w-md mx-auto">
                <div class="flex items-center gap-2">
                    <div id="stepIndicator1" class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center font-bold text-xs ring-4 ring-rose-100 shadow-md">1</div>
                    <span class="text-xs font-bold text-slate-800">Issue Details</span>
                </div>
                <div class="w-12 h-0.5 bg-slate-200 mx-2"></div>
                <div class="flex items-center gap-2">
                    <div id="stepIndicator2" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center font-bold text-xs">2</div>
                    <span class="text-xs font-bold text-slate-400">Time & Ward</span>
                </div>
                <div class="w-12 h-0.5 bg-slate-200 mx-2"></div>
                <div class="flex items-center gap-2">
                    <div id="stepIndicator3" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center font-bold text-xs">3</div>
                    <span class="text-xs font-bold text-slate-400">Conform</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Booking Wizard Left Form (8 Cols) -->
            <div class="lg:col-span-8">
                <div class="card-soft p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/90 shadow-lg">
                    
                    <!-- STEP 1: SERVICE & ISSUE DETAILS -->
                    <div id="bookingStep1" class="space-y-6">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900 mb-1">Step 1: What is the issue?</h2>
                            <p class="text-xs text-slate-500">Provide details so the worker carries the exact tools and spare parts.</p>
                        </div>

                        <!-- Selected Provider Pill if assigned -->
                        <?php if ($selectedProvider): ?>
                            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/80 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="<?= htmlspecialchars($selectedProvider['avatar_url']) ?>" class="w-10 h-10 rounded-xl object-cover ring-2 ring-white">
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-rose-500 block">Assigned Worker</span>
                                        <h4 class="text-xs font-extrabold text-slate-900"><?= htmlspecialchars($selectedProvider['full_name']) ?></h4>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md">Requested</span>
                            </div>
                        <?php endif; ?>

                        <!-- Specific Sub-Service Radio Selector -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Select Service Sub-Type</label>
                            <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                <label class="flex items-center justify-between p-3 rounded-xl border border-rose-300 bg-rose-50/40 cursor-pointer transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <input type="radio" name="serviceOption" value="" data-name="Standard Inspection & Fix" data-price="<?= $activeCategory['base_price'] ?>" checked class="text-brand focus:ring-rose-500">
                                        <div>
                                            <span class="text-xs font-bold text-slate-900 block">General Diagnostic & Minor Fix</span>
                                            <span class="text-[11px] text-slate-500">Doorstep inspection by technician</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-extrabold text-slate-900"><?= formatNpr($activeCategory['base_price']) ?></span>
                                </label>

                                <?php foreach ($subServices as $svc): ?>
                                    <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-slate-300 bg-slate-50/50 hover:bg-slate-100/60 cursor-pointer transition-colors">
                                        <div class="flex items-center gap-2.5">
                                            <input type="radio" name="serviceOption" value="<?= $svc['id'] ?>" data-name="<?= htmlspecialchars($svc['name']) ?>" data-price="<?= $svc['price'] ?>" class="text-brand focus:ring-rose-500">
                                            <div>
                                                <span class="text-xs font-bold text-slate-900 block"><?= htmlspecialchars($svc['name']) ?></span>
                                                <span class="text-[11px] text-slate-500 font-nepali"><?= htmlspecialchars($svc['nepali_name']) ?> (<?= htmlspecialchars($svc['unit']) ?>)</span>
                                            </div>
                                        </div>
                                        <span class="text-xs font-extrabold text-slate-900"><?= formatNpr($svc['price']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Urgency Choice (Ride-share dispatch speed feel) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Service Dispatch Urgency</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="p-3.5 rounded-2xl border-2 border-slate-200 hover:border-brand cursor-pointer transition-colors flex items-start gap-3">
                                    <input type="radio" name="urgency" value="standard" checked class="mt-0.5 text-brand focus:ring-rose-500">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 block">Standard Booking</span>
                                        <span class="text-[11px] text-slate-500">Scheduled time slot (+Rs. 0)</span>
                                    </div>
                                </label>
                                <label class="p-3.5 rounded-2xl border-2 border-amber-300 bg-amber-50/40 hover:border-amber-500 cursor-pointer transition-colors flex items-start gap-3">
                                    <input type="radio" name="urgency" value="urgent_asap" class="mt-0.5 text-brand focus:ring-rose-500">
                                    <div>
                                        <span class="text-xs font-bold text-amber-900 block">⚡ Urgent ASAP (30-45 mins)</span>
                                        <span class="text-[11px] text-amber-700">Immediate priority dispatch (+Rs. 200)</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Problem Description -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Describe Problem / Symptoms</label>
                            <textarea id="problemDescription" rows="3" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-3 bg-slate-50" placeholder="e.g. Kitchen tap pipe is leaking heavily under the counter. Need plumber to replace Teflon tape and washer."></textarea>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="button" onclick="nextBookingStep()" class="inline-flex items-center gap-2 px-6 py-3 text-xs font-extrabold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-md shadow-rose-600/20 active:scale-95 transition-all">
                                <span>Continue to Schedule & Address</span>
                                <?= icon('arrow-right', 'w-4 h-4 text-white') ?>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: DATE, TIME & NEPALI LOCATION -->
                    <div id="bookingStep2" class="hidden space-y-6">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900 mb-1">Step 2: Schedule & Address in Nepal</h2>
                            <p class="text-xs text-slate-500">Specify your preferred time and municipal ward for dispatch.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Service Date</label>
                                <input type="date" id="scheduledDate" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Preferred Time Window</label>
                                <select id="scheduledSlot" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50">
                                    <option value="Morning (8:00 AM - 11:00 AM)">Morning (8:00 AM - 11:00 AM)</option>
                                    <option value="Afternoon (12:00 PM - 3:00 PM)" selected>Afternoon (12:00 PM - 3:00 PM)</option>
                                    <option value="Evening (4:00 PM - 7:00 PM)">Evening (4:00 PM - 7:00 PM)</option>
                                    <option value="Emergency ASAP (Within 45 mins)">Emergency ASAP (Within 45 mins)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Nepali Location Hierarchy -->
                        <div class="pt-2 border-t border-slate-100 space-y-4">
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700">Home Address (Municipality & Ward)</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">District</label>
                                    <select id="bookingDistrict" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50">
                                        <option value="Kathmandu" <?= ($currentUser['district'] ?? 'Kathmandu') === 'Kathmandu' ? 'selected' : '' ?>>Kathmandu</option>
                                        <option value="Lalitpur" <?= ($currentUser['district'] ?? '') === 'Lalitpur' ? 'selected' : '' ?>>Lalitpur</option>
                                        <option value="Bhaktapur" <?= ($currentUser['district'] ?? '') === 'Bhaktapur' ? 'selected' : '' ?>>Bhaktapur</option>
                                        <option value="Chitwan">Chitwan</option>
                                        <option value="Kaski">Pokhara (Kaski)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Ward Number</label>
                                    <select id="bookingWard" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50">
                                        <?php for ($w = 1; $w <= 32; $w++): ?>
                                            <option value="<?= $w ?>" <?= ($currentUser['ward_no'] ?? 4) == $w ? 'selected' : '' ?>>
                                                Ward <?= $w ?> <?= $w === 4 ? '(Baluwatar)' : ($w === 1 ? '(Naxal)' : ($w === 3 ? '(Maharajgunj)' : '')) ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Municipality / Local Body</label>
                                <input type="text" id="bookingMunicipality" value="<?= htmlspecialchars($currentUser['municipality'] ?? 'Kathmandu Metropolitan City') ?>" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">House / Tole / Street Address</label>
                                <input type="text" id="streetAddress" value="<?= htmlspecialchars($currentUser['address_street'] ?? 'Baluwatar House 24, Marg 3') ?>" placeholder="e.g. House No. 45, Bishalnagar Marg" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Prominent Landmark (Optional)</label>
                                <input type="text" id="landmark" placeholder="e.g. Opposite Russian Embassy, near White House" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50">
                            </div>
                        </div>

                        <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                            <button type="button" onclick="prevBookingStep()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                                Back
                            </button>
                            <button type="button" onclick="nextBookingStep()" class="inline-flex items-center gap-2 px-6 py-3 text-xs font-extrabold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-md shadow-rose-600/20 active:scale-95 transition-all">
                                <span>Review & Payment Method</span>
                                <?= icon('arrow-right', 'w-4 h-4 text-white') ?>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: REVIEW SUMMARY & PAYMENT METHOD -->
                    <div id="bookingStep3" class="hidden space-y-6">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900 mb-1">Step 3: Review Booking & Payment Option</h2>
                            <p class="text-xs text-slate-500">Pay conveniently after work inspection via Cash, eSewa, or Khalti.</p>
                        </div>

                        <!-- Summary Card -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 text-xs">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200/70">
                                <span class="text-slate-500">Service:</span>
                                <span id="revServiceName" class="font-extrabold text-slate-900">Plumbing Diagnostic</span>
                            </div>
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200/70">
                                <span class="text-slate-500">Schedule:</span>
                                <span id="revDateTime" class="font-bold text-slate-900">Today • Afternoon</span>
                            </div>
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200/70">
                                <span class="text-slate-500">Ward Location:</span>
                                <span id="revLocation" class="font-bold text-slate-900">Baluwatar, Ward 4</span>
                            </div>
                            <div class="pt-1">
                                <span class="text-slate-500 block mb-1">Problem Note:</span>
                                <p id="revDescription" class="text-slate-700 italic bg-white p-2.5 rounded-lg border border-slate-200"></p>
                            </div>
                        </div>

                        <!-- Payment Method Selector -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Select Preferred Payment Mode</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label class="p-3.5 rounded-2xl border-2 border-slate-200 hover:border-brand cursor-pointer transition-colors flex items-center gap-2.5">
                                    <input type="radio" name="paymentMethod" value="cash" checked class="text-brand focus:ring-rose-500">
                                    <div>
                                        <span class="text-xs font-extrabold text-slate-900 block">Cash on Work</span>
                                        <span class="text-[10px] text-slate-500">Direct to mistiri</span>
                                    </div>
                                </label>
                                <label class="p-3.5 rounded-2xl border-2 border-emerald-200 bg-emerald-50/40 hover:border-emerald-500 cursor-pointer transition-colors flex items-center gap-2.5">
                                    <input type="radio" name="paymentMethod" value="esewa" class="text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <span class="text-xs font-extrabold text-emerald-900 block">eSewa QR</span>
                                        <span class="text-[10px] text-emerald-700">Digital wallet</span>
                                    </div>
                                </label>
                                <label class="p-3.5 rounded-2xl border-2 border-purple-200 bg-purple-50/40 hover:border-purple-500 cursor-pointer transition-colors flex items-center gap-2.5">
                                    <input type="radio" name="paymentMethod" value="khalti" class="text-purple-600 focus:ring-purple-500">
                                    <div>
                                        <span class="text-xs font-extrabold text-purple-900 block">Khalti Pay</span>
                                        <span class="text-[10px] text-purple-700">Scan & Pay</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                            <button type="button" onclick="prevBookingStep()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                                Back
                            </button>
                            <button type="button" id="btnSubmitBooking" onclick="submitFinalBooking()" class="inline-flex items-center gap-2 px-8 py-3.5 text-xs font-extrabold text-white bg-brand hover:bg-brand-dark rounded-2xl shadow-lg shadow-rose-600/25 active:scale-95 transition-all">
                                <span>Confirm & Dispatch Booking</span>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 4: SUCCESS CONFIRMATION & LIVE TRACKER -->
                    <div id="bookingStep4" class="hidden text-center py-8 space-y-6">
                        <div class="w-16 h-16 rounded-3xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-md check-pop">
                            <?= icon('check-circle', 'w-10 h-10 text-emerald-600') ?>
                        </div>

                        <div class="space-y-1">
                            <span class="text-[11px] font-extrabold uppercase tracking-widest text-emerald-600 block">Service Request Dispatched!</span>
                            <h2 class="text-2xl font-extrabold text-slate-900">Technician Assigned to Your Ward</h2>
                            <p class="text-xs text-slate-500 max-w-md mx-auto">
                                We have sent your request to verified technicians. You will receive an arrival update shortly.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 max-w-sm mx-auto text-xs space-y-1.5">
                            <span class="text-slate-400 block font-semibold">Booking Reference Code</span>
                            <span id="confirmedBookingCode" class="text-lg font-black text-brand tracking-wider">SEWA-2026-8899</span>
                            <span class="text-[11px] text-emerald-700 font-bold block pt-1">Status: Pending Worker Acceptance</span>
                        </div>

                        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a href="/customer-dashboard.php" class="w-full sm:w-auto px-6 py-3 text-xs font-extrabold text-white bg-slate-900 hover:bg-slate-800 rounded-xl shadow-md">
                                Go to My Bookings
                            </a>
                            <a href="/search.php" class="w-full sm:w-auto px-6 py-3 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl">
                                Book Another Services
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Price Breakdown Sticky Sidebar (4 Cols) -->
            <div class="lg:col-span-4 sticky top-24">
                <div class="card-soft p-6 bg-white rounded-3xl border border-slate-200/90 shadow-lg space-y-5">
                    
                    <div class="pb-3 border-b border-slate-100">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                            <?= icon('wallet', 'w-4 h-4 text-brand') ?>
                            <span>Price Breakdown (NPR)</span>
                        </h3>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Service Diagnostic Base:</span>
                            <span id="summaryBasePrice" class="font-bold text-slate-900"><?= formatNpr($activeCategory['base_price']) ?></span>
                        </div>
                        <div id="summaryUrgencyRow" class="hidden flex items-center justify-between text-amber-700 bg-amber-50 p-2 rounded-lg">
                            <span>⚡ Urgent Priority Dispatch:</span>
                            <span id="summaryUrgencyFee" class="font-bold">रू 200.00</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>7-Day Warranty Assurance:</span>
                            <span class="font-bold text-emerald-600">FREE</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-baseline justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Estimated Total</span>
                            <span class="text-xs text-slate-500">Pay after completion</span>
                        </div>
                        <span id="summaryTotalPrice" class="text-2xl font-black text-brand"><?= formatNpr($activeCategory['base_price']) ?></span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-slate-700">
                            <?= icon('shield-check', 'w-3.5 h-3.5 text-brand') ?>
                            <span>SewaSathi Price Assurance</span>
                        </div>
                        <p>No unexpected hidden fees. Technician quotes any additional spare parts before installation.</p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</main>

<script src="/assets/js/booking.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    initBooking({
        categoryId: <?= (int)$activeCategory['id'] ?>,
        categoryName: <?= json_encode($activeCategory['name']) ?>,
        providerId: <?= $selectedProvId ? (int)$selectedProvId : 'null' ?>,
        providerName: <?= json_encode($selectedProvider['full_name'] ?? 'Auto-Assign Nearest Worker') ?>,
        servicePrice: <?= (float)$activeCategory['base_price'] ?>,
        totalAmount: <?= (float)$activeCategory['base_price'] ?>
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
