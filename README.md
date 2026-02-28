# FitnessTracker - Proyecto 2º DAW

Este es el proyecto final para el módulo de Desarrollo de Aplicaciones Web.

## Funcionalidades del Sprint 3

El Sprint 3 incluye el diseño de base de datos relacional y lógica de entrenamientos y objetivos.

**FT-37: Consistencia en el cálculo de resultados.**
- **¿Qué datos se guardan en la base de datos?**
  Las calorías quemadas por entrenamiento se calculan en el servidor (`guardar_entreno.php`, `actualizar_entreno.php`) basándose en una fórmula fija antes de insertarlas/actualizarlas. Esto permite registrar el dato y visualizarlo ágilmente en gráficas sin recalcular por cada registro.
- **¿Qué datos se calculan al vuelo (on-the-fly)?**
  El progreso de los objetivos (mensuales o semanales) y las métricas acumuladas (en `estadisticas.php`) se calculan *on-the-fly* mediante agregaciones SQL cruzando la tabla `objetivos` y la tabla `entrenamientos`. Esto asegura que el progreso mostrado siempre esté 100% sincronizado con los entrenamientos actuales (al insertar, editar o borrar), previniendo inconsistencias en la base de datos.