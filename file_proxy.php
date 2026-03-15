<?php
/**
 * file_proxy.php — Serve files from Supabase via API
 */

require_once 'db.php';

$id    = (int)($_GET['id'] ?? 0);
$field = $_GET['field'] ?? '';

$allowedFields = ['photo', 'aadhaar_front', 'aadhaar_back', 'marksheet'];

if ($id <= 0 || !in_array($field, $allowedFields)) {
    http_response_code(400);
    exit('Invalid request.');
}

// Request specific columns from Supabase
$response = supabaseRequest("applications?id=eq.{$id}&select={$field},{$field}_mime");

if (isset($response['error']) || empty($response)) {
    http_response_code(404);
    exit('File not found or API error.');
}

$app = $response[0] ?? null;

if (!$app || empty($app[$field])) {
    http_response_code(404);
    exit('File content empty.');
}

// We stored files as base64 in submit.php
$content = base64_decode($app[$field]);
$mime    = $app["{$field}_mime"] ?: 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . strlen($content));
header('Cache-Control: public, max-age=3600');

echo $content;
