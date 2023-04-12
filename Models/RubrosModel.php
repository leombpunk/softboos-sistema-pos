<?php
class RubrosModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
    public function selectRubros(){
		$sql = "SELECT RUBRO_ID AS id, NOMBRE AS de, DATE(FECHA_ALTA) AS fa, ESTADO_ID AS estado FROM rubros WHERE ESTADO_ID < 3";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectRubro(int $rubroID){
		$sql = "SELECT RUBRO_ID AS id, NOMBRE AS de, ESTADO_ID AS est, DATE(FECHA_ALTA) AS fa, IMAGEN_URL AS img FROM rubros WHERE RUBRO_ID = {$rubroID}";
		$request = $this->select($sql);
		return $request;
	}
	public function insertRubro(array $datos){
		$sql = "INSERT INTO rubros(NOMBRE, ESTADO_ID, IMAGEN_URL, FECHA_ALTA) 
		VALUES(?,?,?,NOW())";
		$request = $this->insert($sql,$datos);
		return $request;
	}
	public function updateRubro(array $datos){
		$sql = "SELECT 1 FROM rubros WHERE NOMBRE = '".$datos[0]."' AND RUBRO_ID != ".$datos[3];
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE rubros SET NOMBRE = ?, ESTADO_ID = ?, IMAGEN_URL = ? WHERE RUBRO_ID = ?";
			$request = $this->update($sql,$datos);
		}
		else {
			$request = "exist";
		}
		return $request;
	}
	public function deleteRubro(int $id){
		$sql = "SELECT 1 FROM mercaderias_rubros WHERE RUBRO_ID = {$id}";
		$request = $this->select_all($sql);
		if (empty($request)){
			// $sql = "DELETE FROM rubros WHERE RUBRO_ID = {$id}";
			$sql = "UPDATE rubros SET ESTADO_ID = 3 WHERE RUBRO_ID = {$id}";
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