<?php
session_start(); // Iniciamos la sesión para poder guardar datos del usuario
require_once 'configuracion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Buscamos el usuario en la BD
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch();

    // Verificamos contraseña
    if ($usuario && $password === $usuario['password']) {
        // LOGIN CORRECTO
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        
        // Lo mandamos al Dashboard 
        header("Location: index.php"); 
        exit();
    } else {
        // LOGIN INCORRECTO
        header("Location: login.html?error=1");
        exit();
    }
}
?>