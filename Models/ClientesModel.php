<?php 
class ClientesModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function selectClientes(){
		$sql = "SELECT c.CLIENTE_ID, c.DNI, c.NOMBRE, c.APELLIDO, c.TELEFONO, c.MAIL, c.FECHA_ALTA, c.CUIL, 
		c.ESTADO_ID
		FROM clientes c
		WHERE FECHA_BAJA IS NULL";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectClientesMaster(){
		$sql = "SELECT c.CLIENTE_ID, c.DNI, c.NOMBRE, c.APELLIDO, c.TELEFONO, c.MAIL, c.FECHA_ALTA, c.CUIL, 
		c.ESTADO_ID
		FROM clientes c";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectCliente(int $id){
		$sql = "SELECT c.CLIENTE_ID, c.DNI, c.NOMBRE, c.APELLIDO, c.FECHA_NACIMIENTO, c.CUIL, c.TELEFONO,
			c.MAIL, c.DIRECCION, c.FECHA_ALTA, e.DESCRIPCION AS ESTADO 
			FROM clientes c 
			INNER JOIN estado e on c.ESTADO_ID = e.ESTADO_ID 
			WHERE c.CLIENTE_ID = {$id} AND c.ESTADO_ID < 3";
		$request = $this->select($sql);
		return $request;
	}
	public function selectClienteMaster(int $id){
		$sql = "SELECT c.CLIENTE_ID, c.DNI, c.NOMBRE, c.APELLIDO, c.FECHA_NACIMIENTO, c.CUIL, c.TELEFONO,
			c.MAIL, c.DIRECCION, c.FECHA_ALTA, e.DESCRIPCION AS ESTADO 
			FROM clientes c 
			INNER JOIN estado e on c.ESTADO_ID = e.ESTADO_ID 
			WHERE c.CLIENTE_ID = {$id}";
		$request = $this->select($sql);
		return $request;
	}
	public function insertCliente(array $datos){
		$sql = "INSERT INTO clientes(DNI, NOMBRE, APELLIDO, FECHA_NACIMIENTO, CUIL, TELEFONO, MAIL, DIRECCION, 
		ESTADO_ID, FECHA_ALTA) VALUES(?, ?, ?, ?, IF('".$datos[4]."'='',NULL,?), IF('".$datos[5]."'='',NULL,?), 
		IF('".$datos[6]."'='',NULL,?), IF('".$datos[7]."'='',NULL,?), ?, NOW())";
		$request = $this->insert($sql,$datos);
		return $request;
	}
	public function updateCliente(array $datos){
		$sql = "UPDATE clientes SET DNI = ?, NOMBRE = ?, APELLIDO = ?, FECHA_NACIMIENTO = ?, 
		CUIL = IF('".$datos[4]."'='',NULL,?), TELEFONO = IF('".$datos[5]."'='',NULL,?), 
		MAIL = IF('".$datos[6]."'='',NULL,?), DIRECCION = IF('".$datos[7]."'='',NULL,?), 
		ESTADO_ID = ? WHERE CLIENTE_ID = ? ";
		$request = $this->update($sql,$datos);
		return $request;
	}
	public function deleteCliente(int $id){
		$sql = "SELECT 1 FROM facturas_venta AS fv WHERE fv.CLIENTE_ID = {$id}";
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE clientes SET FECHA_BAJA = NOW(), ESTADO_ID = 3 WHERE CLIENTE_ID = {$id}";
			$request = $this->delete($sql);
			if($request){
				$request = "ok";
			}
			else {
				$request = "error";
			}
		} else {
			$request = "exist";
		}
		
		return $request;
	}
	public function restaurarCliente(int $id){
		$sql = "UPDATE clientes SET FECHA_BAJA = NULL, ESTADO_ID = ? WHERE CLIENTE_ID = ?";
		$request = $this->update($sql,array(1,$id));
		return $request;
	}
} 
?>