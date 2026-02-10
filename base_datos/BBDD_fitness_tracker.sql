DROP DATABASE IF EXISTS fitness_tracker;
CREATE DATABASE fitness_tracker;
USE fitness_tracker;
CREATE TABLE usuarios (
	id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,       
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255),
    biografia TEXT,
    telefono VARCHAR(20),
    ciudad VARCHAR(50),
    poblacion VARCHAR(50),
    codigo_postal VARCHAR(10),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE ejercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    grupo_muscular ENUM('Pecho', 'Espalda', 'Pierna', 'Brazos', 'Hombros', 'Core', 'Cardio') NOT NULL,
    descripcion TEXT
) ENGINE=InnoDB;
CREATE TABLE entrenamientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha DATE NOT NULL,
    tipo ENUM('Fuerza', 'Carrera', 'Caminata') NOT NULL,
    duracion_minutos INT DEFAULT 0,
    calorias INT DEFAULT 0,
    sensacion INT CHECK (sensacion BETWEEN 1 AND 10),
    distancia_km DECIMAL(5,2) DEFAULT 0.00,
    CONSTRAINT fk_entrenamiento_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_entrenamiento_fecha ON entrenamientos(fecha);
CREATE INDEX idx_entrenamiento_usuario ON entrenamientos(usuario_id);

CREATE TABLE detalles_entrenamiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entrenamiento_id INT NOT NULL,
    ejercicio_id INT NOT NULL,
    series INT DEFAULT 0,
    repeticiones INT DEFAULT 0,
    peso_kg DECIMAL(5,2) DEFAULT 0.00,
    CONSTRAINT fk_detalle_entrenamiento
        FOREIGN KEY (entrenamiento_id) REFERENCES entrenamientos(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_detalle_ejercicio
        FOREIGN KEY (ejercicio_id) REFERENCES ejercicios(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE objetivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('Peso Corporal', 'Frecuencia Semanal', 'Distancia Mensual') NOT NULL,
    valor_objetivo DECIMAL(10,2) NOT NULL,
    fecha_limite DATE NOT NULL,
    estado ENUM('Pendiente', 'Completado', 'Fallido') DEFAULT 'Pendiente',
    
    -- (FT-21 Relaciones FK)
    CONSTRAINT fk_objetivo_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;