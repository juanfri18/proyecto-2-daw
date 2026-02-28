<?php
session_start();
require_once 'configuracion/conexion.php';

// 1. Seguridad
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recogemos el ID 
    $id = $_POST['id'];
    $usuario_id = $_SESSION['user_id']; 

    // Recogemos datos corregidos
    $fecha = $_POST['fecha'];
    $modulo = $_POST['modulo']; 
    
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
        // 2. Sentencia UPDATE
        // Actualizamos SOLO si el id y el usuario coinciden 
        $sql = "UPDATE entrenamientos 
                SET fecha = :fecha, 
                    tipo = :tipo, 
                    duracion_minutos = :duracion, 
                    sensacion = :sensacion, 
                    distancia_km = :distancia,
                    calorias = :calorias
                WHERE id = :id AND usuario_id = :uid";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':fecha' => $fecha,
            ':tipo' => $tipo_db,
            ':duracion' => $duracion,
            ':sensacion' => $sensacion,
            ':distancia' => $distancia,
            ':calorias' => $calorias_calculadas,
            ':id' => $id,
            ':uid' => $usuario_id
        ]);

        header("Location: index.php?msg=actualizado");
        exit();

    } catch (PDOException $e) {
        error_log("Error BD al actualizar: " . $e->getMessage());
        header("Location: index.php?error=db_error");
        exit();
    }
}
?>