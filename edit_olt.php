<?php
include 'includes/db.php';
include 'includes/header.php';

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM olts WHERE id = $id");
$olt = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $ip = $_POST['ip'];
    $port = $_POST['port'];
    $community = $_POST['community'];
    $version = $_POST['version'];
    $vendor = $_POST['vendor'];

    $stmt = $conn->prepare("UPDATE olts SET name=?, ip=?, port=?, community=?, version=?, vendor=? WHERE id=?");
    $stmt->bind_param("ssisssi", $name, $ip, $port, $community, $version, $vendor, $id);
    $stmt->execute();
    
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            OLT berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
}
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit OLT</h5>
    </div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama OLT</label>
                <input type="text" name="name" value="<?= htmlspecialchars($olt['name']) ?>" required class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">IP Address</label>
                <input type="text" name="ip" value="<?= htmlspecialchars($olt['ip']) ?>" required class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Port</label>
                <input type="number" name="port" value="<?= $olt['port'] ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Community</label>
                <input type="text" name="community" value="<?= htmlspecialchars($olt['community']) ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Version</label>
                <select name="version" class="form-select">
                    <option value="1" <?= $olt['version'] == '1' ? 'selected' : '' ?>>1</option>
                    <option value="2c" <?= $olt['version'] == '2c' ? 'selected' : '' ?>>2c</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Vendor</label>
                <select name="vendor" class="form-select">
                    <option value="hioso" <?= $olt['vendor'] == 'hioso' ? 'selected' : '' ?>>Hioso</option>
                    <option value="hsgq" <?= $olt['vendor'] == 'hsgq' ? 'selected' : '' ?>>HSGQ</option>
                    <option value="zte" <?= $olt['vendor'] == 'zte' ? 'selected' : '' ?>>ZTE</option>
                    <option value="fiberhome" <?= $olt['vendor'] == 'fiberhome' ? 'selected' : '' ?>>FiberHome</option>
                </select>
            </div>
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <a href="olt_list.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>