CREATE DATABASE  IF NOT EXISTS `magiscode` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `magiscode`;
-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: localhost    Database: magiscode
-- ------------------------------------------------------
-- Server version	8.0.46

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
-- Table structure for table `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auditoria` (
  `id_auditoria` int NOT NULL AUTO_INCREMENT,
  `tabla_afectada` varchar(300) NOT NULL,
  `accion_a` varchar(20) NOT NULL,
  `usuario_bd` varchar(30) DEFAULT NULL,
  `fecha_evento` datetime DEFAULT CURRENT_TIMESTAMP,
  `valor_anterior` text,
  `valor_nuevo` text,
  PRIMARY KEY (`id_auditoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria`
--

LOCK TABLES `auditoria` WRITE;
/*!40000 ALTER TABLE `auditoria` DISABLE KEYS */;
/*!40000 ALTER TABLE `auditoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificado`
--

DROP TABLE IF EXISTS `certificado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `certificado` (
  `id_certificado` int NOT NULL AUTO_INCREMENT,
  `codigo_validacion` varchar(300) DEFAULT NULL,
  `fecha_emision` datetime DEFAULT CURRENT_TIMESTAMP,
  `ruta_pdf` varchar(500) NOT NULL,
  `id_curso_ce` int NOT NULL,
  `id_usuario_ce` bigint NOT NULL,
  PRIMARY KEY (`id_certificado`),
  UNIQUE KEY `codigo_validacion` (`codigo_validacion`),
  KEY `id_curso_ce` (`id_curso_ce`),
  KEY `id_usuario_ce` (`id_usuario_ce`),
  CONSTRAINT `certificado_ibfk_1` FOREIGN KEY (`id_curso_ce`) REFERENCES `curso` (`id_curso`),
  CONSTRAINT `certificado_ibfk_2` FOREIGN KEY (`id_usuario_ce`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificado`
--

LOCK TABLES `certificado` WRITE;
/*!40000 ALTER TABLE `certificado` DISABLE KEYS */;
INSERT INTO `certificado` VALUES (1,'CERT-2026-0001','2026-06-22 17:26:31','certificados/cert_0001.pdf',1,1000000011),(2,'CERT-2026-0002','2026-06-24 10:39:35','certificados/cert_0002.pdf',2,1000000012),(3,'CERT-2026-0003','2026-06-24 10:39:35','certificados/cert_0003.pdf',3,1000000013);
/*!40000 ALTER TABLE `certificado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curso`
--

DROP TABLE IF EXISTS `curso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `curso` (
  `id_curso` int NOT NULL AUTO_INCREMENT,
  `titulo_curso` varchar(300) NOT NULL,
  `descripcion_curso` text NOT NULL,
  `ruta_imagen` varchar(300) NOT NULL,
  `estado_curso` varchar(20) DEFAULT 'Activo',
  `id_usuario_c` bigint NOT NULL,
  PRIMARY KEY (`id_curso`),
  KEY `id_usuario_c` (`id_usuario_c`),
  CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`id_usuario_c`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curso`
--

LOCK TABLES `curso` WRITE;
/*!40000 ALTER TABLE `curso` DISABLE KEYS */;
INSERT INTO `curso` VALUES (1,'Python Básico','Fundamentos de programación en Python','img/python_basico.jpg','Activo',1000000002),(2,'Python Intermedio','Programación orientada a objetos en Python','img/python_intermedio.jpg','Activo',1000000002),(3,'Java Fundamentals','Introducción al lenguaje Java','img/java_fundamentals.jpg','Activo',1000000003),(4,'Java POO','Programación orientada a objetos con Java','img/java_poo.jpg','Activo',1000000004),(5,'MySQL Básico','Fundamentos de bases de datos relacionales','img/mysql_basico.jpg','Activo',1000000005),(6,'MySQL Avanzado','Consultas complejas y optimización','img/mysql_avanzado.jpg','Activo',1000000005),(7,'HTML5','Desarrollo de páginas web con HTML5','img/html5.jpg','Activo',1000000006),(8,'CSS3','Diseño y estilos web modernos','img/css3.jpg','Activo',1000000006),(9,'JavaScript','Programación del lado del cliente','img/javascript.jpg','Activo',1000000007),(10,'React','Desarrollo de interfaces con React','img/react.jpg','Activo',1000000007),(11,'Spring Boot','Desarrollo backend con Spring Boot','img/springboot.jpg','Activo',1000000008),(12,'Power BI','Análisis y visualización de datos','img/powerbi.jpg','Activo',1000000008),(13,'Git y GitHub','Control de versiones','img/git.jpg','Activo',1000000009),(14,'Análisis de Requisitos','Ingeniería de requisitos de software','img/requisitos.jpg','Activo',1000000010),(15,'Bases de Datos Relacionales','Modelado y normalización','img/bd_relacionales.jpg','Activo',1000000010);
/*!40000 ALTER TABLE `curso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curso_aprendiz`
--

DROP TABLE IF EXISTS `curso_aprendiz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `curso_aprendiz` (
  `id_asignacion` int NOT NULL AUTO_INCREMENT,
  `estado_c_a` varchar(100) NOT NULL,
  `avance` decimal(5,2) NOT NULL,
  `fecha_asignacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_curso_c_a` int NOT NULL,
  `id_usuario_c_a` bigint NOT NULL,
  PRIMARY KEY (`id_asignacion`),
  UNIQUE KEY `id_curso_c_a` (`id_curso_c_a`,`id_usuario_c_a`),
  KEY `id_usuario_c_a` (`id_usuario_c_a`),
  CONSTRAINT `curso_aprendiz_ibfk_1` FOREIGN KEY (`id_curso_c_a`) REFERENCES `curso` (`id_curso`),
  CONSTRAINT `curso_aprendiz_ibfk_2` FOREIGN KEY (`id_usuario_c_a`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curso_aprendiz`
--

LOCK TABLES `curso_aprendiz` WRITE;
/*!40000 ALTER TABLE `curso_aprendiz` DISABLE KEYS */;
INSERT INTO `curso_aprendiz` VALUES (1,'Aprobado',100.00,'2026-06-22 17:26:17',1,1000000011),(2,'Aprobado',100.00,'2026-06-22 17:26:17',1,1000000012),(3,'Aprobado',100.00,'2026-06-22 17:26:17',2,1000000013),(12,'Activo',15.00,'2026-06-22 17:30:59',2,1000000012),(13,'Inactivo',0.00,'2026-06-24 08:51:07',3,1000000014),(14,'Inactivo',10.00,'2026-06-24 08:51:07',6,1000000015),(15,'Inactivo',45.00,'2026-06-24 08:51:07',7,1000000016),(16,'Inactivo',15.00,'2026-06-24 08:51:07',9,1000000017),(17,'Inactivo',30.00,'2026-06-24 08:51:07',8,1000000018),(18,'Inactivo',90.00,'2026-06-24 08:51:07',4,1000000019),(19,'Inactivo',75.00,'2026-06-24 08:51:07',13,1000000020),(20,'Inactivo',0.00,'2026-06-24 08:51:07',10,1000000021),(21,'Activo',0.00,'2026-06-24 13:23:07',11,1014198021);
/*!40000 ALTER TABLE `curso_aprendiz` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_evaluacion`
--

DROP TABLE IF EXISTS `estado_evaluacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_evaluacion` (
  `id_estado` int NOT NULL AUTO_INCREMENT,
  `estado` varchar(20) NOT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_evaluacion`
--

LOCK TABLES `estado_evaluacion` WRITE;
/*!40000 ALTER TABLE `estado_evaluacion` DISABLE KEYS */;
INSERT INTO `estado_evaluacion` VALUES (1,'Activa'),(2,'Inactiva');
/*!40000 ALTER TABLE `estado_evaluacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_usuario`
--

DROP TABLE IF EXISTS `estado_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_usuario` (
  `id_estado` int NOT NULL AUTO_INCREMENT,
  `nombre_estado` varchar(300) NOT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_usuario`
--

LOCK TABLES `estado_usuario` WRITE;
/*!40000 ALTER TABLE `estado_usuario` DISABLE KEYS */;
INSERT INTO `estado_usuario` VALUES (1,'Activo'),(2,'Inactivo'),(3,'Bloqueado');
/*!40000 ALTER TABLE `estado_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluacion`
--

DROP TABLE IF EXISTS `evaluacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evaluacion` (
  `id_evaluacion` int NOT NULL AUTO_INCREMENT,
  `titulo_evaluacion` varchar(300) NOT NULL,
  `descripcion_evaluacion` text NOT NULL,
  `puntaje_aprobacion` decimal(4,2) NOT NULL,
  `id_estado_e` int NOT NULL DEFAULT '1',
  `id_curso_e` int NOT NULL,
  PRIMARY KEY (`id_evaluacion`),
  KEY `id_estado_e` (`id_estado_e`),
  KEY `id_curso_e` (`id_curso_e`),
  CONSTRAINT `evaluacion_ibfk_1` FOREIGN KEY (`id_estado_e`) REFERENCES `estado_evaluacion` (`id_estado`),
  CONSTRAINT `evaluacion_ibfk_2` FOREIGN KEY (`id_curso_e`) REFERENCES `curso` (`id_curso`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluacion`
--

LOCK TABLES `evaluacion` WRITE;
/*!40000 ALTER TABLE `evaluacion` DISABLE KEYS */;
INSERT INTO `evaluacion` VALUES (1,'Evaluación Python Básico','Prueba de conocimientos fundamentales de Python',3.00,1,1),(2,'Evaluación Python Intermedio','Prueba sobre programación orientada a objetos',3.50,1,2),(3,'Evaluación Java Fundamentals','Conceptos básicos del lenguaje Java',3.00,1,3),(4,'Java POO','Evaluación de Programación Orientada a Objetos en Java',3.00,1,4),(5,'MySQL Básico','Evaluación de Fundamentos de Bases de Datos con MySQL',3.00,1,5),(6,'MySQL Avanzado','Evaluación de Consultas Avanzadas y Optimización en MySQL',3.00,1,6),(7,'HTML5','Evaluación de Estructuración Web con HTML5',3.00,1,7),(8,'CSS3','Evaluación de Diseño y Estilos con CSS3',3.00,1,8),(9,'JavaScript','Evaluación de Programación Web con JavaScript',3.00,1,9),(10,'React','Evaluación de Desarrollo de Interfaces con React',3.00,1,10),(11,'Spring Boot','Evaluación de Desarrollo Backend con Spring Boot',3.00,1,11),(12,'Power BI','Evaluación de Análisis y Visualización de Datos con Power BI',3.00,1,12),(13,'Git y GitHub','Evaluación de Control de Versiones con Git y GitHub',3.00,1,13);
/*!40000 ALTER TABLE `evaluacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leccion`
--

DROP TABLE IF EXISTS `leccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leccion` (
  `id_leccion` int NOT NULL AUTO_INCREMENT,
  `titulo_leccion` varchar(100) NOT NULL,
  `orden_leccion` int NOT NULL,
  `id_modulo_l` int NOT NULL,
  PRIMARY KEY (`id_leccion`),
  KEY `id_modulo_l` (`id_modulo_l`),
  CONSTRAINT `leccion_ibfk_1` FOREIGN KEY (`id_modulo_l`) REFERENCES `modulo` (`id_modulo`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leccion`
--

LOCK TABLES `leccion` WRITE;
/*!40000 ALTER TABLE `leccion` DISABLE KEYS */;
INSERT INTO `leccion` VALUES (1,'¿Qué es Python?',1,1),(2,'Instalación de Python',2,1),(3,'Listas, Tuplas y Diccionarios',3,1),(4,'Conjuntos y Comprensión de Colecciones',4,1),(5,'Creación de Clases',1,2),(6,'Herencia y Encapsulamiento en Python',2,2),(7,'Lectura y Escritura de Archivos',1,6),(8,'Manejo de Excepciones',2,6),(9,'Variables y Operadores',1,4),(10,'Entrada y Salida de Datos',2,4),(11,'Estructuras Condicionales',1,5),(12,'Bucles y Métodos',2,5),(13,'Arrays en Java',1,9),(14,'Collections Framework',2,9),(15,'Definición de Clases',1,7),(16,'Creación de Objetos',2,7),(17,'Herencia en Java',1,8),(18,'Polimorfismo y Sobrescritura',2,8),(19,'Interfaces en Java',1,12),(20,'Proyecto Práctico de POO',2,12),(21,'Conceptos de Bases de Datos',1,13),(22,'Instalación de MySQL',2,13),(23,'Consultas SELECT',1,14),(24,'Filtros y Ordenamiento',2,14),(25,'Funciones SQL Básicas',1,15),(26,'Relaciones entre Tablas',2,15),(27,'INNER JOIN y LEFT JOIN',1,16),(28,'Subconsultas',2,16),(29,'Procedimientos Almacenados',1,17),(30,'Triggers en MySQL',2,17),(31,'Índices y Rendimiento',1,18),(32,'Seguridad de Usuarios',2,18),(33,'Etiquetas HTML Básicas',1,19),(34,'Estructura de una Página Web',2,19),(35,'Formularios HTML',1,20),(36,'Audio y Video',2,20),(37,'Etiquetas Semánticas',1,21),(38,'Accesibilidad Web',2,21),(39,'Selectores CSS',1,22),(40,'Colores y Fuentes',2,22),(41,'Media Queries',1,23),(42,'Diseño Adaptativo',2,23),(43,'Flexbox',1,24),(44,'CSS Grid',2,24),(45,'Variables y Tipos',1,25),(46,'Operadores y Conversiones',2,25),(47,'Funciones JavaScript',1,26),(48,'Eventos del Navegador',2,26),(49,'Manipulación del DOM',1,27),(50,'Consumo de APIs REST',2,27),(51,'Introducción a JSX',1,28),(52,'Creación de Componentes',2,28),(53,'Props en React',1,29),(54,'Manejo del Estado',2,29),(55,'Hooks Básicos',1,30),(56,'Consumo de APIs con React',2,30),(57,'Configuración de Spring Boot',1,31),(58,'Estructura de un Proyecto',2,31),(59,'Creación de APIs REST',1,32),(60,'Conexión a Base de Datos',2,32),(61,'Spring Security',1,33),(62,'Despliegue de Aplicaciones',2,33),(63,'Importación de Datos',1,34),(64,'Limpieza y Transformación',2,34),(65,'Relaciones entre Tablas',1,35),(66,'Creación de Medidas DAX',2,35),(67,'Visualizaciones en Power BI',1,36),(68,'Creación de Dashboards',2,36),(69,'Instalación y Configuración de Git',1,37),(70,'Primer Repositorio Local',2,37),(71,'Creación de Branches',1,38),(72,'Merge y Resolución de Conflictos',2,38),(73,'Repositorios en GitHub',1,39),(74,'Pull Requests y Colaboración',2,39);
/*!40000 ALTER TABLE `leccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulo`
--

DROP TABLE IF EXISTS `modulo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulo` (
  `id_modulo` int NOT NULL AUTO_INCREMENT,
  `nombre_modulo` varchar(200) NOT NULL,
  `orden_modulo` int NOT NULL,
  `id_curso_m` int NOT NULL,
  PRIMARY KEY (`id_modulo`),
  KEY `id_curso_m` (`id_curso_m`),
  CONSTRAINT `modulo_ibfk_1` FOREIGN KEY (`id_curso_m`) REFERENCES `curso` (`id_curso`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulo`
--

LOCK TABLES `modulo` WRITE;
/*!40000 ALTER TABLE `modulo` DISABLE KEYS */;
INSERT INTO `modulo` VALUES (1,'Introducción a Python',1,1),(2,'Variables y Operadores',2,1),(3,'Estructuras de Control',3,1),(4,'Estructuras de Datos y Colecciones',1,1),(5,'Programación Orientada a Objetos con Python',2,1),(6,'Manejo de Archivos y Excepciones',3,1),(7,'Sintaxis y Tipos de Datos',1,2),(8,'Control de Flujo y Métodos',2,2),(9,'Arreglos y Colecciones',3,2),(10,'Clases y Objetos',1,4),(11,'Herencia y Polimorfismo',2,4),(12,'Interfaces y Proyecto Integrador',3,4),(13,'Introducción a Bases de Datos',1,5),(14,'Consultas SQL Básicas',2,5),(15,'Funciones y Relaciones',3,5),(16,'Subconsultas y Joins Avanzados',1,6),(17,'Procedimientos y Triggers',2,6),(18,'Optimización y Seguridad',3,6),(19,'Estructura de un Documento HTML',1,7),(20,'Formularios y Multimedia',2,7),(21,'HTML Semántico',3,7),(22,'Selectores y Propiedades CSS',1,8),(23,'Diseño Responsive',2,8),(24,'Flexbox y Grid',3,8),(25,'Variables y Tipos de Datos',1,9),(26,'Funciones y Eventos',2,9),(27,'DOM y Consumo de APIs',3,9),(28,'Componentes y JSX',1,10),(29,'Estado y Props',2,10),(30,'Hooks y Consumo de APIs',3,10),(31,'Fundamentos de Spring Boot',1,11),(32,'APIs REST y Persistencia',2,11),(33,'Seguridad y Despliegue',3,11),(34,'Transformación de Datos',1,12),(35,'Modelado y Relaciones',2,12),(36,'Dashboards e Indicadores',3,12),(37,'Fundamentos de Git',1,13),(38,'Trabajo con Ramas y Merge',2,13),(39,'GitHub y Colaboración',3,13);
/*!40000 ALTER TABLE `modulo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pregunta`
--

DROP TABLE IF EXISTS `pregunta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pregunta` (
  `id_pregunta` int NOT NULL AUTO_INCREMENT,
  `tipo_pregunta` varchar(100) NOT NULL,
  `pregunta` varchar(500) NOT NULL,
  `id_evaluacion_p` int NOT NULL,
  PRIMARY KEY (`id_pregunta`),
  KEY `id_evaluacion_p` (`id_evaluacion_p`),
  CONSTRAINT `pregunta_ibfk_1` FOREIGN KEY (`id_evaluacion_p`) REFERENCES `evaluacion` (`id_evaluacion`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pregunta`
--

LOCK TABLES `pregunta` WRITE;
/*!40000 ALTER TABLE `pregunta` DISABLE KEYS */;
INSERT INTO `pregunta` VALUES (1,'Seleccion Multiple','¿Qué función se utiliza para imprimir información en Python?',1),(2,'Seleccion Multiple','¿Cuál es el símbolo para comentarios de una línea en Python?',1),(3,'Verdadero/Falso','Python es un lenguaje compilado exclusivamente.',1),(4,'Seleccion Multiple','¿Qué concepto permite crear una clase a partir de otra en Python?',2),(5,'Seleccion Multiple','¿Qué método se ejecuta al crear una instancia de una clase?',2),(6,'Verdadero/Falso','Python soporta herencia múltiple.',2),(7,'Seleccion Multiple','¿Cuál es el método principal de entrada en una aplicación Java?',3),(8,'Seleccion Multiple','¿Qué palabra clave se utiliza para declarar una variable constante?',3),(9,'Verdadero/Falso','Java es un lenguaje sensible a mayúsculas y minúsculas.',3),(10,'Seleccion Multiple','¿Qué pilar de la POO permite ocultar información?',4),(11,'Seleccion Multiple','¿Qué palabra clave se utiliza para heredar una clase en Java?',4),(12,'Verdadero/Falso','Una interfaz puede contener métodos.',4),(13,'Seleccion Multiple','¿Qué comando se utiliza para consultar datos?',5),(14,'Seleccion Multiple','¿Qué cláusula permite filtrar registros?',5),(15,'Verdadero/Falso','Una clave primaria puede contener valores NULL.',5),(16,'Seleccion Multiple','¿Qué tipo de JOIN devuelve registros coincidentes de ambas tablas?',6),(17,'Seleccion Multiple','¿Qué objeto almacena lógica reutilizable en MySQL?',6),(18,'Verdadero/Falso','Los índices pueden mejorar el rendimiento de las consultas.',6),(19,'Seleccion Multiple','¿Qué etiqueta define un formulario?',7),(20,'Seleccion Multiple','¿Qué etiqueta se utiliza para insertar una imagen?',7),(21,'Verdadero/Falso','HTML es un lenguaje de programación.',7),(22,'Seleccion Multiple','¿Qué propiedad cambia el color del texto?',8),(23,'Seleccion Multiple','¿Qué modelo permite distribuir elementos en una fila o columna?',8),(24,'Verdadero/Falso','CSS permite aplicar estilos a una página web.',8),(25,'Seleccion Multiple','¿Qué función muestra mensajes en la consola?',9),(26,'Seleccion Multiple','¿Qué palabra clave declara una variable moderna?',9),(27,'Verdadero/Falso','JavaScript puede manipular el DOM.',9),(28,'Seleccion Multiple','¿Qué permite JSX en React?',10),(29,'Seleccion Multiple','¿Qué Hook se utiliza para manejar estado?',10),(30,'Verdadero/Falso','React utiliza componentes reutilizables.',10),(31,'Seleccion Multiple','¿Qué anotación marca una clase como controlador REST?',11),(32,'Seleccion Multiple','¿Qué herramienta de construcción es común en Spring Boot?',11),(33,'Verdadero/Falso','Spring Boot facilita la creación de aplicaciones Java.',11),(34,'Seleccion Multiple','¿Qué lenguaje se utiliza para crear medidas en Power BI?',12),(35,'Seleccion Multiple','¿Qué componente permite visualizar información?',12),(36,'Verdadero/Falso','Power BI permite conectarse a múltiples fuentes de datos.',12),(37,'Seleccion Multiple','¿Qué comando envía cambios al repositorio remoto?',13),(38,'Seleccion Multiple','¿Qué comando descarga cambios del repositorio remoto?',13),(39,'Verdadero/Falso','Git permite controlar versiones de un proyecto.',13);
/*!40000 ALTER TABLE `pregunta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recurso`
--

DROP TABLE IF EXISTS `recurso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recurso` (
  `id_recurso` int NOT NULL AUTO_INCREMENT,
  `nombre_recurso` varchar(300) NOT NULL,
  `tipo_recurso` varchar(100) NOT NULL,
  `ruta_archivo` varchar(300) NOT NULL,
  `fecha_carga` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_leccion_r` int NOT NULL,
  PRIMARY KEY (`id_recurso`),
  KEY `id_leccion_r` (`id_leccion_r`),
  CONSTRAINT `recurso_ibfk_1` FOREIGN KEY (`id_leccion_r`) REFERENCES `leccion` (`id_leccion`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurso`
--

LOCK TABLES `recurso` WRITE;
/*!40000 ALTER TABLE `recurso` DISABLE KEYS */;
INSERT INTO `recurso` VALUES (1,'Material de apoyo - ¿Qué es Python?','PDF','recursos/leccion_1.pdf','2026-06-24 00:00:00',1),(2,'Material de apoyo - Instalación de Python','PDF','recursos/leccion_2.pdf','2026-06-24 00:00:00',2),(3,'Material de apoyo - Listas, Tuplas y Diccionarios','PDF','recursos/leccion_3.pdf','2026-06-24 00:00:00',3),(4,'Material de apoyo - Conjuntos y Comprensión de Colecciones','PDF','recursos/leccion_4.pdf','2026-06-24 00:00:00',4),(5,'Material de apoyo - Creación de Clases','PDF','recursos/leccion_5.pdf','2026-06-24 00:00:00',5),(6,'Material de apoyo - Herencia y Encapsulamiento en Python','PDF','recursos/leccion_6.pdf','2026-06-24 00:00:00',6),(7,'Material de apoyo - Lectura y Escritura de Archivos','PDF','recursos/leccion_7.pdf','2026-06-24 00:00:00',7),(8,'Material de apoyo - Manejo de Excepciones','PDF','recursos/leccion_8.pdf','2026-06-24 00:00:00',8),(9,'Material de apoyo - Variables y Operadores','PDF','recursos/leccion_9.pdf','2026-06-24 00:00:00',9),(10,'Material de apoyo - Entrada y Salida de Datos','PDF','recursos/leccion_10.pdf','2026-06-24 00:00:00',10),(11,'Material de apoyo - Estructuras Condicionales','PDF','recursos/leccion_11.pdf','2026-06-24 00:00:00',11),(12,'Material de apoyo - Bucles y Métodos','PDF','recursos/leccion_12.pdf','2026-06-24 00:00:00',12),(13,'Material de apoyo - Arrays en Java','PDF','recursos/leccion_13.pdf','2026-06-24 00:00:00',13),(14,'Material de apoyo - Collections Framework','PDF','recursos/leccion_14.pdf','2026-06-24 00:00:00',14),(15,'Material de apoyo - Definición de Clases','PDF','recursos/leccion_15.pdf','2026-06-24 00:00:00',15),(16,'Material de apoyo - Creación de Objetos','PDF','recursos/leccion_16.pdf','2026-06-24 00:00:00',16),(17,'Material de apoyo - Herencia en Java','PDF','recursos/leccion_17.pdf','2026-06-24 00:00:00',17),(18,'Material de apoyo - Polimorfismo y Sobrescritura','PDF','recursos/leccion_18.pdf','2026-06-24 00:00:00',18),(19,'Material de apoyo - Interfaces en Java','PDF','recursos/leccion_19.pdf','2026-06-24 00:00:00',19),(20,'Material de apoyo - Proyecto Práctico de POO','PDF','recursos/leccion_20.pdf','2026-06-24 00:00:00',20),(21,'Material de apoyo - Conceptos de Bases de Datos','PDF','recursos/leccion_21.pdf','2026-06-24 00:00:00',21),(22,'Material de apoyo - Instalación de MySQL','PDF','recursos/leccion_22.pdf','2026-06-24 00:00:00',22),(23,'Material de apoyo - Consultas SELECT','PDF','recursos/leccion_23.pdf','2026-06-24 00:00:00',23),(24,'Material de apoyo - Filtros y Ordenamiento','PDF','recursos/leccion_24.pdf','2026-06-24 00:00:00',24),(25,'Material de apoyo - Funciones SQL Básicas','PDF','recursos/leccion_25.pdf','2026-06-24 00:00:00',25),(26,'Material de apoyo - Relaciones entre Tablas','PDF','recursos/leccion_26.pdf','2026-06-24 00:00:00',26),(27,'Material de apoyo - INNER JOIN y LEFT JOIN','PDF','recursos/leccion_27.pdf','2026-06-24 00:00:00',27),(28,'Material de apoyo - Subconsultas','PDF','recursos/leccion_28.pdf','2026-06-24 00:00:00',28),(29,'Material de apoyo - Procedimientos Almacenados','PDF','recursos/leccion_29.pdf','2026-06-24 00:00:00',29),(30,'Material de apoyo - Triggers en MySQL','PDF','recursos/leccion_30.pdf','2026-06-24 00:00:00',30),(31,'Material de apoyo - Índices y Rendimiento','PDF','recursos/leccion_31.pdf','2026-06-24 00:00:00',31),(32,'Material de apoyo - Seguridad de Usuarios','PDF','recursos/leccion_32.pdf','2026-06-24 00:00:00',32),(33,'Material de apoyo - Etiquetas HTML Básicas','PDF','recursos/leccion_33.pdf','2026-06-24 00:00:00',33),(34,'Material de apoyo - Estructura de una Página Web','PDF','recursos/leccion_34.pdf','2026-06-24 00:00:00',34),(35,'Material de apoyo - Formularios HTML','PDF','recursos/leccion_35.pdf','2026-06-24 00:00:00',35),(36,'Material de apoyo - Audio y Video','PDF','recursos/leccion_36.pdf','2026-06-24 00:00:00',36),(37,'Material de apoyo - Etiquetas Semánticas','PDF','recursos/leccion_37.pdf','2026-06-24 00:00:00',37),(38,'Material de apoyo - Accesibilidad Web','PDF','recursos/leccion_38.pdf','2026-06-24 00:00:00',38),(39,'Material de apoyo - Selectores CSS','PDF','recursos/leccion_39.pdf','2026-06-24 00:00:00',39),(40,'Material de apoyo - Colores y Fuentes','PDF','recursos/leccion_40.pdf','2026-06-24 00:00:00',40),(41,'Material de apoyo - Media Queries','PDF','recursos/leccion_41.pdf','2026-06-24 00:00:00',41),(42,'Material de apoyo - Diseño Adaptativo','PDF','recursos/leccion_42.pdf','2026-06-24 00:00:00',42),(43,'Material de apoyo - Flexbox','PDF','recursos/leccion_43.pdf','2026-06-24 00:00:00',43),(44,'Material de apoyo - CSS Grid','PDF','recursos/leccion_44.pdf','2026-06-24 00:00:00',44),(45,'Material de apoyo - Variables y Tipos','PDF','recursos/leccion_45.pdf','2026-06-24 00:00:00',45),(46,'Material de apoyo - Operadores y Conversiones','PDF','recursos/leccion_46.pdf','2026-06-24 00:00:00',46),(47,'Material de apoyo - Funciones JavaScript','PDF','recursos/leccion_47.pdf','2026-06-24 00:00:00',47),(48,'Material de apoyo - Eventos del Navegador','PDF','recursos/leccion_48.pdf','2026-06-24 00:00:00',48),(49,'Material de apoyo - Manipulación del DOM','PDF','recursos/leccion_49.pdf','2026-06-24 00:00:00',49),(50,'Material de apoyo - Consumo de APIs REST','PDF','recursos/leccion_50.pdf','2026-06-24 00:00:00',50),(51,'Material de apoyo - Introducción a JSX','PDF','recursos/leccion_51.pdf','2026-06-24 00:00:00',51),(52,'Material de apoyo - Creación de Componentes','PDF','recursos/leccion_52.pdf','2026-06-24 00:00:00',52),(53,'Material de apoyo - Props en React','PDF','recursos/leccion_53.pdf','2026-06-24 00:00:00',53),(54,'Material de apoyo - Manejo del Estado','PDF','recursos/leccion_54.pdf','2026-06-24 00:00:00',54),(55,'Material de apoyo - Hooks Básicos','PDF','recursos/leccion_55.pdf','2026-06-24 00:00:00',55),(56,'Material de apoyo - Consumo de APIs con React','PDF','recursos/leccion_56.pdf','2026-06-24 00:00:00',56),(57,'Material de apoyo - Configuración de Spring Boot','PDF','recursos/leccion_57.pdf','2026-06-24 00:00:00',57),(58,'Material de apoyo - Estructura de un Proyecto','PDF','recursos/leccion_58.pdf','2026-06-24 00:00:00',58),(59,'Material de apoyo - Creación de APIs REST','PDF','recursos/leccion_59.pdf','2026-06-24 00:00:00',59),(60,'Material de apoyo - Conexión a Base de Datos','PDF','recursos/leccion_60.pdf','2026-06-24 00:00:00',60),(61,'Material de apoyo - Spring Security','PDF','recursos/leccion_61.pdf','2026-06-24 00:00:00',61),(62,'Material de apoyo - Despliegue de Aplicaciones','PDF','recursos/leccion_62.pdf','2026-06-24 00:00:00',62),(63,'Material de apoyo - Importación de Datos','PDF','recursos/leccion_63.pdf','2026-06-24 00:00:00',63),(64,'Material de apoyo - Limpieza y Transformación','PDF','recursos/leccion_64.pdf','2026-06-24 00:00:00',64),(65,'Material de apoyo - Relaciones entre Tablas','PDF','recursos/leccion_65.pdf','2026-06-24 00:00:00',65),(66,'Material de apoyo - Creación de Medidas DAX','PDF','recursos/leccion_66.pdf','2026-06-24 00:00:00',66),(67,'Material de apoyo - Visualizaciones en Power BI','PDF','recursos/leccion_67.pdf','2026-06-24 00:00:00',67),(68,'Material de apoyo - Creación de Dashboards','PDF','recursos/leccion_68.pdf','2026-06-24 00:00:00',68),(69,'Material de apoyo - Instalación y Configuración de Git','PDF','recursos/leccion_69.pdf','2026-06-24 00:00:00',69),(70,'Material de apoyo - Primer Repositorio Local','PDF','recursos/leccion_70.pdf','2026-06-24 00:00:00',70),(71,'Material de apoyo - Creación de Branches','PDF','recursos/leccion_71.pdf','2026-06-24 00:00:00',71),(72,'Material de apoyo - Merge y Resolución de Conflictos','PDF','recursos/leccion_72.pdf','2026-06-24 00:00:00',72),(73,'Material de apoyo - Repositorios en GitHub','PDF','recursos/leccion_73.pdf','2026-06-24 00:00:00',73),(74,'Material de apoyo - Pull Requests y Colaboración','PDF','recursos/leccion_74.pdf','2026-06-24 00:00:00',74);
/*!40000 ALTER TABLE `recurso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respuesta`
--

DROP TABLE IF EXISTS `respuesta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `respuesta` (
  `id_respuesta` int NOT NULL AUTO_INCREMENT,
  `respuesta` varchar(300) NOT NULL,
  `es_correcta` tinyint(1) NOT NULL,
  `id_pregunta_r` int NOT NULL,
  PRIMARY KEY (`id_respuesta`),
  KEY `id_pregunta_r` (`id_pregunta_r`),
  CONSTRAINT `respuesta_ibfk_1` FOREIGN KEY (`id_pregunta_r`) REFERENCES `pregunta` (`id_pregunta`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respuesta`
--

LOCK TABLES `respuesta` WRITE;
/*!40000 ALTER TABLE `respuesta` DISABLE KEYS */;
INSERT INTO `respuesta` VALUES (1,'print()',1,1),(2,'input()',0,1),(3,'echo()',0,1),(4,'show()',0,1),(5,'#',1,2),(6,'//',0,2),(7,'/* */',0,2),(8,'--',0,2),(9,'Verdadero',0,3),(10,'Falso',1,3),(11,'Herencia',1,4),(12,'Polimorfismo',0,4),(13,'Encapsulamiento',0,4),(14,'Abstracción',0,4),(15,'__init__',1,5),(16,'main',0,5),(17,'start',0,5),(18,'new',0,5),(19,'VERDADERO',1,6),(20,'FALSO',0,6),(21,'main()',1,7),(22,'start()',0,7),(23,'run()',0,7),(24,'execute()',0,7),(25,'final',1,8),(26,'const',0,8),(27,'static',0,8),(28,'readonly',0,8),(29,'VERDADERO',1,9),(30,'FALSO',0,9),(31,'Encapsulamiento',1,10),(32,'Herencia',0,10),(33,'Polimorfismo',0,10),(34,'Abstracción',0,10),(35,'extends',1,11),(36,'implements',0,11),(37,'inherits',0,11),(38,'super',0,11),(39,'VERDADERO',1,12),(40,'FALSO',0,12),(41,'SELECT',1,13),(42,'INSERT',0,13),(43,'UPDATE',0,13),(44,'DELETE',0,13),(45,'WHERE',1,14),(46,'GROUP BY',0,14),(47,'ORDER BY',0,14),(48,'HAVING',0,14),(49,'VERDADERO',0,15),(50,'FALSO',1,15),(51,'INNER JOIN',1,16),(52,'LEFT JOIN',0,16),(53,'RIGHT JOIN',0,16),(54,'CROSS JOIN',0,16),(55,'Procedimiento Almacenado',1,17),(56,'Vista',0,17),(57,'Índice',0,17),(58,'Cursor',0,17),(59,'VERDADERO',1,18),(60,'FALSO',0,18),(61,'form',1,19),(62,'input',0,19),(63,'table',0,19),(64,'section',0,19),(65,'img',1,20),(66,'image',0,20),(67,'src',0,20),(68,'picture',0,20),(69,'VERDADERO',0,21),(70,'FALSO',1,21),(71,'color',1,22),(72,'font-color',0,22),(73,'text-color',0,22),(74,'foreground',0,22),(75,'Flexbox',1,23),(76,'Table',0,23),(77,'Float',0,23),(78,'Inline',0,23),(79,'VERDADERO',1,24),(80,'FALSO',0,24),(81,'console.log()',1,25),(82,'print()',0,25),(83,'echo()',0,25),(84,'write()',0,25),(85,'let',1,26),(86,'var',0,26),(87,'int',0,26),(88,'define',0,26),(89,'VERDADERO',1,27),(90,'FALSO',0,27),(91,'Permite escribir HTML dentro de JavaScript',1,28),(92,'Conecta bases de datos',0,28),(93,'Gestiona servidores',0,28),(94,'Compila Java',0,28),(95,'useState',1,29),(96,'useEffect',0,29),(97,'useRef',0,29),(98,'useMemo',0,29),(99,'VERDADERO',1,30),(100,'FALSO',0,30),(101,'@RestController',1,31),(102,'@Controller',0,31),(103,'@Service',0,31),(104,'@Repository',0,31),(105,'Maven',1,32),(106,'Notepad',0,32),(107,'Excel',0,32),(108,'Docker',0,32),(109,'VERDADERO',1,33),(110,'FALSO',0,33),(111,'DAX',1,34),(112,'SQL',0,34),(113,'Python',0,34),(114,'Java',0,34),(115,'Visual',1,35),(116,'Tabla Física',0,35),(117,'Trigger',0,35),(118,'Procedimiento',0,35),(119,'VERDADERO',1,36),(120,'FALSO',0,36),(121,'git push',1,37),(122,'git pull',0,37),(123,'git clone',0,37),(124,'git add',0,37),(125,'git pull',1,38),(126,'git push',0,38),(127,'git commit',0,38),(128,'git init',0,38),(129,'VERDADERO',1,39),(130,'FALSO',0,39);
/*!40000 ALTER TABLE `respuesta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resultado_evaluacion`
--

DROP TABLE IF EXISTS `resultado_evaluacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resultado_evaluacion` (
  `id_resultado` int NOT NULL AUTO_INCREMENT,
  `calificacion` decimal(4,2) NOT NULL,
  `aprobado` tinyint(1) NOT NULL,
  `fecha_presentacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_evaluacion_r` int NOT NULL,
  `id_usuario_r` bigint NOT NULL,
  PRIMARY KEY (`id_resultado`),
  KEY `id_evaluacion_r` (`id_evaluacion_r`),
  KEY `id_usuario_r` (`id_usuario_r`),
  CONSTRAINT `resultado_evaluacion_ibfk_1` FOREIGN KEY (`id_evaluacion_r`) REFERENCES `evaluacion` (`id_evaluacion`),
  CONSTRAINT `resultado_evaluacion_ibfk_2` FOREIGN KEY (`id_usuario_r`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultado_evaluacion`
--

LOCK TABLES `resultado_evaluacion` WRITE;
/*!40000 ALTER TABLE `resultado_evaluacion` DISABLE KEYS */;
INSERT INTO `resultado_evaluacion` VALUES (1,4.50,1,'2026-06-22 17:26:23',1,1000000011),(2,3.20,1,'2026-06-22 17:26:23',1,1000000012),(3,2.80,0,'2026-06-22 17:26:23',1,1000000013),(4,4.50,1,'2026-06-24 13:55:23',11,1014198021);
/*!40000 ALTER TABLE `resultado_evaluacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol`
--

DROP TABLE IF EXISTS `rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion_rol` text NOT NULL,
  `estado_rol` varchar(20) DEFAULT 'Activo',
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol`
--

LOCK TABLES `rol` WRITE;
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
INSERT INTO `rol` VALUES (1,'Administrador','Administrador de la plataforma','Activo'),(2,'Instructor','Instructor de cursos','Activo'),(3,'Aprendiz','Aprendiz inscrito','Activo');
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_documento`
--

DROP TABLE IF EXISTS `tipo_documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_documento` (
  `id_tipo_documento` int NOT NULL AUTO_INCREMENT,
  `nombre_tipo` varchar(50) NOT NULL,
  PRIMARY KEY (`id_tipo_documento`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_documento`
--

LOCK TABLES `tipo_documento` WRITE;
/*!40000 ALTER TABLE `tipo_documento` DISABLE KEYS */;
INSERT INTO `tipo_documento` VALUES (1,'CC'),(2,'CE'),(3,'TI'),(4,'PASAPORTE');
/*!40000 ALTER TABLE `tipo_documento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id_usuario` bigint NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `apellido` varchar(300) NOT NULL,
  `correo` varchar(500) NOT NULL,
  `username` varchar(30) NOT NULL,
  `hash_contrasena` varchar(255) NOT NULL,
  `intentos_fallidos` int DEFAULT '0',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_rol_u` int NOT NULL,
  `id_estado_u` int NOT NULL,
  `id_tipo_documento_u` int NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo` (`correo`),
  UNIQUE KEY `username` (`username`),
  KEY `id_rol_u` (`id_rol_u`),
  KEY `id_estado_u` (`id_estado_u`),
  KEY `id_tipo_documento_u` (`id_tipo_documento_u`),
  CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol_u`) REFERENCES `rol` (`id_rol`),
  CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`id_estado_u`) REFERENCES `estado_usuario` (`id_estado`),
  CONSTRAINT `usuario_ibfk_3` FOREIGN KEY (`id_tipo_documento_u`) REFERENCES `tipo_documento` (`id_tipo_documento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1000000001,'Administrador','Sistema','admin@magiscode.com','admin','hash_admin',0,'2026-06-22 17:11:43',1,1,1),(1000000002,'Juan','Perez','juan.perez@magiscode.com','jperez','hash123',0,'2026-06-22 17:11:49',2,1,1),(1000000003,'Maria','Gomez','maria.gomez@magiscode.com','mgomez','hash123',0,'2026-06-22 17:11:56',2,1,1),(1000000004,'Andres','Rodriguez','andres.rodriguez@magiscode.com','arodriguez','hash123',0,'2026-06-22 17:17:40',2,1,1),(1000000005,'Laura','Martinez','laura.martinez@magiscode.com','lmartinez','hash123',0,'2026-06-22 17:17:40',2,1,1),(1000000006,'Carlos','Gomez','carlos.gomez@magiscode.com','cgomez','hash123',0,'2026-06-22 17:17:40',2,1,1),(1000000007,'Diana','Castro','diana.castro@magiscode.com','dcastro','hash123',0,'2026-06-22 17:17:40',2,1,1),(1000000008,'Felipe','Moreno','felipe.moreno@magiscode.com','fmoreno','hash123',0,'2026-06-22 17:17:40',2,1,1),(1000000009,'Paula','Herrera','paula.herrera@magiscode.com','pherrera','hash123',0,'2026-06-22 17:17:40',2,1,1),(1000000010,'Jorge','Vargas','jorge.vargas@magiscode.com','jvargas','hash123',0,'2026-06-22 17:17:40',2,1,1),(1000000011,'Carlos','Ramirez','carlos.ramirez@correo.com','cramirez','hash123',0,'2026-06-22 17:12:05',3,1,1),(1000000012,'Sofia','Lopez','sofia.lopez@correo.com','slopez','hash123',0,'2026-06-22 17:18:27',3,1,1),(1000000013,'Mateo','Ramirez','mateo.ramirez@correo.com','mramirez','hash123',0,'2026-06-22 17:18:27',3,1,1),(1000000014,'Valentina','Torres','valentina.torres@correo.com','vtorres','hash123',0,'2026-06-22 17:18:27',3,1,1),(1000000015,'Daniel','Sanchez','daniel.sanchez@correo.com','dsanchez','hash123',0,'2026-06-22 17:18:27',3,1,1),(1000000016,'Camila','Rojas','camila.rojas@correo.com','crojas','hash123',0,'2026-06-22 17:18:27',3,1,1),(1000000017,'Nicolas','Mendoza','nicolas.mendoza@correo.com','nmendoza','hash123',0,'2026-06-22 17:18:27',3,1,1),(1000000018,'Mariana','Silva','mariana.silva@correo.com','msilva','hash123',0,'2026-06-22 17:18:27',3,1,1),(1000000019,'Sebastian','Ortiz','sebastian.ortiz@correo.com','sortiz','hash123',0,'2026-06-22 17:18:27',3,1,1),(1000000020,'Juliana','Pineda','juliana.pineda@correo.com','jpineda','hash123',0,'2026-06-22 17:18:27',3,1,1),(1000000021,'Alejandro','Navarro','alejandro.navarro@correo.com','anavarro','hash123',0,'2026-06-22 17:18:27',3,1,1),(1014198021,'sebastian','lara','sebas@gmail.com','seblar021','hash987',0,'2026-06-24 12:57:13',3,1,2),(1014198022,'Sandra','Herrera','sandy@gmail.com','sanher432','hashh765',0,'2026-06-24 15:39:21',3,1,1);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `ver_curso_modulo_leccion`
--

DROP TABLE IF EXISTS `ver_curso_modulo_leccion`;
/*!50001 DROP VIEW IF EXISTS `ver_curso_modulo_leccion`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ver_curso_modulo_leccion` AS SELECT 
 1 AS `nombre`,
 1 AS `apellido`,
 1 AS `nombre_rol`,
 1 AS `titulo_curso`,
 1 AS `avance`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_aprendiz_aprobados`
--

DROP TABLE IF EXISTS `vw_aprendiz_aprobados`;
/*!50001 DROP VIEW IF EXISTS `vw_aprendiz_aprobados`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_aprendiz_aprobados` AS SELECT 
 1 AS `nombre`,
 1 AS `apellido`,
 1 AS `estado_c_a`,
 1 AS `titulo_curso`*/;
SET character_set_client = @saved_cs_client;

--
-- Dumping events for database 'magiscode'
--

--
-- Dumping routines for database 'magiscode'
--
/*!50003 DROP PROCEDURE IF EXISTS `sp_inscribir_aprendiz` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_inscribir_aprendiz`(
IN p_id_curso INT,
IN p_id_usuario BIGINT
)
BEGIN
INSERT INTO curso_aprendiz
(
estado_c_a,
avance,
id_curso_c_a,
id_usuario_c_a
)
VALUES
(
'Activo',
0.00,
p_id_curso,
p_id_usuario
);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_registrar_resultado` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_resultado`(
IN p_id_usuario BIGINT,
IN p_id_evaluacion INT,
IN p_calificacion DECIMAL(4,2)
)
BEGIN

DECLARE v_puntaje_aprobacion DECIMAL(4,2);

SELECT puntaje_aprobacion
INTO v_puntaje_aprobacion
FROM evaluacion
WHERE id_evaluacion = p_id_evaluacion;

INSERT INTO resultado_evaluacion
(
calificacion,
aprobado,
id_evaluacion_r,
id_usuario_r
)
VALUES
(
p_calificacion,
p_calificacion >= v_puntaje_aprobacion,
p_id_evaluacion,
p_id_usuario
);

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `ver_curso_modulo_leccion`
--

/*!50001 DROP VIEW IF EXISTS `ver_curso_modulo_leccion`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `ver_curso_modulo_leccion` AS select `u`.`nombre` AS `nombre`,`u`.`apellido` AS `apellido`,`r`.`nombre_rol` AS `nombre_rol`,`c`.`titulo_curso` AS `titulo_curso`,`ca`.`avance` AS `avance` from (((`curso_aprendiz` `ca` join `usuario` `u` on((`ca`.`id_usuario_c_a` = `u`.`id_usuario`))) join `curso` `c` on((`ca`.`id_curso_c_a` = `c`.`id_curso`))) join `rol` `r` on((`u`.`id_rol_u` = `r`.`id_rol`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_aprendiz_aprobados`
--

/*!50001 DROP VIEW IF EXISTS `vw_aprendiz_aprobados`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_aprendiz_aprobados` AS select `u`.`nombre` AS `nombre`,`u`.`apellido` AS `apellido`,`ca`.`estado_c_a` AS `estado_c_a`,`c`.`titulo_curso` AS `titulo_curso` from ((`usuario` `u` join `curso_aprendiz` `ca` on((`u`.`id_usuario` = `ca`.`id_usuario_c_a`))) join `curso` `c` on((`c`.`id_curso` = `ca`.`id_curso_c_a`))) where (`ca`.`avance` = 100) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-07 16:47:22
