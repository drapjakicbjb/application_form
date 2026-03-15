<?php
/**
 * admin/change_password.php — Change Admin Password (Supabase API Version)
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current  = trim($_POST['current_password']  ?? '');
    $newPwd   = trim($_POST['new_password']       ?? '');
    $confirm  = trim($_POST['confirm_password']   ?? '');

    if (empty($current) || empty($newPwd) || empty($confirm)) {
        $error = 'All fields are required.';
    } elseif (strlen($newPwd) < 8) {
        $error = 'New password must be at least 8 characters long.';
    } elseif ($newPwd !== $confirm) {
        $error = 'New password and confirm password do not match.';
    } else {
        // Fetch current admin user via API
        $adminId = $_SESSION['admin_id'];
        $response = supabaseRequest("admin_users?id=eq.{$adminId}");
        
        if (isset($response['error']) || empty($response)) {
            $error = 'API Error: Could not fetch user details.';
        } else {
            $admin = $response[0];
            if (!password_verify($current, $admin['password'])) {
                $error = 'Current password is incorrect.';
            } else {
                // Update password via API PATCH
                $newHash = password_hash($newPwd, PASSWORD_BCRYPT, ['cost' => 12]);
                $updRes = supabaseRequest("admin_users?id=eq.{$adminId}", 'PATCH', ['password' => $newHash]);
                
                if (isset($updRes['error'])) {
                    $error = 'API Error: Could not update password.';
                } else {
                    $success = 'Password changed successfully!';
                }
            }
        }
    }
}

$username = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Change Password | Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
</head>
<body class="bg-slate-50 text-slate-900">

  <div class="flex h-screen overflow-hidden">
    <?php include 'partials/sidebar.php'; ?>

    <main class="flex-1 overflow-auto p-8">
      <div class="max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Security Settings</h1>

        <?php if ($success): ?>
          <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6">
            <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>

        <?php if ($error): ?>
          <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl mb-6">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
          <h2 class="text-lg font-bold mb-4">Update Password</h2>
          <form method="POST" action="change_password.php" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">Current Password</label>
              <input type="password" name="current_password" required
                class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">New Password (Min 8 chars)</label>
              <input type="password" name="new_password" required minlength="8"
                class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Confirm New Password</label>
              <input type="password" name="confirm_password" required
                class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>
            <button type="submit"
               class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg transition">
               Update Credentials
            </button>
          </form>
        </div>
      </div>
    </main>
  </div>

</body>
</html>
