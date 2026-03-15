<?php
/**
 * admin/view_applications.php — Admin Dashboard (Supabase API Version)
 * List applications and view specific application details using Supabase REST API.
 */

require_once '../db.php';

// Simple session check (add actual auth logic as needed)
session_start();
if (!isset($_SESSION['admin_id'])) {
    // For demo, if no session, just redirect to login if it exists, or continue
    // header('Location: login.php');
}

$id = (int)($_GET['id'] ?? 0);
$viewApp = null;

if ($id > 0) {
    // Fetch individual application via API
    $response = supabaseRequest("applications?id=eq.{$id}");
    if (!isset($response['error']) && !empty($response)) {
        $viewApp = $response[0];
    }
}

// Fetch all applications for the sidebar list
// Ordered by created_at descending
$listResponse = supabaseRequest("applications?select=id,student_name,class_applied,created_at&order=created_at.desc");
$applications = (isset($listResponse['error'])) ? [] : $listResponse;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Applications - Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
  </style>
</head>
<body class="bg-slate-50 text-slate-900">

  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <?php include 'partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 flex overflow-hidden">
      
      <!-- Applications List Sidebar -->
      <div class="w-80 border-r border-slate-200 bg-white flex flex-col flex-shrink-0">
        <div class="p-4 border-bottom border-slate-100 bg-slate-50/50">
          <h2 class="text-lg font-bold text-slate-800">Applications</h2>
          <p class="text-xs text-slate-500"><?= count($applications) ?> total submissions</p>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar">
          <?php if (empty($applications)): ?>
            <div class="p-8 text-center text-slate-400">
              <i class="fa-solid fa-folder-open text-4xl mb-3 opacity-20"></i>
              <p class="text-sm">No applications found</p>
            </div>
          <?php else: ?>
            <?php foreach ($applications as $app): ?>
              <a href="?id=<?= $app['id'] ?>" 
                 class="block p-4 border-b border-slate-50 hover:bg-indigo-50/50 transition-colors <?= ($id == $app['id']) ? 'bg-indigo-50 border-l-4 border-l-indigo-600' : '' ?>">
                <div class="flex justify-between items-start mb-1">
                  <span class="text-xs font-bold text-indigo-600">#<?= sprintf('%04d', $app['id']) ?></span>
                  <span class="text-[10px] text-slate-400"><?= date('d M', strtotime($app['created_at'])) ?></span>
                </div>
                <h3 class="font-semibold text-slate-800 text-sm truncate"><?= htmlspecialchars($app['student_name']) ?></h3>
                <p class="text-xs text-slate-500"><?= htmlspecialchars($app['class_applied']) ?></p>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Application Details -->
      <div class="flex-1 overflow-y-auto bg-slate-50 custom-scrollbar">
        <?php if ($viewApp): ?>
          <div class="max-w-4xl mx-auto p-8">
            
            <!-- Actions Header -->
            <div class="flex justify-between items-center mb-6">
              <a href="view_applications.php" class="text-slate-500 hover:text-slate-800 lg:hidden">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back
              </a>
              <div class="flex gap-2 ml-auto">
                <a href="print_application.php?id=<?= $viewApp['id'] ?>" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition shadow-sm">
                  <i class="fa-solid fa-print mr-2 text-slate-400"></i> Print Details
                </a>
              </div>
            </div>

            <!-- Header card -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl p-6 text-white flex flex-col sm:flex-row items-start sm:items-center gap-4">
              <?php if (!empty($viewApp['photo'])): ?>
              <img src="../file_proxy.php?id=<?= $viewApp['id'] ?>&field=photo"
                   alt="Student Photo"
                   class="w-20 h-20 rounded-xl object-contain bg-white/20 border-4 border-white/30 shadow-lg flex-shrink-0" />
              <?php else: ?>
              <div class="w-20 h-20 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-user text-white text-3xl"></i>
              </div>
              <?php endif; ?>
              
              <div>
                <h1 class="text-2xl font-bold"><?= htmlspecialchars($viewApp['student_name']) ?></h1>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 opacity-90 text-sm">
                  <span><i class="fa-solid fa-envelope mr-1.5 opacity-70"></i> <?= htmlspecialchars($viewApp['email']) ?></span>
                  <span><i class="fa-solid fa-phone mr-1.5 opacity-70"></i> <?= htmlspecialchars($viewApp['phone']) ?></span>
                </div>
              </div>
              
              <div class="sm:ml-auto bg-white/10 px-4 py-2 rounded-xl backdrop-blur-md border border-white/10 text-center min-w-[100px]">
                <p class="text-[10px] uppercase tracking-wider opacity-70">Status</p>
                <p class="font-bold"><?= $viewApp['status'] ?></p>
              </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
              
              <!-- Basic Info -->
              <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-4 flex items-center">
                  <span class="w-2 h-2 bg-indigo-600 rounded-full mr-2"></span> Basic Details
                </h2>
                <div class="space-y-4">
                  <div><p class="text-xs text-slate-400 mb-1">Date of Birth</p><p class="font-medium"><?= date('d F Y', strtotime($viewApp['dob'])) ?></p></div>
                  <div><p class="text-xs text-slate-400 mb-1">Gender</p><p class="font-medium"><?= $viewApp['gender'] ?></p></div>
                  <div><p class="text-xs text-slate-400 mb-1">Class Applied</p><p class="font-medium text-indigo-600"><?= $viewApp['class_applied'] ?></p></div>
                  <?php if (!empty($viewApp['stream'])): ?>
                  <div><p class="text-xs text-slate-400 mb-1">Stream</p><p class="font-medium text-purple-600"><?= $viewApp['stream'] ?></p></div>
                  <?php endif; ?>
                  <div><p class="text-xs text-slate-400 mb-1">Blood Group</p><p class="font-medium"><?= htmlspecialchars($viewApp['blood_group'] ?: '—') ?></p></div>
                  <div><p class="text-xs text-slate-400 mb-1">Aadhaar</p><p class="font-medium">XXXX XXXX <?= substr(htmlspecialchars($viewApp['aadhaar']), -4) ?></p></div>
                  <div><p class="text-xs text-slate-400 mb-1">Previous School</p><p class="font-medium"><?= htmlspecialchars($viewApp['previous_school'] ?: '—') ?></p></div>
                </div>
              </div>

              <!-- Parent Info -->
              <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-4 flex items-center">
                  <span class="w-2 h-2 bg-purple-600 rounded-full mr-2"></span> Family & Address
                </h2>
                <div class="space-y-4">
                  <div><p class="text-xs text-slate-400 mb-1">Father's Name</p><p class="font-medium"><?= htmlspecialchars($viewApp['father_name']) ?></p></div>
                  <div><p class="text-xs text-slate-400 mb-1">Mother's Name</p><p class="font-medium"><?= htmlspecialchars($viewApp['mother_name']) ?></p></div>
                  <div><p class="text-xs text-slate-400 mb-1">Full Address</p><p class="font-medium text-sm leading-relaxed"><?= htmlspecialchars($viewApp['address']) ?></p></div>
                  <div class="grid grid-cols-2 gap-4">
                    <div><p class="text-xs text-slate-400 mb-1">City</p><p class="font-medium"><?= htmlspecialchars($viewApp['city']) ?></p></div>
                    <div><p class="text-xs text-slate-400 mb-1">State</p><p class="font-medium"><?= htmlspecialchars($viewApp['state']) ?></p></div>
                  </div>
                  <div><p class="text-xs text-slate-400 mb-1">Pincode</p><p class="font-medium"><?= htmlspecialchars($viewApp['pincode']) ?></p></div>
                </div>
              </div>

            </div>

            <!-- Documents -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 mt-8">
              <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-6 flex items-center">
                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span> Submitted Documents
              </h2>
              
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <?php 
                $docs = [
                  'photo'         => ['Student Photo',        'fa-user', true],
                  'aadhaar_front' => ['Aadhaar Front',       'fa-id-card', true],
                  'aadhaar_back'  => ['Aadhaar Back',        'fa-id-card', true],
                  'marksheet'     => ['Previous Marksheet',   'fa-file-invoice', false],
                ];
                foreach ($docs as $field => [$label, $icon, $showThumb]):
                  $hasFile = !empty($viewApp[$field]);
                  $url = $hasFile ? "../file_proxy.php?id={$viewApp['id']}&field={$field}" : null;
                  $mime = $viewApp[$field . '_mime'] ?? '';
                  $isImage = strpos($mime, 'image/') === 0;
                ?>
                <div class="border border-slate-100 rounded-xl p-3 flex flex-col items-center gap-2 bg-slate-50 hover:bg-white transition group relative overflow-hidden">
                  <?php if ($url && $isImage): ?>
                    <img src="<?= $url ?>" 
                         class="w-full h-24 object-contain rounded-lg bg-white shadow-sm" alt="<?= $label ?>" />
                  <?php elseif ($url): ?>
                    <div class="w-full h-24 flex items-center justify-center bg-white rounded-lg shadow-sm">
                       <i class="fa-solid fa-file-pdf text-3xl text-red-500"></i>
                    </div>
                  <?php else: ?>
                    <div class="w-full h-24 flex items-center justify-center bg-slate-100/50 rounded-lg border-2 border-dashed border-slate-200">
                       <i class="fa-solid <?= $icon ?> text-2xl text-slate-300"></i>
                    </div>
                  <?php endif; ?>
                  
                  <span class="text-[10px] font-bold text-slate-500 text-center uppercase tracking-tighter"><?= $label ?></span>
                  
                  <?php if ($url): ?>
                  <a href="<?= $url ?>" target="_blank" 
                     class="absolute inset-0 bg-indigo-600/90 text-white flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-xl">
                    <i class="fa-solid fa-eye mb-1"></i>
                    <span class="text-[10px] font-bold">VIEW</span>
                  </a>
                  <?php endif; ?>
                </div>
                <?php endforeach; ?>
              </div>
            </div>

          </div>
        <?php else: ?>
          <div class="h-full flex flex-col items-center justify-center p-12 text-slate-400">
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-sm mb-6 border border-slate-100">
              <i class="fa-solid fa-id-card-clip text-4xl opacity-20"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-800">Select an application</h2>
            <p class="max-w-xs text-center mt-2 text-sm">Choose an entry from the left sidebar to view the full student profile and documents.</p>
          </div>
        <?php endif; ?>
      </div>

    </main>
  </div>

</body>
</html>
