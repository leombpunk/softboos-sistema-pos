<?php 
class InformesModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function selectMovimientos(){
		$sql = "SELECT mc.ID AS id, mc.DESCRIPCION AS descripcion, mt.DESCRIPCION AS tipo, mc.FECHA_ALTA AS alta, mc.MONTO AS monto, CONCAT(e.NOMBRE,' ',e.APELLIDO) AS empleado 
		FROM movimientos_caja AS mc
		INNER JOIN empleados AS e ON mc.EMPLEADO_ID = e.EMPLEADO_ID
        INNER JOIN movimiento_tipo AS mt ON mc.TIPO_ID = mt.MOVIMIENTOTIPO_ID
		WHERE mc.ESTADO_ID <> 3";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectMovimiento(int $id){
		$sql = "SELECT * FROM movimientos_caja WHERE ID = {$id}";
		$request = $this->select($sql);
		return $request;
	}
	public function selectInformeDelDia(string $fecha){
		$sql = "SELECT m.MERCADERIA_ID AS id, m.NOMBRE AS descripcion, dpv.UNIMEDIDA_ID, um.NOMBRE, SUM(dpv.CANTIDAD) AS cantidad, dpv.PRECIO, 
		SUM(dpv.CANTIDAD*dpv.PRECIO) AS monto, fv.FORMAPAGO_ID, fp.FORMA_PAGO, fv.FECHA_EMISION AS fecha, 'INGRESO' AS movimiento
		FROM detalle_pedidos_venta AS dpv
		INNER JOIN facturas_venta AS fv ON dpv.FACTURAVENTA_ID = fv.FACTURAVENTA_ID
			AND DATE(fv.FECHA_EMISION) = DATE('{$fecha}') AND fv.ESTADO_ID = 3
		INNER JOIN forma_pago AS fp ON fv.FORMAPAGO_ID = fp.FORMAPAGO_ID
		INNER JOIN unidades_medida AS um ON dpv.UNIMEDIDA_ID = um.UNIMEDIDA_ID
		RIGHT JOIN mercaderias AS m ON dpv.MERCADERIA_ID = m.MERCADERIA_ID
			WHERE m.PARA_VENTA = 1
		GROUP BY m.MERCADERIA_ID ASC, fv.FORMAPAGO_ID ASC
		UNION
		SELECT dpc.MERCADERIA_ID AS id, m.NOMBRE AS descripcion, dpc.UNIMEDIDA_ID, um.NOMBRE, SUM(dpc.CANTIDAD) AS cantidad, dpc.PRECIO, 
			CONCAT('-',SUM(dpc.CANTIDAD*dpc.PRECIO)) AS monto, fc.FORMAPAGO_ID, fp.FORMA_PAGO, fc.FECHA_EMISION AS fecha, 'EGRESO' AS movimiento
			FROM detalle_pedidos_compra AS dpc
			INNER JOIN facturas_compra AS fc ON fc.FACTURACOMPRA_ID = dpc.FACTURACOMPRA_ID
			INNER JOIN forma_pago AS fp ON fp.FORMAPAGO_ID = fc.FORMAPAGO_ID
			INNER JOIN unidades_medida AS um ON um.UNIMEDIDA_ID = dpc.UNIMEDIDA_ID
			INNER JOIN mercaderias AS m ON m.MERCADERIA_ID = dpc.MERCADERIA_ID
			WHERE DATE(fc.FECHA_EMISION) = DATE('{$fecha}') AND fc.ESTADO_ID = 3
			GROUP BY dpc.MERCADERIA_ID ASC, fc.FORMAPAGO_ID ASC
		UNION
		SELECT mc.ID, mc.DESCRIPCION, '', '', '', '', IF(mt.TM_TIPO = 1,mc.MONTO,CONCAT('-',mc.MONTO)) AS monto, mc.TIPO_ID, '', mc.FECHA_ALTA, 
			mt.DESCRIPCION
			FROM movimientos_caja AS mc
			INNER JOIN movimiento_tipo AS mt ON mt.MOVIMIENTOTIPO_ID = mc.TIPO_ID
			WHERE DATE(mc.FECHA_ALTA) = DATE('{$fecha}')
				AND mc.ESTADO_ID = 1
		ORDER BY movimiento ASC, fecha DESC, descripcion ASC";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectInformeDelMes(string $fecha){
		$sql = "SELECT m.MERCADERIA_ID AS id, m.NOMBRE AS descripcion, dpv.UNIMEDIDA_ID, um.NOMBRE, SUM(dpv.CANTIDAD) AS cantidad, dpv.PRECIO, 
		SUM(dpv.CANTIDAD*dpv.PRECIO) AS monto, fv.FORMAPAGO_ID, fp.FORMA_PAGO, fv.FECHA_EMISION AS fecha, 'INGRESO' AS movimiento
		FROM detalle_pedidos_venta AS dpv
		INNER JOIN facturas_venta AS fv ON dpv.FACTURAVENTA_ID = fv.FACTURAVENTA_ID
			AND MONTH(fv.FECHA_EMISION) = MONTH('{$fecha}') AND YEAR('{$fecha}') = YEAR(NOW()) AND fv.ESTADO_ID = 3
		INNER JOIN forma_pago AS fp ON fv.FORMAPAGO_ID = fp.FORMAPAGO_ID
		INNER JOIN unidades_medida AS um ON dpv.UNIMEDIDA_ID = um.UNIMEDIDA_ID
		RIGHT JOIN mercaderias AS m ON dpv.MERCADERIA_ID = m.MERCADERIA_ID
			WHERE m.PARA_VENTA = 1
		GROUP BY m.MERCADERIA_ID ASC, fv.FORMAPAGO_ID ASC
		UNION
		SELECT dpc.MERCADERIA_ID AS id, m.NOMBRE AS descripcion, dpc.UNIMEDIDA_ID, um.NOMBRE, SUM(dpc.CANTIDAD) AS cantidad, dpc.PRECIO, 
			CONCAT('-',SUM(dpc.CANTIDAD*dpc.PRECIO)) AS monto, fc.FORMAPAGO_ID, fp.FORMA_PAGO, fc.FECHA_EMISION AS fecha, 'EGRESO' AS movimiento
			FROM detalle_pedidos_compra AS dpc
			INNER JOIN facturas_compra AS fc ON fc.FACTURACOMPRA_ID = dpc.FACTURACOMPRA_ID
			INNER JOIN forma_pago AS fp ON fp.FORMAPAGO_ID = fc.FORMAPAGO_ID
			INNER JOIN unidades_medida AS um ON um.UNIMEDIDA_ID = dpc.UNIMEDIDA_ID
			INNER JOIN mercaderias AS m ON m.MERCADERIA_ID = dpc.MERCADERIA_ID
			WHERE MONTH(fc.FECHA_EMISION) = MONTH('{$fecha}') AND YEAR('{$fecha}') = YEAR(NOW()) AND fc.ESTADO_ID = 3
			GROUP BY dpc.MERCADERIA_ID ASC, fc.FORMAPAGO_ID ASC
		UNION
		SELECT mc.ID, IF(mt.TM_TIPO = 1,'INGRESO DE DINERO','GASTOS VARIOS'), '', '', '', '', IF(mt.TM_TIPO = 1,mc.MONTO,CONCAT('-',mc.MONTO)) AS monto, mc.TIPO_ID, '', mc.FECHA_ALTA, 
			mt.DESCRIPCION
			FROM movimientos_caja AS mc
			INNER JOIN movimiento_tipo AS mt ON mt.MOVIMIENTOTIPO_ID = mc.TIPO_ID
			WHERE MONTH(mc.FECHA_ALTA) = MONTH('{$fecha}') AND YEAR('{$fecha}') = YEAR(NOW())
				AND mc.ESTADO_ID = 1 AND NOT mc.TIPO_ID = 3
			GROUP BY mc.TIPO_ID ASC
		ORDER BY movimiento ASC, fecha DESC, descripcion ASC";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectInformeDelAnho(string $fecha){
		$sql = "SELECT m.MERCADERIA_ID AS id, m.NOMBRE AS descripcion, dpv.UNIMEDIDA_ID, um.NOMBRE, SUM(dpv.CANTIDAD) AS cantidad, dpv.PRECIO, 
		SUM(dpv.CANTIDAD*dpv.PRECIO) AS monto, fv.FORMAPAGO_ID, fp.FORMA_PAGO, fv.FECHA_EMISION AS fecha, 'INGRESO' AS movimiento
		FROM detalle_pedidos_venta AS dpv
		INNER JOIN facturas_venta AS fv ON dpv.FACTURAVENTA_ID = fv.FACTURAVENTA_ID
			AND YEAR(fv.FECHA_EMISION) = YEAR('{$fecha}') AND fv.ESTADO_ID = 3
		INNER JOIN forma_pago AS fp ON fv.FORMAPAGO_ID = fp.FORMAPAGO_ID
		INNER JOIN unidades_medida AS um ON dpv.UNIMEDIDA_ID = um.UNIMEDIDA_ID
		RIGHT JOIN mercaderias AS m ON dpv.MERCADERIA_ID = m.MERCADERIA_ID
			WHERE m.PARA_VENTA = 1
		GROUP BY m.MERCADERIA_ID ASC, fv.FORMAPAGO_ID ASC
		UNION
		SELECT dpc.MERCADERIA_ID AS id, m.NOMBRE AS descripcion, dpc.UNIMEDIDA_ID, um.NOMBRE, SUM(dpc.CANTIDAD) AS cantidad, dpc.PRECIO, 
			CONCAT('-',SUM(dpc.CANTIDAD*dpc.PRECIO)) AS monto, fc.FORMAPAGO_ID, fp.FORMA_PAGO, fc.FECHA_EMISION AS fecha, 'EGRESO' AS movimiento
			FROM detalle_pedidos_compra AS dpc
			INNER JOIN facturas_compra AS fc ON fc.FACTURACOMPRA_ID = dpc.FACTURACOMPRA_ID
			INNER JOIN forma_pago AS fp ON fp.FORMAPAGO_ID = fc.FORMAPAGO_ID
			INNER JOIN unidades_medida AS um ON um.UNIMEDIDA_ID = dpc.UNIMEDIDA_ID
			INNER JOIN mercaderias AS m ON m.MERCADERIA_ID = dpc.MERCADERIA_ID
			WHERE YEAR(fc.FECHA_EMISION) = YEAR('{$fecha}') AND fc.ESTADO_ID = 3
			GROUP BY dpc.MERCADERIA_ID ASC, fc.FORMAPAGO_ID ASC
		UNION
		SELECT mc.ID, IF(mt.TM_TIPO = 1,'INGRESO DE DINERO','GASTOS VARIOS'), '', '', '', '', IF(mt.TM_TIPO = 1,mc.MONTO,CONCAT('-',mc.MONTO)) AS monto, mc.TIPO_ID, '', mc.FECHA_ALTA, 
			mt.DESCRIPCION
			FROM movimientos_caja AS mc
			INNER JOIN movimiento_tipo AS mt ON mt.MOVIMIENTOTIPO_ID = mc.TIPO_ID
			WHERE YEAR(mc.FECHA_ALTA) = YEAR('{$fecha}')
				AND mc.ESTADO_ID = 1 AND NOT mc.TIPO_ID = 3
			GROUP BY mc.TIPO_ID ASC
		ORDER BY movimiento ASC, fecha DESC, descripcion ASC";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectInformeDelaSemana(string $fecha){
		$sql = "SELECT m.MERCADERIA_ID AS id, m.NOMBRE AS descripcion, dpv.UNIMEDIDA_ID, um.NOMBRE, SUM(dpv.CANTIDAD) AS cantidad, dpv.PRECIO, 
		SUM(dpv.CANTIDAD*dpv.PRECIO) AS monto, fv.FORMAPAGO_ID, fp.FORMA_PAGO, fv.FECHA_EMISION AS fecha, 'INGRESO' AS movimiento
		FROM detalle_pedidos_venta AS dpv
		INNER JOIN facturas_venta AS fv ON dpv.FACTURAVENTA_ID = fv.FACTURAVENTA_ID
			AND WEEKOFYEAR(fv.FECHA_EMISION) = WEEKOFYEAR('{$fecha}') AND YEAR('{$fecha}') = YEAR(NOW()) AND fv.ESTADO_ID = 3
		INNER JOIN forma_pago AS fp ON fv.FORMAPAGO_ID = fp.FORMAPAGO_ID
		INNER JOIN unidades_medida AS um ON dpv.UNIMEDIDA_ID = um.UNIMEDIDA_ID
		RIGHT JOIN mercaderias AS m ON dpv.MERCADERIA_ID = m.MERCADERIA_ID
			WHERE m.PARA_VENTA = 1
		GROUP BY m.MERCADERIA_ID ASC, fv.FORMAPAGO_ID ASC
		UNION
		SELECT dpc.MERCADERIA_ID AS id, m.NOMBRE AS descripcion, dpc.UNIMEDIDA_ID, um.NOMBRE, SUM(dpc.CANTIDAD) AS cantidad, dpc.PRECIO, 
			CONCAT('-',SUM(dpc.CANTIDAD*dpc.PRECIO)) AS monto, fc.FORMAPAGO_ID, fp.FORMA_PAGO, fc.FECHA_EMISION AS fecha, 'EGRESO' AS movimiento
			FROM detalle_pedidos_compra AS dpc
			INNER JOIN facturas_compra AS fc ON fc.FACTURACOMPRA_ID = dpc.FACTURACOMPRA_ID
			INNER JOIN forma_pago AS fp ON fp.FORMAPAGO_ID = fc.FORMAPAGO_ID
			INNER JOIN unidades_medida AS um ON um.UNIMEDIDA_ID = dpc.UNIMEDIDA_ID
			INNER JOIN mercaderias AS m ON m.MERCADERIA_ID = dpc.MERCADERIA_ID
			WHERE WEEKOFYEAR(fc.FECHA_EMISION) = WEEKOFYEAR('{$fecha}') AND YEAR('{$fecha}') = YEAR(NOW()) AND fc.ESTADO_ID = 3
			GROUP BY dpc.MERCADERIA_ID ASC, fc.FORMAPAGO_ID ASC
		UNION
		SELECT mc.ID, IF(mt.TM_TIPO = 1,'INGRESO DE DINERO','GASTOS VARIOS'), '', '', '', '', IF(mt.TM_TIPO = 1,mc.MONTO,CONCAT('-',mc.MONTO)) AS monto, mc.TIPO_ID, '', mc.FECHA_ALTA, 
			mt.DESCRIPCION
			FROM movimientos_caja AS mc
			INNER JOIN movimiento_tipo AS mt ON mt.MOVIMIENTOTIPO_ID = mc.TIPO_ID
			WHERE WEEKOFYEAR(mc.FECHA_ALTA) = WEEKOFYEAR('{$fecha}') AND YEAR('{$fecha}') = YEAR(NOW())
				AND mc.ESTADO_ID = 1 AND NOT mc.TIPO_ID = 3
			GROUP BY mc.TIPO_ID ASC
		ORDER BY movimiento ASC, fecha DESC, descripcion ASC";
		$request = $this->select_all($sql);
		return $request;
	}
}
?>