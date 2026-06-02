DROP DATABASE IF EXISTS biblioteca;
CREATE DATABASE biblioteca CHARACTER SET utf8 COLLATE utf8_general_ci;
USE biblioteca;

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

-- ── TABLA USUARIO ──────────────────────────────────────────────
CREATE TABLE Usuario (
    idUsuario VARCHAR(15) PRIMARY KEY,
    correoInst VARCHAR(150) NOT NULL,
    nombre VARCHAR(45) NOT NULL,
    activo ENUM('si', 'no') DEFAULT 'si',
    password VARCHAR(255) NULL,
    idRol INT NULL,
    FOREIGN KEY (idRol) REFERENCES Rol(idRol)
);

-- ── TABLAS QUE DEPENDEN DE USUARIO ────────────────────────────
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

-- ── TABLA MATERIAL ─────────────────────────────────────────────
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

-- ── TABLA EJEMPLAR ─────────────────────────────────────────────
CREATE TABLE Ejemplar (
    idEjemplar INT PRIMARY KEY AUTO_INCREMENT,
    codigoEjemplar VARCHAR(50) NOT NULL,
    idMaterial INT NOT NULL,
    estado ENUM('disponible', 'prestado', 'baja') DEFAULT 'disponible',
    FOREIGN KEY (idMaterial) REFERENCES Material(idMaterial)
);

-- ── TABLA REGLASPRESTAMO ───────────────────────────────────────
CREATE TABLE ReglasPrestamo (
    idReglasPrestamo INT PRIMARY KEY AUTO_INCREMENT,
    idRol INT NOT NULL,
    diasPrestamo INT NOT NULL,
    maxPrestamo INT NOT NULL,
    renovaciones INT DEFAULT 0,
    precioMulta DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (idRol) REFERENCES Rol(idRol)
);

-- ── TABLA PRESTAMO ─────────────────────────────────────────────
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

-- ── TABLA MULTA ────────────────────────────────────────────────
CREATE TABLE Multa (
    idMulta INT PRIMARY KEY AUTO_INCREMENT,
    idPrestamo INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fechaGenerada DATETIME DEFAULT CURRENT_TIMESTAMP,
    fechaPago DATETIME,
    pagada ENUM('si', 'no') DEFAULT 'no',
    FOREIGN KEY (idPrestamo) REFERENCES Prestamo(idPrestamo)
);

-- ── TABLA SERVICIO ─────────────────────────────────────────────
CREATE TABLE Servicio (
    idServicio INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario VARCHAR(15) NOT NULL,
    idTipoServicio INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    descripcion VARCHAR(200),
    FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario),
    FOREIGN KEY (idTipoServicio) REFERENCES TipoServicio(idTipoServicio)
);

-- ── TABLAS DE PERMISOS ─────────────────────────────────────────
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

-- ══════════════════════════════════════════════════════════════
-- DATOS
-- ══════════════════════════════════════════════════════════════

-- Roles
INSERT INTO Rol VALUES (1,'Administrador'),(2,'Encargado'),(3,'Invitado');

-- Áreas
INSERT INTO Area (descripcion) VALUES ('Ciencias'),('Ingeniería'),('Programacion'),('Economia');

-- Carreras
INSERT INTO Carrera (descripcion) VALUES
('Sistemas'),('Electromecanica'),('Administracion'),
('Arquitectura'),('Gestión Empresarial'),('Industrial'),('Gatronomia');

-- Divisiones
INSERT INTO Division (descripcion) VALUES
('División de Ingeniería'),('División de Ciencias Básicas'),('División de Administración');

-- Tipos de material
INSERT INTO TipoMaterial (descripcion) VALUES
('Libro'),('Revista'),('Tesis'),('Residencia'),('Multimedia');

-- Editoriales
INSERT INTO Editorial (nombre) VALUES
('Cengage Learning'),('ITSC'),('McGraw Hill'),
('Pearson'),('Reilly'),('Sams Publishing'),('Sudamericana');

-- Tipos de servicio
INSERT INTO TipoServicio (descripcion) VALUES
('Consulta en sala'),('Uso de computadoras'),('Asesoría bibliográfica'),
('Préstamo interno'),('Uso de sala de estudio');

-- Permisos
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

-- RolPermiso
-- Administrador: todos los permisos
INSERT INTO RolPermiso (idRol, idPermiso)
SELECT 1, idPermiso FROM Permiso;

-- Encargado: todo menos desactivar usuarios y módulo roles
INSERT INTO RolPermiso (idRol, idPermiso)
SELECT 2, idPermiso FROM Permiso
WHERE NOT (modulo = 'usuarios' AND accion = 'desactivar')
AND modulo != 'roles';

DELETE FROM RolPermiso 
WHERE idRol = 2 
AND idPermiso = (SELECT idPermiso FROM Permiso WHERE modulo = 'adeudos' AND accion = 'condonar');

-- Invitado: solo ver estadísticas
INSERT INTO RolPermiso (idRol, idPermiso)
SELECT 3, idPermiso FROM Permiso
WHERE accion = 'ver';

-- Reglas de préstamo por rol
INSERT INTO ReglasPrestamo (idRol, diasPrestamo, maxPrestamo, renovaciones, precioMulta) VALUES
(1, 5, 5, 3, 25.00),
(2, 3, 5, 3, 20.00),
(3, 2, 2, 2, 25.00);

-- Usuarios (solo un Administrador)
INSERT INTO Usuario (idUsuario, correoInst, nombre, activo, idRol) VALUES
('ADM001',    'mirandatalamantes9@gmail.com',        'Admin Miry',     'si', 1),
('ENC001',    'ENC001@cdconstitucion.tecnm.mx',      'Jorge',          'si', 2),
('ENC002',    'maricruztalamantes2@gmail.com',       'Encargado Prueba','si', 2),
('DOC001',    'D001@cdconstitucion.tecnm.mx',        'Prof. García',   'si', 3),
('DOC002',    'D002@cdconstitucion.tecnm.mx',        'Prof. Ramírez',  'si', 3),
('233110177', 'L233110177@cdconstitucion.tecnm.mx',  'Arturo Higuera', 'si', 3),
('233110179', 'L233110179@cdconstitucion.tecnm.mx',  'Miranda',        'si', 3),
('233110180', 'L233110180@cdconstitucion.tecnm.mx',  'Carlos Pérez',   'si', 3),
('233110181', 'L233110181@cdconstitucion.tecnm.mx',  'Ana López',      'si', 3),
('233110182', 'L233110182@cdconstitucion.tecnm.mx',  'Luis Martínez',  'si', 3),
('INV001',    'maricruztalamantes2@gmail.com',       'Invitado Prueba','si', 3);

-- RelRol
INSERT INTO RelRol (idUsuario, correoInst, idRol)
SELECT idUsuario, correoInst, idRol FROM Usuario;

INSERT INTO RelRol (idUsuario, correoInst, idRol)
VALUES ('ENC002', 'maricruztalamantes2@gmail.com', 2);
-- Alumnos
INSERT INTO Alumno (idUsuario, idCarrera) VALUES
('233110177', 1),
('233110179', 1),
('233110180', 1),
('233110181', 3),
('233110182', 2);

-- Docentes
INSERT INTO Docente (idUsuario, idDivision) VALUES
('DOC001', 1),
('DOC002', 3);

-- Material
INSERT INTO Material (titulo, autor, isbn, anioPublicacion, idEditorial, edicion, idTipoMaterial, idArea, idCarrera, esPrestable) VALUES
('Cien años de soledad',    'Gabriel García Márquez', '978-0307474728', 1967, 7, '1ra', 1, 1, NULL, 'si'),
('Programación en Python',  'Mark Lutz',              '978-1449355739', 2013, 5, '5ta', 1, 3, NULL, 'si'),
('Cálculo Diferencial',     'James Stewart',          '978-6074816211', 2012, 1, '7ma', 1, 1, NULL, 'si'),
('Administración Moderna',  'Harold Koontz',          '978-6071509949', 2012, 3, '14va',1, 4, NULL, 'si'),
('Estructuras de Datos',    'Robert Lafore',          '978-0672324536', 2002, 6, '2da', 1, 3, NULL, 'si'),
('Revista de Ingeniería',   'ITSC',                   NULL,             2023, 2, '1ra', 2, 2, NULL, 'no'),
('Tesis: IA en Educación',  'Pedro Ruiz',             NULL,             2022, 2, '1ra', 3, NULL, 1,  'no'),
('Electricidad Industrial', 'Stephen Chapman',        '978-0073380582', 2011, 3, '4ta', 1, 2, NULL, 'si'),
('Base de Datos',           'Ramez Elmasri',          '978-0136086208', 2010, 4, '6ta', 1, 3, NULL, 'si'),
('Contabilidad General',    'Horngren Charles',       '978-6073221023', 2015, 4, '9na', 1, 4, NULL, 'si');

-- Ejemplares
INSERT INTO Ejemplar (codigoEjemplar, idMaterial, estado) VALUES
('LIB-1-1', 1, 'disponible'),('LIB-1-2', 1, 'disponible'),('LIB-1-3', 1, 'disponible'),
('LIB-2-1', 2, 'disponible'),('LIB-2-2', 2, 'disponible'),
('LIB-3-1', 3, 'disponible'),('LIB-3-2', 3, 'disponible'),
('LIB-4-1', 4, 'disponible'),('LIB-4-2', 4, 'disponible'),
('LIB-5-1', 5, 'disponible'),('LIB-5-2', 5, 'disponible'),
('REV-6-1', 6, 'disponible'),('TES-7-1', 7, 'disponible'),
('LIB-8-1', 8, 'disponible'),('LIB-9-1', 9, 'disponible'),
('LIB-10-1',10,'disponible');

-- Servicios
INSERT INTO Servicio (idUsuario, idTipoServicio, fecha, descripcion) VALUES
('233110179', 1, '2026-01-06 10:00:00', 'Consulta en sala'),
('233110179', 3, '2026-03-02 09:30:00', 'Asesoría bibliográfica'),
('233110180', 2, '2026-01-11 11:00:00', 'Uso de computadora'),
('233110181', 1, '2026-02-04 09:00:00', 'Consulta en sala'),
('233110182', 5, '2026-02-19 14:00:00', 'Uso de sala de estudio'),
('DOC001',    3, '2026-03-16 11:00:00', 'Asesoría a estudiantes'),
('ADM001',    2, '2026-03-04 10:15:00', 'Uso de sistema administrativo'),
('233110177', 1, '2026-03-03 10:30:00', 'Préstamo interno'),
('233110179', 5, '2026-04-02 15:00:00', 'Uso de sala de estudio'),
('233110180', 1, '2026-04-06 09:30:00', 'Consulta en sala'),
('233110181', 2, '2026-05-03 10:00:00', 'Uso de computadora'),
('233110182', 3, '2026-05-07 11:30:00', 'Asesoría bibliográfica');

-- Préstamos devueltos (historial)
INSERT INTO Prestamo (idUsuario, correoInst, idEjemplar, fechaPrestamo, fechaDevolucion, estado) VALUES
('233110177', 'L233110177@cdconstitucion.tecnm.mx', 2,  '2026-01-05', '2026-01-07', 'devuelto'),
('233110180', 'L233110180@cdconstitucion.tecnm.mx', 3,  '2026-01-10', '2026-01-12', 'devuelto'),
('233110181', 'L233110181@cdconstitucion.tecnm.mx', 5,  '2026-01-15', '2026-01-17', 'devuelto'),
('233110177', 'L233110177@cdconstitucion.tecnm.mx', 7,  '2026-02-03', '2026-02-05', 'devuelto'),
('233110179', 'L233110179@cdconstitucion.tecnm.mx', 8,  '2026-02-10', '2026-02-12', 'devuelto'),
('233110180', 'L233110180@cdconstitucion.tecnm.mx', 9,  '2026-02-18', '2026-02-20', 'devuelto'),
('DOC001',    'D001@cdconstitucion.tecnm.mx',       2,  '2026-02-20', '2026-02-25', 'devuelto'),
('233110181', 'L233110181@cdconstitucion.tecnm.mx', 3,  '2026-03-02', '2026-03-04', 'devuelto'),
('233110182', 'L233110182@cdconstitucion.tecnm.mx', 5,  '2026-03-08', '2026-03-10', 'devuelto'),
('233110177', 'L233110177@cdconstitucion.tecnm.mx', 12, '2026-03-15', '2026-03-17', 'devuelto'),
('DOC002',    'D002@cdconstitucion.tecnm.mx',       7,  '2026-03-20', '2026-03-22', 'devuelto'),
('233110179', 'L233110179@cdconstitucion.tecnm.mx', 2,  '2026-04-01', '2026-04-03', 'devuelto'),
('233110180', 'L233110180@cdconstitucion.tecnm.mx', 8,  '2026-04-05', '2026-04-07', 'devuelto'),
('233110181', 'L233110181@cdconstitucion.tecnm.mx', 9,  '2026-04-12', '2026-04-14', 'devuelto'),
('233110182', 'L233110182@cdconstitucion.tecnm.mx', 3,  '2026-04-18', '2026-04-20', 'devuelto'),
('233110177', 'L233110177@cdconstitucion.tecnm.mx', 5,  '2026-05-02', '2026-05-04', 'devuelto'),
('DOC001',    'D001@cdconstitucion.tecnm.mx',       7,  '2026-05-06', '2026-05-08', 'devuelto'),
('233110179', 'L233110179@cdconstitucion.tecnm.mx', 12, '2026-05-10', '2026-05-12', 'devuelto'),
('233110180', 'L233110180@cdconstitucion.tecnm.mx', 2,  '2026-05-15', '2026-05-17', 'devuelto'),
('233110181', 'L233110181@cdconstitucion.tecnm.mx', 8,  '2026-05-20', '2026-05-22', 'devuelto');

-- Préstamos activos y vencidos
INSERT INTO Prestamo (idUsuario, correoInst, idEjemplar, fechaPrestamo, fechaDevolucion, estado) VALUES
('233110177', 'L233110177@cdconstitucion.tecnm.mx', 4,  '2026-05-20', '2026-05-25', 'vencido'),
('233110180', 'L233110180@cdconstitucion.tecnm.mx', 6,  '2026-05-22', '2026-05-28', 'vencido'),
('233110181', 'L233110181@cdconstitucion.tecnm.mx', 10, '2026-05-28', '2026-06-05', 'activo'),
('233110182', 'L233110182@cdconstitucion.tecnm.mx', 11, '2026-05-29', '2026-06-07', 'activo'),
('DOC001',    'D001@cdconstitucion.tecnm.mx',       14, '2026-05-27', '2026-06-03', 'activo');

SELECT titulo, disponibles, totalEjemplares 
FROM vista_material 
ORDER BY disponibles ASC;

SELECT 
    SUM(CASE WHEN disponibles > 0 THEN 1 ELSE 0 END) AS con_disponibles,
    SUM(CASE WHEN disponibles = 0 THEN 1 ELSE 0 END) AS sin_disponibles
FROM vista_material;
use biblioteca;
select * from usuario;
SELECT pagada, COUNT(*), SUM(monto) FROM Multa GROUP BY pagada;