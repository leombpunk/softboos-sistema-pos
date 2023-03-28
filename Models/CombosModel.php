<?php 
class CombosModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function selectCombos(){
		$sql = "SELECT * FROM recetas WHERE ESTADO_ID < 3";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectCombo(int $id){
		$sql = "SELECT * FROM recetas WHERE RECETA_ID = {$id}";
		$request = $this->select($sql);
		return $request;
	}
	public function insertCombo(int $idMercaderia, string $nombre, string $descripcion, int $estado, array $ingredientes){
		$sql = "INSERT INTO receta(MERCADERIA_ID, NOMBRE, DESCRIPCION, FECHA_ALTA, ALTA_EMPLEADO, ESTADO_ID) 
		VALUES(?,NOW(),?)";
		$arrValues = array($idMercaderia,$nombre,$descripcion,$estado);
		try {
			$this->mysqlStartTransaction();
			$request = $this->insert($sql,$arrValues);
			if (is_int(intval($request)) and $request > 0){
				$recetaId = intval($request);
				foreach ($ingredientes as $key => $value) {
					$datos = array($value['idInsumo'],$value['idUnidadMedida'],$value['cantidad']);
					$request = $this->insertInsumo($recetaId,$datos);
				}
			} else {
				throw new Exception("Algo malio sal, y no se que pueda ser", 1);
			}
			$this->mysqlCommit();
			$request = "ok";
		} catch (Exception $e) {
			$this->mysqlRollback();
			$request = "error. {$e}";
		}
		return $request;
	}
	public function updateCombo(int $idMercaderia, string $nombre, string $descripcion, int $estado, array $ingredientes, int $id){
		$sql = "SELECT 1 FROM recetas_elaboradas WHERE RECETA_ID = {$id}";
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE forma_pago SET FORMA_PAGO = ?, ESTADO_ID = ? WHERE FORMAPAGO_ID = {$id}";
			$arrValues = array($nombre,$estado);
			$request = $this->update($sql,$arrValues);
		}
		else {
			$request = "falopa";
		}
		return $request;
	}
	public function deleteCombo(int $id){
		$sql = "SELECT 1 FROM mercaderias AS m
		INNER JOIN recetas AS r ON m.MERCADERIA_ID = r.MERCADERIA_ID
		WHERE RECETA_ID = {$id} AND m.ESTADO_ID <> 3";
		$request = $this->select_all($sql);
		if (empty($request)){
			// $sql = "DELETE FROM forma_pago WHERE FORMAPAGO_ID = {$id}";
			$sql = "UPDATE recetas SET ESTADO_ID = 3, FECHA_BAJA = NOW(), BAJA_EMPLEADO = {$_SESSION['userID']} WHERE RECETA_ID = {$id}";
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
	//insumo insertar normal, al actualizar borrar los insumos relacionados e insertarlos de nuevo
	private function insertInsumo(int $recetaId, array $insumos){
		$sql = "INSERT INTO lista_insumos(RECETA_ID, MERCADERIA_ID, UNIMEDIDA_ID, INSUMO_CANTIDAD, INSUMO_CANTIDAD_REAL) 
		VALUES({$recetaId},?,?,?,{$insumos['cantidad']})";
		$request = $this->insert($sql,$insumos);
		return $request;
	}
	private function deleteInsumo(int $id){
		$sql = "DELETE * FROM lista_insumos WHERE RECETA_ID = {$id}";
		$request = $this->delete($sql);
		return $request;
	}
}
?>