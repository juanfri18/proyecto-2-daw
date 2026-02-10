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
        .navbar-custom { background-color: var(--primary-color); }
        .navbar-brand, .nav-link { color: white !important; }
        .stat-card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-heartbeat me-2"></i>FitnessTracker</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="registro.php">Registrar</a></li>
                    <li class="nav-item"><a class="nav-link active" href="estadisticas.php">Estadísticas</a></li>
                    <li class="nav-item"><a class="nav-link" href="perfil.php">Perfil</a></li>
                </ul>
            </div>
        </div>
    </nav>

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