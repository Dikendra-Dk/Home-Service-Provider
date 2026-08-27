<?php
$pageTitle = 'Join as Service Provider';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/auth.php';

$db = Database::getConnection();
$categories = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $experienceYears = (int)($_POST['experience_years'] ?? 1);
    $hourlyRate = (float)($_POST['hourly_rate'] ?? 450.00);
    $citizenshipNo = trim($_POST['citizenship_no'] ?? '');
    $bio = trim($_POST['bio'] ?? 'Experienced technician with certified trade skills.');
    $district = $_POST['district'] ?? 'Kathmandu';
    $wardNo = (int)($_POST['ward_no'] ?? 4);
    $municipality = trim($_POST['municipality'] ?? 'Kathmandu Metropolitan City');
    $password = $_POST['password'] ?? '';

    if (empty($fullName) || empty($email) || empty($phone) || empty($citizenshipNo) || empty($password)) {
        $error = 'Please complete all required fields including Citizenship Number.';
    } else {
        // Check existing
        $check = $db->prepare("SELECT id FROM users WHERE email = :e OR phone = :p LIMIT 1");
        $check->execute(['e' => $email, 'p' => $phone]);
        if ($check->fetch()) {
            $error = 'An account with this email or phone number already exists.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $avatarUrl = 'https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=300&auto=format&fit=crop&q=80';
            $documentPath = 'https://images.unsplash.com/photo-1584438784894-089d6a62b8fa?w=600&auto=format&fit=crop&q=80';

            // Insert user
            $uStmt = $db->prepare("
                INSERT INTO users (full_name, email, phone, role, password_hash, avatar_url, province, district, municipality, ward_no, address_street, status, created_at)
                VALUES (:name, :email, :phone, 'provider', :hash, :avatar, 'Bagmati', :district, :muni, :ward, :street, 'active', NOW())
            ");
            $uStmt->execute([
                'name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'hash' => $passwordHash,
                'avatar' => $avatarUrl,
                'district' => $district,
                'muni' => $municipality,
                'ward' => $wardNo,
                'street' => $district . ' Ward ' . $wardNo
            ]);

            $newUserId = $db->lastInsertId();

            // Insert provider profile (Status: Pending Verification)
            $pStmt = $db->prepare("
                INSERT INTO provider_profiles (
                    user_id, category_id, tagline, bio, experience_years, hourly_rate,
                    is_available, is_verified, verification_status, citizenship_no, document_type, document_path,
                    rating_avg, review_count, jobs_completed, service_wards, created_at
                ) VALUES (
                    :uid, :cid, :tagline, :bio, :exp, :rate,
                    1, 0, 'pending', :cit_no, 'Citizenship & Trade Certificate', :doc_path,
                    5.00, 0, 0, :wards, NOW()
                )
            ");

            $tagline = "Professional {$categories[0]['name']} • {$experienceYears}+ Yrs Experience";
            $wardsJson = json_encode(["{$district} {$wardNo}", "{$district} " . ($wardNo + 1)]);

            $pStmt->execute([
                'uid' => $newUserId,
                'cid' => $categoryId,
                'tagline' => $tagline,
                'bio' => $bio,
                'exp' => $experienceYears,
                'rate' => $hourlyRate,
                'cit_no' => $citizenshipNo,
                'doc_path' => $documentPath,
                'wards' => $wardsJson
            ]);

            // Auto-login
            authenticateUser($email, $password);

            setFlash('success', 'Provider application submitted! Your citizenship credentials have been sent to the Admin queue for verification.');
            header("Location: /provider-dashboard.php");
            exit;
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="flex-grow py-12 bg-slate-50 flex items-center justify-center">
    <div class="max-w-2xl w-full mx-auto px-4">
        
        <div class="card-soft p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/90 shadow-xl space-y-6">
            
            <div class="text-center space-y-1">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto shadow-sm mb-3">
                    <?= icon('badge-check', 'w-6 h-6') ?>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Register as Service Partner</h1>
                <p class="text-xs text-slate-500">Join Nepal's trusted home service network. Get regular daily jobs on your phone.</p>
            </div>

            <?php if ($error): ?>
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2">
                    <?= icon('alert-circle', 'w-4 h-4 text-rose-600 shrink-0') ?>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4 text-xs">
                
                <!-- 1. Personal Info -->
                <div class="space-y-3">
                    <span class="text-slate-500 font-bold uppercase text-[10px] block">Personal Information</span>
                    
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Full Name (As on Citizenship Card)</label>
                        <input type="text" name="full_name" required placeholder="e.g. Ram Bahadur Shrestha" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Mobile Phone (+977)</label>
                            <input type="tel" name="phone" required placeholder="9851XXXXXX" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                            <input type="email" name="email" required placeholder="ram.plumber@gmail.com" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                        </div>
                    </div>
                </div>

                <!-- 2. Trade & Skill Details -->
                <div class="pt-2 border-t border-slate-100 space-y-3">
                    <span class="text-slate-500 font-bold uppercase text-[10px] block">Service Trade & Experience</span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Primary Trade Category</label>
                            <select name="category_id" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?> (<?= htmlspecialchars($cat['nepali_name']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Years of Practical Experience</label>
                            <input type="number" name="experience_years" value="5" min="1" max="40" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Base Inspection Fee (NPR)</label>
                        <input type="number" name="hourly_rate" value="450" step="50" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Brief Bio / Specialty</label>
                        <textarea name="bio" rows="2" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50" placeholder="e.g. Master plumber specializing in sanitary fittings, pump motor repairs, and leak diagnostics."></textarea>
                    </div>
                </div>

                <!-- 3. Identity Verification Step -->
                <div class="pt-2 border-t border-slate-100 space-y-3">
                    <span class="text-brand font-bold uppercase text-[10px] flex items-center gap-1.5">
                        <?= icon('shield-check', 'w-3.5 h-3.5 text-brand') ?>
                        <span>Identity Verification (Required for Approval)</span>
                    </span>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Citizenship Certificate Number (नागरिकता नं.)</label>
                        <input type="text" name="citizenship_no" required placeholder="e.g. 27-01-78-01923" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-mono font-semibold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Upload Citizenship / Trade Certificate Scan</label>
                        <input type="file" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-brand hover:file:bg-rose-100 cursor-pointer">
                        <span class="text-[10px] text-slate-400 block mt-1">Accepted: JPG, PNG, PDF up to 5MB. Verified by SewaSathi admin team within 24 hours.</span>
                    </div>
                </div>

                <!-- 4. Location & Password -->
                <div class="pt-2 border-t border-slate-100 space-y-3">
                    <span class="text-slate-500 font-bold uppercase text-[10px] block">Operational Hub & Security</span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Primary District</label>
                            <select name="district" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                                <option value="Kathmandu">Kathmandu</option>
                                <option value="Lalitpur">Lalitpur</option>
                                <option value="Bhaktapur">Bhaktapur</option>
                                <option value="Chitwan">Chitwan</option>
                                <option value="Kaski">Pokhara (Kaski)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Home Ward Number</label>
                            <select name="ward_no" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                                <?php for ($w = 1; $w <= 32; $w++): ?>
                                    <option value="<?= $w ?>">Ward <?= $w ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Create Password</label>
                        <input type="password" name="password" required placeholder="Minimum 6 characters" class="w-full rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-slate-50 font-semibold">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 text-xs font-extrabold text-white bg-slate-900 hover:bg-slate-800 rounded-xl shadow-lg active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <?= icon('badge-check', 'w-4 h-4 text-emerald-400') ?>
                    <span>Submit Verification & Join Network</span>
                </button>
            </form>

            <div class="pt-2 text-center text-xs text-slate-500">
                Already registered as a provider? <a href="/login.php" class="font-extrabold text-brand hover:underline">Log In</a>
            </div>

        </div>

    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
