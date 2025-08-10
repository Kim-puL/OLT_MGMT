<?php
include 'includes/db.php';
include 'includes/onu_functions.php';

// Jalankan update jika parameter diberikan
if (isset($_GET['olt_id'])) {
    $id = intval($_GET['olt_id']);
    $result = $conn->query("SELECT * FROM olts WHERE id = $id");
    $olt = $result->fetch_assoc();

    if ($olt) {
        $success = updateONUData($olt);
        if ($success) {
            echo '<div class="alert alert-success">Data ONU berhasil diperbarui. Halaman akan di-refresh...</div>
            <script>
                setTimeout(function() {
                    window.location.href = "onu_list.php?olt_id=' . $id . '";
                }, 2000); // 2 detik
            </script>';
        } else {
            echo '<div class="alert alert-danger">Gagal mengambil data SNMP dari OLT.</div>';
        }
    } else {
        echo '<div class="alert alert-warning">OLT tidak ditemukan.</div>';
    }
} else {
    echo '<div class="alert alert-info">Parameter OLT ID tidak ditemukan.</div>';
}
?>