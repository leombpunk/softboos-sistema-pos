<?php 
class SucursalesModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function selectSucursales(){
		$sql = "SELECT suc.SUCURSAL_ID, suc.RAZONSOCIAL AS nombre, suc.TELEFONO AS telefono, DATE(suc.FECHA_ALTA) AS fechaAlta, 
		suc.CODIGO_SUCURSAL AS codigo, suc.CUIT, suc.MAIL, suc.WEB, suc.ESTADO_ID, e.DESCRIPCION
        FROM sucursales AS suc
        INNER JOIN estado AS e ON suc.ESTADO_ID = e.ESTADO_ID
		WHERE suc.ESTADO_ID < 3";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectSucursalesMaster(){
		$sql = "SELECT suc.SUCURSAL_ID, suc.RAZONSOCIAL AS nombre, suc.TELEFONO AS telefono, DATE(suc.FECHA_ALTA) AS fechaAlta, 
		suc.CODIGO_SUCURSAL AS codigo, suc.CUIT, suc.MAIL, suc.WEB, suc.ESTADO_ID, e.DESCRIPCION
        FROM sucursales AS suc
        INNER JOIN estado AS e ON suc.ESTADO_ID = e.ESTADO_ID";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectSucursal(int $idSucursal){
		$sql = "SELECT suc.*, e.DESCRIPCION 
        FROM sucursales AS suc
		INNER JOIN estado AS e ON suc.ESTADO_ID = e.ESTADO_ID
		WHERE suc.SUCURSAL_ID = {$idSucursal} AND suc.ESTADO_ID < 3";
		$request = $this->select($sql);
		return $request;
	}
	public function selectSucursalMaster(int $idSucursal){
		$sql = "SELECT suc.*, e.DESCRIPCION 
        FROM sucursales AS suc
		INNER JOIN estado AS e ON suc.ESTADO_ID = e.ESTADO_ID
		WHERE suc.SUCURSAL_ID = {$idSucursal}";
		$request = $this->select($sql);
		return $request;
	}
    public function selectSucursalEmpleados(int $idSucursal){
		$sql = "SELECT emp.* 
		FROM empleados AS emp
        INNER JOIN sucursales AS suc ON suc.SUCURSAL_ID = emp.SUCURSAL_ID
		INNER JOIN estado AS e ON suc.ESTADO_ID = e.ESTADO_ID
			AND suc.ESTADO_ID = 1
		WHERE suc.SUCURSAL_ID = {$idSucursal}";
		$request = $this->select_all($sql);
		return $request;
    }
	public function insertSucursal(array $datos){
		$sql = "INSERT INTO sucursales(RAZONSOCIAL, CODIGO_SUCURSAL, CUIT, DIRECCION, TELEFONO, MAIL, WEB, ESTADO_ID, LOGO_URL, FECHA_ALTA) 
		VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
		$arrSucursal = array($datos[0],$datos[1],$datos[2],$datos[3],$datos[4],$datos[5],$datos[6],$datos[7],$datos[8]);
		$request = $this->insert($sql,$arrSucursal);
		return $request;
	}
	public function updateSucursal(array $datos){
		$sql = "SELECT 1 FROM sucursales WHERE SUCRUSAL_ID != ".$datos[12]." AND (RAZONSOCIAL = '".$datos[0]."' OR CODIGO_SUCURSAL = '".$datos[1]."')";
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE sucursales SET RAZONSOCIAL = ?, CODIGO_SUCURSAL = ?, CUIT = ?, DIRECCION = ?, 
			TELEFONO = ?, MAIL = ?, WEB = ?, ESTADO_ID = ?, LOGO_URL = ? WHERE SUCRUSAL_ID = ?";
			$arrDatos = array($datos[0],$datos[1],$datos[2],$datos[3],$datos[4],$datos[5],$datos[6],$datos[7],$datos[8],$datos[9]); //hacer esto es una huevada
			$request = $this->update($sql,$arrDatos);
		}
		else {
			$request = -1;
		}
		return $request;
	}
	public function deleteSucursal(int $id){
		$sql = "SELECT 1 FROM empleados WHERE SUCURSAL_ID = {$id}";
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE sucursales SET ESTADO_ID = 3 WHERE SUCURSAL_ID = {$id}";
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
	public function restaurarSucursal(int $id){
		$sql = "UPDATE sucursales SET ESTADO_ID = ? WHERE SUCURSAL_ID = ?";
		$request = $this->update($sql,array(1,$id));
		return $request;
	}
}
?>