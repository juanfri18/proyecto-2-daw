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
        .navbar-custom { background-color: var(--primary-color); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .navbar-brand { color: white !important; font-weight: 800; font-size: 1.4rem; letter-spacing: 0.5px;}
        .btn-menu { background: rgba(255,255,255,0.1); border: none; color: white; border-radius: 8px; padding: 8px 12px; transition: 0.3s; }
        .btn-menu:hover { background: rgba(255,255,255,0.2); transform: translateY(-1px); }
        /* Offcanvas Sidebar Premium */
        .offcanvas-custom { border-radius: 0 20px 20px 0; border: none; box-shadow: 5px 0 25px rgba(0,0,0,0.15); width: 280px !important; }
        .offcanvas-header-custom { background: linear-gradient(135deg, var(--primary-color), #1e3c72); color: white; border-radius: 0 20px 0 0; padding: 1.5rem; }
        .menu-link { padding: 12px 20px; border-radius: 12px; margin-bottom: 8px; color: #495057; font-weight: 600; transition: all 0.3s ease; display: flex; align-items: center; text-decoration: none;}
        .menu-link i { font-size: 1.2rem; width: 30px; text-align: center; }
        .menu-link:hover { background-color: #f8f9fa; color: var(--primary-color); transform: translateX(5px); }
        .menu-link.active { background-color: rgba(42, 81, 153, 0.1); color: var(--primary-color); }
        .menu-link.logout { color: #dc3545; margin-top: auto; }
        .menu-link.logout:hover { background-color: rgba(220, 53, 69, 0.1); color: #c82333; }
        
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .form-section { display: none; margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px; }
        .btn-save { background-color: var(--primary-color); color: white; font-weight: bold; padding: 12px; border-radius: 25px; border: none; width: 100%; transition: 0.3s; }
        .btn-save:hover { background-color: #1e3c72; }
        .admin-note { font-size: 0.8rem; color: #6c757d; font-style: italic; border-left: 3px solid #dee2e6; padding-left: 10px; margin-top: 10px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-custom sticky-top py-3">
        <div class="container-fluid px-4 align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn-menu me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <i class="fas fa-bars fs-5"></i>
                </button>
                <a class="navbar-brand mb-0" href="index.php"><i class="fas fa-heartbeat me-2"></i>FitnessTracker</a>
            </div>
            <a href="perfil.php" class="text-white text-decoration-none">
                <div class="d-flex align-items-center" style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 20px;">
                    <i class="fas fa-user-circle fs-5 me-2"></i>
                    <span class="fw-bold d-none d-sm-inline"><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Perfil'); ?></span>
                </div>
            </a>
        </div>
    </nav>

    <!-- Sidebar Offcanvas -->
    <div class="offcanvas offcanvas-start offcanvas-custom" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header offcanvas-header-custom">
            <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel"><i class="fas fa-heartbeat me-2"></i>Menú Principal</h5>
            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-4">
            <div class="mb-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; background: rgba(42, 81, 153, 0.1); color: var(--primary-color); font-size: 1.5rem;">
                    <i class="fas fa-user"></i>
                </div>
                <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></h6>
            </div>
            
            <a href="index.php" class="menu-link"><i class="fas fa-home text-primary"></i> Inicio</a>
            <a href="registro.php" class="menu-link active"><i class="fas fa-plus-circle text-success"></i> Registrar Actividad</a>
            <a href="estadisticas.php" class="menu-link"><i class="fas fa-chart-line text-info"></i> Estadísticas</a>
            <a href="perfil.php" class="menu-link"><i class="fas fa-user-cog text-secondary"></i> Mi Perfil</a>
            
            <a href="logout.php" class="menu-link logout mt-auto"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </div>

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