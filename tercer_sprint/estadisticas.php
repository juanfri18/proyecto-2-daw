<?php
session_start();
require_once 'configuracion/conexion.php';

// Seguridad: Si no estás logueado, fuera
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$usuario_id = $_SESSION['user_id'];

try {
    // LÓGICA DE NEGOCIO 

    // Calcular TOTALES usando funciones de agregación SQL
    // Usamos IFNULL para que si no hay datos devuelva 0 en vez de NULL 
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

    // Formatear datos para la vista
    $kpis = [
        'km' => number_format($totales['total_km'], 1),
        'cal' => number_format($totales['total_cal'], 0, ',', '.'),
        'horas' => floor($totales['total_min'] / 60),
        'minutos' => $totales['total_min'] % 60,
        'sesiones' => $totales['num_sesiones']
    ];

    // Obtener el MEJOR REGISTRO 
    // Buscamos la sesión con mayor distancia
    $stmtBest = $pdo->prepare("SELECT MAX(distancia_km) as record FROM entrenamientos WHERE usuario_id = :uid");
    $stmtBest->execute([':uid' => $usuario_id]);
    $mejor_marca = $stmtBest->fetchColumn() ?: 0; // Si es false, pone 0

    // 3. Datos para la GRÁFICA 
    $stmtGraph = $pdo->prepare("SELECT fecha, calorias FROM entrenamientos 
                                WHERE usuario_id = :uid 
                                ORDER BY fecha ASC LIMIT 7");
    $stmtGraph->execute([':uid' => $usuario_id]);
    $chartData = $stmtGraph->fetchAll(PDO::FETCH_ASSOC);

    // Convertir a JSON para que JavaScript lo entienda
    $labels = [];
    $dataPoints = [];
    foreach($chartData as $row) {
        $labels[] = date('d/m', strtotime($row['fecha']));
        $dataPoints[] = $row['calorias'];
    }

} catch (PDOException $e) {
    die("Error en estadísticas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - FitnessTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-color: #2A5199; }
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .navbar-custom { background-color: var(--primary-color); }
        .navbar-brand, .nav-link { color: white !important; }
        .stat-card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
        .icon-box { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 1.5rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-heartbeat me-2"></i>FitnessTracker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="registro.php">Registrar</a></li>
                    <li class="nav-item"><a class="nav-link active" href="estadisticas.php">Estadísticas</a></li>
                    <li class="nav-item"><a class="nav-link" href="perfil.html">Perfil</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="fw-bold mb-4" style="color: var(--primary-color);">Tus Métricas</h2>

        <div class="row g-4 mb-5">
            <div class="col-md-3 col-sm-6">
                <div class="card stat-card p-3 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary me-3"><i class="fas fa-road"></i></div>
                        <div>
                            <small class="text-muted d-block">Distancia Total</small>
                            <h4 class="fw-bold mb-0"><?php echo $kpis['km']; ?> km</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stat-card p-3 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger me-3"><i class="fas fa-fire"></i></div>
                        <div>
                            <small class="text-muted d-block">Calorías Quemadas</small>
                            <h4 class="fw-bold mb-0"><?php echo $kpis['cal']; ?> kcal</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stat-card p-3 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success bg-opacity-10 text-success me-3"><i class="fas fa-clock"></i></div>
                        <div>
                            <small class="text-muted d-block">Tiempo Total</small>
                            <h4 class="fw-bold mb-0"><?php echo $kpis['horas']; ?>h <?php echo $kpis['minutos']; ?>m</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stat-card p-3 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning me-3"><i class="fas fa-trophy"></i></div>
                        <div>
                            <small class="text-muted d-block">Mejor Distancia</small>
                            <h4 class="fw-bold mb-0"><?php echo $mejor_marca; ?> km</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card stat-card p-4">
            <h5 class="fw-bold mb-3">Progreso de Calorías (Últimos entrenos)</h5>
            <canvas id="caloriesChart" height="100"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Inyectamos los datos de PHP a JS
        const labels = <?php echo json_encode($labels); ?>;
        const data = <?php echo json_encode($dataPoints); ?>;

        const ctx = document.getElementById('caloriesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['Sin datos'],
                datasets: [{
                    label: 'Kcal Quemadas',
                    data: data.length ? data : [0],
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: { responsive: true }
        });
    </script>
</body>
</html>