<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring OLT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            background-color: #f5f5f5;
            padding-top: 56px;
        }
        
        .navbar {
            background-color: var(--primary) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            border: none;
        }
        
        .card-header {
            background-color: var(--secondary);
            color: white;
            border-radius: 8px 8px 0 0 !important;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .table th {
            background-color: var(--primary);
            color: white;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-online {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }
        
        .status-offline {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }
        
        .power-value {
            font-weight: 600;
        }
        
        .power-good {
            color: var(--success);
        }
        
        .power-warning {
            color: var(--warning);
        }
        
        .power-danger {
            color: var(--danger);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-router"></i> OLT Monitoring
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="olt_list.php"><i class="bi bi-server"></i> Daftar OLT</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="onu_list.php"><i class="bi bi-hdd-network"></i> Manajemen ONU</a>
                </li>
            </ul>
            <div class="d-flex">
                <span class="navbar-text text-white">
                    <i class="bi bi-calendar-check"></i> <?= date('d M Y') ?>
                </span>
            </div>
        </div>
    </div>
</nav>
<div class="container mt-4">