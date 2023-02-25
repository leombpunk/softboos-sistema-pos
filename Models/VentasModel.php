<?php 
class VentasModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function selectVentas(){
		//Hacer un inner join con facturaventa_formapago y detalle_pedidos_venta.
		//Trae resultados repetidos si existe mas de una forma de pago.
		//Si la forma de pago se puede limitar a un numero n (ejemplo 3 y siempre 3, que no cambie)
		//entonces le puedo agregar el sum con detalle_pedidos_venta del detalle, o agregar un campo 
		//en facturas_venta que sea total así no lo tengo que calcular (ya que la operación es muy tediosa)
		$sql = "SELECT fv.FACTURAVENTA_ID, fv.NUMERO_FACTURA, fv.FACTURATIPO_ID, ft.FACTURA_TIPO, fvfp1.FORMAPAGO_ID AS FORMAPAGO1, 
		fvfp2.FORMAPAGO_ID AS FORMAPAGO2, fvfp3.FORMAPAGO_ID AS FORMAPAGO3, fv.FECHA_EMISION, fv.TOTAL 
		FROM facturas_venta AS fv 
		INNER JOIN factura_tipo AS ft ON fv.FACTURATIPO_ID = ft.FACTURATIPO_ID 
		INNER JOIN facturaventa_formapago AS fvfp1 ON fv.FACTURAVENTA_ID = fvfp1.FACTURA_ID AND fvfp1.FORMAPAGO_ID = 1 
		LEFT JOIN facturaventa_formapago AS fvfp2 ON fv.FACTURAVENTA_ID = fvfp2.FACTURA_ID AND fvfp2.FORMAPAGO_ID = 2 
		LEFT JOIN facturaventa_formapago AS fvfp3 ON fv.FACTURAVENTA_ID = fvfp3.FACTURA_ID AND fvfp3.FORMAPAGO_ID = 3";
		// $sql = "SELECT * FROM facturas_venta AS fv INNER JOIN facturaventa_formapago AS fvfp ON fv.FACTURAVENTA_ID = fvfp.FACTURA_ID";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectVenta(int $id){
		$sql = "SELECT 1";
		$request = $this->select($sql);
		return $request;
	}
	public function insertVenta(array $datos){
		//deberia de separar en funciones la cabecera y el detalle
		$sql = "INSERT INTO facturas_venta(RAZONSOCIAL, CUIT, MAIL, TELEFONO, WEB, DIRECCION, ESTADO_ID, FECHA_ALTA) 
		VALUES(?, ?, IF('".$datos[2]."'='',NULL,?), IF('".$datos[3]."'='',NULL,?), IF('".$datos[4]."'='',NULL,?), 
		IF('".$datos[5]."'='',NULL,?), ?, NOW())";
		$request = $this->insert($sql,$datos);
		return $request;
	}
	// public function updateVenta(array $datos){
	// 	$sql = "UPDATE facturas_venta SET RAZONSOCIAL = ?, CUIT = IF('".$datos[1]."'='',NULL,?), MAIL = IF('".$datos[2]."'='',NULL,?), 
	// 	TELEFONO = IF('".$datos[3]."'='',NULL,?), WEB=IF('".$datos[4]."'='',NULL,?), DIRECCION = IF('".$datos[5]."'='',NULL,?), 
	// 	ESTADO_ID = ? WHERE PROVEEDOR_ID = ?";
	// 	$request = $this->update($sql,$datos);
	// 	return $request;
	// }
	public function deleteVenta(int $id){
		$sql = "UPDATE facturas_venta SET FECHA_BAJA = NOW(), ESTADO_ID = 3 WHERE PROVEEDOR_ID = {$id}";
		$request = $this->delete($sql);
		if($request){
			$request = "ok";
		}
		else {
			$request = "error";
		}
		return $request;
	}
	public function selectDetalles(int $id){
		$sql = "";
		$request = $this->select_all($sql);
		return $request;
	}
} 
?>