-- ── TABLAS INDEPENDIENTES ──────────────────────────────────────
CREATE TABLE Rol (
    idRol INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(50) NOT NULL
);

CREATE TABLE Area (
    idArea INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE Carrera (
    idCarrera INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE Division (
    idDivision INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE TipoMaterial (
    idTipoMaterial INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(50) NOT NULL
);

CREATE TABLE Editorial (
    idEditorial INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE TipoServicio (
    idTipoServicio INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE Usuario (
    idUsuario VARCHAR(15) PRIMARY KEY,
    correoInst VARCHAR(150) NOT NULL,
    nombre VARCHAR(45) NOT NULL,
    activo ENUM('si', 'no') DEFAULT 'si',
    password VARCHAR(255) NULL,
    idRol INT NULL,
    FOREIGN KEY (idRol) REFERENCES Rol(idRol)
);

CREATE TABLE RelRol (
    idRelRol INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario VARCHAR(15) NOT NULL,
    correoInst VARCHAR(150),
    idRol INT NOT NULL,
    FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario),
    FOREIGN KEY (idRol) REFERENCES Rol(idRol)
);

CREATE TABLE Docente (
    idUsuario VARCHAR(15) PRIMARY KEY,
    idDivision INT NOT NULL,
    FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario),
    FOREIGN KEY (idDivision) REFERENCES Division(idDivision)
);

CREATE TABLE Alumno (
    idUsuario VARCHAR(15) PRIMARY KEY,
    idCarrera INT NOT NULL,
    FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario),
    FOREIGN KEY (idCarrera) REFERENCES Carrera(idCarrera)
);

CREATE TABLE Material (
    idMaterial INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(45) NOT NULL,
    autor VARCHAR(45) NOT NULL,
    isbn VARCHAR(45),
    anioPublicacion YEAR,
    editorial VARCHAR(100),
    idEditorial INT NULL,
    edicion VARCHAR(20),
    idTipoMaterial INT NOT NULL,
    idArea INT NULL,
    idCarrera INT NULL,
    rutaArchivo VARCHAR(200),
    rutaPortada VARCHAR(200),
    esPrestable ENUM('si', 'no') DEFAULT 'si',
    FOREIGN KEY (idTipoMaterial) REFERENCES TipoMaterial(idTipoMaterial),
    FOREIGN KEY (idEditorial) REFERENCES Editorial(idEditorial),
    FOREIGN KEY (idArea) REFERENCES Area(idArea),
    FOREIGN KEY (idCarrera) REFERENCES Carrera(idCarrera)
);

CREATE TABLE Ejemplar (
    idEjemplar INT PRIMARY KEY AUTO_INCREMENT,
    codigoEjemplar VARCHAR(50) NOT NULL,
    idMaterial INT NOT NULL,
    estado ENUM('disponible', 'prestado', 'baja') DEFAULT 'disponible',
    FOREIGN KEY (idMaterial) REFERENCES Material(idMaterial)
);

CREATE TABLE ReglasPrestamo (
    idReglasPrestamo INT PRIMARY KEY AUTO_INCREMENT,
    idRol INT NOT NULL,
    diasPrestamo INT NOT NULL,
    maxPrestamo INT NOT NULL,
    renovaciones INT DEFAULT 0,
    precioMulta DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (idRol) REFERENCES Rol(idRol)
);

CREATE TABLE Prestamo (
    idPrestamo INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario VARCHAR(15) NOT NULL,
    correoInst VARCHAR(150),
    idEjemplar INT NOT NULL,
    fechaPrestamo DATETIME DEFAULT CURRENT_TIMESTAMP,
    fechaDevolucion DATETIME NOT NULL,
    estado ENUM('activo', 'devuelto', 'vencido') DEFAULT 'activo',
    FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario),
    FOREIGN KEY (idEjemplar) REFERENCES Ejemplar(idEjemplar)
);

CREATE TABLE Multa (
    idMulta INT PRIMARY KEY AUTO_INCREMENT,
    idPrestamo INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fechaGenerada DATETIME DEFAULT CURRENT_TIMESTAMP,
    fechaPago DATETIME,
    pagada ENUM('si', 'no') DEFAULT 'no',
    FOREIGN KEY (idPrestamo) REFERENCES Prestamo(idPrestamo)
);

CREATE TABLE Servicio (
    idServicio INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario VARCHAR(15) NOT NULL,
    idTipoServicio INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    descripcion VARCHAR(200),
    FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario),
    FOREIGN KEY (idTipoServicio) REFERENCES TipoServicio(idTipoServicio)
);

CREATE TABLE Permiso (
    idPermiso INT PRIMARY KEY AUTO_INCREMENT,
    modulo VARCHAR(50) NOT NULL,
    accion VARCHAR(50) NOT NULL,
    descripcion VARCHAR(150)
);

CREATE TABLE RolPermiso (
    idRol INT NOT NULL,
    idPermiso INT NOT NULL,
    PRIMARY KEY (idRol, idPermiso),
    FOREIGN KEY (idRol) REFERENCES Rol(idRol),
    FOREIGN KEY (idPermiso) REFERENCES Permiso(idPermiso)
);

-- ── DATOS ──────────────────────────────────────────────────────
-- ── ROLES ─────────────────────────────────────────────────────
INSERT INTO Rol VALUES (1,'Administrador'),(2,'Encargado'),(3,'Invitado');

-- ── ÁREAS ─────────────────────────────────────────────────────
INSERT INTO Area (descripcion) VALUES 
('Ciencias'),('Ingeniería'),('Programacion'),('Economia');

-- ── CARRERAS ──────────────────────────────────────────────────
INSERT INTO Carrera (descripcion) VALUES
('Sistemas Computacionales'),('Electromecanica'),('Administracion'),
('Arquitectura'),('Gestión Empresarial'),('Industrial'),('Gastronomia');

-- ── DIVISIONES ────────────────────────────────────────────────
INSERT INTO Division (descripcion) VALUES
('División de Ingeniería'),('División de Ciencias Básicas'),('División de Administración');

-- ── TIPOS DE MATERIAL ─────────────────────────────────────────
INSERT INTO TipoMaterial (descripcion) VALUES
('Libro'),('Revista'),('Tesis'),('Residencia'),('Multimedia');

-- ── EDITORIALES ───────────────────────────────────────────────
INSERT INTO Editorial (nombre) VALUES
('McGraw-Hill'),('Pearson'),('Editorial Medica Panamericana'),
('Thomson International'),('Editorial Reverte'),('Addison Wesley'),
('Prentice Hall'),('Paraninfo'),('Alfaguara'),('Salamandra'),('Peralta');

-- ── TIPOS DE SERVICIO ─────────────────────────────────────────
INSERT INTO TipoServicio (descripcion) VALUES
('Consulta en sala'),('Uso de computadoras'),('Asesoría bibliográfica'),
('Préstamo interno'),('Uso de sala de estudio');

-- ── PERMISOS ──────────────────────────────────────────────────
INSERT INTO Permiso (modulo, accion, descripcion) VALUES
('catalogo',     'agregar',    'Agregar material al catálogo'),
('catalogo',     'editar',     'Editar material del catálogo'),
('catalogo',     'eliminar',   'Eliminar material del catálogo'),
('prestamos',    'agregar',    'Registrar nuevo préstamo'),
('prestamos',    'devolver',   'Marcar préstamo como devuelto'),
('prestamos',    'historial',  'Ver historial de préstamos'),
('usuarios',     'agregar',    'Agregar nuevo usuario'),
('usuarios',     'editar',     'Editar usuario existente'),
('usuarios',     'desactivar', 'Desactivar usuario'),
('adeudos',      'ver',        'Ver adeudos'),
('adeudos',      'pago',       'Registrar pago de multa'),
('adeudos',      'condonar',   'Condonar multa de usuario'),
('estadisticas', 'ver',        'Ver estadísticas'),
('roles',        'agregar',    'Agregar nuevo rol'),
('roles',        'editar',     'Editar rol existente'),
('roles',        'eliminar',   'Eliminar rol');

-- ── ROL PERMISO ───────────────────────────────────────────────
INSERT INTO RolPermiso (idRol, idPermiso) SELECT 1, idPermiso FROM Permiso;
INSERT INTO RolPermiso (idRol, idPermiso) SELECT 2, idPermiso FROM Permiso
WHERE NOT (modulo = 'usuarios' AND accion = 'desactivar') AND modulo != 'roles';
DELETE FROM RolPermiso WHERE idRol = 2 
AND idPermiso = (SELECT idPermiso FROM Permiso WHERE modulo = 'adeudos' AND accion = 'condonar');
INSERT INTO RolPermiso (idRol, idPermiso) SELECT 3, idPermiso FROM Permiso WHERE accion = 'ver';

-- ── REGLAS DE PRÉSTAMO ────────────────────────────────────────
INSERT INTO ReglasPrestamo (idRol, diasPrestamo, maxPrestamo, renovaciones, precioMulta) VALUES
(1, 5, 5, 3, 25.00),
(2, 3, 5, 3, 20.00),
(3, 2, 2, 2, 25.00);

-- ── USUARIOS ──────────────────────────────────────────────────
INSERT INTO Usuario (idUsuario, correoInst, nombre, activo, idRol) VALUES
('ADM001',    'mirandatalamantes9@gmail.com',        'Admin Miry',                        'si', 1),
('ENC001',    'ENC001@cdconstitucion.tecnm.mx',      'Jorge',                             'si', 2),
('ENC002',    'maricruztalamantes2@gmail.com',       'Encargado Prueba',                  'si', 2),
('DOC001',    'D001@cdconstitucion.tecnm.mx',        'Isaías Terán Gomez',                'si', 3),
('DOC002',    'D002@cdconstitucion.tecnm.mx',        'Alejandro Urias Castro',            'si', 3),
('DOC003',    'D003@cdconstitucion.tecnm.mx',        'Luz Elena Butterfield',             'si', 3),
('233110169', 'L233110169@cdconstitucion.tecnm.mx',  'Ernestina Murillo Lara',            'si', 3),
('233110173', 'L233110173@cdconstitucion.tecnm.mx',  'Maria Murillo Larrinaga',           'si', 3),
('233110178', 'L233110178@cdconstitucion.tecnm.mx',  'Alan Collins Navarro',              'si', 3),
('233110179', 'L233110179@cdconstitucion.tecnm.mx',  'Miranda Maricruz Talamantes Meza',  'si', 3),
('233110180', 'L233110180@cdconstitucion.tecnm.mx',  'Carlos Pérez',                      'si', 3),
('233110181', 'L233110181@cdconstitucion.tecnm.mx',  'Lennin Rafael Martinez Camargo',    'si', 3),
('233110182', 'L233110182@cdconstitucion.tecnm.mx',  'Samuel Santos Lopez',               'si', 3),
('233110184', 'L233110184@cdconstitucion.tecnm.mx',  'Geovanny Flores Gomez',             'si', 3);

-- ── RELROL ────────────────────────────────────────────────────
INSERT INTO RelRol (idUsuario, correoInst, idRol) SELECT idUsuario, correoInst, idRol FROM Usuario;

-- ── ALUMNOS ───────────────────────────────────────────────────
INSERT INTO Alumno (idUsuario, idCarrera) VALUES
('233110169', 1),('233110173', 1),('233110178', 1),
('233110179', 1),('233110180', 1),('233110181', 3),
('233110182', 2),('233110184', 1);

-- ── DOCENTES ──────────────────────────────────────────────────
INSERT INTO Docente (idUsuario, idDivision) VALUES
('DOC001', 1),('DOC002', 3),('DOC003', 2);

-- ── MATERIALES ────────────────────────────────────────────────
INSERT INTO Material (titulo, autor, isbn, anioPublicacion, idEditorial, edicion, idTipoMaterial, idArea, idCarrera, esPrestable) VALUES
('Fundamentos de Programación',        'Joyanes Aguilar, Luis',            '9786071514684', 2020, 1, '10ma', 1, 3,    NULL, 'si'),
('Física Universitaria',               'Sears, Zemansky, Young, Freedman', '9786073221245', 2013, 2, '13va', 1, 1,    NULL, 'si'),
('Química General',                    'Petrucci, Ralph; Rodriguez, Juan', '9788490355336', 2017, 2, '10ma', 1, 1,    NULL, 'si'),
('Biología',                           'Campbell, Neil Alexander',         '9788479039981', 2007, 3, '7ma',  1, 1,    NULL, 'si'),
('Cálculo Diferencial e Integral',     'Stewart, James',                   '9789706865441', 2006, 4, '10ma', 1, 1,    NULL, 'si'),
('Electricidad Industrial',            'Dawes, Chester L.',                '9788429130201', 1966, 5, '1ra',  1, 2,    NULL, 'si'),
('Mecánica de Materiales',             'Beer, Johnston, DeWolf, Mazurek',  '958600127X',    1993, 1, '3ra',  1, 2,    NULL, 'si'),
('Termodinámica',                      'Çengel, Yunus A.',                 '9789701009116', 1997, 1, '2da',  1, 2,    NULL, 'si'),
('Mecánica Vectorial para Ingenieros', 'Hibbeler, R. C.',                  '9702605016',    2008, 2, '10ma', 1, 2,    NULL, 'si'),
('Estructuras de Datos en Java',       'Weiss, Mark Allen',                '9788478290352', 2000, 6, '10ma', 1, 3,    NULL, 'si'),
('Bases de Datos',                     'Mora Rioja, Arturo',               '9788490770429', 2015, 8, '10ma', 1, 3,    NULL, 'si'),
('Ingeniería de Software',             'Sommerville, Ian',                 '9789702602064', 2002, 6, '6ta',  1, 3,    NULL, 'si'),
('Redes de Computadoras',              'Tanenbaum, Andrew S.',             '9688809586',    1998, 7, '3ra',  1, 3,    NULL, 'si'),
('Don Quijote de la Mancha',           'Cervantes Saavedra, Miguel de',    '9788418797451', 1910, 11,'1ra',  1, NULL, NULL, 'si'),
('Revista de Ingeniería Mecánica',     'ITSCC',                            NULL,            2023, 2, '1ra',  2, 2,    NULL, 'no'),
('Revista de Tecnología e Innovación', 'ITSCC',                            NULL,            2022, 2, '2da',  2, 3,    NULL, 'no'),
('Revista de Ciencias Exactas',        'ITSCC',                            NULL,            2023, 2, '1ra',  2, 1,    NULL, 'no'),
('Tesis: Control de Inventario',        'Pérez López, Juan Carlos',     NULL, 2022, 2, '1ra', 3, NULL, 1, 'no'),
('Tesis: Automatización Industrial',    'García Ramírez, María Elena',  NULL, 2023, 2, '1ra', 3, NULL, 2, 'no'),
('Tesis: Análisis Financiero PYMES',    'Rodríguez Torres, Ana Sofía',  NULL, 2022, 2, '1ra', 3, NULL, 3, 'no'),
('Residencia: App Web Biblioteca',      'Martínez Soto, Luis Fernando', NULL, 2023, 2, '1ra', 4, NULL, 1, 'no'),
('Residencia: Mantenimiento Industrial','Flores Mendoza, Carlos',       NULL, 2022, 2, '1ra', 4, NULL, 2, 'no'),
('Residencia: Plan de Negocios',        'López Vega, Daniela',          NULL, 2023, 2, '1ra', 4, NULL, 7, 'no'),
('Introducción a la Programación - Video',          'ITSCC',                        NULL, 2023, 2, '1ra', 5, NULL, NULL, 'no'),
('Matemáticas Básicas - Interactivo',               'ITSCC',                        NULL, 2022, 2, '1ra', 5, NULL, NULL, 'no'),
('Inglés Técnico para Ingenieros',                  'ITSCC',                        NULL, 2023, 2, '1ra', 5, NULL, NULL, 'no');

-- ── EJEMPLARES ────────────────────────────────────────────────
INSERT INTO Ejemplar (codigoEjemplar, idMaterial, estado) VALUES
('LIB-1-1',  1,  'disponible'), ('LIB-1-2',  1,  'disponible'),
('LIB-2-1',  2,  'disponible'), ('LIB-2-2',  2,  'disponible'),
('LIB-3-1',  3,  'disponible'), ('LIB-3-2',  3,  'disponible'),
('LIB-4-1',  4,  'disponible'), ('LIB-4-2',  4,  'disponible'),
('LIB-5-1',  5,  'disponible'), ('LIB-5-2',  5,  'disponible'),
('LIB-6-1',  6,  'disponible'), ('LIB-6-2',  6,  'disponible'),
('LIB-7-1',  7,  'disponible'), ('LIB-7-2',  7,  'disponible'),
('LIB-8-1',  8,  'disponible'), ('LIB-8-2',  8,  'disponible'),
('LIB-9-1',  9,  'disponible'), ('LIB-9-2',  9,  'disponible'),
('LIB-10-1', 10, 'disponible'), ('LIB-10-2', 10, 'disponible'),
('LIB-11-1', 11, 'disponible'), ('LIB-11-2', 11, 'disponible'),
('LIB-12-1', 12, 'disponible'), ('LIB-12-2', 12, 'disponible'),
('LIB-13-1', 13, 'disponible'), ('LIB-13-2', 13, 'disponible'),
('LIB-14-1', 14, 'disponible'), ('LIB-14-2', 14, 'disponible'),
('REV-15-1', 15, 'disponible'),
('REV-16-1', 16, 'disponible'),
('REV-17-1', 17, 'disponible'),
('TES-18-1', 18, 'disponible'),
('TES-19-1', 19, 'disponible'),
('TES-20-1', 20, 'disponible'),
('RES-21-1', 21, 'disponible'),
('RES-22-1', 22, 'disponible'),
('RES-23-1', 23, 'disponible'),
('MUL-24-1', 24, 'disponible'),
('MUL-25-1', 25, 'disponible'),
('MUL-26-1', 26, 'disponible');


-- ── PRÉSTAMOS DEVUELTOS (historial) ───────────────────────────
INSERT INTO Prestamo (idUsuario, correoInst, idEjemplar, fechaPrestamo, fechaDevolucion, estado) VALUES
('233110169', 'L233110169@cdconstitucion.tecnm.mx', 1,  '2026-01-05', '2026-01-07', 'devuelto'),
('233110173', 'L233110173@cdconstitucion.tecnm.mx', 3,  '2026-01-10', '2026-01-12', 'devuelto'),
('233110178', 'L233110178@cdconstitucion.tecnm.mx', 5,  '2026-01-15', '2026-01-17', 'devuelto'),
('233110179', 'L233110179@cdconstitucion.tecnm.mx', 7,  '2026-02-03', '2026-02-05', 'devuelto'),
('233110180', 'L233110180@cdconstitucion.tecnm.mx', 9,  '2026-02-10', '2026-02-12', 'devuelto'),
('233110181', 'L233110181@cdconstitucion.tecnm.mx', 11, '2026-02-18', '2026-02-20', 'devuelto'),
('DOC001',    'D001@cdconstitucion.tecnm.mx',       13, '2026-02-20', '2026-02-25', 'devuelto'),
('233110182', 'L233110182@cdconstitucion.tecnm.mx', 2,  '2026-03-02', '2026-03-04', 'devuelto'),
('233110184', 'L233110184@cdconstitucion.tecnm.mx', 4,  '2026-03-08', '2026-03-10', 'devuelto'),
('233110169', 'L233110169@cdconstitucion.tecnm.mx', 6,  '2026-03-15', '2026-03-17', 'devuelto'),
('DOC002',    'D002@cdconstitucion.tecnm.mx',       8,  '2026-03-20', '2026-03-22', 'devuelto'),
('233110173', 'L233110173@cdconstitucion.tecnm.mx', 10, '2026-04-01', '2026-04-03', 'devuelto'),
('233110178', 'L233110178@cdconstitucion.tecnm.mx', 12, '2026-04-05', '2026-04-07', 'devuelto'),
('233110179', 'L233110179@cdconstitucion.tecnm.mx', 14, '2026-04-12', '2026-04-14', 'devuelto'),
('233110180', 'L233110180@cdconstitucion.tecnm.mx', 16, '2026-04-18', '2026-04-20', 'devuelto'),
('233110181', 'L233110181@cdconstitucion.tecnm.mx', 18, '2026-05-02', '2026-05-04', 'devuelto'),
('DOC001',    'D001@cdconstitucion.tecnm.mx',       20, '2026-05-06', '2026-05-08', 'devuelto'),
('233110182', 'L233110182@cdconstitucion.tecnm.mx', 22, '2026-05-10', '2026-05-12', 'devuelto'),
('233110184', 'L233110184@cdconstitucion.tecnm.mx', 24, '2026-05-15', '2026-05-17', 'devuelto'),
('233110169', 'L233110169@cdconstitucion.tecnm.mx', 26, '2026-05-20', '2026-05-22', 'devuelto');

-- ── PRÉSTAMOS ACTIVOS Y VENCIDOS ──────────────────────────────
INSERT INTO Prestamo (idUsuario, correoInst, idEjemplar, fechaPrestamo, fechaDevolucion, estado) VALUES
('233110173', 'L233110173@cdconstitucion.tecnm.mx', 1,  '2026-05-20', '2026-05-25', 'vencido'),
('233110178', 'L233110178@cdconstitucion.tecnm.mx', 3,  '2026-05-22', '2026-05-28', 'vencido'),
('233110179', 'L233110179@cdconstitucion.tecnm.mx', 5,  '2026-05-28', '2026-06-05', 'activo'),
('233110180', 'L233110180@cdconstitucion.tecnm.mx', 7,  '2026-05-29', '2026-06-07', 'activo'),
('DOC003',    'D003@cdconstitucion.tecnm.mx',       9,  '2026-05-27', '2026-06-03', 'activo');

-- Actualizar ejemplares prestados
UPDATE Ejemplar SET estado = 'prestado' WHERE idEjemplar IN (1, 3, 5, 7, 9);

-- ── MULTAS ────────────────────────────────────────────────────
INSERT INTO Multa (idPrestamo, monto, pagada) VALUES
(56, 125.00, 'no'),
(57, 175.00, 'no');

SELECT idPrestamo, idUsuario, estado FROM Prestamo WHERE estado = 'vencido';


SELECT * FROM Carrera;
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM Multa;
DELETE FROM Servicio;
DELETE FROM Prestamo;
DELETE FROM Ejemplar;
DELETE FROM Material;
DELETE FROM Alumno;
DELETE FROM Docente;
DELETE FROM RelRol;
DELETE FROM Usuario;
DELETE FROM RolPermiso;
DELETE FROM ReglasPrestamo;
DELETE FROM Permiso;
DELETE FROM Rol;
DELETE FROM Editorial;
DELETE FROM TipoMaterial;
DELETE FROM TipoServicio;
DELETE FROM Area;
DELETE FROM Division;
DELETE FROM Carrera;
SET FOREIGN_KEY_CHECKS = 1;

ALTER TABLE Carrera AUTO_INCREMENT = 1;
ALTER TABLE Area AUTO_INCREMENT = 1;
ALTER TABLE Division AUTO_INCREMENT = 1;
ALTER TABLE TipoMaterial AUTO_INCREMENT = 1;
ALTER TABLE Editorial AUTO_INCREMENT = 1;
ALTER TABLE TipoServicio AUTO_INCREMENT = 1;
ALTER TABLE Permiso AUTO_INCREMENT = 1;
ALTER TABLE ReglasPrestamo AUTO_INCREMENT = 1;
ALTER TABLE Usuario AUTO_INCREMENT = 1;
ALTER TABLE RelRol AUTO_INCREMENT = 1;
ALTER TABLE Material AUTO_INCREMENT = 1;
ALTER TABLE Ejemplar AUTO_INCREMENT = 1;
ALTER TABLE Rol AUTO_INCREMENT = 1;




use biblioteca;
SELECT codigoEjemplar, estado FROM Ejemplar WHERE estado = 'disponible';
