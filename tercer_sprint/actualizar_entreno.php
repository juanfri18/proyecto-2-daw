<?php
session_start();
require_once 'configuracion/conexion.php';

// 1. Seguridad
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recogemos el ID (¡Importante! viaja oculto en el formulario)
    $id = $_POST['id'];
    $usuario_id = $_SESSION['user_id']; // Para seguridad extra

    // Recogemos datos corregidos
    $fecha = $_POST['fecha'];
    $modulo = $_POST['modulo']; 
    
    $tipo_db = 'Fuerza';
    if ($modulo === 'carrera') $tipo_db = 'Carrera';
    if ($modulo === 'caminata') $tipo_db = 'Caminata';

    $duracion = !empty($_POST['tiempo']) ? $_POST['tiempo'] : 0;
    $sensacion = $_POST['sensacion'];
    $distancia = !empty($_POST['distancia']) ? $_POST['distancia'] : 0;

    try {
        // 2. Sentencia UPDATE
        // Actualizamos SOLO si el id y el usuario coinciden (seguridad)
        $sql = "UPDATE entrenamientos 
                SET fecha = :fecha, 
                    tipo = :tipo, 
                    duracion_minutos = :duracion, 
                    sensacion = :sensacion, 
                    distancia_km = :distancia
                WHERE id = :id AND usuario_id = :uid";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':fecha' => $fecha,
            ':tipo' => $tipo_db,
            ':duracion' => $duracion,
            ':sensacion' => $sensacion,
            ':distancia' => $distancia,
            ':id' => $id,
            ':uid' => $usuario_id
        ]);

        header("Location: index.php?msg=actualizado");
        exit();

    } catch (PDOException $e) {
        die("Error al actualizar: " . $e->getMessage());
    }
}
?>