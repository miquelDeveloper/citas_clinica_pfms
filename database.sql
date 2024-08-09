CREATE DATABASE IF NOT EXISTS `citas_clinica` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `citas_clinica`;

CREATE TABLE IF NOT EXISTS `citas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `dni` char(9) NOT NULL,
  `telefono` char(9) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tipo_cita` varchar(255) NOT NULL,
  `fecha` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `citas_fecha_IDX` (`fecha`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3518 DEFAULT CHARSET=utf8mb4;