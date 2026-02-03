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
    
    $tipo_db = 'Fuerza';
    if ($modulo === 'carrera') $tipo_db = 'Carrera';
    if ($modulo === 'caminata') $tipo_db = 'Caminata';

    $duracion = !empty($_POST['tiempo']) ? $_POST['tiempo'] : 0;
    $sensacion = $_POST['sensacion'];
    $distancia = !empty($_POST['distancia']) ? $_POST['distancia'] : 0;

    if (empty($fecha) || empty($modulo)) {
        die("Error: Faltan datos obligatorios.");
    }

    try {
        $sql = "INSERT INTO entrenamientos (usuario_id, fecha, tipo, duracion_minutos, sensacion, distancia_km) 
                VALUES (:uid, :fecha, :tipo, :duracion, :sensacion, :distancia)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $usuario_id,
            ':fecha' => $fecha,
            ':tipo' => $tipo_db,
            ':duracion' => $duracion,
            ':sensacion' => $sensacion,
            ':distancia' => $distancia
        ]);

        header("Location: index.php?msg=guardado");
        exit();

    } catch (PDOException $e) {
        die("Error al guardar en BD: " . $e->getMessage());
    }
}
?>