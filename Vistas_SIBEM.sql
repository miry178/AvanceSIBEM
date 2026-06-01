-- Vistas
CREATE VIEW vista_material AS
SELECT 
    m.idMaterial, m.titulo, m.autor, m.isbn, m.anioPublicacion,
    m.editorial, m.edicion, m.esPrestable, m.rutaPortada,
    tm.descripcion AS tipoMaterial,
    CASE 
        WHEN m.idArea    IS NOT NULL THEN a.descripcion
        WHEN m.idCarrera IS NOT NULL THEN c.descripcion
    END AS clasificacion,
    COUNT(e.idEjemplar) AS totalEjemplares,
    SUM(e.estado = 'disponible') AS disponibles
FROM Material m
LEFT JOIN TipoMaterial tm ON m.idTipoMaterial = tm.idTipoMaterial
LEFT JOIN Area          a  ON m.idArea        = a.idArea
LEFT JOIN Carrera       c  ON m.idCarrera     = c.idCarrera
LEFT JOIN Ejemplar      e  ON m.idMaterial    = e.idMaterial AND e.estado != 'baja'
GROUP BY m.idMaterial;

CREATE VIEW vista_usuarios AS
SELECT 
    u.idUsuario, u.nombre, u.correoInst, u.activo, u.idRol,
    CASE
        WHEN a.idUsuario IS NOT NULL THEN 'Alumno'
        WHEN d.idUsuario IS NOT NULL THEN 'Docente'
        ELSE r.descripcion
    END AS tipoPersona,
    a.idCarrera,
    ca.descripcion AS carrera,
    d.idDivision,
    di.descripcion AS division
FROM Usuario u
JOIN Rol r ON u.idRol = r.idRol
LEFT JOIN Alumno a ON u.idUsuario = a.idUsuario
LEFT JOIN Docente d ON u.idUsuario = d.idUsuario
LEFT JOIN Carrera ca ON a.idCarrera = ca.idCarrera
LEFT JOIN Division di ON d.idDivision = di.idDivision;

CREATE VIEW vista_prestamos AS
SELECT 
    p.idPrestamo,
    p.idUsuario,
    p.correoInst AS correoInst,
    u.nombre,
    m.titulo,
    p.fechaPrestamo,
    p.fechaDevolucion,
    p.estado,
    DATEDIFF(p.fechaDevolucion, NOW()) AS diasRestantes
FROM Prestamo p
JOIN Usuario  u  ON p.idUsuario   = u.idUsuario
JOIN Ejemplar e  ON p.idEjemplar  = e.idEjemplar
JOIN Material m  ON e.idMaterial  = m.idMaterial;

CREATE VIEW vista_ejemplares_disponibles AS
SELECT 
    e.idEjemplar,
    e.codigoEjemplar,
    e.estado,
    m.titulo,
    m.autor
FROM Ejemplar e
JOIN Material m ON e.idMaterial = m.idMaterial
WHERE e.estado = 'disponible';

CREATE VIEW vista_adeudos AS
SELECT 
    mu.idMulta, mu.idPrestamo, mu.monto, mu.pagada,
    p.idUsuario, p.fechaPrestamo, p.fechaDevolucion,
    u.nombre AS usuario,
    u.correoInst AS correo,
    CASE
        WHEN a.idUsuario IS NOT NULL THEN 'Alumno'
        WHEN d.idUsuario IS NOT NULL THEN 'Docente'
        ELSE r.descripcion
    END AS tipo,
    m.titulo AS libro
FROM Multa mu
JOIN Prestamo  p  ON mu.idPrestamo = p.idPrestamo
JOIN Usuario   u  ON p.idUsuario   = u.idUsuario
JOIN Rol       r  ON u.idRol       = r.idRol
JOIN Ejemplar  ej ON p.idEjemplar  = ej.idEjemplar
JOIN Material  m  ON ej.idMaterial = m.idMaterial
LEFT JOIN Alumno   a ON u.idUsuario = a.idUsuario
LEFT JOIN Docente  d ON u.idUsuario = d.idUsuario;

CREATE VIEW vista_contadores_prestamos AS
SELECT
    COUNT(*) AS total,
    SUM(estado = 'activo')   AS activos,
    SUM(estado = 'vencido')  AS vencidos,
    SUM(estado = 'devuelto') AS devueltos
FROM Prestamo;