<?php
/**
 * admin/delete_application.php — Deletes one application record (Supabase API Version)
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: view_applications.php?msg=invalid');
    exit;
}

// Perform DELETE via Supabase API
$response = supabaseRequest("applications?id=eq.{$id}", 'DELETE');

if (isset($response['error'])) {
    error_log('Delete API Error: ' . $response['message']);
    header('Location: view_applications.php?msg=error');
} else {
    header('Location: view_applications.php?msg=deleted');
}
exit;
