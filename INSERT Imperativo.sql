-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-08-2021 a las 02:35:00
-- Versión del servidor: 5.7.17
-- Versión de PHP: 7.1.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `softboos`
--
CREATE DATABASE IF NOT EXISTS `softboos` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `softboos`;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`ESTADO_ID`, `DESCRIPCION`) VALUES
(1, 'activo'),
(2, 'inactivo'),
(3, 'borrado'),
(4, 'agregado'),
(5, 'modificado');

--
-- Volcado de datos para la tabla `estado_pedido`
--

INSERT INTO `estado_pedido` (`ESTADO_ID`, `DESCRIPCION`) VALUES
(1, 'PENDIENTE'),
(2, 'CANCELADO'),
(3, 'PAGADO');

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`MODULO_ID`, `NOMBRE`, `ESTADO_ID`) VALUES
(1, 'Dashboard', 1),
(2, 'Cargos', 1),
(3, 'Clientes', 1),
(4, 'Empleados', 1),
(5, 'Productos', 1),
(6, 'Rubros', 1),
(7, 'Unidades de Medida', 1);

--
-- Volcado de datos para la tabla `niveles_acceso`
--

INSERT INTO `niveles_acceso` (`NIVELACCESO_ID`, `NIVEL_ACCESO`, `ACCION`) VALUES
(1, 1, 'LEER'),
(2, 2, 'LEER'),
(3, 3, 'LEER'),
(4, 4, 'LEER'),
(5, 5, 'LEER'),
(6, 6, 'AGREGAR'),
(7, 7, 'AGREGAR'),
(8, 8, 'AGREGAR'),
(9, 9, 'AGREGAR'),
(10, 10, 'AGREGAR'),
(11, 11, 'MODIFICAR'),
(12, 12, 'MODIFICAR'),
(13, 13, 'MODIFICAR'),
(14, 14, 'MODIFICAR'),
(15, 15, 'MODIFICAR'),
(16, 16, 'BORRAR'),
(17, 17, 'BORRAR'),
(18, 18, 'BORRAR'),
(19, 19, 'BORRAR'),
(20, 20, 'BORRAR');

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`CARGO_ID`, `ESTADO_ID`, `NIVELACCESO_ID`, `CARGO_DESCRIPCION`, `FECHA_ALTA`, `FECHA_BAJA`) VALUES
(1, 1, 20, 'MASTER', '2021-05-22 13:18:54', NULL);

--
-- Volcado de datos para la tabla `cargos_auditoria`
--

-- INSERT INTO `cargos_auditoria` (`CARGO_AUDITORIA_ID`, `CARGO_ID`, `NIVELACCESO_ID`, `CARGO_DESCRIPCION`, `FECHA`, `ESTADO`, `ESTADO_ID`, `DB_USER_ID`) VALUES
-- (1, 1, 20, 'MASTER', '2021-05-28 21:54:19', 1, 5, 'root@localhost');

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`SUCURSAL_ID`, `RAZONSOCIAL`, `CODIGO_SUCURSAL`, `CUIT`, `MAIL`, `TELEFONO`, `WEB`, `DIRECCION`, `LOGO_URL`, `FECHA_ALTA`, `FECHA_BAJA`) VALUES
(1, 'YOSUKO PIZZAS', '00001', '123456789012', 'yosukopizzas@yosuko.com', '543764123456', NULL, 'av siempre viva', NULL, '2021-05-04 08:00:00', NULL);

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`EMPLEADO_ID`, `SUCURSAL_ID`, `CARGO_ID`, `ESTADO_ID`, `DNI`, `NOMBRE`, `APELLIDO`, `FECHA_NACIMIENTO`, `CUIL`, `CONTRASENA`, `MAIL`, `TELEFONO`, `DIRECCION`, `FECHA_ALTA`, `FECHA_BAJA`) VALUES
(1, 1, 1, 1, '36636440', 'Leandro Matias', 'Boos', '1991-12-19', '20366364405', 'putoelquelolea', NULL, NULL, 'av siempre viva', '2021-05-22 13:27:36', NULL);

--
-- Volcado de datos para la tabla `factura_tipo`
--

INSERT INTO `factura_tipo` (`FACTURATIPO_ID`, `FACTURA_TIPO`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C');

--
-- Volcado de datos para la tabla `forma_pago`
--

INSERT INTO `forma_pago` (`FORMAPAGO_ID`, `FORMA_PAGO`) VALUES
(1, 'EFECTIVO'),
(2, 'TARJETA DEBITO'),
(3, 'TARJETA CREDITO');

--
-- Volcado de datos para la tabla `iva`
--

INSERT INTO `iva` (`IVA_ID`, `IVA_NOMBRE`, `IVA_PORCENTAJE`) VALUES
(1, 'IVA-10.5', '10.5'),
(2, 'IVA-21', '21.0');

--
-- Volcado de datos para la tabla `movimiento_tipo`
--

INSERT INTO `movimiento_tipo` (`MOVIMIENTOTIPO_ID`, `DESCRIPCION`, `TM_TIPO`) VALUES
(1, 'COMPRA', 2),
(2, 'VENTA', 1),
(3, 'INGRESO', 1),
(4, 'EGRESO', 2),
(5, 'APERTURA', 0),
(6, 'CIERRE', 0);

--
-- Volcado de datos para la tabla `permisos`
--

-- ESTO ESTA COMANTADO PORQUE EXISTE UN TRIGGER QUE YA HACE EL INSERT

-- INSERT INTO `permisos` (`PERMISO_ID`, `CARGO_ID`, `MODULO_ID`, `LEER`, `AGREGAR`, `MODIFICAR`, `BORRAR`) VALUES
-- (1, 1, 1, 1, 1, 1, 1),
-- (2, 1, 2, 1, 1, 1, 1),
-- (3, 1, 3, 1, 1, 1, 1),
-- (4, 1, 4, 1, 1, 1, 1),
-- (5, 1, 5, 1, 1, 1, 1),
-- (6, 1, 7, 1, 1, 1, 1),
-- (7, 1, 6, 1, 1, 1, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
