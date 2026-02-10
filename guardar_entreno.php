<?php
session_start();
require_once 'configuracion/conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $usuario_id = $_SESSION['user_id'];
    $fecha = $_POST['fecha'];
    $modulo = $_POST['modulo']; 
    // Validación básica (FT-26 y FT-31)
    if (empty($fecha) || empty($modulo)) {
        header("Location: registro.php?error=campos_vacios");
        exit();
    }

    $tipo_db = 'Fuerza';
    if ($modulo === 'carrera') $tipo_db = 'Carrera';
    if ($modulo === 'caminata') $tipo_db = 'Caminata';

    $duracion = !empty($_POST['tiempo']) ? floatval($_POST['tiempo']) : 0;
    $sensacion = !empty($_POST['sensacion']) ? intval($_POST['sensacion']) : 5;
    $distancia = !empty($_POST['distancia']) ? floatval($_POST['distancia']) : 0;

    // CÁLCULO DE CALORÍAS (FT-33)
    $calorias_calculadas = 0;
    if ($tipo_db === 'Carrera') {
        $calorias_calculadas = $duracion * 11; 
    } elseif ($tipo_db === 'Caminata') {
        $calorias_calculadas = $duracion * 4.5;
    } elseif ($tipo_db === 'Fuerza') {
        $calorias_calculadas = $duracion * 6.5;
    }
    $calorias_calculadas = round($calorias_calculadas);

    try {
        $sql = "INSERT INTO entrenamientos (usuario_id, fecha, tipo, duracion_minutos, sensacion, distancia_km, calorias) 
                VALUES (:uid, :fecha, :tipo, :duracion, :sensacion, :distancia, :calorias)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $usuario_id,
            ':fecha' => $fecha,
            ':tipo' => $tipo_db,
            ':duracion' => $duracion,
            ':sensacion' => $sensacion,
            ':distancia' => $distancia,
            ':calorias' => $calorias_calculadas 
        ]);

        header("Location: index.php?msg=guardado");
        exit();

    } catch (PDOException $e) {
        // FT-32: Manejo de errores sin pantallazo feo
        error_log("Error DB: " . $e->getMessage()); // Guardar log interno
        header("Location: registro.php?error=db_error"); // Redirigir amigablemente
        exit();
    }
}
?>