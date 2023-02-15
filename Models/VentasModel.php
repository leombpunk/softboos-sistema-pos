<?php 
class VentasModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function selectVentas(){
		$sql = "SELECT p.PROVEEDOR_ID, p.RAZONSOCIAL, p.CUIT, p.MAIL, p.TELEFONO, p.WEB, p.DIRECCION, p.ESTADO_ID, p.FECHA_ALTA
		FROM proveedores p
		WHERE p.FECHA_BAJA IS NULL";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectVenta(int $id){
		$sql = "SELECT p.PROVEEDOR_ID, p.RAZONSOCIAL, p.CUIT, p.MAIL, p.TELEFONO, p.WEB, p.DIRECCION, p.FECHA_ALTA,
		e.DESCRIPCION AS ESTADO
		FROM proveedores p 
		INNER JOIN estado e on p.ESTADO_ID = e.ESTADO_ID 
		WHERE p.PROVEEDOR_ID = {$id}";
		$request = $this->select($sql);
		return $request;
	}
	public function insertVenta(array $datos){
		$sql = "INSERT INTO proveedores(RAZONSOCIAL, CUIT, MAIL, TELEFONO, WEB, DIRECCION, ESTADO_ID, FECHA_ALTA) 
		VALUES(?, ?, IF('".$datos[2]."'='',NULL,?), IF('".$datos[3]."'='',NULL,?), IF('".$datos[4]."'='',NULL,?), 
		IF('".$datos[5]."'='',NULL,?), ?, NOW())";
		$request = $this->insert($sql,$datos);
		return $request;
	}
	// public function updateVenta(array $datos){
	// 	$sql = "UPDATE proveedores SET RAZONSOCIAL = ?, CUIT = IF('".$datos[1]."'='',NULL,?), MAIL = IF('".$datos[2]."'='',NULL,?), 
	// 	TELEFONO = IF('".$datos[3]."'='',NULL,?), WEB=IF('".$datos[4]."'='',NULL,?), DIRECCION = IF('".$datos[5]."'='',NULL,?), 
	// 	ESTADO_ID = ? WHERE PROVEEDOR_ID = ?";
	// 	$request = $this->update($sql,$datos);
	// 	return $request;
	// }
	public function deleteVenta(int $id){
		$sql = "UPDATE proveedores SET FECHA_BAJA = NOW(), ESTADO_ID = 3 WHERE PROVEEDOR_ID = {$id}";
		$request = $this->delete($sql);
		if($request){
			$request = "ok";
		}
		else {
			$request = "error";
		}
		return $request;
	}
} 
?>