<?php 
class InformesModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	//traer todo o solo lo del día, esa es la cuestión
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
	public function selectInformeDelDia(){
		$sql = "SELECT dpv.MERCADERIA_ID AS id, m.NOMBRE AS descripcion, dpv.UNIMEDIDA_ID, um.NOMBRE, SUM(dpv.CANTIDAD) AS cantidad, dpv.PRECIO, SUM(dpv.CANTIDAD*dpv.PRECIO) AS monto, ff.FORMAPAGO_ID, fp.FORMA_PAGO 
		FROM detalle_pedidos_venta AS dpv
		INNER JOIN facturas_venta AS fv ON fv.FACTURAVENTA_ID = dpv.FACTURAVENTA_ID
		INNER JOIN facturaventa_formapago AS ff ON ff.FACTURA_ID = fv.FACTURAVENTA_ID
		INNER JOIN forma_pago AS fp ON fp.FORMAPAGO_ID = ff.FORMAPAGO_ID
		INNER JOIN unidades_medida AS um ON um.UNIMEDIDA_ID = dpv.UNIMEDIDA_ID
		LEFT JOIN mercaderias AS m ON m.MERCADERIA_ID = dpv.MERCADERIA_ID
			AND m.PARA_VENTA = 1
		WHERE DATE(fv.FECHA_EMISION) = DATE(NOW()) AND fv.ESTADO_ID = 3
		GROUP BY dpv.MERCADERIA_ID ASC, ff.FORMAPAGO_ID ASC
		UNION
		SELECT mc.ID, mc.DESCRIPCION, '', '', '', '', IF(mt.TM_TIPO = 1,mc.MONTO,CONCAT('-',mc.MONTO)) AS monto, mc.TIPO_ID, mt.DESCRIPCION 
		FROM movimientos_caja AS mc
		INNER JOIN movimiento_tipo AS mt ON mt.MOVIMIENTOTIPO_ID = mc.TIPO_ID
		WHERE DATE(mc.FECHA_ALTA) = DATE(NOW())
			AND mc.ESTADO_ID = 1";
		$request = $this->select_all($sql);
		return $request;
	}
}
?>