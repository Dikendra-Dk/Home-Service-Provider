<?php
$pageTitle = 'Sign In';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/auth.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($identifier) || empty($password)) {
        $error = 'Please enter your email/phone and password.';
    } else {
        $result = authenticateUser($identifier, $password);
        if ($result['success']) {
            $user = $result['user'];
            $redirect = $_SESSION['redirect_after_login'] ?? null;
            unset($_SESSION['redirect_after_login']);

            if ($redirect) {
                header("Location: {$redirect}");
                exit;
            }

            if ($user['role'] === 'admin') {
                header("Location: /admin-dashboard.php");
            } elseif ($user['role'] === 'provider') {
                header("Location: /provider-dashboard.php");
            } else {
                header("Location: /customer-dashboard.php");
            }
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="flex-grow py-12 bg-slate-50 flex items-center justify-center">
    <div class="max-w-md w-full mx-auto px-4">
        
        <div class="card-soft p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/90 shadow-xl space-y-6">
            
            <div class="text-center space-y-1">
                <div class="w-12 h-12 rounded-2xl bg-brand text-white flex items-center justify-center mx-auto shadow-md shadow-rose-600/20 mb-3">
                    <?= icon('wrench', 'w-6 h-6') ?>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Welcome to SewaSathi</h1>
                <p class="text-xs text-slate-500">Sign in to book or manage verified home services</p>
            </div>

            <?php if ($error): ?>
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2">
                    <?= icon('alert-circle', 'w-4 h-4 text-rose-600 shrink-0') ?>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email or Phone Number</label>
                    <input type="text" name="identifier" id="loginIdentifier" required placeholder="customer@sewasathi.com or 9841..." class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-3 bg-slate-50 text-slate-900 font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" id="loginPassword" required placeholder="••••••••" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-3 bg-slate-50 text-slate-900 font-semibold">
                </div>

                <button type="submit" class="w-full py-3.5 px-4 text-xs font-extrabold text-white bg-brand hover:bg-brand-dark rounded-xl shadow-lg shadow-rose-600/25 active:scale-[0.98] transition-all">
                    Sign In
                </button>
            </form>

            <!-- 1-Click Demo Logins for Quick Testing -->
            <div class="pt-4 border-t border-slate-100 space-y-2.5">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block text-center">Quick Demo 1-Click Fill</span>
                
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="fillDemo('customer@sewasathi.com', 'password123')" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-center transition-colors">
                        <span class="text-[11px] font-bold text-slate-800 block">Customer</span>
                        <span class="text-[9px] text-slate-400">Aayush S.</span>
                    </button>
                    <button type="button" onclick="fillDemo('provider@sewasathi.com', 'password123')" class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-center transition-colors">
                        <span class="text-[11px] font-bold text-emerald-800 block">Provider</span>
                        <span class="text-[9px] text-emerald-600">Ram B. Plumber</span>
                    </button>
                    <button type="button" onclick="fillDemo('admin@sewasathi.com', 'admin123')" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200 text-center transition-colors">
                        <span class="text-[11px] font-bold text-brand block">Admin</span>
                        <span class="text-[9px] text-rose-500">Super Admin</span>
                    </button>
                </div>
            </div>

            <div class="pt-2 text-center text-xs text-slate-500 space-y-2">
                <p>Don't have an account? <a href="/register.php" class="font-extrabold text-brand hover:underline">Register as Customer</a></p>
                <p><a href="/register-provider.php" class="text-emerald-700 font-bold hover:underline">Want to work as a Mistiri / Technician?</a></p>
            </div>

        </div>

    </div>
</main>

<script>
function fillDemo(email, pass) {
    document.getElementById('loginIdentifier').value = email;
    document.getElementById('loginPassword').value = pass;
    showToast(`Filled ${email} credentials! Click Sign In.`, 'info');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
