<?php
$pageTitle = 'Create Customer Account';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/auth.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $district = $_POST['district'] ?? 'Kathmandu';
    $municipality = trim($_POST['municipality'] ?? 'Kathmandu Metropolitan City');
    $wardNo = (int)($_POST['ward_no'] ?? 4);
    $street = trim($_POST['address_street'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($fullName) || empty($email) || empty($phone) || empty($password)) {
        $error = 'Please complete all required fields.';
    } else {
        $db = Database::getConnection();

        // Check if email or phone already exists
        $checkStmt = $db->prepare("SELECT id FROM users WHERE email = :e OR phone = :p LIMIT 1");
        $checkStmt->execute(['e' => $email, 'p' => $phone]);
        if ($checkStmt->fetch()) {
            $error = 'An account with this email or phone number already exists.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $avatarUrl = 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200&auto=format&fit=crop&q=80';

            $insertStmt = $db->prepare("
                INSERT INTO users (full_name, email, phone, role, password_hash, avatar_url, province, district, municipality, ward_no, address_street, status, created_at)
                VALUES (:name, :email, :phone, 'customer', :hash, :avatar, 'Bagmati', :district, :muni, :ward, :street, 'active', NOW())
            ");
            $insertStmt->execute([
                'name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'hash' => $passwordHash,
                'avatar' => $avatarUrl,
                'district' => $district,
                'muni' => $municipality,
                'ward' => $wardNo,
                'street' => $street
            ]);

            $newUserId = $db->lastInsertId();

            // Auto-login
            $_SESSION['user'] = [
                'id' => $newUserId,
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'role' => 'customer',
                'avatar_url' => $avatarUrl,
                'province' => 'Bagmati',
                'district' => $district,
                'municipality' => $municipality,
                'ward_no' => $wardNo,
                'address_street' => $street
            ];

            setFlash('success', 'Account created successfully! Welcome to SewaSathi.');
            header("Location: /search.php");
            exit;
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="flex-grow py-12 bg-slate-50 flex items-center justify-center">
    <div class="max-w-xl w-full mx-auto px-4">
        
        <div class="card-soft p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/90 shadow-xl space-y-6">
            
            <div class="text-center space-y-1">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-brand flex items-center justify-center mx-auto shadow-sm mb-3">
                    <?= icon('user', 'w-6 h-6') ?>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Create Customer Account</h1>
                <p class="text-xs text-slate-500">Book trusted mistiris & home technicians in your ward</p>
            </div>

            <?php if ($error): ?>
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2">
                    <?= icon('alert-circle', 'w-4 h-4 text-rose-600 shrink-0') ?>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="full_name" required placeholder="e.g. Bijay Khadka" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Mobile Number (+977)</label>
                        <input type="tel" name="phone" required placeholder="9841XXXXXX" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" required placeholder="bijay@gmail.com" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                    </div>
                </div>

                <!-- Nepali Location -->
                <div class="pt-2 border-t border-slate-100 space-y-3">
                    <span class="text-slate-500 font-bold uppercase text-[10px] block">Your Location in Nepal</span>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">District</label>
                            <select name="district" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                                <option value="Kathmandu">Kathmandu</option>
                                <option value="Lalitpur">Lalitpur</option>
                                <option value="Bhaktapur">Bhaktapur</option>
                                <option value="Chitwan">Chitwan</option>
                                <option value="Kaski">Pokhara (Kaski)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Ward Number</label>
                            <select name="ward_no" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                                <?php for ($w = 1; $w <= 32; $w++): ?>
                                    <option value="<?= $w ?>" <?= $w == 4 ? 'selected' : '' ?>>Ward <?= $w ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Municipality / Local Body</label>
                        <input type="text" name="municipality" value="Kathmandu Metropolitan City" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tole / Street Address</label>
                        <input type="text" name="address_street" placeholder="e.g. Baluwatar Marg, House 12" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="block font-bold text-slate-700 mb-1">Create Password</label>
                    <input type="password" name="password" required placeholder="Minimum 6 characters" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                </div>

                <button type="submit" class="w-full py-3.5 px-4 text-xs font-extrabold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-lg shadow-rose-600/25 active:scale-[0.98] transition-all">
                    Register & Continue
                </button>
            </form>

            <div class="pt-2 text-center text-xs text-slate-500">
                Already have an account? <a href="/login.php" class="font-extrabold text-brand hover:underline">Log In</a>
            </div>

        </div>

    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
