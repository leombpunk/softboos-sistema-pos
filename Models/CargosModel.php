<?php 
class CargosModel extends Mysql {
	private $arrDatos;
	public function __construct(){
		parent::__construct();
	}
	public function selectCargos(){
		$sql = "SELECT * FROM cargos WHERE ESTADO_ID < 3";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectCargo(int $cargoID){
		$sql = "SELECT * FROM cargos WHERE CARGO_ID = {$cargoID}";
		$request = $this->select($sql);
		return $request;
	}
	public function insertCargo(string $nombre,int $nivelcargo, int $estado){
		$sql = "INSERT INTO cargos(CARGO_DESCRIPCION, FECHA_ALTA, NIVELACCESO_ID, ESTADO_ID) 
		VALUES(?,NOW(),?,?)";
		$arrValues = array($nombre,$nivelcargo,$estado);
		$request = $this->insert($sql,$arrValues);
		return $request;
	}
	public function updateCargo(int $id, string $nombre, int $nivelcargo, int $estado){
		$sql = "SELECT 1 FROM cargos WHERE CARGO_DESCRIPCION = '{$nombre}' AND CARGO_ID != {$id}";
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE cargos SET CARGO_DESCRIPCION = ?, NIVELACCESO_ID = ?, ESTADO_ID = ? WHERE CARGO_ID = {$id}";
			$arrValues = array($nombre,$nivelcargo,$estado);
			$request = $this->update($sql,$arrValues);
		}
		else {
			$request = "exist";
		}
		return $request;
	}
	public function deleteCargo(int $id){
		$sql = "SELECT 1 FROM empleados WHERE CARGO_ID = {$id}";
		$request = $this->select_all($sql);
		if (empty($request)){
			// $sql = "DELETE FROM cargos WHERE CARGO_ID = {$id}";
			$sql = "UPDATE cargos SET ESTADO_ID = 3 WHERE CARGO_ID = {$id}";
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
	public function selectNivelesAcceso(){
		$sql = "SELECT * FROM niveles_acceso";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectPermisos(int $id){
		$sql = "SELECT m.MODULO_ID AS ID, m.NOMBRE, p.LEER AS L, p.AGREGAR AS A, p.MODIFICAR AS M, p.BORRAR AS B 
			FROM permisos p 
			INNER JOIN modulos m ON m.MODULO_ID = p.MODULO_ID 
			INNER JOIN estado e ON e.ESTADO_ID = m.ESTADO_ID 
				AND e.DESCRIPCION <> 'borrado' 
			WHERE p.CARGO_ID = {$id}";
		$request = $this->select_all($sql);
		return $request;
	}
	public function updatePermisos(int $cargoID, array $permisos){
		// $arrPermiso = array("LEER","AGREGAR","MODIFICAR","BORRAR");
		foreach ($permisos as $key => $value) {
			// $dato = explode("_",$value);
			// echo "variable dato: ";
			// var_dump($value["datos"]);
			// echo "<br>";
			// if ($dato[1] == 1) $i = 0;
			// elseif ($dato[1] == 2) $i = 1;
			// elseif ($dato[1] == 3) $i = 2;
			// elseif ($dato[1] == 4) $i = 3;
			// else return "error";
			// // echo "i=".$i;
			$arrDatos = array(intval($value["datos"]["leer"]),intval($value["datos"]["agregar"]),intval($value["datos"]["modificar"]),intval($value["datos"]["borrar"]),$cargoID,intval($value["modulo"]));
			$sql = "UPDATE permisos SET LEER = ?, AGREGAR = ?, MODIFICAR = ?,BORRAR = ? WHERE CARGO_ID = ? AND MODULO_ID = ?";
			$request = $this->update($sql,$arrDatos);
		}
		return $request;		
	}
	public function getModulos(){
		$sql = "SELECT * FROM modulos";
		$request = $this->select_all($sql);
		return $request;
	}
}
?>