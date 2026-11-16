<?php
// reportajes.php - Lista de reportajes con paginacion
require_once 'config/database.php';

// Configuracion de paginacion
$por_pagina = 6;
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$offset = ($pagina - 1) * $por_pagina;

// Contar total
$stmt = $pdo->query("SELECT COUNT(*) as total FROM reportajes");
$total_reportajes = $stmt->fetch()['total'];
$total_paginas = ceil($total_reportajes / $por_pagina);

// Traer los de esta pagina
$stmt = $pdo->prepare("SELECT * FROM reportajes ORDER BY fecha_publicacion DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$reportajes = $stmt->fetchAll();

function fechaMes($fecha) {
    if (empty($fecha)) return '';
    return date('M d, Y', strtotime($fecha));
}

// Función maestra para encontrar imágenes en cualquier carpeta
function obtenerImagenFinal($ruta_db) {
    if (empty($ruta_db)) return 'assets/images/reportajes/default.jpg';
    if (file_exists($ruta_db)) return $ruta_db;
    if (file_exists('uploads/' . $ruta_db)) return 'uploads/' . $ruta_db;
    if (file_exists('uploads/reportajes/' . $ruta_db)) return 'uploads/reportajes/' . $ruta_db;
    if (file_exists('assets/images/reportajes/' . $ruta_db)) return 'assets/images/reportajes/' . $ruta_db;
    return 'assets/images/reportajes/default.jpg';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reportajes - Diálogo y Desarrollo Perú</title>
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600&amp;subset=latin-ext,vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-starter.css">
</head>
<body>

<header id="site-header" class="fixed-top">
  <div class="container">
      <nav class="navbar navbar-expand-lg stroke">
      <a class="navbar-brand" href="index.php">
          <img src="assets/images/iconos/logo.png" alt="Logo" style="height:75px;" />
      </a> 
          <button class="navbar-toggler collapsed bg-gradient" type="button" data-toggle="collapse"
              data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false"
              aria-label="Toggle navigation">
              <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>
              <span class="navbar-toggler-icon fa icon-close fa-times"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
              <ul class="navbar-nav ml-auto">
                  <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                  <li class="nav-item"><a class="nav-link" href="index.php#actualidad">Actualidad</a></li>
                  <li class="nav-item active"><a class="nav-link" href="reportajes.php">Reportajes</a></li>
                  <li class="nav-item"><a class="nav-link" href="podcasts.php">Podcast</a></li>
                  <li class="nav-item"><a class="nav-link" href="boletines.php">Boletín NTEP</a></li>
                  <li class="nav-item"><a class="nav-link" href="alianzas.php">Alianzas</a></li>
                  <li class="nav-item"><a class="nav-link" href="about.php">Sobre D&D</a></li>
                  <li class="ml-2"><a href="contact.html" class="btn btn-style btn-outline-secondary">Contacto</a></li>
              </ul>
          </div>
      </nav>
  </div>
</header>

<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Reportajes</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="index.php">Inicio</a></li>
                            <li class="active">Reportajes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php if (count($reportajes) > 0): ?>
                    <?php foreach ($reportajes as $reportaje): ?>
                        <div class="col-lg-4 col-md-6 grids5-info mt-5">
                            <a href="reportaje.php?id=<?= $reportaje['id'] ?>" class="d-block">
                                <!-- Forzamos altura fija para que se vea ordenado -->
                                <img src="<?= htmlspecialchars(obtenerImagenFinal($reportaje['foto_principal'])) ?>" alt="<?= htmlspecialchars($reportaje['titulo']) ?>" class="img-fluid" style="width: 100%; height: 250px; object-fit: cover;" />
                            </a>
                            <div class="blog-info">
                                <h5><?= fechaMes($reportaje['fecha_publicacion']) ?></h5>
                                <h4><a href="reportaje.php?id=<?= $reportaje['id'] ?>" class="d-block"><?= htmlspecialchars($reportaje['titulo']) ?></a></h4>
                                <a href="reportaje.php?id=<?= $reportaje['id'] ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p>No hay reportajes disponibles.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($total_paginas > 1): ?>
            <div class="pagination">
                <ul>
                    <?php if ($pagina > 1): ?>
                        <li class="prev"><a href="?pagina=<?= $pagina - 1 ?>"> Ant</a></li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li>
                            <a href="?pagina=<?= $i ?>" class="<?= ($i == $pagina) ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($pagina < $total_paginas): ?>
                        <li class="next"><a href="?pagina=<?= $pagina + 1 ?>"> Sig </a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<footer class="w3l-footer-29-main py-5" id="footer">
  <div class="footer-29 py-md-3">
    <div class="container">
      <div class="bottom-copies text-center">
        <p class="copy-footer-29">© 2026 Diálogo y Desarrollo Perú. All rights reserved | Designed by <a target="_blank" href="https://www.wsperu.info/">WebSolutions</a></p>
      </div>
    </div>
  </div>
</footer>

<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>