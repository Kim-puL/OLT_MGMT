<?php
include 'includes/db.php';
include 'includes/onu_functions.php';

$result = $conn->query("SELECT * FROM olts");

while ($olt = $result->fetch_assoc()) {
    echo "[" . date('Y-m-d H:i:s') . "] Update OLT: {$olt['name']} ({$olt['ip']})\n";
    $success = updateONUData($olt);
    echo $success ? " → Berhasil\n" : " → Gagal\n";
}
?>