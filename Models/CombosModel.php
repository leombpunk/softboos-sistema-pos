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
		$sql = "SELECT r.*, m.NOMBRE AS mercanom, e.DESCRIPCION AS estado 
		FROM recetas AS r
		INNER JOIN mercaderias AS m ON r.MERCADERIA_ID = m.MERCADERIA_ID
		INNER JOIN estado AS e ON r.ESTADO_ID = e.ESTADO_ID
		WHERE r.RECETA_ID = {$id}";
		$request = $this->select($sql);
		return $request;
	}
	public function selectInsumosCombo(int $id){
		$sql = "SELECT li.MERCADERIA_ID AS id, m.NOMBRE AS nombre, li.UNIMEDIDA_ID AS umid, um.NOMBRE AS umnombre, li.INSUMO_CANTIDAD AS cantidad
		FROM lista_insumos AS li
		INNER JOIN mercaderias AS m ON li.MERCADERIA_ID = m.MERCADERIA_ID
		INNER JOIN recetas AS r ON li.RECETA_ID = r.RECETA_ID
		INNER JOIN unidades_medida AS um ON li.UNIMEDIDA_ID = um.UNIMEDIDA_ID
		WHERE li.RECETA_ID = {$id}";
		$request = $this->select_all($sql);
		return $request;
	}
	public function insertCombo(int $idMercaderia, string $nombre, string $descripcion, int $estado, array $ingredientes){
		$sql = "INSERT INTO recetas(MERCADERIA_ID, NOMBRE, DESCRIPCION, FECHA_ALTA, ALTA_EMPLEADO, ESTADO_ID) 
		VALUES(?,?,?,NOW(),{$_SESSION['userID']},?)";
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
			} 
			// else {
			// 	throw new PDOException("Algo malio sal, y no se que pueda ser", 1);
			// }
			$this->mysqlCommit();
			$request = "Ok";
		} catch (PDOException $e) {
			$this->mysqlRollback();
			$request = "Error. ".$e->getMessage()." - ".$e->getCode();
			// $request = "Error. ".mensajeSQL($e);
			// throw new PDOException($request);
		}
		return $request;
	}
	public function updateCombo(string $nombre, string $descripcion, int $estado, array $ingredientes, int $id){
		$sql = "SELECT 1 FROM recetas WHERE NOMBRE LIKE '%{$nombre}%' AND RECETA_ID <> {$id}";
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE recetas SET NOMBRE = ?, DESCRIPCION = ?, ESTADO_ID = ? WHERE RECETA_ID = {$id}";
			$arrValues = array($nombre,$descripcion,$estado);
			try {
				$this->mysqlStartTransaction();
				$request = $this->update($sql,$arrValues);
				if ($request){
					$this->deleteInsumo($id);
					foreach ($ingredientes as $key => $value) {
						$datos = array($value['idInsumo'],$value['idUnidadMedida'],$value['cantidad']);
						$request = $this->insertInsumo($id,$datos);
					}
				}
				//delete e insert de nuevo
				$this->mysqlCommit();
				$request = "Ok";
			} catch (PDOException $e) {
				$this->mysqlRollback();
				$request = "Error. {$e}";
			}
		}
		else {
			$request = "Falopa";
		}
		return $request;
	}
	public function deleteCombo(int $id){
		// $sql = "SELECT 1 FROM mercaderias AS m
		// INNER JOIN recetas AS r ON m.MERCADERIA_ID = r.MERCADERIA_ID
		// WHERE r.RECETA_ID = {$id} AND m.ESTADO_ID <> 3";
		// $request = $this->select_all($sql);
		// if (empty($request)){
			$sql = "UPDATE recetas SET ESTADO_ID = 3, FECHA_BAJA = NOW(), BAJA_EMPLEADO = {$_SESSION['userID']} WHERE RECETA_ID = {$id}";
			$request = $this->delete($sql);
			if($request){
				$request = "ok";
			}
			else {
				$request = "error";
			}
		// }
		// else {
		// 	$request = "exist";
		// }
		return $request;
	}
	//insumo insertar normal, al actualizar borrar los insumos relacionados e insertarlos de nuevo
	private function insertInsumo(int $recetaId, array $insumos){
		$sql = "INSERT INTO lista_insumos(RECETA_ID, MERCADERIA_ID, UNIMEDIDA_ID, INSUMO_CANTIDAD, INSUMO_CANTIDAD_REAL) 
		VALUES({$recetaId},?,?,?,{$insumos[2]})";
		$request = $this->insert($sql,$insumos);
		return $request;
	}
	private function deleteInsumo(int $id){
		//actualizar tabla a estado borrado y quien lo borro
		$sql = "DELETE FROM lista_insumos WHERE RECETA_ID = {$id}";
		$request = $this->delete($sql);
		return $request;
	}
}
?>