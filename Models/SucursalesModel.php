<?php 
class SucursalesModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function selectSucursales(){
		$sql = "SELECT suc.*, e.DESCRIPCION 
        FROM sucursales AS suc
        INNER JOIN estado AS e ON suc.ESTADO_ID = e.ESTADO_ID";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectSucursal(int $idSucursal){
		$sql = "SELECT * 
        FROM sucursales
		WHERE SUCURSAL_ID = {$idSucursal}";
		$request = $this->select($sql);
		return $request;
	}
    public function selectSucursalEmpleados(int $idSucursal){

    }
	public function insertSucursal(array $datos){
		$sql = "INSERT INTO mercaderias(NOMBRE, CODIGO, IVA_ID, ESTADO_ID, ALERTA_MINCANT, ALERTA_MAXCANT, FECHA_ALTA, PARA_VENTA, PARA_INSUMO) 
		VALUES(?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
		$arrSucursal = array($datos[0],$datos[1],$datos[6],$datos[11],$datos[4],$datos[5],$datos[10],$datos[9]);
		$request = $this->insert($sql,$arrSucursal);
		return $request;
	}
	public function updateSucursal(array $datos){
		$sql = "SELECT 1 FROM mercaderias WHERE MERCADERIA_ID != ".$datos[12]." AND (NOMBRE = '".$datos[0]."' OR CODIGO = '".$datos[1]."')";
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE mercaderias SET NOMBRE = ?, CODIGO = ?, IVA_ID = ?, ESTADO_ID = ?, 
			ALERTA_MINCANT = ?, ALERTA_MAXCANT = ?, PARA_VENTA = ?, PARA_INSUMO = ? WHERE MERCADERIA_ID = ?";
			$arrDatos = array($datos[0],$datos[1],$datos[6],$datos[11],$datos[4],$datos[5],$datos[10],$datos[9],$datos[12]);
			$request = $this->update($sql,$arrDatos);
		}
		else {
			$request = 0;
		}
		return $request;
	}
	public function deleteSucursal(int $id){
		$sql = "SELECT 1 FROM detalle_pedidos_venta WHERE MERCADERIA_ID = {$id}";
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE mercaderias SET ESTADO_ID = 3 WHERE MERCADERIA_ID = {$id}";
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
}
?>