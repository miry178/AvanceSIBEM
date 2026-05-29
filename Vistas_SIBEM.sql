-- ── VISTAS ─────────────────────────────────────────────────────

CREATE VIEW vista_usuarios AS
SELECT
    u.idUsuario,
    u.nombre,
    u.correoInst,
    u.activo,
    r.idRol,
    r.descripcion   AS tipoPersona,
    al.idCarrera,
    c.descripcion    AS carrera,
    dc.idDivision,
    dv.descripcion   AS division
FROM Usuario u
LEFT JOIN RelRol     rr  ON u.idUsuario  = rr.idUsuario
LEFT JOIN Rol        r   ON rr.idRol     = r.idRol
LEFT JOIN Alumno     al  ON u.idUsuario  = al.idUsuario
LEFT JOIN Carrera    c   ON al.idCarrera = c.idCarrera
LEFT JOIN Docente    dc  ON u.idUsuario  = dc.idUsuario
LEFT JOIN Division   dv  ON dc.idDivision = dv.idDivision;



DROP VIEW vista_material;

CREATE VIEW vista_material AS
SELECT 
    m.idMaterial,
    m.titulo,
    m.autor,
    m.isbn,
    m.anioPublicacion,
    m.editorial,
    m.edicion,
    m.esPrestable,
    m.rutaPortada,
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

-- Vista principal de préstamos
CREATE VIEW vista_prestamos AS
SELECT 
    p.idPrestamo,
    p.idUsuario,
    u.nombre,
    u.correoInst,
    e.codigoEjemplar,
    m.titulo,
    m.autor,
    p.fechaPrestamo,
    p.fechaDevolucion,
    p.estado,
    TIMESTAMPDIFF(DAY, NOW(), p.fechaDevolucion) AS diasRestantes
FROM Prestamo p
JOIN Usuario  u ON p.idUsuario  = u.idUsuario
JOIN Ejemplar e ON p.idEjemplar = e.idEjemplar
JOIN Material m ON e.idMaterial = m.idMaterial;

-- Vista contadores para tarjetas
CREATE VIEW vista_contadores_prestamos AS
SELECT
    SUM(estado = 'activo') AS totalActivos,
    SUM(estado = 'vencido') AS totalVencidos,
    SUM(estado = 'activo' AND fechaDevolucion BETWEEN NOW() 
        AND DATE_ADD(NOW(), INTERVAL 3 DAY)) AS totalPorVencer
FROM Prestamo;

-- Vista ejemplares disponibles
CREATE VIEW vista_ejemplares_disponibles AS
SELECT
    e.idEjemplar,
    e.codigoEjemplar,
    e.estado,
    m.titulo,
    m.autor,
    m.isbn
FROM Ejemplar e
JOIN Material m ON e.idMaterial = m.idMaterial
WHERE e.estado = 'disponible';



