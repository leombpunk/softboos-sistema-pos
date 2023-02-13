-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-02-2023 a las 22:28:53
-- Versión del servidor: 10.4.22-MariaDB
-- Versión de PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `PrCajaIngresoEgresoSegunFacturas`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PrCajaIngresoEgresoSegunFacturas` (IN `id_factura` INT(11) UNSIGNED, IN `tipo` INT(1) UNSIGNED, IN `descripcion` VARCHAR(100), OUT `salida` INT(1))  NO SQL
BEGIN
DECLARE _empleado_id int(11);
DECLARE _nfactura bigint(13);
DECLARE _monto decimal(8,2);
SELECT 1 INTO salida;
IF (tipo < 0 OR tipo > 2) THEN
    SELECT 0 INTO salida;
ELSE
    IF (_monto AND tipo = 1) THEN
		IF NOT(SELECT fc.FACTURACOMPRA_ID FROM facturas_compra fc WHERE fc.FACTURACOMPRA_ID = id_factura) THEN
			SELECT 0 INTO salida;
		ELSE
			SELECT fc.NUMERO_FACTURA, fc.EMPLEADO_ID INTO _nfactura, _empleado_id FROM facturas_compra AS fc WHERE fc.FACTURACOMPRA_ID = id_factura;
		END IF;
		SELECT SUM(dp.PRECIO) INTO _monto FROM detalle_pedidos_compra AS dp WHERE dp.FACTURACOMPRA_ID = id_factura;
       	INSERT INTO movimiento_caja(EMPLEADO_ID, FACTURACOMPRA_ID, MC_SALDO_ACTUAL, MC_MONTO_E_S, MC_FECHA_MOV, TIPO, DESCRIPCION) SELECT _empleado_id, id_factura, MC_SALDO_ACTUAL - (_monto), _monto, NOW(), tipo, CONCAT(descripcion,' ',_nfactura) FROM movimiento_caja WHERE MC_ID = FcMovimientoCajaUltimaID();		
    ELSEIF (_monto AND tipo = 2) THEN 
		IF NOT(SELECT fv.FACTURAVENTA_ID FROM facturas_venta fv WHERE fv.FACTURAVENTA_ID = id_factura) THEN
			SELECT 0 INTO salida;
		ELSE
			SELECT fv.NUMERO_FACTURA, fv.EMPLEADO_ID INTO _nfactura, _empleado_id FROM facturas_venta AS fv WHERE fv.FACTURACION_ID = id_factura;
		END IF;
		SELECT SUM(dp.PRECIO) INTO _monto FROM detalle_pedidos_venta AS dp WHERE dp.FACTURAVENTA_ID = id_factura;
       	INSERT INTO movimiento_caja(EMPLEADO_ID, FACTURAVENTA_ID, MC_SALDO_ACTUAL, MC_MONTO_E_S, MC_FECHA_MOV, TIPO, DESCRIPCION) SELECT _empleado_id, id_factura, MC_SALDO_ACTUAL + (_monto), _monto, NOW(), tipo, CONCAT(descripcion,' ',_nfactura) FROM movimiento_caja WHERE MC_ID = FcMovimientoCajaUltimaID();        
    ELSE
        SELECT 1 INTO salida;
    END IF;
END IF;
END$$

DROP PROCEDURE IF EXISTS `PrDetallepedidoStockUpdateTresSimple`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PrDetallepedidoStockUpdateTresSimple` (IN `id_stock` INT(11) UNSIGNED, IN `id_unidad` INT(11) UNSIGNED, IN `cantidad` DECIMAL(8,3), IN `id_operacion` INT(11) UNSIGNED, OUT `retorno` DECIMAL(8,3))  NO SQL
BEGIN
DECLARE consulta1 int;
DECLARE prioridad1 int;
DECLARE unirelid int;
DECLARE valor decimal(8,3);
DECLARE cant_update decimal(8,3);

START TRANSACTION;
SET consulta1 = (SELECT 1 FROM mercaderias_unidadesmedida AS uso WHERE uso.MERCADERIA_ID = id_stock AND uso.UNIMEDIDA_ID = id_unidad);
IF consulta1 THEN
    SET prioridad1 = (SELECT uso.UNIMEDIDA_ID FROM 	mercaderias_unidadesmedida AS uso WHERE uso.MERCADERIA_ID = id_stock AND uso.PRIORIDAD = 1);
    IF prioridad1 THEN
    	SET unirelid = (SELECT tum.UNIMEDIDA_ID FROM unidades_medida AS tum WHERE tum.UNIDADMINIMA_ID = prioridad1 AND tum.UNIMEDIDA_ID = id_unidad LIMIT 1);
        SET valor = (SELECT tum.VALOR FROM unidades_medida AS tum WHERE tum.UNIDADMINIMA_ID = prioridad1 AND tum.UNIMEDIDA_ID = id_unidad LIMIT 1);
        IF id_unidad = prioridad1 THEN
        	SET cant_update = (1*cantidad)/valor;      
        ELSEIF id_unidad = unirelid THEN
        	SET cant_update = (valor*cantidad)/1;       
        END IF;
        SELECT cant_update INTO retorno;
    END IF;
    COMMIT;
ELSE 
    ROLLBACK;
END IF;
END$$

DROP PROCEDURE IF EXISTS `PrRecetaElaboradaUpdateStock`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PrRecetaElaboradaUpdateStock` (IN `idreceta` INT(11) UNSIGNED, IN `idunidad` INT(11) UNSIGNED, IN `cantidad` DECIMAL(11,3), OUT `dato` INT(11) UNSIGNED)  NO SQL
BEGIN
DECLARE done INT DEFAULT FALSE;
DECLARE _calculado decimal(11,3);

DECLARE _idart INT;
DECLARE _iduni INT;
DECLARE _cant DECIMAL(11,3);

DECLARE _stockid INT DEFAULT 0;
DECLARE _cantidadUpdate DECIMAL(11,3) DEFAULT 0.0;

DECLARE cur_insumos CURSOR FOR SELECT MERCADERIA_ID, UNIMEDIDA_ID, INSUMO_CANTIDAD FROM lista_insumos WHERE RECETA_ID = idreceta;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

OPEN cur_insumos;
START TRANSACTION;
SET _stockid = (SELECT MERCADERIA_ID FROM recetas WHERE RECETA_ID = idreceta);
SET dato = 0;

CALL PrDetallepedidoStockUpdateTresSimple(_stockid, idunidad, cantidad, 1, _cantidadUpdate);

read_loop: LOOP
  FETCH cur_insumos INTO _idart, _iduni, _cant;
  IF done THEN
  	LEAVE read_loop;
  END IF;
  
  SET _calculado = cantidad * _cant;
  CALL PrDetallepedidoStockUpdateTresSimple(_idart, _iduni, _calculado, 1, _cantidadUpdate);
  IF _cantidadUpdate THEN
  	UPDATE stock_actual SET CANTIDAD_ANTERIOR = CANTIDAD_ACTUAL, CANTIDAD_ACTUAL = CANTIDAD_ACTUAL - (_cantidadUpdate) WHERE MERCADERIA_ID = _idart;
    
  END IF;
END LOOP;

CALL PrDetallepedidoStockUpdateTresSimple(_stockid, idunidad, cantidad, 1, _cantidadUpdate);
IF _cantidadUpdate THEN
  	UPDATE stock_actual SET CANTIDAD_ANTERIOR = CANTIDAD_ACTUAL, CANTIDAD_ACTUAL = CANTIDAD_ACTUAL + (_cantidadUpdate) WHERE MERCADERIA_ID = _stockid;
    SELECT _cantidadUpdate INTO dato;
ELSE
	ROLLBACK;
END IF;
COMMIT;
CLOSE cur_insumos;
END$$

DROP PROCEDURE IF EXISTS `SpCargosSelectFull`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SpCargosSelectFull` ()  NO SQL
    COMMENT 'trae todos los datos del cargo'
SELECT c.CARGO_ID id, c.CARGO_DESCRIPCION cargo, c.ESTADO_ID estado_id, e.DESCRIPCION estado, c.NIVELACCESO_ID rango, na.ACCION alcance, c.FECHA_ALTA alta, c.FECHA_BAJA baja 
FROM cargos c 
INNER JOIN estado e ON e.ESTADO_ID = c.ESTADO_ID 
INNER JOIN niveles_acceso na ON na.NIVELACCESO_ID = c.NIVELACCESO_ID$$

DROP PROCEDURE IF EXISTS `SpCargosSelectFullActivoInactivo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SpCargosSelectFullActivoInactivo` ()  NO SQL
    COMMENT 'trae todos los datos del cargo que esten activos e inactivos'
SELECT c.CARGO_ID id, c.CARGO_DESCRIPCION cargo, c.ESTADO_ID estado_id, e.DESCRIPCION estado, c.NIVELACCESO_ID rango, na.ACCION alcance, c.FECHA_ALTA alta, c.FECHA_BAJA baja 
FROM cargos c 
INNER JOIN estado e ON e.ESTADO_ID = c.ESTADO_ID 
INNER JOIN niveles_acceso na ON na.NIVELACCESO_ID = c.NIVELACCESO_ID 
WHERE c.ESTADO_ID < 3$$

DROP PROCEDURE IF EXISTS `SpCargosSelectOne`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SpCargosSelectOne` (IN `id_cargo` INT(11) UNSIGNED, IN `status_minor_to` INT(1) UNSIGNED)  NO SQL
    COMMENT 'trae un cargo segun parametros'
BEGIN
DECLARE _id int(11) unsigned;
DECLARE _status int(1) unsigned;
SET _id = id_cargo;
SET _status = status_minor_to;
SELECT c.CARGO_ID id, c.CARGO_DESCRIPCION cargo, c.ESTADO_ID estado_id, e.DESCRIPCION estado, c.NIVELACCESO_ID rango, na.ACCION alcance, c.FECHA_ALTA alta, c.FECHA_BAJA baja 
FROM cargos c 
INNER JOIN estado e ON e.ESTADO_ID = c.ESTADO_ID 
INNER JOIN niveles_acceso na ON na.NIVELACCESO_ID = c.NIVELACCESO_ID 
WHERE c.CARGO_ID = _id AND c.ESTADO_ID < _status;
END$$

DROP PROCEDURE IF EXISTS `SpEmpleadoAlterPass`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SpEmpleadoAlterPass` (IN `usu` CHAR(11), IN `pass` VARCHAR(16), IN `servidor` VARCHAR(15))  NO SQL
BEGIN
	SET @sql = CONCAT("ALTER USER '",usu,"'@'",servidor,"' IDENTIFIED BY '",pass,"'");
	PREPARE wea FROM @sql;
    EXECUTE wea;
    DEALLOCATE PREPARE wea;
END$$

DROP PROCEDURE IF EXISTS `SpEmpleadoCreateUser`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SpEmpleadoCreateUser` (IN `usu` CHAR(11), IN `pass` VARCHAR(16), IN `db` VARCHAR(20), IN `servidor` VARCHAR(15))  NO SQL
BEGIN
    SET @sql = CONCAT('CREATE USER ', "'", usu, "'", "@'localhost'", ' IDENTIFIED BY ', "'" , pass , "' WITH MAX_USER_CONNECTIONS 1 PASSWORD EXPIRE INTERVAL 365 DAY" );
    PREPARE stmt1 FROM @sql; 
    EXECUTE stmt1; 
    DEALLOCATE PREPARE stmt1;
    
    SET @grant = CONCAT('GRANT EXECUTE ON `softboos`.* TO ',"'",usu,"'@'localhost'");
    PREPARE stmt2 FROM @grant;
    EXECUTE stmt2;
    DEALLOCATE PREPARE stmt2;
END$$

DROP PROCEDURE IF EXISTS `SpEmpleadosDatosSelect`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SpEmpleadosDatosSelect` (IN `emple_id` INT(11) UNSIGNED)  NO SQL
    COMMENT 'trae todos los datos del empleado'
BEGIN
SELECT e.EMPLEADO_ID, e.DNI, e.NOMBRE, e.APELLIDO, e.FECHA_NACIMIENTO, e.CUIL, e.CONTRASENA, e.MAIL, e.TELEFONO, e.DIRECCION, e.SUCURSAL_ID, s.CODIGO_SUCURSAL, s.RAZONSOCIAL, s.DIRECCION, e.CARGO_ID, c.NIVELACCESO_ID, c.CARGO_DESCRIPCION 
FROM empleados e 
INNER JOIN cargos c ON e.CARGO_ID = c.CARGO_ID 
INNER JOIN sucursales s ON e.SUCURSAL_ID = s.SUCURSAL_ID 
WHERE e.EMPLEADO_ID = emple_id;
END$$

DROP PROCEDURE IF EXISTS `SpPermisosGet`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SpPermisosGet` (IN `cargo_id` INT(11) UNSIGNED, IN `modulo_id` INT(11) UNSIGNED)  NO SQL
BEGIN
SELECT m.MODULO_ID, m.NOMBRE, p.LEER, p.AGREGAR, p.MODIFICAR, p.BORRAR 
FROM permisos p 
INNER JOIN modulos m ON p.MODULO_ID = m.MODULO_ID 
	AND m.MODULO_ID = modulo_id 
INNER JOIN estado e ON e.ESTADO_ID = m.ESTADO_ID 
	AND e.DESCRIPCION = 'activo' 
WHERE p.CARGO_ID = cargo_id;
END$$

--
-- Funciones
--
DROP FUNCTION IF EXISTS `FcMovimientoCajaUltimaID`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `FcMovimientoCajaUltimaID` () RETURNS INT(11) UNSIGNED NO SQL
BEGIN
DECLARE _ultimo_id int(11);
SELECT MAX(MC_ID) INTO _ultimo_id FROM movimiento_caja;
RETURN _ultimo_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `atributos`
--

DROP TABLE IF EXISTS `atributos`;
CREATE TABLE `atributos` (
  `ATRIBUTO_ID` int(11) UNSIGNED NOT NULL,
  `RUBRO_ID` int(11) UNSIGNED NOT NULL,
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `NOMBRE` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `UNIDAD` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `DESCRIPCION` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `TAG` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `TYPE` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `NAME` varchar(15) COLLATE utf8_spanish_ci NOT NULL COMMENT 'el nombre par el tag lo creo programaticamente segun en nombre establecido por el usuario anteponiendo la palabra filtro mas empezando con mayusculas el nombre'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

DROP TABLE IF EXISTS `cargos`;
CREATE TABLE `cargos` (
  `CARGO_ID` int(11) UNSIGNED NOT NULL,
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `NIVELACCESO_ID` int(11) UNSIGNED NOT NULL,
  `CARGO_DESCRIPCION` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `FECHA_BAJA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`CARGO_ID`, `ESTADO_ID`, `NIVELACCESO_ID`, `CARGO_DESCRIPCION`, `FECHA_ALTA`, `FECHA_BAJA`) VALUES
(1, 1, 20, 'MASTER', '2022-08-17 18:34:05', NULL);

--
-- Disparadores `cargos`
--
DROP TRIGGER IF EXISTS `TrCargoFechaInsert`;
DELIMITER $$
CREATE TRIGGER `TrCargoFechaInsert` BEFORE INSERT ON `cargos` FOR EACH ROW SET NEW.FECHA_ALTA = NOW()
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `TrCargosInsertAuditoria`;
DELIMITER $$
CREATE TRIGGER `TrCargosInsertAuditoria` AFTER INSERT ON `cargos` FOR EACH ROW BEGIN
INSERT INTO cargos_auditoria(CARGO_ID, NIVELACCESO_ID, CARGO_DESCRIPCION, FECHA, ESTADO, ESTADO_ID, DB_USER_ID) VALUES(NEW.CARGO_ID, NEW.NIVELACCESO_ID, NEW.CARGO_DESCRIPCION, NOW(), NEW.ESTADO_ID, 4, CURRENT_USER());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `TrCargosInsertInPermisos`;
DELIMITER $$
CREATE TRIGGER `TrCargosInsertInPermisos` AFTER INSERT ON `cargos` FOR EACH ROW BEGIN
DECLARE done INT DEFAULT FALSE;
DECLARE idModulo INT;
DECLARE cur_modulos CURSOR FOR SELECT MODULO_ID FROM modulos;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

OPEN cur_modulos;
read_loop: LOOP
	FETCH cur_modulos INTO idModulo;
    IF done THEN
    	LEAVE read_loop;
	END IF;
    IF NEW.CARGO_ID THEN
    	INSERT INTO permisos(CARGO_ID, MODULO_ID, LEER, AGREGAR, MODIFICAR, BORRAR) VALUES(NEW.CARGO_ID, idModulo, 1, 1, 1, 1);
    ELSE
    	INSERT INTO permisos(CARGO_ID, MODULO_ID, LEER, AGREGAR, MODIFICAR, BORRAR) VALUES(NEW.CARGO_ID, idModulo, 0, 0, 0, 0);
    END IF;
    /*INSERT INTO permisos(CARGO_ID, MODULO_ID, LEER, AGREGAR, MODIFICAR, BORRAR) VALUES(NEW.CARGO_ID, idModulo, 0, 0, 0, 0);*/
END LOOP;
CLOSE cur_modulos;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `TrCargosUpdateAuditoria`;
DELIMITER $$
CREATE TRIGGER `TrCargosUpdateAuditoria` AFTER UPDATE ON `cargos` FOR EACH ROW BEGIN
IF NEW.ESTADO_ID = 3 THEN
	INSERT INTO cargos_auditoria(CARGO_ID, NIVELACCESO_ID, CARGO_DESCRIPCION, FECHA, ESTADO, ESTADO_ID, DB_USER_ID) VALUES(NEW.CARGO_ID, NEW.NIVELACCESO_ID, NEW.CARGO_DESCRIPCION, NOW(), NEW.ESTADO_ID, 3, CURRENT_USER());
ELSE
	INSERT INTO cargos_auditoria(CARGO_ID, NIVELACCESO_ID, CARGO_DESCRIPCION, FECHA, ESTADO, ESTADO_ID, DB_USER_ID) VALUES(NEW.CARGO_ID, NEW.NIVELACCESO_ID, NEW.CARGO_DESCRIPCION, NOW(), NEW.ESTADO_ID, 5, CURRENT_USER());
END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos_auditoria`
--

DROP TABLE IF EXISTS `cargos_auditoria`;
CREATE TABLE `cargos_auditoria` (
  `CARGO_AUDITORIA_ID` int(11) UNSIGNED NOT NULL,
  `CARGO_ID` int(11) UNSIGNED NOT NULL,
  `NIVELACCESO_ID` int(11) UNSIGNED NOT NULL,
  `CARGO_DESCRIPCION` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `FECHA` datetime NOT NULL,
  `ESTADO` int(11) UNSIGNED NOT NULL,
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `DB_USER_ID` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `cargos_auditoria`
--

INSERT INTO `cargos_auditoria` (`CARGO_AUDITORIA_ID`, `CARGO_ID`, `NIVELACCESO_ID`, `CARGO_DESCRIPCION`, `FECHA`, `ESTADO`, `ESTADO_ID`, `DB_USER_ID`) VALUES
(17, 1, 20, 'MASTER', '2022-08-17 18:34:05', 1, 4, 'root@localhost');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `CLIENTE_ID` int(11) UNSIGNED NOT NULL,
  `DNI` char(8) COLLATE utf8_spanish_ci NOT NULL,
  `NOMBRE` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `APELLIDO` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `FECHA_NACIMIENTO` date NOT NULL,
  `CUIL` char(11) COLLATE utf8_spanish_ci DEFAULT NULL,
  `TELEFONO` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `MAIL` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `PASS` varchar(16) COLLATE utf8_spanish_ci DEFAULT NULL,
  `DIRECCION` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `FECHA_BAJA` datetime DEFAULT NULL,
  `ESTADO_ID` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`CLIENTE_ID`, `DNI`, `NOMBRE`, `APELLIDO`, `FECHA_NACIMIENTO`, `CUIL`, `TELEFONO`, `MAIL`, `PASS`, `DIRECCION`, `FECHA_ALTA`, `FECHA_BAJA`, `ESTADO_ID`) VALUES
(1000000056, '12345678', 'FELIPE', 'MELO', '2000-04-15', '12345678910', NULL, 'felipe@correo.com', NULL, 'VIVE EN UNA NUBE DE PEDOS', '2023-02-13 18:18:43', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuentos`
--

DROP TABLE IF EXISTS `descuentos`;
CREATE TABLE `descuentos` (
  `DESCUENTO_ID` int(11) UNSIGNED NOT NULL,
  `EMPLEADO_ID` int(11) UNSIGNED NOT NULL,
  `PRODCUTO_ID` int(11) UNSIGNED NOT NULL,
  `UNIMEDIDA_ID` int(11) UNSIGNED NOT NULL,
  `DESCUENTO` decimal(4,2) UNSIGNED NOT NULL,
  `CANTIDAD` decimal(8,2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos_compra`
--

DROP TABLE IF EXISTS `detalle_pedidos_compra`;
CREATE TABLE `detalle_pedidos_compra` (
  `DPCOMPRA_ID` int(11) UNSIGNED NOT NULL,
  `FACTURACOMPRA_ID` int(11) UNSIGNED NOT NULL,
  `MERCADERIA_ID` int(11) UNSIGNED NOT NULL COMMENT 'refiere a artstock.id',
  `UNIMEDIDA_ID` int(11) UNSIGNED NOT NULL,
  `CANTIDAD` decimal(8,3) NOT NULL COMMENT 'cantidad en decimales',
  `CANTIDAD_REAL` decimal(8,3) NOT NULL,
  `PRECIO` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos_venta`
--

DROP TABLE IF EXISTS `detalle_pedidos_venta`;
CREATE TABLE `detalle_pedidos_venta` (
  `DPVENTA_ID` int(11) UNSIGNED NOT NULL,
  `FACTURAVENTA_ID` int(11) UNSIGNED NOT NULL,
  `MERCADERIA_ID` int(11) UNSIGNED NOT NULL COMMENT 'refiere a artstock.id',
  `UNIMEDIDA_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `CANTIDAD` decimal(8,3) NOT NULL COMMENT 'cantidad en decimales',
  `CANTIDAD_REAL` decimal(8,3) NOT NULL,
  `PRECIO` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

DROP TABLE IF EXISTS `empleados`;
CREATE TABLE `empleados` (
  `EMPLEADO_ID` int(11) UNSIGNED NOT NULL,
  `SUCURSAL_ID` int(11) UNSIGNED NOT NULL,
  `CARGO_ID` int(11) UNSIGNED NOT NULL,
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `DNI` char(8) COLLATE utf8_spanish_ci NOT NULL,
  `NOMBRE` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `APELLIDO` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `FECHA_NACIMIENTO` date NOT NULL,
  `CUIL` char(11) COLLATE utf8_spanish_ci NOT NULL,
  `CONTRASENA` varchar(16) COLLATE utf8_spanish_ci NOT NULL,
  `MAIL` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `TELEFONO` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `DIRECCION` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `FECHA_BAJA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`EMPLEADO_ID`, `SUCURSAL_ID`, `CARGO_ID`, `ESTADO_ID`, `DNI`, `NOMBRE`, `APELLIDO`, `FECHA_NACIMIENTO`, `CUIL`, `CONTRASENA`, `MAIL`, `TELEFONO`, `DIRECCION`, `FECHA_ALTA`, `FECHA_BAJA`) VALUES
(1, 1, 1, 1, '36636440', 'Leandro Matias', 'Boos', '1991-12-19', '20366364405', 'putoelquelolea', NULL, NULL, 'av siempre viva', '2021-05-22 13:27:36', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

DROP TABLE IF EXISTS `estado`;
CREATE TABLE `estado` (
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `DESCRIPCION` varchar(10) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`ESTADO_ID`, `DESCRIPCION`) VALUES
(1, 'activo'),
(2, 'inactivo'),
(3, 'borrado'),
(4, 'agregado'),
(5, 'modificado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_pedido`
--

DROP TABLE IF EXISTS `estado_pedido`;
CREATE TABLE `estado_pedido` (
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `DESCRIPCION` varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `estado_pedido`
--

INSERT INTO `estado_pedido` (`ESTADO_ID`, `DESCRIPCION`) VALUES
(1, 'PENDIENTE'),
(2, 'CANCELADO'),
(3, 'PAGADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturacompra_formapago`
--

DROP TABLE IF EXISTS `facturacompra_formapago`;
CREATE TABLE `facturacompra_formapago` (
  `FACTURACFP_ID` int(11) UNSIGNED NOT NULL,
  `FACTURA_ID` int(11) UNSIGNED NOT NULL,
  `FORMAPAGO_ID` int(11) UNSIGNED NOT NULL,
  `CANTIDAD_PAGO` tinyint(2) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas_compra`
--

DROP TABLE IF EXISTS `facturas_compra`;
CREATE TABLE `facturas_compra` (
  `FACTURACOMPRA_ID` int(11) UNSIGNED NOT NULL,
  `FACTURATIPO_ID` int(11) UNSIGNED NOT NULL,
  `NUMERO_FACTURA` bigint(13) UNSIGNED ZEROFILL NOT NULL,
  `PROVEEDOR_ID` int(11) UNSIGNED NOT NULL,
  `SUCURSAL_ID` int(11) UNSIGNED NOT NULL,
  `EMPLEADO_ID` int(11) UNSIGNED NOT NULL,
  `TESTIGO_ID` int(11) UNSIGNED NOT NULL,
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `FECHA_EMISION` date NOT NULL,
  `DIRECCION_ENVIO` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas_venta`
--

DROP TABLE IF EXISTS `facturas_venta`;
CREATE TABLE `facturas_venta` (
  `FACTURAVENTA_ID` int(11) UNSIGNED NOT NULL,
  `FACTURATIPO_ID` int(11) UNSIGNED NOT NULL,
  `NUMERO_FACTURA` bigint(13) UNSIGNED ZEROFILL NOT NULL,
  `SUCURSAL_ID` int(11) UNSIGNED NOT NULL,
  `CLIENTE_ID` int(11) UNSIGNED NOT NULL,
  `EMPLEADO_ID` int(11) UNSIGNED NOT NULL,
  `TESTIGO_ID` int(11) UNSIGNED NOT NULL,
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `FECHA_EMISION` date NOT NULL,
  `DIRECCION_ENVIO` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturaventa_formapago`
--

DROP TABLE IF EXISTS `facturaventa_formapago`;
CREATE TABLE `facturaventa_formapago` (
  `FACTURAVFP_ID` int(11) UNSIGNED NOT NULL,
  `FACTURA_ID` int(11) UNSIGNED NOT NULL,
  `FORMAPAGO_ID` int(11) UNSIGNED NOT NULL,
  `CANTIDAD_PAGO` tinyint(2) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura_tipo`
--

DROP TABLE IF EXISTS `factura_tipo`;
CREATE TABLE `factura_tipo` (
  `FACTURATIPO_ID` int(11) UNSIGNED NOT NULL,
  `FACTURA_TIPO` char(1) CHARACTER SET utf32 COLLATE utf32_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `factura_tipo`
--

INSERT INTO `factura_tipo` (`FACTURATIPO_ID`, `FACTURA_TIPO`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forma_pago`
--

DROP TABLE IF EXISTS `forma_pago`;
CREATE TABLE `forma_pago` (
  `FORMAPAGO_ID` int(11) UNSIGNED NOT NULL,
  `FORMA_PAGO` varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `forma_pago`
--

INSERT INTO `forma_pago` (`FORMAPAGO_ID`, `FORMA_PAGO`) VALUES
(1, 'EFECTIVO'),
(2, 'TARJETA DEBITO'),
(3, 'TARJETA CREDITO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iva`
--

DROP TABLE IF EXISTS `iva`;
CREATE TABLE `iva` (
  `IVA_ID` int(11) UNSIGNED NOT NULL,
  `IVA_NOMBRE` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `IVA_PORCENTAJE` decimal(3,1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `iva`
--

INSERT INTO `iva` (`IVA_ID`, `IVA_NOMBRE`, `IVA_PORCENTAJE`) VALUES
(1, 'IVA-10.5', '10.5'),
(2, 'IVA-21', '21.0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_insumos`
--

DROP TABLE IF EXISTS `lista_insumos`;
CREATE TABLE `lista_insumos` (
  `INSUMO_ID` int(11) UNSIGNED NOT NULL,
  `RECETA_ID` int(11) UNSIGNED NOT NULL,
  `MERCADERIA_ID` int(11) UNSIGNED NOT NULL,
  `UNIMEDIDA_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `INSUMO_CANTIDAD` decimal(8,3) UNSIGNED NOT NULL,
  `INSUMO_CANTIDAD_REAL` decimal(8,3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas` (
  `MARCA_ID` int(11) UNSIGNED NOT NULL,
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `NOMBRE` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `FECHA_ALTA` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mercaderias`
--

DROP TABLE IF EXISTS `mercaderias`;
CREATE TABLE `mercaderias` (
  `MERCADERIA_ID` int(11) UNSIGNED NOT NULL,
  `IVA_ID` int(11) UNSIGNED NOT NULL,
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `CODIGO` char(10) COLLATE utf8_spanish_ci NOT NULL,
  `NOMBRE` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `CANTIDAD_INICIAL` decimal(11,3) DEFAULT NULL,
  `ALERTA_MINCANT` decimal(11,3) UNSIGNED NOT NULL,
  `ALERTA_MAXCANT` decimal(11,3) UNSIGNED NOT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `FECHA_BAJA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mercaderias_cantidad_actual`
--

DROP TABLE IF EXISTS `mercaderias_cantidad_actual`;
CREATE TABLE `mercaderias_cantidad_actual` (
  `STOCKACTUAL_ID` int(11) UNSIGNED NOT NULL,
  `MERCADERIA_ID` int(11) UNSIGNED NOT NULL,
  `SUCURSAL_ID` int(11) UNSIGNED NOT NULL,
  `CANTIDAD_ACTUAL` decimal(11,3) NOT NULL,
  `CANTIDAD_ANTERIOR` decimal(11,3) NOT NULL,
  `FECHA_ALTA` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mercaderias_rubros`
--

DROP TABLE IF EXISTS `mercaderias_rubros`;
CREATE TABLE `mercaderias_rubros` (
  `MERCADERIA_ID` int(11) UNSIGNED NOT NULL,
  `RUBRO_ID` int(11) UNSIGNED NOT NULL,
  `ENTRADA` char(1) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mercaderias_unidadesmedida`
--

DROP TABLE IF EXISTS `mercaderias_unidadesmedida`;
CREATE TABLE `mercaderias_unidadesmedida` (
  `MERCAUNIMED_ID` int(11) UNSIGNED NOT NULL,
  `MERCADERIA_ID` int(11) UNSIGNED NOT NULL,
  `UNIMEDIDA_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `PRIORIDAD` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

DROP TABLE IF EXISTS `modulos`;
CREATE TABLE `modulos` (
  `MODULO_ID` int(11) UNSIGNED NOT NULL,
  `NOMBRE` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `ESTADO_ID` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
(7, 'Unidades de Medida', 1),
(8, 'Compras', 1);

--
-- Disparadores `modulos`
--
DROP TRIGGER IF EXISTS `TrModulosInsertPermisos`;
DELIMITER $$
CREATE TRIGGER `TrModulosInsertPermisos` AFTER INSERT ON `modulos` FOR EACH ROW BEGIN
DECLARE done INT DEFAULT FALSE;
DECLARE idCargo INT;
DECLARE cur_cargos CURSOR FOR SELECT CARGO_ID FROM cargos;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

OPEN cur_cargos;
read_loop: LOOP
	FETCH cur_cargos INTO idCargo;
    IF done THEN
    	LEAVE read_loop;
	END IF;
    /*seccion de codigo a testear*/
    IF idCargo = 1 THEN
    	INSERT INTO permisos(CARGO_ID, MODULO_ID, LEER, AGREGAR, MODIFICAR, BORRAR) VALUES(idCargo, 			NEW.MODULO_ID, 1, 1, 1, 1);
    ELSE
    	INSERT INTO permisos(CARGO_ID, MODULO_ID, LEER, AGREGAR, MODIFICAR, BORRAR) VALUES(idCargo, 			NEW.MODULO_ID, 0, 0, 0, 0);
    END IF;
    /*hasta aqui el codigo a testear*/
    /*INSERT INTO permisos(CARGO_ID, MODULO_ID, LEER, AGREGAR, MODIFICAR, BORRAR) VALUES(idCargo, NEW.MODULO_ID, 0, 0, 0, 0);*/
END LOOP;
CLOSE cur_cargos;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_caja`
--

DROP TABLE IF EXISTS `movimiento_caja`;
CREATE TABLE `movimiento_caja` (
  `MC_ID` int(11) UNSIGNED NOT NULL,
  `EMPLEADO_ID` int(11) UNSIGNED NOT NULL,
  `FACTURAC_ID` int(11) UNSIGNED DEFAULT NULL,
  `FACTURAV_ID` int(11) UNSIGNED DEFAULT NULL,
  `SALDO_ACTUAL` decimal(8,2) NOT NULL DEFAULT 0.00,
  `MC_MONTO_E_S` decimal(8,2) NOT NULL,
  `FECHA_MOVIMIENTO` datetime NOT NULL,
  `MOVIMIENTOTIPO_ID` int(11) UNSIGNED NOT NULL,
  `DESCRIPCION` varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_tipo`
--

DROP TABLE IF EXISTS `movimiento_tipo`;
CREATE TABLE `movimiento_tipo` (
  `MOVIMIENTOTIPO_ID` int(11) UNSIGNED NOT NULL,
  `DESCRIPCION` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `TM_TIPO` int(1) UNSIGNED NOT NULL COMMENT '1=ingreso, 2=egreso'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles_acceso`
--

DROP TABLE IF EXISTS `niveles_acceso`;
CREATE TABLE `niveles_acceso` (
  `NIVELACCESO_ID` int(11) UNSIGNED NOT NULL,
  `NIVEL_ACCESO` tinyint(3) UNSIGNED NOT NULL,
  `ACCION` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas_creditos`
--

DROP TABLE IF EXISTS `notas_creditos`;
CREATE TABLE `notas_creditos` (
  `CREDITO_ID` int(11) UNSIGNED NOT NULL,
  `CLIENTE_ID` int(11) UNSIGNED NOT NULL,
  `EMPLEADO_ID` int(11) UNSIGNED NOT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `MONTO` decimal(8,2) UNSIGNED NOT NULL,
  `ESTADO` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 activo, 0 inactivo',
  `DESCRIPCION` varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

DROP TABLE IF EXISTS `permisos`;
CREATE TABLE `permisos` (
  `PERMISO_ID` int(11) UNSIGNED NOT NULL,
  `CARGO_ID` int(11) UNSIGNED NOT NULL,
  `MODULO_ID` int(11) UNSIGNED NOT NULL,
  `LEER` tinyint(1) UNSIGNED NOT NULL,
  `AGREGAR` tinyint(1) UNSIGNED NOT NULL,
  `MODIFICAR` tinyint(1) UNSIGNED NOT NULL,
  `BORRAR` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`PERMISO_ID`, `CARGO_ID`, `MODULO_ID`, `LEER`, `AGREGAR`, `MODIFICAR`, `BORRAR`) VALUES
(86, 1, 1, 1, 1, 1, 1),
(87, 1, 2, 1, 1, 1, 1),
(88, 1, 3, 1, 1, 1, 1),
(89, 1, 4, 1, 1, 1, 1),
(90, 1, 5, 1, 1, 1, 1),
(91, 1, 6, 1, 1, 1, 1),
(92, 1, 7, 1, 1, 1, 1),
(93, 1, 8, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productosventa_atributos`
--

DROP TABLE IF EXISTS `productosventa_atributos`;
CREATE TABLE `productosventa_atributos` (
  `PROVATR_ID` int(11) UNSIGNED NOT NULL,
  `PV_COD` int(11) UNSIGNED NOT NULL,
  `ATRIBUTO_ID` int(11) UNSIGNED NOT NULL,
  `VALOR` varchar(15) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_compra`
--

DROP TABLE IF EXISTS `productos_compra`;
CREATE TABLE `productos_compra` (
  `PC_ID` int(11) UNSIGNED NOT NULL,
  `MERCADERIA_ID` int(11) UNSIGNED NOT NULL,
  `PC_NOMBRE` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `PC_FECHA_BAJA` datetime DEFAULT NULL,
  `PRECIO_COSTO` decimal(8,2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='tabla para que el triger de update no joda';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_venta`
--

DROP TABLE IF EXISTS `productos_venta`;
CREATE TABLE `productos_venta` (
  `PV_COD` int(11) UNSIGNED NOT NULL,
  `MERCADERIA_ID` int(11) UNSIGNED NOT NULL,
  `RUBRO_ID` int(11) UNSIGNED NOT NULL,
  `UNIMEDIDA_ID` int(11) UNSIGNED NOT NULL,
  `PV_NOMBRE` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `PV_DESCRIPCION` varchar(500) COLLATE utf8_spanish_ci DEFAULT NULL,
  `PV_IMAGENES` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `PV_PRECIO_VENTA` decimal(8,2) UNSIGNED NOT NULL,
  `PV_FECHA_LAST_UPDATE_PRICE` date DEFAULT NULL,
  `PV_FECHA_BAJA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
  `PROVEEDOR_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `RAZONSOCIAL` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `CUIT` char(12) COLLATE utf8_spanish_ci DEFAULT NULL,
  `MAIL` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `TELEFONO` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `WEB` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `DIRECCION` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `LOGO_URL` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `FECHA_BAJA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Disparadores `proveedores`
--
DROP TRIGGER IF EXISTS `TrProveedorFechaInsert`;
DELIMITER $$
CREATE TRIGGER `TrProveedorFechaInsert` BEFORE INSERT ON `proveedores` FOR EACH ROW SET NEW.FECHA_ALTA = NOW()
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores_productos`
--

DROP TABLE IF EXISTS `proveedores_productos`;
CREATE TABLE `proveedores_productos` (
  `PROVEEDOR_ID` int(11) UNSIGNED NOT NULL,
  `PRODUCTO_ID` int(11) UNSIGNED NOT NULL,
  `EMPLEADO_ID` int(11) UNSIGNED NOT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `FECHA_BAJA` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas`
--

DROP TABLE IF EXISTS `recetas`;
CREATE TABLE `recetas` (
  `RECETA_ID` int(11) UNSIGNED NOT NULL,
  `MERCADERIA_ID` int(11) UNSIGNED NOT NULL,
  `NOMBRE` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `FECHA_BAJA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Disparadores `recetas`
--
DROP TRIGGER IF EXISTS `TrRecetasFechaInsert`;
DELIMITER $$
CREATE TRIGGER `TrRecetasFechaInsert` BEFORE INSERT ON `recetas` FOR EACH ROW SET NEW.FECHA_ALTA = NOW()
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas_elaboradas`
--

DROP TABLE IF EXISTS `recetas_elaboradas`;
CREATE TABLE `recetas_elaboradas` (
  `RECETA_ID` int(11) UNSIGNED NOT NULL,
  `EMPLEADO_ID` int(11) UNSIGNED NOT NULL,
  `UNIMEDIDA_ID` int(11) UNSIGNED NOT NULL,
  `RE_CANTIDAD` decimal(8,3) NOT NULL,
  `RE_CANTIDAD_REAL` decimal(8,3) NOT NULL,
  `RE_FECHA` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Disparadores `recetas_elaboradas`
--
DROP TRIGGER IF EXISTS `TrRecetaelaboradaRediInsert`;
DELIMITER $$
CREATE TRIGGER `TrRecetaelaboradaRediInsert` AFTER INSERT ON `recetas_elaboradas` FOR EACH ROW BEGIN
INSERT INTO receta_elab_det_insu(RECETA_ID, INSUMO_ID, REDI_CANTIDAD, REDI_CANTIDAD_REAL, REDI_FECHA)
SELECT li.RECETA_ID, li.INSUMO_ID, (li.INSUMO_CANTIDAD*NEW.RE_CANTIDAD), (li.INSUMO_CANTIDAD_REAL*NEW.RE_CANTIDAD_REAL), NOW() 
FROM lista_insumos li 
WHERE li.RECETA_ID = NEW.RECETA_ID;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receta_elab_det_insu`
--

DROP TABLE IF EXISTS `receta_elab_det_insu`;
CREATE TABLE `receta_elab_det_insu` (
  `RECETA_ID` int(11) UNSIGNED NOT NULL,
  `INSUMO_ID` int(11) UNSIGNED NOT NULL,
  `REDI_CANTIDAD` decimal(8,3) NOT NULL,
  `REDI_CANTIDAD_REAL` decimal(8,3) NOT NULL,
  `REDI_FECHA` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rubros`
--

DROP TABLE IF EXISTS `rubros`;
CREATE TABLE `rubros` (
  `RUBRO_ID` int(11) UNSIGNED NOT NULL,
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `NOMBRE` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `IMAGEN_URL` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Disparadores `rubros`
--
DROP TRIGGER IF EXISTS `TrRubroFechaInsert`;
DELIMITER $$
CREATE TRIGGER `TrRubroFechaInsert` BEFORE INSERT ON `rubros` FOR EACH ROW SET NEW.FECHA_ALTA = NOW()
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

DROP TABLE IF EXISTS `sucursales`;
CREATE TABLE `sucursales` (
  `SUCURSAL_ID` int(11) UNSIGNED NOT NULL,
  `RAZONSOCIAL` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `CODIGO_SUCURSAL` char(5) COLLATE utf8_spanish_ci NOT NULL,
  `CUIT` char(12) COLLATE utf8_spanish_ci DEFAULT NULL,
  `MAIL` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `TELEFONO` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `WEB` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `DIRECCION` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `LOGO_URL` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `FECHA_BAJA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`SUCURSAL_ID`, `RAZONSOCIAL`, `CODIGO_SUCURSAL`, `CUIT`, `MAIL`, `TELEFONO`, `WEB`, `DIRECCION`, `LOGO_URL`, `FECHA_ALTA`, `FECHA_BAJA`) VALUES
(1, 'YOSUKO PIZZAS', '00001', '123456789012', 'yosukopizzas@yosuko.com', '543764123456', NULL, 'av siempre viva', NULL, '2021-05-04 08:00:00', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medida`
--

DROP TABLE IF EXISTS `unidades_medida`;
CREATE TABLE `unidades_medida` (
  `UNIMEDIDA_ID` int(11) UNSIGNED NOT NULL,
  `UNIDADMINIMA_ID` int(11) UNSIGNED DEFAULT NULL,
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `NOMBRE` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `ABREVIATURA` varchar(4) COLLATE utf8_spanish_ci NOT NULL,
  `VALOR` decimal(8,3) UNSIGNED NOT NULL DEFAULT 0.000
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Disparadores `unidades_medida`
--
DROP TRIGGER IF EXISTS `TrUnidadesMedidaUpdate`;
DELIMITER $$
CREATE TRIGGER `TrUnidadesMedidaUpdate` BEFORE UPDATE ON `unidades_medida` FOR EACH ROW BEGIN
IF NEW.UNIDADMINIMA_ID IS NULL THEN
	SET NEW.UNIDADMINIMA_ID = (SELECT UNIMEDIDA_ID FROM unidades_medida WHERE UNIMEDIDA_ID = NEW.UNIMEDIDA_ID);
    SET NEW.VALOR = 1;
END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `TrUnidadesMedidaValorInsert`;
DELIMITER $$
CREATE TRIGGER `TrUnidadesMedidaValorInsert` BEFORE INSERT ON `unidades_medida` FOR EACH ROW BEGIN
IF NEW.UNIDADMINIMA_ID IS null THEN
	    SET NEW.VALOR = 1;
END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `atributos`
--
ALTER TABLE `atributos`
  ADD PRIMARY KEY (`ATRIBUTO_ID`);

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`CARGO_ID`),
  ADD UNIQUE KEY `uq_CARGO_DESCRIPCION` (`CARGO_DESCRIPCION`) USING BTREE,
  ADD KEY `ix_NIVELACCESO_ID` (`NIVELACCESO_ID`) USING BTREE,
  ADD KEY `ix_ESTADO_ID` (`ESTADO_ID`) USING BTREE;

--
-- Indices de la tabla `cargos_auditoria`
--
ALTER TABLE `cargos_auditoria`
  ADD PRIMARY KEY (`CARGO_AUDITORIA_ID`),
  ADD KEY `NIVELACCESO_ID` (`NIVELACCESO_ID`),
  ADD KEY `ESTADO_ID` (`ESTADO_ID`),
  ADD KEY `CARGO_ID` (`CARGO_ID`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`CLIENTE_ID`),
  ADD UNIQUE KEY `uq_P_DNI` (`DNI`) USING BTREE,
  ADD UNIQUE KEY `uq_MAIL` (`MAIL`) USING BTREE,
  ADD KEY `ix_TELEFONO` (`TELEFONO`) USING BTREE,
  ADD KEY `ix_P_DNI` (`DNI`) USING BTREE,
  ADD KEY `ix_MAIL` (`MAIL`) USING BTREE,
  ADD KEY `ix_ESTADO_ID` (`ESTADO_ID`) USING BTREE;

--
-- Indices de la tabla `descuentos`
--
ALTER TABLE `descuentos`
  ADD PRIMARY KEY (`DESCUENTO_ID`),
  ADD KEY `FK_UNI_MEDIDA` (`UNIMEDIDA_ID`) USING BTREE,
  ADD KEY `PRODUCTO_ID` (`PRODCUTO_ID`) USING BTREE,
  ADD KEY `EMPLEADO_ID` (`EMPLEADO_ID`);

--
-- Indices de la tabla `detalle_pedidos_compra`
--
ALTER TABLE `detalle_pedidos_compra`
  ADD PRIMARY KEY (`DPCOMPRA_ID`),
  ADD KEY `detalle_pedido_ibfk_3` (`UNIMEDIDA_ID`),
  ADD KEY `FK_DETALLE_COMPRA` (`FACTURACOMPRA_ID`) USING BTREE,
  ADD KEY `MERCADERIA_ID` (`MERCADERIA_ID`) USING BTREE;

--
-- Indices de la tabla `detalle_pedidos_venta`
--
ALTER TABLE `detalle_pedidos_venta`
  ADD PRIMARY KEY (`DPVENTA_ID`),
  ADD KEY `FK_VENTA_DETALLE_OK` (`FACTURAVENTA_ID`),
  ADD KEY `PV_COD` (`MERCADERIA_ID`),
  ADD KEY `detalle_pedido_ibfk_3` (`UNIMEDIDA_ID`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`EMPLEADO_ID`),
  ADD KEY `DNI` (`DNI`),
  ADD KEY `MAIL` (`MAIL`),
  ADD KEY `TELEFONO` (`TELEFONO`),
  ADD KEY `CUIL` (`CUIL`),
  ADD KEY `NIVELACCESO_ID` (`CARGO_ID`),
  ADD KEY `SUCURSAL_ID` (`SUCURSAL_ID`),
  ADD KEY `empleados_ibfk_3` (`ESTADO_ID`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`ESTADO_ID`);

--
-- Indices de la tabla `estado_pedido`
--
ALTER TABLE `estado_pedido`
  ADD PRIMARY KEY (`ESTADO_ID`);

--
-- Indices de la tabla `facturacompra_formapago`
--
ALTER TABLE `facturacompra_formapago`
  ADD PRIMARY KEY (`FACTURACFP_ID`),
  ADD KEY `FORMAPAGO_ID` (`FORMAPAGO_ID`),
  ADD KEY `VENTA_ID` (`FACTURA_ID`);

--
-- Indices de la tabla `facturas_compra`
--
ALTER TABLE `facturas_compra`
  ADD PRIMARY KEY (`FACTURACOMPRA_ID`),
  ADD KEY `IX_TESTIGO_ID` (`TESTIGO_ID`) USING BTREE,
  ADD KEY `IX_SUCURSAL_ID` (`SUCURSAL_ID`) USING BTREE,
  ADD KEY `IX_EMPLEADO_ID` (`EMPLEADO_ID`) USING BTREE,
  ADD KEY `IX_PROVEEDOR_ID` (`PROVEEDOR_ID`) USING BTREE,
  ADD KEY `IX_NUMERO_FACTURA` (`NUMERO_FACTURA`) USING BTREE,
  ADD KEY `IX_FACTURATIPO` (`FACTURATIPO_ID`) USING BTREE,
  ADD KEY `IX_ESTADO_ID` (`ESTADO_ID`) USING BTREE;

--
-- Indices de la tabla `facturas_venta`
--
ALTER TABLE `facturas_venta`
  ADD PRIMARY KEY (`FACTURAVENTA_ID`),
  ADD KEY `IX_EMPLEADO_ID` (`EMPLEADO_ID`) USING BTREE,
  ADD KEY `IX_FACTURATIPO_ID` (`FACTURATIPO_ID`) USING BTREE,
  ADD KEY `IX_SUCURSAL_ID` (`SUCURSAL_ID`) USING BTREE,
  ADD KEY `IX_TESTIGO_ID` (`TESTIGO_ID`) USING BTREE,
  ADD KEY `IX_NUMERO_FACTURA` (`NUMERO_FACTURA`) USING BTREE,
  ADD KEY `IX_CLIENTE_ID` (`CLIENTE_ID`) USING BTREE,
  ADD KEY `IX_ESTADO_ID` (`ESTADO_ID`) USING BTREE;

--
-- Indices de la tabla `facturaventa_formapago`
--
ALTER TABLE `facturaventa_formapago`
  ADD PRIMARY KEY (`FACTURAVFP_ID`),
  ADD KEY `FORMAPAGO_ID` (`FORMAPAGO_ID`),
  ADD KEY `VENTA_ID` (`FACTURA_ID`);

--
-- Indices de la tabla `factura_tipo`
--
ALTER TABLE `factura_tipo`
  ADD PRIMARY KEY (`FACTURATIPO_ID`);

--
-- Indices de la tabla `forma_pago`
--
ALTER TABLE `forma_pago`
  ADD PRIMARY KEY (`FORMAPAGO_ID`);

--
-- Indices de la tabla `iva`
--
ALTER TABLE `iva`
  ADD PRIMARY KEY (`IVA_ID`);

--
-- Indices de la tabla `lista_insumos`
--
ALTER TABLE `lista_insumos`
  ADD PRIMARY KEY (`INSUMO_ID`),
  ADD KEY `FK_INSUMOS_STOCK_OK` (`MERCADERIA_ID`),
  ADD KEY `FK_UNIDADMEDIDA_INSUMOS_OK` (`UNIMEDIDA_ID`),
  ADD KEY `RECETA_ID` (`RECETA_ID`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`MARCA_ID`);

--
-- Indices de la tabla `mercaderias`
--
ALTER TABLE `mercaderias`
  ADD PRIMARY KEY (`MERCADERIA_ID`),
  ADD KEY `FK_INVENTARIO_IVA_OK` (`IVA_ID`),
  ADD KEY `mercaderias_ibfk_2` (`ESTADO_ID`);

--
-- Indices de la tabla `mercaderias_cantidad_actual`
--
ALTER TABLE `mercaderias_cantidad_actual`
  ADD PRIMARY KEY (`STOCKACTUAL_ID`),
  ADD KEY `MERCADERIA_ID` (`MERCADERIA_ID`),
  ADD KEY `SUCURSAL_ID` (`SUCURSAL_ID`);

--
-- Indices de la tabla `mercaderias_rubros`
--
ALTER TABLE `mercaderias_rubros`
  ADD PRIMARY KEY (`MERCADERIA_ID`,`RUBRO_ID`),
  ADD KEY `ART_STOCK_ID` (`MERCADERIA_ID`),
  ADD KEY `CATEPRODUCTO_ID` (`RUBRO_ID`);

--
-- Indices de la tabla `mercaderias_unidadesmedida`
--
ALTER TABLE `mercaderias_unidadesmedida`
  ADD PRIMARY KEY (`MERCAUNIMED_ID`),
  ADD UNIQUE KEY `UkArtStockUniMedida` (`MERCADERIA_ID`,`UNIMEDIDA_ID`) USING BTREE,
  ADD KEY `FK_UNIDADMEDIDA_STOCK_OK2` (`UNIMEDIDA_ID`),
  ADD KEY `FK_UNIDADMEDIDA_STOCK_OK` (`MERCADERIA_ID`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`MODULO_ID`),
  ADD KEY `modulos_ibfk_1` (`ESTADO_ID`);

--
-- Indices de la tabla `movimiento_caja`
--
ALTER TABLE `movimiento_caja`
  ADD PRIMARY KEY (`MC_ID`) USING BTREE,
  ADD KEY `PERSONA_ID` (`EMPLEADO_ID`),
  ADD KEY `TIPO` (`MOVIMIENTOTIPO_ID`),
  ADD KEY `FACTURA_ID` (`FACTURAV_ID`),
  ADD KEY `FACTURID_AC` (`FACTURAC_ID`);

--
-- Indices de la tabla `movimiento_tipo`
--
ALTER TABLE `movimiento_tipo`
  ADD PRIMARY KEY (`MOVIMIENTOTIPO_ID`),
  ADD KEY `TM_TIPO` (`TM_TIPO`);

--
-- Indices de la tabla `niveles_acceso`
--
ALTER TABLE `niveles_acceso`
  ADD PRIMARY KEY (`NIVELACCESO_ID`);

--
-- Indices de la tabla `notas_creditos`
--
ALTER TABLE `notas_creditos`
  ADD PRIMARY KEY (`CREDITO_ID`),
  ADD KEY `CLIENTE_ID` (`CLIENTE_ID`),
  ADD KEY `EMPLEADO_ID` (`EMPLEADO_ID`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`PERMISO_ID`),
  ADD KEY `CARGO_ID` (`CARGO_ID`),
  ADD KEY `MODULO_ID` (`MODULO_ID`);

--
-- Indices de la tabla `productosventa_atributos`
--
ALTER TABLE `productosventa_atributos`
  ADD PRIMARY KEY (`PROVATR_ID`);

--
-- Indices de la tabla `productos_compra`
--
ALTER TABLE `productos_compra`
  ADD PRIMARY KEY (`PC_ID`),
  ADD KEY `ART_STOCK_ID` (`MERCADERIA_ID`);

--
-- Indices de la tabla `productos_venta`
--
ALTER TABLE `productos_venta`
  ADD PRIMARY KEY (`PV_COD`),
  ADD UNIQUE KEY `ART_ID_UNIMEDIDA_ID` (`MERCADERIA_ID`,`UNIMEDIDA_ID`) USING BTREE,
  ADD KEY `FK_PRODUCTOVENTA_CATEGORIA_OK` (`RUBRO_ID`),
  ADD KEY `FK_UNIDADMEDIDA_PRODUCTO_OK` (`UNIMEDIDA_ID`),
  ADD KEY `ART_STOCK_ID` (`MERCADERIA_ID`) USING BTREE;

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`PROVEEDOR_ID`),
  ADD UNIQUE KEY `uq_MAIL` (`MAIL`),
  ADD UNIQUE KEY `uq_PP_CUIT` (`CUIT`) USING BTREE,
  ADD KEY `ix_TELEFONO` (`TELEFONO`),
  ADD KEY `ix_WEB` (`WEB`),
  ADD KEY `ix_PP_CUIT` (`CUIT`) USING BTREE,
  ADD KEY `ix_MAIL` (`MAIL`) USING BTREE,
  ADD KEY `ix_PP_RAZONSOCIAL` (`RAZONSOCIAL`) USING BTREE;

--
-- Indices de la tabla `proveedores_productos`
--
ALTER TABLE `proveedores_productos`
  ADD KEY `PROVEEDOR_ID` (`PROVEEDOR_ID`),
  ADD KEY `PRODUCTO_ID` (`PRODUCTO_ID`),
  ADD KEY `EMPLEADO_ID` (`EMPLEADO_ID`);

--
-- Indices de la tabla `recetas`
--
ALTER TABLE `recetas`
  ADD PRIMARY KEY (`RECETA_ID`),
  ADD UNIQUE KEY `UkART_STOCK_ID` (`MERCADERIA_ID`) USING BTREE,
  ADD KEY `IxART_STOCK_ID` (`MERCADERIA_ID`) USING BTREE;

--
-- Indices de la tabla `recetas_elaboradas`
--
ALTER TABLE `recetas_elaboradas`
  ADD PRIMARY KEY (`RECETA_ID`,`EMPLEADO_ID`,`UNIMEDIDA_ID`,`RE_FECHA`) USING BTREE,
  ADD KEY `FK_EMPLE_RECETAELAB_OK` (`EMPLEADO_ID`),
  ADD KEY `FK_RECETA_UNIDADM_OK` (`UNIMEDIDA_ID`);

--
-- Indices de la tabla `receta_elab_det_insu`
--
ALTER TABLE `receta_elab_det_insu`
  ADD KEY `INSUMO_ID` (`INSUMO_ID`),
  ADD KEY `RECETA_ID` (`RECETA_ID`);

--
-- Indices de la tabla `rubros`
--
ALTER TABLE `rubros`
  ADD PRIMARY KEY (`RUBRO_ID`),
  ADD KEY `rubros_ibfk_1` (`ESTADO_ID`);

--
-- Indices de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`SUCURSAL_ID`),
  ADD UNIQUE KEY `CODIGO_SUCURSAL_2` (`CODIGO_SUCURSAL`),
  ADD UNIQUE KEY `uq_MAIL` (`MAIL`),
  ADD UNIQUE KEY `uq_PP_CUIT` (`CUIT`) USING BTREE,
  ADD KEY `ix_TELEFONO` (`TELEFONO`),
  ADD KEY `ix_WEB` (`WEB`),
  ADD KEY `ix_PP_CUIT` (`CUIT`) USING BTREE,
  ADD KEY `ix_MAIL` (`MAIL`) USING BTREE,
  ADD KEY `ix_PP_RAZONSOCIAL` (`RAZONSOCIAL`) USING BTREE,
  ADD KEY `CODIGO_SUCURSAL` (`CODIGO_SUCURSAL`);

--
-- Indices de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  ADD PRIMARY KEY (`UNIMEDIDA_ID`),
  ADD KEY `UNIDADMINIMA_ID` (`UNIDADMINIMA_ID`),
  ADD KEY `ESTADO_ID` (`ESTADO_ID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `atributos`
--
ALTER TABLE `atributos`
  MODIFY `ATRIBUTO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
  MODIFY `CARGO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `cargos_auditoria`
--
ALTER TABLE `cargos_auditoria`
  MODIFY `CARGO_AUDITORIA_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `CLIENTE_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000000057;

--
-- AUTO_INCREMENT de la tabla `descuentos`
--
ALTER TABLE `descuentos`
  MODIFY `DESCUENTO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_pedidos_compra`
--
ALTER TABLE `detalle_pedidos_compra`
  MODIFY `DPCOMPRA_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT de la tabla `detalle_pedidos_venta`
--
ALTER TABLE `detalle_pedidos_venta`
  MODIFY `DPVENTA_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `EMPLEADO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `ESTADO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `estado_pedido`
--
ALTER TABLE `estado_pedido`
  MODIFY `ESTADO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `facturacompra_formapago`
--
ALTER TABLE `facturacompra_formapago`
  MODIFY `FACTURACFP_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `facturas_compra`
--
ALTER TABLE `facturas_compra`
  MODIFY `FACTURACOMPRA_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de la tabla `facturas_venta`
--
ALTER TABLE `facturas_venta`
  MODIFY `FACTURAVENTA_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de la tabla `facturaventa_formapago`
--
ALTER TABLE `facturaventa_formapago`
  MODIFY `FACTURAVFP_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `factura_tipo`
--
ALTER TABLE `factura_tipo`
  MODIFY `FACTURATIPO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `forma_pago`
--
ALTER TABLE `forma_pago`
  MODIFY `FORMAPAGO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `iva`
--
ALTER TABLE `iva`
  MODIFY `IVA_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `lista_insumos`
--
ALTER TABLE `lista_insumos`
  MODIFY `INSUMO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `MARCA_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mercaderias`
--
ALTER TABLE `mercaderias`
  MODIFY `MERCADERIA_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `mercaderias_cantidad_actual`
--
ALTER TABLE `mercaderias_cantidad_actual`
  MODIFY `STOCKACTUAL_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `mercaderias_unidadesmedida`
--
ALTER TABLE `mercaderias_unidadesmedida`
  MODIFY `MERCAUNIMED_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `MODULO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `movimiento_caja`
--
ALTER TABLE `movimiento_caja`
  MODIFY `MC_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de la tabla `movimiento_tipo`
--
ALTER TABLE `movimiento_tipo`
  MODIFY `MOVIMIENTOTIPO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `niveles_acceso`
--
ALTER TABLE `niveles_acceso`
  MODIFY `NIVELACCESO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `notas_creditos`
--
ALTER TABLE `notas_creditos`
  MODIFY `CREDITO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `PERMISO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de la tabla `productosventa_atributos`
--
ALTER TABLE `productosventa_atributos`
  MODIFY `PROVATR_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos_compra`
--
ALTER TABLE `productos_compra`
  MODIFY `PC_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `productos_venta`
--
ALTER TABLE `productos_venta`
  MODIFY `PV_COD` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `PROVEEDOR_ID` int(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `recetas`
--
ALTER TABLE `recetas`
  MODIFY `RECETA_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `rubros`
--
ALTER TABLE `rubros`
  MODIFY `RUBRO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `SUCURSAL_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  MODIFY `UNIMEDIDA_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD CONSTRAINT `cargos_ibfk_1` FOREIGN KEY (`NIVELACCESO_ID`) REFERENCES `niveles_acceso` (`NIVELACCESO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cargos_ibfk_2` FOREIGN KEY (`ESTADO_ID`) REFERENCES `estado` (`ESTADO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `cargos_auditoria`
--
ALTER TABLE `cargos_auditoria`
  ADD CONSTRAINT `cargos_auditoria_ibfk_1` FOREIGN KEY (`ESTADO_ID`) REFERENCES `estado` (`ESTADO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`ESTADO_ID`) REFERENCES `estado` (`ESTADO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `descuentos`
--
ALTER TABLE `descuentos`
  ADD CONSTRAINT `descuentos_ibfk_1` FOREIGN KEY (`UNIMEDIDA_ID`) REFERENCES `unidades_medida` (`UNIMEDIDA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `descuentos_ibfk_2` FOREIGN KEY (`PRODCUTO_ID`) REFERENCES `productos_venta` (`PV_COD`) ON UPDATE CASCADE,
  ADD CONSTRAINT `descuentos_ibfk_3` FOREIGN KEY (`EMPLEADO_ID`) REFERENCES `empleados` (`EMPLEADO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_pedidos_compra`
--
ALTER TABLE `detalle_pedidos_compra`
  ADD CONSTRAINT `detalle_pedidos_compra_ibfk_1` FOREIGN KEY (`FACTURACOMPRA_ID`) REFERENCES `facturas_compra` (`FACTURACOMPRA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pedidos_compra_ibfk_2` FOREIGN KEY (`UNIMEDIDA_ID`) REFERENCES `unidades_medida` (`UNIMEDIDA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pedidos_compra_ibfk_3` FOREIGN KEY (`MERCADERIA_ID`) REFERENCES `mercaderias` (`MERCADERIA_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_pedidos_venta`
--
ALTER TABLE `detalle_pedidos_venta`
  ADD CONSTRAINT `detalle_pedidos_venta_ibfk_1` FOREIGN KEY (`FACTURAVENTA_ID`) REFERENCES `facturas_venta` (`FACTURAVENTA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pedidos_venta_ibfk_2` FOREIGN KEY (`UNIMEDIDA_ID`) REFERENCES `unidades_medida` (`UNIMEDIDA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pedidos_venta_ibfk_3` FOREIGN KEY (`MERCADERIA_ID`) REFERENCES `mercaderias` (`MERCADERIA_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`CARGO_ID`) REFERENCES `cargos` (`CARGO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `empleados_ibfk_2` FOREIGN KEY (`SUCURSAL_ID`) REFERENCES `sucursales` (`SUCURSAL_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `empleados_ibfk_3` FOREIGN KEY (`ESTADO_ID`) REFERENCES `estado` (`ESTADO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `facturacompra_formapago`
--
ALTER TABLE `facturacompra_formapago`
  ADD CONSTRAINT `facturacompra_formapago_ibfk_1` FOREIGN KEY (`FORMAPAGO_ID`) REFERENCES `forma_pago` (`FORMAPAGO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturacompra_formapago_ibfk_2` FOREIGN KEY (`FACTURA_ID`) REFERENCES `facturas_compra` (`FACTURACOMPRA_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `facturas_compra`
--
ALTER TABLE `facturas_compra`
  ADD CONSTRAINT `facturas_compra_ibfk_1` FOREIGN KEY (`ESTADO_ID`) REFERENCES `estado_pedido` (`ESTADO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturas_compra_ibfk_2` FOREIGN KEY (`FACTURATIPO_ID`) REFERENCES `factura_tipo` (`FACTURATIPO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturas_compra_ibfk_3` FOREIGN KEY (`FACTURACOMPRA_ID`) REFERENCES `facturacompra_formapago` (`FACTURA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturas_compra_ibfk_4` FOREIGN KEY (`TESTIGO_ID`) REFERENCES `empleados` (`EMPLEADO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturas_compra_ibfk_5` FOREIGN KEY (`SUCURSAL_ID`) REFERENCES `sucursales` (`SUCURSAL_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturas_compra_ibfk_6` FOREIGN KEY (`PROVEEDOR_ID`) REFERENCES `proveedores` (`PROVEEDOR_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturas_compra_ibfk_7` FOREIGN KEY (`EMPLEADO_ID`) REFERENCES `empleados` (`EMPLEADO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `facturas_venta`
--
ALTER TABLE `facturas_venta`
  ADD CONSTRAINT `facturas_venta_ibfk_1` FOREIGN KEY (`FACTURATIPO_ID`) REFERENCES `factura_tipo` (`FACTURATIPO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturas_venta_ibfk_2` FOREIGN KEY (`SUCURSAL_ID`) REFERENCES `sucursales` (`SUCURSAL_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturas_venta_ibfk_3` FOREIGN KEY (`CLIENTE_ID`) REFERENCES `clientes` (`CLIENTE_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturas_venta_ibfk_4` FOREIGN KEY (`EMPLEADO_ID`) REFERENCES `empleados` (`EMPLEADO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturas_venta_ibfk_5` FOREIGN KEY (`TESTIGO_ID`) REFERENCES `empleados` (`EMPLEADO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturas_venta_ibfk_6` FOREIGN KEY (`ESTADO_ID`) REFERENCES `estado_pedido` (`ESTADO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `facturaventa_formapago`
--
ALTER TABLE `facturaventa_formapago`
  ADD CONSTRAINT `facturaventa_formapago_ibfk_1` FOREIGN KEY (`FORMAPAGO_ID`) REFERENCES `forma_pago` (`FORMAPAGO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturaventa_formapago_ibfk_2` FOREIGN KEY (`FACTURA_ID`) REFERENCES `facturas_venta` (`FACTURAVENTA_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `lista_insumos`
--
ALTER TABLE `lista_insumos`
  ADD CONSTRAINT `lista_insumos_ibfk_1` FOREIGN KEY (`UNIMEDIDA_ID`) REFERENCES `unidades_medida` (`UNIMEDIDA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `lista_insumos_ibfk_2` FOREIGN KEY (`RECETA_ID`) REFERENCES `recetas` (`RECETA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `lista_insumos_ibfk_3` FOREIGN KEY (`MERCADERIA_ID`) REFERENCES `mercaderias` (`MERCADERIA_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `mercaderias`
--
ALTER TABLE `mercaderias`
  ADD CONSTRAINT `mercaderias_ibfk_1` FOREIGN KEY (`IVA_ID`) REFERENCES `iva` (`IVA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mercaderias_ibfk_2` FOREIGN KEY (`ESTADO_ID`) REFERENCES `estado` (`ESTADO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `mercaderias_cantidad_actual`
--
ALTER TABLE `mercaderias_cantidad_actual`
  ADD CONSTRAINT `mercaderias_cantidad_actual_ibfk_1` FOREIGN KEY (`MERCADERIA_ID`) REFERENCES `mercaderias` (`MERCADERIA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mercaderias_cantidad_actual_ibfk_2` FOREIGN KEY (`SUCURSAL_ID`) REFERENCES `sucursales` (`SUCURSAL_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `mercaderias_rubros`
--
ALTER TABLE `mercaderias_rubros`
  ADD CONSTRAINT `mercaderias_rubros_ibfk_1` FOREIGN KEY (`MERCADERIA_ID`) REFERENCES `mercaderias` (`MERCADERIA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mercaderias_rubros_ibfk_2` FOREIGN KEY (`RUBRO_ID`) REFERENCES `rubros` (`RUBRO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `mercaderias_unidadesmedida`
--
ALTER TABLE `mercaderias_unidadesmedida`
  ADD CONSTRAINT `mercaderias_unidadesmedida_ibfk_1` FOREIGN KEY (`UNIMEDIDA_ID`) REFERENCES `unidades_medida` (`UNIMEDIDA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mercaderias_unidadesmedida_ibfk_2` FOREIGN KEY (`MERCADERIA_ID`) REFERENCES `mercaderias` (`MERCADERIA_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD CONSTRAINT `modulos_ibfk_1` FOREIGN KEY (`ESTADO_ID`) REFERENCES `estado` (`ESTADO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimiento_caja`
--
ALTER TABLE `movimiento_caja`
  ADD CONSTRAINT `movimiento_caja_ibfk_1` FOREIGN KEY (`EMPLEADO_ID`) REFERENCES `empleados` (`EMPLEADO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `movimiento_caja_ibfk_3` FOREIGN KEY (`MOVIMIENTOTIPO_ID`) REFERENCES `movimiento_tipo` (`MOVIMIENTOTIPO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `movimiento_caja_ibfk_4` FOREIGN KEY (`FACTURAV_ID`) REFERENCES `facturas_venta` (`FACTURAVENTA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `movimiento_caja_ibfk_5` FOREIGN KEY (`FACTURAC_ID`) REFERENCES `facturas_compra` (`FACTURACOMPRA_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `notas_creditos`
--
ALTER TABLE `notas_creditos`
  ADD CONSTRAINT `notas_creditos_ibfk_1` FOREIGN KEY (`EMPLEADO_ID`) REFERENCES `empleados` (`EMPLEADO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `notas_creditos_ibfk_2` FOREIGN KEY (`CLIENTE_ID`) REFERENCES `clientes` (`CLIENTE_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`CARGO_ID`) REFERENCES `cargos` (`CARGO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `permisos_ibfk_2` FOREIGN KEY (`MODULO_ID`) REFERENCES `modulos` (`MODULO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_compra`
--
ALTER TABLE `productos_compra`
  ADD CONSTRAINT `productos_compra_ibfk_1` FOREIGN KEY (`MERCADERIA_ID`) REFERENCES `mercaderias` (`MERCADERIA_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_venta`
--
ALTER TABLE `productos_venta`
  ADD CONSTRAINT `productos_venta_ibfk_1` FOREIGN KEY (`MERCADERIA_ID`) REFERENCES `mercaderias` (`MERCADERIA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `productos_venta_ibfk_2` FOREIGN KEY (`RUBRO_ID`) REFERENCES `rubros` (`RUBRO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `productos_venta_ibfk_3` FOREIGN KEY (`UNIMEDIDA_ID`) REFERENCES `unidades_medida` (`UNIMEDIDA_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `proveedores_productos`
--
ALTER TABLE `proveedores_productos`
  ADD CONSTRAINT `proveedores_productos_ibfk_1` FOREIGN KEY (`PROVEEDOR_ID`) REFERENCES `proveedores` (`PROVEEDOR_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `proveedores_productos_ibfk_2` FOREIGN KEY (`PRODUCTO_ID`) REFERENCES `mercaderias` (`MERCADERIA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `proveedores_productos_ibfk_3` FOREIGN KEY (`EMPLEADO_ID`) REFERENCES `empleados` (`EMPLEADO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `recetas`
--
ALTER TABLE `recetas`
  ADD CONSTRAINT `recetas_ibfk_1` FOREIGN KEY (`MERCADERIA_ID`) REFERENCES `mercaderias` (`MERCADERIA_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `recetas_elaboradas`
--
ALTER TABLE `recetas_elaboradas`
  ADD CONSTRAINT `recetas_elaboradas_ibfk_1` FOREIGN KEY (`UNIMEDIDA_ID`) REFERENCES `unidades_medida` (`UNIMEDIDA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `recetas_elaboradas_ibfk_2` FOREIGN KEY (`EMPLEADO_ID`) REFERENCES `empleados` (`EMPLEADO_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `recetas_elaboradas_ibfk_3` FOREIGN KEY (`RECETA_ID`) REFERENCES `recetas` (`RECETA_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `receta_elab_det_insu`
--
ALTER TABLE `receta_elab_det_insu`
  ADD CONSTRAINT `receta_elab_det_insu_ibfk_1` FOREIGN KEY (`RECETA_ID`) REFERENCES `recetas_elaboradas` (`RECETA_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `receta_elab_det_insu_ibfk_2` FOREIGN KEY (`INSUMO_ID`) REFERENCES `lista_insumos` (`INSUMO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `rubros`
--
ALTER TABLE `rubros`
  ADD CONSTRAINT `rubros_ibfk_1` FOREIGN KEY (`ESTADO_ID`) REFERENCES `estado` (`ESTADO_ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  ADD CONSTRAINT `unidades_medida_ibfk_1` FOREIGN KEY (`UNIDADMINIMA_ID`) REFERENCES `unidades_medida` (`UNIMEDIDA_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `unidades_medida_ibfk_2` FOREIGN KEY (`ESTADO_ID`) REFERENCES `estado` (`ESTADO_ID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
