<?php
include 'includes/db.php';
include 'includes/header.php';

// Hapus OLT jika ada permintaan
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM olts WHERE id = $id");
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            OLT berhasil dihapus!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
}


// Query dengan error handling
$result = $conn->query("
    SELECT 
        o.id,
        o.name,
        o.ip,
        o.port,
        o.community,
        o.version,
        o.vendor,
        COUNT(u.id) as total_onu,
        SUM(CASE WHEN (u.status = 'Online' OR u.status = 'Up') 
                 AND u.rx_power IS NOT NULL 
                 AND u.rx_power != 'N/A'
              THEN 1 ELSE 0 END) as online_onu
    FROM olts o
    LEFT JOIN onus u ON o.id = u.olt_id
    GROUP BY o.id, o.name, o.ip, o.port, o.community, o.version, o.vendor
    ORDER BY o.name
") or die("Query error: " . $conn->error);

// Debug data
//$debugData = $conn->query("SELECT o.id, o.name, u.status, u.rx_power FROM olts o LEFT JOIN onus u ON o.id = u.olt_id");
//echo "<pre>Debug Data: " . print_r($debugData->fetch_all(MYSQLI_ASSOC), true) . "</pre>";

?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-server"></i> Daftar OLT</h5>
                <div>
                    <button id="refreshStatus" class="btn btn-sm btn-primary me-2">
                        <i class="bi bi-arrow-clockwise"></i> Refresh Status
                    </button>
                    <a href="add_olt.php" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah OLT
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="col" data-olt-id="<?= $row['id'] ?>">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-router"></i> <?= htmlspecialchars($row['name']) ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted">IP Address</small>
                        <p class="mb-0"><?= htmlspecialchars($row['ip']) ?></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Port</small>
                        <p class="mb-0"><?= $row['port'] ?></p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted">Vendor</small>
                        <p class="mb-0"><?= ucfirst(htmlspecialchars($row['vendor'])) ?></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">SNMP Version</small>
                        <p class="mb-0"><?= htmlspecialchars($row['version']) ?></p>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <small class="text-muted">Status</small>
                        <div class="olt-status">
                            <span class="badge bg-secondary">
                                <i class="bi bi-question-circle"></i> Checking...
                            </span>
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">Community</small>
                        <p class="mb-0"><?= htmlspecialchars($row['community']) ?></p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-6">
                        <div class="card bg-light">
                            <div class="card-body text-center py-2">
                                <h6 class="card-title mb-0">Total ONU</h6>
                                <p class="mb-0 fs-4"><?= $row['total_onu'] ?: '0' ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-light">
                            <div class="card-body text-center py-2">
                                <h6 class="card-title mb-0">Online</h6>
                                <p class="mb-0 fs-4 <?= $row['online_onu'] > 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $row['online_onu'] ?: '0' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-end gap-2">
                    <a href="edit_olt.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <a href="olt_list.php?delete=<?= $row['id'] ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Yakin ingin menghapus OLT ini?')">
                        <i class="bi bi-trash"></i> Hapus
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    async function checkOltStatus(ip, element) {
        try {
            const response = await fetch(`../api/check_olt_status.php?ip=${encodeURIComponent(ip)}&t=${Date.now()}`);
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            
            element.innerHTML = data.status === 'online' ? 
                `<span class="badge bg-success"><i class="bi bi-check-circle"></i> Online</span>` :
                `<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Offline</span>`;
                
        } catch (error) {
            console.error('Error:', error);
            element.innerHTML = `
                <span class="badge bg-warning text-dark">
                    <i class="bi bi-exclamation-triangle"></i> Error
                </span>`;
        }
    }

    // Perhatikan selector yang diperbaiki
    document.querySelectorAll('.olt-status').forEach(element => {
        const card = element.closest('.card');
        const ip = card.querySelector('.card-body .row .col-6:first-child p').textContent.trim();
        checkOltStatus(ip, element);
    });

    document.getElementById('refreshStatus').addEventListener('click', function() {
        document.querySelectorAll('.olt-status').forEach(element => {
            element.innerHTML = `<span class="badge bg-secondary"><i class="bi bi-arrow-repeat"></i> Checking...</span>`;
            const card = element.closest('.card');
            const ip = card.querySelector('.card-body .row .col-6:first-child p').textContent.trim();
            checkOltStatus(ip, element);
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>