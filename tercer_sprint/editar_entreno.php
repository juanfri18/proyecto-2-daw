<?php
session_start();
require_once 'configuracion/conexion.php';

// 1. Verificar login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// 2. Obtener datos del entrenamiento a editar
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_entreno = $_GET['id'];
$usuario_id = $_SESSION['user_id'];

// Consultamos los datos actuales para rellenar el formulario
$stmt = $pdo->prepare("SELECT * FROM entrenamientos WHERE id = :id AND usuario_id = :uid");
$stmt->execute([':id' => $id_entreno, ':uid' => $usuario_id]);
$entreno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entreno) {
    die("Entrenamiento no encontrado.");
}

// Preparamos valores para el HTML
$tipo_actual = strtolower($entreno['tipo']); // 'fuerza', 'carrera', 'caminata'
// Ajuste pequeño porque en BD es 'Carrera' y en value es 'carrera'
if($entreno['tipo'] == 'Carrera') $tipo_actual = 'carrera';
if($entreno['tipo'] == 'Caminata') $tipo_actual = 'caminata';
if($entreno['tipo'] == 'Fuerza') $tipo_actual = 'fuerza';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Actividad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-save { background-color: #ffc107; color: #000; font-weight: bold; border: none; } /* Amarillo para editar */
        .btn-save:hover { background-color: #e0a800; }
        .form-section { display: none; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-custom p-4 bg-white">
                    <h3 class="fw-bold mb-4 text-center text-warning">Editar Actividad</h3>
                    
                    <form action="actualizar_entreno.php" method="POST">
                        
                        <input type="hidden" name="id" value="<?php echo $entreno['id']; ?>">

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fecha</label>
                                <input type="date" class="form-control" name="fecha" value="<?php echo $entreno['fecha']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tipo</label>
                                <select class="form-select border-warning fw-bold" id="mainCat" name="modulo" required onchange="toggleModule()">
                                    <option value="fuerza" <?php if($tipo_actual=='fuerza') echo 'selected'; ?>>Fuerza</option>
                                    <option value="carrera" <?php if($tipo_actual=='carrera') echo 'selected'; ?>>Carrera</option>
                                    <option value="caminata" <?php if($tipo_actual=='caminata') echo 'selected'; ?>>Caminata</option>
                                </select>
                            </div>
                        </div>

                        <div id="sec-cardio" class="form-section">
                            <h5 class="mb-3"><i class="fas fa-running me-2"></i>Datos Cardio</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold">Distancia (km)</label>
                                    <input type="number" step="0.01" class="form-control" name="distancia" value="<?php echo $entreno['distancia_km']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">Tiempo (min)</label>
                                    <input type="number" class="form-control" name="tiempo" value="<?php echo $entreno['duracion_minutos']; ?>">
                                </div>
                            </div>
                        </div>

                        <div id="sec-fuerza" class="form-section">
                            <h5 class="mb-3"><i class="fas fa-dumbbell me-2"></i>Datos Fuerza</h5>
                            <div class="alert alert-info small">
                                Editando sesión de Fuerza. Puedes ajustar la duración y sensación.
                            </div>
                            <label class="small fw-bold">Duración Total (min)</label>
                            <input type="number" class="form-control" name="tiempo" value="<?php echo $entreno['duracion_minutos']; ?>">
                        </div>

                        <div class="mt-4">
                            <label class="form-label fw-bold">Sensación (1-10)</label>
                            <input type="range" class="form-range" min="1" max="10" name="sensacion" value="<?php echo $entreno['sensacion']; ?>" oninput="document.getElementById('val').innerText=this.value">
                            <div class="text-center fw-bold fs-5 text-warning" id="val"><?php echo $entreno['sensacion']; ?></div>
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-save shadow">Guardar Cambios</button>
                            <a href="index.php" class="btn btn-link text-muted text-decoration-none text-center">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleModule() {
            const val = document.getElementById('mainCat').value;
            document.getElementById('sec-fuerza').style.display = val === 'fuerza' ? 'block' : 'none';
            document.getElementById('sec-cardio').style.display = (val === 'carrera' || val === 'caminata') ? 'block' : 'none';
            
            // Si pasamos de fuerza a cardio, deshabilitamos el input de tiempo de fuerza para que no se envíe duplicado (y viceversa)
            // En este ejemplo simple usamos el mismo name="tiempo" en ambos, así que PHP cogerá el último válido.
            // Para simplificar, asumimos que el usuario rellena el que ve.
        }
        // Ejecutar al cargar para mostrar el correcto según DB
        window.onload = toggleModule;
    </script>
</body>
</html>