<?php
session_start();
require_once 'configuracion/conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_usuario = $_SESSION['user_id'];
    
    // 1. Recoger datos del texto
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $biografia = trim($_POST['biografia']);
    $telefono = trim($_POST['telefono']);
    $ciudad = trim($_POST['ciudad']);
    $poblacion = trim($_POST['poblacion']);
    $cp = trim($_POST['cp']);
    $password = trim($_POST['password']); // En Sprint 3 usamos texto plano

    // 2. Manejo de la FOTO DE PERFIL
    // Recuperamos la foto actual por si no sube ninguna nueva
    $stmt = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id_usuario]);
    $foto_actual = $stmt->fetchColumn();

    $nombre_foto = $foto_actual; // Por defecto mantenemos la misma

    // Si el usuario ha subido un fichero...
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileSize = $_FILES['foto']['size'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Extensiones permitidas
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Generar nombre único: profile_ID_TIMESTAMP.ext
            $newFileName = 'profile_' . $id_usuario . '_' . time() . '.' . $fileExtension;
            
            // Directorio destino (Crea la carpeta 'img/uploads' si no existe)
            $uploadFileDir = 'img/uploads/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $nombre_foto = $newFileName; // Actualizamos la variable para la BD
            }
        }
    }

    // 3. Actualizar Base de Datos
    try {
        $sql = "UPDATE usuarios SET 
                    nombre = :nombre,
                    apellidos = :apellidos,
                    email = :email,
                    biografia = :biografia,
                    telefono = :telefono,
                    ciudad = :ciudad,
                    poblacion = :poblacion,
                    codigo_postal = :cp,
                    password = :password,
                    foto_perfil = :foto
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellidos' => $apellidos,
            ':email' => $email,
            ':biografia' => $biografia,
            ':telefono' => $telefono,
            ':ciudad' => $ciudad,
            ':poblacion' => $poblacion,
            ':cp' => $cp,
            ':password' => $password,
            ':foto' => $nombre_foto,
            ':id' => $id_usuario
        ]);

        // Actualizamos la sesión por si cambió el nombre
        $_SESSION['nombre'] = $nombre;

        // Volver al perfil con mensaje de éxito
        header("Location: perfil.php?msg=ok");
        exit();

    } catch (PDOException $e) {
        die("Error al actualizar perfil: " . $e->getMessage());
    }
} else {
    header("Location: perfil.php");
    exit();
}
?>