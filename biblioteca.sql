-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: biblioteca
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alumno`
--

DROP TABLE IF EXISTS `alumno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumno` (
  `idUsuario` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `idCarrera` int NOT NULL,
  PRIMARY KEY (`idUsuario`),
  KEY `idCarrera` (`idCarrera`),
  CONSTRAINT `alumno_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`),
  CONSTRAINT `alumno_ibfk_2` FOREIGN KEY (`idCarrera`) REFERENCES `carrera` (`idCarrera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumno`
--

LOCK TABLES `alumno` WRITE;
/*!40000 ALTER TABLE `alumno` DISABLE KEYS */;
INSERT INTO `alumno` VALUES ('233110177',1),('233110179',1),('233110180',1),('233110182',2),('233110181',3);
/*!40000 ALTER TABLE `alumno` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `area` (
  `idArea` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idArea`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `area`
--

LOCK TABLES `area` WRITE;
/*!40000 ALTER TABLE `area` DISABLE KEYS */;
INSERT INTO `area` VALUES (1,'Ciencias'),(2,'Ingeniería'),(3,'Programacion'),(4,'Economia');
/*!40000 ALTER TABLE `area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrera`
--

DROP TABLE IF EXISTS `carrera`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carrera` (
  `idCarrera` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idCarrera`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrera`
--

LOCK TABLES `carrera` WRITE;
/*!40000 ALTER TABLE `carrera` DISABLE KEYS */;
INSERT INTO `carrera` VALUES (1,'Sistemas'),(2,'Electromecanica'),(3,'Administracion'),(4,'Arquitectura');
/*!40000 ALTER TABLE `carrera` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `division`
--

DROP TABLE IF EXISTS `division`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `division` (
  `idDivision` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idDivision`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `division`
--

LOCK TABLES `division` WRITE;
/*!40000 ALTER TABLE `division` DISABLE KEYS */;
INSERT INTO `division` VALUES (1,'División de Ingeniería'),(2,'División de Ciencias Básicas'),(3,'División de Administración');
/*!40000 ALTER TABLE `division` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docente`
--

DROP TABLE IF EXISTS `docente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `docente` (
  `idUsuario` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `idDivision` int NOT NULL,
  PRIMARY KEY (`idUsuario`),
  KEY `idDivision` (`idDivision`),
  CONSTRAINT `docente_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`),
  CONSTRAINT `docente_ibfk_2` FOREIGN KEY (`idDivision`) REFERENCES `division` (`idDivision`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docente`
--

LOCK TABLES `docente` WRITE;
/*!40000 ALTER TABLE `docente` DISABLE KEYS */;
INSERT INTO `docente` VALUES ('DOC001',1),('DOC002',3);
/*!40000 ALTER TABLE `docente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ejemplar`
--

DROP TABLE IF EXISTS `ejemplar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ejemplar` (
  `idEjemplar` int NOT NULL AUTO_INCREMENT,
  `codigoEjemplar` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `idMaterial` int NOT NULL,
  `estado` enum('disponible','prestado','baja') COLLATE utf8mb4_general_ci DEFAULT 'disponible',
  PRIMARY KEY (`idEjemplar`),
  KEY `idMaterial` (`idMaterial`),
  CONSTRAINT `ejemplar_ibfk_1` FOREIGN KEY (`idMaterial`) REFERENCES `material` (`idMaterial`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ejemplar`
--

LOCK TABLES `ejemplar` WRITE;
/*!40000 ALTER TABLE `ejemplar` DISABLE KEYS */;
INSERT INTO `ejemplar` VALUES (1,'EJ-001',1,'prestado'),(2,'EJ-002',1,'disponible'),(3,'EJ-003',1,'disponible'),(4,'EJ-004',2,'prestado'),(5,'EJ-005',2,'disponible'),(6,'EJ-006',3,'prestado'),(7,'EJ-007',3,'disponible'),(8,'EJ-008',4,'disponible'),(9,'EJ-009',4,'disponible'),(10,'EJ-010',5,'prestado'),(11,'EJ-011',5,'disponible'),(12,'EJ-012',6,'disponible'),(13,'EJ-013',7,'disponible'),(14,'EJ-014',8,'prestado'),(15,'EJ-015',9,'disponible'),(16,'EJ-016',10,'disponible');
/*!40000 ALTER TABLE `ejemplar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material`
--

DROP TABLE IF EXISTS `material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material` (
  `idMaterial` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `autor` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `isbn` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `anioPublicacion` year DEFAULT NULL,
  `editorial` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `edicion` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `idTipoMaterial` int NOT NULL,
  `idArea` int DEFAULT NULL,
  `idCarrera` int DEFAULT NULL,
  `rutaArchivo` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rutaPortada` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `esPrestable` enum('si','no') COLLATE utf8mb4_general_ci DEFAULT 'si',
  PRIMARY KEY (`idMaterial`),
  KEY `idTipoMaterial` (`idTipoMaterial`),
  KEY `idArea` (`idArea`),
  KEY `idCarrera` (`idCarrera`),
  CONSTRAINT `material_ibfk_1` FOREIGN KEY (`idTipoMaterial`) REFERENCES `tipomaterial` (`idTipoMaterial`),
  CONSTRAINT `material_ibfk_2` FOREIGN KEY (`idArea`) REFERENCES `area` (`idArea`),
  CONSTRAINT `material_ibfk_3` FOREIGN KEY (`idCarrera`) REFERENCES `carrera` (`idCarrera`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material`
--

LOCK TABLES `material` WRITE;
/*!40000 ALTER TABLE `material` DISABLE KEYS */;
INSERT INTO `material` VALUES (1,'Cien años de soledad','Gabriel García Márquez','978-0307474728',1967,'Sudamericana','1ra',1,1,NULL,NULL,NULL,'si'),(2,'Programación en Python','Mark Lutz','978-1449355739',2013,'Reilly','5ta',1,3,NULL,NULL,NULL,'si'),(3,'Cálculo Diferencial','James Stewart','978-6074816211',2012,'Cengage Learning','7ma',1,1,NULL,NULL,NULL,'si'),(4,'Administración Moderna','Harold Koontz','978-6071509949',2012,'McGraw Hill','14va',1,4,NULL,NULL,NULL,'si'),(5,'Estructuras de Datos','Robert Lafore','978-0672324536',2002,'Sams Publishing','2da',1,3,NULL,NULL,NULL,'si'),(6,'Revista de Ingeniería','ITSC',NULL,2023,'ITSC','1ra',2,2,NULL,NULL,NULL,'no'),(7,'Tesis: IA en Educación','Pedro Ruiz',NULL,2022,'ITSC','1ra',3,NULL,1,NULL,NULL,'no'),(8,'Electricidad Industrial','Stephen Chapman','978-0073380582',2011,'McGraw Hill','4ta',1,2,NULL,NULL,NULL,'si'),(9,'Base de Datos','Ramez Elmasri','978-0136086208',2010,'Pearson','6ta',1,3,NULL,NULL,NULL,'si'),(10,'Contabilidad General','Horngren Charles','978-6073221023',2015,'Pearson','9na',1,4,NULL,NULL,NULL,'si');
/*!40000 ALTER TABLE `material` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multa`
--

DROP TABLE IF EXISTS `multa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `multa` (
  `idMulta` int NOT NULL AUTO_INCREMENT,
  `idPrestamo` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fechaGenerada` datetime DEFAULT CURRENT_TIMESTAMP,
  `fechaPago` datetime DEFAULT NULL,
  `pagada` enum('si','no') COLLATE utf8mb4_general_ci DEFAULT 'no',
  PRIMARY KEY (`idMulta`),
  KEY `idPrestamo` (`idPrestamo`),
  CONSTRAINT `multa_ibfk_1` FOREIGN KEY (`idPrestamo`) REFERENCES `prestamo` (`idPrestamo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multa`
--

LOCK TABLES `multa` WRITE;
/*!40000 ALTER TABLE `multa` DISABLE KEYS */;
INSERT INTO `multa` VALUES (1,5,55.00,'2026-03-07 00:00:00',NULL,'no'),(2,6,80.00,'2026-03-02 00:00:00',NULL,'no'),(3,7,20.00,'2026-02-16 00:00:00',NULL,'si'),(4,8,10.00,'2026-02-25 00:00:00',NULL,'si');
/*!40000 ALTER TABLE `multa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permiso`
--

DROP TABLE IF EXISTS `permiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permiso` (
  `idPermiso` int NOT NULL AUTO_INCREMENT,
  `modulo` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `accion` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`idPermiso`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permiso`
--

LOCK TABLES `permiso` WRITE;
/*!40000 ALTER TABLE `permiso` DISABLE KEYS */;
INSERT INTO `permiso` VALUES (1,'catalogo','agregar','Agregar material al catálogo'),(2,'catalogo','editar','Editar material del catálogo'),(3,'catalogo','eliminar','Eliminar material del catálogo'),(4,'prestamos','agregar','Registrar nuevo préstamo'),(5,'prestamos','devolver','Marcar préstamo como devuelto'),(6,'prestamos','historial','Ver historial de préstamos'),(7,'usuarios','agregar','Agregar nuevo usuario'),(8,'usuarios','editar','Editar usuario existente'),(9,'usuarios','desactivar','Desactivar usuario'),(10,'adeudos','ver','Ver adeudos'),(11,'adeudos','pago','Registrar pago de multa'),(12,'estadisticas','ver','Ver estadísticas'),(13,'roles','agregar','Agregar nuevo rol'),(14,'roles','editar','Editar rol existente'),(15,'roles','eliminar','Eliminar rol');
/*!40000 ALTER TABLE `permiso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prestamo`
--

DROP TABLE IF EXISTS `prestamo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prestamo` (
  `idPrestamo` int NOT NULL AUTO_INCREMENT,
  `idUsuario` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `correoInst` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `idEjemplar` int NOT NULL,
  `fechaPrestamo` datetime DEFAULT CURRENT_TIMESTAMP,
  `fechaDevolucion` datetime NOT NULL,
  `estado` enum('activo','devuelto','vencido') COLLATE utf8mb4_general_ci DEFAULT 'activo',
  PRIMARY KEY (`idPrestamo`),
  KEY `idUsuario` (`idUsuario`),
  KEY `idEjemplar` (`idEjemplar`),
  CONSTRAINT `prestamo_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`),
  CONSTRAINT `prestamo_ibfk_2` FOREIGN KEY (`idEjemplar`) REFERENCES `ejemplar` (`idEjemplar`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prestamo`
--

LOCK TABLES `prestamo` WRITE;
/*!40000 ALTER TABLE `prestamo` DISABLE KEYS */;
INSERT INTO `prestamo` VALUES (1,'233110179','L233110179@cdconstitucion.tecnm.mx',1,'2026-03-10 00:00:00','2026-03-24 00:00:00','activo'),(2,'233110180','L233110180@cdconstitucion.tecnm.mx',4,'2026-03-12 00:00:00','2026-03-26 00:00:00','activo'),(3,'233110181','L233110181@cdconstitucion.tecnm.mx',6,'2026-03-10 00:00:00','2026-03-17 00:00:00','activo'),(4,'233110182','L233110182@cdconstitucion.tecnm.mx',10,'2026-03-11 00:00:00','2026-03-18 00:00:00','activo'),(5,'DOC001','D001@cdconstitucion.tecnm.mx',14,'2026-02-20 00:00:00','2026-03-06 00:00:00','vencido'),(6,'233110179','L233110179@cdconstitucion.tecnm.mx',11,'2026-02-15 00:00:00','2026-03-01 00:00:00','vencido'),(7,'DOC002','D002@cdconstitucion.tecnm.mx',7,'2026-02-01 00:00:00','2026-02-15 00:00:00','devuelto'),(8,'233110180','L233110180@cdconstitucion.tecnm.mx',9,'2026-02-10 00:00:00','2026-02-24 00:00:00','devuelto');
/*!40000 ALTER TABLE `prestamo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reglasprestamo`
--

DROP TABLE IF EXISTS `reglasprestamo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reglasprestamo` (
  `idReglasPrestamo` int NOT NULL AUTO_INCREMENT,
  `idRol` int NOT NULL,
  `diasPrestamo` int NOT NULL,
  `maxPrestamo` int NOT NULL,
  `renovaciones` int DEFAULT '0',
  `precioMulta` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`idReglasPrestamo`),
  KEY `idRol` (`idRol`),
  CONSTRAINT `reglasprestamo_ibfk_1` FOREIGN KEY (`idRol`) REFERENCES `rol` (`idRol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reglasprestamo`
--

LOCK TABLES `reglasprestamo` WRITE;
/*!40000 ALTER TABLE `reglasprestamo` DISABLE KEYS */;
INSERT INTO `reglasprestamo` VALUES (3,2,5,5,2,25.00),(4,3,2,2,1,25.00);
/*!40000 ALTER TABLE `reglasprestamo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relrol`
--

DROP TABLE IF EXISTS `relrol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relrol` (
  `idRelRol` int NOT NULL AUTO_INCREMENT,
  `idUsuario` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `correoInst` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `idRol` int NOT NULL,
  PRIMARY KEY (`idRelRol`),
  KEY `idUsuario` (`idUsuario`),
  KEY `idRol` (`idRol`),
  CONSTRAINT `relrol_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`),
  CONSTRAINT `relrol_ibfk_2` FOREIGN KEY (`idRol`) REFERENCES `rol` (`idRol`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relrol`
--

LOCK TABLES `relrol` WRITE;
/*!40000 ALTER TABLE `relrol` DISABLE KEYS */;
INSERT INTO `relrol` VALUES (10,'233110177','L233110177@cdconstitucion.tecnm.mx',3),(11,'233110179','L233110179@cdconstitucion.tecnm.mx',3),(12,'233110180','L233110180@cdconstitucion.tecnm.mx',3),(13,'233110181','L233110181@cdconstitucion.tecnm.mx',3),(14,'233110182','L233110182@cdconstitucion.tecnm.mx',3),(15,'ADM001','A001@cdconstitucion.tecnm.mx',1),(16,'DOC001','D001@cdconstitucion.tecnm.mx',2),(17,'DOC002','D002@cdconstitucion.tecnm.mx',2),(18,'ENC001','ENC001@cdconstitucion.tecnm.mx',2),(25,'ADM002','mirandatalamantes9@gmail.com',1),(26,'INV001','maricruztalamantes2@gmail.com',3);
/*!40000 ALTER TABLE `relrol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol`
--

DROP TABLE IF EXISTS `rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol` (
  `idRol` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idRol`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol`
--

LOCK TABLES `rol` WRITE;
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
INSERT INTO `rol` VALUES (1,'Administrador'),(2,'Encargado'),(3,'Invitado');
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rolpermiso`
--

DROP TABLE IF EXISTS `rolpermiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rolpermiso` (
  `idRol` int NOT NULL,
  `idPermiso` int NOT NULL,
  PRIMARY KEY (`idRol`,`idPermiso`),
  KEY `idPermiso` (`idPermiso`),
  CONSTRAINT `rolpermiso_ibfk_1` FOREIGN KEY (`idRol`) REFERENCES `rol` (`idRol`),
  CONSTRAINT `rolpermiso_ibfk_2` FOREIGN KEY (`idPermiso`) REFERENCES `permiso` (`idPermiso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rolpermiso`
--

LOCK TABLES `rolpermiso` WRITE;
/*!40000 ALTER TABLE `rolpermiso` DISABLE KEYS */;
INSERT INTO `rolpermiso` VALUES (1,1),(2,1),(1,2),(2,2),(1,3),(2,3),(1,4),(2,4),(1,5),(2,5),(1,6),(2,6),(1,7),(2,7),(1,8),(2,8),(1,9),(1,10),(2,10),(3,10),(1,11),(2,11),(1,12),(2,12),(3,12),(1,13),(1,14),(1,15);
/*!40000 ALTER TABLE `rolpermiso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servicio`
--

DROP TABLE IF EXISTS `servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servicio` (
  `idServicio` int NOT NULL AUTO_INCREMENT,
  `idUsuario` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `idTipoServicio` int NOT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `descripcion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`idServicio`),
  KEY `idUsuario` (`idUsuario`),
  KEY `idTipoServicio` (`idTipoServicio`),
  CONSTRAINT `servicio_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`),
  CONSTRAINT `servicio_ibfk_2` FOREIGN KEY (`idTipoServicio`) REFERENCES `tiposervicio` (`idTipoServicio`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicio`
--

LOCK TABLES `servicio` WRITE;
/*!40000 ALTER TABLE `servicio` DISABLE KEYS */;
INSERT INTO `servicio` VALUES (1,'233110179',1,'2026-03-01 10:00:00','Consulta en sala'),(2,'233110179',3,'2026-03-02 09:30:00','Asesoría bibliográfica'),(3,'233110180',2,'2026-03-01 11:00:00','Uso de computadora'),(4,'233110180',5,'2026-03-03 12:10:00','Uso de sala de estudio'),(5,'233110181',1,'2026-03-02 12:00:00','Consulta en sala'),(6,'233110181',3,'2026-03-04 11:45:00','Asesoría bibliográfica'),(7,'233110182',5,'2026-03-03 14:00:00','Uso de sala de estudio'),(8,'233110182',4,'2026-03-05 16:20:00','Préstamo interno'),(9,'DOC001',3,'2026-03-02 10:00:00','Asesoría a estudiantes'),(10,'ADM001',2,'2026-03-04 10:15:00','Uso de sistema administrativo');
/*!40000 ALTER TABLE `servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipomaterial`
--

DROP TABLE IF EXISTS `tipomaterial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipomaterial` (
  `idTipoMaterial` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idTipoMaterial`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipomaterial`
--

LOCK TABLES `tipomaterial` WRITE;
/*!40000 ALTER TABLE `tipomaterial` DISABLE KEYS */;
INSERT INTO `tipomaterial` VALUES (1,'Libro'),(2,'Revista'),(3,'Tesis'),(4,'Residencia'),(5,'Multimedia');
/*!40000 ALTER TABLE `tipomaterial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tiposervicio`
--

DROP TABLE IF EXISTS `tiposervicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tiposervicio` (
  `idTipoServicio` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idTipoServicio`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiposervicio`
--

LOCK TABLES `tiposervicio` WRITE;
/*!40000 ALTER TABLE `tiposervicio` DISABLE KEYS */;
INSERT INTO `tiposervicio` VALUES (1,'Consulta en sala'),(2,'Uso de computadoras'),(3,'Asesoría bibliográfica'),(4,'Préstamo interno'),(5,'Uso de sala de estudio');
/*!40000 ALTER TABLE `tiposervicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `idUsuario` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `correoInst` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `activo` enum('si','no') COLLATE utf8mb4_general_ci DEFAULT 'si',
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `idRol` int DEFAULT NULL,
  PRIMARY KEY (`idUsuario`),
  KEY `idRol` (`idRol`),
  CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`idRol`) REFERENCES `rol` (`idRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES ('233110177','L233110177@cdconstitucion.tecnm.mx','Arturo Higuera','si',NULL,3),('233110179','L233110179@cdconstitucion.tecnm.mx','Miranda','si',NULL,3),('233110180','L233110180@cdconstitucion.tecnm.mx','Carlos Pérez','si',NULL,3),('233110181','L233110181@cdconstitucion.tecnm.mx','Ana López','si',NULL,3),('233110182','L233110182@cdconstitucion.tecnm.mx','Luis Martínez','si',NULL,3),('ADM001','A001@cdconstitucion.tecnm.mx','Administrador','si',NULL,1),('ADM002','mirandatalamantes9@gmail.com','Admin Miry','si',NULL,1),('DOC001','D001@cdconstitucion.tecnm.mx','Prof. García','si',NULL,2),('DOC002','D002@cdconstitucion.tecnm.mx','Prof. Ramírez','si',NULL,2),('ENC001','ENC001@cdconstitucion.tecnm.mx','Jorge','si',NULL,2),('INV001','maricruztalamantes2@gmail.com','Invitado Prueba','si',NULL,3);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'biblioteca'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-19 21:55:36
