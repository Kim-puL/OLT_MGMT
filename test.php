<?php
// Form untuk input SNMP info dan SNMP set
echo '
<!DOCTYPE html>
<html>
<head>
    <title>SNMP Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">🔧 SNMP Fetch & Set</h2>
    <form method="post">
        <div class="row g-3">
            <div class="col-md-3">
                <label>IP OLT</label>
                <input type="text" name="ip" class="form-control" placeholder="192.168.1.1" required>
            </div>
            <div class="col-md-3">
                <label>Community</label>
                <input type="text" name="community" class="form-control" placeholder="public" required>
            </div>
            <div class="col-md-2">
                <label>Version</label>
                <select name="version" class="form-control">
                    <option value="2c">2c</option>
                    <option value="1">1</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>OID</label>
                <input type="text" name="oid" class="form-control" required>
            </div>
        </div>

        <hr>
        <h5>📝 SNMP SET (Opsional)</h5>
        <div class="row g-3">
            <div class="col-md-3">
                <label>Tipe (i, s, x, a, o)</label>
                <input type="text" name="set_type" class="form-control" placeholder="Contoh: i">
            </div>
            <div class="col-md-6">
                <label>Nilai</label>
                <input type="text" name="set_value" class="form-control" placeholder="Contoh: 1">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" name="submit" class="btn btn-primary w-100">Jalankan SNMP</button>
            </div>
        </div>
    </form>
    <hr>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_POST['ip'];
    $community = $_POST['community'];
    $version = $_POST['version'];
    $oid = $_POST['oid'];
    $setType = $_POST['set_type'];
    $setValue = $_POST['set_value'];

    echo "<h5>🖥️ Debug Info</h5><pre>";
    echo "IP: $ip\n";
    echo "Community: $community\n";
    echo "Version: $version\n";
    echo "OID: $oid\n";
    echo "</pre>";

    // Jalankan SNMP Walk (GET)
    $result = @snmp2_real_walk($ip, $community, $oid, 1000000, 2);

    if ($result !== false) {
        echo "<h5>📦 Hasil SNMP:</h5>";
        echo "<table class='table table-bordered table-sm'><thead><tr><th>OID</th><th>Value</th></tr></thead><tbody>";
        foreach ($result as $oid => $val) {
            echo "<tr><td>$oid</td><td>$val</td></tr>";
        }
        echo "</tbody></table>";
        echo "<p class='text-success'>✅ Berhasil mengambil data SNMP.</p>";
    } else {
        echo "<p class='text-danger'>❌ Gagal mengambil data SNMP.</p>";
    }

    // Jalankan SNMP SET jika ada input
    if (!empty($setType) && !empty($setValue)) {
        echo "<h5>🛠️ SNMP SET Result:</h5>";
        $setResult = @snmp2_set($ip, $community, $oid, $setType, $setValue);
        if ($setResult) {
            echo "<p class='text-success'>✅ SNMP SET berhasil untuk OID $oid ($setType = $setValue)</p>";
        } else {
            echo "<p class='text-danger'>❌ Gagal melakukan SNMP SET. Periksa community atau hak akses write.</p>";
        }
    }
}

echo '</div></body></html>';
?>
