<?php
/**
 * admin/dashboard.php — Admin Dashboard (Supabase API Version)
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';

// Fetch stats via API
// Note: Direct count is best done via RPC or header, but here we'll fetch IDs and count for simplicity
$allApps = supabaseRequest("applications?select=id,status,created_at,class_applied");
$applications = (isset($allApps['error'])) ? [] : $allApps;

$total      = count($applications);
$pending    = 0;
$accepted   = 0;
$rejected   = 0;
$reviewed   = 0;
$today_cnt  = 0;
$today      = date('Y-m-d');

$classCounts = [];

foreach ($applications as $app) {
    if ($app['status'] === 'Pending') $pending++;
    if ($app['status'] === 'Accepted') $accepted++;
    if ($app['status'] === 'Rejected') $rejected++;
    if ($app['status'] === 'Reviewed') $reviewed++;
    
    if (date('Y-m-d', strtotime($app['created_at'])) === $today) $today_cnt++;
    
    $cls = $app['class_applied'];
    $classCounts[$cls] = ($classCounts[$cls] ?? 0) + 1;
}

// Sort class counts
arsort($classCounts);
$classBreakdown = array_slice($classCounts, 0, 5, true);

// Recent 5 applications
$recentResponse = supabaseRequest("applications?select=id,student_name,class_applied,phone,status,created_at&order=created_at.desc&limit=5");
$recent = (isset($recentResponse['error'])) ? [] : $recentResponse;

$username = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | School Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    body { font-family:'Inter',sans-serif; background:#f1f5f9; }
    .stat-card {
      padding: 1.5rem; border-radius: 1rem; color: white; position: relative; overflow: hidden;
      display: flex; justify-content: space-between; align-items: center;
    }
    .stat-icon { font-size: 3rem; opacity: 0.2; position: absolute; right: -0.5rem; bottom: -0.5rem; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { text-align: left; padding: 12px 24px; font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; border-bottom: 1px solid #f1f5f9; }
    .data-table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
    .badge { padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .badge-pending { background: #fef3c7; color: #d97706; }
    .badge-accepted { background: #dcfce7; color: #16a34a; }
    .badge-rejected { background: #fee2e2; color: #dc2626; }
    .badge-reviewed { background: #dbeafe; color: #2563eb; }
    .btn-view { padding: 6px 12px; border-radius: 6px; background: #f1f5f9; color: #475569; font-size: 12px; font-weight: 600; transition: 0.2s; }
    .btn-view:hover { background: #e2e8f0; color: #1e293b; }
  </style>
</head>
<body>
<div class="flex h-screen overflow-hidden">

  <!-- ===== SIDEBAR ===== -->
  <?php include 'partials/sidebar.php'; ?>

  <!-- ===== MAIN ===== -->
  <div class="flex-1 overflow-auto bg-slate-50">
    <!-- Top bar -->
    <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between sticky top-0 z-30">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Dashboard</h2>
        <p class="text-xs text-gray-400">Welcome back, <?= htmlspecialchars($username) ?></p>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-xs bg-green-100 text-green-700 font-semibold px-3 py-1 rounded-full">
          <i class="fa-solid fa-circle text-xs mr-1"></i> Online
        </span>
        <a href="logout.php" class="text-sm text-red-500 hover:text-red-700 font-medium transition flex items-center gap-1">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
      </div>
    </header>

    <main class="p-6 space-y-6">

      <!-- ===== STAT CARDS ===== -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <div class="stat-card" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
          <div>
            <p class="text-indigo-200 text-xs font-semibold uppercase tracking-wider">Total Applications</p>
            <p class="text-4xl font-extrabold mt-1"><?= $total ?></p>
          </div>
          <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
          <div>
            <p class="text-amber-100 text-xs font-semibold uppercase tracking-wider">Pending Review</p>
            <p class="text-4xl font-extrabold mt-1"><?= $pending ?></p>
          </div>
          <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#059669)">
          <div>
            <p class="text-emerald-100 text-xs font-semibold uppercase tracking-wider">Accepted</p>
            <p class="text-4xl font-extrabold mt-1"><?= $accepted ?></p>
          </div>
          <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#ec4899,#be185d)">
          <div>
            <p class="text-pink-100 text-xs font-semibold uppercase tracking-wider">Today</p>
            <p class="text-4xl font-extrabold mt-1"><?= $today_cnt ?></p>
          </div>
          <div class="stat-icon"><i class="fa-solid fa-calendar-day"></i></div>
        </div>
      </div>

      <!-- ===== BREAKDOWN & ACTIONS ===== -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Class Breakdown -->
        <div class="bg-white rounded-2xl p-5 shadow-sm lg:col-span-1 border border-slate-100">
          <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-chart-bar text-indigo-500"></i> Applications by Class
          </h3>
          <?php foreach ($classBreakdown as $clsName => $cnt):
            $pct = $total > 0 ? round(($cnt/$total)*100) : 0; ?>
          <div class="mb-3">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-600 font-medium"><?= htmlspecialchars($clsName) ?></span>
              <span class="text-indigo-600 font-semibold"><?= $cnt ?></span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
              <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all" style="width:<?= $pct ?>%"></div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($classBreakdown)): ?>
            <p class="text-gray-400 text-sm text-center py-4">No data yet.</p>
          <?php endif; ?>
        </div>

        <!-- Status Breakdown -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
          <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-circle-half-stroke text-purple-500"></i> Application Status
          </h3>
          <div class="space-y-4">
            <?php
            $statuses = [
              ['Pending',  $pending,  'bg-amber-400'],
              ['Accepted', $accepted, 'bg-emerald-500'],
              ['Rejected', $rejected, 'bg-rose-500'],
              ['Reviewed', $reviewed, 'bg-blue-500'],
            ];
            foreach ($statuses as [$label,$cnt,$color]):
              $pct2 = $total > 0 ? round(($cnt/$total)*100) : 0;
            ?>
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 rounded-full <?= $color ?> flex-shrink-0"></div>
              <div class="flex-1">
                <div class="flex justify-between text-sm mb-0.5">
                  <span class="text-gray-600"><?= $label ?></span>
                  <span class="font-semibold text-gray-700"><?= $cnt ?></span>
                </div>
                <div class="relative w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                  <div class="<?= $color ?> h-full" style="width:<?= $pct2 ?>%"></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
          <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-bolt text-amber-500"></i> Quick Actions
          </h3>
          <div class="space-y-3">
            <a href="view_applications.php"
               class="flex items-center gap-3 p-3 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium text-sm transition">
              <i class="fa-solid fa-list w-5 text-center"></i> View All Applications
            </a>
            <a href="../index.html" target="_blank"
               class="flex items-center gap-3 p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-medium text-sm transition">
              <i class="fa-solid fa-external-link w-5 text-center"></i> Open Admission Form
            </a>
            <a href="logout.php"
               class="flex items-center gap-3 p-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-medium text-sm transition">
              <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Logout
            </a>
          </div>
        </div>
      </div>

      <!-- ===== RECENT APPLICATIONS TABLE ===== -->
      <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h3 class="font-bold text-gray-700 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i> Recent Applications
          </h3>
          <a href="view_applications.php" class="text-xs text-indigo-600 hover:underline font-semibold">View All →</a>
        </div>
        <div class="overflow-x-auto">
          <table class="data-table">
            <thead>
              <tr>
                <th>#ID</th>
                <th>Student Name</th>
                <th>Class</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent as $row): ?>
              <tr>
                <td class="font-semibold text-indigo-600">#<?= sprintf('%04d', $row['id']) ?></td>
                <td class="font-medium text-gray-800"><?= htmlspecialchars($row['student_name']) ?></td>
                <td><?= htmlspecialchars($row['class_applied']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td>
                  <span class="badge badge-<?= strtolower($row['status']) ?>">
                    <?= htmlspecialchars($row['status']) ?>
                  </span>
                </td>
                <td class="text-gray-500 text-xs"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                <td>
                  <a href="view_applications.php?id=<?= $row['id'] ?>" class="btn-view">
                    <i class="fa-solid fa-eye"></i> View
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($recent)): ?>
              <tr><td colspan="7" class="text-center text-gray-400 py-12">No applications yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div><!-- /main -->
</div><!-- /layout -->
</body>
</html>
