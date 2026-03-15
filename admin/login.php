<?php
/**
 * admin/login.php — Admin Login Page (Supabase API Version)
 */

session_start();
require_once '../db.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Fetch user by username via Supabase API
        $response = supabaseRequest("admin_users?username=eq.{$username}");
        
        if (isset($response['error'])) {
            $error = 'API Error: ' . ($response['message'] ?? 'Unable to connect.');
        } else {
            $admin = $response[0] ?? null;

            if ($admin && password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id']        = $admin['id'];
                $_SESSION['admin_username']  = $admin['username'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login | School Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Inter', sans-serif; background: #0f172a; }
    .glass-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

  <div class="w-full max-w-md">
    <div class="text-center mb-8">
      <div class="inline-flex w-20 h-20 rounded-2xl bg-indigo-600/20 text-indigo-500 items-center justify-center mb-4 text-3xl shadow-2xl">
        <i class="fa-solid fa-lock"></i>
      </div>
      <h1 class="text-3xl font-extrabold text-white">Admin Login</h1>
      <p class="text-slate-400 mt-2">Manage your school admissions portal</p>
    </div>

    <div class="glass-card rounded-3xl p-8 shadow-2xl">
      <?php if ($error): ?>
      <div class="bg-red-500/10 border border-red-500/50 text-red-400 rounded-xl px-4 py-3 mb-6 text-sm flex items-center gap-3">
        <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="login.php" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5" for="username">Username</label>
          <div class="relative">
            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
            <input type="text" id="username" name="username" required
              class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-900/50 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition shadow-inner"
              placeholder="Enter username" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5" for="password">Password</label>
          <div class="relative">
            <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
            <input type="password" id="password" name="password" required
              class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-900/50 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition shadow-inner"
              placeholder="••••••••" />
          </div>
        </div>

        <button type="submit"
          class="w-full py-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-base shadow-lg hover:shadow-indigo-500/20 transition-all transform hover:-translate-y-0.5">
          Sign In to Dashboard
        </button>
      </form>

      <div class="mt-8 pt-6 border-t border-slate-800 text-center">
        <a href="../index.html" class="text-slate-500 hover:text-indigo-400 text-sm transition font-medium">
          <i class="fa-solid fa-arrow-left mr-1"></i> Back to Admission Form
        </a>
      </div>
    </div>
  </div>

</body>
</html>
