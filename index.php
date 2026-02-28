<?php
session_start();
require_once 'configuracion/conexion.php'; // 1. Traigo la conexión a la BD

// 2. Seguridad: Si no está logueado, fuera
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    error_log("Error al cargar actividades: " . $e->getMessage());
    $actividades = [];
    $error_bd = "Hubo un problema al cargar los datos de entrenamientos.";
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
        
        .profile-card, .post-card { border: none; border-radius: 15px; background: white; }
        .stat-badge { background: #eef2f7; border-radius: 10px; padding: 10px; text-align: center; }
        .stat-value { color: var(--primary-color); font-weight: 700; }
        .section-title { font-weight: 700; border-left: 5px solid var(--primary-color); padding-left: 15px; }
        .btn-add { background-color: var(--primary-color); color: white; border-radius: 25px; font-weight: 600; padding: 10px 20px; text-decoration: none; display: inline-block;}
        .btn-add:hover { background-color: #1e3c72; color: white; }
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
                <small class="text-muted">Tratando de mejorar cada día</small>
            </div>
            
            <a href="index.php" class="menu-link active"><i class="fas fa-home text-primary"></i> Inicio</a>
            <a href="registro.php" class="menu-link"><i class="fas fa-plus-circle text-success"></i> Registrar Actividad</a>
            <a href="estadisticas.php" class="menu-link"><i class="fas fa-chart-line text-info"></i> Estadísticas</a>
            <a href="perfil.php" class="menu-link"><i class="fas fa-user-cog text-secondary"></i> Mi Perfil</a>
            
            <a href="logout.php" class="menu-link logout mt-auto"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </div>

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

                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'actualizado'): ?>
                    <div class="alert alert-success mt-3">¡Entrenamiento actualizado correctamente!</div>
                <?php endif; ?>
                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'eliminado'): ?>
                    <div class="alert alert-warning mt-3">Entrenamiento eliminado.</div>
                <?php endif; ?>
                <?php if (isset($_GET['error']) && $_GET['error'] == 'db_error'): ?>
                    <div class="alert alert-danger mt-3">Hubo un error con la base de datos al realizar la operación.</div>
                <?php endif; ?>
                <?php if (isset($error_bd)): ?>
                    <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error_bd); ?></div>
                <?php endif; ?>

                <?php if (isset($actividades) && count($actividades) > 0): ?>
                    
                    <?php foreach ($actividades as $actividad): ?>
                        <div class="card post-card shadow-sm mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
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
                                            <?php echo htmlspecialchars($actividad['tipo']); ?>
                                        </h6>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y', strtotime($actividad['fecha'])); ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="editar_entreno.php?id=<?php echo $actividad['id']; ?>" 
                                    class="btn btn-outline-primary btn-sm rounded-circle" 
                                    title="Editar">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    <a href="eliminar_entreno.php?id=<?php echo $actividad['id']; ?>" 
                                    class="btn btn-outline-danger btn-sm rounded-circle" 
                                    onclick="return confirm('¿Seguro que quieres borrar este entreno?');"
                                    title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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