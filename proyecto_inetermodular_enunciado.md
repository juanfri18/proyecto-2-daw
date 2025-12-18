# 🚀 PROPUESTAS DE PROYECTOS INTERMODULARES - 2º DAW


6. **FitnessTracker** - Aplicación de seguimiento deportivo básico

## 🎯 **Especificaciones Técnicas Unificadas**
- **Duración:** 7 meses (27 semanas, 5-7h/semana = 135-189h total)
- **Sprints:** 9 sprints de 3 semanas cada uno (15-21h por sprint)
- **Evolución tecnológica:**
  - **Sprints 1-2:** HTML5, CSS3, JavaScript, PHP vanilla, MySQL básico
  - **Sprints 3-4:** Introducción Laravel (instalación, rutas, vistas)
  - **Sprints 5-7:** Laravel intermedio (modelos, controladores, Eloquent)
  - **Sprints 8-9:** Laravel avanzado, Docker, despliegue
- **Base de datos:** MySQL con phpMyAdmin → Eloquent ORM
- **Control de versiones:** Git + GitHub (introducido gradualmente)

---

## 🎯 **Resultados de Aprendizaje (RA) y Criterios de Evaluación**

### **RA1. Identifica necesidades del sector productivo, relacionándolas con proyectos tipo que las puedan satisfacer.**

**Criterios de evaluación:**
a) Se han clasificado las empresas del sector por sus características organizativas y el tipo de producto o servicio que ofrecen.  
b) Se han caracterizado las empresas tipo indicando la estructura organizativa y las funciones de cada departamento.  
c) Se han identificado las necesidades más demandadas a las empresas.  
d) Se han valorado las oportunidades de negocio previsibles en el sector.  
e) Se ha identificado el tipo de proyecto requerido para dar respuesta a las demandas previstas.  
f) Se han determinado las características específicas requeridas al proyecto.  
g) Se han determinado las obligaciones fiscales, laborales y de prevención de riesgos y sus condiciones de aplicación.  
h) Se han identificado posibles ayudas o subvenciones para la incorporación de nuevas tecnologías de producción o de servicio que se proponen.  
i) Se ha elaborado el guión de trabajo que se va a seguir para la elaboración del proyecto.

---

### **RA2. Diseña proyectos relacionados con las competencias expresadas en el título, incluyendo y desarrollando las fases que lo componen.**

**Criterios de evaluación:**
a) Se ha recopilado información relativa a los aspectos que van a ser tratados en el proyecto.  
b) Se ha realizado el estudio de viabilidad técnica del mismo.  
c) Se han identificado las fases o partes que componen el proyecto y su contenido.  
d) Se han establecido los objetivos que se pretenden conseguir identificando su alcance.  
e) Se han previsto los recursos materiales y personales necesarios para realizarlo.  
f) Se ha realizado el presupuesto económico correspondiente.  
g) Se han identificado las necesidades de financiación para la puesta en marcha del mismo.  
h) Se ha definido y elaborado la documentación necesaria para su diseño.  
i) Se han identificado los aspectos que se deben controlar para garantizar la calidad del proyecto.


---

### **RA3. Planifica la ejecución del proyecto, determinando el plan de intervención y la documentación asociada.**

**Criterios de evaluación:**
a) Se han secuenciado las actividades ordenándolas en función de las necesidades de ejecución.  
b) Se han determinado los recursos y la logística necesarios para cada actividad.  
c) Se han identificado las necesidades de permisos y autorizaciones para llevar a cabo las actividades.  
d) Se han determinado los procedimientos de actuación o ejecución de las actividades.  
e) Se han identificado los riesgos inherentes a la ejecución definiendo el plan de prevención de riesgos y los medios y equipos necesarios.  
f) Se han planificado la asignación de recursos materiales y humanos y los tiempos de ejecución.  
g) Se ha hecho la valoración económica que da respuesta a las condiciones de la implementación.  
h) Se ha definido y elaborado la documentación necesaria para la implementación o ejecución.

---

### **RA4. Define los procedimientos para el seguimiento y control en la ejecución del proyecto, justificando la selección de variables e instrumentos empleados.**

**Criterios de evaluación:**
a) Se ha definido el procedimiento de evaluación de las actividades o intervenciones.  
b) Se han definido los indicadores de calidad para realizar la evaluación.  
c) Se ha definido el procedimiento para la evaluación de las incidencias que puedan presentarse durante la realización de las actividades, su posible solución y registro.  
d) Se ha definido el procedimiento para gestionar los posibles cambios en los recursos y en las actividades, incluyendo el sistema de registro de los mismos.  
e) Se ha definido y elaborado la documentación necesaria para la evaluación de las actividades y del proyecto.  
f) Se ha establecido el procedimiento para la participación en la evaluación de los usuarios o clientes y se han elaborado los documentos específicos.  
g) Se ha establecido un sistema para garantizar el cumplimiento del pliego de condiciones del proyecto cuando este existe.


---

## Proyecto 6 – FitnessTracker

**Descripción:** Aplicación web para el seguimiento básico de actividad física, donde usuarios pueden registrar entrenamientos, establecer objetivos y ver su progreso de forma visual.

**Objetivo:** Ayudar a las personas a mantener un registro de su actividad física y motivarse mediante el seguimiento de objetivos personales.

**Características funcionales principales:**
- Registro de usuarios con datos físicos básicos
- Logging de entrenamientos (tipo, duración, intensidad)
- Establecimiento de objetivos semanales/mensuales
- Dashboard con gráficos de progreso
- Calendario de entrenamientos
- Sistema de logros básicos
- Estadísticas personales
- Recordatorios de entrenamientos

**Tecnologías:**
- **Backend:** Laravel 10 con Carbon para fechas
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5, Chart.js
- **Base de datos:** MySQL 8.0
- **Despliegue:** Docker + Docker Compose
- **CI/CD:** GitHub Actions
- **Adicionales:** Chart.js para gráficos, Laravel Scheduler

### 🗓️ Sprint Planning - FitnessTracker (7 meses)

| Semana | Sprint | Objetivo principal | Tecnologías | Tareas del Backlog | Revisión / Entregable |
|--------|---------|--------------------|--------------------|--------------------|-----------------------|
| 1–3 | Sprint 1 | Análisis, diseño UI y modelo E/R | Figma/Balsamiq, Diagrama E/R | T1, T2, T3 | Wireframes, modelo E/R fitness completo |
| 4–6 | Sprint 2 | Maquetación frontend fitness | HTML5, CSS3, Bootstrap, Chart.js | T4, T5, T6 | Dashboard estático completo con gráficos JS |
| 7–9 | Sprint 3 | Backend PHP y base de datos | PHP, MySQL, PDO | T7, T8, T9 | CRUD entrenamientos funcional con métricas PHP |
| 10–12 | Sprint 4 | Migración a Laravel | Laravel, Blade, Eloquent | T10, T11, T12 | Proyecto Laravel con vistas Blade y modelos |
| 13–15 | Sprint 5 | Autenticación y objetivos | Laravel Auth, Validaciones | T13, T14, T15 | Sistema login con metas personales funcional |
| 16–18 | Sprint 6 | Calendario y visualización | FullCalendar.js, Laravel Collections | T16, T17, T18 | Calendario entrenamientos y progreso visual |
| 19–21 | Sprint 7 | Gamificación y recordatorios | Notifications, Tasks Scheduling | T19, T20, T21 | Logros, recordatorios automáticos, estadísticas |
| 22–24 | Sprint 8 | Optimización y testing | PHPUnit, Laravel Debugbar | T22, T23 | Tests métricas y rendimiento optimizado |
| 25–27 | Sprint 9 | Dockerización y despliegue | Docker, Docker Compose | T24 | App fitness contenerizada y desplegada |

### 📊 Relación Sprints con RA y Criterios de Evaluación - FitnessTracker

| Sprint | RA Principales | Criterios de Evaluación | Entregables Clave |
|--------|----------------|-------------------------|-------------------|
| **Sprint 1** | RA1 | e) Se han identificado las características específicas del proyecto, d) Se han valorado las oportunidades de negocio previsibles en el sector, i) Se ha elaborado el guión de trabajo que se va a seguir para la elaboración del proyecto | Análisis métricas fitness, wireframes UI, modelo E/R fitness |
| **Sprint 2** | RA1, RA2 | f) Se han determinado los aspectos que se deben incluir en el proyecto, h) Se ha definido y elaborado la documentación necesaria para su diseño | Maquetación HTML completa, estilos CSS responsivos, gráficos Chart.js |
| **Sprint 3** | RA2 | b) Se ha realizado el estudio de viabilidad técnica del mismo, c) Se han identificado las fases o partes que componen el proyecto y su contenido | Base datos MySQL, CRUD PHP entrenamientos, cálculos métricas PHP |
| **Sprint 4** | RA2, RA3 | a) Se ha recopilado información relativa a los aspectos que van a ser tratados en el proyecto, a) Se han secuenciado las actividades ordenándolas en función de las necesidades de ejecución | Proyecto Laravel configurado, vistas Blade, modelos Eloquent |
| **Sprint 5** | RA2, RA3 | d) Se han establecido los objetivos que se pretenden conseguir identificando su alcance, i) Se han identificado los aspectos que se deben controlar para garantizar la calidad del proyecto | Sistema autenticación, objetivos personales, validaciones |
| **Sprint 6** | RA2, RA3 | e) Se han previsto los recursos materiales y personales necesarios para realizarlo, b) Se han determinado los recursos y la logística necesarios para cada actividad | Calendario entrenamientos, visualización progreso, gráficos |
| **Sprint 7** | RA2, RA3 | f) Se ha realizado el presupuesto económico correspondiente, d) Se han determinado los procedimientos de actuación o ejecución de las actividades | Sistema logros, recordatorios automáticos, estadísticas avanzadas |
| **Sprint 8** | RA3, RA4 | h) Se ha definido y elaborado la documentación necesaria para la implementación o ejecución, a) Se ha definido el procedimiento de evaluación de las actividades o intervenciones | Tests PHPUnit, optimización cálculos, debugging |
| **Sprint 9** | RA4 | c) Se han aplicado indicadores de calidad a las actividades diseñadas en el proyecto, e) Se ha definido y elaborado la documentación necesaria para la evaluación de las actividades y del proyecto | Dockerfile, docker-compose.yml, app fitness desplegada productiva |

### 📋 Product Backlog Detallado - FitnessTracker

| ID | Tarea | Descripción Específica | Sprint | Estimación | Prioridad |
|----|-------|------------------------|--------|-------------|-----------|
| T1 | Análisis métricas fitness | Estudio métricas: tipos ejercicio, duración, calorías, objetivos | Sprint 1 | 4h | Alta |
| T2 | Wireframes app fitness | Diseño mockups: dashboard, registro entrenamiento, estadísticas | Sprint 1 | 6h | Alta |
| T3 | Modelo E/R fitness | Diagrama E/R: usuarios, entrenamientos, ejercicios, objetivos, métricas | Sprint 1 | 8h | Alta |
| T4 | Maquetación HTML dashboard | HTML semántico: dashboard principal, formulario entrenamiento, gráficos | Sprint 2 | 6h | Alta |
| T5 | Estilos CSS fitness | CSS Bootstrap: dashboard deportivo, cards estadísticas, responsive | Sprint 2 | 6h | Alta |
| T6 | JavaScript métricas | Cálculos JS: calorías, progreso, gráficos Chart.js, simulación datos | Sprint 2 | 6h | Alta |
| T7 | Base datos MySQL fitness | Creación tablas: usuarios, entrenamientos, ejercicios, objetivos, datos prueba | Sprint 3 | 6h | Alta |
| T8 | CRUD PHP entrenamientos | Scripts PHP para gestión entrenamientos: registrar, listar, editar | Sprint 3 | 6h | Alta |
| T9 | Cálculos métricas PHP | Lógica PHP: calorías quemadas, progreso objetivos, estadísticas | Sprint 3 | 6h | Alta |
| T10 | Instalación Laravel fitness | Setup Laravel, configuración, estructura proyecto fitness | Sprint 4 | 4h | Alta |
| T11 | Migración a Blade fitness | Convertir HTML a Blade, layouts dashboard, componentes métricas | Sprint 4 | 8h | Alta |
| T12 | Rutas y controladores fitness | Rutas RESTful, EntrenamientoController, ObjetivoController, MetricaController | Sprint 4 | 6h | Alta |
| T13 | Modelos Eloquent fitness | Modelos Entrenamiento, Ejercicio, Objetivo, Metrica con relaciones | Sprint 5 | 6h | Alta |
| T14 | Sistema objetivos Laravel | Lógica Eloquent: crear objetivos, calcular progreso, validaciones | Sprint 5 | 8h | Alta |
| T15 | Dashboard dinámico | Dashboard Ajax: actualización tiempo real, métricas actuales | Sprint 5 | 4h | Media |
| T16 | Autenticación fitness | Laravel Breeze, perfiles fitness: datos físicos, nivel actividad | Sprint 6 | 8h | Alta |
| T17 | Gráficos avanzados | Visualizaciones Chart.js: líneas progreso, barras comparativas | Sprint 6 | 6h | Media |
| T18 | Calendario entrenamientos | Vista calendario entrenamientos, planificación semanal | Sprint 6 | 4h | Media |
| T19 | Sistema logros gamificación | Logros automáticos: rachas, hitos, medallas, puntos motivación | Sprint 7 | 8h | Media |
| T20 | Estadísticas avanzadas | Análisis tendencias, comparativas periodos, reportes progreso | Sprint 7 | 8h | Media |
| T21 | Optimización UX fitness | Interface responsive, widgets personalizables, navegación rápida | Sprint 8 | 8h | Alta |
| T22 | Testing fitness | Tests: cálculos calorías, progreso objetivos, métricas | Sprint 8 | 8h | Alta |
| T23 | Docker app fitness | Containerización, docker-compose, configuración producción | Sprint 9 | 8h | Alta |
| T24 | Documentación fitness | Manual usuario, guía entrenamientos, plan uso app | Sprint 9 | 8h | Alta |

---

## 📊 Resumen Comparativo Simplificado

| Proyecto | Complejidad | CRUD Principal | Funcionalidad Destacada | Dificultad |
|----------|-------------|----------------|------------------------|------------|
| **TaskManager Pro** | Básica | Tareas/Proyectos | Asignaciones | ⭐⭐ |
| **BiblioTech** | Media | Libros/Préstamos | Cálculo fechas | ⭐⭐⭐ |
| **EventosLocal** | Media | Eventos/Inscripciones | API Maps | ⭐⭐⭐ |
| **TiendaOnline** | Media | Productos/Pedidos | Carrito compra | ⭐⭐⭐ |
| **RecetasFáciles** | Básica | Recetas/Comentarios | Red social | ⭐⭐ |
| **FitnessTracker** | Media | Entrenamientos | Gráficos | ⭐⭐⭐ |

<!-- ## 💡 Recomendaciones Simplificadas

### 🎯 **Para Empezar (Estudiantes Noveles)**
- **TaskManager Pro** o **RecetasFáciles**

### 🔧 **Nivel Intermedio**
- **BiblioTech**, **EventosLocal** o **FitnessTracker**

### 🚀 **Más Desafiante**
- **TiendaOnline** (por la lógica de e-commerce) -->





