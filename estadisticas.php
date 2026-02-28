<?php
session_start();
require_once 'configuracion/conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$usuario_id = $_SESSION['user_id'];

try {
    // 1. TOTALES GLOBALES
    $sql = "SELECT 
                IFNULL(SUM(distancia_km), 0) as total_km,
                IFNULL(SUM(duracion_minutos), 0) as total_min,
                IFNULL(SUM(calorias), 0) as total_cal,
                COUNT(*) as num_sesiones
            FROM entrenamientos 
            WHERE usuario_id = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':uid' => $usuario_id]);
    $totales = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. TOTALES SEMANA ACTUAL (FT-35)
    // WEEK(fecha, 1) usa el lunes como inicio de semana
    $sqlSemana = "SELECT 
                IFNULL(SUM(distancia_km), 0) as sem_km,
                IFNULL(SUM(calorias), 0) as sem_cal
            FROM entrenamientos 
            WHERE usuario_id = :uid 
            AND YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)";
    $stmtSem = $pdo->prepare($sqlSemana);
    $stmtSem->execute([':uid' => $usuario_id]);
    $semana = $stmtSem->fetch(PDO::FETCH_ASSOC);

    // 3. MEJOR REGISTRO (FT-35)
    $stmtBest = $pdo->prepare("SELECT MAX(distancia_km) as record FROM entrenamientos WHERE usuario_id = :uid");
    $stmtBest->execute([':uid' => $usuario_id]);
    $mejor_marca = $stmtBest->fetchColumn() ?: 0;

    // 4. OBJETIVOS Y PROGRESO (FT-34)
    // Calculamos el progreso real comparando con los datos de entrenamientos
    $stmtObj = $pdo->prepare("SELECT * FROM objetivos WHERE usuario_id = :uid AND estado = 'Pendiente'");
    $stmtObj->execute([':uid' => $usuario_id]);
    $objetivos = $stmtObj->fetchAll(PDO::FETCH_ASSOC);

    $lista_objetivos = [];
    
    foreach($objetivos as $obj) {
        $actual = 0;
        $porcentaje = 0;
        
        if ($obj['tipo'] == 'Distancia Mensual') {
            // Sumar distancia de ESTE mes
            $sqlMes = "SELECT IFNULL(SUM(distancia_km), 0) FROM entrenamientos 
                       WHERE usuario_id = :uid 
                       AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
            $stmtM = $pdo->prepare($sqlMes);
            $stmtM->execute([':uid' => $usuario_id]);
            $actual = $stmtM->fetchColumn();
            
        } elseif ($obj['tipo'] == 'Frecuencia Semanal') {
            // Contar sesiones de ESTA semana
            $sqlFreq = "SELECT COUNT(*) FROM entrenamientos 
                        WHERE usuario_id = :uid 
                        AND YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)";
            $stmtF = $pdo->prepare($sqlFreq);
            $stmtF->execute([':uid' => $usuario_id]);
            $actual = $stmtF->fetchColumn();
        } 
        // Nota: Para 'Peso Corporal' necesitaríamos una tabla de histórico de pesos.
        
        // Calcular porcentaje (max 100%)
        if($obj['valor_objetivo'] > 0) {
            $porcentaje = ($actual / $obj['valor_objetivo']) * 100;
        }
        
        $lista_objetivos[] = [
            'titulo' => $obj['tipo'],
            'meta' => $obj['valor_objetivo'],
            'actual' => $actual,
            'porcentaje' => min($porcentaje, 100), // Tope visual 100%
            'color' => ($porcentaje >= 100) ? 'success' : 'primary'
        ];
    }

    // 5. GRÁFICA (FT-37)
    $stmtGraph = $pdo->prepare("SELECT fecha, calorias FROM entrenamientos 
                                WHERE usuario_id = :uid 
                                ORDER BY fecha ASC LIMIT 7");
    $stmtGraph->execute([':uid' => $usuario_id]);
    $chartData = $stmtGraph->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $dataPoints = [];
    foreach($chartData as $row) {
        $labels[] = date('d/m', strtotime($row['fecha']));
        $dataPoints[] = $row['calorias'];
    }

} catch (PDOException $e) {
    die("Error cargando estadísticas."); // Aquí simplificamos para la vista
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas - FitnessTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-color: #2A5199; }
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .navbar-custom { background-color: var(--primary-color); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .navbar-brand { color: white !important; font-weight: 800; font-size: 1.4rem; letter-spacing: 0.5px;}
        .btn-menu { background: rgba(255,255,255,0.1); border: none; color: white; border-radius: 8px; padding: 8px 12px; transition: 0.3s; }
        .btn-menu:hover { background: rgba(255,255,255,0.2); transform: translateY(-1px); }
        /* Offcanvas Sidebar Premium */
        .offcanvas-custom { border-radius: 0 20px 20px 0; border: none; box-shadow: 5px 0 25px rgba(0,0,0,0.15); width: 280px !important; }
        .offcanvas-header-custom { background: linear-gradient(135deg, var(--primary-color), #1e3c72); color: white; border-radius: 0 20px 0 0; padding: 1.5rem; }
        .menu-link { padding: 12px 20px; border-radius: 12px; margin-bottom: 8px; color: #495057; font-weight: 600; transition: all 0.3s ease; display: flex; align-items: center; text-decoration: none;}
        .menu-link i { font-size: 1.2rem; width: 30px; text-align: center; }
        .menu-link:hover { background-color: #f8f9fa; color: var(--primary-color); transform: translateX(5px); }
        .menu-link.active { background-color: rgba(42, 81, 153, 0.1); color: var(--primary-color); }
        .menu-link.logout { color: #dc3545; margin-top: auto; }
        .menu-link.logout:hover { background-color: rgba(220, 53, 69, 0.1); color: #c82333; }
        
        .stat-card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <nav class="navbar navbar-custom sticky-top py-3">
        <div class="container-fluid px-4 align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn-menu me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <i class="fas fa-bars fs-5"></i>
                </button>
                <a class="navbar-brand mb-0" href="index.php"><i class="fas fa-heartbeat me-2"></i>FitnessTracker</a>
            </div>
            <a href="perfil.php" class="text-white text-decoration-none">
                <div class="d-flex align-items-center" style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 20px;">
                    <i class="fas fa-user-circle fs-5 me-2"></i>
                    <span class="fw-bold d-none d-sm-inline"><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Perfil'); ?></span>
                </div>
            </a>
        </div>
    </nav>

    <!-- Sidebar Offcanvas -->
    <div class="offcanvas offcanvas-start offcanvas-custom" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header offcanvas-header-custom">
            <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel"><i class="fas fa-heartbeat me-2"></i>Menú Principal</h5>
            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-4">
            <div class="mb-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; background: rgba(42, 81, 153, 0.1); color: var(--primary-color); font-size: 1.5rem;">
                    <i class="fas fa-user"></i>
                </div>
                <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></h6>
            </div>
            
            <a href="index.php" class="menu-link"><i class="fas fa-home text-primary"></i> Inicio</a>
            <a href="registro.php" class="menu-link"><i class="fas fa-plus-circle text-success"></i> Registrar Actividad</a>
            <a href="estadisticas.php" class="menu-link active"><i class="fas fa-chart-line text-info"></i> Estadísticas</a>
            <a href="perfil.php" class="menu-link"><i class="fas fa-user-cog text-secondary"></i> Mi Perfil</a>
            
            <a href="logout.php" class="menu-link logout mt-auto"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </div>

    <div class="container py-5">
        <h2 class="fw-bold mb-4" style="color: var(--primary-color);">Tus Métricas</h2>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card p-3 h-100">
                    <small class="text-muted">Distancia Total</small>
                    <h3 class="fw-bold text-primary"><?php echo number_format($totales['total_km'], 1); ?> km</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card p-3 h-100">
                    <small class="text-muted">Esta Semana</small>
                    <h3 class="fw-bold text-success"><?php echo number_format($semana['sem_cal'], 0); ?> kcal</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card p-3 h-100">
                    <small class="text-muted">Tiempo Total</small>
                    <h3 class="fw-bold text-info"><?php echo floor($totales['total_min']/60); ?>h <?php echo $totales['total_min']%60; ?>m</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card p-3 h-100">
                    <small class="text-muted">Mejor Distancia</small>
                    <h3 class="fw-bold text-warning"><?php echo $mejor_marca; ?> km</h3>
                </div>
            </div>
        </div>

        <h4 class="fw-bold mb-3">Mis Objetivos</h4>
        <div class="row mb-5">
            <?php if(count($lista_objetivos) > 0): ?>
                <?php foreach($lista_objetivos as $meta): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card stat-card p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold"><?php echo $meta['titulo']; ?></span>
                                <span class="text-muted small"><?php echo $meta['actual']; ?> / <?php echo $meta['meta']; ?></span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-<?php echo $meta['color']; ?> progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: <?php echo $meta['porcentaje']; ?>%">
                                    <?php echo round($meta['porcentaje']); ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12"><div class="alert alert-light border">No tienes objetivos pendientes. ¡Crea uno en la base de datos!</div></div>
            <?php endif; ?>
        </div>

        <div class="card stat-card p-4">
            <h5 class="fw-bold mb-3">Progreso de Calorías</h5>
            <canvas id="caloriesChart" height="100"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('caloriesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Kcal',
                    data: <?php echo json_encode($dataPoints); ?>,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            }
        });
    </script>
</body>
</html>