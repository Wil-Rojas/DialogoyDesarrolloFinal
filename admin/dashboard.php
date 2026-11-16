<?php
// admin/dashboard.php
session_start();
require_once __DIR__ . '/../config/database.php';

// --- FORZAR SESIÓN PARA ENTRAR (SOLO LOCAL) ---
$_SESSION['usuario_id'] = 1;
$_SESSION['usuario_nombre'] = 'Admin';
$_SESSION['usuario_rol'] = 'admin';
// ----------------------------------------------

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$stats = getEstadisticas($pdo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cabin:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-red: #e0020d;
            --dark-bg: #1a1a2e;
            --light-bg: #f5f6fa;
            --text-dark: #2c3e50;
            --text-muted: #8392a5;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Cabin', 'Segoe UI', sans-serif;
            background: var(--light-bg);
            color: var(--text-dark);
        }
        
        /* SIDEBAR OSCURO MODERNO */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: var(--dark-bg);
            position: fixed;
            top: 0;
            left: 0;
            padding: 25px 0;
            overflow-y: auto;
            z-index: 100;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .logo-container {
            padding: 0 25px 20px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
            text-align: center;
        }
        
        .sidebar .logo-container img { max-width: 120px; height: auto; }
        
        .sidebar .user-info {
            padding: 15px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
        }
        
        .sidebar .user-info .name { font-size: 15px; font-weight: 600; color: #ffffff; }
        .sidebar .user-info .role { font-size: 12px; color: var(--primary-red); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; font-weight: 600; }
        
        .sidebar .nav-link {
            color: #a0aec0;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: all 0.25s ease;
            border-left: 4px solid transparent;
            font-size: 14px;
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover { background: rgba(255, 255, 255, 0.05); color: #ffffff; border-left-color: var(--primary-red); }
        .sidebar .nav-link.active { background: rgba(224, 2, 13, 0.1); color: var(--primary-red); border-left-color: var(--primary-red); font-weight: 600; }
        .sidebar .nav-link i { width: 20px; text-align: center; font-size: 16px; }
        .sidebar .nav-link .badge { background: var(--primary-red); color: #ffffff; padding: 2px 10px; border-radius: 20px; font-size: 11px; margin-left: auto; font-weight: 600; }
        
        /* MAIN CONTENT */
        .main-content {
            margin-left: 260px;
            padding: 40px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* TOP BAR / HEADER */
        .top-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .top-header h2 { font-size: 28px; font-weight: 700; color: var(--text-dark); margin: 0; }
        .top-header p { color: var(--text-muted); margin: 5px 0 0 0; font-size: 14px; }
        
        /* STATS CARDS */
        .row-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .stat-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid #e1e5eb;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--primary-red); }
        .stat-card .number { font-size: 36px; font-weight: 700; color: var(--text-dark); line-height: 1; margin-bottom: 10px; }
        .stat-card .label { color: var(--text-muted); font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-card .icon { position: absolute; right: 20px; top: 20px; font-size: 40px; opacity: 0.1; color: var(--text-dark); }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .sidebar .logo-container img { max-width: 40px; }
            .sidebar .user-info { display: none; }
            .sidebar .nav-link { padding: 15px; justify-content: center; }
            .sidebar .nav-link span { display: none; }
            .sidebar .nav-link .badge { display: none; }
            .main-content { margin-left: 70px; padding: 20px; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="../assets/images/iconos/logo.png" alt="Diálogo y Desarrollo">
        </div>
        
        <div class="user-info">
            <div class="name"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador') ?></div>
            <div class="role"><?= htmlspecialchars($_SESSION['usuario_rol'] ?? 'admin') ?></div>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link active" href="dashboard.php">
                <i class="fas fa-chart-pie"></i> <span>Dashboard</span>
            </a>
            <a class="nav-link" href="modules/reportajes/">
                <i class="fas fa-file-alt"></i> <span>Reportajes</span>
                <span class="badge"><?= $stats['reportajes'] ?? 0 ?></span>
            </a>
            <a class="nav-link" href="modules/noticias/">
                <i class="fas fa-newspaper"></i> <span>Noticias</span>
                <span class="badge"><?= $stats['noticias'] ?? 0 ?></span>
            </a>
            <a class="nav-link" href="modules/boletines/">
                <i class="fas fa-file-pdf"></i> <span>Boletines</span>
                <span class="badge"><?= $stats['boletines'] ?? 0 ?></span>
            </a>
            <a class="nav-link" href="modules/podcasts/">
                <i class="fas fa-microphone"></i> <span>Podcasts</span>
                <span class="badge"><?= $stats['podcasts'] ?? 0 ?></span>
            </a>
            <a class="nav-link" href="modules/videos/">
                <i class="fas fa-video"></i> <span>Videos</span>
                <span class="badge"><?= $stats['videos'] ?? 0 ?></span>
            </a>
            <a class="nav-link" href="modules/autores/">
                <i class="fas fa-users"></i> <span>Autores</span>
                <span class="badge"><?= $stats['autores'] ?? 0 ?></span>
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> <span>Salir</span>
            </a>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        
        <div class="top-header">
            <div>
                <h2>Bienvenido de nuevo, <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador') ?></h2>
                <p>Aquí está el resumen de hoy, <?= date('d/m/Y') ?></p>
            </div>
        </div>
        
        <!-- STATS CARDS -->
        <div class="row-stats">
            <div class="stat-card">
                <div class="label">Reportajes</div>
                <div class="number"><?= $stats['reportajes'] ?? 0 ?></div>
                <i class="fas fa-file-alt icon"></i>
            </div>
            
            <div class="stat-card">
                <div class="label">Noticias</div>
                <div class="number"><?= $stats['noticias'] ?? 0 ?></div>
                <i class="fas fa-newspaper icon"></i>
            </div>
            
            <div class="stat-card">
                <div class="label">Boletines</div>
                <div class="number"><?= $stats['boletines'] ?? 0 ?></div>
                <i class="fas fa-file-pdf icon"></i>
            </div>
            
            <div class="stat-card">
                <div class="label">Podcasts</div>
                <div class="number"><?= $stats['podcasts'] ?? 0 ?></div>
                <i class="fas fa-microphone icon"></i>
            </div>
            
            <div class="stat-card">
                <div class="label">Videos</div>
                <div class="number"><?= $stats['videos'] ?? 0 ?></div>
                <i class="fas fa-video icon"></i>
            </div>
            
            <div class="stat-card">
                <div class="label">Autores</div>
                <div class="number"><?= $stats['autores'] ?? 0 ?></div>
                <i class="fas fa-users icon"></i>
            </div>
        </div>
        
    </div>

</body>
</html>