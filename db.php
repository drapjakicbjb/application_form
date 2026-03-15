<?php
/**
 * db.php — Supabase REST API Client
 * Replaces direct PDO with cURL requests to the Supabase PostgREST API.
 */

// --- SUPABASE API CONFIGURATION ---
// Get these from Settings > API in your Supabase Dashboard
define('SUPABASE_URL', 'https://jqvgwrodaiflbypltjdy.supabase.co');
define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Impxdmd3cm9kYWlmbGJ5cGx0amR5Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzM1ODU0NzgsImV4cCI6MjA4OTE2MTQ3OH0.1LuaunIGVMXP6Lp4D9Kw9ye0-Rgeo_SW_ZFKCr2PoUY'); // Replace with your 'anon' public key

/**
 * Sends a request to the Supabase REST API.
 */
function supabaseRequest(string $path, string $method = 'GET', ?array $data = null, array $extraHeaders = []): ?array {
    $url = rtrim(SUPABASE_URL, '/') . '/rest/v1/' . ltrim($path, '/');
    
    $ch = curl_init($url);
    
    $headers = [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY,
        'Content-Type: application/json',
        'Prefer: return=representation' // Useful for getting inserted data back
    ];
    
    $headers = array_merge($headers, $extraHeaders);
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    // For local dev with XAMPP sometimes SSL verification fails
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => true, 'message' => 'CURL Error: ' . $error];
    }
    
    $decoded = json_decode($response, true);
    
    if ($httpCode >= 400) {
        return [
            'error' => true, 
            'http_code' => $httpCode, 
            'message' => $decoded['message'] ?? 'API Error',
            'details' => $decoded
        ];
    }
    
    return $decoded;
}
