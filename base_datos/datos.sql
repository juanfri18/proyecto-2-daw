USE fitness_tracker;

INSERT INTO ejercicios (nombre, grupo_muscular) VALUES
('Press Banca', 'Pecho'), ('Aperturas con Mancuernas', 'Pecho'), ('Flexiones', 'Pecho'), ('Cruce de Poleas', 'Pecho'),
('Dominadas', 'Espalda'), ('Remo con Barra', 'Espalda'), ('Jalón al Pecho', 'Espalda'), ('Peso Muerto', 'Espalda'),
('Sentadilla', 'Pierna'), ('Prensa', 'Pierna'), ('Zancadas', 'Pierna'), ('Extension de Cuadriceps', 'Pierna'),
('Curl de Biceps', 'Brazos'), ('Extension de Triceps', 'Brazos'), ('Press Militar', 'Hombros'), ('Elevaciones Laterales', 'Hombros'),
('Plancha Abdominal', 'Core'), ('Russian Twist', 'Core'),
('Carrera Suave', 'Cardio'), ('Caminata Rápida', 'Cardio');
INSERT INTO usuarios (nombre, apellidos, email, password, ciudad) VALUES
('Juan', 'Pérez', 'juan@fit.com', '123456', 'Madrid'),
('Ana', 'García', 'ana@fit.com', '123456', 'Barcelona'),
('Carlos', 'López', 'carlos@fit.com', '123456', 'Valencia'),
('Laura', 'Martínez', 'laura@fit.com', '123456', 'Sevilla'),
('Pedro', 'Sánchez', 'pedro@fit.com', '123456', 'Bilbao');
INSERT INTO entrenamientos (usuario_id, fecha, tipo, duracion_minutos, calorias, sensacion, distancia_km) VALUES
(1, '2023-10-01', 'Fuerza', 60, 300, 8, 0),
(1, '2023-10-02', 'Carrera', 45, 450, 9, 8.5),
(2, '2023-10-01', 'Caminata', 30, 150, 4, 3.0),
(2, '2023-10-03', 'Fuerza', 50, 250, 7, 0),
(3, '2023-10-04', 'Fuerza', 70, 350, 9, 0),
(3, '2023-10-05', 'Carrera', 60, 600, 10, 10.0),
(4, '2023-10-01', 'Fuerza', 45, 200, 6, 0),
(4, '2023-10-03', 'Fuerza', 45, 210, 7, 0),
(5, '2023-10-02', 'Caminata', 90, 300, 5, 6.0),
(5, '2023-10-06', 'Carrera', 20, 200, 8, 4.0);
INSERT INTO detalles_entrenamiento (entrenamiento_id, ejercicio_id, series, repeticiones, peso_kg) VALUES
(1, 1, 4, 10, 60.5),
(1, 5, 3, 8, 0),   
(4, 9, 4, 12, 80),  
(7, 13, 3, 15, 12);
INSERT INTO objetivos (usuario_id, tipo, valor_objetivo, fecha_limite, estado) VALUES
(1, 'Peso Corporal', 75.0, '2023-12-31', 'Pendiente'),
(2, 'Frecuencia Semanal', 4, '2023-11-30', 'Pendiente'),
(3, 'Distancia Mensual', 50.0, '2023-10-31', 'Completado'),
(4, 'Peso Corporal', 60.0, '2024-01-01', 'Pendiente'),
(5, 'Frecuencia Semanal', 3, '2023-11-15', 'Fallido');