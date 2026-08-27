<?php
$pageTitle = 'Home Service Booking Nepal';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';

$db = Database::getConnection();

// Fetch active categories
$catStmt = $db->query("
    SELECT c.*, COUNT(pp.id) as provider_count 
    FROM categories c 
    LEFT JOIN provider_profiles pp ON c.id = pp.category_id AND pp.is_verified = 1
    WHERE c.is_active = 1 
    GROUP BY c.id 
    ORDER BY c.is_popular DESC, c.id ASC
");
$categories = $catStmt->fetchAll();

// Fetch featured top-rated verified providers
$provStmt = $db->query("
    SELECT pp.*, u.full_name, u.avatar_url, u.district, u.municipality, u.ward_no, c.name as category_name, c.slug as category_slug, c.icon_name
    FROM provider_profiles pp
    JOIN users u ON pp.user_id = u.id
    JOIN categories c ON pp.category_id = c.id
    WHERE pp.is_verified = 1 AND pp.verification_status = 'approved'
    ORDER BY pp.rating_avg DESC, pp.jobs_completed DESC
    LIMIT 6
");
$featuredProviders = $provStmt->fetchAll();
?>

<main class="flex-grow">
    
    <!-- Hero Section with Search & Location Selector -->
    <section class="relative bg-gradient-to-b from-rose-50/70 via-white to-slate-50 pt-10 pb-16 lg:pt-16 lg:pb-24 overflow-hidden hero-glow border-b border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4">
                
                <!-- Trust Pill Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-100/80 border border-rose-200 text-brand text-xs font-extrabold tracking-wide uppercase">
                    <?= icon('shield-check', 'w-4 h-4 text-brand') ?>
                    <span>Nepal's #1 On-Demand Home Mistiri & Technician Network</span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                    Trusted Local Experts <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand via-rose-600 to-slate-900">Right at Your Doorstep</span>
                </h1>

                <p class="text-sm sm:text-base text-slate-600 font-medium max-w-2xl mx-auto leading-relaxed">
                    Book verified plumbers, electricians, mistiris, cleaners, and painters in Kathmandu Valley & Pokhara within 30 minutes. Fixed rates, police-verified workers, and 7-day guarantee.
                </p>

                <!-- Search & Ward Selector Card (Ride-share style quick dispatch bar) -->
                <div class="mt-8 pt-4">
                    <form action="/search.php" method="GET" class="card-soft p-3 sm:p-4 bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-3xl shadow-xl shadow-slate-900/5 max-w-4xl mx-auto text-left border border-slate-200/90">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                            
                            <!-- 1. Service Needed -->
                            <div class="md:col-span-4 p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100/80 transition-colors border border-slate-200/60">
                                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                    <?= icon('wrench', 'w-3.5 h-3.5 text-brand') ?>
                                    <span>What service do you need?</span>
                                </label>
                                <select name="category" class="w-full bg-transparent text-xs sm:text-sm font-bold text-slate-800 focus:outline-none cursor-pointer">
                                    <option value="">All Services (Plumbing, Wiring, etc.)</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat['slug']) ?>">
                                            <?= htmlspecialchars($cat['name']) ?> (<?= htmlspecialchars($cat['nepali_name']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- 2. Location (District & Ward) -->
                            <div class="md:col-span-5 p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100/80 transition-colors border border-slate-200/60">
                                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                    <?= icon('map-pin', 'w-3.5 h-3.5 text-rose-500') ?>
                                    <span>Your Ward / Area (Nepal)</span>
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <select name="district" class="w-full bg-transparent text-xs sm:text-sm font-bold text-slate-800 focus:outline-none cursor-pointer">
                                        <option value="Kathmandu">Kathmandu</option>
                                        <option value="Lalitpur">Lalitpur</option>
                                        <option value="Bhaktapur">Bhaktapur</option>
                                        <option value="Kaski">Pokhara (Kaski)</option>
                                        <option value="Chitwan">Chitwan</option>
                                    </select>
                                    <select name="ward" class="w-full bg-transparent text-xs sm:text-sm font-bold text-slate-800 focus:outline-none cursor-pointer">
                                        <option value="">All Wards</option>
                                        <option value="4" selected>Ward 4 (Baluwatar)</option>
                                        <option value="1">Ward 1 (Naxal)</option>
                                        <option value="3">Ward 3 (Maharajgunj)</option>
                                        <option value="7">Ward 7 (Chabahil)</option>
                                        <option value="10">Ward 10 (Baneshwor)</option>
                                        <option value="31">Ward 31 (Kalanki)</option>
                                        <?php for ($i = 2; $i <= 32; $i++): if(!in_array($i, [1,3,4,7,10,31])): ?>
                                            <option value="<?= $i ?>">Ward <?= $i ?></option>
                                        <?php endif; endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- 3. Submit CTA -->
                            <div class="md:col-span-3">
                                <button type="submit" class="w-full h-full min-h-[52px] flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl sm:rounded-2xl text-sm font-extrabold text-white bg-brand hover:bg-brand-dark active:scale-[0.98] shadow-lg shadow-rose-600/25 transition-all">
                                    <?= icon('search', 'w-4 h-4 text-white') ?>
                                    <span>Find Worker</span>
                                </button>
                            </div>

                        </div>
                    </form>

                    <!-- Fast Quick Tags -->
                    <div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs text-slate-500">
                        <span class="font-bold text-slate-600">Quick Searches:</span>
                        <a href="/search.php?category=plumbing" class="px-2.5 py-1 rounded-full bg-white hover:bg-rose-50 text-slate-700 border border-slate-200 hover:border-rose-300 font-medium transition-colors">Emergency Plumber</a>
                        <a href="/search.php?category=electrical" class="px-2.5 py-1 rounded-full bg-white hover:bg-rose-50 text-slate-700 border border-slate-200 hover:border-rose-300 font-medium transition-colors">Electrician Short Circuit</a>
                        <a href="/search.php?category=masonry" class="px-2.5 py-1 rounded-full bg-white hover:bg-rose-50 text-slate-700 border border-slate-200 hover:border-rose-300 font-medium transition-colors">Tile Mistiri</a>
                        <a href="/search.php?category=cleaning" class="px-2.5 py-1 rounded-full bg-white hover:bg-rose-50 text-slate-700 border border-slate-200 hover:border-rose-300 font-medium transition-colors">Sofa Deep Clean</a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 3-Step "How It Works" Section -->
    <section id="how-it-works" class="py-16 bg-white border-b border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-xs font-extrabold uppercase tracking-widest text-brand">Simple & Fast</h2>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">How SewaSathi Works in 3 Steps</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-2">Just like booking a ride, but for hiring certified mistiris & technicians to your home.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                
                <!-- Step 1 -->
                <div class="card-soft p-6 bg-slate-50/60 rounded-2xl relative border border-slate-200/80">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-brand flex items-center justify-center font-black text-lg shadow-sm mb-5">
                        1
                    </div>
                    <h4 class="text-base font-bold text-slate-900">Choose Service & Ward</h4>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Select what you need fixed (plumbing, wiring, masonry, cleaning) and pinpoint your municipality ward in Kathmandu Valley or Pokhara.
                    </p>
                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center gap-2 text-[11px] font-bold text-slate-500">
                        <?= icon('map-pin', 'w-3.5 h-3.5 text-brand') ?>
                        <span>Ward-accurate dispatch</span>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="card-soft p-6 bg-slate-50/60 rounded-2xl relative border border-slate-200/80">
                    <div class="w-12 h-12 rounded-2xl bg-brand text-white flex items-center justify-center font-black text-lg shadow-md shadow-rose-600/25 mb-5">
                        2
                    </div>
                    <h4 class="text-base font-bold text-slate-900">Verified Worker Arrives</h4>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        A certified CTEVT & police-verified mistiri nearby accepts your request and arrives at your doorstep in 30-45 minutes with full gear.
                    </p>
                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center gap-2 text-[11px] font-bold text-slate-500">
                        <?= icon('clock', 'w-3.5 h-3.5 text-brand') ?>
                        <span>30-45 Min arrival time</span>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="card-soft p-6 bg-slate-50/60 rounded-2xl relative border border-slate-200/80">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-lg shadow-sm mb-5">
                        3
                    </div>
                    <h4 class="text-base font-bold text-slate-900">Pay Safely After Service</h4>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Inspect the completed job. Pay via Cash, eSewa, or Khalti with standard transparent rates, backed by our 7-day service warranty.
                    </p>
                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center gap-2 text-[11px] font-bold text-slate-500">
                        <?= icon('shield-check', 'w-3.5 h-3.5 text-emerald-600') ?>
                        <span>7-Day satisfaction warranty</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Featured Service Categories Grid -->
    <section class="py-16 bg-slate-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
                <div>
                    <h2 class="text-xs font-extrabold uppercase tracking-widest text-brand">Popular Categories</h2>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Verified Services for Your Home</h3>
                </div>
                <a href="/search.php" class="mt-4 md:mt-0 inline-flex items-center gap-1.5 text-xs font-bold text-brand hover:text-brand-dark">
                    <span>View All Services</span>
                    <?= icon('arrow-right', 'w-3.5 h-3.5') ?>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                <?php foreach ($categories as $cat): ?>
                    <a href="/search.php?category=<?= htmlspecialchars($cat['slug']) ?>" class="card-soft p-5 rounded-2xl bg-white flex flex-col justify-between group hover:-translate-y-1 transition-all duration-200 border border-slate-200/80">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-rose-50 group-hover:bg-brand text-brand group-hover:text-white flex items-center justify-center transition-colors duration-200 shadow-sm">
                                <?= icon($cat['icon_name'], 'w-6 h-6 stroke-[2]') ?>
                            </div>
                            <h4 class="text-base font-bold text-slate-900 mt-4 group-hover:text-brand transition-colors">
                                <?= htmlspecialchars($cat['name']) ?>
                            </h4>
                            <p class="text-[11px] font-semibold text-slate-400 font-nepali">
                                <?= htmlspecialchars($cat['nepali_name']) ?>
                            </p>
                            <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                                <?= htmlspecialchars($cat['description']) ?>
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-medium"><?= $cat['provider_count'] ?>+ Workers</span>
                            <span class="font-bold text-slate-900">From <?= formatNpr($cat['base_price']) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Top Verified Providers Section -->
    <section class="py-16 bg-white border-t border-b border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-extrabold border border-emerald-200/60 mb-2">
                        <?= icon('badge-check', 'w-3.5 h-3.5 text-emerald-600') ?>
                        <span>Verified Background & CTEVT Assured</span>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Top-Rated Nearby Workers</h3>
                </div>
                <a href="/search.php" class="mt-4 md:mt-0 inline-flex items-center gap-1.5 text-xs font-bold text-brand hover:text-brand-dark">
                    <span>Browse All Providers</span>
                    <?= icon('arrow-right', 'w-3.5 h-3.5') ?>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($featuredProviders as $provider): ?>
                    <div class="card-soft p-5 rounded-2xl bg-white border border-slate-200/80 flex flex-col justify-between">
                        <div>
                            <!-- Header: Avatar, Name, Verified Badge -->
                            <div class="flex items-start gap-3.5">
                                <img src="<?= htmlspecialchars($provider['avatar_url']) ?>" alt="<?= htmlspecialchars($provider['full_name']) ?>" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-100 shrink-0">
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <h4 class="font-extrabold text-sm text-slate-900 truncate"><?= htmlspecialchars($provider['full_name']) ?></h4>
                                        <span title="Police & CTEVT Verified" class="text-brand shrink-0">
                                            <?= icon('verified', 'w-4 h-4 text-brand') ?>
                                        </span>
                                    </div>
                                    <p class="text-xs font-semibold text-rose-600"><?= htmlspecialchars($provider['category_name']) ?> (<?= $provider['experience_years'] ?> yrs exp)</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <?= renderStarRating((float)$provider['rating_avg']) ?>
                                        <span class="text-xs font-bold text-slate-700"><?= number_format($provider['rating_avg'], 1) ?></span>
                                        <span class="text-xs text-slate-400">(<?= $provider['review_count'] ?>)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tagline / Bio -->
                            <p class="text-xs text-slate-600 mt-3.5 line-clamp-2 leading-relaxed">
                                <?= htmlspecialchars($provider['bio']) ?>
                            </p>

                            <!-- Location & Status -->
                            <div class="mt-3.5 flex items-center justify-between text-xs text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <div class="flex items-center gap-1.5 truncate">
                                    <?= icon('map-pin', 'w-3.5 h-3.5 text-brand shrink-0') ?>
                                    <span class="truncate"><?= htmlspecialchars($provider['district']) ?>, Ward <?= htmlspecialchars((string)$provider['ward_no']) ?></span>
                                </div>
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-600 text-[11px] shrink-0">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Available</span>
                                </span>
                            </div>
                        </div>

                        <!-- Price & Action -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Base Rate</span>
                                <span class="text-sm font-extrabold text-slate-900"><?= formatNpr($provider['hourly_rate']) ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="/provider.php?id=<?= $provider['id'] ?>" class="px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 rounded-xl transition-colors">
                                    Profile
                                </a>
                                <a href="/book.php?category=<?= $provider['category_id'] ?>&provider_id=<?= $provider['id'] ?>" class="px-3.5 py-2 text-xs font-bold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-md shadow-rose-600/20 active:scale-95 transition-all">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Trust & Safety Section -->
    <section id="safety-trust" class="py-16 bg-slate-900 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-6 space-y-5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-brand/20 border border-brand/40 text-rose-300 text-xs font-bold uppercase">
                        <?= icon('shield-check', 'w-4 h-4 text-brand') ?>
                        <span>Safety Standards</span>
                    </div>
                    <h3 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                        Safe, Transparent & Reliable for Every Nepali Home
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        We know inviting a worker into your home requires trust. That's why every SewaSathi mistiri is vetted thoroughly with citizenship record checks, technical trade verification, and customer ratings.
                    </p>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                <?= icon('check-circle', 'w-4 h-4') ?>
                            </div>
                            <p class="text-xs text-slate-300"><strong class="text-white">Police & Identity Checked:</strong> National ID / Citizenship card verified before onboarding.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                <?= icon('check-circle', 'w-4 h-4') ?>
                            </div>
                            <p class="text-xs text-slate-300"><strong class="text-white">Transparent Pricing:</strong> No arbitrary overcharging. Standard municipal rate card in NPR.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                <?= icon('check-circle', 'w-4 h-4') ?>
                            </div>
                            <p class="text-xs text-slate-300"><strong class="text-white">Emergency Response Support:</strong> Direct hotline helpline (1660-01-73927) for rapid grievance resolution.</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <div class="p-8 rounded-3xl bg-slate-800/80 border border-slate-700 shadow-2xl space-y-6">
                        <h4 class="text-base font-bold text-white flex items-center gap-2">
                            <?= icon('award', 'w-5 h-5 text-rose-400') ?>
                            <span>The SewaSathi 7-Day Service Guarantee</span>
                        </h4>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            If any issue repaired by our certified worker reoccurs within 7 days, we dispatch a technician back to your home for a free inspection and repair warranty.
                        </p>
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-700">
                                <span class="text-2xl font-black text-rose-400">10,000+</span>
                                <span class="block text-xs text-slate-400 mt-1">Jobs Completed</span>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-700">
                                <span class="text-2xl font-black text-emerald-400">4.9 ★</span>
                                <span class="block text-xs text-slate-400 mt-1">Average Satisfaction</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Provider Callout Banner -->
    <section class="py-14 bg-rose-50 border-b border-rose-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Are you a Skilled Mistiri, Plumber, or Electrician?</h3>
            <p class="text-xs sm:text-sm text-slate-600 max-w-xl mx-auto">
                Join 500+ verified workers earning reliable daily income across Kathmandu Valley, Pokhara, and Chitwan. Get job requests directly on your phone.
            </p>
            <div class="pt-2">
                <a href="/register-provider.php" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-xs sm:text-sm font-extrabold text-white bg-slate-900 hover:bg-slate-800 shadow-lg shadow-slate-900/20 active:scale-95 transition-all">
                    <?= icon('badge-check', 'w-4 h-4 text-emerald-400') ?>
                    <span>Register as SewaSathi Service Partner</span>
                </a>
            </div>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
