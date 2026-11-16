<?php
// Cargamos la conexión actual (PDO)
require_once __DIR__ . '/config/database.php';

// Función para traer datos de la BD
function getReportajes($pdo, $limit = 3) {
    $stmt = $pdo->prepare("SELECT * FROM reportajes ORDER BY fecha_publicacion DESC LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getNoticias($pdo, $limit = 3) {
    $stmt = $pdo->prepare("SELECT * FROM noticias ORDER BY fecha_publicacion DESC LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

$reportajes = getReportajes($pdo);
$noticias = getNoticias($pdo);

// Función maestra para encontrar imágenes en cualquier carpeta
function obtenerImagenFinal($ruta_db, $carpeta_defecto) {
    if (empty($ruta_db)) return $carpeta_defecto . 'default.jpg';
    
    // Si la ruta guardada en la BD ya funciona, la usamos
    if (file_exists($ruta_db)) return $ruta_db;
    
    // Si solo guardaron el nombre, buscamos en uploads/
    if (file_exists('uploads/' . $ruta_db)) return 'uploads/' . $ruta_db;
    
    // Si es de reportajes, buscamos en la carpeta de reportajes
    if (file_exists('uploads/reportajes/' . $ruta_db)) return 'uploads/reportajes/' . $ruta_db;
    if (file_exists('assets/images/reportajes/' . $ruta_db)) return 'assets/images/reportajes/' . $ruta_db;

    // Si es de noticias, buscamos en la carpeta de noticias
    if (file_exists('uploads/noticias/' . $ruta_db)) return 'uploads/noticias/' . $ruta_db;
    if (file_exists('assets/images/noticias/' . $ruta_db)) return 'assets/images/noticias/' . $ruta_db;

    // Si no se encontró nada, usa la imagen por defecto
    return $carpeta_defecto . 'default.jpg';
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>DDP Noticias - Diálogo y Desarrollo Perú</title>
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
                  <li class="nav-item active"><a class="nav-link" href="index.php">Inicio</a></li>
                  <li class="nav-item"><a class="nav-link" href="#actualidad">Actualidad</a></li>
                  <li class="nav-item"><a class="nav-link" href="reportajes.php">Reportajes</a></li>
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
                </div>
            </div>
        </div>
    </div>
</section>

<div class="grids-block-5 py-1">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php if (count($reportajes) > 0): ?>
                    <?php foreach ($reportajes as $reportaje): 
                        $titulo = htmlspecialchars($reportaje['titulo']);
                        $fecha = date('M j, Y', strtotime($reportaje['fecha_publicacion']));
                        $imagen = obtenerImagenFinal($reportaje['foto_principal'], 'assets/images/reportajes/');
                        $link = 'reportaje.php?id=' . $reportaje['id'];
                    ?>
                    <div class="col-lg-4 col-md-6 grids5-info mt-5">
                        <a href="<?= $link ?>" class="d-block">
                            <!-- Forzamos altura fija para que se vea ordenado -->
                            <img src="<?= $imagen ?>" alt="<?= $titulo ?>" class="img-fluid" style="width: 100%; height: 250px; object-fit: cover;" />
                        </a>
                        <div class="blog-info">
                            <h5><?= $fecha ?></h5>
                            <h4><a href="<?= $link ?>" class="d-block"><?= $titulo ?></a></h4>
                            <a href="<?= $link ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center col-12">No hay reportajes disponibles.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<section class="breadcrumb-area py-sm-5 py-1">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Noticias Recientes</h2>
                    <a class="anchor" id="actualidad"></a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php if (count($noticias) > 0): ?>
                    <?php $i = 0; foreach ($noticias as $noticia): 
                        $titulo = htmlspecialchars($noticia['titulo']);
                        $fecha = date('F j, Y', strtotime($noticia['fecha_publicacion']));
                        $imagen = obtenerImagenFinal($noticia['foto'], 'assets/images/noticias/');
                        $link = $noticia['link_externo'] ?: '#';
                        $target = $noticia['link_externo'] ? '_blank' : '_self';
                        $clase_mt = ($i > 0) ? ' mt-lg-0 mt-5' : '';
                        $i++;
                    ?>
                    <div class="col-lg-4 col-md-6 grids5-info<?= $clase_mt ?>">
                        <a target="<?= $target ?>" href="<?= $link ?>" class="d-block">
                            <img src="<?= $imagen ?>" alt="<?= $titulo ?>" class="img-fluid" style="width: 100%; height: 250px; object-fit: cover;" />
                        </a>
                        <div class="blog-info">
                            <h5><?= $fecha ?></h5>
                            <h4><a target="<?= $target ?>" href="<?= $link ?>" class="d-block"><?= $titulo ?></a></h4>
                            <a target="<?= $target ?>" href="<?= $link ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center col-12">No hay noticias disponibles.</p>
                <?php endif; ?>
            </div>
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