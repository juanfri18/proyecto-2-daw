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
        .navbar-custom { background-color: var(--primary-color); }
        .navbar-brand, .nav-link { color: white !important; }
        .nav-link.active { font-weight: 700; border-bottom: 2px solid white; }
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
                    <li class="nav-item"><a class="nav-link" href="estadisticas.php">Estadísticas</a></li>
                    <li class="nav-item"><a class="nav-link active" href="perfil.php">Perfil</a></li>
                    <li class="nav-item"><a class="nav-link text-warning" href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
                </ul>
            </div>
        </div>
    </nav>

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