<?php
class UdMedidasModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
    public function selectUdMedidas(){
		$sql = "SELECT um.UNIMEDIDA_ID AS id, '1' AS cantidad1, um.NOMBRE AS de, 'es igual a' AS equal, 
		um.VALOR AS val, um2.NOMBRE AS de2, um.UNIDADMINIMA_ID AS unid, 
		IF(um.UNIMEDIDA_ID = um.UNIDADMINIMA_ID,'BASE','REFENCIADA') AS tipo, um.ESTADO_ID AS estado 
		FROM unidades_medida um 
		INNER JOIN (
			SELECT UNIMEDIDA_ID, NOMBRE, ABREVIATURA FROM unidades_medida 
		) AS um2 ON um2.UNIMEDIDA_ID = um.UNIDADMINIMA_ID
		WHERE um.ESTADO_ID < 3";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectUdMedida(int $udMedidaID){
		$sql = "SELECT um.UNIMEDIDA_ID AS id, um.NOMBRE AS de, um.VALOR AS val, um.UNIDADMINIMA_ID AS unid, 
		um.ESTADO_ID AS estado, um.ABREVIATURA AS abr 
		FROM unidades_medida um 
		WHERE um.UNIMEDIDA_ID = {$udMedidaID}";
		$request = $this->select($sql);
		return $request;
	}
	public function selectUMBase(){
		$sql = "SELECT UNIMEDIDA_ID AS id, NOMBRE AS de 
		FROM unidades_medida 
		WHERE UNIMEDIDA_ID = UNIDADMINIMA_ID AND ESTADO_ID = 1";
		$request = $this->select_all($sql);
		return $request;
	}
	public function insertUdMedida(array $datos){
		$sql = "INSERT INTO unidades_medida(NOMBRE, ABREVIATURA, UNIDADMINIMA_ID, VALOR, ESTADO_ID) 
		VALUES(?,?,IF('".$datos[2]."'='',NULL,?),?,?)";
		$request = $this->insert($sql,$datos);
		return $request;
	}
	public function updateUdMinID(int $id){
		$sql = "UPDATE unidades_medida SET UNIDADMINIMA_ID = ? WHERE UNIMEDIDA_ID = ?";
		$request = $this->update($sql,array($id,$id));
		return $request;
	}
	public function updateUdMedida(array $datos){
		$sql = "SELECT 1 FROM unidades_medida WHERE NOMBRE = '".$datos[0]."' AND UNIMEDIDA_ID != ".$datos[5];
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE unidades_medida SET NOMBRE = ?, ABREVIATURA = ?, UNIDADMINIMA_ID = 
			IF('".$datos[2]."'='',NULL,?), VALOR = ?, ESTADO_ID = ? WHERE UNIMEDIDA_ID = ?";
			$request = $this->update($sql,$datos);
		}
		else {
			$request = "falopa";
		}
		return $request;
	}
	public function deleteUdMedida(int $id){
		$sql = "SELECT 1 FROM mercaderias_unidadesmedida WHERE UNIMEDIDA_ID = {$id}";
		$request = $this->select_all($sql);
		if (empty($request)){
			// $sql = "DELETE FROM unidades_medida WHERE UNIMEDIDA_ID = {$id}";
			$sql = "UPDATE unidades_medida SET ESTADO_ID = 3 WHERE UNIMEDIDA_ID = {$id}";
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