<?php
/**
 * admin/print_application.php — Official Print Version (Supabase API Version)
 */

require_once '../db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid Application ID.");
}

// Fetch application data via API
$response = supabaseRequest("applications?id=eq.{$id}");

if (isset($response['error']) || empty($response)) {
    die("Application not found.");
}

$app = $response[0];

// Handle URLs for images/PDFs
function imgUrl(int $id, string $field): string {
    return "../file_proxy.php?id={$id}&field={$field}";
}

$submittedDate = date('d F Y', strtotime($app['created_at']));
$submittedTime = date('h:i A', strtotime($app['created_at']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Application_<?= $app['id'] ?>_<?= str_replace(' ', '_', $app['student_name']) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    @page { size: A4; margin: 15mm; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.5; color: #333; margin: 0; padding: 0; background: #fff; }
    
    .print-wrapper { max-width: 100%; margin: 0 auto; }
    
    .print-header { display: flex; align-items: flex-start; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
    .school-info { flex: 1; }
    .school-name { font-size: 24px; font-weight: bold; text-transform: uppercase; color: #1a365d; margin: 0; }
    .school-addr { font-size: 13px; color: #666; margin: 5px 0; }
    
    .student-photo {
      width: 110px; height: 140px; border: 1px solid #ddd; object-fit: contain; background: #f9f9f9; padding: 2px;
    }
    .photo-placeholder {
      width: 110px; height: 140px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; background: #fafafa;
    }

    .reg-strip { background: #f1f5f9; padding: 10px 15px; border-radius: 5px; display: flex; justify-content: space-between; margin-bottom: 30px; border: 1px solid #e2e8f0; }
    .reg-id { font-weight: bold; color: #2563eb; }

    .section-title { font-size: 16px; font-weight: bold; background: #f8fafc; padding: 8px 12px; border-left: 4px solid #1a365d; margin: 25px 0 15px; text-transform: uppercase; color: #1a365d; }
    
    .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px 30px; padding: 0 12px; }
    .info-item { margin-bottom: 10px; }
    .field-label { font-size: 11px; text-transform: uppercase; color: #718096; margin-bottom: 2px; }
    .field-value { font-size: 14px; font-weight: 600; color: #2d3748; }

    .docs-section { margin-top: 40px; page-break-before: auto; }
    .docs-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .doc-item { border: 1px solid #eee; padding: 10px; border-radius: 8px; text-align: center; background: #fff; }
    .doc-img { width: 100%; height: 180px; object-fit: contain; margin-bottom: 8px; background: #fdfdfd; }
    .doc-pdf { height: 180px; display: flex; flex-direction: column; items-center; justify-content: center; background: #fff5f5; color: #c53030; border-radius: 5px; }
    .doc-pdf i { font-size: 40px; margin-bottom: 10px; }
    
    .signature-area { margin-top: 60px; display: flex; justify-content: space-between; padding: 0 40px; }
    .sig-box { text-align: center; width: 200px; }
    .sig-line { border-top: 1px solid #333; margin-bottom: 5px; }
    .sig-text { font-size: 12px; color: #666; }

    @media print {
      .no-print { display: none; }
      body { -webkit-print-color-adjust: exact; }
    }
  </style>
</head>
<body>

<div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 100;">
  <button onclick="window.print()" style="padding: 10px 20px; background: #1a365d; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
    <i class="fa-solid fa-print"></i> PRINT NOW
  </button>
</div>

<div class="print-wrapper">
  <!-- Header with photo -->
  <div class="print-header">
    <div class="school-info">
      <h1 class="school-name">Dr. A.P.J. Abdul Kalam Inter College</h1>
      <p class="school-addr">Siswa Bazar, Maharajganj, Uttar Pradesh - 273163</p>
      <p class="school-addr">Email: info@apjschool.edu.in | Help: +91 9123456789</p>
      <div style="margin-top: 15px; display: inline-block; border: 2px solid #1a365d; padding: 5px 15px; font-weight: bold; color: #1a365d;">
        ADMISSION FORM 2024-25
      </div>
    </div>
    
    <?php
    $hasPhoto = !empty($app['photo']);
    $photoUrl = $hasPhoto ? imgUrl($app['id'], 'photo') : '';
    $photoMime = $app['photo_mime'] ?? '';
    $isPhotoImg = strpos($photoMime, 'image/') === 0;
    ?>
    <?php if ($photoUrl && $isPhotoImg): ?>
      <img src="<?= $photoUrl ?>" alt="Student Photo" class="student-photo" />
    <?php else: ?>
      <div class="photo-placeholder"><i class="fa-solid fa-user text-4xl text-slate-200"></i></div>
    <?php endif; ?>
  </div>

  <div class="reg-strip">
    <div>Registration ID: <span class="reg-id">#<?= sprintf('%06d', $app['id']) ?></span></div>
    <div>Date: <strong><?= $submittedDate ?></strong></div>
  </div>

  <div class="section-title text-indigo-900">1. Student Details</div>
  <div class="info-grid">
    <div class="info-item"><p class="field-label">Student Name</p><p class="field-value"><?= htmlspecialchars($app['student_name']) ?></p></div>
    <div class="info-item"><p class="field-label">Date of Birth</p><p class="field-value"><?= date('d F Y', strtotime($app['dob'])) ?></p></div>
    <div class="info-item"><p class="field-label">Gender</p><p class="field-value"><?= htmlspecialchars($app['gender']) ?></p></div>
    <div class="info-item"><p class="field-label">Blood Group</p><p class="field-value"><?= htmlspecialchars($app['blood_group'] ?: '—') ?></p></div>
    <div class="info-item"><p class="field-label">Aadhaar Number</p><p class="field-value">XXXX XXXX <?= substr(htmlspecialchars($app['aadhaar']), -4) ?></p></div>
    <div class="info-item"><p class="field-label">Class Applied</p><p class="field-value"><?= htmlspecialchars($app['class_applied']) ?></p></div>
    <?php if ($app['stream']): ?>
    <div class="info-item"><p class="field-label">Stream</p><p class="field-value"><?= htmlspecialchars($app['stream']) ?></p></div>
    <?php endif; ?>
    <?php if ($app['previous_school']): ?>
    <div class="info-item"><p class="field-label">Previous School</p><p class="field-value"><?= htmlspecialchars($app['previous_school']) ?></p></div>
    <?php endif; ?>
  </div>

  <div class="section-title">2. Parent & Contact Details</div>
  <div class="info-grid">
    <div class="info-item"><p class="field-label">Father's Name</p><p class="field-value"><?= htmlspecialchars($app['father_name']) ?></p></div>
    <div class="info-item"><p class="field-label">Mother's Name</p><p class="field-value"><?= htmlspecialchars($app['mother_name']) ?></p></div>
    <div class="info-item"><p class="field-label">Mobile Number</p><p class="field-value"><?= htmlspecialchars($app['phone']) ?></p></div>
    <div class="info-item"><p class="field-label">Email Address</p><p class="field-value"><?= htmlspecialchars($app['email'] ?: '—') ?></p></div>
    <div class="info-item" style="grid-column: span 2;"><p class="field-label">Full Address</p><p class="field-value"><?= htmlspecialchars($app['address']) ?>, <?= htmlspecialchars($app['city']) ?>, <?= htmlspecialchars($app['state']) ?> - <?= htmlspecialchars($app['pincode']) ?></p></div>
  </div>

  <div class="section-title">3. Documents Submitted</div>
  <div class="docs-grid">
    <?php
    $docFields = [
        'aadhaar_front' => 'Aadhaar Card (Front)',
        'aadhaar_back'  => 'Aadhaar Card (Back)',
        'marksheet'     => 'Marksheet',
    ];
    foreach ($docFields as $field => $label):
        $hasFile = !empty($app[$field]);
        $url  = $hasFile ? imgUrl($app['id'], $field) : '';
        $mime = $app[$field . '_mime'] ?? '';
        $isImg = strpos($mime, 'image/') === 0;
    ?>
    <div class="doc-item">
      <?php if ($url && $isImg): ?>
        <img src="<?= htmlspecialchars($url) ?>" alt="<?= $label ?>" class="doc-img" />
      <?php elseif ($url): ?>
        <div class="doc-pdf"><i class="fa-solid fa-file-pdf"></i>PDF DOCUMENT</div>
      <?php else: ?>
        <div class="doc-pdf" style="background:#f1f5f9; color:#94a3b8; border: 1px dashed #ccc;">
          <i class="fa-solid fa-file-circle-xmark"></i> NOT SUBMITTED
        </div>
      <?php endif; ?>
      <p style="font-size: 12px; font-weight: bold; margin-top: 5px;"><?= $label ?></p>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="signature-area">
    <div class="sig-box"><div class="sig-line"></div><p class="sig-text">Student Signature</p></div>
    <div class="sig-box"><div class="sig-line"></div><p class="sig-text">Guardian Signature</p></div>
    <div class="sig-box"><div class="sig-line"></div><p class="sig-text">Principal Signature</p></div>
  </div>

  <div style="margin-top: 40px; font-size: 10px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 10px;">
    This is a computer-generated document. Generated on <?= date('d-m-Y h:i A') ?>.
  </div>
</div>

<script>
  window.onload = function() {
    // Optional: auto-trigger print after images load
    // setTimeout(() => window.print(), 1000);
  }
</script>

</body>
</html>
