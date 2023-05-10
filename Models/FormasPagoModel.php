<?php 
class FormasPagoModel extends Mysql {
	private $arrDatos;
	public function __construct(){
		parent::__construct();
	}
	public function selectFormasPagos(){
		$sql = "SELECT * FROM forma_pago WHERE ESTADO_ID < 3";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectFormasPagosMaster(){
		$sql = "SELECT * FROM forma_pago";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectFormasPago(int $cargoID){
		$sql = "SELECT * FROM forma_pago WHERE FORMAPAGO_ID = {$cargoID} AND ESTADO_ID < 3";
		$request = $this->select($sql);
		return $request;
	}
	public function selectFormasPagoMaster(int $cargoID){
		$sql = "SELECT * FROM forma_pago WHERE FORMAPAGO_ID = {$cargoID}";
		$request = $this->select($sql);
		return $request;
	}
	public function insertFormasPago(string $nombre, int $estado){
		$sql = "INSERT INTO forma_pago(FORMA_PAGO, FECHA_ALTA, ESTADO_ID) 
		VALUES(?,NOW(),?)";
		$arrValues = array($nombre,$estado);
		$request = $this->insert($sql,$arrValues);
		return $request;
	}
	public function updateFormasPago(int $id, string $nombre, int $estado){
		$sql = "SELECT 1 FROM forma_pago WHERE FORMA_PAGO = '{$nombre}' AND FORMAPAGO_ID != {$id}";
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE forma_pago SET FORMA_PAGO = ?, ESTADO_ID = ? WHERE FORMAPAGO_ID = {$id}";
			$arrValues = array($nombre,$estado);
			$request = $this->update($sql,$arrValues);
		}
		else {
			$request = "exist";
		}
		return $request;
	}
	public function deleteFormasPago(int $id){
		$sql = "SELECT 1 FROM facturas_venta WHERE FORMAPAGO_ID = {$id}";
		$request = $this->select_all($sql);
		$sql2 = "SELECT 1 FROM facturas_compra WHERE FORMAPAGO_ID = {$id}";
		$request2 = $this->select_all($sql2);
		if (empty($request) and empty($request2)){
			// $sql = "DELETE FROM forma_pago WHERE FORMAPAGO_ID = {$id}";
			$sql = "UPDATE forma_pago SET ESTADO_ID = 3 WHERE FORMAPAGO_ID = {$id}";
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
	public function restaurarFormasPago(int $id){
		$sql = "UPDATE forma_pago SET ESTADO_ID = ? WHERE FORMAPAGO_ID = ?";
		$request = $this->update($sql, array(1,$id));
		return $request;
	}
}
?>