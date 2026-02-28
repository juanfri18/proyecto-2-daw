<?php
session_start();
require_once 'configuracion/conexion.php';

// 1. Seguridad: Si no hay sesión, al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['user_id'];

// 2. Obtener datos actuales del usuario
try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id_usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error al cargar perfil: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitnessTracker - Mi Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-color: #2A5199; --bg-light: #f4f6f9; }
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
        
        .card-profile { border: none; border-radius: 15px; overflow: hidden; }
        .profile-header { height: 100px; background: linear-gradient(135deg, var(--primary-color), #1e3c72); }
        .avatar-container { margin-top: -50px; text-align: center; position: relative; }
        /* Si no hay foto, mostramos un color de fondo gris */
        .profile-img { width: 120px; height: 120px; border-radius: 50%; border: 5px solid white; object-fit: cover; background: #ddd; }
        .btn-camera { position: absolute; bottom: 0; right: 50%; margin-right: -60px; background: white; border-radius: 50%; padding: 8px; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .section-title { color: var(--primary-color); font-weight: 700; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; margin-top: 20px; }
        .btn-save { background-color: var(--primary-color); color: white; border-radius: 25px; padding: 10px 30px; font-weight: 600; border:none; }
        .btn-save:hover { background-color: #1e3c72; }
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
            <a href="estadisticas.php" class="menu-link"><i class="fas fa-chart-line text-info"></i> Estadísticas</a>
            <a href="perfil.php" class="menu-link active"><i class="fas fa-user-cog text-secondary"></i> Mi Perfil</a>
            
            <a href="logout.php" class="menu-link logout mt-auto"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </div>

    <div class="container-fluid px-4 py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                
                <?php if(isset($_GET['msg']) && $_GET['msg']=='ok'): ?>
                    <div class="alert alert-success text-center">¡Perfil actualizado correctamente!</div>
                <?php endif; ?>

                <div class="card card-profile shadow-sm bg-white">
                    <div class="profile-header"></div>
                    <div class="card-body p-4">
                        
                        <form action="actualizar_perfil.php" method="POST" enctype="multipart/form-data">
                            
                            <div class="avatar-container mb-4">
                                <?php 
                                    $ruta_foto = !empty($user['foto_perfil']) ? "img/uploads/" . htmlspecialchars($user['foto_perfil']) : "https://via.placeholder.com/120?text=Usuario";
                                ?>
                                <img src="<?php echo $ruta_foto; ?>" id="preview" class="profile-img">
                                
                                <input type="file" name="foto" id="fileUp" hidden onchange="loadImg(event)" accept="image/*">
                                <label for="fileUp" class="btn-camera" title="Cambiar foto"><i class="fas fa-camera text-primary"></i></label>
                                
                                <h3 class="mt-2 fw-bold mb-0">
                                    <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']); ?>
                                </h3>
                                <p class="text-muted fst-italic" id="bioDisplay">
                                    "<?php echo !empty($user['biografia']) ? htmlspecialchars($user['biografia']) : 'Sin biografía...'; ?>"
                                </p>
                            </div>

                            <h5 class="section-title"><i class="fas fa-user me-2"></i>Datos Personales</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold">Nombre</label>
                                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">Apellidos</label>
                                    <input type="text" name="apellidos" class="form-control" value="<?php echo htmlspecialchars($user['apellidos']); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="small fw-bold">Biografía / Estado</label>
                                    <input type="text" name="biografia" class="form-control" id="bioInput" 
                                           value="<?php echo htmlspecialchars($user['biografia']); ?>" 
                                           oninput="updateBio()" maxlength="255">
                                </div>
                            </div>

                            <h5 class="section-title"><i class="fas fa-map-marker-alt me-2"></i>Contacto y Ubicación</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold">Teléfono</label>
                                    <input type="tel" name="telefono" class="form-control" value="<?php echo htmlspecialchars($user['telefono']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">Correo (Login)</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="small fw-bold">Ciudad</label>
                                    <input type="text" name="ciudad" class="form-control" value="<?php echo htmlspecialchars($user['ciudad']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">Población</label>
                                    <input type="text" name="poblacion" class="form-control" value="<?php echo htmlspecialchars($user['poblacion']); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="small fw-bold">CP</label>
                                    <input type="text" name="cp" class="form-control" value="<?php echo htmlspecialchars($user['codigo_postal']); ?>">
                                </div>
                            </div>

                            <h5 class="section-title"><i class="fas fa-shield-alt me-2"></i>Seguridad</h5>
                            <div class="row g-3 mb-5">
                                <div class="col-md-12">
                                    <label class="small fw-bold">Contraseña</label>
                                    <input type="password" name="password" class="form-control" value="<?php echo htmlspecialchars($user['password']); ?>">
                                    <small class="text-muted">Cámbiala solo si deseas actualizarla.</small>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-save shadow-sm">Guardar Cambios</button>
                                <a href="index.php" class="btn btn-link text-muted ms-3 text-decoration-none">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Previsualización de imagen en JS
        function loadImg(e) {
            if(e.target.files && e.target.files[0]) {
                const r = new FileReader();
                r.onload = function(ev){ document.getElementById('preview').src = ev.target.result; };
                r.readAsDataURL(e.target.files[0]);
            }
        }
        // Actualizar biografía en tiempo real (solo visual)
        function updateBio() {
            const val = document.getElementById('bioInput').value;
            document.getElementById('bioDisplay').innerText = val ? `"${val}"` : "";
        }
    </script>
</body>
</html>