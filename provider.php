<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';

$providerId = (int)($_GET['id'] ?? 1);
$db = Database::getConnection();

// Fetch provider profile & user details
$stmt = $db->prepare("
    SELECT pp.*, u.full_name, u.email, u.phone, u.avatar_url, u.province, u.district, u.municipality, u.ward_no, u.address_street,
           c.name as category_name, c.nepali_name as category_nepali_name, c.slug as category_slug, c.icon_name, c.base_price
    FROM provider_profiles pp
    JOIN users u ON pp.user_id = u.id
    JOIN categories c ON pp.category_id = c.id
    WHERE pp.id = :pid AND pp.is_verified = 1
");
$stmt->execute(['pid' => $providerId]);
$provider = $stmt->fetch();

if (!$provider) {
    header("Location: /search.php");
    exit;
}

$pageTitle = $provider['full_name'] . ' • Verified ' . $provider['category_name'];
require_once __DIR__ . '/includes/header.php';

// Fetch sub-services for this category
$srvStmt = $db->prepare("SELECT * FROM services WHERE category_id = :cid ORDER BY price ASC");
$srvStmt->execute(['cid' => $provider['category_id']]);
$services = $srvStmt->fetchAll();

// Fetch reviews for this provider
$revStmt = $db->prepare("
    SELECT r.*, u.full_name as customer_name, u.avatar_url as customer_avatar, u.municipality as customer_muni, u.ward_no as customer_ward
    FROM reviews r
    JOIN users u ON r.customer_id = u.id
    WHERE r.provider_id = :pid
    ORDER BY r.created_at DESC
");
$revStmt->execute(['pid' => $providerId]);
$reviews = $revStmt->fetchAll();

$serviceWards = json_decode($provider['service_wards'] ?? '[]', true) ?: [$provider['district'] . ' Ward ' . $provider['ward_no']];
?>

<main class="flex-grow py-8 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wider mb-6">
            <a href="/index.php" class="hover:text-brand">Home</a>
            <span>/</span>
            <a href="/search.php?category=<?= htmlspecialchars($provider['category_slug']) ?>" class="hover:text-brand"><?= htmlspecialchars($provider['category_name']) ?></a>
            <span>/</span>
            <span class="text-slate-900"><?= htmlspecialchars($provider['full_name']) ?></span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 8 Cols: Main Profile Details -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Profile Header Card -->
                <div class="card-soft p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/80 relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                        
                        <div class="relative shrink-0">
                            <img src="<?= htmlspecialchars($provider['avatar_url']) ?>" alt="<?= htmlspecialchars($provider['full_name']) ?>" class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl object-cover ring-4 ring-slate-100 shadow-md">
                            <div class="absolute -bottom-2 -right-2 bg-brand text-white p-1.5 rounded-xl shadow-md" title="CTEVT Certified & Police Verified">
                                <?= icon('verified', 'w-5 h-5 text-white') ?>
                            </div>
                        </div>

                        <div class="space-y-1.5 flex-grow min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900"><?= htmlspecialchars($provider['full_name']) ?></h1>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Verified Professional
                                </span>
                            </div>

                            <p class="text-xs sm:text-sm font-semibold text-rose-600 flex items-center gap-1.5">
                                <?= icon($provider['icon_name'], 'w-4 h-4') ?>
                                <span><?= htmlspecialchars($provider['category_name']) ?> (<?= htmlspecialchars($provider['category_nepali_name']) ?>)</span>
                            </p>

                            <!-- Ratings & Completed Jobs -->
                            <div class="flex flex-wrap items-center gap-4 pt-1 text-xs">
                                <div class="flex items-center gap-1.5">
                                    <?= renderStarRating((float)$provider['rating_avg'], 'w-4 h-4 text-amber-400') ?>
                                    <span class="font-extrabold text-slate-900"><?= number_format($provider['rating_avg'], 1) ?></span>
                                    <span class="text-slate-400">(<?= count($reviews) ?> reviews)</span>
                                </div>
                                <span class="text-slate-300">•</span>
                                <span class="text-slate-600 font-semibold"><?= $provider['experience_years'] ?>+ Years Experience</span>
                                <span class="text-slate-300">•</span>
                                <span class="text-emerald-700 font-bold"><?= $provider['jobs_completed'] ?>+ Jobs Completed</span>
                            </div>
                        </div>

                    </div>

                    <!-- Live Dispatch Status Banner -->
                    <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4 bg-slate-50/80 -mx-6 -mb-6 sm:-mx-8 sm:-mb-8 p-4 sm:px-8">
                        <div class="flex items-center gap-2">
                            <?php if ($provider['is_available']): ?>
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                </span>
                                <span class="text-xs font-extrabold text-emerald-700">Available for Rapid Dispatch in Kathmandu Valley (30-45 mins arrival)</span>
                            <?php else: ?>
                                <span class="w-3 h-3 rounded-full bg-slate-400"></span>
                                <span class="text-xs font-bold text-slate-500">Currently on a Job / Busy (Book in advance for scheduled slots)</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-xs text-slate-500 font-medium">
                            Base inspection charge: <strong class="text-slate-900"><?= formatNpr($provider['hourly_rate']) ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Bio & Professional Qualifications -->
                <div class="card-soft p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/80 space-y-6">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 mb-3">About & Professional Experience</h2>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            <?= nl2br(htmlspecialchars($provider['bio'])) ?>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Verification ID</span>
                            <span class="text-xs font-extrabold text-slate-800"><?= htmlspecialchars($provider['citizenship_no'] ?: 'Verified on File') ?></span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Trade Certifications</span>
                            <span class="text-xs font-extrabold text-slate-800"><?= htmlspecialchars($provider['document_type'] ?: 'National Trade Certificate') ?></span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Languages Spoken</span>
                            <span class="text-xs font-extrabold text-slate-800">Nepali, English, Newari, Hindi</span>
                        </div>
                    </div>

                    <!-- Covered Municipal Wards -->
                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-1.5">
                            <?= icon('map-pin', 'w-4 h-4 text-brand') ?>
                            <span>Service Coverage & Municipal Wards</span>
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($serviceWards as $ward): ?>
                                <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200">
                                    <?= htmlspecialchars($ward) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Rate Card Breakdown -->
                <div class="card-soft p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/80">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-extrabold text-slate-900">Standard Service Rate Card</h2>
                        <span class="text-xs text-slate-500">Fixed NPR Pricing</span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        <!-- Inspection base fee -->
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900">Inspection & Minor Diagnostic Visit</h4>
                                <p class="text-[11px] text-slate-500">Includes technician home arrival and on-site diagnosis</p>
                            </div>
                            <span class="text-xs sm:text-sm font-extrabold text-slate-900"><?= formatNpr($provider['hourly_rate']) ?></span>
                        </div>

                        <?php foreach ($services as $srv): ?>
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900"><?= htmlspecialchars($srv['name']) ?></h4>
                                    <p class="text-[11px] text-slate-500 font-nepali"><?= htmlspecialchars($srv['nepali_name']) ?> (<?= htmlspecialchars($srv['unit']) ?>)</p>
                                </div>
                                <span class="text-xs sm:text-sm font-extrabold text-slate-900"><?= formatNpr($srv['price']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Customer Reviews Section -->
                <div class="card-soft p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/80 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Verified Customer Reviews</h2>
                            <p class="text-xs text-slate-500">Direct feedback from households in Kathmandu & Lalitpur</p>
                        </div>
                        <div class="flex items-center gap-1.5 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-100">
                            <?= icon('star', 'w-4 h-4 text-amber-500') ?>
                            <span class="text-sm font-extrabold text-slate-900"><?= number_format($provider['rating_avg'], 1) ?></span>
                            <span class="text-xs text-slate-400">/ 5.0</span>
                        </div>
                    </div>

                    <?php if (!empty($reviews)): ?>
                        <div class="space-y-4 divide-y divide-slate-100">
                            <?php foreach ($reviews as $rev): ?>
                                <div class="pt-4 first:pt-0 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <img src="<?= htmlspecialchars($rev['customer_avatar'] ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&auto=format&fit=crop&q=80') ?>" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200">
                                            <div>
                                                <h4 class="text-xs font-bold text-slate-900"><?= htmlspecialchars($rev['customer_name']) ?></h4>
                                                <p class="text-[10px] text-slate-400"><?= htmlspecialchars($rev['customer_muni']) ?>, Ward <?= htmlspecialchars((string)$rev['customer_ward']) ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs">
                                            <?= renderStarRating((float)$rev['rating'], 'w-3.5 h-3.5 text-amber-400') ?>
                                            <span class="text-[11px] text-slate-400"><?= date('M j, Y', strtotime($rev['created_at'])) ?></span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed bg-slate-50/70 p-3 rounded-xl border border-slate-100">
                                        "<?= htmlspecialchars($rev['comment']) ?>"
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-6 text-xs text-slate-400">
                            No reviews yet. Be the first customer to book and rate <?= htmlspecialchars($provider['full_name']) ?>!
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Right 4 Cols: Quick Booking Card Sidebar -->
            <div class="lg:col-span-4 sticky top-24">
                <div class="card-soft p-6 bg-white rounded-3xl border border-slate-200/90 shadow-xl space-y-6">
                    
                    <div class="pb-4 border-b border-slate-100">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block">Hire Directly</span>
                        <div class="flex items-baseline justify-between mt-1">
                            <span class="text-2xl font-black text-slate-900"><?= formatNpr($provider['hourly_rate']) ?></span>
                            <span class="text-xs text-slate-500 font-semibold">Starting base rate</span>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs text-slate-600">
                        <div class="flex items-center gap-2.5">
                            <?= icon('check-circle', 'w-4 h-4 text-emerald-600 shrink-0') ?>
                            <span>CTEVT & Police background certified</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <?= icon('clock', 'w-4 h-4 text-blue-600 shrink-0') ?>
                            <span>Estimated arrival: <strong>30-45 minutes</strong></span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <?= icon('award', 'w-4 h-4 text-purple-600 shrink-0') ?>
                            <span>Includes <strong>7-Day Service Warranty</strong></span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <?= icon('wallet', 'w-4 h-4 text-rose-600 shrink-0') ?>
                            <span>Pay Cash / eSewa / Khalti after work</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="/book.php?category=<?= $provider['category_id'] ?>&provider_id=<?= $provider['id'] ?>" class="w-full flex items-center justify-center gap-2 py-3.5 px-4 text-sm font-extrabold text-white bg-brand hover:bg-brand-dark rounded-2xl shadow-lg shadow-rose-600/25 active:scale-[0.98] transition-all">
                            <?= icon('calendar', 'w-4 h-4 text-white') ?>
                            <span>Book <?= explode(' ', $provider['full_name'])[0] ?> Now</span>
                        </a>
                    </div>

                    <p class="text-[11px] text-center text-slate-400">
                        Zero cancellation fee before technician arrival.
                    </p>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- Sticky Mobile Book Bar for Phone Users -->
<div class="md:hidden fixed bottom-14 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200 p-3 z-30 shadow-lg">
    <div class="flex items-center justify-between gap-3">
        <div>
            <span class="text-[10px] text-slate-400 font-bold uppercase block">Starting Base</span>
            <span class="text-base font-extrabold text-slate-900"><?= formatNpr($provider['hourly_rate']) ?></span>
        </div>
        <a href="/book.php?category=<?= $provider['category_id'] ?>&provider_id=<?= $provider['id'] ?>" class="flex-grow flex items-center justify-center gap-2 py-3 px-4 text-xs font-extrabold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-md">
            <span>Book Worker Now</span>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
