<?php
$pageTitle = 'Find Verified Home Workers';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';

$db = Database::getConnection();

// Fetch all active categories for filter
$categories = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

// Get filter query params
$selectedCategorySlug = $_GET['category'] ?? '';
$selectedDistrict = $_GET['district'] ?? ($currentUser['district'] ?? 'Kathmandu');
$selectedWard = isset($_GET['ward']) && $_GET['ward'] !== '' ? (int)$_GET['ward'] : 0;
$availabilityFilter = isset($_GET['available_only']) ? 1 : 0;
$minRating = (float)($_GET['min_rating'] ?? 0);
$sortBy = $_GET['sort'] ?? 'rating_desc';

// Build SQL Query
$sql = "
    SELECT pp.*, u.full_name, u.avatar_url, u.district, u.municipality, u.ward_no, u.phone,
           c.name as category_name, c.nepali_name as category_nepali_name, c.slug as category_slug, c.icon_name
    FROM provider_profiles pp
    JOIN users u ON pp.user_id = u.id
    JOIN categories c ON pp.category_id = c.id
    WHERE pp.is_verified = 1 AND pp.verification_status = 'approved'
";

$params = [];

if (!empty($selectedCategorySlug)) {
    $sql .= " AND c.slug = :category_slug";
    $params['category_slug'] = $selectedCategorySlug;
}

if (!empty($selectedDistrict)) {
    $sql .= " AND u.district = :district";
    $params['district'] = $selectedDistrict;
}

if ($selectedWard > 0) {
    // Check if ward matches provider home ward or is in service_wards JSON
    $sql .= " AND (u.ward_no = :ward OR JSON_SEARCH(pp.service_wards, 'one', CONCAT('%', :ward_str, '%')) IS NOT NULL)";
    $params['ward'] = $selectedWard;
    $params['ward_str'] = (string)$selectedWard;
}

if ($availabilityFilter) {
    $sql .= " AND pp.is_available = 1";
}

if ($minRating > 0) {
    $sql .= " AND pp.rating_avg >= :min_rating";
    $params['min_rating'] = $minRating;
}

// Sorting
switch ($sortBy) {
    case 'price_asc':
        $sql .= " ORDER BY pp.hourly_rate ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY pp.hourly_rate DESC";
        break;
    case 'experience_desc':
        $sql .= " ORDER BY pp.experience_years DESC";
        break;
    case 'proximity':
        $sql .= " ORDER BY (u.ward_no = " . (int)$activeWard . ") DESC, pp.rating_avg DESC";
        break;
    case 'rating_desc':
    default:
        $sql .= " ORDER BY pp.rating_avg DESC, pp.jobs_completed DESC";
        break;
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$providers = $stmt->fetchAll();
?>

<main class="flex-grow py-8 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Search Header & Proximity Status Pill -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">
                    <a href="/index.php" class="hover:text-brand">Home</a>
                    <span>/</span>
                    <span class="text-slate-900">Find Workers</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Verified Home Service Technicians
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Showing <strong class="text-slate-800"><?= count($providers) ?> verified workers</strong> ready for dispatch in <strong class="text-brand"><?= htmlspecialchars($selectedDistrict) ?><?= $selectedWard ? ", Ward $selectedWard" : "" ?></strong>
                </p>
            </div>

            <!-- Proximity / Location Status Pill (Ride-Share style proximity feel) -->
            <div class="flex items-center gap-3 bg-white p-3 rounded-2xl border border-slate-200/80 shadow-sm shrink-0">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-brand flex items-center justify-center shrink-0">
                    <?= icon('map-pin', 'w-5 h-5 text-brand') ?>
                </div>
                <div class="text-xs">
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Proximity Dispatch Hub</span>
                    <span class="font-extrabold text-slate-800"><?= htmlspecialchars($selectedDistrict) ?>, Ward <?= htmlspecialchars((string)($selectedWard ?: $activeWard)) ?></span>
                    <span class="text-[11px] text-emerald-600 font-bold block">Avg. response time: 25-40 min</span>
                </div>
                <button type="button" onclick="openLocationModal()" class="px-2.5 py-1 text-xs font-bold text-brand hover:bg-rose-50 rounded-lg transition-colors border border-rose-200/60">
                    Change
                </button>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Filter Sidebar -->
            <aside class="lg:col-span-3">
                <div class="card-soft p-5 bg-white rounded-2xl border border-slate-200 sticky top-24 space-y-6">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                            <?= icon('filter', 'w-4 h-4 text-brand') ?>
                            <span>Filter Results</span>
                        </h3>
                        <a href="/search.php" class="text-xs font-bold text-slate-400 hover:text-brand">Reset</a>
                    </div>

                    <form action="/search.php" method="GET" class="space-y-5">
                        
                        <!-- 1. Category Filter -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Service Category</label>
                            <select name="category" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['slug']) ?>" <?= $selectedCategorySlug === $cat['slug'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 2. District & Ward -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">District Location</label>
                            <select name="district" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 mb-2">
                                <option value="Kathmandu" <?= $selectedDistrict === 'Kathmandu' ? 'selected' : '' ?>>Kathmandu</option>
                                <option value="Lalitpur" <?= $selectedDistrict === 'Lalitpur' ? 'selected' : '' ?>>Lalitpur</option>
                                <option value="Bhaktapur" <?= $selectedDistrict === 'Bhaktapur' ? 'selected' : '' ?>>Bhaktapur</option>
                                <option value="Kaski" <?= $selectedDistrict === 'Kaski' ? 'selected' : '' ?>>Pokhara (Kaski)</option>
                                <option value="Chitwan" <?= $selectedDistrict === 'Chitwan' ? 'selected' : '' ?>>Chitwan</option>
                            </select>

                            <label class="block text-xs font-bold text-slate-700 mb-2">Ward Number</label>
                            <select name="ward" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50">
                                <option value="">All Wards</option>
                                <?php for ($w = 1; $w <= 32; $w++): ?>
                                    <option value="<?= $w ?>" <?= $selectedWard === $w ? 'selected' : '' ?>>
                                        Ward <?= $w ?> <?= $w === 4 ? '(Baluwatar)' : ($w === 1 ? '(Naxal)' : ($w === 3 ? '(Maharajgunj)' : '')) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- 3. Minimum Star Rating -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Minimum Rating</label>
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                                    <input type="radio" name="min_rating" value="0" <?= $minRating == 0 ? 'checked' : '' ?> class="text-rose-600 focus:ring-rose-500">
                                    <span>Any Rating</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                                    <input type="radio" name="min_rating" value="4.5" <?= $minRating == 4.5 ? 'checked' : '' ?> class="text-rose-600 focus:ring-rose-500">
                                    <span>4.5 & Above ★</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                                    <input type="radio" name="min_rating" value="4.0" <?= $minRating == 4.0 ? 'checked' : '' ?> class="text-rose-600 focus:ring-rose-500">
                                    <span>4.0 & Above ★</span>
                                </label>
                            </div>
                        </div>

                        <!-- 4. Availability Toggle -->
                        <div class="pt-2 border-t border-slate-100">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="available_only" value="1" <?= $availabilityFilter ? 'checked' : '' ?> class="rounded text-rose-600 focus:ring-rose-500 h-4 w-4">
                                <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Available for Instant Dispatch</span>
                                </span>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 text-xs font-extrabold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-md shadow-rose-600/20 transition-all">
                            Apply Filters
                        </button>
                    </form>

                </div>
            </aside>

            <!-- Results Section -->
            <section class="lg:col-span-9 space-y-4">
                
                <!-- Sort Bar -->
                <div class="flex items-center justify-between bg-white p-3.5 rounded-2xl border border-slate-200 text-xs">
                    <span class="text-slate-500 font-medium">Found <strong class="text-slate-800"><?= count($providers) ?></strong> technicians</span>
                    
                    <div class="flex items-center gap-2">
                        <span class="text-slate-500 font-bold hidden sm:inline">Sort By:</span>
                        <select onchange="window.location.href = updateQueryParam('sort', this.value)" class="bg-slate-50 border border-slate-200 text-xs font-bold rounded-lg p-1.5 focus:border-rose-500 focus:ring-rose-500">
                            <option value="rating_desc" <?= $sortBy === 'rating_desc' ? 'selected' : '' ?>>Highest Rated ★</option>
                            <option value="proximity" <?= $sortBy === 'proximity' ? 'selected' : '' ?>>Nearest Proximity (Ward)</option>
                            <option value="experience_desc" <?= $sortBy === 'experience_desc' ? 'selected' : '' ?>>Most Experienced</option>
                            <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <!-- Provider Cards List/Grid -->
                <?php if (!empty($providers)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <?php foreach ($providers as $p): ?>
                            <div class="card-soft p-5 bg-white rounded-2xl border border-slate-200/80 flex flex-col justify-between hover:border-slate-300">
                                <div>
                                    
                                    <!-- Top Row: Avatar, Info, Verified Checkmark -->
                                    <div class="flex items-start gap-3.5">
                                        <img src="<?= htmlspecialchars($p['avatar_url']) ?>" alt="<?= htmlspecialchars($p['full_name']) ?>" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-100 shrink-0">
                                        <div class="flex-grow min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <h3 class="font-extrabold text-sm text-slate-900 truncate">
                                                    <a href="/provider.php?id=<?= $p['id'] ?>" class="hover:text-brand"><?= htmlspecialchars($p['full_name']) ?></a>
                                                </h3>
                                                <span title="CTEVT & Nepal Police Verified" class="text-brand shrink-0">
                                                    <?= icon('verified', 'w-4 h-4 text-brand') ?>
                                                </span>
                                            </div>
                                            <p class="text-xs font-semibold text-rose-600 flex items-center gap-1">
                                                <?= icon($p['icon_name'], 'w-3.5 h-3.5') ?>
                                                <span><?= htmlspecialchars($p['category_name']) ?> • <?= $p['experience_years'] ?> yrs</span>
                                            </p>
                                            
                                            <!-- Rating & Review Count -->
                                            <div class="flex items-center gap-2 mt-1">
                                                <?= renderStarRating((float)$p['rating_avg']) ?>
                                                <span class="text-xs font-bold text-slate-800"><?= number_format($p['rating_avg'], 1) ?></span>
                                                <span class="text-xs text-slate-400 font-medium">(<?= $p['review_count'] ?> reviews)</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bio Snippet -->
                                    <p class="text-xs text-slate-600 mt-3 line-clamp-2 leading-relaxed">
                                        <?= htmlspecialchars($p['bio']) ?>
                                    </p>

                                    <!-- Service Area & Proximity Badge -->
                                    <div class="mt-3.5 flex flex-wrap items-center gap-2 text-[11px] font-semibold text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                        <div class="flex items-center gap-1 text-slate-700 truncate">
                                            <?= icon('map-pin', 'w-3.5 h-3.5 text-brand shrink-0') ?>
                                            <span><?= htmlspecialchars($p['district']) ?>, Ward <?= htmlspecialchars((string)$p['ward_no']) ?></span>
                                        </div>
                                        <span class="text-slate-300">|</span>
                                        <?php if ($p['is_available']): ?>
                                            <span class="text-emerald-600 font-bold flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>Ready to dispatch</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-slate-400 font-bold">Currently Busy</span>
                                        <?php endif; ?>
                                    </div>

                                </div>

                                <!-- Bottom Price & Booking CTA -->
                                <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Base Rate</span>
                                        <span class="text-sm font-extrabold text-slate-900"><?= formatNpr($p['hourly_rate']) ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="/provider.php?id=<?= $p['id'] ?>" class="px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 rounded-xl transition-colors">
                                            Profile
                                        </a>
                                        <a href="/book.php?category=<?= $p['category_id'] ?>&provider_id=<?= $p['id'] ?>" class="px-4 py-2 text-xs font-extrabold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-md shadow-rose-600/20 active:scale-95 transition-all">
                                            Book Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Sleek Empty State with Suggestions -->
                    <div class="card-soft p-12 bg-white rounded-3xl text-center border border-slate-200/80 space-y-4">
                        <div class="w-16 h-16 rounded-2xl bg-rose-50 text-brand flex items-center justify-center mx-auto">
                            <?= icon('search', 'w-8 h-8 text-brand') ?>
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900">No Verified Workers Found for Selected Filters</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                            Try expanding your search by selecting "All Wards" or choosing a different service category. We have active technicians stationed across Kathmandu Valley.
                        </p>
                        <div class="pt-2">
                            <a href="/search.php" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-md">
                                <?= icon('refresh', 'w-4 h-4') ?>
                                <span>Reset All Filters</span>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </section>

        </div>
    </div>
</main>

<script>
function updateQueryParam(key, val) {
    const url = new URL(window.location.href);
    url.searchParams.set(key, val);
    return url.toString();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
