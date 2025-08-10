<?php
include 'db.php';

function getOIDMapping($vendor) {
    $stmt = $GLOBALS['conn']->prepare("SELECT oid_name, oid_tx, oid_rx, oid_status FROM oid_mappings WHERE vendor = ?");
    $stmt->bind_param("s", $vendor);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return [
            'name'   => $row['oid_name'],
            'tx'     => $row['oid_tx'],
            'rx'     => $row['oid_rx'],
            'status' => $row['oid_status']
        ];
    }
    return null;
}

function updateONUData($olt) {
    $ip = $olt['ip'];
    $community = $olt['community'];
    $port = (int)$olt['port'];
    $version = $olt['version'];
    $vendor = $olt['vendor'];
    $oids = getOIDMapping($vendor);

    if (!$oids) return false;

    $names = @snmp2_walk($ip, $community, $oids['name'], 1000000, 2);
    $txs   = @snmp2_walk($ip, $community, $oids['tx'],   1000000, 2);
    $rxs   = @snmp2_walk($ip, $community, $oids['rx'],   1000000, 2);
    $statusList = @snmp2_walk($ip, $community, $oids['status'], 1000000, 2);

    if (!$names || !$txs || !$rxs) {
        return false;
    }

    $txMap = [];
    foreach ($txs as $oid => $value) {
        $index = str_replace($oids['tx'] . ".", '', $oid);
        $txMap[$index] = $value;
    }

    $rxMap = [];
    foreach ($rxs as $oid => $value) {
        $index = str_replace($oids['rx'] . ".", '', $oid);
        $rxMap[$index] = $value;
    }

    $statusMap = [];
    foreach ($statusList as $oid => $value) {
        $index = str_replace($oids['status'] . ".", '', $oid);
        if (preg_match('/INTEGER: (\d+)/', $value, $match)) {
            $statusMap[$index] = ($match[1] == 1) ? 'Up' : 'Down';
        } else {
            $statusMap[$index] = 'Unknown';
        }
    }

    $stmt = $GLOBALS['conn']->prepare("DELETE FROM onus WHERE olt_id = ?");
    $stmt->bind_param("i", $olt['id']);
    $stmt->execute();

    foreach ($names as $oid => $nameValue) {
        $index = str_replace($oids['name'] . ".", '', $oid);
        $name = trim(str_replace(['STRING:', '"'], '', $nameValue));

        $txRaw = $txMap[$index] ?? 'N/A';
        $rxRaw = $rxMap[$index] ?? 'N/A';
        $status = $statusMap[$index] ?? 'Unknown';

        if ($vendor === 'hioso') {
            $tx = preg_match('/"([-+]?[0-9]*\.?[0-9]+)"/', $txRaw, $txMatch) ? $txMatch[1] : 'N/A';
            $rx = preg_match('/"([-+]?[0-9]*\.?[0-9]+)"/', $rxRaw, $rxMatch) ? $rxMatch[1] : 'N/A';
        } else {
            $tx = preg_match('/-?\d+/', $txRaw, $txMatch) ? number_format($txMatch[0] / 100.0, 4) : 'N/A';
            $rx = preg_match('/-?\d+/', $rxRaw, $rxMatch) ? number_format($rxMatch[0] / 100.0, 4) : 'N/A';
        }

        $stmt = $GLOBALS['conn']->prepare("INSERT INTO onus (olt_id, name, tx_power, rx_power, status, last_updated) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("issss", $olt['id'], $name, $tx, $rx, $status);
        $stmt->execute();
    }

    return true;
}
?>