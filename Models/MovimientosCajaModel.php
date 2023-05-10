<?php 
class MovimientosCajaModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	//traer todo o solo lo del día, esa es la cuestión
	public function selectMovimientos(){
		$sql = "SELECT mc.ID AS id, mc.DESCRIPCION AS descripcion, mt.DESCRIPCION AS tipo, mc.FECHA_ALTA AS alta, mc.MONTO AS monto, CONCAT(e.NOMBRE,' ',e.APELLIDO) AS empleado,
		mc.ESTADO_ID 
		FROM movimientos_caja AS mc
		INNER JOIN empleados AS e ON mc.EMPLEADO_ID = e.EMPLEADO_ID
        INNER JOIN movimiento_tipo AS mt ON mc.TIPO_ID = mt.MOVIMIENTOTIPO_ID
		WHERE mc.ESTADO_ID < 3";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectMovimientosMaster(){
		$sql = "SELECT mc.ID AS id, mc.DESCRIPCION AS descripcion, mt.DESCRIPCION AS tipo, mc.FECHA_ALTA AS alta, mc.MONTO AS monto, CONCAT(e.NOMBRE,' ',e.APELLIDO) AS empleado,
		mc.ESTADO_ID 
		FROM movimientos_caja AS mc
		INNER JOIN empleados AS e ON mc.EMPLEADO_ID = e.EMPLEADO_ID
        INNER JOIN movimiento_tipo AS mt ON mc.TIPO_ID = mt.MOVIMIENTOTIPO_ID";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectMovimiento(int $id){
		$sql = "SELECT * FROM movimientos_caja WHERE ID = {$id} AND ESTADO_ID < 3";
		$request = $this->select($sql);
		return $request;
	}
	public function selectMovimientoMaster(int $id){
		$sql = "SELECT * FROM movimientos_caja WHERE ID = {$id}";
		$request = $this->select($sql);
		return $request;
	}
	public function selectTipoMovimiento(){
		if(isSetAperturaCaja()){
			$sql = "SELECT MOVIMIENTOTIPO_ID AS id, DESCRIPCION AS descripcion
			FROM movimiento_tipo
			WHERE MOVIMIENTOTIPO_ID <> 3";//ingreso egreso
		} else {
			$sql = "SELECT MOVIMIENTOTIPO_ID AS id, DESCRIPCION AS descripcion
			FROM movimiento_tipo
			WHERE MOVIMIENTOTIPO_ID = 3";//saldo inicial o apertura de caja queseyo
		}
		$request = $this->select_all($sql);
		return $request;
	}
	public function insertMovimiento(string $descripcion, int $tipo, $monto){
		$sql = "INSERT INTO movimientos_caja(EMPLEADO_ID, DESCRIPCION, FECHA_ALTA, ESTADO_ID, TIPO_ID, MONTO) 
		VALUES(?,?,NOW(),1,?,?)";
		$arrValues = array($_SESSION['userID'],$descripcion,$tipo,$monto);
		$request = $this->insert($sql,$arrValues);
		return $request;
	}
	public function updateMovimiento(int $id, string $nombre){
		$sql = "UPDATE movimientos_caja SET DESCRIPCION = ?, ESTADO_ID = 5 WHERE ID = {$id}";
		$arrValues = array($nombre);
		$request = $this->update($sql,$arrValues);
		return $request;
	}
	public function deleteMovimiento(int $id){
		$sql = "UPDATE movimientos_caja SET ESTADO_ID = 3, FECHA_BAJA = NOW(), BAJA_EMPLEADO_ID = {$_SESSION['userID']} WHERE ID = {$id}";
		$request = $this->delete($sql);
		if($request){
			$request = "ok";
		}
		else {
			$request = "error";
		}
		return $request;
	}
	public function restaurarMovimiento(int $id){
		$sql = "UPDATE movimientos_caja SET ESTADO_ID = ?, FECHA_BAJA = NULL, BAJA_EMPLEADO = NULL WHERE ID = ?";
		$request = $this->update($sql,array(1,$id));
		return $request;
	}
}
?>