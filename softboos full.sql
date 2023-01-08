-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-08-2021 a las 02:12:02
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
(1, 1, 20, 'MASTER', '2021-05-22 13:18:54', NULL),
(2, 1, 19, 'GERENTE', '2021-05-22 13:19:23', NULL),
(3, 1, 6, 'COCINERO', '2021-05-27 12:04:17', NULL),
(7, 1, 10, 'CAJERO', '2021-05-27 12:48:40', NULL),
(8, 3, 15, 'SECRETARIO', '2021-05-28 21:56:27', NULL);

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
    INSERT INTO permisos(CARGO_ID, MODULO_ID, LEER, AGREGAR, MODIFICAR, BORRAR) VALUES(NEW.CARGO_ID, idModulo, 0, 0, 0, 0);
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
(1, 3, 6, 'COCINERO', '2021-05-27 12:04:17', 1, 4, 'root@localhost'),
(2, 7, 10, 'CAJERO', '2021-05-27 12:48:40', 1, 4, 'root@localhost'),
(3, 1, 20, 'MASTER', '2021-05-28 21:54:19', 1, 5, 'root@localhost'),
(4, 8, 15, 'SECRETARIO', '2021-05-28 21:56:27', 1, 4, 'root@localhost'),
(5, 8, 15, 'SECRETARIO', '2021-05-28 21:56:47', 3, 3, 'root@localhost'),
(6, 3, 6, 'COCINERO', '2021-05-29 16:43:05', 1, 5, 'root@localhost'),
(7, 7, 10, 'CAJERO', '2021-05-29 16:43:10', 1, 5, 'root@localhost');

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
  `FECHA_BAJA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`CLIENTE_ID`, `DNI`, `NOMBRE`, `APELLIDO`, `FECHA_NACIMIENTO`, `CUIL`, `TELEFONO`, `MAIL`, `PASS`, `DIRECCION`, `FECHA_ALTA`, `FECHA_BAJA`) VALUES
(1, '36636440', 'LEANDRO', 'BOOS', '1991-12-19', NULL, '3765119764', 'leo@correo.com', NULL, 'POSADAS VILLA PELO CALLE FALSA 123 3', '2018-11-11 00:00:00', NULL),
(2, '11458795', 'JOSE', 'PADOVAN', '1993-09-12', NULL, '12345678', 'asdasdkf@gmail.com.ar', NULL, 'DDDDDDDDBBBBBBBB FFFFFFFFFFFFFFFF 9874 0', '2018-11-12 00:00:00', NULL),
(3, '12345679', 'ASD', 'CTM KKKK', '1993-09-12', NULL, '12345679', 'adadkf@gmail.com', NULL, 'BBBBBBBBEEEEEEEEEEE FFFFFFFFFFFFFFFF 9874 0', '2018-11-12 00:00:00', NULL),
(4, '45987321', 'MANCO', 'CROTO Y', '1990-12-12', NULL, '2224441', 'manco@correo.com', NULL, 'FSAFSDFSA FGRWEG 7000 0', '2018-11-12 00:00:00', NULL),
(5, '45987322', 'TERMO', 'CABEZA DE ', '1990-12-12', NULL, '2224443', 'Ctermo@cliente.com', NULL, 'AFSDFSA FGRWEG 7000 0', '2018-11-12 00:00:00', NULL),
(6, '45987326', 'GILBERTO', 'GIMENEZ', '1990-12-12', NULL, '5224443', 'gilberto@gmail.com', NULL, 'CALLE 321 DONDE LA WEA ESTÃ¡ OPÃ©', '2018-11-12 00:00:00', NULL),
(7, '11458711', 'JORGE', 'SOSA', '1985-08-15', NULL, '654687987', 'falopa213@gmail.com', NULL, 'FALSA CITY FALSO BARRIO FALSA CALLE 108 3', '2018-11-12 00:00:00', NULL),
(8, '34567891', 'YOSUKO', 'DELIVERY', '1980-05-09', NULL, '236467383', 'yosukopizzas@empresa.com', NULL, 'POSADAS VILLA CABELLO CALLE 325-8 1234 0', '2018-11-25 00:00:00', NULL),
(1000000002, '97321458', 'GALARGA', 'ELVER', '1990-12-12', NULL, NULL, NULL, NULL, NULL, '2019-02-01 00:00:00', NULL),
(1000000004, '17231929', 'SILVIA', 'APONTE', '1964-04-06', NULL, '3764396896', 'lili@correo.com', NULL, 'VILLA PELOS', '2019-08-25 00:00:00', NULL),
(1000000005, '30258852', 'JUANITO', 'Y LOS CRONOSAURIOS', '1990-06-15', NULL, NULL, NULL, NULL, 'JUNTO AL VECINO DE LA IZQUIERDA DE LA MANO DERECHA DE LA CALLE QUE PASA EN FRENTE DE CASA', '2019-09-04 00:00:00', NULL),
(1000000006, '45669887', 'JUAN', 'SOTELO', '1985-12-15', NULL, NULL, NULL, NULL, 'NUVE 45 DE PEDOS', '2019-09-07 00:00:00', NULL),
(1000000007, '78999888', 'JACINTO', 'LA WEA', '1990-05-06', NULL, NULL, 'shacinto@gmail.com', NULL, NULL, '2019-09-07 00:00:00', NULL),
(1000000008, '12332198', 'GOKU', 'SON', '1985-05-04', NULL, NULL, 'kakaroto@ssj.com', NULL, NULL, '2019-09-08 00:00:00', NULL),
(1000000009, '36524113', 'PANFILO', 'ESTORNUDO', '1994-05-16', NULL, '3765852236', 'panfilo@empleado.com', NULL, 'PANFILOLANDIA CERCA DEL CALIFORNIA', '2019-09-09 00:00:00', NULL),
(1000000010, '35147789', 'PEPE', 'EL ADMIN', '1980-09-04', NULL, NULL, 'admin@pepito.com.ar', NULL, NULL, '2019-09-09 00:00:00', NULL),
(1000000011, '35889916', 'ALAN', 'SOZA', '1990-12-12', NULL, NULL, 'alan@cliente.com', NULL, 'AL LADO DEL VECINO', '2019-09-21 00:00:00', NULL),
(1000000012, '39722411', 'AGUI', 'MONTAÑEZ', '1996-05-24', NULL, NULL, 'agui@correo.com', NULL, 'EN EL CORAZÓN DE LEO', '2019-10-07 00:00:00', NULL),
(1000000013, '33333333', 'MAURO', 'MONTAÑEZ', '1992-07-27', NULL, '3764668830', 'mauro@cliente.com', NULL, 'VECINO DE LEO', '2019-10-07 00:00:00', NULL),
(1000000014, '27456889', 'SEBASTIAN', 'WEA', '1984-05-02', NULL, NULL, NULL, NULL, NULL, '2019-10-10 00:00:00', NULL),
(1000000015, '11111111', 'KOKI', 'PROFE', '1990-01-01', NULL, NULL, 'koki@admin.com', NULL, NULL, '2019-11-10 00:00:00', NULL),
(1000000016, '32654987', 'PEPE', 'ARGENTO', '1985-01-15', NULL, NULL, NULL, NULL, NULL, '2019-11-18 00:00:00', NULL),
(1000000017, '65123478', 'JUAN', 'APONTE', '1965-08-14', NULL, NULL, NULL, NULL, NULL, '2019-11-18 00:00:00', NULL),
(1000000018, '12369582', 'PEDRO', 'CORBALAN', '1985-08-08', NULL, NULL, NULL, NULL, NULL, '2019-11-18 00:00:00', NULL),
(1000000019, '14564998', 'OSCAR', 'BENITEZ', '1984-09-06', NULL, NULL, NULL, NULL, NULL, '2019-11-18 00:00:00', NULL),
(1000000020, '12354788', 'FULGENCIO', 'CANO', '1980-05-05', NULL, NULL, NULL, NULL, NULL, '2019-11-18 00:00:00', NULL),
(1000000021, '47555632', 'PEDRO', 'LOPEZ', '1980-05-02', NULL, NULL, NULL, NULL, NULL, '2019-11-21 00:00:00', NULL),
(1000000022, '35122456', 'MARCELO', 'FERNANDEZ', '1987-05-12', NULL, NULL, NULL, NULL, NULL, '2020-03-11 00:00:00', NULL),
(1000000023, '12365487', 'RAMBO', 'MAMBO', '1990-12-12', NULL, NULL, NULL, NULL, NULL, '2020-03-11 00:00:00', NULL),
(1000000024, '45998632', 'COLOR', 'ABC', '2000-08-15', NULL, NULL, 'pepelawea@asd.algo', NULL, NULL, '2020-03-12 00:00:00', NULL),
(1000000025, '34568972', 'FELIPE', 'MELO', '1986-05-14', NULL, NULL, NULL, NULL, NULL, '2020-03-12 00:00:00', NULL),
(1000000029, '12558974', 'PELUCA', 'PELADO', '1984-08-19', NULL, NULL, NULL, NULL, '', '2020-03-12 00:00:00', NULL),
(1000000030, '23669857', 'VALENTINA', 'ALEJO Y', '1985-05-02', NULL, '3764589871', 'ale.y.vale@locoarts.com.ar', NULL, 'QUIEN TE CONOCE HDP', '2020-03-12 00:00:00', NULL),
(1000000031, '43255658', 'NORMA', 'PAREDES', '1982-08-24', NULL, '3764895269', 'n0rma@gmail.com', NULL, 'DONDE CAGO EL CONEJO', '2020-03-19 00:00:00', NULL),
(1000000032, '51369582', 'MAURICIO', 'GOLLENA', '1989-06-15', NULL, '23513695826', 'Gonorrea@gmail.com', NULL, 'BARRIO A4 AMEWO', '2020-03-19 00:00:00', NULL),
(1000000034, '36987546', 'LARA EDITADA', 'CROFT', '2000-08-14', NULL, NULL, 'laraC@persona.es', NULL, 'GG JUEGOS', '2020-03-27 00:00:00', NULL),
(1000000035, '35465889', 'MARIO', 'BROS', '1985-05-18', NULL, NULL, NULL, NULL, NULL, '2020-04-19 00:00:00', NULL),
(1000000036, '32159753', 'LUIS', 'PASTEKNIK', '1991-07-04', NULL, NULL, NULL, NULL, NULL, '2020-04-19 00:00:00', NULL),
(1000000037, '36636441', 'PEPE', 'SILVA', '2020-10-30', NULL, '3764123555', 'pepe@nosilva.com', NULL, 'AV MITRE 1234', '2020-10-31 00:00:00', NULL),
(1000000051, '36636445', 'CIRILO', 'APONTE', '2020-10-31', NULL, NULL, NULL, NULL, NULL, '2020-10-31 00:00:00', NULL),
(1000000052, '14789665', 'PABLO', 'ESCOBAR', '1981-09-05', NULL, NULL, NULL, NULL, NULL, '2020-12-04 00:00:00', NULL),
(1000000054, '34000987', 'PREVENTISTA', 'PRUEBA UNO', '1976-04-15', NULL, NULL, NULL, NULL, NULL, '2021-02-15 00:00:00', NULL),
(1000000055, '12345678', 'elfa', 'lopa', '1991-05-14', NULL, NULL, 'elfalopa@equis.de', NULL, NULL, '2021-05-28 23:14:51', NULL);

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

--
-- Volcado de datos para la tabla `descuentos`
--

INSERT INTO `descuentos` (`DESCUENTO_ID`, `EMPLEADO_ID`, `PRODCUTO_ID`, `UNIMEDIDA_ID`, `DESCUENTO`, `CANTIDAD`) VALUES
(1, 1, 1, 6, '10.00', '6.00');

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

--
-- Volcado de datos para la tabla `detalle_pedidos_compra`
--

INSERT INTO `detalle_pedidos_compra` (`DPCOMPRA_ID`, `FACTURACOMPRA_ID`, `MERCADERIA_ID`, `UNIMEDIDA_ID`, `CANTIDAD`, `CANTIDAD_REAL`, `PRECIO`) VALUES
(1, 1, 1, 6, '1.000', '1.000', '50.00'),
(2, 1, 2, 3, '1.000', '1.000', '30.00'),
(3, 1, 3, 2, '3.000', '3.000', '35.00'),
(4, 2, 1, 6, '2.000', '2.000', '50.00'),
(5, 2, 3, 2, '5.000', '5.000', '35.00'),
(6, 3, 1, 6, '3.000', '3.000', '50.00'),
(7, 4, 2, 3, '1.000', '1.000', '30.00'),
(8, 4, 3, 2, '4.000', '4.000', '35.00'),
(9, 4, 1, 6, '2.000', '2.000', '50.00'),
(10, 5, 3, 2, '1.000', '1.000', '35.00'),
(11, 6, 2, 3, '5.000', '5.000', '30.00'),
(12, 7, 1, 6, '1.000', '1.000', '50.00'),
(13, 8, 3, 2, '2.000', '2.000', '35.00'),
(14, 10, 1, 6, '5.000', '5.000', '50.00'),
(15, 10, 3, 2, '3.000', '3.000', '35.00'),
(16, 10, 2, 3, '1.000', '1.000', '30.00'),
(17, 11, 1, 6, '5.000', '5.000', '50.00'),
(18, 12, 3, 2, '2.000', '2.000', '35.00'),
(19, 12, 1, 6, '5.000', '5.000', '50.00'),
(20, 13, 2, 3, '1.000', '1.000', '30.00'),
(21, 14, 1, 6, '1.000', '1.000', '50.00'),
(22, 15, 3, 2, '1.000', '1.000', '35.00'),
(23, 16, 2, 3, '1.000', '1.000', '30.00'),
(24, 17, 5, 6, '10.000', '10.000', '70.00'),
(27, 20, 5, 6, '10.000', '10.000', '70.00'),
(28, 20, 1, 6, '1.000', '1.000', '50.00'),
(29, 21, 5, 6, '10.000', '10.000', '70.00'),
(30, 22, 5, 6, '1.000', '1.000', '70.00'),
(31, 23, 1, 6, '1.000', '1.000', '50.00'),
(32, 23, 5, 6, '5.000', '5.000', '100.00'),
(37, 25, 1, 6, '1.000', '1.000', '65.50'),
(38, 25, 5, 6, '1.000', '1.000', '100.00'),
(39, 26, 6, 6, '2.000', '2.000', '150.00'),
(40, 26, 1, 6, '1.000', '1.000', '65.50'),
(41, 27, 7, 6, '1.000', '1.000', '550.99'),
(42, 28, 1, 6, '1.000', '1.000', '65.50'),
(43, 29, 1, 6, '1.000', '1.000', '65.50'),
(44, 30, 7, 6, '1.000', '1.000', '550.99'),
(45, 31, 5, 6, '1.000', '1.000', '100.00'),
(46, 32, 5, 6, '1.000', '1.000', '100.00'),
(47, 33, 9, 6, '2.000', '2.000', '100.00'),
(48, 34, 1, 6, '1.000', '1.000', '65.50'),
(49, 34, 7, 6, '1.000', '1.000', '550.99'),
(50, 35, 10, 6, '1.000', '1.000', '95.00'),
(51, 36, 1, 6, '1.000', '1.000', '70.00'),
(52, 36, 6, 6, '1.000', '1.000', '150.00'),
(53, 37, 6, 6, '1.000', '1.000', '150.00'),
(54, 38, 1, 6, '1.000', '1.000', '70.00'),
(55, 38, 6, 6, '1.000', '1.000', '150.00'),
(56, 38, 7, 5, '1.000', '12.000', '550.99'),
(57, 39, 7, 5, '1.000', '12.000', '550.99'),
(58, 40, 1, 4, '5.000', '60.000', '315.00'),
(59, 40, 9, 6, '6.000', '6.000', '40.00'),
(60, 40, 8, 6, '6.000', '6.000', '50.00'),
(61, 41, 1, 6, '1.000', '1.000', '70.00'),
(62, 41, 5, 6, '1.000', '1.000', '100.00'),
(63, 42, 6, 6, '1.000', '1.000', '150.00'),
(64, 42, 9, 6, '1.000', '1.000', '100.00'),
(65, 43, 9, 6, '1.000', '1.000', '100.00'),
(66, 44, 8, 6, '1.000', '1.000', '65.00'),
(67, 45, 9, 6, '1.000', '1.000', '100.00'),
(68, 46, 6, 6, '1.000', '1.000', '150.00'),
(69, 47, 1, 6, '1.000', '1.000', '80.00'),
(70, 48, 1, 6, '1.000', '1.000', '70.00'),
(71, 49, 6, 6, '1.000', '1.000', '150.00'),
(72, 50, 7, 5, '1.000', '12.000', '550.99'),
(73, 51, 1, 6, '8.000', '8.000', '50.00'),
(74, 51, 5, 6, '12.000', '12.000', '60.00'),
(75, 51, 8, 6, '6.000', '6.000', '50.00'),
(76, 51, 9, 6, '6.000', '6.000', '40.00'),
(77, 51, 10, 6, '6.000', '6.000', '85.00'),
(78, 52, 1, 6, '8.000', '8.000', '50.00'),
(79, 53, 29, 6, '6.000', '6.000', '60.00'),
(80, 54, 1, 4, '1.000', '12.000', '800.00'),
(81, 55, 1, 4, '3.000', '36.000', '800.00'),
(82, 56, 1, 4, '3.000', '36.000', '800.00'),
(83, 57, 1, 6, '1.000', '1.000', '70.00'),
(84, 57, 7, 5, '1.000', '0.000', '550.99'),
(85, 57, 6, 6, '1.000', '0.000', '150.00'),
(86, 57, 9, 6, '1.000', '1.000', '100.00'),
(87, 58, 26, 3, '1.000', '1.000', '70.00'),
(88, 58, 22, 6, '1.000', '1.000', '40.00'),
(89, 58, 24, 6, '1.000', '1.000', '95.00'),
(90, 59, 3, 3, '1.000', '1.000', '25.00'),
(91, 59, 2, 3, '1.000', '1.000', '10.00'),
(92, 59, 6, 6, '1.000', '1.000', '49.50'),
(93, 59, 6, 6, '1.000', '1.000', '49.50'),
(94, 59, 4, 3, '1.000', '1.000', '28.00'),
(95, 59, 33, 1, '1.000', '0.000', '100.00'),
(96, 59, 28, 7, '1.000', '1.000', '70.00'),
(97, 59, 16, 6, '1.000', '1.000', '100.00'),
(98, 59, 19, 6, '1.000', '1.000', '50.00'),
(99, 59, 18, 6, '1.000', '1.000', '25.00'),
(100, 59, 21, 6, '1.000', '1.000', '40.00'),
(101, 60, 6, 6, '1.000', '0.000', '150.00'),
(102, 60, 10, 6, '1.000', '1.000', '95.00'),
(103, 60, 10, 6, '1.000', '1.000', '95.00'),
(104, 60, 5, 6, '1.000', '0.000', '100.00'),
(105, 60, 6, 6, '1.000', '0.000', '150.00'),
(106, 60, 6, 6, '1.000', '0.000', '150.00'),
(107, 60, 7, 5, '1.000', '0.000', '550.99'),
(108, 61, 6, 6, '1.000', '0.000', '150.00'),
(109, 61, 6, 6, '1.000', '0.000', '150.00'),
(110, 61, 5, 6, '1.000', '0.000', '100.00'),
(111, 61, 1, 6, '1.000', '1.000', '70.00'),
(112, 61, 9, 6, '1.000', '1.000', '100.00'),
(113, 61, 7, 5, '1.000', '0.000', '550.99'),
(114, 62, 1, 6, '6.000', '6.000', '0.00'),
(115, 64, 2, 3, '1.000', '1.000', '10.00'),
(116, 64, 3, 3, '1.000', '1.000', '25.00'),
(117, 64, 4, 3, '1.000', '1.000', '28.00'),
(118, 65, 4, 3, '1.000', '1.000', '28.00'),
(119, 65, 2, 3, '1.000', '1.000', '10.00'),
(120, 65, 12, 7, '1.000', '1.000', '80.00'),
(121, 65, 13, 7, '1.000', '1.000', '80.00'),
(122, 67, 4, 3, '1.000', '1.000', '28.00'),
(123, 67, 2, 3, '1.000', '1.000', '10.00'),
(124, 67, 12, 7, '1.000', '1.000', '80.00'),
(125, 67, 13, 7, '1.000', '1.000', '80.00'),
(126, 69, 4, 3, '1.000', '1.000', '28.00'),
(127, 69, 2, 3, '1.000', '1.000', '10.00'),
(128, 69, 12, 7, '1.000', '1.000', '80.00'),
(129, 69, 13, 7, '1.000', '1.000', '80.00'),
(130, 70, 17, 6, '1.000', '1.000', '35.00'),
(131, 70, 18, 6, '1.000', '1.000', '25.00'),
(132, 70, 19, 6, '1.000', '1.000', '50.00'),
(133, 71, 31, 7, '100.000', '100.000', '50.00'),
(134, 72, 7, 6, '12.000', '12.000', '8.60'),
(135, 72, 2, 3, '1.000', '1.000', '10.00'),
(136, 73, 2, 3, '1.000', '1.000', '10.00'),
(137, 73, 3, 3, '1.000', '1.000', '25.00'),
(138, 73, 4, 3, '1.000', '1.000', '28.00'),
(139, 74, 2, 3, '1.000', '1.000', '10.00'),
(140, 74, 3, 3, '1.000', '1.000', '25.00'),
(141, 74, 4, 3, '1.000', '1.000', '28.00'),
(142, 76, 2, 3, '1.000', '1.000', '10.00'),
(143, 76, 3, 3, '1.000', '1.000', '25.00'),
(144, 76, 4, 3, '1.000', '1.000', '28.00'),
(145, 77, 2, 3, '50.000', '50.000', '100.00'),
(146, 77, 37, 3, '10.000', '10.000', '900.00'),
(147, 78, 1, 6, '1.000', '1.000', '80.00'),
(148, 78, 37, 3, '0.100', '0.000', '210.00'),
(149, 79, 1, 6, '1.000', '1.000', '80.00'),
(150, 80, 1, 6, '2.000', '2.000', '80.00'),
(151, 81, 5, 6, '1.000', '0.000', '100.00'),
(152, 82, 37, 3, '1.000', '0.000', '210.00');

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

--
-- Volcado de datos para la tabla `detalle_pedidos_venta`
--

INSERT INTO `detalle_pedidos_venta` (`DPVENTA_ID`, `FACTURAVENTA_ID`, `MERCADERIA_ID`, `UNIMEDIDA_ID`, `CANTIDAD`, `CANTIDAD_REAL`, `PRECIO`) VALUES
(1, 1, 1, 00000000006, '1.000', '1.000', '50.00'),
(2, 1, 2, 00000000003, '1.000', '1.000', '30.00'),
(3, 1, 3, 00000000002, '3.000', '3.000', '35.00'),
(4, 2, 1, 00000000006, '2.000', '2.000', '50.00'),
(5, 2, 3, 00000000002, '5.000', '5.000', '35.00'),
(6, 3, 1, 00000000006, '3.000', '3.000', '50.00'),
(7, 4, 2, 00000000003, '1.000', '1.000', '30.00'),
(8, 4, 3, 00000000002, '4.000', '4.000', '35.00'),
(9, 4, 1, 00000000006, '2.000', '2.000', '50.00'),
(10, 5, 3, 00000000002, '1.000', '1.000', '35.00'),
(11, 6, 2, 00000000003, '5.000', '5.000', '30.00'),
(12, 7, 1, 00000000006, '1.000', '1.000', '50.00'),
(13, 8, 3, 00000000002, '2.000', '2.000', '35.00'),
(14, 10, 1, 00000000006, '5.000', '5.000', '50.00'),
(15, 10, 3, 00000000002, '3.000', '3.000', '35.00'),
(16, 10, 2, 00000000003, '1.000', '1.000', '30.00'),
(17, 11, 1, 00000000006, '5.000', '5.000', '50.00'),
(18, 12, 3, 00000000002, '2.000', '2.000', '35.00'),
(19, 12, 1, 00000000006, '5.000', '5.000', '50.00'),
(20, 13, 2, 00000000003, '1.000', '1.000', '30.00'),
(21, 14, 1, 00000000006, '1.000', '1.000', '50.00'),
(22, 15, 3, 00000000002, '1.000', '1.000', '35.00'),
(23, 16, 2, 00000000003, '1.000', '1.000', '30.00'),
(24, 17, 5, 00000000006, '10.000', '10.000', '70.00'),
(27, 20, 5, 00000000006, '10.000', '10.000', '70.00'),
(28, 20, 1, 00000000006, '1.000', '1.000', '50.00'),
(29, 21, 5, 00000000006, '10.000', '10.000', '70.00'),
(30, 22, 5, 00000000006, '1.000', '1.000', '70.00'),
(31, 23, 1, 00000000006, '1.000', '1.000', '50.00'),
(32, 23, 5, 00000000006, '5.000', '5.000', '100.00'),
(37, 25, 1, 00000000006, '1.000', '1.000', '65.50'),
(38, 25, 5, 00000000006, '1.000', '1.000', '100.00'),
(39, 26, 6, 00000000006, '2.000', '2.000', '150.00'),
(40, 26, 1, 00000000006, '1.000', '1.000', '65.50'),
(41, 27, 7, 00000000006, '1.000', '1.000', '550.99'),
(42, 28, 1, 00000000006, '1.000', '1.000', '65.50'),
(43, 29, 1, 00000000006, '1.000', '1.000', '65.50'),
(44, 30, 7, 00000000006, '1.000', '1.000', '550.99'),
(45, 31, 5, 00000000006, '1.000', '1.000', '100.00'),
(46, 32, 5, 00000000006, '1.000', '1.000', '100.00'),
(47, 33, 9, 00000000006, '2.000', '2.000', '100.00'),
(48, 34, 1, 00000000006, '1.000', '1.000', '65.50'),
(49, 34, 7, 00000000006, '1.000', '1.000', '550.99'),
(50, 35, 10, 00000000006, '1.000', '1.000', '95.00'),
(51, 36, 1, 00000000006, '1.000', '1.000', '70.00'),
(52, 36, 6, 00000000006, '1.000', '1.000', '150.00'),
(53, 37, 6, 00000000006, '1.000', '1.000', '150.00'),
(54, 38, 1, 00000000006, '1.000', '1.000', '70.00'),
(55, 38, 6, 00000000006, '1.000', '1.000', '150.00'),
(56, 38, 7, 00000000005, '1.000', '12.000', '550.99'),
(57, 39, 7, 00000000005, '1.000', '12.000', '550.99'),
(58, 40, 1, 00000000004, '5.000', '60.000', '315.00'),
(59, 40, 9, 00000000006, '6.000', '6.000', '40.00'),
(60, 40, 8, 00000000006, '6.000', '6.000', '50.00'),
(61, 41, 1, 00000000006, '1.000', '1.000', '70.00'),
(62, 41, 5, 00000000006, '1.000', '1.000', '100.00'),
(63, 42, 6, 00000000006, '1.000', '1.000', '150.00'),
(64, 42, 9, 00000000006, '1.000', '1.000', '100.00'),
(65, 43, 9, 00000000006, '1.000', '1.000', '100.00'),
(66, 44, 8, 00000000006, '1.000', '1.000', '65.00'),
(67, 45, 9, 00000000006, '1.000', '1.000', '100.00'),
(68, 46, 6, 00000000006, '1.000', '1.000', '150.00'),
(69, 47, 1, 00000000006, '1.000', '1.000', '80.00'),
(70, 48, 1, 00000000006, '1.000', '1.000', '70.00'),
(71, 49, 6, 00000000006, '1.000', '1.000', '150.00'),
(72, 50, 7, 00000000005, '1.000', '12.000', '550.99'),
(73, 51, 1, 00000000006, '8.000', '8.000', '50.00'),
(74, 51, 5, 00000000006, '12.000', '12.000', '60.00'),
(75, 51, 8, 00000000006, '6.000', '6.000', '50.00'),
(76, 51, 9, 00000000006, '6.000', '6.000', '40.00'),
(77, 51, 10, 00000000006, '6.000', '6.000', '85.00'),
(78, 52, 1, 00000000006, '8.000', '8.000', '50.00'),
(79, 53, 29, 00000000006, '6.000', '6.000', '60.00'),
(80, 54, 1, 00000000004, '1.000', '12.000', '800.00'),
(81, 55, 1, 00000000004, '3.000', '36.000', '800.00'),
(82, 56, 1, 00000000004, '3.000', '36.000', '800.00'),
(83, 57, 1, 00000000006, '1.000', '1.000', '70.00'),
(84, 57, 7, 00000000005, '1.000', '0.000', '550.99'),
(85, 57, 6, 00000000006, '1.000', '0.000', '150.00'),
(86, 57, 9, 00000000006, '1.000', '1.000', '100.00'),
(87, 58, 26, 00000000003, '1.000', '1.000', '70.00'),
(88, 58, 22, 00000000006, '1.000', '1.000', '40.00'),
(89, 58, 24, 00000000006, '1.000', '1.000', '95.00'),
(90, 59, 3, 00000000003, '1.000', '1.000', '25.00'),
(91, 59, 2, 00000000003, '1.000', '1.000', '10.00'),
(92, 59, 6, 00000000006, '1.000', '1.000', '49.50'),
(93, 59, 6, 00000000006, '1.000', '1.000', '49.50'),
(94, 59, 4, 00000000003, '1.000', '1.000', '28.00'),
(95, 59, 33, 00000000001, '1.000', '0.000', '100.00'),
(96, 59, 28, 00000000007, '1.000', '1.000', '70.00'),
(97, 59, 16, 00000000006, '1.000', '1.000', '100.00'),
(98, 59, 19, 00000000006, '1.000', '1.000', '50.00'),
(99, 59, 18, 00000000006, '1.000', '1.000', '25.00'),
(100, 59, 21, 00000000006, '1.000', '1.000', '40.00'),
(101, 60, 6, 00000000006, '1.000', '0.000', '150.00'),
(102, 60, 10, 00000000006, '1.000', '1.000', '95.00'),
(103, 60, 10, 00000000006, '1.000', '1.000', '95.00'),
(104, 60, 5, 00000000006, '1.000', '0.000', '100.00'),
(105, 60, 6, 00000000006, '1.000', '0.000', '150.00'),
(106, 60, 6, 00000000006, '1.000', '0.000', '150.00'),
(107, 60, 7, 00000000005, '1.000', '0.000', '550.99'),
(108, 61, 6, 00000000006, '1.000', '0.000', '150.00'),
(109, 61, 6, 00000000006, '1.000', '0.000', '150.00'),
(110, 61, 5, 00000000006, '1.000', '0.000', '100.00'),
(111, 61, 1, 00000000006, '1.000', '1.000', '70.00'),
(112, 61, 9, 00000000006, '1.000', '1.000', '100.00'),
(113, 61, 7, 00000000005, '1.000', '0.000', '550.99'),
(114, 62, 1, 00000000006, '6.000', '6.000', '0.00'),
(115, 64, 2, 00000000003, '1.000', '1.000', '10.00'),
(116, 64, 3, 00000000003, '1.000', '1.000', '25.00'),
(117, 64, 4, 00000000003, '1.000', '1.000', '28.00'),
(118, 65, 4, 00000000003, '1.000', '1.000', '28.00'),
(119, 65, 2, 00000000003, '1.000', '1.000', '10.00'),
(120, 65, 12, 00000000007, '1.000', '1.000', '80.00'),
(121, 65, 13, 00000000007, '1.000', '1.000', '80.00'),
(122, 67, 4, 00000000003, '1.000', '1.000', '28.00'),
(123, 67, 2, 00000000003, '1.000', '1.000', '10.00'),
(124, 67, 12, 00000000007, '1.000', '1.000', '80.00'),
(125, 67, 13, 00000000007, '1.000', '1.000', '80.00'),
(126, 69, 4, 00000000003, '1.000', '1.000', '28.00'),
(127, 69, 2, 00000000003, '1.000', '1.000', '10.00'),
(128, 69, 12, 00000000007, '1.000', '1.000', '80.00'),
(129, 69, 13, 00000000007, '1.000', '1.000', '80.00'),
(130, 70, 17, 00000000006, '1.000', '1.000', '35.00'),
(131, 70, 18, 00000000006, '1.000', '1.000', '25.00'),
(132, 70, 19, 00000000006, '1.000', '1.000', '50.00'),
(133, 71, 31, 00000000007, '100.000', '100.000', '50.00'),
(134, 72, 7, 00000000006, '12.000', '12.000', '8.60'),
(135, 72, 2, 00000000003, '1.000', '1.000', '10.00'),
(136, 73, 2, 00000000003, '1.000', '1.000', '10.00'),
(137, 73, 3, 00000000003, '1.000', '1.000', '25.00'),
(138, 73, 4, 00000000003, '1.000', '1.000', '28.00'),
(139, 74, 2, 00000000003, '1.000', '1.000', '10.00'),
(140, 74, 3, 00000000003, '1.000', '1.000', '25.00'),
(141, 74, 4, 00000000003, '1.000', '1.000', '28.00'),
(142, 76, 2, 00000000003, '1.000', '1.000', '10.00'),
(143, 76, 3, 00000000003, '1.000', '1.000', '25.00'),
(144, 76, 4, 00000000003, '1.000', '1.000', '28.00'),
(145, 77, 2, 00000000003, '50.000', '50.000', '100.00'),
(146, 77, 37, 00000000003, '10.000', '10.000', '900.00'),
(147, 78, 1, 00000000006, '1.000', '1.000', '80.00'),
(148, 78, 37, 00000000003, '0.100', '0.000', '210.00'),
(149, 79, 1, 00000000006, '1.000', '1.000', '80.00'),
(150, 80, 1, 00000000006, '2.000', '2.000', '80.00'),
(151, 81, 5, 00000000006, '1.000', '0.000', '100.00'),
(152, 82, 37, 00000000003, '1.000', '0.000', '210.00');

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
(1, 1, 1, 1, '36636440', 'Leandro Matias', 'Boos', '1991-12-19', '20366364405', 'putoelquelolea', NULL, NULL, 'av siempre viva', '2021-05-22 13:27:36', NULL),
(2, 1, 2, 1, '39722411', 'Agui Cristel', 'Montañez', '1996-05-24', '11397224111', '12345678', '', NULL, NULL, '2021-05-26 21:52:40', NULL);

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
  `CANTIDAD_PAGO` tinyint(2) UNSIGNED DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `facturacompra_formapago`
--

INSERT INTO `facturacompra_formapago` (`FACTURACFP_ID`, `FACTURA_ID`, `FORMAPAGO_ID`, `CANTIDAD_PAGO`) VALUES
(1, 1, 1, 1),
(2, 3, 1, 1),
(3, 4, 1, 1),
(4, 5, 1, 1),
(5, 7, 1, 1),
(6, 9, 1, 1),
(7, 10, 1, 1),
(8, 11, 1, 1),
(9, 12, 1, 1),
(10, 13, 1, 1),
(11, 14, 1, 1),
(12, 15, 1, 1),
(13, 16, 1, 1),
(14, 17, 1, 1),
(17, 20, 1, 1),
(18, 23, 1, 1),
(19, 25, 1, 1),
(20, 2, 1, 1),
(21, 22, 1, 1),
(22, 21, 1, 1),
(23, 6, 1, 1),
(24, 8, 1, 1),
(25, 26, 1, 1),
(26, 27, 1, 1),
(27, 28, 1, 1),
(28, 29, 1, 1),
(29, 30, 1, 1),
(30, 31, 1, 1),
(31, 32, 1, 1),
(32, 33, 1, 1),
(33, 34, 1, 1),
(34, 35, 1, 1),
(35, 36, 1, 1),
(36, 37, 1, 1),
(37, 38, 1, 1),
(38, 39, 1, 1),
(39, 40, 1, 1),
(40, 41, 1, 1),
(41, 42, 1, 1),
(42, 43, 1, 1),
(43, 44, 1, 1),
(44, 45, 1, 1),
(45, 46, 1, 1),
(46, 47, 1, 1),
(47, 48, 1, 1),
(48, 49, 1, 1),
(49, 50, 1, 1),
(50, 51, 1, 1),
(51, 52, 1, 1),
(52, 53, 1, 1),
(53, 54, 1, 1),
(54, 55, 1, 1),
(55, 56, 1, 1),
(56, 57, 1, 1),
(57, 58, 1, 1),
(58, 59, 1, 1),
(59, 60, 1, 1),
(60, 61, 2, 1),
(61, 62, 1, 1),
(62, 64, 1, 1),
(63, 65, 1, 1),
(64, 67, 1, 1),
(65, 69, 1, 1),
(66, 70, 2, 1),
(67, 71, 1, 1),
(68, 72, 2, 1),
(69, 73, 1, 1),
(70, 74, 1, 1),
(71, 76, 1, 1),
(72, 77, 1, 1),
(73, 78, 1, 1),
(74, 79, 1, 1),
(75, 80, 1, 1),
(76, 81, 1, 1),
(77, 82, 1, 1);

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

--
-- Volcado de datos para la tabla `facturas_compra`
--

INSERT INTO `facturas_compra` (`FACTURACOMPRA_ID`, `FACTURATIPO_ID`, `NUMERO_FACTURA`, `PROVEEDOR_ID`, `SUCURSAL_ID`, `EMPLEADO_ID`, `TESTIGO_ID`, `ESTADO_ID`, `FECHA_ALTA`, `FECHA_EMISION`, `DIRECCION_ENVIO`) VALUES
(1, 2, 0000000000001, 15, 1, 1, 1, 1, '2019-02-17 13:45:30', '0000-00-00', NULL),
(2, 2, 0000000000002, 15, 1, 1, 1, 1, '2019-02-17 13:49:24', '0000-00-00', NULL),
(3, 2, 0000000000003, 15, 1, 2, 1, 1, '2019-02-17 13:56:05', '0000-00-00', NULL),
(4, 2, 0000000000004, 15, 1, 1, 1, 1, '2019-02-17 13:59:30', '0000-00-00', NULL),
(5, 2, 0000000000005, 15, 1, 2, 1, 1, '2019-02-17 14:01:14', '0000-00-00', NULL),
(6, 2, 0000000000006, 15, 1, 2, 1, 1, '2019-02-17 14:03:30', '0000-00-00', NULL),
(7, 2, 0000000000007, 15, 1, 1, 1, 1, '2019-02-18 20:43:28', '0000-00-00', NULL),
(8, 2, 0000000000008, 15, 1, 1, 1, 1, '2019-02-18 20:45:54', '0000-00-00', NULL),
(9, 2, 0000000000009, 15, 1, 2, 1, 1, '2019-03-09 20:10:37', '0000-00-00', NULL),
(10, 2, 0000000000010, 15, 1, 1, 1, 1, '2019-03-09 20:12:06', '0000-00-00', NULL),
(11, 2, 0000000000011, 15, 1, 2, 1, 1, '2019-03-11 15:22:57', '0000-00-00', NULL),
(12, 2, 0000000000012, 15, 1, 2, 1, 1, '2019-03-12 12:50:20', '0000-00-00', NULL),
(13, 2, 0000000000013, 15, 1, 1, 1, 1, '2019-03-12 13:01:46', '0000-00-00', NULL),
(14, 2, 0000000000014, 15, 1, 1, 1, 1, '2019-03-12 13:02:38', '0000-00-00', NULL),
(15, 2, 0000000000015, 15, 1, 2, 1, 1, '2019-03-12 13:05:55', '0000-00-00', NULL),
(16, 2, 0000000000016, 15, 1, 2, 1, 1, '2019-03-12 13:06:53', '0000-00-00', NULL),
(17, 1, 0000555123014, 2, 1, 2, 1, 1, '2019-03-12 15:28:39', '0000-00-00', NULL),
(20, 1, 0000555123015, 2, 1, 2, 1, 1, '2019-03-12 15:33:33', '0000-00-00', NULL),
(21, 1, 0001111111154, 2, 1, 1, 1, 1, '2019-03-12 15:37:13', '0000-00-00', NULL),
(22, 1, 0000045678977, 2, 1, 1, 1, 1, '2019-03-12 15:37:58', '0000-00-00', NULL),
(23, 2, 0000000000017, 15, 1, 1, 1, 1, '2019-03-12 16:05:45', '0000-00-00', NULL),
(25, 2, 0000000000018, 15, 1, 6, 1, 1, '2019-10-06 00:00:00', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(26, 2, 0000000000019, 15, 1, 6, 1, 1, '2019-10-07 00:00:00', '0000-00-00', 'AL LADO DEL VECINO'),
(27, 2, 0000000000020, 15, 1, 6, 1, 1, '2019-10-07 00:00:00', '0000-00-00', 'AL LADO DEL VECINO'),
(28, 2, 0000000000021, 15, 1, 6, 1, 1, '2019-10-07 00:00:00', '0000-00-00', NULL),
(29, 2, 0000000000022, 15, 1, 6, 1, 1, '2019-10-07 00:00:00', '0000-00-00', 'EN ALGUN LUGAR DE POSADAS'),
(30, 2, 0000000000023, 15, 1, 6, 1, 1, '2019-10-07 00:00:00', '0000-00-00', 'VECINO DE LEO'),
(31, 2, 0000000000024, 15, 1, 6, 1, 1, '2019-10-07 00:00:00', '0000-00-00', 'VECINO DE LEO'),
(32, 2, 0000000000025, 15, 1, 6, 1, 1, '2019-10-09 00:00:00', '0000-00-00', NULL),
(33, 2, 0000000000026, 15, 1, 1, 1, 1, '2019-10-11 00:00:00', '0000-00-00', 'VILLA PELO'),
(34, 2, 0000000000027, 15, 1, 8, 1, 1, '2019-11-17 00:00:00', '0000-00-00', 'EN ALGUN LUGAR DE POSADAS'),
(35, 2, 0000000000028, 15, 1, 8, 1, 1, '2019-11-17 17:01:01', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(36, 2, 0000000000029, 15, 1, 9, 1, 1, '2019-11-24 21:55:33', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(37, 2, 0000000000030, 15, 1, 9, 1, 1, '2019-11-24 22:11:28', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(38, 2, 0000000000031, 15, 1, 9, 1, 1, '2019-11-24 22:12:04', '0000-00-00', 'VILLA PELO'),
(39, 2, 0000000000032, 15, 1, 9, 1, 1, '2019-11-25 14:59:11', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(40, 1, 0006548789878, 10, 1, 9, 1, 1, '2019-11-26 17:27:44', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(41, 2, 0000000000033, 15, 1, 8, 1, 1, '2019-11-29 17:18:55', '0000-00-00', NULL),
(42, 2, 0000000000034, 15, 1, 8, 1, 1, '2019-11-29 18:16:23', '0000-00-00', 'DDDDDDDDBBBBBBBB FFFFFFFFFFFFFFFF 9874 0'),
(43, 2, 0000000000035, 15, 1, 8, 1, 1, '2019-11-29 18:18:13', '0000-00-00', 'DDDDDDDDBBBBBBBB FFFFFFFFFFFFFFFF 9874 0'),
(44, 2, 0000000000036, 15, 1, 8, 1, 1, '2019-11-29 18:19:55', '0000-00-00', 'VILLA PELO'),
(45, 2, 0000000000037, 15, 1, 8, 1, 1, '2019-11-29 18:42:43', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(46, 2, 0000000000038, 15, 1, 8, 1, 1, '2019-11-29 20:39:21', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(47, 2, 0000000000039, 15, 1, 8, 1, 1, '2019-11-29 20:42:44', '0000-00-00', 'EN ALGUN LUGAR DE POSADAS'),
(48, 2, 0000000000040, 15, 1, 8, 1, 1, '2019-11-29 22:56:58', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(49, 2, 0000000000041, 15, 1, 8, 1, 1, '2019-11-29 22:58:02', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(50, 2, 0000000000042, 15, 1, 8, 1, 1, '2019-11-29 23:01:58', '0000-00-00', 'EN ALGUN LUGAR DE POSADAS'),
(51, 1, 0023245678978, 7, 1, 8, 1, 1, '2019-11-29 23:03:54', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(52, 1, 0023245678999, 7, 1, 8, 1, 1, '2019-11-29 23:08:13', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(53, 1, 0000478548978, 6, 1, 8, 1, 1, '2020-02-09 19:39:16', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(54, 1, 0007895465478, 9, 1, 9, 1, 1, '2020-02-17 19:40:26', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(55, 1, 0047898456121, 10, 1, 1, 1, 1, '2020-03-19 17:50:16', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(56, 1, 0000045134657, 10, 1, 1, 1, 1, '2020-03-19 18:47:39', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(57, 2, 0000000000043, 15, 1, 8, 1, 1, '2020-03-19 18:50:08', '0000-00-00', 'AL LADO DEL VECINO'),
(58, 1, 0023245678979, 2, 1, 1, 1, 1, '2020-03-19 20:48:00', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(59, 1, 0000045134646, 6, 1, 5, 1, 1, '2020-03-19 20:51:05', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(60, 2, 0000000000044, 15, 1, 13, 1, 1, '2020-03-19 21:02:01', '0000-00-00', 'QUIEN TE CONOCE PAPÃ'),
(61, 2, 0000000000045, 15, 1, 8, 1, 1, '2020-03-19 21:03:33', '0000-00-00', 'AFSDFSA FGRWEG 7000 0'),
(62, 1, 0036589745125, 10, 1, 6, 1, 1, '2020-04-05 22:37:44', '0000-00-00', NULL),
(64, 1, 0005487561247, 10, 1, 8, 1, 1, '2020-04-05 23:08:08', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(65, 1, 0046456777111, 9, 1, 8, 1, 1, '2020-04-06 00:47:37', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(67, 1, 0046456777112, 5, 1, 8, 1, 1, '2020-04-06 00:50:27', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(69, 1, 0046456777113, 1, 1, 8, 1, 1, '2020-04-06 00:55:30', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(70, 1, 0032144444444, 14, 1, 8, 1, 1, '2020-04-06 01:09:29', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(71, 1, 0011112222222, 14, 1, 8, 1, 1, '2020-04-06 01:11:07', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(72, 1, 0011113333333, 8, 1, 8, 1, 1, '2020-04-06 01:19:20', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(73, 1, 0032144444443, 9, 1, 8, 1, 1, '2020-04-06 01:20:03', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(74, 1, 0054875612478, 8, 1, 8, 1, 1, '2020-04-06 01:20:39', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(76, 1, 0054875612476, 8, 1, 8, 1, 1, '2020-04-06 01:23:53', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(77, 1, 0000364587441, 17, 1, 8, 1, 1, '2020-11-01 01:29:48', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(78, 2, 0000000000046, 8, 1, 8, 1, 1, '2020-11-01 01:35:58', '0000-00-00', 'AV MITRE 1234'),
(79, 2, 0000000000047, 8, 1, 9, 1, 1, '2020-12-03 21:21:00', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(80, 2, 0000000000048, 8, 1, 9, 1, 1, '2020-12-03 21:41:40', '0000-00-00', 'DONDE CAGO EL CONEJO'),
(81, 2, 0000000000049, 8, 1, 9, 1, 1, '2020-12-03 21:52:51', '0000-00-00', NULL),
(82, 2, 0000000000050, 8, 1, 9, 1, 1, '2020-12-03 22:06:08', '0000-00-00', 'QUIEN TE CONOCE PAPÃ');

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

--
-- Volcado de datos para la tabla `facturas_venta`
--

INSERT INTO `facturas_venta` (`FACTURAVENTA_ID`, `FACTURATIPO_ID`, `NUMERO_FACTURA`, `SUCURSAL_ID`, `CLIENTE_ID`, `EMPLEADO_ID`, `TESTIGO_ID`, `ESTADO_ID`, `FECHA_ALTA`, `FECHA_EMISION`, `DIRECCION_ENVIO`) VALUES
(1, 2, 0000000000001, 1, 1, 1, 1, 1, '2019-02-17 13:45:30', '0000-00-00', NULL),
(2, 2, 0000000000002, 1, 2, 1, 1, 1, '2019-02-17 13:49:24', '0000-00-00', NULL),
(3, 2, 0000000000003, 1, 1, 2, 1, 1, '2019-02-17 13:56:05', '0000-00-00', NULL),
(4, 2, 0000000000004, 1, 1, 1, 1, 1, '2019-02-17 13:59:30', '0000-00-00', NULL),
(5, 2, 0000000000005, 1, 1, 2, 1, 1, '2019-02-17 14:01:14', '0000-00-00', NULL),
(6, 2, 0000000000006, 1, 5, 2, 1, 1, '2019-02-17 14:03:30', '0000-00-00', NULL),
(7, 2, 0000000000007, 1, 1, 1, 1, 1, '2019-02-18 20:43:28', '0000-00-00', NULL),
(8, 2, 0000000000008, 1, 1000000002, 1, 1, 1, '2019-02-18 20:45:54', '0000-00-00', NULL),
(9, 2, 0000000000009, 1, 1, 2, 1, 1, '2019-03-09 20:10:37', '0000-00-00', NULL),
(10, 2, 0000000000010, 1, 1, 1, 1, 1, '2019-03-09 20:12:06', '0000-00-00', NULL),
(11, 2, 0000000000011, 1, 1, 2, 1, 1, '2019-03-11 15:22:57', '0000-00-00', NULL),
(12, 2, 0000000000012, 1, 1, 2, 1, 1, '2019-03-12 12:50:20', '0000-00-00', NULL),
(13, 2, 0000000000013, 1, 1, 1, 1, 1, '2019-03-12 13:01:46', '0000-00-00', NULL),
(14, 2, 0000000000014, 1, 1, 1, 1, 1, '2019-03-12 13:02:38', '0000-00-00', NULL),
(15, 2, 0000000000015, 1, 1, 2, 1, 1, '2019-03-12 13:05:55', '0000-00-00', NULL),
(16, 2, 0000000000016, 1, 1, 2, 1, 1, '2019-03-12 13:06:53', '0000-00-00', NULL),
(17, 1, 0000555123014, 1, 1, 2, 1, 1, '2019-03-12 15:28:39', '0000-00-00', NULL),
(20, 1, 0000555123015, 1, 1, 2, 1, 1, '2019-03-12 15:33:33', '0000-00-00', NULL),
(21, 1, 0001111111154, 1, 3, 1, 1, 1, '2019-03-12 15:37:13', '0000-00-00', NULL),
(22, 1, 0000045678977, 1, 2, 1, 1, 1, '2019-03-12 15:37:58', '0000-00-00', NULL),
(23, 2, 0000000000017, 1, 1, 1, 1, 1, '2019-03-12 16:05:45', '0000-00-00', NULL),
(25, 2, 0000000000018, 1, 1, 6, 1, 1, '2019-10-06 00:00:00', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(26, 2, 0000000000019, 1, 1000000011, 6, 1, 1, '2019-10-07 00:00:00', '0000-00-00', 'AL LADO DEL VECINO'),
(27, 2, 0000000000020, 1, 1000000011, 6, 1, 1, '2019-10-07 00:00:00', '0000-00-00', 'AL LADO DEL VECINO'),
(28, 2, 0000000000021, 1, 1000000012, 6, 1, 1, '2019-10-07 00:00:00', '0000-00-00', NULL),
(29, 2, 0000000000022, 1, 1000000012, 6, 1, 1, '2019-10-07 00:00:00', '0000-00-00', 'EN ALGUN LUGAR DE POSADAS'),
(30, 2, 0000000000023, 1, 1000000013, 6, 1, 1, '2019-10-07 00:00:00', '0000-00-00', 'VECINO DE LEO'),
(31, 2, 0000000000024, 1, 1000000013, 6, 1, 1, '2019-10-07 00:00:00', '0000-00-00', 'VECINO DE LEO'),
(32, 2, 0000000000025, 1, 1000000002, 6, 1, 1, '2019-10-09 00:00:00', '0000-00-00', NULL),
(33, 2, 0000000000026, 1, 1000000004, 1, 1, 1, '2019-10-11 00:00:00', '0000-00-00', 'VILLA PELO'),
(34, 2, 0000000000027, 1, 1000000012, 8, 1, 1, '2019-11-17 00:00:00', '0000-00-00', 'EN ALGUN LUGAR DE POSADAS'),
(35, 2, 0000000000028, 1, 1, 8, 1, 1, '2019-11-17 17:01:01', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(36, 2, 0000000000029, 1, 1, 9, 1, 1, '2019-11-24 21:55:33', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(37, 2, 0000000000030, 1, 1, 9, 1, 1, '2019-11-24 22:11:28', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(38, 2, 0000000000031, 1, 1000000004, 9, 1, 1, '2019-11-24 22:12:04', '0000-00-00', 'VILLA PELO'),
(39, 2, 0000000000032, 1, 1, 9, 1, 1, '2019-11-25 14:59:11', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(40, 1, 0006548789878, 1, 8, 9, 1, 1, '2019-11-26 17:27:44', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(41, 2, 0000000000033, 1, 1000000002, 8, 1, 1, '2019-11-29 17:18:55', '0000-00-00', NULL),
(42, 2, 0000000000034, 1, 2, 8, 1, 1, '2019-11-29 18:16:23', '0000-00-00', 'DDDDDDDDBBBBBBBB FFFFFFFFFFFFFFFF 9874 0'),
(43, 2, 0000000000035, 1, 2, 8, 1, 1, '2019-11-29 18:18:13', '0000-00-00', 'DDDDDDDDBBBBBBBB FFFFFFFFFFFFFFFF 9874 0'),
(44, 2, 0000000000036, 1, 1000000004, 8, 1, 1, '2019-11-29 18:19:55', '0000-00-00', 'VILLA PELO'),
(45, 2, 0000000000037, 1, 1, 8, 1, 1, '2019-11-29 18:42:43', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(46, 2, 0000000000038, 1, 1, 8, 1, 1, '2019-11-29 20:39:21', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(47, 2, 0000000000039, 1, 1000000012, 8, 1, 1, '2019-11-29 20:42:44', '0000-00-00', 'EN ALGUN LUGAR DE POSADAS'),
(48, 2, 0000000000040, 1, 1, 8, 1, 1, '2019-11-29 22:56:58', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(49, 2, 0000000000041, 1, 1, 8, 1, 1, '2019-11-29 22:58:02', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(50, 2, 0000000000042, 1, 1000000012, 8, 1, 1, '2019-11-29 23:01:58', '0000-00-00', 'EN ALGUN LUGAR DE POSADAS'),
(51, 1, 0023245678978, 1, 8, 8, 1, 1, '2019-11-29 23:03:54', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(52, 1, 0023245678999, 1, 8, 8, 1, 1, '2019-11-29 23:08:13', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(53, 1, 0000478548978, 1, 8, 8, 1, 1, '2020-02-09 19:39:16', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(54, 1, 0007895465478, 1, 8, 9, 1, 1, '2020-02-17 19:40:26', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(55, 1, 0047898456121, 1, 8, 1, 1, 1, '2020-03-19 17:50:16', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(56, 1, 0000045134657, 1, 8, 1, 1, 1, '2020-03-19 18:47:39', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(57, 2, 0000000000043, 1, 1000000011, 8, 1, 1, '2020-03-19 18:50:08', '0000-00-00', 'AL LADO DEL VECINO'),
(58, 1, 0023245678979, 1, 8, 1, 1, 1, '2020-03-19 20:48:00', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(59, 1, 0000045134646, 1, 8, 5, 1, 1, '2020-03-19 20:51:05', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(60, 2, 0000000000044, 1, 1000000030, 13, 1, 1, '2020-03-19 21:02:01', '0000-00-00', 'QUIEN TE CONOCE PAPÃ'),
(61, 2, 0000000000045, 1, 5, 8, 1, 1, '2020-03-19 21:03:33', '0000-00-00', 'AFSDFSA FGRWEG 7000 0'),
(62, 1, 0036589745125, 1, 1, 6, 1, 1, '2020-04-05 22:37:44', '0000-00-00', NULL),
(64, 1, 0005487561247, 1, 8, 8, 1, 1, '2020-04-05 23:08:08', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(65, 1, 0046456777111, 1, 8, 8, 1, 1, '2020-04-06 00:47:37', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(67, 1, 0046456777112, 1, 8, 8, 1, 1, '2020-04-06 00:50:27', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(69, 1, 0046456777113, 1, 8, 8, 1, 1, '2020-04-06 00:55:30', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(70, 1, 0032144444444, 1, 8, 8, 1, 1, '2020-04-06 01:09:29', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(71, 1, 0011112222222, 1, 8, 8, 1, 1, '2020-04-06 01:11:07', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(72, 1, 0011113333333, 1, 8, 8, 1, 1, '2020-04-06 01:19:20', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(73, 1, 0032144444443, 1, 8, 8, 1, 1, '2020-04-06 01:20:03', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(74, 1, 0054875612478, 1, 8, 8, 1, 1, '2020-04-06 01:20:39', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(76, 1, 0054875612476, 1, 8, 8, 1, 1, '2020-04-06 01:23:53', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(77, 1, 0000364587441, 1, 8, 8, 1, 1, '2020-11-01 01:29:48', '0000-00-00', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0'),
(78, 2, 0000000000046, 1, 1000000037, 8, 1, 1, '2020-11-01 01:35:58', '0000-00-00', 'AV MITRE 1234'),
(79, 2, 0000000000047, 1, 1, 9, 1, 1, '2020-12-03 21:21:00', '0000-00-00', 'POSADAS VILLA PELO CALLE FALSA 123 3'),
(80, 2, 0000000000048, 1, 1000000031, 9, 1, 1, '2020-12-03 21:41:40', '0000-00-00', 'DONDE CAGO EL CONEJO'),
(81, 2, 0000000000049, 1, 1000000021, 9, 1, 1, '2020-12-03 21:52:51', '0000-00-00', NULL),
(82, 2, 0000000000050, 1, 1000000030, 9, 1, 1, '2020-12-03 22:06:08', '0000-00-00', 'QUIEN TE CONOCE PAPÃ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturaventa_formapago`
--

DROP TABLE IF EXISTS `facturaventa_formapago`;
CREATE TABLE `facturaventa_formapago` (
  `FACTURAVFP_ID` int(11) UNSIGNED NOT NULL,
  `FACTURA_ID` int(11) UNSIGNED NOT NULL,
  `FORMAPAGO_ID` int(11) UNSIGNED NOT NULL,
  `CANTIDAD_PAGO` tinyint(2) UNSIGNED DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `facturaventa_formapago`
--

INSERT INTO `facturaventa_formapago` (`FACTURAVFP_ID`, `FACTURA_ID`, `FORMAPAGO_ID`, `CANTIDAD_PAGO`) VALUES
(1, 1, 1, 1),
(2, 3, 1, 1),
(3, 4, 1, 1),
(4, 5, 1, 1),
(5, 7, 1, 1),
(6, 9, 1, 1),
(7, 10, 1, 1),
(8, 11, 1, 1),
(9, 12, 1, 1),
(10, 13, 1, 1),
(11, 14, 1, 1),
(12, 15, 1, 1),
(13, 16, 1, 1),
(14, 17, 1, 1),
(17, 20, 1, 1),
(18, 23, 1, 1),
(19, 25, 1, 1),
(20, 2, 1, 1),
(21, 22, 1, 1),
(22, 21, 1, 1),
(23, 6, 1, 1),
(24, 8, 1, 1),
(25, 26, 1, 1),
(26, 27, 1, 1),
(27, 28, 1, 1),
(28, 29, 1, 1),
(29, 30, 1, 1),
(30, 31, 1, 1),
(31, 32, 1, 1),
(32, 33, 1, 1),
(33, 34, 1, 1),
(34, 35, 1, 1),
(35, 36, 1, 1),
(36, 37, 1, 1),
(37, 38, 1, 1),
(38, 39, 1, 1),
(39, 40, 1, 1),
(40, 41, 1, 1),
(41, 42, 1, 1),
(42, 43, 1, 1),
(43, 44, 1, 1),
(44, 45, 1, 1),
(45, 46, 1, 1),
(46, 47, 1, 1),
(47, 48, 1, 1),
(48, 49, 1, 1),
(49, 50, 1, 1),
(50, 51, 1, 1),
(51, 52, 1, 1),
(52, 53, 1, 1),
(53, 54, 1, 1),
(54, 55, 1, 1),
(55, 56, 1, 1),
(56, 57, 1, 1),
(57, 58, 1, 1),
(58, 59, 1, 1),
(59, 60, 1, 1),
(60, 61, 2, 1),
(61, 62, 1, 1),
(62, 64, 1, 1),
(63, 65, 1, 1),
(64, 67, 1, 1),
(65, 69, 1, 1),
(66, 70, 2, 1),
(67, 71, 1, 1),
(68, 72, 2, 1),
(69, 73, 1, 1),
(70, 74, 1, 1),
(71, 76, 1, 1),
(72, 77, 1, 1),
(73, 78, 1, 1),
(74, 79, 1, 1),
(75, 80, 1, 1),
(76, 81, 1, 1),
(77, 82, 1, 1);

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

--
-- Volcado de datos para la tabla `lista_insumos`
--

INSERT INTO `lista_insumos` (`INSUMO_ID`, `RECETA_ID`, `MERCADERIA_ID`, `UNIMEDIDA_ID`, `INSUMO_CANTIDAD`, `INSUMO_CANTIDAD_REAL`) VALUES
(1, 1, 3, 00000000007, '500.000', '0.000'),
(2, 1, 9, 00000000006, '1.000', '0.000'),
(5, 2, 3, 00000000007, '500.000', '0.000'),
(6, 2, 9, 00000000006, '1.000', '0.000'),
(7, 2, 10, 00000000006, '1.000', '0.000'),
(8, 3, 4, 00000000003, '0.500', '0.500'),
(9, 3, 28, 00000000007, '1.000', '1.000'),
(10, 4, 28, 00000000007, '50.000', '50.000'),
(11, 4, 4, 00000000003, '0.100', '0.100'),
(12, 4, 13, 00000000007, '500.000', '500.000'),
(13, 5, 10, 00000000006, '1.000', '1.000'),
(14, 5, 13, 00000000007, '3.000', '3.000'),
(18, 6, 2, 00000000003, '0.500', '0.500'),
(19, 6, 31, 00000000007, '100.000', '100.000'),
(20, 6, 13, 00000000007, '50.000', '50.000'),
(21, 6, 9, 00000000006, '1.000', '1.000'),
(22, 2, 3, 00000000003, '0.500', '0.500'),
(23, 2, 15, 00000000006, '1.000', '1.000'),
(24, 2, 28, 00000000007, '100.000', '100.000'),
(25, 5, 10, 00000000006, '1.000', '1.000'),
(26, 5, 13, 00000000007, '3.000', '3.000'),
(53, 7, 28, 00000000007, '1.000', '1.000'),
(54, 7, 25, 00000000006, '1.000', '1.000');

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

--
-- Volcado de datos para la tabla `mercaderias`
--

INSERT INTO `mercaderias` (`MERCADERIA_ID`, `IVA_ID`, `ESTADO_ID`, `CODIGO`, `NOMBRE`, `CANTIDAD_INICIAL`, `ALERTA_MINCANT`, `ALERTA_MAXCANT`, `FECHA_ALTA`, `FECHA_BAJA`) VALUES
(1, 2, 1, '00101', 'COCA COLA 1L', '10.000', '18.000', '100.000', '2018-11-13 00:00:00', NULL),
(2, 1, 1, '001741', 'TOMATE', '10.000', '25.000', '100.000', '2018-11-17 00:00:00', NULL),
(3, 1, 1, '36598741', 'HARINA 0000', '10.000', '50.000', '150.000', '2018-11-17 00:00:00', NULL),
(4, 1, 1, '12896', 'CEBOLLA', '10.000', '20.000', '33.000', '2018-11-17 00:00:00', NULL),
(5, 2, 1, '', 'BRAHMA', '10.000', '24.000', '500.000', '2018-11-24 00:00:00', NULL),
(6, 1, 1, '', 'PIZZA FAMILIAR', '10.000', '10.000', '100.000', '2018-11-24 00:00:00', NULL),
(7, 1, 1, '', 'EMPANADA DE CARNE', '10.000', '12.000', '120.000', '2018-11-24 00:00:00', NULL),
(8, 2, 1, '', 'MANAOS COLA 2L', '10.000', '5.000', '50.000', '2019-10-11 00:00:00', NULL),
(9, 2, 1, '', 'MANAOS UVA 3L', '10.000', '5.000', '20.000', '2019-10-11 00:00:00', NULL),
(10, 2, 2, '3687958', 'QUILMES', '10.000', '5.000', '50.000', '2019-10-18 00:00:00', NULL),
(11, 1, 1, '', 'PREPIZZA', '10.000', '5.000', '20.000', '2019-10-27 00:00:00', NULL),
(12, 1, 2, '352897', 'QUESORETE', '10.000', '1.000', '5.000', '2020-02-06 00:00:00', NULL),
(13, 1, 1, '', 'QUESORETE', '10.000', '1.000', '5.000', '2020-02-06 00:00:00', '2020-02-06 14:22:04'),
(14, 1, 1, '', 'FIDEOS MOLTO', '10.000', '10.000', '30.000', '2020-02-06 00:00:00', NULL),
(15, 1, 1, '', 'AROZ MAROLIO', '10.000', '10.000', '30.000', '2020-02-06 00:00:00', NULL),
(16, 2, 1, '', 'PAPAS FRITAS', '10.000', '10.000', '20.000', '2020-02-06 00:00:00', NULL),
(17, 1, 1, '', 'CHIPA', '10.000', '5.000', '25.000', '2020-02-06 00:00:00', NULL),
(18, 1, 1, '', 'CHIPITA', '10.000', '5.000', '25.000', '2020-02-06 00:00:00', NULL),
(19, 1, 1, '', 'CHIPOTA', '10.000', '5.000', '25.000', '2020-02-06 00:00:00', NULL),
(20, 1, 1, '', 'TODDY', '10.000', '10.000', '25.000', '2020-02-06 00:00:00', NULL),
(21, 1, 1, '', 'PEPAS', '10.000', '10.000', '20.000', '2020-02-06 00:00:00', NULL),
(22, 1, 1, '', 'FALOPAS', '10.000', '10.000', '20.000', '2020-02-06 00:00:00', NULL),
(23, 1, 1, '', 'PAPAS ', '10.000', '10.000', '20.000', '2020-02-06 00:00:00', NULL),
(24, 1, 1, '', 'HEINEKEN', '10.000', '12.000', '24.000', '2020-02-06 00:00:00', NULL),
(25, 1, 1, '', 'CARA DE COCO', '10.000', '12.000', '24.000', '2020-02-06 00:00:00', NULL),
(26, 1, 1, '', 'MILANESAS DE CARNE', '10.000', '1.000', '10.000', '2020-02-06 00:00:00', NULL),
(27, 1, 1, '', 'MILANESAS DE POLLO', '10.000', '1.000', '10.000', '2020-02-06 00:00:00', NULL),
(28, 2, 1, '', 'WASKA', '10.000', '100.000', '1000.000', '2020-02-06 00:00:00', NULL),
(29, 1, 1, '', 'BAGGIO MULTIFRUTA', '6.000', '5.000', '10.000', '2020-02-09 00:00:00', NULL),
(30, 1, 1, '', 'PANCHO VILLA', NULL, '10.000', '30.000', '2020-03-11 00:00:00', NULL),
(31, 1, 1, '', 'PAPAS PAI', '100.000', '500.000', '2000.000', '2020-03-12 00:00:00', NULL),
(32, 1, 1, '', 'CACAHUATE', NULL, '1000.000', '1100.000', '2020-03-19 00:00:00', NULL),
(33, 2, 1, '', 'BAGGIO MULTIFRUTA', '0.000', '150.000', '352.000', '2020-03-19 00:00:00', '2020-10-31 20:33:20'),
(34, 2, 1, '', 'BAGGIO MULTIFRUTA', NULL, '150.000', '352.000', '2020-03-19 00:00:00', '2020-10-31 20:33:36'),
(35, 1, 1, '', 'JUGO DE ALGO', NULL, '10.000', '30.000', '2020-03-19 00:00:00', NULL),
(36, 1, 1, '', 'PAN CON MANTECA', NULL, '25.000', '100.000', '2020-03-19 00:00:00', NULL),
(37, 1, 1, '', 'FIAMBRE OSCHI', '10.000', '1.000', '10.000', '2020-10-31 00:00:00', NULL),
(38, 1, 1, '', 'SANDWICH DE ALGO', NULL, '5.000', '25.000', '2020-10-31 00:00:00', NULL),
(43, 2, 1, '00102', 'FALOPA', NULL, '4.000', '6.000', '2021-06-29 21:02:30', NULL);

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

--
-- Volcado de datos para la tabla `mercaderias_cantidad_actual`
--

INSERT INTO `mercaderias_cantidad_actual` (`STOCKACTUAL_ID`, `MERCADERIA_ID`, `SUCURSAL_ID`, `CANTIDAD_ACTUAL`, `CANTIDAD_ANTERIOR`, `FECHA_ALTA`) VALUES
(1, 43, 1, '0.000', '0.000', '2021-06-29 21:02:30');

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

--
-- Volcado de datos para la tabla `mercaderias_rubros`
--

INSERT INTO `mercaderias_rubros` (`MERCADERIA_ID`, `RUBRO_ID`, `ENTRADA`) VALUES
(1, 2, '1'),
(2, 9, '1'),
(3, 9, '1'),
(4, 9, '1'),
(5, 1, '1'),
(5, 9, '2'),
(6, 3, '1'),
(7, 4, '1'),
(8, 2, '1'),
(9, 2, '1'),
(9, 9, '2'),
(10, 8, '1'),
(10, 9, '2'),
(11, 9, '1'),
(12, 9, '1'),
(13, 9, '1'),
(14, 9, '1'),
(15, 9, '1'),
(15, 10, '2'),
(16, 10, '1'),
(17, 10, '1'),
(18, 10, '1'),
(19, 10, '1'),
(20, 10, '1'),
(21, 10, '1'),
(22, 10, '1'),
(23, 10, '1'),
(24, 1, '1'),
(25, 9, '1'),
(25, 10, '2'),
(26, 9, '1'),
(27, 9, '1'),
(28, 9, '1'),
(29, 2, '1'),
(30, 10, '1'),
(31, 9, '1'),
(32, 10, '1'),
(33, 7, '1'),
(34, 7, '1'),
(35, 12, '1'),
(36, 12, '1'),
(37, 9, '1'),
(37, 12, '2'),
(38, 12, '1'),
(43, 2, '1');

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

--
-- Volcado de datos para la tabla `mercaderias_unidadesmedida`
--

INSERT INTO `mercaderias_unidadesmedida` (`MERCAUNIMED_ID`, `MERCADERIA_ID`, `UNIMEDIDA_ID`, `PRIORIDAD`) VALUES
(1, 1, 00000000004, 2),
(2, 1, 00000000006, 1),
(3, 2, 00000000003, 1),
(4, 3, 00000000003, 1),
(5, 4, 00000000003, 1),
(8, 6, 00000000006, 1),
(9, 7, 00000000005, 2),
(10, 7, 00000000006, 1),
(11, 8, 00000000006, 1),
(12, 9, 00000000006, 1),
(13, 10, 00000000006, 1),
(14, 11, 00000000006, 1),
(15, 5, 00000000006, 1),
(16, 5, 00000000004, 2),
(18, 13, 00000000007, 1),
(19, 14, 00000000006, 1),
(20, 15, 00000000006, 1),
(21, 16, 00000000006, 1),
(22, 17, 00000000006, 1),
(23, 18, 00000000006, 1),
(25, 20, 00000000006, 1),
(26, 21, 00000000006, 1),
(27, 22, 00000000006, 1),
(28, 23, 00000000006, 1),
(29, 24, 00000000006, 1),
(30, 25, 00000000006, 1),
(31, 26, 00000000003, 1),
(32, 27, 00000000003, 1),
(33, 28, 00000000007, 1),
(34, 29, 00000000006, 1),
(36, 31, 00000000007, 1),
(37, 32, 00000000006, 1),
(38, 19, 00000000006, 1),
(39, 19, 00000000005, 2),
(40, 19, 00000000002, 3),
(41, 33, 00000000001, 1),
(42, 34, 00000000001, 1),
(43, 35, 00000000014, 1),
(44, 12, 00000000007, 1),
(45, 12, 00000000002, 2),
(47, 36, 00000000006, 1),
(48, 36, 00000000005, 2),
(49, 30, 00000000006, 1),
(50, 30, 00000000005, 2),
(51, 37, 00000000003, 1),
(52, 38, 00000000006, 1),
(53, 43, 00000000006, 1);

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
(7, 'Unidades de Medida', 1);

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
    INSERT INTO permisos(CARGO_ID, MODULO_ID, LEER, AGREGAR, MODIFICAR, BORRAR) VALUES(idCargo, NEW.MODULO_ID, 0, 0, 0, 0);
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
  `SALDO_ACTUAL` decimal(8,2) NOT NULL DEFAULT '0.00',
  `MC_MONTO_E_S` decimal(8,2) NOT NULL,
  `FECHA_MOVIMIENTO` datetime NOT NULL,
  `MOVIMIENTOTIPO_ID` int(11) UNSIGNED NOT NULL,
  `DESCRIPCION` varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `movimiento_caja`
--

INSERT INTO `movimiento_caja` (`MC_ID`, `EMPLEADO_ID`, `FACTURAC_ID`, `FACTURAV_ID`, `SALDO_ACTUAL`, `MC_MONTO_E_S`, `FECHA_MOVIMIENTO`, `MOVIMIENTOTIPO_ID`, `DESCRIPCION`) VALUES
(1, 1, NULL, NULL, '10115.00', '115.00', '2019-11-27 15:32:29', 2, 'Por factura venta numero 1'),
(2, 1, NULL, NULL, '10200.00', '85.00', '2019-11-27 17:39:47', 2, 'Por factura venta numero 2'),
(3, 8, NULL, NULL, '10295.00', '95.00', '2019-11-29 20:55:59', 2, 'Por factura venta numero 28'),
(12, 9, NULL, NULL, '10515.00', '220.00', '2019-11-29 22:33:36', 2, 'Por factura venta numero 29'),
(14, 9, NULL, NULL, '10665.00', '150.00', '2019-11-29 22:50:39', 2, 'Por factura venta numero  30'),
(18, 8, NULL, NULL, '11215.99', '550.99', '2019-11-29 23:01:58', 2, 'Por factura venta numero  42'),
(19, 8, NULL, NULL, '11165.99', '50.00', '2019-11-29 23:08:14', 1, 'Por factura compra numero  23245678999'),
(20, 8, NULL, NULL, '10766.00', '399.99', '2019-12-05 16:41:25', 4, 'Extraccion de dinero por gastos varios'),
(21, 8, NULL, NULL, '9881.00', '234.00', '2019-12-09 13:15:50', 4, 'Ingreso de dinero por operaciones varias'),
(25, 8, NULL, NULL, '10349.00', '234.00', '2019-12-09 13:39:10', 3, 'Ingreso de dinero por operaciones varias'),
(26, 8, NULL, NULL, '10349.00', '234.00', '2019-12-09 13:48:18', 3, 'Ingreso de dinero por operaciones varias'),
(27, 8, NULL, NULL, '10135.00', '20.00', '2019-12-09 13:55:12', 3, 'Ingreso de dinero por operaciones varias'),
(28, 8, NULL, NULL, '10065.00', '50.00', '2019-12-09 13:59:20', 4, 'Extraccion de dinero por gastos varios'),
(29, 8, NULL, NULL, '10137.00', '22.00', '2019-12-09 14:01:53', 3, 'Ingreso de dinero por operaciones varias'),
(30, 8, NULL, NULL, '10114.00', '1.00', '2019-12-09 14:06:24', 4, 'Extraccion de dinero por gastos varios'),
(31, 8, NULL, NULL, '10116.00', '1.00', '2019-12-09 14:11:10', 3, 'Ingreso de dinero por operaciones varias'),
(32, 8, NULL, NULL, '10090.00', '25.00', '2019-12-09 14:15:35', 4, 'Extraccion de dinero por gastos varios'),
(33, 8, NULL, NULL, '10125.00', '10.00', '2019-12-09 14:15:45', 3, 'Ingreso de dinero por operaciones varias'),
(34, 8, NULL, NULL, '10110.00', '5.00', '2019-12-09 14:17:03', 4, 'Extraccion de dinero por gastos varios'),
(35, 8, NULL, NULL, '10215.00', '100.00', '2019-12-09 14:28:03', 3, 'Ingreso de dinero por operaciones varias'),
(36, 8, NULL, NULL, '10215.00', '100.00', '2019-12-09 14:46:38', 3, 'Ingreso de dinero por operaciones varias'),
(37, 8, NULL, NULL, '10215.00', '100.00', '2019-12-09 14:47:35', 3, 'Ingreso de dinero por operaciones varias'),
(38, 8, NULL, NULL, '10215.00', '100.00', '2019-12-09 14:47:49', 3, 'Ingreso de dinero por operaciones varias'),
(39, 8, NULL, NULL, '10315.00', '100.00', '2019-12-09 14:57:38', 3, 'Ingreso de dinero por operaciones varias'),
(40, 8, NULL, NULL, '8079.00', '2236.00', '2019-12-09 14:57:53', 4, 'Extraccion de dinero por gastos varios'),
(41, 8, NULL, NULL, '8079.00', '0.00', '2020-01-04 18:59:24', 5, 'Apertura de Caja'),
(42, 8, NULL, NULL, '8079.00', '0.00', '2020-01-05 16:23:03', 5, 'Apertura de Caja'),
(43, 8, NULL, NULL, '8079.00', '0.00', '2020-01-06 21:23:35', 5, 'Apertura de Caja'),
(44, 8, NULL, NULL, '16159.00', '8080.00', '2020-01-06 21:38:28', 5, 'Apertura de Caja'),
(45, 8, NULL, NULL, '8079.00', '8080.00', '2020-01-06 21:44:01', 6, 'Cierre de Caja (Apertura ID 00000000044)'),
(46, 8, NULL, NULL, '8079.00', '0.00', '2020-01-06 21:44:14', 5, 'Apertura de Caja'),
(47, 8, NULL, NULL, '8079.00', '0.00', '2020-01-23 13:07:19', 5, 'Apertura de Caja'),
(48, 8, NULL, NULL, '8079.00', '0.00', '2020-01-25 13:52:38', 5, 'Apertura de Caja'),
(49, 8, NULL, NULL, '8079.00', '0.00', '2020-03-22 23:53:03', 5, 'Apertura de Caja'),
(50, 8, NULL, NULL, '8079.00', '0.00', '2020-04-04 21:20:47', 5, 'Apertura de Caja'),
(51, 8, NULL, NULL, '8079.00', '0.00', '2020-04-05 22:47:36', 5, 'Apertura de Caja'),
(52, 8, NULL, NULL, '8016.00', '63.00', '2020-04-06 01:23:54', 1, 'Por factura compra numero  54875612476'),
(53, 9, NULL, NULL, '8016.00', '0.00', '2020-04-15 16:10:49', 5, 'Apertura de Caja'),
(54, 8, NULL, NULL, '8016.00', '0.00', '2020-11-01 01:19:46', 5, 'Apertura de Caja'),
(55, 8, NULL, NULL, '9016.00', '1000.00', '2020-11-01 01:20:59', 3, 'Ingreso de dinero por operaciones varias'),
(56, 8, NULL, NULL, '8516.00', '500.00', '2020-11-01 01:22:40', 4, 'Extraccion de dinero por gastos varios'),
(57, 8, NULL, NULL, '7516.00', '1000.00', '2020-11-01 01:29:48', 1, 'Por factura compra numero  364587441'),
(58, 8, NULL, NULL, '7806.00', '290.00', '2020-11-01 01:35:58', 2, 'Por factura venta numero  46'),
(59, 8, NULL, NULL, '7806.00', '0.00', '2020-11-01 01:45:49', 6, 'Cierre de Caja (Apertura ID 00000000054)'),
(60, 9, NULL, NULL, '7906.00', '100.00', '2020-12-03 21:06:03', 5, 'Apertura de Caja'),
(61, 9, NULL, NULL, '7986.00', '80.00', '2020-12-03 21:21:00', 2, 'Por factura venta numero  47'),
(62, 9, NULL, NULL, '8066.00', '80.00', '2020-12-03 21:41:41', 2, 'Por factura venta numero  48'),
(63, 9, NULL, NULL, '8166.00', '100.00', '2020-12-03 21:52:51', 2, 'Por factura venta numero  49'),
(64, 9, NULL, NULL, '8376.00', '210.00', '2020-12-03 22:06:08', 2, 'Por factura venta numero  50'),
(65, 8, NULL, NULL, '8376.00', '0.00', '2021-02-19 19:58:40', 5, 'Apertura de Caja'),
(66, 8, NULL, NULL, '8376.00', '0.00', '2021-03-10 13:55:39', 6, 'Cierre de Caja (Apertura ID 00000000065)'),
(67, 8, NULL, NULL, '8376.00', '0.00', '2021-03-10 15:21:05', 5, 'Apertura de Caja');

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
  `ESTADO` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1 activo, 0 inactivo',
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
(1, 1, 1, 1, 1, 1, 1),
(2, 1, 2, 1, 1, 1, 1),
(3, 1, 3, 1, 1, 1, 1),
(4, 1, 4, 1, 1, 1, 1),
(5, 1, 5, 1, 1, 1, 1),
(6, 2, 1, 1, 1, 1, 1),
(7, 2, 2, 1, 0, 0, 0),
(8, 2, 3, 0, 0, 0, 0),
(9, 2, 4, 0, 0, 0, 0),
(10, 2, 5, 0, 0, 0, 0),
(11, 3, 1, 1, 0, 0, 0),
(12, 3, 2, 0, 0, 0, 0),
(13, 3, 3, 0, 0, 0, 0),
(14, 3, 4, 0, 0, 0, 0),
(15, 3, 5, 0, 0, 0, 0),
(31, 7, 1, 1, 0, 0, 0),
(32, 7, 2, 0, 0, 0, 0),
(33, 7, 3, 0, 0, 0, 0),
(34, 7, 4, 0, 0, 0, 0),
(35, 7, 5, 0, 0, 0, 0),
(36, 8, 1, 0, 0, 0, 0),
(37, 8, 2, 0, 0, 0, 0),
(38, 8, 3, 0, 0, 0, 0),
(39, 8, 4, 0, 0, 0, 0),
(40, 8, 5, 0, 0, 0, 0),
(41, 3, 6, 0, 0, 0, 0),
(42, 7, 6, 0, 0, 0, 0),
(43, 8, 6, 0, 0, 0, 0),
(44, 2, 6, 0, 0, 0, 0),
(45, 1, 6, 1, 1, 1, 1),
(46, 3, 7, 0, 0, 0, 0),
(47, 7, 7, 0, 0, 0, 0),
(48, 8, 7, 0, 0, 0, 0),
(49, 2, 7, 0, 0, 0, 0),
(50, 1, 7, 1, 1, 1, 1);

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

--
-- Volcado de datos para la tabla `productos_compra`
--

INSERT INTO `productos_compra` (`PC_ID`, `MERCADERIA_ID`, `PC_NOMBRE`, `PC_FECHA_BAJA`, `PRECIO_COSTO`) VALUES
(1, 1, 'COCA COLA 1L.', NULL, '0.00'),
(2, 2, 'TOMATE', NULL, '0.00'),
(3, 3, 'HARINA 0000', NULL, '0.00'),
(4, 4, 'CEBOLLA', NULL, '0.00'),
(5, 5, 'BRAHMA', NULL, '0.00'),
(6, 6, 'PIZZA FAMILIAR', NULL, '0.00'),
(7, 7, 'EMPANADA DE CARNE', NULL, '0.00');

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

--
-- Volcado de datos para la tabla `productos_venta`
--

INSERT INTO `productos_venta` (`PV_COD`, `MERCADERIA_ID`, `RUBRO_ID`, `UNIMEDIDA_ID`, `PV_NOMBRE`, `PV_DESCRIPCION`, `PV_IMAGENES`, `PV_PRECIO_VENTA`, `PV_FECHA_LAST_UPDATE_PRICE`, `PV_FECHA_BAJA`) VALUES
(1, 1, 2, 6, NULL, '', '', '80.00', NULL, NULL),
(2, 5, 1, 6, NULL, '', '', '100.00', NULL, NULL),
(3, 6, 3, 6, NULL, '', '', '150.00', NULL, NULL),
(4, 7, 4, 5, NULL, '', '', '550.99', NULL, NULL),
(5, 9, 2, 6, NULL, '', '', '100.00', NULL, NULL),
(6, 8, 2, 6, NULL, '', '', '65.00', NULL, NULL),
(7, 10, 1, 6, NULL, '', '', '95.00', NULL, NULL),
(8, 12, 12, 2, NULL, '', '', '300.00', NULL, NULL),
(9, 37, 9, 3, NULL, '', '', '210.00', NULL, NULL);

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
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`PROVEEDOR_ID`, `RAZONSOCIAL`, `CUIT`, `MAIL`, `TELEFONO`, `WEB`, `DIRECCION`, `LOGO_URL`, `FECHA_ALTA`, `FECHA_BAJA`) VALUES
(00000000001, 'PEPITO GRILLO', '21474836471', NULL, NULL, NULL, 'DIRECCION XD', NULL, '0000-00-00 00:00:00', NULL),
(00000000002, 'BOGEDA CHOBORRA', '21474836472', NULL, NULL, NULL, 'DASDA54 893 ASDQWE', NULL, '0000-00-00 00:00:00', NULL),
(00000000003, 'CAPERUSA', '21474836473', NULL, NULL, NULL, 'WEALANFIA 123', NULL, '0000-00-00 00:00:00', NULL),
(00000000004, 'COCA COLA COMPANY', '78963258417', NULL, NULL, NULL, 'COCA COLA LANDIA', NULL, '0000-00-00 00:00:00', NULL),
(00000000005, 'VAMOS MANAOS', '45326549879', NULL, NULL, NULL, 'POR AHI NOMAS', NULL, '0000-00-00 00:00:00', NULL),
(00000000006, 'WEAS INC', '23651234786', 'weasinc@gmail.com', NULL, NULL, 'CERCA DE LAS WEAS', NULL, '0000-00-00 00:00:00', NULL),
(00000000007, 'LOTR', '55123695824', NULL, NULL, NULL, 'LAS MINAS DE MORIA', NULL, '0000-00-00 00:00:00', NULL),
(00000000008, 'BAJA BODEGA', '21474836474', NULL, NULL, NULL, 'ZONA BAJA', NULL, '0000-00-00 00:00:00', NULL),
(00000000009, 'ARCOR', '25123547886', NULL, NULL, NULL, 'QUIEN TE CONOCE', NULL, '0000-00-00 00:00:00', NULL),
(00000000010, 'CACAHUATE EXPRESS', '24857981891', NULL, NULL, NULL, 'ALLÁ LEJOS', NULL, '0000-00-00 00:00:00', NULL),
(00000000014, 'ALOHA', '2147483647', 'puto@elque.lolea', NULL, 'https://www.asd.com', 'ME CHUPA LA PIJA 123', NULL, '2020-03-28 22:41:34', NULL),
(00000000015, 'YOSUKO DELIVERY', '12345678913', 'yd1@empresa.com', '2364673831', 'https://www.softboos.wea', 'POSADAS VILLA CABELLO CALLE 325-8 1234 0', NULL, '2020-03-30 22:24:45', NULL),
(00000000016, 'YOSUKO DELIVERY II', '44444444444', 'yd2@yahoo.com.ar', '4758841256', NULL, 'LEJOS DONDE CAGO EL CONEJO', NULL, '2020-04-03 20:08:03', NULL),
(00000000017, 'MUNDO DULCE', '25478985751', 'm.dulce@empresa.com', '3765897654', NULL, 'DULCELANDIA 7894', NULL, '2020-10-31 15:07:17', NULL),
(00000000018, 'EMPRESA PRUEBA', '71478985235', 'empresa@prueba.1', NULL, NULL, NULL, NULL, '2021-02-15 21:30:54', NULL);

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
-- Volcado de datos para la tabla `recetas`
--

INSERT INTO `recetas` (`RECETA_ID`, `MERCADERIA_ID`, `NOMBRE`, `FECHA_ALTA`, `FECHA_BAJA`) VALUES
(1, 11, 'PREPIZZA', '2020-01-19 00:00:00', NULL),
(2, 7, 'EMPANADA DE CARNE', '2020-01-19 00:00:00', NULL),
(3, 30, 'PANCHO VILLA', '2020-03-11 01:07:08', NULL),
(4, 31, 'PAPAS PAI', '2020-03-12 16:23:20', NULL),
(5, 29, 'BAGGIO RECETA', '2020-03-19 20:34:40', NULL),
(6, 36, 'PAN CON MANTECA', '2020-03-19 21:55:07', NULL),
(7, 38, 'SANDWICH DE FIAMBRE', '2020-10-31 21:01:57', NULL),
(8, 32, 'CACAHUATE', '2020-10-31 21:02:53', NULL);

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
-- Volcado de datos para la tabla `recetas_elaboradas`
--

INSERT INTO `recetas_elaboradas` (`RECETA_ID`, `EMPLEADO_ID`, `UNIMEDIDA_ID`, `RE_CANTIDAD`, `RE_CANTIDAD_REAL`, `RE_FECHA`) VALUES
(1, 4, 6, '6.000', '6.000', '1000-11-15 00:00:00'),
(1, 8, 6, '1.000', '1.000', '2020-03-19 23:23:40'),
(2, 8, 5, '1.000', '12.000', '2019-12-04 22:01:59'),
(2, 8, 5, '1.000', '12.000', '2019-12-04 22:04:01'),
(2, 8, 5, '1.000', '12.000', '2019-12-04 23:13:37'),
(2, 8, 5, '1.000', '12.000', '2019-12-04 23:28:31'),
(2, 8, 5, '1.000', '12.000', '2020-03-19 23:22:47'),
(2, 8, 5, '1.000', '12.000', '2020-03-19 23:23:18'),
(2, 8, 5, '1.000', '12.000', '2020-03-19 23:24:54'),
(2, 8, 6, '1.000', '1.000', '2019-12-04 23:28:56'),
(2, 8, 6, '1.000', '1.000', '2019-12-04 23:34:30'),
(2, 8, 6, '1.000', '1.000', '2019-12-04 23:35:29'),
(2, 8, 6, '1.000', '1.000', '2019-12-04 23:57:03'),
(2, 9, 6, '2.000', '2.000', '2020-02-13 00:00:00'),
(4, 8, 7, '100.000', '100.000', '2020-03-20 00:05:08'),
(4, 8, 7, '100.000', '100.000', '2020-03-20 00:09:20'),
(4, 8, 7, '350.000', '350.000', '2020-03-20 00:28:38'),
(4, 8, 7, '350.000', '350.000', '2020-03-20 00:37:51'),
(4, 8, 7, '50.000', '50.000', '2020-03-21 15:09:14'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 15:54:11'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 15:55:19'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 16:07:55'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 16:10:01'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 16:13:59'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 16:16:12'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 16:22:42'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 16:37:21'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 16:37:59'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 16:44:16'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 18:24:23'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 19:27:15'),
(4, 8, 7, '1.000', '1.000', '2020-03-21 19:29:20'),
(4, 8, 7, '10.000', '10.000', '2020-03-21 19:31:40'),
(4, 8, 7, '10.000', '10.000', '2020-03-21 19:32:11'),
(4, 9, 7, '350.000', '350.000', '2020-03-20 00:38:40'),
(4, 9, 7, '350.000', '350.000', '2020-03-20 00:46:04'),
(4, 9, 7, '50.000', '50.000', '2020-03-20 00:59:29'),
(5, 8, 6, '2.000', '2.000', '2020-03-19 22:03:15'),
(5, 8, 6, '1.000', '1.000', '2020-03-19 23:23:31'),
(5, 8, 6, '1.000', '1.000', '2020-03-19 23:25:16'),
(5, 8, 6, '1.000', '1.000', '2020-03-19 23:31:52'),
(5, 8, 6, '1.000', '1.000', '2020-03-19 23:33:30'),
(5, 8, 6, '1.000', '1.000', '2020-03-19 23:37:43'),
(5, 8, 6, '1.000', '1.000', '2020-03-19 23:38:14'),
(5, 8, 6, '1.000', '1.000', '2020-03-19 23:46:42'),
(5, 8, 6, '1.000', '1.000', '2020-03-19 23:46:58'),
(5, 8, 6, '1.000', '1.000', '2020-03-20 00:15:26'),
(6, 8, 6, '2.000', '2.000', '2020-03-19 23:48:28'),
(6, 8, 6, '1.000', '1.000', '2020-03-19 23:50:06'),
(7, 8, 6, '5.000', '5.000', '2020-10-31 21:18:29'),
(7, 8, 6, '1.000', '1.000', '2020-10-31 21:25:11'),
(8, 8, 6, '1.000', '1.000', '2021-02-16 14:13:52');

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
-- Volcado de datos para la tabla `rubros`
--

INSERT INTO `rubros` (`RUBRO_ID`, `ESTADO_ID`, `NOMBRE`, `FECHA_ALTA`, `IMAGEN_URL`) VALUES
(1, 1, 'VIDEO GAMES', '2019-11-14 18:02:23', 'images/uploads/1622736359-31262.jpg'),
(2, 1, 'GASEOSA', '2019-11-14 18:02:23', 'images/uploads/album-icon.jpg'),
(3, 1, 'PIZZA', '2019-11-14 18:02:23', 'images/uploads/album-icon.jpg'),
(4, 1, 'EMPANADA', '2019-11-14 18:02:23', 'images/uploads/album-icon.jpg'),
(5, 1, 'VINO', '2019-11-14 18:02:23', 'images/uploads/album-icon.jpg'),
(6, 1, 'FERNET', '2019-11-14 18:02:23', 'images/uploads/album-icon.jpg'),
(7, 1, 'JUGO', '2019-11-14 18:02:23', 'images/uploads/album-icon.jpg'),
(8, 1, 'AGUITA MINERAL', '2019-11-14 18:02:23', 'images/uploads/album-icon.jpg'),
(9, 1, 'INSUMO', '2019-11-14 18:02:23', 'images/uploads/album-icon.jpg'),
(10, 1, 'WEAS', '2019-11-14 18:12:54', 'images/uploads/album-icon.jpg'),
(11, 1, 'CACAHUATE', '2020-03-19 20:42:57', 'images/uploads/album-icon.jpg'),
(12, 1, 'COMIDA', '2020-03-19 21:32:27', 'images/uploads/album-icon.jpg'),
(13, 2, 'RUBRO PRUEBA', '2021-02-15 22:43:59', 'images/uploads/album-icon.jpg'),
(14, 1, 'SALCHI', '2021-05-31 23:09:22', 'images/uploads/1622513466-salchi.jpeg'),
(15, 1, 'CONSOLAS', '2021-06-03 12:42:04', 'images/uploads/1622745456-1366_2000.jpeg'),
(16, 1, 'SAPITOS', '2021-06-07 00:53:14', 'images/uploads/album-icon.jpg'),
(17, 1, 'COMBO', '2021-06-26 20:07:23', 'images/uploads/album-icon.jpg');

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
  `VALOR` decimal(8,3) UNSIGNED NOT NULL DEFAULT '0.000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `unidades_medida`
--

INSERT INTO `unidades_medida` (`UNIMEDIDA_ID`, `UNIDADMINIMA_ID`, `ESTADO_ID`, `NOMBRE`, `ABREVIATURA`, `VALOR`) VALUES
(1, 8, 1, 'LITRO', 'L', '1000.000'),
(2, 3, 1, 'KILO', 'K', '1000.000'),
(3, 3, 1, 'KILOGRAMO', 'KG', '1.000'),
(4, 6, 1, 'CAJON X12', 'CJ', '12.000'),
(5, 6, 1, 'DOCENA', 'DC', '12.000'),
(6, 6, 1, 'UNIDAD', 'UND', '1.000'),
(7, 7, 1, 'GRAMO', 'GR', '1.000'),
(8, 8, 1, 'MILILITRO', 'ML', '1.000'),
(9, 7, 1, 'CUCHARA', 'CH', '100.000'),
(10, 7, 1, 'CUCHARITA', 'CHT', '50.000'),
(11, 6, 1, 'CAJON X8', 'CJ', '8.000'),
(12, 6, 1, 'CAJON X4', 'CJ', '4.000'),
(13, 6, 1, 'CAJON X6', 'CJ', '6.000'),
(14, 8, 1, 'VASO', 'VA', '250.000'),
(17, 17, 1, 'CENTIMETRO', 'CM', '1.000'),
(18, 17, 1, 'METRO', 'MT', '100.000'),
(19, 19, 1, 'UNIDAD1', 'UM1', '1.000'),
(20, 19, 1, 'TEST6', 'T6', '41.000'),
(21, 20, 1, 'TEST2', 'T2', '65.000'),
(22, 1, 1, 'TEST2', 'ASD', '5.000'),
(23, 7, 1, 'LIBRA', 'LB', '453.592'),
(24, 24, 1, 'PULGADA', 'PLG', '1.000'),
(25, 25, 1, 'PALITO', 'PAL', '1.000'),
(26, 7, 2, 'ONZA', 'OZ', '28.700'),
(27, NULL, 1, 'BASE TEST', 'BT', '1.000'),
(28, NULL, 1, 'BASE TEST', 'BT', '1.000'),
(29, NULL, 1, 'BASE TEST', 'BT', '1.000'),
(30, NULL, 1, 'BASE TEST', 'BT', '1.000'),
(31, NULL, 1, 'BASE TEST', 'BT', '1.000'),
(32, NULL, 1, 'BASE TEST', 'BT', '1.000'),
(33, 33, 1, 'BASE TEST', 'BT', '1.000'),
(34, 34, 1, 'BASE TEST', 'BT', '1.000');

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
  ADD KEY `ix_MAIL` (`MAIL`) USING BTREE;

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
  MODIFY `CARGO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `cargos_auditoria`
--
ALTER TABLE `cargos_auditoria`
  MODIFY `CARGO_AUDITORIA_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `CLIENTE_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000000056;
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
  MODIFY `MODULO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
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
  MODIFY `PERMISO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
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
