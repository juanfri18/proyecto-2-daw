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

    $duracion = !empty($_POST['tiempo']) ? floatval($_POST['tiempo']) : 0;
    $sensacion = $_POST['sensacion'];
    $distancia = !empty($_POST['distancia']) ? floatval($_POST['distancia']) : 0;

    // CÁLCULO AUTOMÁTICO DE CALORÍAS
    // Fórmula aproximada (METs estimados)
    $calorias_calculadas = 0;
    
    if ($tipo_db === 'Carrera') {
        // Aprox 11 kcal por minuto corriendo
        $calorias_calculadas = $duracion * 11; 
    } elseif ($tipo_db === 'Caminata') {
        // Aprox 4.5 kcal por minuto caminando
        $calorias_calculadas = $duracion * 4.5;
    } elseif ($tipo_db === 'Fuerza') {
        // Aprox 6.5 kcal por minuto pesas
        $calorias_calculadas = $duracion * 6.5;
    }

    // Redondear para que sea un número entero
    $calorias_calculadas = round($calorias_calculadas);

    if (empty($fecha) || empty($modulo)) {
        die("Error: Faltan datos obligatorios.");
    }

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
        die("Error al guardar en BD: " . $e->getMessage());
    }
}
?>