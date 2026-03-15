<?php
/**
 * admin/partials/sidebar.php — Reusable Admin Sidebar
 * Include in all admin pages.
 */
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar hidden md:flex flex-col py-6">
  <!-- Brand -->
  <div class="flex items-center gap-3 px-6 mb-8">
    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
      <i class="fa-solid fa-graduation-cap text-white text-lg"></i>
    </div>
    <div>
      <p class="text-white font-bold text-sm leading-tight">School Admin</p>
      <p class="text-indigo-400 text-xs">Management Panel</p>
    </div>
  </div>

  <!-- Nav -->
  <nav class="flex-1 space-y-0.5">
    <p class="text-indigo-500 text-xs font-bold uppercase tracking-widest px-7 mb-2">Main Menu</p>

    <a href="dashboard.php" class="nav-item <?= $current==='dashboard.php'?'active':'' ?>">
      <i class="fa-solid fa-gauge-high"></i> Dashboard
    </a>
    <a href="view_applications.php" class="nav-item <?= $current==='view_applications.php'?'active':'' ?>">
      <i class="fa-solid fa-file-lines"></i> Applications
    </a>
    <a href="view_applications.php?status=Pending" class="nav-item">
      <i class="fa-solid fa-hourglass-half"></i> Pending
    </a>
    <a href="view_applications.php?status=Accepted" class="nav-item">
      <i class="fa-solid fa-circle-check"></i> Accepted
    </a>
    <a href="view_applications.php?status=Rejected" class="nav-item" style="color:#fca5a5">
      <i class="fa-solid fa-circle-xmark"></i> Rejected
    </a>

    <div class="my-4 border-t border-white/10 mx-5"></div>
    <p class="text-indigo-500 text-xs font-bold uppercase tracking-widest px-7 mb-2">Settings</p>

    <a href="change_password.php" class="nav-item <?= $current==='change_password.php'?'active':'' ?>">
      <i class="fa-solid fa-key"></i> Change Password
    </a>

    <div class="my-4 border-t border-white/10 mx-5"></div>
    <p class="text-indigo-500 text-xs font-bold uppercase tracking-widest px-7 mb-2">External</p>

    <a href="../index.html" target="_blank" class="nav-item">
      <i class="fa-solid fa-arrow-up-right-from-square"></i> Admission Form
    </a>
  </nav>

  <!-- User Footer -->
  <div class="px-5 mt-auto">
    <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-3 mb-3">
      <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
        <?= strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)) ?>
      </div>
      <div class="min-w-0">
        <p class="text-white font-semibold text-sm truncate"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></p>
        <p class="text-indigo-300 text-xs">Administrator</p>
      </div>
    </div>
    <a href="logout.php"
       class="flex items-center gap-2 text-sm text-red-300 hover:text-red-100 font-medium px-2 py-2 transition">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
  </div>
</aside>
