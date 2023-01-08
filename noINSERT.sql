-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-08-2021 a las 02:34:27
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

DROP TABLE IF EXISTS `estado`;
CREATE TABLE `estado` (
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `DESCRIPCION` varchar(10) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_pedido`
--

DROP TABLE IF EXISTS `estado_pedido`;
CREATE TABLE `estado_pedido` (
  `ESTADO_ID` int(11) UNSIGNED NOT NULL,
  `DESCRIPCION` varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT;

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
  `CANTIDAD_PAGO` tinyint(2) UNSIGNED DEFAULT '1'
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forma_pago`
--

DROP TABLE IF EXISTS `forma_pago`;
CREATE TABLE `forma_pago` (
  `FORMAPAGO_ID` int(11) UNSIGNED NOT NULL,
  `FORMA_PAGO` varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
