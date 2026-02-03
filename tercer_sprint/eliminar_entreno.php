<?php
session_start();
require_once 'configuracion/conexion.php';

// 1. Seguridad: Solo logueados
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// 2. Validamos que nos llegue una ID
if (isset($_GET['id'])) {
    $id_entreno = $_GET['id'];
    $usuario_id = $_SESSION['user_id'];

    try {
        // 3. Borramos (¡OJO! Solo si el entrenamiento pertenece a ESTE usuario)
        // Esto evita que Juan borre los entrenos de Ana cambiando el ID en la URL.
        $stmt = $pdo->prepare("DELETE FROM entrenamientos WHERE id = :id AND usuario_id = :uid");
        $stmt->execute([':id' => $id_entreno, ':uid' => $usuario_id]);

        // Volvemos al inicio
        header("Location: index.php?msg=eliminado");
        exit();

    } catch (PDOException $e) {
        die("Error al eliminar: " . $e->getMessage());
    }
} else {
    // Si intentan entrar sin ID, los echamos
    header("Location: index.php");
    exit();
}
?>