<?php
session_start();
require_once 'configuracion/conexion.php'; // 1. Traemos la conexión a la BD

// 2. Seguridad: Si no está logueado, fuera
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$nombre_usuario = $_SESSION['nombre'];

// 3. CONSULTA SQL: Sacar los últimos 5 entrenamientos de este usuario
try {
    $stmt = $pdo->prepare("
        SELECT * FROM entrenamientos 
        WHERE usuario_id = :uid 
        ORDER BY fecha DESC 
        LIMIT 5
    ");
    $stmt->execute([':uid' => $user_id]);
    $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar actividades: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitnessTracker - Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-color: #2A5199; --bg-light: #f0f2f5; }
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; }
        .navbar-custom { background-color: var(--primary-color); }
        .navbar-brand, .nav-link { color: white !important; }
        .nav-link.active { font-weight: 700; border-bottom: 2px solid white; }
        .profile-card, .post-card { border: none; border-radius: 15px; background: white; }
        .stat-badge { background: #eef2f7; border-radius: 10px; padding: 10px; text-align: center; }
        .stat-value { color: var(--primary-color); font-weight: 700; }
        .section-title { font-weight: 700; border-left: 5px solid var(--primary-color); padding-left: 15px; }
        .btn-add { background-color: var(--primary-color); color: white; border-radius: 25px; font-weight: 600; padding: 10px 20px; text-decoration: none; display: inline-block;}
        .btn-add:hover { background-color: #1e3c72; color: white; }
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
                    <li class="nav-item"><a class="nav-link" href="estadisticas.html">Estadísticas</a></li>
                    <li class="nav-item"><a class="nav-link" href="perfil.html">Perfil</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <div class="row g-4 ">
            <div class="col-lg-4">
                <div class="card profile-card p-4 shadow-sm mb-4 ">
                    <div class="text-center mb-3">
                        <div class="rounded-circle mx-auto mb-2" style="width: 80px; height: 80px; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4 class="fw-bold mb-0">
                            <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                        </h4>
                        <p class="text-muted small">"Preparando maratón"</p>
                    </div>
                    <div class="row g-2 mb-4">
                        <div class="col-6"><div class="stat-badge"><small class="d-block text-muted">Peso</small><span class="stat-value">78 kg</span></div></div>
                        <div class="col-6"><div class="stat-badge"><small class="d-block text-muted">Grasa</small><span class="stat-value">18%</span></div></div>
                    </div>
                    <h6 class="fw-bold small text-muted">Progreso Semanal</h6>
                    <canvas id="miniChart" height="150"></canvas>
                    <div class="d-grid mt-4">
                        <a href="registro.php" class="btn btn-add text-center"><i class="fas fa-plus me-2"></i>Nueva Actividad</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <h4 class="section-title">Actividad Reciente</h4>

                <?php if (isset($actividades) && count($actividades) > 0): ?>
                    
                    <?php foreach ($actividades as $actividad): ?>
                        <div class="card post-card shadow-sm mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="user-avatar text-white me-3" style="background-color: var(--primary-color);">
                                        <?php if($actividad['tipo'] == 'Fuerza'): ?>
                                            <i class="fas fa-dumbbell"></i>
                                        <?php elseif($actividad['tipo'] == 'Carrera'): ?>
                                            <i class="fas fa-running"></i>
                                        <?php else: ?>
                                            <i class="fas fa-walking"></i>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div>
                                        <h6 class="mb-0 fw-bold">
                                            <?php echo htmlspecialchars($nombre_usuario); ?> 
                                            <span class="text-muted fw-normal">registró <?php echo htmlspecialchars($actividad['tipo']); ?></span>
                                        </h6>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y', strtotime($actividad['fecha'])); ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="row bg-light rounded p-3 mx-1 mb-3">
                                    <div class="col-4 text-center border-end">
                                        <small class="d-block text-muted">Duración</small>
                                        <span class="fw-bold"><?php echo $actividad['duracion_minutos']; ?> min</span>
                                    </div>
                                    
                                    <div class="col-4 text-center border-end">
                                        <?php if($actividad['distancia_km'] > 0): ?>
                                            <small class="d-block text-muted">Distancia</small>
                                            <span class="fw-bold"><?php echo $actividad['distancia_km']; ?> km</span>
                                        <?php else: ?>
                                            <small class="d-block text-muted">Calorías</small>
                                            <span class="fw-bold"><?php echo $actividad['calorias']; ?> kcal</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-4 text-center">
                                        <small class="d-block text-muted">Sensación</small>
                                        <?php 
                                            // Color del badge según intensidad
                                            $badgeColor = 'bg-success';
                                            if ($actividad['sensacion'] >= 8) $badgeColor = 'bg-danger';
                                            elseif ($actividad['sensacion'] >= 5) $badgeColor = 'bg-warning';
                                        ?>
                                        <span class="badge <?php echo $badgeColor; ?>">
                                            <?php echo $actividad['sensacion']; ?>/10
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <div class="alert alert-info text-center">
                        Todavía no has registrado ninguna actividad. ¡Ve a "Registrar" y empieza hoy!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. Gráfica de progreso 
        const ctx = document.getElementById('miniChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['L','M','X','J','V','S','D'],
                datasets: [{ 
                    data: [300, 450, 0, 550, 400, 700, 200], // Esto luego lo conectare a la BD
                    borderColor: '#2A5199', 
                    tension: 0.4, 
                    fill: true, 
                    backgroundColor: 'rgba(42,81,153,0.1)' 
                }]
            },
            options: { 
                plugins: { legend: {display: false} }, 
                scales: { y: {display: false}, x: {grid: {display: false}} } 
            }
        });

    </script>
</body>
</html>
</body>
</html>