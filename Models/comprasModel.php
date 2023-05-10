<?php 
class ComprasModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function selectCompras(){
		$sql = "SELECT fc.FACTURACOMPRA_ID, fc.NUMERO_FACTURA, fc.FACTURATIPO_ID, ft.FACTURA_TIPO, fc.FECHA_EMISION, fc.TOTAL,
		fc.FORMAPAGO_ID, fp.FORMA_PAGO, fc.ESTADO_ID, ep.DESCRIPCION, 
		CONCAT(suc.RAZONSOCIAL,' suc.',suc.CODIGO_SUCURSAL) AS SUCURSAL,
		fc.PROVEEDOR_ID, pro.RAZONSOCIAL, fc.EMPLEADO_ID, CONCAT(emp.NOMBRE,' ',emp.APELLIDO) AS EMPLEADO
		FROM facturas_compra AS fc 
		INNER JOIN factura_tipo AS ft ON fc.FACTURATIPO_ID = ft.FACTURATIPO_ID 
		INNER JOIN forma_pago AS fp ON fc.FORMAPAGO_ID = fp.FORMAPAGO_ID
		INNER JOIN estado_pedido AS ep ON fc.ESTADO_ID = ep.ESTADO_ID
		INNER JOIN sucursales AS suc ON fc.SUCURSAL_ID = suc.SUCURSAL_ID
		INNER JOIN proveedores AS pro ON fc.PROVEEDOR_ID = pro.PROVEEDOR_ID
		INNER JOIN empleados AS emp ON fc.EMPLEADO_ID = emp.EMPLEADO_ID";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectCompra(int $id){
		//actualizar consulta
		$sql = "SELECT fc.*, fp.FORMA_PAGO, suc.RAZONSOCIAL AS SUCURSAL, suc.CODIGO_SUCURSAL, 
		pro.PROVEEDOR_ID, pro.RAZONSOCIAL, CONCAT(emp.NOMBRE,' ',emp.APELLIDO) AS EMPLEADO
		FROM facturas_compra fc
		INNER JOIN proveedores pro ON fc.PROVEEDOR_ID = pro.PROVEEDOR_ID
		INNER JOIN forma_pago AS fp ON fc.FORMAPAGO_ID = fp.FORMAPAGO_ID
		INNER JOIN sucursales AS suc ON fc.SUCURSAL_ID = suc.SUCURSAL_ID
		INNER JOIN empleados AS emp ON fc.EMPLEADO_ID = emp.EMPLEADO_ID
		WHERE fc.FACTURACOMPRA_ID = {$id}";
		$request = $this->select($sql);
		return $request;
	}
	public function selectDetalle(int $id){
		$sql = "SELECT m.CODIGO, m.NOMBRE AS DESCRIPCION, um.NOMBRE AS UNIMEDIDA, i.IVA_PORCENTAJE, dpc.CANTIDAD, dpc.PRECIO, (dpc.PRECIO*dpc.CANTIDAD) AS TOTAL
		FROM detalle_pedidos_compra dpc
		INNER JOIN mercaderias m ON m.MERCADERIA_ID = dpc.MERCADERIA_ID
		INNER JOIN unidades_medida um ON um.UNIMEDIDA_ID = dpc.UNIMEDIDA_ID
		INNER JOIN iva i ON i.IVA_ID = m.IVA_ID
		WHERE dpc.FACTURACOMPRA_ID = {$id}";
		$request = $this->select_all($sql);
		return $request;
	}
	public function insertCompra(array $datos){//falta testear este metodo
		//deberia de separar en funciones la cabecera y el detalle
		// try {
			$datosCabecera = array($datos[0],$datos[1],$datos[2],$datos[3],$datos[4],$datos[5],$datos[6],$datos[7],$datos[8],$datos[9],$datos[10],$datos[11]);
			$datosDetalles = $datos[12];
			// $this->mysqlStartTransaction();
			$request = $this->insertCabecera($datosCabecera);
			$facturaId = intval($request);
			if (is_int(intval($request)) and $request > 0) {
				foreach ($datosDetalles as $key => $value) {
					$arrDatos = array($value['productoId'],$value['unidadMedidaId'],$value['cantidad'],$value['precio']);
					$request = $this->insertDetalles($facturaId, $arrDatos);
				}
			}
			// else {
			// 	throw new Exception("Algo malio sal, y no se que pueda ser", 1);
			// }
			// $this->mysqlCommit();
			$request = "ok";
		// } catch (Exception $e) {
		// 	$this->mysqlRollback();
		// 	$request = "error. {$e}";
		// }
		return $request;
	}
	public function deleteCompra(int $id){
		$sql = "UPDATE facturas_compra SET FECHA_BAJA = NOW(), ESTADO_ID = ?, BAJA_EMPLEADO = ? WHERE FACTURACOMPRA_ID = ?";
		$arrData = array(2, $_SESSION['userID'], $id);
		$request = $this->update($sql, $arrData);
		if($request){
			$request = "ok";
		}
		else {
			$request = "error";
		}
		return $request;
	}
	private function insertCabecera(array $datos){
		$sql = "INSERT INTO facturas_compra(FACTURATIPO_ID, NUMERO_FACTURA, FORMAPAGO_ID, PROVEEDOR_ID, SUCURSAL_ID, 
		EMPLEADO_ID, TESTIGO_ID, ESTADO_ID, FECHA_ALTA, FECHA_EMISION, DIRECCION_ENVIO, TOTAL, IVA_TOTAL)
		VALUES(?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)";
		$request = $this->insert($sql, $datos);
		return $request;
	}
	private function insertDetalles(int $facturaId, array $datos){
		$sql = "INSERT INTO detalle_pedidos_compra(FACTURACOMPRA_ID, MERCADERIA_ID, UNIMEDIDA_ID, CANTIDAD, CANTIDAD_REAL, PRECIO)
		VALUES({$facturaId}, ?, ?, ?, 0, ?)";
		$request = $this->insert($sql, $datos);
		return $request;
	}
} 
?>