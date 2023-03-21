<?php 
class DashboardModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function selectProductosVentaCantidad(){
		$sql = "SELECT COUNT(MERCADERIA_ID) AS cantidad
		FROM mercaderias
		WHERE ESTADO_ID <> 3";
		$request = $this->select($sql);
		return $request;
	}
	public function selectProductoMasPedido(){
		$sql = "SELECT m.MERCADERIA_ID, m.NOMBRE, MAX(compare.cantidad)
		FROM mercaderias AS m
		INNER JOIN (SELECT dpv.MERCADERIA_ID AS id, SUM(dpv.CANTIDAD) AS cantidad
		FROM detalle_pedidos_venta AS dpv
		INNER JOIN facturas_venta AS fv ON fv.FACTURAVENTA_ID = dpv.FACTURAVENTA_ID
		WHERE fv.ESTADO_ID <> 2
		GROUP BY dpv.MERCADERIA_ID) AS compare ON compare.id = m.MERCADERIA_ID
		WHERE m.ESTADO_ID <> 3";
		$request = $this->select($sql);
		return $request;
	}
	public function selectIngresosDelDia(){
		$sql = "SELECT IFNULL(SUM(fv.TOTAL),0) AS total
		FROM facturas_venta AS fv
		WHERE fv.ESTADO_ID = 3 AND DATE(fv.FECHA_EMISION) = DATE(NOW())";
		$request = $this->select($sql);
		return $request;
	}
	public function selectTotalVentas(){
		$sql = "SELECT IFNULL(SUM(dpv.CANTIDAD),0) AS total_ventas
		FROM detalle_pedidos_venta AS dpv
		INNER JOIN facturas_venta AS fv ON fv.FACTURAVENTA_ID = dpv.FACTURAVENTA_ID
		WHERE fv.ESTADO_ID = 3";
		$request = $this->select($sql);
		return $request;
	}
}
?>