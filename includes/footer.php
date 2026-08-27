<?php
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/../config/constants.php';
?>
    <!-- Global Footer -->
    <footer class="bg-slate-900 text-slate-400 mt-auto border-t border-slate-800 text-sm">
        
        <!-- Trust Banner Section -->
        <div class="border-b border-slate-800/80 bg-slate-950/40 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-brand/10 text-brand flex items-center justify-center shrink-0 border border-brand/20">
                            <?= icon('shield-check', 'w-5 h-5 text-brand') ?>
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-sm">Police & Skill Verified</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Every mistiri and technician undergoes identity and citizenship verification.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                            <?= icon('award', 'w-5 h-5 text-emerald-400') ?>
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-sm">7-Day Service Guarantee</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Free revisit if the repaired issue reoccurs within 7 business days.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/20">
                            <?= icon('clock', 'w-5 h-5 text-blue-400') ?>
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-sm">Rapid Ward Dispatch</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Technicians stationed across municipal wards arrive in 30-45 minutes.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center shrink-0 border border-purple-500/20">
                            <?= icon('wallet', 'w-5 h-5 text-purple-400') ?>
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-sm">Transparent Fixed Rates</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Standard rate cards in NPR. Pay via Cash, eSewa, or Khalti only after work is done.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Footer Links -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
                
                <!-- Brand Info -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-brand flex items-center justify-center text-white shadow-md">
                            <?= icon('wrench', 'w-5 h-5 text-white') ?>
                        </div>
                        <span class="font-extrabold text-xl tracking-tight text-white">Sewa<span class="text-brand">Sathi</span></span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                        SewaSathi (सेवासाथी) connects Nepali households with verified, reliable local mistiris, plumbers, electricians, and cleaners directly to your doorstep.
                    </p>
                    <div class="pt-2 text-xs text-slate-400 space-y-1.5">
                        <p class="flex items-center gap-2">
                            <?= icon('map-pin', 'w-4 h-4 text-brand') ?>
                            <span>Headquarters: Baluwatar Marg, Kathmandu, Nepal</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <?= icon('phone', 'w-4 h-4 text-brand') ?>
                            <span>Helpline: <strong class="text-white"><?= EMERGENCY_HOTLINE ?></strong> (Toll-free)</span>
                        </p>
                    </div>
                </div>

                <!-- Popular Services -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200 mb-4">Popular Services</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="/search.php?category=plumbing" class="hover:text-white transition-colors">Plumbing & Water Motors</a></li>
                        <li><a href="/search.php?category=electrical" class="hover:text-white transition-colors">Electrician & Wiring Fix</a></li>
                        <li><a href="/search.php?category=masonry" class="hover:text-white transition-colors">Tile & Masonry Mistiri</a></li>
                        <li><a href="/search.php?category=cleaning" class="hover:text-white transition-colors">Sofa & Full House Deep Clean</a></li>
                        <li><a href="/search.php?category=appliances" class="hover:text-white transition-colors">Washing Machine & Fridge</a></li>
                    </ul>
                </div>

                <!-- Service Hubs in Nepal -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200 mb-4">Service Locations</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="/search.php?district=Kathmandu" class="hover:text-white transition-colors">Kathmandu (Wards 1–32)</a></li>
                        <li><a href="/search.php?district=Lalitpur" class="hover:text-white transition-colors">Lalitpur (Pulchowk, Jhamsikhel)</a></li>
                        <li><a href="/search.php?district=Bhaktapur" class="hover:text-white transition-colors">Bhaktapur & Thimi Wards</a></li>
                        <li><a href="/search.php?district=Kaski" class="hover:text-white transition-colors">Pokhara Valley (Lakeside)</a></li>
                        <li><a href="/search.php?district=Chitwan" class="hover:text-white transition-colors">Bharatpur & Narayangarh</a></li>
                    </ul>
                </div>

                <!-- Quick Portals & For Providers -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200 mb-4">Partner With Us</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="/register-provider.php" class="text-emerald-400 font-bold hover:underline">Register as a Mistiri/Worker</a></li>
                        <li><a href="/login.php" class="hover:text-white transition-colors">Provider Dashboard Login</a></li>
                        <li><a href="/login.php?role=admin" class="hover:text-white transition-colors">Admin Verification Portal</a></li>
                        <li><a href="/index.php#safety-trust" class="hover:text-white transition-colors">Safety Standards & Guarantee</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Sub-footer -->
            <div class="mt-12 pt-6 border-t border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500">
                <p>&copy; <?= date('Y') ?> SewaSathi Nepal Technologies Pvt. Ltd. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <span class="text-slate-400">Payment Accepted via:</span>
                    <span class="px-2 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800 font-bold text-[10px]">eSewa</span>
                    <span class="px-2 py-0.5 rounded bg-purple-950 text-purple-400 border border-purple-800 font-bold text-[10px]">Khalti</span>
                    <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700 font-bold text-[10px]">Cash on Work</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation Bar (App-like Feel for Pathao/InDrive UX) -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-lg border-t border-slate-200 z-40 py-1.5 px-4 shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
        <div class="grid grid-cols-4 gap-1 text-center">
            <a href="/index.php" class="flex flex-col items-center py-1 text-xs <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-brand font-bold' : 'text-slate-500' ?>">
                <?= icon('wrench', 'w-5 h-5 mb-0.5') ?>
                <span>Home</span>
            </a>
            <a href="/search.php" class="flex flex-col items-center py-1 text-xs <?= basename($_SERVER['PHP_SELF']) == 'search.php' ? 'text-brand font-bold' : 'text-slate-500' ?>">
                <?= icon('search', 'w-5 h-5 mb-0.5') ?>
                <span>Find</span>
            </a>
            <?php if (isLoggedIn()): ?>
                <?php if (isCustomer()): ?>
                    <a href="/customer-dashboard.php" class="flex flex-col items-center py-1 text-xs <?= basename($_SERVER['PHP_SELF']) == 'customer-dashboard.php' ? 'text-brand font-bold' : 'text-slate-500' ?>">
                        <?= icon('file-text', 'w-5 h-5 mb-0.5') ?>
                        <span>Bookings</span>
                    </a>
                <?php elseif (isProvider()): ?>
                    <a href="/provider-dashboard.php" class="flex flex-col items-center py-1 text-xs <?= basename($_SERVER['PHP_SELF']) == 'provider-dashboard.php' ? 'text-brand font-bold' : 'text-slate-500' ?>">
                        <?= icon('dashboard', 'w-5 h-5 mb-0.5') ?>
                        <span>Jobs</span>
                    </a>
                <?php elseif (isAdmin()): ?>
                    <a href="/admin-dashboard.php" class="flex flex-col items-center py-1 text-xs <?= basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php' ? 'text-brand font-bold' : 'text-slate-500' ?>">
                        <?= icon('shield-check', 'w-5 h-5 mb-0.5') ?>
                        <span>Admin</span>
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <a href="/login.php" class="flex flex-col items-center py-1 text-xs <?= basename($_SERVER['PHP_SELF']) == 'login.php' ? 'text-brand font-bold' : 'text-slate-500' ?>">
                    <?= icon('file-text', 'w-5 h-5 mb-0.5') ?>
                    <span>Bookings</span>
                </a>
            <?php endif; ?>
            
            <?php if (isLoggedIn()): ?>
                <a href="<?= isCustomer() ? '/customer-dashboard.php#profile' : (isProvider() ? '/provider-dashboard.php#profile' : '/admin-dashboard.php') ?>" class="flex flex-col items-center py-1 text-xs text-slate-500">
                    <?= icon('user', 'w-5 h-5 mb-0.5') ?>
                    <span>Account</span>
                </a>
            <?php else: ?>
                <a href="/login.php" class="flex flex-col items-center py-1 text-xs text-slate-500">
                    <?= icon('user', 'w-5 h-5 mb-0.5') ?>
                    <span>Login</span>
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Interactive Location Selector Modal (Province -> District -> Municipality -> Ward) -->
    <div id="locationModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm transition-opacity flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-brand flex items-center justify-center">
                        <?= icon('map-pin', 'w-5 h-5 text-brand') ?>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Select Your Location in Nepal</h3>
                        <p class="text-xs text-slate-500">Shows nearby verified technicians in your ward</p>
                    </div>
                </div>
                <button type="button" onclick="closeLocationModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                    <?= icon('x', 'w-5 h-5') ?>
                </button>
            </div>

            <form id="globalLocationForm" onsubmit="saveGlobalLocation(event)" class="mt-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Province</label>
                        <select id="modalProvince" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50" onchange="onModalProvinceChange()">
                            <option value="Bagmati" selected>Bagmati Province</option>
                            <option value="Gandaki">Gandaki Province</option>
                            <option value="Koshi">Koshi Province</option>
                            <option value="Lumbini">Lumbini Province</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">District</label>
                        <select id="modalDistrict" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50" onchange="onModalDistrictChange()">
                            <option value="Kathmandu" selected>Kathmandu</option>
                            <option value="Lalitpur">Lalitpur</option>
                            <option value="Bhaktapur">Bhaktapur</option>
                            <option value="Chitwan">Chitwan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Municipality / Metropolitan</label>
                    <select id="modalMunicipality" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50" onchange="onModalMunicipalityChange()">
                        <option value="Kathmandu Metropolitan City" selected>Kathmandu Metropolitan City</option>
                        <option value="Kirtipur Municipality">Kirtipur Municipality</option>
                        <option value="Budhanilkantha Municipality">Budhanilkantha Municipality</option>
                        <option value="Tokha Municipality">Tokha Municipality</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Ward Number</label>
                    <select id="modalWard" class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50">
                        <?php for ($w = 1; $w <= 32; $w++): ?>
                            <option value="<?= $w ?>" <?= $w == $activeWard ? 'selected' : '' ?>>Ward <?= $w ?> (<?= $w == 4 ? 'Baluwatar / Bishalnagar' : ($w == 1 ? 'Naxal / Hattisar' : ($w == 3 ? 'Maharajgunj' : 'Ward area ' . $w)) ?>)</option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="closeLocationModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-md shadow-rose-600/20">Apply Location</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Global JavaScript -->
    <script src="/assets/js/app.js"></script>
</body>
</html>
