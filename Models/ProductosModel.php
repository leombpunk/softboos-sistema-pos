<?php 
class ProductosModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function selectProductos(){
		$sql = "SELECT m.MERCADERIA_ID id, m.CODIGO cod, m.NOMBRE nom, m.ALERTA_MINCANT, m.ALERTA_MAXCANT, 
		um.NOMBRE umnom, r.NOMBRE rnom, IFNULL(mca.CANTIDAD_ACTUAL,0) cant, m.ESTADO_ID est 
		FROM mercaderias m 
		INNER JOIN mercaderias_unidadesmedida mu ON m.MERCADERIA_ID = mu.MERCADERIA_ID AND mu.PRIORIDAD = 1 
		INNER JOIN unidades_medida um ON mu.UNIMEDIDA_ID = um.UNIMEDIDA_ID 
		INNER JOIN mercaderias_rubros mr ON m.MERCADERIA_ID = mr.MERCADERIA_ID AND mr.ENTRADA = 1 
		INNER JOIN rubros r ON mr.RUBRO_ID = r.RUBRO_ID AND m.FECHA_BAJA IS NULL 
		LEFT JOIN mercaderias_cantidad_actual mca ON m.MERCADERIA_ID = mca.MERCADERIA_ID";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectProducto(int $productoID){
		$sql = "SELECT  m.MERCADERIA_ID id, m.CODIGO cod, m.NOMBRE nom, m.ALERTA_MINCANT cmin, m.ALERTA_MAXCANT cmax, 
		um.UNIMEDIDA_ID umid, r.RUBRO_ID rid, IFNULL(mca.CANTIDAD_ACTUAL,0) cant, m.ESTADO_ID est, m.IVA_ID iva 
		FROM mercaderias m 
		INNER JOIN mercaderias_unidadesmedida mu ON m.MERCADERIA_ID = mu.MERCADERIA_ID AND mu.PRIORIDAD = 1 
		INNER JOIN unidades_medida um ON mu.UNIMEDIDA_ID = um.UNIMEDIDA_ID 
		INNER JOIN mercaderias_rubros mr ON m.MERCADERIA_ID = mr.MERCADERIA_ID AND mr.ENTRADA = 1 
		INNER JOIN rubros r ON mr.RUBRO_ID = r.RUBRO_ID AND m.FECHA_BAJA IS NULL 
		LEFT JOIN mercaderias_cantidad_actual mca ON m.MERCADERIA_ID = mca.MERCADERIA_ID
		WHERE m.MERCADERIA_ID = {$productoID}";
		$request = $this->select($sql);
		return $request;
	}
	public function selectRubros(){
		$sql = "SELECT RUBRO_ID, NOMBRE FROM rubros WHERE ESTADO_ID = 1";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectUnMedidas(){
		$sql = "SELECT UNIMEDIDA_ID, NOMBRE FROM unidades_medida 
		WHERE ESTADO_ID = 1 AND UNIMEDIDA_ID = UNIDADMINIMA_ID";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectIVA(){
		$sql = "SELECT IVA_ID, IVA_PORCENTAJE FROM iva";
		$request = $this->select_all($sql);
		return $request;
	}
	public function insertProducto(array $datos){
		$last_id = 0;
		$sql = "INSERT INTO mercaderias(NOMBRE, CODIGO, IVA_ID, ESTADO_ID, ALERTA_MINCANT, ALERTA_MAXCANT, FECHA_ALTA) 
		VALUES(?, ?, ?, ?, ?, ?, NOW())";
		$arrProducto = array($datos[0],$datos[1],$datos[6],$datos[7],$datos[4],$datos[5]);
		$request = $this->insert($sql,$arrProducto);
		if ($request > 0){ //cantidad actual
			$last_id = $request;
			$sql = "INSERT INTO mercaderias_cantidad_actual(MERCADERIA_ID, SUCURSAL_ID, CANTIDAD_ACTUAL, CANTIDAD_ANTERIOR, FECHA_ALTA)
			VALUES(?, ?, 0, 0, NOW())";
			$arrProCantActual = array($last_id, $_SESSION['userDATA']['SUCURSAL_ID']);
			$this->insert($sql, $arrProCantActual);
		}
		if ($request > 0){ //unidades de medidas
			$sql = "INSERT INTO mercaderias_unidadesmedida(MERCADERIA_ID, UNIMEDIDA_ID, PRIORIDAD)
			VALUES(?, ?, ?)";
			$arrProUniMedida = array($last_id, $datos[3],1);
			$this->insert($sql,$arrProUniMedida);
		}
		if ($request > 0){ //rubros
			$sql = "INSERT INTO mercaderias_rubros(MERCADERIA_ID, RUBRO_ID, ENTRADA)
			VALUES(?, ?, ?)";
			$arrProRubro = array($last_id, $datos[2], 1);
			$this->insert($sql,$arrProRubro);
		}
		return $request;
	}
	public function updateProducto(array $datos){
		$sql = "SELECT 1 FROM mercaderias WHERE MERCADERIA_ID != ".$datos[8]." AND (NOMBRE = '".$datos[0]."' AND CODIGO = '".$datos[1]."')";
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE mercaderias SET NOMBRE = ?, CODIGO = ?,IVA_ID = ?, ESTADO_ID = ?, 
			ALERTA_MINCANT = ?, ALERTA_MAXCANT = ? WHERE MERCADERIA_ID = ?";
			$arrDatos = array($datos[0],$datos[1],$datos[6],$datos[7],$datos[4],$datos[5],$datos[8]);
			$request = $this->update($sql,$arrDatos);
			if ($request){ //update mercaderias rubros
				$sql = "UPDATE mercaderias_rubros SET RUBRO_ID = ? WHERE MERCADERIA_ID = ? AND ENTRADA = ?";
				$arrDatos = array($datos[2],$datos[8],1);
				$this->update($sql,$arrDatos);
			}
			if ($request){ //update mercaderiass unidades de medida
				$sql = "UPDATE mercaderias_unidadesmedida SET UNIMEDIDA_ID = ? WHERE MERCADERIA_ID = ? AND PRIORIDAD = ?";
				$arrDatos = array($datos[3],$datos[8],1);
				$this->update($sql,$arrDatos);
			}
		}
		else {
			$request = "falopa";
		}
		return $request;
	}
	public function deleteProducto(int $id){
		$sql = "SELECT 1 FROM mercaderias WHERE MERCADERIAS_ID = {$id}";
		$request = $this->select_all($sql);
		if (empty($request)){
			$sql = "UPDATE mercaderias SET ESTADO_ID = 3 WHERE MERCADERIAS_ID = {$id}";
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
	public function selectProductoFull(int $productoID){
		$sql = "SELECT m.MERCADERIA_ID, m.CODIGO, m.NOMBRE, m.ALERTA_MINCANT, m.ALERTA_MAXCANT, 
		m.FECHA_ALTA, m.FECHA_BAJA, m.IVA_ID, i.IVA_NOMBRE, m.ESTADO_ID, e.DESCRIPCION, r.RUBRO_ID, 
		r.NOMBRE, um.UNIMEDIDA_ID, um.NOMBRE, mca.CANTIDAD_ACTUAL, s.SUCURSAL_ID, s.CODIGO_SUCURSAL 
		FROM mercaderias m 
		INNER JOIN mercaderias_rubros mr ON m.MERCADERIA_ID = mr.MERCADERIA_ID 
		INNER JOIN rubros r ON mr.RUBRO_ID = r.RUBRO_ID 
		INNER JOIN mercaderias_unidadesmedida mu ON m.MERCADERIA_ID = mu.MERCADERIA_ID 
		INNER JOIN unidades_medida um ON mu.UNIMEDIDA_ID = um.UNIMEDIDA_ID
		INNER JOIN iva i ON m.IVA_ID = i.IVA_ID 
		INNER JOIN estado e ON m.ESTADO_ID = e.ESTADO_ID 
		LEFT JOIN mercaderias_cantidad_actual mca ON m.MERCADERIA_ID = mca.MERCADERIA_ID 
		LEFT JOIN sucursales s ON mca.SUCURSAL_ID = s.SUCURSAL_ID 
		WHERE m.MERCADERIA_ID = {$productoID}";
		$request = $this->select_all($sql);
		return $request;
	}
}
?>