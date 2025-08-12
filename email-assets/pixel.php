<?php

// Detect device or client from user agent string
function detectDeviceOrClient($ua) {
    $ua = strtolower($ua);
    if (strpos($ua, 'iphone') !== false) return 'iPhone';
    if (strpos($ua, 'ipad') !== false) return 'iPad';
    if (strpos($ua, 'android') !== false) return 'Android';
    if (strpos($ua, 'macintosh') !== false) return 'Mac';
    if (strpos($ua, 'windows') !== false) return 'Windows';
    if (strpos($ua, 'outlook') !== false) return 'Outlook';
    if (strpos($ua, 'gmail') !== false) return 'Gmail';
    if (strpos($ua, 'thunderbird') !== false) return 'Thunderbird';
    if (strpos($ua, 'applewebkit') !== false && strpos($ua, 'mail') !== false) return 'Apple Mail';
    return 'Other';
}

// Get incoming data
$uid = isset($_GET['uid']) ? $_GET['uid'] : 'unknown';
$ip = $_SERVER['REMOTE_ADDR'];
$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$device = detectDeviceOrClient($ua);

// GeoIP lookup (ipinfo.io free tier)
$city = $region = $country = $org = '';
$geo = @json_decode(@file_get_contents("https://ipinfo.io/{$ip}/json"), true);
if ($geo) {
    $city = isset($geo['city']) ? $geo['city'] : '';
    $region = isset($geo['region']) ? $geo['region'] : '';
    $country = isset($geo['country']) ? $geo['country'] : '';
    $org = isset($geo['org']) ? $geo['org'] : '';
}

// Replace with your own secret!
$secret_token = 'skf83b4f7mNa!FjS';

// Google Apps Script endpoint (replace this with YOURS!)
$google_script_url = 'https://script.google.com/macros/s/AKfycbz2SKqSUbz-wbYpT8rO9gsMNej0zK5WaAWoCdKx4e9rZ_nNXteZGiuQ3ZKuG9ARaZx-zA/exec';

// ISO 8601 UTC format, e.g., 2025-07-15T23:45:30Z
date_default_timezone_set('UTC');
$timestamp = gmdate('Y-m-d\TH:i:s\Z');

// Build params in correct order
$params = http_build_query([
    'timestamp' => $timestamp,
    'uid' => $uid,
    'ip' => $ip,
    'city' => $city,
    'region' => $region,
    'country' => $country,
    'org' => $org,
    'device' => $device, // should be "Mac", "Windows", etc.
    'ua' => $ua,         // the full user agent string
    'token' => $secret_token
]);

// Fire and forget: send to Google Sheet (don't wait for response)
@file_get_contents($google_script_url . '?' . $params);

// Return 1x1 transparent GIF
header('Content-Type: image/gif');
header('Cache-Control: no-store, no-cache, must-revalidate, proxy-revalidate');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==');
exit;
?>