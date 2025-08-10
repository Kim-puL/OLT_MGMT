<?php
include 'includes/db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $ip = $_POST['ip'];
    $port = $_POST['port'];
    $community = $_POST['community'];
    $version = $_POST['version'];
    $vendor = $_POST['vendor'];

    $stmt = $conn->prepare("INSERT INTO olts (name, ip, port, community, version, vendor) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss", $name, $ip, $port, $community, $version, $vendor);
    $stmt->execute();
    
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            OLT berhasil ditambahkan!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
}
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah OLT Baru</h5>
    </div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama OLT</label>
                <input type="text" name="name" required class="form-control" placeholder="Contoh: OLT-Jakarta-01">
            </div>
            <div class="col-md-6">
                <label class="form-label">IP Address</label>
                <input type="text" name="ip" required class="form-control" placeholder="Contoh: 192.168.1.100">
            </div>
            <div class="col-md-3">
                <label class="form-label">Port</label>
                <input type="number" name="port" value="161" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Community</label>
                <input type="text" name="community" value="public" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Version</label>
                <select name="version" class="form-select">
                    <option value="1">1</option>
                    <option value="2c" selected>2c</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Vendor</label>
                <select name="vendor" class="form-select">
                    <option value="hioso">Hioso</option>
                    <option value="hsgq">HSGQ</option>
                    <option value="zte">ZTE</option>
                    <option value="fiberhome">FiberHome</option>
                </select>
            </div>
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <a href="olt_list.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan OLT
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>