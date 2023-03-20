<?php 
class InformesModel extends Mysql {
	private $arrDatos;
	public function __construct(){
		parent::__construct();
	}
	//traer todo o solo lo del día, esa es la cuestión
	public function selectMovimientos(){
		$sql = "SELECT mc.ID AS id, mc.DESCRIPCION AS descripcion, mt.DESCRIPCION AS tipo, mc.FECHA_ALTA AS alta, mc.MONTO AS monto, CONCAT(e.NOMBRE,' ',e.APELLIDO) AS empleado 
		FROM movimientos_caja AS mc
		INNER JOIN empleados AS e ON mc.EMPLEADO_ID = e.EMPLEADO_ID
        INNER JOIN movimiento_tipo AS mt ON mc.TIPO_ID = mt.MOVIMIENTOTIPO_ID
		WHERE mc.ESTADO_ID <> 3";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectMovimiento(int $id){
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
}
?>