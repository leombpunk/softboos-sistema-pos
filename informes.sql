--Informes SQL
--Ventas del dia total ganancia (total sumatoria de todas las ventas del dia y su monto)
SELECT `fv`.*, `ff`.*, `fp`.`FORMA_PAGO`, `total_ventas`.`cantidad` 
FROM `facturas_venta` AS `fv`
INNER JOIN `facturaventa_formapago` AS `ff` ON `ff`.`FACTURA_ID` = `fv`.`FACTURAVENTA_ID`
INNER JOIN `forma_pago` AS `fp` ON `fp`.`FORMAPAGO_ID` = `ff`.`FORMAPAGO_ID`
INNER JOIN (
    SELECT `dpv`.`FACTURAVENTA_ID` AS `id`, sum(`CANTIDAD`) AS `cantidad`
    FROM `detalle_pedidos_venta` AS `dpv`
    INNER JOIN `facturas_venta` AS `fv2` ON `dpv`.`FACTURAVENTA_ID` = `fv2`.`FACTURAVENTA_ID`
    GROUP BY `dpv`.`FACTURAVENTA_ID`
) AS `total_ventas` ON `total_ventas`.`id` = `fv`.`FACTURAVENTA_ID`
WHERE `fv`.`FECHA_EMISION` = NOW() AND `fv`.`ESTADO_ID` = 3
GROUP BY `ff`.`FORMAPAGO_ID` ASC;
--refactorizando
--esta version es mejor
--modificar el where de fechas segun corresponda
SELECT `ff`.`FORMAPAGO_ID`, `fp`.`FORMA_PAGO`, `fv`.`TOTAL`, sum(`dpv`.`CANTIDAD`) AS `cantidad`
FROM `facturas_venta` AS `fv`
INNER JOIN `facturaventa_formapago` AS `ff` ON `ff`.`FACTURA_ID` = `fv`.`FACTURAVENTA_ID`
INNER JOIN `forma_pago` AS `fp` ON `fp`.`FORMAPAGO_ID` = `ff`.`FORMAPAGO_ID`
INNER JOIN `detalle_pedidos_venta` AS `dpv` ON `dpv`.`FACTURAVENTA_ID` = `fv`.`FACTURAVENTA_ID`
WHERE `fv`.`FECHA_EMISION` = NOW() AND `fv`.`ESTADO_ID` = 3
GROUP BY `ff`.`FORMAPAGO_ID` ASC;

--Ventas del dia total cantidad agrupado por producto
SELECT `dpv`.`MERCADERIA_ID`, `mer`.`CODIGO`, `mer`.`NOMBRE`, sum(`dpv`.`CANTIDAD`) AS `cantidad`
FROM `detalle_pedidos_venta` AS `dpv`
INNER JOIN `mercaderias` AS `mer` ON `dpv`.`MERCADERIA_ID` = `mer`.`MERCADERIA_ID`
INNER JOIN `facturas_venta` AS `fv` ON `dpv`.`FACTURAVENTA_ID` = `fv`.`FACTURAVENTA_ID`
WHERE `fv`.`FECHA_EMISION` = NOW() AND `fv`.`ESTADO_ID` = 3 
GROUP BY `dpv`.`MERCADERIA_ID` ASC;

--Ventas del dia total ganancia agrupado por producto
SELECT `dpv`.`MERCADERIA_ID`, `mer`.`CODIGO`, `mer`.`NOMBRE`, sum(`dpv`.`CANTIDAD`*`dpv`.`PRECIO`) AS `ingresos`, 
`ff`.`FORMAPAGO_ID`, `fp`.`FORMA_PAGO`
FROM `detalle_pedidos_venta` AS `dpv`
INNER JOIN `mercaderias` AS `mer` ON `dpv`.`MERCADERIA_ID` = `mer`.`MERCADERIA_ID`
INNER JOIN `facturas_venta` AS `fv` ON `dpv`.`FACTURAVENTA_ID` = `fv`.`FACTURAVENTA_ID`
INNER JOIN `facturaventa_formapago` AS `ff` ON `ff`.`FACTURA_ID` = `fv`.`FACTURAVENTA_ID`
INNER JOIN `forma_pago` AS `fp` ON `fp`.`FORMAPAGO_ID` = `ff`.`FORMAPAGO_ID`
WHERE `fv`.`FECHA_EMISION` = NOW() AND `fv`.`ESTADO_ID` = 3 
GROUP BY `dpv`.`MERCADERIA_ID` ASC, `ff`.`FORMAPAGO_ID` ASC;
--Ventas de la semana total ganancia (total sumatoria de todas las ventas del dia y su monto)

--Ventas de la semana total cantidad agrupado por producto

--Ventas de la semana total ganancia agrupado por producto

--Ventas del mes total ganancia (total sumatoria de todas las ventas del dia y su monto)

--Ventas del mes total cantidad agrupado por producto

--Ventas del mes total ganancia agrupado por producto

--Ventas (rango de fechas) total ganancia (total sumatoria de todas las ventas del dia y su monto)

--Ventas (rango de fechas) total cantidad agrupado por producto

--Ventas (rango de fechas) total ganancia agrupado por producto

--Dividir por forma de pago (efectivo, credito, debito)

--Que hago con el tipo de factura, factura A B ect.?

--Movimientos del dia agrupados por horas (cantidad de ventas)