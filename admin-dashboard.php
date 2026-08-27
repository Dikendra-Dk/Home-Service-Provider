<?php
$pageTitle = 'Admin Operations Portal';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/auth.php';

requireRole('admin', '/login.php');

$db = Database::getConnection();

// Fetch Pending Provider Verifications
$pendingStmt = $db->query("
    SELECT pp.*, u.full_name, u.email, u.phone, u.avatar_url, u.district, u.municipality, u.ward_no,
           c.name as category_name
    FROM provider_profiles pp
    JOIN users u ON pp.user_id = u.id
    JOIN categories c ON pp.category_id = c.id
    WHERE pp.verification_status = 'pending'
    ORDER BY pp.created_at ASC
");
$pendingProviders = $pendingStmt->fetchAll();

// Fetch All Users
$usersStmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
$allUsers = $usersStmt->fetchAll();

// Fetch All Bookings
$bookingsStmt = $db->query("
    SELECT b.*, c.name as category_name, cu.full_name as customer_name, pu.full_name as provider_name
    FROM bookings b
    JOIN categories c ON b.category_id = c.id
    JOIN users cu ON b.customer_id = cu.id
    LEFT JOIN provider_profiles pp ON b.provider_id = pp.id
    LEFT JOIN users pu ON pp.user_id = pu.id
    ORDER BY b.created_at DESC
");
$allBookings = $bookingsStmt->fetchAll();

// Fetch Categories
$categories = $db->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();

// Stats
$totalCustomers = count(array_filter($allUsers, fn($u) => $u['role'] === 'customer'));
$totalProviders = count(array_filter($allUsers, fn($u) => $u['role'] === 'provider'));
$totalRevenue = array_reduce($allBookings, fn($acc, $b) => $acc + (float)$b['estimated_amount'], 0);

require_once __DIR__ . '/includes/header.php';
?>

<main class="flex-grow py-8 bg-slate-100/70">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Admin Title Banner -->
        <div class="bg-slate-900 text-white p-6 rounded-3xl shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white">SewaSathi Administration Control Center</h1>
                    <span class="px-2 py-0.5 rounded bg-brand text-white font-mono text-[10px] uppercase font-bold">SUPERADMIN</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Manage verified technicians, approve citizenship credentials, and monitor platform activity.</p>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="px-3 py-1.5 rounded-xl bg-slate-800 border border-slate-700 text-slate-300 font-semibold">
                    <?= count($pendingProviders) ?> Pending Verifications
                </span>
            </div>
        </div>

        <!-- Metrics Overview -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="card-soft p-4 sm:p-5 bg-white rounded-2xl border border-slate-200">
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Pending Verifications</span>
                <span class="text-2xl font-black <?= count($pendingProviders) > 0 ? 'text-brand' : 'text-slate-900' ?> mt-1 block"><?= count($pendingProviders) ?></span>
                <span class="text-[10px] text-slate-500">Requires review</span>
            </div>
            <div class="card-soft p-4 sm:p-5 bg-white rounded-2xl border border-slate-200">
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Total Providers</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block"><?= $totalProviders ?></span>
                <span class="text-[10px] text-emerald-600 font-bold">Registered workers</span>
            </div>
            <div class="card-soft p-4 sm:p-5 bg-white rounded-2xl border border-slate-200">
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Total Customers</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block"><?= $totalCustomers ?></span>
                <span class="text-[10px] text-slate-500">Nepali households</span>
            </div>
            <div class="card-soft p-4 sm:p-5 bg-white rounded-2xl border border-slate-200">
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Gross Booking Volume</span>
                <span class="text-2xl font-black text-emerald-700 mt-1 block"><?= formatNpr($totalRevenue) ?></span>
                <span class="text-[10px] text-slate-500"><?= count($allBookings) ?> bookings</span>
            </div>
        </div>

        <!-- Tab Controls -->
        <div class="bg-white p-2 rounded-2xl border border-slate-200 shadow-sm">
            <nav class="flex flex-wrap gap-2 text-xs font-bold" aria-label="Admin Navigation">
                <button type="button" onclick="switchAdminTab('pending')" id="adminTabPending" class="admin-tab-btn px-4 py-2.5 rounded-xl bg-brand text-white flex items-center gap-2">
                    <?= icon('shield-check', 'w-4 h-4') ?>
                    <span>Pending Verifications (<?= count($pendingProviders) ?>)</span>
                </button>
                <button type="button" onclick="switchAdminTab('bookings')" id="adminTabBookings" class="admin-tab-btn px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 flex items-center gap-2">
                    <?= icon('file-text', 'w-4 h-4') ?>
                    <span>All Bookings (<?= count($allBookings) ?>)</span>
                </button>
                <button type="button" onclick="switchAdminTab('users')" id="adminTabUsers" class="admin-tab-btn px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 flex items-center gap-2">
                    <?= icon('users', 'w-4 h-4') ?>
                    <span>Manage Users (<?= count($allUsers) ?>)</span>
                </button>
                <button type="button" onclick="switchAdminTab('categories')" id="adminTabCategories" class="admin-tab-btn px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 flex items-center gap-2">
                    <?= icon('wrench', 'w-4 h-4') ?>
                    <span>Service Categories (<?= count($categories) ?>)</span>
                </button>
            </nav>
        </div>

        <!-- 1. PENDING VERIFICATIONS QUEUE -->
        <div id="adminContentPending" class="space-y-4">
            <div class="card-soft bg-white rounded-3xl border border-slate-200/90 shadow-md overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Provider Verification Queue</h2>
                        <p class="text-xs text-slate-500">Review CTEVT certificates and citizenship credentials to prevent fraud.</p>
                    </div>
                </div>

                <?php if (!empty($pendingProviders)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700 divide-y divide-slate-100">
                            <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                <tr>
                                    <th class="p-4">Provider</th>
                                    <th class="p-4">Trade Category</th>
                                    <th class="p-4">Location / Wards</th>
                                    <th class="p-4">Citizenship / Doc</th>
                                    <th class="p-4">Experience</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($pendingProviders as $prov): ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <img src="<?= htmlspecialchars($prov['avatar_url']) ?>" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-200">
                                                <div>
                                                    <span class="font-extrabold text-slate-900 block"><?= htmlspecialchars($prov['full_name']) ?></span>
                                                    <span class="text-[11px] text-slate-400"><?= htmlspecialchars($prov['phone']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 font-bold text-rose-600">
                                            <?= htmlspecialchars($prov['category_name']) ?>
                                        </td>
                                        <td class="p-4 font-medium">
                                            <?= htmlspecialchars($prov['district']) ?>, Ward <?= htmlspecialchars((string)$prov['ward_no']) ?>
                                        </td>
                                        <td class="p-4">
                                            <span class="font-mono font-bold text-slate-800 block"><?= htmlspecialchars($prov['citizenship_no'] ?: '27-01-XX-XXXXX') ?></span>
                                            <span class="text-[10px] text-slate-400"><?= htmlspecialchars($prov['document_type'] ?: 'Citizenship Scan') ?></span>
                                        </td>
                                        <td class="p-4 font-semibold text-slate-700">
                                            <?= (int)$prov['experience_years'] ?> Years
                                        </td>
                                        <td class="p-4 text-right">
                                            <button type="button" 
                                                    onclick="openVerificationModal(<?= $prov['id'] ?>, '<?= addslashes($prov['full_name']) ?>', '<?= addslashes($prov['category_name']) ?>', <?= (int)$prov['experience_years'] ?>, '<?= addslashes($prov['document_type']) ?>', '<?= addslashes($prov['document_path']) ?>', '<?= addslashes($prov['citizenship_no']) ?>', '<?= addslashes($prov['phone']) ?>')"
                                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-brand text-white font-extrabold text-xs shadow-sm hover:bg-brand-dark transition-all">
                                                <span>Inspect & Verify</span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-12 text-center text-xs text-slate-400 space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                            <?= icon('check-circle', 'w-6 h-6') ?>
                        </div>
                        <p class="font-bold text-slate-800">Verification Queue is Empty!</p>
                        <p>All provider onboarding requests have been reviewed.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 2. ALL BOOKINGS MONITOR -->
        <div id="adminContentBookings" class="hidden space-y-4">
            <div class="card-soft bg-white rounded-3xl border border-slate-200/90 shadow-md overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Platform Bookings Monitor</h2>
                        <p class="text-xs text-slate-500">Live feed of service requests across Nepal.</p>
                    </div>
                    <div>
                        <input type="text" id="bookingSearchInput" onkeyup="filterTableRows('bookingSearchInput', 'adminBookingsTable')" placeholder="Search bookings..." class="text-xs rounded-xl border-slate-200 p-2 bg-slate-50">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table id="adminBookingsTable" class="w-full text-left text-xs text-slate-700 divide-y divide-slate-100">
                        <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                            <tr>
                                <th class="p-4">Code / Date</th>
                                <th class="p-4">Service</th>
                                <th class="p-4">Customer</th>
                                <th class="p-4">Assigned Worker</th>
                                <th class="p-4">Ward Location</th>
                                <th class="p-4">Amount</th>
                                <th class="p-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($allBookings as $b): 
                                $bMeta = STATUS_CONFIG[$b['status']] ?? STATUS_CONFIG['pending'];
                            ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-4">
                                        <span class="font-mono font-bold text-slate-900 block"><?= htmlspecialchars($b['booking_code']) ?></span>
                                        <span class="text-[10px] text-slate-400"><?= htmlspecialchars($b['scheduled_date']) ?></span>
                                    </td>
                                    <td class="p-4 font-bold text-slate-800"><?= htmlspecialchars($b['category_name']) ?></td>
                                    <td class="p-4 font-medium"><?= htmlspecialchars($b['customer_name']) ?></td>
                                    <td class="p-4 font-medium text-slate-800"><?= htmlspecialchars($b['provider_name'] ?: 'Unassigned') ?></td>
                                    <td class="p-4 text-slate-600"><?= htmlspecialchars($b['district']) ?>, W-<?= htmlspecialchars((string)$b['ward_no']) ?></td>
                                    <td class="p-4 font-bold text-slate-900"><?= formatNpr($b['estimated_amount']) ?></td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border <?= $bMeta['badge_class'] ?>">
                                            <?= $bMeta['label'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. MANAGE USERS -->
        <div id="adminContentUsers" class="hidden space-y-4">
            <div class="card-soft bg-white rounded-3xl border border-slate-200/90 shadow-md overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">User & Provider Accounts</h2>
                        <p class="text-xs text-slate-500">Manage customer and worker statuses.</p>
                    </div>
                    <input type="text" id="userSearchInput" onkeyup="filterTableRows('userSearchInput', 'adminUsersTable')" placeholder="Search by name, email or phone..." class="text-xs rounded-xl border-slate-200 p-2 bg-slate-50">
                </div>

                <div class="overflow-x-auto">
                    <table id="adminUsersTable" class="w-full text-left text-xs text-slate-700 divide-y divide-slate-100">
                        <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                            <tr>
                                <th class="p-4">User</th>
                                <th class="p-4">Contact</th>
                                <th class="p-4">Role</th>
                                <th class="p-4">Ward Location</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($allUsers as $u): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-4">
                                        <div class="flex items-center gap-2.5">
                                            <img src="<?= htmlspecialchars($u['avatar_url']) ?>" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200">
                                            <span class="font-bold text-slate-900"><?= htmlspecialchars($u['full_name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="block text-slate-700"><?= htmlspecialchars($u['phone']) ?></span>
                                        <span class="text-[10px] text-slate-400"><?= htmlspecialchars($u['email']) ?></span>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase <?= $u['role'] === 'provider' ? 'bg-indigo-50 text-indigo-700' : ($u['role'] === 'admin' ? 'bg-rose-100 text-brand' : 'bg-slate-100 text-slate-700') ?>">
                                            <?= $u['role'] ?>
                                        </span>
                                    </td>
                                    <td class="p-4"><?= htmlspecialchars($u['district']) ?>, Ward <?= htmlspecialchars((string)$u['ward_no']) ?></td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $u['status'] === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' ?>">
                                            <?= ucfirst($u['status']) ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <?php if ($u['role'] !== 'admin'): ?>
                                            <button type="button" onclick="toggleUserStatus(<?= $u['id'] ?>, '<?= $u['status'] === 'active' ? 'suspended' : 'active' ?>')" class="text-xs font-bold text-slate-600 hover:text-brand underline">
                                                <?= $u['status'] === 'active' ? 'Suspend' : 'Reactivate' ?>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 4. MANAGE CATEGORIES -->
        <div id="adminContentCategories" class="hidden space-y-4">
            <div class="card-soft bg-white rounded-3xl border border-slate-200/90 shadow-md p-6">
                <h2 class="text-base font-extrabold text-slate-900 mb-4">Configured Service Trades & Base Rates</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <?php foreach ($categories as $cat): ?>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-rose-100 text-brand flex items-center justify-center">
                                    <?= icon($cat['icon_name'], 'w-4 h-4') ?>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900"><?= htmlspecialchars($cat['name']) ?></h4>
                                    <span class="text-[10px] text-slate-400 font-nepali"><?= htmlspecialchars($cat['nepali_name']) ?></span>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-slate-200/70 flex items-center justify-between text-xs">
                                <span class="text-slate-500">Base Price:</span>
                                <span class="font-extrabold text-slate-900"><?= formatNpr($cat['base_price']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Verification Document Preview Modal -->
<div id="verifyDocumentModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-100 space-y-5 animate-in fade-in zoom-in-95">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 id="verifyModalName" class="text-base font-extrabold text-slate-900">Bimal Bishwakarma</h3>
                <p id="verifyModalCategory" class="text-xs text-rose-600 font-semibold">Plumbing • 3 Years Experience</p>
            </div>
            <button type="button" onclick="closeVerificationModal()" class="text-slate-400 hover:text-slate-600">
                <?= icon('x', 'w-5 h-5') ?>
            </button>
        </div>

        <div class="space-y-4 text-xs">
            <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Citizenship No:</span>
                    <p id="verifyModalCitizenshipNo" class="font-mono font-bold text-slate-900">27-06-80-01923</p>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Mobile Phone:</span>
                    <p id="verifyModalPhone" class="font-bold text-slate-900">9851098769</p>
                </div>
            </div>

            <div>
                <span class="text-slate-500 font-bold uppercase text-[10px] block mb-1">Document Upload Preview (<span id="verifyModalDocType">Scan</span>):</span>
                <div class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-900 max-h-64 flex items-center justify-center">
                    <img id="verifyModalDocImage" src="" alt="Document Scan" class="w-full h-auto object-contain max-h-60">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Verification Remarks / Notes</label>
                <input type="text" id="verifyRemarks" placeholder="e.g. CTEVT certificate matched with National ID records." class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50">
            </div>

            <div class="pt-3 grid grid-cols-2 gap-3">
                <button type="button" id="btn-rejected" onclick="submitVerification('rejected')" class="py-2.5 px-4 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl transition-colors border border-rose-200">
                    Reject Document
                </button>
                <button type="button" id="btn-approved" onclick="submitVerification('approved')" class="py-2.5 px-4 text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-600/20 active:scale-95 transition-all">
                    Approve & Verify Worker
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/admin.js"></script>
<script>
function switchAdminTab(tab) {
    document.querySelectorAll('.admin-tab-btn').forEach(b => {
        b.className = 'admin-tab-btn px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 flex items-center gap-2';
    });
    document.getElementById('adminContentPending').classList.add('hidden');
    document.getElementById('adminContentBookings').classList.add('hidden');
    document.getElementById('adminContentUsers').classList.add('hidden');
    document.getElementById('adminContentCategories').classList.add('hidden');

    if (tab === 'pending') {
        document.getElementById('adminTabPending').className = 'admin-tab-btn px-4 py-2.5 rounded-xl bg-brand text-white flex items-center gap-2';
        document.getElementById('adminContentPending').classList.remove('hidden');
    } else if (tab === 'bookings') {
        document.getElementById('adminTabBookings').className = 'admin-tab-btn px-4 py-2.5 rounded-xl bg-brand text-white flex items-center gap-2';
        document.getElementById('adminContentBookings').classList.remove('hidden');
    } else if (tab === 'users') {
        document.getElementById('adminTabUsers').className = 'admin-tab-btn px-4 py-2.5 rounded-xl bg-brand text-white flex items-center gap-2';
        document.getElementById('adminContentUsers').classList.remove('hidden');
    } else if (tab === 'categories') {
        document.getElementById('adminTabCategories').className = 'admin-tab-btn px-4 py-2.5 rounded-xl bg-brand text-white flex items-center gap-2';
        document.getElementById('adminContentCategories').classList.remove('hidden');
    }
}

function toggleUserStatus(userId, newStatus) {
    if (confirm(`Change user account status to ${newStatus}?`)) {
        fetch('/api/admin.php?action=toggle_user_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId, status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 500);
            }
        });
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
