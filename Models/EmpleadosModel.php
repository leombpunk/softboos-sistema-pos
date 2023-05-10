<?php 
class EmpleadosModel extends Mysql{
	private $arrDatos;
	public function __construct(){
		// session_start();
		parent::__construct();
	}
	public function selectEmpleados(){
		$sql = "SELECT e.EMPLEADO_ID, e.DNI, e.NOMBRE, e.APELLIDO, e.FECHA_NACIMIENTO, e.CUIL, e.MAIL, e.TELEFONO, e.DIRECCION, e.SUCURSAL_ID, s.CODIGO_SUCURSAL, s.RAZONSOCIAL, s.DIRECCION AS SUC_DIRECCION, e.CARGO_ID, c.CARGO_DESCRIPCION, e.ESTADO_ID 
			FROM empleados e 
			INNER JOIN cargos c ON e.CARGO_ID = c.CARGO_ID 
			INNER JOIN sucursales s ON e.SUCURSAL_ID = s.SUCURSAL_ID 
			WHERE e.ESTADO_ID < 3";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectEmpleadosMaster(){
		$sql = "SELECT e.EMPLEADO_ID, e.DNI, e.NOMBRE, e.APELLIDO, e.FECHA_NACIMIENTO, e.CUIL, e.MAIL, e.TELEFONO, e.DIRECCION, e.SUCURSAL_ID, s.CODIGO_SUCURSAL, s.RAZONSOCIAL, s.DIRECCION AS SUC_DIRECCION, e.CARGO_ID, c.CARGO_DESCRIPCION, e.ESTADO_ID 
			FROM empleados e 
			INNER JOIN cargos c ON e.CARGO_ID = c.CARGO_ID 
			INNER JOIN sucursales s ON e.SUCURSAL_ID = s.SUCURSAL_ID";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectEmpleado(int $id){
		$sql = "SELECT e.EMPLEADO_ID, e.DNI, e.NOMBRE, e.APELLIDO, e.FECHA_NACIMIENTO, e.CUIL, e.MAIL, e.TELEFONO, e.DIRECCION, e.SUCURSAL_ID, s.CODIGO_SUCURSAL, s.RAZONSOCIAL, s.DIRECCION AS SUC_DIRECCION, e.CARGO_ID, c.CARGO_DESCRIPCION, e.ESTADO_ID 
			FROM empleados e 
			INNER JOIN cargos c ON c.CARGO_ID = e.CARGO_ID 
			INNER JOIN sucursales s ON s.SUCURSAL_ID = e.SUCURSAL_ID 
			WHERE e.EMPLEADO_ID = {$id} AND e.ESTADO_ID < 3";
		$request = $this->select($sql);
		return $request;
	}
	public function selectEmpleadoMaster(int $id){
		$sql = "SELECT e.EMPLEADO_ID, e.DNI, e.NOMBRE, e.APELLIDO, e.FECHA_NACIMIENTO, e.CUIL, e.MAIL, e.TELEFONO, e.DIRECCION, e.SUCURSAL_ID, s.CODIGO_SUCURSAL, s.RAZONSOCIAL, s.DIRECCION AS SUC_DIRECCION, e.CARGO_ID, c.CARGO_DESCRIPCION, e.ESTADO_ID 
			FROM empleados e 
			INNER JOIN cargos c ON c.CARGO_ID = e.CARGO_ID 
			INNER JOIN sucursales s ON s.SUCURSAL_ID = e.SUCURSAL_ID 
			WHERE e.EMPLEADO_ID = {$id}";
		$request = $this->select($sql);
		return $request;
	}
	public function selectSucursales(){
		$sql = "SELECT SUCURSAL_ID, CODIGO_SUCURSAL, RAZONSOCIAL
		FROM sucursales
		WHERE ESTADO_ID = 1";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectCargosDiff(){
		$sql = "SELECT * FROM cargos c WHERE c.ESTADO_ID = 1 AND (c.CARGO_ID != ".$_SESSION["userDATA"]["CARGO_ID"]." OR 1 = ".$_SESSION["userDATA"]["CARGO_ID"].")";
		$request = $this->select_all($sql);
		return $request;
	}
	public function insertEmpleado(array $datos){
		$this->arrDatos = $datos;
		$sql = "INSERT INTO empleados(SUCURSAL_ID, CARGO_ID, ESTADO_ID, DNI, NOMBRE, APELLIDO, FECHA_NACIMIENTO, CUIL, CONTRASENA, MAIL, TELEFONO, DIRECCION, FECHA_ALTA) 
		VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, IF('".$datos[8]."'= '',NULL,?), IF('".$datos[9]."'= '',NULL,?), IF('".$datos[10]."'= '',NULL,?), NOW())";
		// echo $sql;
		$request = $this->insert($sql,$this->arrDatos);
		return $request;
	}
	public function updateEmpleado(array $datos){
		$this->arrDatos = $datos;
		$sql = "UPDATE empleados SET SUCURSAL_ID = ?, CARGO_ID = ?, ESTADO_ID = ?, DNI = ?, NOMBRE = ?, APELLIDO = ?, FECHA_NACIMIENTO = ?, MAIL = IF('".$this->arrDatos[6]."'='',NULL,?), TELEFONO = IF('".$this->arrDatos[7]."'='',NULL,?), DIRECCION = IF('".$this->arrDatos[8]."'='',NULL,?) WHERE EMPLEADO_ID = ?";
		$request = $this->update($sql, $this->arrDatos);
		return $request;
	} 
	public function deleteEmpleado(int $id){
		//verifica que el empleado a borrar no sea un administrador
		$sql = "SELECT 1 FROM empleados e WHERE e.EMPLEADO_ID = {$id} AND e.CARGO_ID = 1";
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE empleados SET ESTADO_ID = 3 WHERE EMPLEADO_ID = {$id}";
			$request = $this->delete($sql);
			if($request){
				$request = "ok";
			}
			else {
				$request = "error";
			}
		}
		else {
			$request = "exist";
		}
		return $request;
	}
	public function restaurarEmpleado(int $id){
		$sql = "UPDATE empleados SET ESTADO_ID = ? WHERE EMPLEADO_ID = ?";
		$request = $this->update($sql,array(1,$id));
		return $request;
	}
}
?>