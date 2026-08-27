<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/../config/constants.php';

$currentUser = getCurrentUser();
$currentUri = $_SERVER['REQUEST_URI'];
$activeWard = $_SESSION['selected_ward'] ?? ($currentUser['ward_no'] ?? 4);
$activeMunicipality = $_SESSION['selected_municipality'] ?? ($currentUser['municipality'] ?? 'Kathmandu Metropolitan City');
$activeDistrict = $_SESSION['selected_district'] ?? ($currentUser['district'] ?? 'Kathmandu');
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' • ' . APP_NAME : APP_NAME . ' — ' . APP_TAGLINE ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Mukta:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Mukta', 'sans-serif'],
                        nepali: ['Mukta', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            200: '#fecdd3',
                            300: '#fda4af',
                            400: '#fb7185',
                            500: '#f43f5e',
                            600: '#e11d48',
                            700: '#be123c',
                            800: '#9f1239',
                            900: '#881337',
                            DEFAULT: '#C41E3A', // Authentic Nepali Crimson Red
                            dark: '#991B1B'
                        },
                        navy: {
                            800: '#1E293B',
                            900: '#0F172A',
                            950: '#020617'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="min-h-full flex flex-col text-slate-800 selection:bg-rose-500 selection:text-white pb-16 md:pb-0">

    <!-- Top Notice Banner for Emergency Helpline -->
    <header class="bg-slate-900 text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="font-medium text-slate-200">24/7 Verified Home Technicians Active in Kathmandu Valley, Pokhara & Chitwan</span>
            </div>
            <div class="hidden sm:flex items-center gap-6">
                <a href="tel:16600173927" class="hover:text-white flex items-center gap-1.5 transition-colors">
                    <?= icon('phone', 'w-3.5 h-3.5 text-rose-400') ?>
                    <span>Toll-Free: <strong class="text-white"><?= EMERGENCY_HOTLINE ?></strong></span>
                </a>
                <span class="text-slate-600">|</span>
                <span class="text-slate-300">CTEVT & Nepal Police Verified</span>
            </div>
        </div>
    </header>

    <!-- Main Navigation Bar -->
    <nav class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-sm transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-18 py-3">
                
                <!-- Logo & Nepali Tag -->
                <div class="flex items-center gap-6">
                    <a href="/index.php" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand to-rose-700 flex items-center justify-center text-white shadow-md shadow-rose-600/20 group-hover:scale-105 transition-transform duration-200">
                            <?= icon('wrench', 'w-5 h-5 text-white stroke-[2.2]') ?>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-extrabold text-xl tracking-tight text-slate-900">Sewa<span class="text-brand">Sathi</span></span>
                                <span class="px-1.5 py-0.5 text-[10px] font-bold tracking-wide uppercase bg-rose-100 text-brand rounded-md border border-rose-200/60 font-nepali">सेवासाथी</span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-medium hidden sm:block -mt-0.5">Verified Home Services Nepal</p>
                        </div>
                    </a>

                    <!-- Quick Location Pill Selector -->
                    <div class="hidden lg:flex items-center">
                        <button type="button" onclick="openLocationModal()" class="flex items-center gap-2 px-3 py-1.5 bg-slate-100/90 hover:bg-slate-200/80 border border-slate-200 rounded-full text-xs font-semibold text-slate-700 transition-colors shadow-inner">
                            <?= icon('map-pin', 'w-3.5 h-3.5 text-brand') ?>
                            <span id="navCurrentLocationText" class="truncate max-w-[200px]"><?= htmlspecialchars($activeDistrict) ?>, Ward <?= htmlspecialchars((string)$activeWard) ?></span>
                            <?= icon('chevron-down', 'w-3 h-3 text-slate-400') ?>
                        </button>
                    </div>
                </div>

                <!-- Center Nav Links -->
                <div class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="/search.php" class="px-3 py-2 rounded-lg text-sm font-semibold <?= strpos($currentUri, 'search.php') !== false ? 'text-brand bg-rose-50/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?> transition-colors">
                        Browse Services
                    </a>
                    <a href="/index.php#how-it-works" class="px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                        How It Works
                    </a>
                    <a href="/index.php#safety-trust" class="px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                        Trust & Safety
                    </a>
                    <?php if (!isProvider() && !isAdmin()): ?>
                    <a href="/register-provider.php" class="px-3 py-2 rounded-lg text-sm font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100/80 border border-emerald-200/60 transition-colors flex items-center gap-1.5">
                        <?= icon('badge-check', 'w-4 h-4 text-emerald-600') ?>
                        Join as Provider
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Right Actions & User Menu -->
                <div class="flex items-center gap-3">
                    <?php if (isLoggedIn()): ?>
                        
                        <!-- Role-based Action Badges -->
                        <?php if (isCustomer()): ?>
                            <a href="/customer-dashboard.php" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                                <?= icon('file-text', 'w-4 h-4 text-slate-500') ?>
                                <span>My Bookings</span>
                            </a>
                        <?php elseif (isProvider()): ?>
                            <a href="/provider-dashboard.php" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-lg text-white bg-slate-900 hover:bg-slate-800 shadow-sm transition-colors">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span>Provider Portal</span>
                            </a>
                        <?php elseif (isAdmin()): ?>
                            <a href="/admin-dashboard.php" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-lg text-white bg-brand hover:bg-brand-dark shadow-sm transition-colors">
                                <?= icon('shield-check', 'w-4 h-4 text-white') ?>
                                <span>Admin Panel</span>
                            </a>
                        <?php endif; ?>

                        <!-- User Profile Dropdown Button -->
                        <div class="relative" id="userMenuContainer">
                            <button type="button" onclick="toggleUserDropdown()" class="flex items-center gap-2 p-1.5 rounded-full hover:bg-slate-100 border border-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-rose-500/20">
                                <img class="h-8 w-8 rounded-full object-cover ring-2 ring-white" src="<?= htmlspecialchars($currentUser['avatar_url']) ?>" alt="<?= htmlspecialchars($currentUser['full_name']) ?>">
                                <span class="hidden xl:inline text-xs font-bold text-slate-800 pr-1 max-w-[120px] truncate"><?= htmlspecialchars(explode(' ', $currentUser['full_name'])[0]) ?></span>
                                <?= icon('chevron-down', 'w-3.5 h-3.5 text-slate-400 pr-1') ?>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-56 rounded-xl bg-white shadow-xl ring-1 ring-black/5 py-2 divide-y divide-slate-100 z-50 animate-in fade-in slide-in-from-top-2 duration-150">
                                <div class="px-4 py-2.5">
                                    <p class="text-xs text-slate-500">Signed in as</p>
                                    <p class="text-sm font-bold text-slate-900 truncate"><?= htmlspecialchars($currentUser['full_name']) ?></p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-slate-100 text-slate-600 border border-slate-200">
                                        <?= htmlspecialchars(strtoupper($currentUser['role'])) ?>
                                    </span>
                                </div>
                                <div class="py-1">
                                    <?php if (isCustomer()): ?>
                                        <a href="/customer-dashboard.php" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            <?= icon('dashboard', 'w-4 h-4 text-slate-400') ?> Customer Dashboard
                                        </a>
                                        <a href="/search.php" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            <?= icon('search', 'w-4 h-4 text-slate-400') ?> Find Workers
                                        </a>
                                    <?php elseif (isProvider()): ?>
                                        <a href="/provider-dashboard.php" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            <?= icon('dashboard', 'w-4 h-4 text-slate-400') ?> Provider Dashboard
                                        </a>
                                    <?php elseif (isAdmin()): ?>
                                        <a href="/admin-dashboard.php" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            <?= icon('dashboard', 'w-4 h-4 text-slate-400') ?> Admin Operations
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="py-1">
                                    <a href="/logout.php" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                        <?= icon('logout', 'w-4 h-4 text-rose-500') ?> Sign Out
                                    </a>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- Guest State Links -->
                        <a href="/login.php" class="px-3.5 py-2 text-xs font-bold text-slate-700 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors">
                            Log In
                        </a>
                        <a href="/search.php" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand hover:bg-brand-dark shadow-sm shadow-rose-600/25 active:scale-95 transition-all">
                            <?= icon('search', 'w-3.5 h-3.5 text-white') ?>
                            <span>Book Worker</span>
                        </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Button -->
                    <button type="button" onclick="toggleMobileDrawer()" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none">
                        <?= icon('menu', 'w-6 h-6') ?>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Slide-over Drawer -->
    <div id="mobileDrawer" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm transition-opacity md:hidden">
        <div class="fixed inset-y-0 right-0 max-w-xs w-full bg-white shadow-2xl p-6 flex flex-col justify-between overflow-y-auto">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-brand flex items-center justify-center text-white">
                            <?= icon('wrench', 'w-4 h-4') ?>
                        </div>
                        <span class="font-extrabold text-slate-900">SewaSathi</span>
                    </div>
                    <button type="button" onclick="toggleMobileDrawer()" class="p-2 text-slate-400 hover:text-slate-700">
                        <?= icon('x', 'w-5 h-5') ?>
                    </button>
                </div>

                <div class="mt-4 pb-4 border-b border-slate-100">
                    <button type="button" onclick="openLocationModal(); toggleMobileDrawer();" class="w-full flex items-center justify-between p-3 bg-slate-50 hover:bg-slate-100 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700">
                        <div class="flex items-center gap-2 truncate">
                            <?= icon('map-pin', 'w-4 h-4 text-brand shrink-0') ?>
                            <span class="truncate"><?= htmlspecialchars($activeDistrict) ?>, Ward <?= htmlspecialchars((string)$activeWard) ?></span>
                        </div>
                        <span class="text-xs text-brand font-bold shrink-0">Change</span>
                    </button>
                </div>

                <div class="mt-4 space-y-1">
                    <a href="/index.php" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-100">Home</a>
                    <a href="/search.php" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-100">Browse Workers</a>
                    <a href="/index.php#how-it-works" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-100">How It Works</a>
                    <a href="/register-provider.php" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-emerald-700 bg-emerald-50">Join as Service Provider</a>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100">
                <?php if (isLoggedIn()): ?>
                    <a href="/logout.php" class="w-full flex items-center justify-center gap-2 py-2.5 text-xs font-bold text-rose-600 bg-rose-50 rounded-xl">
                        <?= icon('logout', 'w-4 h-4') ?> Sign Out
                    </a>
                <?php else: ?>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="/login.php" class="flex items-center justify-center py-2.5 text-xs font-bold text-slate-700 bg-slate-100 rounded-xl">Log In</a>
                        <a href="/register.php" class="flex items-center justify-center py-2.5 text-xs font-bold text-white bg-brand rounded-xl">Register</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($flashSuccess = getFlash('success')): ?>
        <div class="bg-emerald-500 text-white text-xs font-medium px-4 py-3 shadow-md flex items-center justify-between">
            <div class="max-w-7xl mx-auto w-full flex items-center gap-2">
                <?= icon('check-circle', 'w-4 h-4 text-white') ?>
                <span><?= htmlspecialchars($flashSuccess) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($flashError = getFlash('error')): ?>
        <div class="bg-rose-600 text-white text-xs font-medium px-4 py-3 shadow-md flex items-center justify-between">
            <div class="max-w-7xl mx-auto w-full flex items-center gap-2">
                <?= icon('alert-circle', 'w-4 h-4 text-white') ?>
                <span><?= htmlspecialchars($flashError) ?></span>
            </div>
        </div>
    <?php endif; ?>
