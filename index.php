<?php
include 'includes/db.php';
include 'includes/header.php';

// Hitung total OLT (tanpa status karena kolom tidak ada)
$oltCount = $conn->query("SELECT COUNT(*) as count FROM olts")->fetch_assoc()['count'];

// Karena tidak ada status OLT, kita asumsikan semua OLT online
$oltOnline = $oltCount;
$oltOffline = 0;

// Hitung ONU Online dan Offline
$onlineCount = $conn->query("SELECT COUNT(*) AS total FROM onus WHERE status = 'Up'")->fetch_assoc()['total'];
$offlineCount = $conn->query("SELECT COUNT(*) AS total FROM onus WHERE status = 'Down'")->fetch_assoc()['total'];
$totalCount = $onlineCount + $offlineCount;

?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="bi bi-speedometer2"></i> Dashboard Overview</h5>
    </div>
    <div class="card-body">

    <div class="alert alert-secondary">
        <p class="lead mb-2">Selamat datang di Sistem Monitoring OLT</p>
        <p class="mb-0">Gunakan menu navigasi di atas untuk mengelola OLT dan ONU.</p>
        <div id="alerts-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1100"></div>
    </div>

        <div class="row">
            <!-- OLT Overview -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-server"></i> OLT Overview</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <h3><?= $oltCount ?></h3>
                                <small class="text-muted">Total</small>
                            </div>
                            <div class="col-4">
                                <h3 class="text-success"><?= $oltOnline ?></h3>
                                <small class="text-muted">Online</small>
                            </div>
                            <div class="col-4">
                                <h3 class="text-danger"><?= $oltOffline ?></h3>
                                <small class="text-muted">Offline</small>
                            </div>
                        </div>
                        <a href="olt_list.php" class="btn btn-primary btn-sm mt-3 w-100">
                            <i class="bi bi-list-ul"></i> View All OLTs
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- ONU Overview -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-hdd-network"></i> ONU Overview</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <h3><?= $totalCount ?></h3>
                                <small class="text-muted">Total</small>
                            </div>
                            <div class="col-4">
                                <h3 class="text-success"><?= $onlineCount ?></h3>
                                <small class="text-muted">Online</small>
                            </div>
                            <div class="col-4">
                                <h3 class="text-danger"><?= $offlineCount ?></h3>
                                <small class="text-muted">Offline</small>
                            </div>
                        </div>
                        <a href="onu_list.php" class="btn btn-info btn-sm mt-3 w-100">
                            <i class="bi bi-list-ul"></i> View All ONUs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Recently Updated OLTs</h5>
        <button id="refreshAllStatus" class="btn btn-sm btn-primary">
            <i class="bi bi-arrow-repeat"></i> Refresh All
        </button>
    </div>

    <div id="update-result" class="mt-3"></div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="oltStatusTable">
                <thead>
                    <tr>
                        <th>OLT Name</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>ONUs</th>
                        <th>Last Update</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $oltUpdates = $conn->query("
                        SELECT 
                            o.id,
                            o.name,
                            o.ip,
                            COUNT(u.id) as onu_count,
                            MAX(u.last_updated) as last_update
                        FROM olts o
                        LEFT JOIN onus u ON o.id = u.olt_id
                        GROUP BY o.id, o.name, o.ip
                        ORDER BY COALESCE(MAX(u.last_updated), '1970-01-01') DESC
                        LIMIT 5
                    ");
                    
                    while ($olt = $oltUpdates->fetch_assoc()):
                    ?>
                    <tr data-olt-id="<?= $olt['id'] ?>" data-ip="<?= htmlspecialchars($olt['ip']) ?>">
                        <td><?= htmlspecialchars($olt['name']) ?></td>
                        <td><?= htmlspecialchars($olt['ip']) ?></td>
                        <td class="olt-status">
                            <span class="badge bg-secondary">
                                <i class="bi bi-arrow-repeat"></i> Checking...
                            </span>
                        </td>
                        <td><?= $olt['onu_count'] ?></td>
                        <td>
                            <?php if ($olt['last_update']): ?>
                                <?= date('d M Y H:i', strtotime($olt['last_update'])) ?>
                            <?php else: ?>
                                Never updated
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="onu_list.php?olt_id=<?= $olt['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="#" class="btn btn-sm btn-warning update-onu" data-id="<?= $olt['id'] ?>">
                                    <i class="bi bi-arrow-up-circle"></i> Update
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk check status via API
    async function checkOltStatus(ip, element) {
        try {
            element.innerHTML = `
                <span class="badge bg-secondary">
                    <i class="bi bi-arrow-repeat"></i> Checking...
                </span>
            `;
            
            const response = await fetch(`api/check_olt_ping.php?ip=${encodeURIComponent(ip)}&t=${Date.now()}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.status === 'online') {
                element.innerHTML = `
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle"></i> Online
                    </span>
                `;
            } else {
                element.innerHTML = `
                    <span class="badge bg-danger">
                        <i class="bi bi-x-circle"></i> Offline
                    </span>
                `;
            }
        } catch (error) {
            console.error('Error checking OLT status:', error);
            element.innerHTML = `
                <span class="badge bg-warning text-dark">
                    <i class="bi bi-exclamation-triangle"></i> Error
                </span>
            `;
        }
    }

    // Check status semua OLT
    function refreshAllStatus() {
        document.querySelectorAll('#oltStatusTable tbody tr').forEach(row => {
            const ip = row.getAttribute('data-ip');
            const statusElement = row.querySelector('.olt-status');
            checkOltStatus(ip, statusElement);
        });
    }

    // Jalankan pertama kali
    refreshAllStatus();

    // Button refresh manual
    document.getElementById('refreshAllStatus').addEventListener('click', refreshAllStatus);

    // Auto-refresh setiap 2 menit
    setInterval(refreshAllStatus, 120000);
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- AJAX Script -->
<script>
$(document).ready(function() {
    $('.update-onu').click(function(e) {
        e.preventDefault();
        let oltId = $(this).data('id');

        $('#update-result').html('<div class="alert alert-info">Memperbarui data ONU...</div>');

        $.get('onu_snmp_update.php', { olt_id: oltId }, function(response) {
            $('#update-result').html(response);
        }).fail(function() {
            $('#update-result').html('<div class="alert alert-danger">Gagal menghubungi server.</div>');
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>