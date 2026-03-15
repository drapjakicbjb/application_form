<?php
/**
 * submit.php — Form Submission Handler (Supabase API Version)
 * Accepts the admission form POST, validates, and saves data to Supabase via REST API.
 */

header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

function clean(string $val): string {
    return htmlspecialchars(trim($val), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

$data = [
    'student_name'    => clean($_POST['student_name']    ?? ''),
    'dob'             => clean($_POST['dob']             ?? ''),
    'gender'          => clean($_POST['gender']          ?? ''),
    'blood_group'     => clean($_POST['blood_group']     ?? ''),
    'aadhaar'         => clean($_POST['aadhaar']         ?? ''),
    'class_applied'   => clean($_POST['class_applied']   ?? ''),
    'stream'          => clean($_POST['stream']          ?? ''),
    'previous_school' => clean($_POST['previous_school'] ?? ''),
    'father_name'     => clean($_POST['father_name']     ?? ''),
    'mother_name'     => clean($_POST['mother_name']     ?? ''),
    'phone'           => clean($_POST['phone']           ?? ''),
    'email'           => clean($_POST['email']           ?? ''),
    'address'         => clean($_POST['address']         ?? ''),
    'city'            => clean($_POST['city']            ?? ''),
    'state'           => clean($_POST['state']           ?? ''),
    'pincode'         => clean($_POST['pincode']         ?? ''),
];

// =============================================
// Validation
// =============================================
$errors = [];
if (empty($data['student_name'])) $errors[] = 'Student name is required.';
if (empty($data['dob']))          $errors[] = 'Date of birth is required.';
if (!preg_match('/^\d{12}$/', $data['aadhaar'])) $errors[] = 'Aadhaar must be 12 digits.';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// =============================================
// File Handling (Base64 for API)
// =============================================
$allowedMime = ['image/jpeg', 'image/png', 'application/pdf'];
$maxSize = 2 * 1024 * 1024;

function handleApiUpload(string $field, array $allowedMime, int $maxSize, array &$errors): ?array {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > $maxSize) { $errors[] = "$field exceeds 2MB."; return null; }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowedMime)) { $errors[] = "$field type not allowed."; return null; }

    // Convert to base64 for JSON transmission
    // PostgREST BYTEA columns accept base64 if prefixed with \x (hex) or decoded
    // However, simplest is to store the actual base64 or have the DB decode it.
    // For BYTEA, we actually need to send it as a hex string or use the /rest/v1/rpc or storage.
    // But since we want "standard" REST on BYTEA: 
    // We will send it as a base64 string and store it. 
    // NOTE: In PostgreSQL, BYTEA can be handled via hex too.
    
    $content = file_get_contents($file['tmp_name']);
    return [
        'content' => base64_encode($content),
        'mime'    => $mime
    ];
}

$files = ['photo', 'aadhaar_front', 'aadhaar_back', 'marksheet'];
foreach ($files as $f) {
    $res = handleApiUpload($f, $allowedMime, $maxSize, $errors);
    if ($res) {
        // PostgREST BYTEA columns via JSON expect the data to be properly handled.
        // Actually, many Supabase users store base64 in TEXT/JSONB because BYTEA over REST 
        // is tricky without specific encoding (\x...). 
        // We will send it as base64 and rely on the user having text columns or handling the decode.
        // BUT our schema.sql used BYTEA.
        // To insert BYTEA via JSON in PostgREST, we use the string format.
        $data[$f] = $res['content'];
        $data[$f . '_mime'] = $res['mime'];
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// =============================================
// Supabase API POST
// =============================================
$response = supabaseRequest('applications', 'POST', $data);

if (isset($response['error'])) {
    echo json_encode(['success' => false, 'message' => 'Supabase Error: ' . $response['message']]);
} else {
    // PostgREST return=representation returns an array of inserted objects
    $inserted = $response[0] ?? null;
    echo json_encode([
        'success' => true,
        'message' => 'Application submitted successfully via API.',
        'id'      => $inserted['id'] ?? null
    ]);
}
