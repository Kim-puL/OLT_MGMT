<?php
include 'includes/db.php';
include 'includes/header.php';

// Get all OLTs for filter dropdown
$olts = $conn->query("SELECT * FROM olts ORDER BY name");

// Get filter parameters
$selectedOltId = $_GET['olt_id'] ?? null;
$statusFilter = $_GET['status'] ?? null;
$searchQuery = $_GET['search'] ?? null;

// Base query to get all ONUs with OLT information
$query = "SELECT onus.*, olts.name as olt_name, olts.ip as olt_ip 
          FROM onus 
          LEFT JOIN olts ON onus.olt_id = olts.id 
          WHERE 1=1";

$params = [];
$types = '';

// Apply filters if they exist
if ($selectedOltId) {
    $query .= " AND onus.olt_id = ?";
    $params[] = $selectedOltId;
    $types .= 'i';
}

if ($statusFilter) {
    if ($statusFilter === 'Online') {
        $query .= " AND onus.status IN ('up', 'Online')";
    } elseif ($statusFilter === 'Offline') {
        $query .= " AND onus.status IN ('down', 'Offline')";
    }
}

if ($searchQuery) {
    $query .= " AND onus.name LIKE ?";
    $searchParam = "%$searchQuery%";
    $params[] = $searchParam;
    $types .= 's';
}

$query .= " ORDER BY olts.name, onus.name";

// Prepare and execute the query
$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$onus = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-hdd-network"></i> Manajemen ONU</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-12">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Filter OLT</label>
                        <select name="olt_id" class="form-select">
                            <option value="">Semua OLT</option>
                            <?php while ($olt = $olts->fetch_assoc()): ?>
                                <option value="<?= $olt['id'] ?>" <?= $selectedOltId == $olt['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($olt['name']) ?> (<?= htmlspecialchars($olt['ip']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="Online" <?= $statusFilter == 'Online' ? 'selected' : '' ?>>Online</option>
                            <option value="Offline" <?= $statusFilter == 'Offline' ? 'selected' : '' ?>>Offline</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pencarian</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama/serial ONU..." value="<?= htmlspecialchars($searchQuery ?? '') ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="onu_list.php" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div id="update-result" class="mt-3"></div>

        <?php if (!empty($onus)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>OLT</th>
                            <th>Nama ONU</th>
                            <th>Serial Number</th>
                            <th>TX Power (dBm)</th>
                            <th>RX Power (dBm)</th>
                            <th>Status</th>
                            <th>Terakhir Update</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($onus as $onu): 
                            $rxClass = 'power-good';
                            $rxValue = floatval(str_replace('N/A', '0', $onu['rx_power']));
                            
                            if ($rxValue < -27) $rxClass = 'power-danger';
                            elseif ($rxValue < -25) $rxClass = 'power-warning';
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($onu['olt_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($onu['name']) ?></td>
                                <td><?= htmlspecialchars($onu['serial_number'] ?? 'N/A') ?></td>
                                <td class="power-value"><?= $onu['tx_power'] ?></td>
                                <td class="power-value <?= $rxClass ?>"><?= $onu['rx_power'] ?></td>
                                <td>
                                    <span class="status-badge status-<?= (strtolower($onu['status']) === 'up' || strtolower($onu['status']) === 'online') ? 'online' : 'offline' ?>">
                                        <?= (strtolower($onu['status']) === 'up' ? 'Online' : (strtolower($onu['status']) === 'down' ? 'Offline' : $onu['status'])) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y H:i', strtotime($onu['last_updated'])) ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-warning update-single" 
                                       data-id="<?= $onu['id'] ?>" title="Update">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                Tidak ada data ONU ditemukan dengan filter yang dipilih.
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- AJAX Script -->
<script>
$(document).ready(function() {
    // Update all ONUs for a specific OLT
    $('.update-onu').click(function(e) {
        e.preventDefault();
        let oltId = $(this).data('id');

        $('#update-result').html('<div class="alert alert-info">Memperbarui data ONU...</div>');

        $.get('onu_snmp_update.php', { olt_id: oltId }, function(response) {
            $('#update-result').html(response);
            // Reload the page after 2 seconds to show updated data
            setTimeout(() => location.reload(), 2000);
        }).fail(function() {
            $('#update-result').html('<div class="alert alert-danger">Gagal menghubungi server.</div>');
        });
    });

    // Update single ONU
    $('.update-single').click(function(e) {
        e.preventDefault();
        let onuId = $(this).data('id');
        let row = $(this).closest('tr');
        
        row.find('td').css('opacity', '0.6');
        $(this).html('<i class="bi bi-hourglass"></i>');

        $.get('onu_snmp_update.php', { onu_id: onuId }, function(response) {
            $('#update-result').html(response);
            // Reload the page after 1 second to show updated data
            setTimeout(() => location.reload(), 1000);
        }).fail(function() {
            $('#update-result').html('<div class="alert alert-danger">Gagal memperbarui ONU.</div>');
            row.find('td').css('opacity', '1');
            $(this).html('<i class="bi bi-arrow-repeat"></i>');
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>