<?php
$pageTitle = 'Provider Dashboard';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/auth.php';

requireRole('provider', '/login.php');

$currentUser = getCurrentUser();
$db = Database::getConnection();

// Fetch provider profile
$pStmt = $db->prepare("
    SELECT pp.*, c.name as category_name, c.nepali_name as category_nepali_name, c.icon_name
    FROM provider_profiles pp
    JOIN categories c ON pp.category_id = c.id
    WHERE pp.user_id = :uid
");
$pStmt->execute(['uid' => $currentUser['id']]);
$providerProfile = $pStmt->fetch();

$providerId = $providerProfile['id'];

// Fetch Incoming Requests (pending requests matching category or explicitly assigned to this provider)
$reqStmt = $db->prepare("
    SELECT b.*, u.full_name as customer_name, u.phone as customer_phone, u.avatar_url as customer_avatar
    FROM bookings b
    JOIN users u ON b.customer_id = u.id
    WHERE (b.provider_id = :pid OR (b.provider_id IS NULL AND b.category_id = :cid))
      AND b.status = 'pending'
    ORDER BY b.created_at DESC
");
$reqStmt->execute(['pid' => $providerId, 'cid' => $providerProfile['category_id']]);
$incomingRequests = $reqStmt->fetchAll();

// Fetch Active On-Going Jobs (accepted, in_progress)
$activeJobStmt = $db->prepare("
    SELECT b.*, u.full_name as customer_name, u.phone as customer_phone, u.avatar_url as customer_avatar
    FROM bookings b
    JOIN users u ON b.customer_id = u.id
    WHERE b.provider_id = :pid AND b.status IN ('accepted', 'in_progress')
    ORDER BY b.scheduled_date ASC
");
$activeJobStmt->execute(['pid' => $providerId]);
$activeJobs = $activeJobStmt->fetchAll();

// Fetch Completed Jobs
$completedStmt = $db->prepare("
    SELECT b.*, u.full_name as customer_name, r.rating, r.comment as review_comment
    FROM bookings b
    JOIN users u ON b.customer_id = u.id
    LEFT JOIN reviews r ON b.id = r.booking_id
    WHERE b.provider_id = :pid AND b.status = 'completed'
    ORDER BY b.updated_at DESC
    LIMIT 10
");
$completedStmt->execute(['pid' => $providerId]);
$completedJobs = $completedStmt->fetchAll();

// Calculate total earnings
$earnStmt = $db->prepare("SELECT SUM(final_amount) as total_earnings, COUNT(id) as total_done FROM bookings WHERE provider_id = :pid AND status = 'completed'");
$earnStmt->execute(['pid' => $providerId]);
$earningsData = $earnStmt->fetch();
$totalEarnings = (float)($earningsData['total_earnings'] ?? 0);

require_once __DIR__ . '/includes/header.php';
?>

<main class="flex-grow py-8 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Live Availability Top Banner -->
        <div class="card-soft p-5 sm:p-6 bg-slate-900 text-white rounded-3xl shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <img src="<?= htmlspecialchars($currentUser['avatar_url']) ?>" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-700">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-extrabold text-white"><?= htmlspecialchars($currentUser['full_name']) ?></h1>
                        <span class="text-brand"><?= icon('verified', 'w-4 h-4 text-brand') ?></span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">
                        <?= htmlspecialchars($providerProfile['category_name']) ?> • Ward <?= htmlspecialchars((string)$currentUser['ward_no']) ?>, <?= htmlspecialchars($currentUser['district']) ?>
                    </p>
                </div>
            </div>

            <!-- Toggle Switch (Available / Busy) -->
            <div class="flex items-center gap-3 bg-slate-800/90 px-4 py-2.5 rounded-2xl border border-slate-700 w-full sm:w-auto justify-between sm:justify-start">
                <div class="flex items-center gap-2">
                    <span id="statusPulseDot" class="w-2.5 h-2.5 rounded-full <?= $providerProfile['is_available'] ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' ?>"></span>
                    <span id="providerStatusText" class="text-xs font-bold <?= $providerProfile['is_available'] ? 'text-emerald-400' : 'text-slate-400' ?>">
                        <?= $providerProfile['is_available'] ? 'Available for Jobs' : 'Currently Busy / Off-Duty' ?>
                    </span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" onchange="toggleProviderAvailability(this)" <?= $providerProfile['is_available'] ? 'checked' : '' ?> class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                </label>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card-soft p-5 bg-white rounded-2xl border border-slate-200">
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Total Earnings (NPR)</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block"><?= formatNpr($totalEarnings) ?></span>
                <span class="text-[11px] text-emerald-600 font-semibold">Verified completed jobs</span>
            </div>
            <div class="card-soft p-5 bg-white rounded-2xl border border-slate-200">
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Completed Jobs</span>
                <span class="text-2xl font-black text-brand mt-1 block"><?= (int)$providerProfile['jobs_completed'] ?></span>
                <span class="text-[11px] text-slate-500 font-semibold">Across Kathmandu Valley</span>
            </div>
            <div class="card-soft p-5 bg-white rounded-2xl border border-slate-200">
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Average Customer Rating</span>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-2xl font-black text-amber-500"><?= number_format($providerProfile['rating_avg'], 1) ?> ★</span>
                    <span class="text-xs text-slate-400">(<?= $providerProfile['review_count'] ?> reviews)</span>
                </div>
            </div>
        </div>

        <!-- Main Workspace Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 8 Cols: Incoming Requests & Active Jobs -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- 1. INCOMING BOOKING REQUESTS -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-extrabold text-slate-900">Incoming Job Requests</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-brand">
                                <?= count($incomingRequests) ?> New
                            </span>
                        </div>
                        <span class="text-xs text-slate-400">Auto-refreshed</span>
                    </div>

                    <?php if (!empty($incomingRequests)): ?>
                        <div class="space-y-4">
                            <?php foreach ($incomingRequests as $req): ?>
                                <div id="request-card-<?= $req['id'] ?>" class="card-soft p-5 bg-white rounded-2xl border-2 border-rose-200 shadow-lg space-y-4 transition-all duration-300">
                                    
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                                        <div class="flex items-center gap-2.5">
                                            <img src="<?= htmlspecialchars($req['customer_avatar']) ?>" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-200">
                                            <div>
                                                <h3 class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($req['customer_name']) ?></h3>
                                                <span class="text-[11px] text-slate-500">Ref: #<?= htmlspecialchars($req['booking_code']) ?></span>
                                            </div>
                                        </div>
                                        <div class="text-left sm:text-right">
                                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Estimated Payout</span>
                                            <span class="text-base font-extrabold text-emerald-700"><?= formatNpr($req['estimated_amount']) ?></span>
                                        </div>
                                    </div>

                                    <!-- Location & Schedule -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs bg-slate-50 p-3 rounded-xl border border-slate-100">
                                        <div class="space-y-1">
                                            <span class="text-slate-400 font-bold uppercase text-[10px]">Location:</span>
                                            <p class="font-bold text-slate-800 flex items-center gap-1.5 truncate">
                                                <?= icon('map-pin', 'w-3.5 h-3.5 text-brand shrink-0') ?>
                                                <span class="truncate"><?= htmlspecialchars($req['street_address']) ?>, Ward <?= htmlspecialchars((string)$req['ward_no']) ?></span>
                                            </p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="text-slate-400 font-bold uppercase text-[10px]">Timing:</span>
                                            <p class="font-bold text-slate-800 flex items-center gap-1.5 truncate">
                                                <?= icon('clock', 'w-3.5 h-3.5 text-blue-500 shrink-0') ?>
                                                <span class="truncate"><?= htmlspecialchars($req['scheduled_date']) ?> • <?= htmlspecialchars($req['scheduled_slot']) ?></span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Problem Statement -->
                                    <div class="text-xs">
                                        <span class="text-slate-400 font-bold uppercase text-[10px] block mb-1">Issue to solve:</span>
                                        <p class="text-slate-700 bg-white p-2.5 rounded-lg border border-slate-200 italic">
                                            "<?= htmlspecialchars($req['problem_description']) ?>"
                                        </p>
                                    </div>

                                    <!-- Action Buttons (Green Accept, Red Decline) -->
                                    <div class="pt-2 grid grid-cols-2 gap-3">
                                        <button type="button" onclick="rejectBooking(<?= $req['id'] ?>)" class="py-2.5 px-4 text-xs font-bold text-slate-700 hover:text-rose-600 bg-slate-100 hover:bg-rose-50 rounded-xl transition-colors">
                                            Decline Job
                                        </button>
                                        <button type="button" onclick="acceptBooking(<?= $req['id'] ?>)" class="py-2.5 px-4 text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-600/20 active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                            <?= icon('check-circle', 'w-4 h-4 text-white') ?>
                                            <span>Accept & Head Out</span>
                                        </button>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="card-soft p-8 bg-white rounded-2xl text-center text-xs text-slate-500 border border-slate-200">
                            No pending incoming requests right now. Technicians marked "Available" receive notifications when households in their ward make a booking.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 2. ACTIVE ACCEPTED JOBS -->
                <div class="space-y-4 pt-4">
                    <h2 class="text-base font-extrabold text-slate-900">Active Jobs in Progress</h2>

                    <?php if (!empty($activeJobs)): ?>
                        <div class="space-y-4">
                            <?php foreach ($activeJobs as $job): 
                                $jStatusMeta = STATUS_CONFIG[$job['status']] ?? STATUS_CONFIG['accepted'];
                            ?>
                                <div class="card-soft p-5 bg-white rounded-2xl border border-slate-200 space-y-4">
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                        <div>
                                            <span class="text-xs font-mono font-bold text-slate-400">#<?= htmlspecialchars($job['booking_code']) ?></span>
                                            <h3 class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($job['customer_name']) ?></h3>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded text-[11px] font-extrabold border <?= $jStatusMeta['badge_class'] ?>">
                                            <?= $jStatusMeta['label'] ?>
                                        </span>
                                    </div>

                                    <div class="text-xs space-y-1 text-slate-600">
                                        <p class="flex items-center gap-1.5">
                                            <?= icon('map-pin', 'w-3.5 h-3.5 text-brand') ?>
                                            <span><?= htmlspecialchars($job['street_address']) ?>, Ward <?= htmlspecialchars((string)$job['ward_no']) ?> (<?= htmlspecialchars($job['landmark'] ?: 'No landmark') ?>)</span>
                                        </p>
                                        <p class="flex items-center gap-1.5">
                                            <?= icon('phone', 'w-3.5 h-3.5 text-slate-400') ?>
                                            <a href="tel:<?= htmlspecialchars($job['customer_phone']) ?>" class="text-brand font-bold underline">Call Customer: <?= htmlspecialchars($job['customer_phone']) ?></a>
                                        </p>
                                    </div>

                                    <div class="pt-2 flex items-center justify-between gap-3">
                                        <span class="text-xs font-bold text-slate-800">Agreed: <?= formatNpr($job['estimated_amount']) ?></span>

                                        <div class="flex items-center gap-2">
                                            <?php if ($job['status'] === 'accepted'): ?>
                                                <button type="button" onclick="updateJobStatus(<?= $job['id'] ?>, 'in_progress')" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm">
                                                    Mark In Progress (Started)
                                                </button>
                                            <?php elseif ($job['status'] === 'in_progress'): ?>
                                                <button type="button" onclick="updateJobStatus(<?= $job['id'] ?>, 'completed')" class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm">
                                                    Mark Work Completed
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="card-soft p-6 bg-white rounded-2xl text-center text-xs text-slate-400 border border-slate-200">
                            No ongoing jobs right now.
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Right 4 Cols: Profile & Rate Card Settings -->
            <div class="lg:col-span-4 sticky top-24">
                <div class="card-soft p-6 bg-white rounded-3xl border border-slate-200/90 shadow-md space-y-5">
                    
                    <div class="pb-3 border-b border-slate-100">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                            <?= icon('sliders', 'w-4 h-4 text-brand') ?>
                            <span>Profile & Rate Card Settings</span>
                        </h3>
                    </div>

                    <form onsubmit="saveProviderProfile(event)" class="space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tagline</label>
                            <input type="text" name="tagline" value="<?= htmlspecialchars($providerProfile['tagline'] ?? '') ?>" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Base Inspection Charge (NPR)</label>
                            <input type="number" name="hourly_rate" value="<?= (float)$providerProfile['hourly_rate'] ?>" step="50" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Years of Practical Experience</label>
                            <input type="number" name="experience_years" value="<?= (int)$providerProfile['experience_years'] ?>" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Bio / Skills Description</label>
                            <textarea name="bio" rows="3" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold"><?= htmlspecialchars($providerProfile['bio'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 text-xs font-extrabold text-white bg-slate-900 hover:bg-slate-800 rounded-xl shadow-md transition-all">
                            Save Profile Changes
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</main>

<script src="/assets/js/provider-dashboard.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
