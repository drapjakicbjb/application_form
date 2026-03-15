<?php
/**
 * confirmation.php — Student Confirmation Page (Supabase API Version)
 */

require_once 'db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.html');
    exit;
}

// Fetch application data via Supabase API
$response = supabaseRequest("applications?id=eq.{$id}");

if (isset($response['error']) || empty($response)) {
    die("Application not found.");
}

$app = $response[0];

// Helper to generate image/file URLs
function uploadUrl(int $id, string $field): string {
    return 'file_proxy.php?id=' . $id . '&field=' . $field;
}

$photoUrl  = !empty($app['photo']) ? uploadUrl($app['id'], 'photo') : '';
$submittedDate = date('d F Y', strtotime($app['created_at']));
$submittedTime = date('h:i A', strtotime($app['created_at']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Submitted - Dr. A.P.J. Abdul Kalam Inter College</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    :root {
      --primary: #4f46e5;
      --secondary: #6366f1;
      --success: #22c55e;
      --bg-slate: #f8fafc;
    }

    body {
      background-color: var(--bg-slate);
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      margin: 0; padding: 20px 10px; color: #1e293b;
    }

    .confirm-container {
      max-width: 900px; margin: 0 auto; background: white;
      border-radius: 24px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.05);
    }

    .hero-banner {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      padding: 40px 20px; text-align: center; color: white;
    }

    .success-badge {
      width: 70px; height: 70px; background: white; color: var(--success);
      border-radius: 50%; display: flex; items-center; justify-content: center;
      margin: 0 auto 15px; font-size: 32px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .hero-banner h1 { margin: 0 0 10px; font-size: 28px; font-weight: 700; }
    .hero-banner p { margin: 0; opacity: 0.9; font-size: 16px; }

    .content-layout { padding: 40px; display: grid; grid-template-columns: 280px 1fr; gap: 40px; }

    @media (max-width: 768px) {
      .content-layout { grid-template-columns: 1fr; padding: 30px 20px; gap: 30px; }
      .hero-banner { padding: 30px 20px; }
    }

    .student-sidebar { text-align: center; }

    .photo-box {
      width: 200px; height: 260px; background: #f1f5f9; border-radius: 20px;
      margin: 0 auto 20px; overflow: hidden; border: 4px solid white;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: center;
    }

    .photo-box img { width: 100%; height: 100%; object-fit: contain; background: #eee; }
    .photo-box i { font-size: 60px; color: #cbd5e1; }

    .reg-id {
      background: #eff6ff; color: #2563eb; padding: 12px; border-radius: 12px;
      font-family: monospace; font-size: 18px; font-weight: 700; display: inline-block;
      min-width: 140px; border: 1px dashed #bfdbfe;
    }

    .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    @media (max-width: 500px) { .info-grid { grid-template-columns: 1fr; } }

    .info-section { margin-bottom: 30px; border-bottom: 1px solid #f1f5f9; padding-bottom: 25px; }
    .info-section:last-child { border: none; margin-bottom: 0; }
    .section-title {
      font-size: 14px; text-transform: uppercase; letter-spacing: 1px;
      color: #64748b; margin: 0 0 20px; font-weight: 700; display: flex; align-items: center; gap: 8px;
    }
    .section-title i { color: var(--primary); }

    .field-label { font-size: 13px; color: #94a3b8; margin: 0 0 4px; }
    .field-value { font-size: 15px; color: #334155; font-weight: 600; margin: 0; word-break: break-all; }

    .docs-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 15px; }
    .doc-item {
      background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px;
      height: 150px; display: flex; flex-direction: column; align-items: center;
      justify-content: center; gap: 8px; transition: all 0.2s; position: relative; overflow: hidden;
    }

    .doc-img { width: 100%; height: 100%; object-fit: contain; background: #fff; padding: 5px; box-sizing: border-box; }
    .doc-icon-placeholder { font-size: 30px; margin-bottom: 5px; }
    .doc-name { font-size: 11px; font-weight: 600; padding: 5px; text-align: center; width: 100%; background: rgba(0,0,0,0.05); position: absolute; bottom: 0; }

    .actions-bar {
      padding: 30px 40px; background: #f8fafc; border-top: 1px solid #f1f5f9;
      display: flex; gap: 15px; justify-content: space-between; align-items: center;
    }

    .btn {
      padding: 12px 24px; border-radius: 10px; font-size: 15px;
      font-weight: 600; cursor: pointer; display: inline-flex; items-center; gap: 10px;
      text-decoration: none; transition: 0.2s; border: none;
    }
    .btn-print { background: var(--primary); color: white; }
    .btn-print:hover { background: var(--secondary); transform: translateY(-2px); }
    .btn-home { background: #fff; color: #475569; border: 1px solid #e2e8f0; }
    .btn-home:hover { background: #f1f5f9; }

    @media print {
      body { background: white; padding: 0; }
      .confirm-container { box-shadow: none; border: none; max-width: 100%; width: 100%; }
      .actions-bar, .success-badge, .hero-banner p { display: none; }
      .hero-banner { background: #fff !important; color: #000 !important; padding: 20px; border-bottom: 2px solid #eee; }
      .hero-banner h1 { font-size: 24px; text-align: left; }
      .content-layout { display: block; padding: 20px; }
      .student-sidebar { float: right; width: 220px; border-left: 1px solid #eee; padding-left: 20px; }
      .student-details { overflow: hidden; }
      .info-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
      .section-title { border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 20px; }
      .doc-item { border: 1px solid #eee; background: white; break-inside: avoid; }
      .doc-img { background: white !important; object-fit: contain !important; }
      .reg-id { font-size: 14px; padding: 5px 10px; }
    }
  </style>
</head>
<body>

  <div class="confirm-container">
    <div class="hero-banner">
      <div class="success-badge"><i class="fa-solid fa-check"></i></div>
      <h1>Submissions Received!</h1>
      <p>Thank you, <?= htmlspecialchars($app['student_name']) ?>. Your application has been successfully logged.</p>
    </div>

    <div class="content-layout">
      <!-- SIDEBAR WITH PHOTO -->
      <div class="student-sidebar">
        <div class="photo-box">
          <?php if ($photoUrl): ?>
            <img src="<?= $photoUrl ?>" alt="Student Photo">
          <?php else: ?>
            <i class="fa-solid fa-user-graduate"></i>
          <?php endif; ?>
        </div>
        <div class="field-label">Application ID</div>
        <div class="reg-id"><?= sprintf('#%06d', $app['id']) ?></div>
        <p style="font-size: 13px; color: #64748b; margin-top: 15px;">
          <i class="fa-regular fa-calendar-check" style="margin-right: 5px;"></i> Submitted on:<br>
          <strong><?= $submittedDate ?></strong> at <?= $submittedTime ?>
        </p>
      </div>

      <!-- MAIN DETAILS -->
      <div class="student-details">
        
        <div class="info-section">
          <h3 class="section-title"><i class="fa-solid fa-user"></i> Student Information</h3>
          <div class="info-grid">
            <div>
              <p class="field-label">Full Name</p>
              <p class="field-value"><?= htmlspecialchars($app['student_name']) ?></p>
            </div>
            <div>
              <p class="field-label">Date of Birth</p>
              <p class="field-value"><?= date('d M Y', strtotime($app['dob'])) ?></p>
            </div>
            <div>
              <p class="field-label">Gender</p>
              <p class="field-value"><?= htmlspecialchars($app['gender']) ?></p>
            </div>
            <div>
              <p class="field-label">Class Applied</p>
              <p class="field-value"><?= htmlspecialchars($app['class_applied']) ?></p>
            </div>
            <?php if (!empty($app['stream'])): ?>
            <div>
              <p class="field-label">Stream</p>
              <p class="field-value"><?= htmlspecialchars($app['stream']) ?></p>
            </div>
            <?php endif; ?>
            <?php if ($app['previous_school']): ?>
            <div>
              <p class="field-label">Previous School</p>
              <p class="field-value"><?= htmlspecialchars($app['previous_school']) ?></p>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="info-section">
          <h3 class="section-title"><i class="fa-solid fa-users"></i> Parents & Contact</h3>
          <div class="info-grid">
            <div>
              <p class="field-label">Father's Name</p>
              <p class="field-value"><?= htmlspecialchars($app['father_name']) ?></p>
            </div>
            <div>
              <p class="field-label">Mother's Name</p>
              <p class="field-value"><?= htmlspecialchars($app['mother_name']) ?></p>
            </div>
            <div>
              <p class="field-label">Phone Number</p>
              <p class="field-value"><?= htmlspecialchars($app['phone']) ?></p>
            </div>
            <div>
              <p class="field-label">Email Address</p>
              <p class="field-value"><?= htmlspecialchars($app['email']) ?></p>
            </div>
            <div style="grid-column: 1 / -1;">
              <p class="field-label">Full Address</p>
              <p class="field-value"><?= htmlspecialchars($app['address']) ?>, <?= htmlspecialchars($app['city']) ?>, <?= htmlspecialchars($app['state']) ?> - <?= htmlspecialchars($app['pincode']) ?></p>
            </div>
          </div>
        </div>

        <div class="info-section">
          <h3 class="section-title"><i class="fa-solid fa-file-shield"></i> Identity & Documents</h3>
          <div class="docs-list">
            <?php 
            $docs = [
              'photo'         => ['Student Photo',        'fa-user', true],
              'aadhaar_front' => ['Aadhaar Front',       'fa-id-card', true],
              'aadhaar_back'  => ['Aadhaar Back',        'fa-id-card', true],
              'marksheet'     => ['Previous Marksheet',   'fa-file-invoice', false],
            ];
            foreach ($docs as $field => [$label, $icon, $isImg]):
              $hasFile = !empty($app[$field]);
              $url = $hasFile ? uploadUrl($app['id'], $field) : null;
              $mime = $app[$field . '_mime'] ?? '';
              $isImage = strpos($mime, 'image/') === 0;
            ?>
            <div class="doc-item">
              <?php if ($url && $isImage): ?>
                <img src="<?= $url ?>" alt="<?= $label ?>" class="doc-img" loading="eager" />
              <?php elseif ($url): ?>
                <div class="doc-icon-placeholder"><i class="fa-solid fa-file-pdf" style="color:#ef4444;"></i></div>
                <div style="font-size: 10px; color: #64748b;">PDF Document</div>
              <?php else: ?>
                <div class="doc-icon-placeholder text-gray-200"><i class="fa-solid <?= $icon ?>"></i></div>
                <div style="font-size: 10px; color: #94a3b8;">Not Uploaded</div>
              <?php endif; ?>
              <div class="doc-name"><?= $label ?></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
    </div>

    <div class="actions-bar">
      <a href="index.html" class="btn btn-home">
        <i class="fa-solid fa-arrow-left"></i> Back to Home
      </a>
      <div style="display: flex; gap: 10px;">
        <button onclick="window.print()" class="btn btn-print">
          <i class="fa-solid fa-download"></i> Download / Print
        </button>
      </div>
    </div>
  </div>

  <footer style="text-align: center; margin-top: 30px; color: #94a3b8; font-size: 13px;">
    &copy; <?= date('Y') ?> Dr. A.P.J. Abdul Kalam Inter College. All rights reserved.
  </footer>

</body>
</html>
