<?php 
class ProveedoresModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function selectProveedores(){
		$sql = "SELECT p.PROVEEDOR_ID, p.RAZONSOCIAL, p.CUIT, p.MAIL, p.TELEFONO, p.WEB, p.DIRECCION, p.ESTADO_ID, c.FECHA_ALTA
		FROM proveedores p
		WHERE p.FECHA_BAJA IS NULL";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectProveedor(int $id){
		$sql = "SELECT p.PROVEEDOR_ID, p.RAZONSOCIAL, p.CUIT, p.MAIL, p.TELEFONO, p.WEB, p.DIRECCION, p.ESTADO_ID
			FROM proveedores p 
			INNER JOIN estado e on p.ESTADO_ID = e.ESTADO_ID 
			WHERE p.PROVEEDOR_ID = {$id}";
		$request = $this->select($sql);
		return $request;
	}
	public function insertProveedor(array $datos){
		$sql = "INSERT INTO proveedores(RAZONSOCIAL, CUIT, MAIL, TELEFONO, WEB, DIRECCION, ESTADO_ID, FECHA_ALTA) 
		VALUES(?, IF('".$datos[1]."'='',NULL,?), IF('".$datos[2]."'='',NULL,?), IF('".$datos[3]."'='',NULL,?), IF('".$datos[4]."'='',NULL,?), 
		IF('".$datos[5]."'='',NULL,?), ?, NOW())";
		$request = $this->insert($sql,$datos);
		return $request;
	}
	public function updateProveedor(array $datos){
		$sql = "UPDATE proveedores SET DNI = ?, NOMBRE = ?, APELLIDO = ?, FECHA_NACIMIENTO = ?, 
		CUIL = IF('".$datos[4]."'='',NULL,?), TELEFONO = IF('".$datos[5]."'='',NULL,?), 
		MAIL = IF('".$datos[6]."'='',NULL,?), DIRECCION = IF('".$datos[7]."'='',NULL,?), 
		ESTADO_ID = ? WHERE PROVEEDOR_ID = ? ";
		$request = $this->update($sql,$datos);
		return $request;
	}
	public function deleteProveedor(int $id){
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