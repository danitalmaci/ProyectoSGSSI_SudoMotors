-- phpMyAdmin SQL Dump
-- versión 5.0.2
-- Servidor: db
-- Tiempo de generación: 04-11-2025
-- Versión del servidor: 10.5.5-MariaDB
-- Versión de PHP: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

--
-- Base de datos: `database`
--

-- --------------------------------------------------------
-- Tabla de usuario
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `USUARIO` (
  `DNI` VARCHAR(10) NOT NULL UNIQUE,
  `NOMBRE` VARCHAR(100) NOT NULL,
  `APELLIDOS` VARCHAR(150) NOT NULL,
  `TELEFONO` VARCHAR(15) NOT NULL UNIQUE,
  `EMAIL` VARCHAR(100) NOT NULL UNIQUE,
  `F_NACIMIENTO` DATE NOT NULL,
  `CONTRASENA` VARCHAR(255) NOT NULL,
  `USERNAME` VARCHAR(50) NOT NULL UNIQUE,
   `ROLE` VARCHAR(20) DEFAULT 'user',
  PRIMARY KEY (`DNI`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Datos de ejemplo de usuario
--

INSERT INTO `USUARIO` (`DNI`, `NOMBRE`, `APELLIDOS`, `TELEFONO`, `EMAIL`, `F_NACIMIENTO`, `CONTRASENA`, `USERNAME`) VALUES
('12345678-Z', 'Aitor', 'Jimenez Jimenez', '668252000', 'aitorji@gmail.com', '2002-10-12', 'RonCola300?', 'aitorjiji'),
('22770213-Y', 'June', 'Alvarez Jimenez', '667925412', 'juneji@gmail.com', '2001-02-16', 'VodkaLimon200?', 'junecastro');
INSERT INTO `USUARIO` (`DNI`, `NOMBRE`, `APELLIDOS`, `TELEFONO`, `EMAIL`, `F_NACIMIENTO`, `CONTRASENA`, `USERNAME`, `ROLE`) VALUES
('36470906-J', 'Administrador', 'Administrador', '123456789', 'admin@gmail.com', '2000-01-01', 'admin02.', 'admin', 'admin');


-- --------------------------------------------------------
-- Tabla para registrar intentos de login
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `LOGIN_INTENTOS` (
  `ID` INT AUTO_INCREMENT PRIMARY KEY,
  `USERNAME` VARCHAR(50),
  `IP_ADDRESS` VARCHAR(45),
  `INTENTOS` INT DEFAULT 0,
  `LAST_ATTEMPT` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- Tabla de logs de actividad de inicio de sesión
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `LOGIN_LOGS` (
  `ID` INT AUTO_INCREMENT PRIMARY KEY,
  `USERNAME` VARCHAR(50),
  `IP_ADDRESS` VARCHAR(45),
  `FECHA` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `RESULTADO` ENUM('exito','fallo') NOT NULL,
  `NAVEGADOR` TEXT,
  `DETALLES` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- Tabla de vehículo
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `VEHICULO` (
  `MATRICULA` VARCHAR(8) NOT NULL UNIQUE,
  `MARCA` VARCHAR(100) NOT NULL,
  `MODELO` VARCHAR(100) NOT NULL,
  `ANO` INT NOT NULL,
  `KMS` INT NOT NULL,
  PRIMARY KEY (`MATRICULA`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Datos de ejemplo de vehículo
--

INSERT INTO `VEHICULO` (`MATRICULA`, `MARCA`, `MODELO`, `ANO`, `KMS`) VALUES
('7895 TYU', 'Kia', 'Sportage', 2018, 205623),
('6255 XDD', 'Ferrari', 'Spider', 2001, 89985),
('3326 IOP', 'Ford', 'Focus', 2025, 235);


COMMIT;

