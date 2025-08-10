<?php
header('Content-Type: application/json');

if (!isset($_GET['ip'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'IP address required']);
    exit;
}

$ip = $_GET['ip'];
$online = false;

// Security: Validasi format IP
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid IP format']);
    exit;
}

// Cek koneksi dengan timeout 3 detik
$socket = @fsockopen($ip, 80, $errno, $errstr, 3);
if ($socket) {
    $online = true;
    fclose($socket);
}

// Alternatif jika fsockopen tidak bekerja
if (!$online) {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $ping = exec("ping -n 2 -w 3000 " . escapeshellarg($ip), $output, $status);
    } else {
        $ping = exec("ping -c 2 -W 3 " . escapeshellarg($ip), $output, $status);
    }
    $online = ($status === 0);
}

echo json_encode([
    'status' => $online ? 'online' : 'offline',
    'ip' => $ip,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>