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
	public function selectVentasPorProductoGrafico(){
		$sql = "SELECT m.MERCADERIA_ID, m.NOMBRE, SUM(IFNULL(dpv.CANTIDAD,0)) AS cantidades, IFNULL(SUM(dpv.CANTIDAD*dpv.PRECIO),0) AS totales
		FROM detalle_pedidos_venta AS dpv
		INNER JOIN facturas_venta AS fv ON fv.FACTURAVENTA_ID = dpv.FACTURAVENTA_ID
			AND DATE(fv.FECHA_EMISION) = DATE(NOW()) 
		RIGHT JOIN mercaderias AS m ON dpv.MERCADERIA_ID = m.MERCADERIA_ID
		GROUP BY m.MERCADERIA_ID
		ORDER BY totales DESC
		LIMIT 20";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectVentasPorHorasGrafico(){
		$sql = "";
		$request = $this->select_all($sql);
		return $request;
	}

	//mejor hacer las siguientes consultas para los graficos: 
	//cantidad total de productos vendidos en general
	public function selectCantidadProductosVendidos(){
		$sql = "SELECT m.MERCADERIA_ID, m.NOMBRE, SUM(IFNULL(dpv.CANTIDAD,0)) AS cantidades
		FROM detalle_pedidos_venta AS dpv
		INNER JOIN facturas_venta AS fv ON fv.FACTURAVENTA_ID = dpv.FACTURAVENTA_ID
		RIGHT JOIN mercaderias AS m ON dpv.MERCADERIA_ID = m.MERCADERIA_ID
		GROUP BY m.MERCADERIA_ID
		ORDER BY cantidades DESC
		LIMIT 20";
		$request = $this->select_all($sql);
		return $request;
	}
	//monto total por producto en general
	public function selectMontoPorProductoVenido(){
		$sql = "SELECT m.MERCADERIA_ID, m.NOMBRE, IFNULL(SUM(dpv.CANTIDAD*dpv.PRECIO),0) AS totales
		FROM detalle_pedidos_venta AS dpv
		INNER JOIN facturas_venta AS fv ON fv.FACTURAVENTA_ID = dpv.FACTURAVENTA_ID
		RIGHT JOIN mercaderias AS m ON dpv.MERCADERIA_ID = m.MERCADERIA_ID
		GROUP BY m.MERCADERIA_ID
		ORDER BY totales DESC
		LIMIT 20";
		$request = $this->select_all($sql);
		return $request;
	}
	//"en general significa desde que se empezo a usar la aplicacion"
}
?>