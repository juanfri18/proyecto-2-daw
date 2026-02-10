<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitnessTracker - Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-color: #2A5199; --accent-cardio: #198754; --accent-fuerza: #2A5199; }
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .navbar-custom { background-color: var(--primary-color); }
        .navbar-brand, .nav-link { color: white !important; }
        .nav-link.active { font-weight: 700; border-bottom: 2px solid white; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .form-section { display: none; margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px; }
        .btn-save { background-color: var(--primary-color); color: white; font-weight: bold; padding: 12px; border-radius: 25px; border: none; width: 100%; transition: 0.3s; }
        .btn-save:hover { background-color: #1e3c72; }
        .admin-note { font-size: 0.8rem; color: #6c757d; font-style: italic; border-left: 3px solid #dee2e6; padding-left: 10px; margin-top: 10px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-heartbeat me-2"></i>FitnessTracker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link active" href="registro.php">Registrar</a></li>
                    <li class="nav-item"><a class="nav-link" href="estadisticas.php">Estadísticas</a></li>
                    <li class="nav-item"><a class="nav-link" href="perfil.html">Perfil</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-custom p-4 p-md-5 bg-white">
                    <h3 class="fw-bold mb-4 text-center" style="color: var(--primary-color);">Registrar Actividad</h3>
                    
                    <form id="fitnessForm" action="guardar_entreno.php" method="POST" class="needs-validation" novalidate>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Fecha</label>
                                <input type="date" class="form-control" id="date" name="fecha" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Duración (min)</label>
                                <input type="number" class="form-control" id="time" name="tiempo" placeholder="Minutos" required oninput="pace()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Módulo / Categoría</label>
                                <select class="form-select border-primary fw-bold" id="mainCat" name="modulo" required onchange="toggleModule()">
                                    <option value="" disabled selected>Seleccione...</option>
                                    <option value="fuerza">Entrenamiento de Fuerza</option>
                                    <option value="carrera">Carrera</option>
                                    <option value="caminata">Caminata</option>
                                </select>
                            </div>
                        </div>

                        <div id="sec-fuerza" class="form-section animate__animated animate__fadeIn">
                            <h5 class="text-primary mb-3"><i class="fas fa-dumbbell me-2"></i>Detalle Musculación</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold">Grupo Muscular</label>
                                    <select class="form-select" id="group" name="grupo_muscular" onchange="loadEx()">
                                        <option value="">Seleccione...</option>
                                        <option value="pecho">Pecho</option>
                                        <option value="espalda">Espalda</option>
                                        <option value="pierna">Pierna</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">Ejercicio</label>
                                    <select class="form-select" id="exList" name="ejercicio">
                                        <option>Seleccione grupo primero...</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row g-2 p-3 bg-light rounded shadow-sm">
                                <div class="col-4"><label class="small fw-bold">Nº Series</label><input type="number" name="series" class="form-control" placeholder="Ej: 4"></div>
                                <div class="col-4"><label class="small fw-bold">Repeticiones</label><input type="number" name="reps" class="form-control" placeholder="Ej: 12"></div>
                                <div class="col-4"><label class="small fw-bold">Carga (Kg)</label><input type="number" name="carga" class="form-control" placeholder="Ej: 60"></div>
                            </div>
                        </div>

                        <div id="sec-cardio" class="form-section animate__animated animate__fadeIn">
                            <h5 id="cardioTitle" class="mb-3"><i class="fas fa-running me-2"></i>Detalle Cardio</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6"><label class="small fw-bold">Distancia (km)</label><input type="number" step="0.01" class="form-control" id="dist" name="distancia" oninput="pace()"></div>
                                <div class="col-md-6"><label class="small fw-bold">Altitud (m)</label><input type="number" class="form-control" name="altitud" placeholder="Desnivel"></div>
                            </div>
                            
                            <div class="alert alert-light border text-center fw-bold mb-3">
                                Ritmo Medio: <span id="paceRes" class="text-primary">--:--</span> min/km
                            </div>
                        </div>

                        <div id="sec-common" class="form-section">
                            <label class="form-label fw-bold mb-3">Sensación Percibida (Cansancio 1-10)</label>
                            <input type="range" class="form-range" min="1" max="10" id="feel" name="sensacion" value="5" oninput="document.getElementById('feelVal').innerText = this.value">
                            <div class="d-flex justify-content-between text-muted small px-1">
                                <span>Muy Fresco</span>
                                <span id="feelVal" class="badge bg-primary fs-6">5</span>
                                <span>Agotado</span>
                            </div>

                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" class="btn btn-save shadow">Guardar Actividad</button>
                                <a href="index.php" class="btn btn-link text-muted text-center text-decoration-none">Cancelar y Volver</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const exercises = { 
            pecho: ['Press Banca', 'Aperturas', 'Flexiones'], 
            espalda: ['Dominadas', 'Remo con Barra', 'Jalón al Pecho'],
            pierna: ['Sentadillas', 'Prensa', 'Zancadas']
        };

        function toggleModule() {
            const val = document.getElementById('mainCat').value;
            document.getElementById('sec-fuerza').style.display = val === 'fuerza' ? 'block' : 'none';
            document.getElementById('sec-cardio').style.display = (val === 'carrera' || val === 'caminata') ? 'block' : 'none';
            document.getElementById('sec-common').style.display = 'block';

            if(val === 'carrera' || val === 'caminata'){
                const title = document.getElementById('cardioTitle');
                if(val === 'carrera') { title.innerHTML = '<i class="fas fa-running me-2"></i>Módulo Running'; title.className = "mb-3 text-danger"; }
                else { title.innerHTML = '<i class="fas fa-walking me-2"></i>Módulo Caminata'; title.className = "mb-3 text-success"; }
            }
        }

        function loadEx() {
            const g = document.getElementById('group').value;
            const l = document.getElementById('exList');
            l.innerHTML = '<option value="">Seleccione...</option>'; 
            exercises[g]?.forEach(e => l.innerHTML += `<option value="${e}">${e}</option>`);
        }

        function pace() {
            const d = parseFloat(document.getElementById('dist').value);
            const t = parseFloat(document.getElementById('time').value);
            if(d && t) { 
                const p = t/d; 
                document.getElementById('paceRes').innerText = `${Math.floor(p)}:${Math.round((p%1)*60).toString().padStart(2,'0')}`; 
            }
        }

        document.getElementById('date').valueAsDate = new Date();
    </script>
</body>
</html>