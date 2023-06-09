<?php 
class VentasModel extends Mysql {
	public function __construct(){
		parent::__construct();
	}
	public function selectVentas(){
		$sql = "SELECT fv.FACTURAVENTA_ID, fv.NUMERO_FACTURA, fv.FACTURATIPO_ID, ft.FACTURA_TIPO, fv.FECHA_EMISION, fv.TOTAL,
		fv.FORMAPAGO_ID, fp.FORMA_PAGO, fv.ESTADO_ID, ep.DESCRIPCION, 
		CONCAT(suc.RAZONSOCIAL,' suc.',suc.CODIGO_SUCURSAL) AS SUCURSAL
		FROM facturas_venta AS fv 
		INNER JOIN factura_tipo AS ft ON fv.FACTURATIPO_ID = ft.FACTURATIPO_ID 
		INNER JOIN forma_pago AS fp ON fv.FORMAPAGO_ID = fp.FORMAPAGO_ID
		INNER JOIN estado_pedido AS ep ON fv.ESTADO_ID = ep.ESTADO_ID
		INNER JOIN sucursales AS suc ON fv.SUCURSAL_ID = suc.SUCURSAL_ID";
		$request = $this->select_all($sql);
		return $request;
	}
	public function selectVenta(int $id){
		$sql = "SELECT fv.*, fp.FORMA_PAGO, CONCAT(suc.RAZONSOCIAL,' suc.',suc.CODIGO_SUCURSAL) AS SUCURSAL, 
		c.CLIENTE_ID, c.NOMBRE, c.APELLIDO, c.DNI
		FROM facturas_venta fv
		INNER JOIN clientes c ON fv.CLIENTE_ID = c.CLIENTE_ID
		INNER JOIN forma_pago AS fp ON fv.FORMAPAGO_ID = fp.FORMAPAGO_ID
		INNER JOIN sucursales AS suc ON fv.SUCURSAL_ID = suc.SUCURSAL_ID
		WHERE fv.FACTURAVENTA_ID = {$id}";
		$request = $this->select($sql);
		return $request;
	}
	public function selectDetalle(int $id){
		$sql = "SELECT m.CODIGO, m.NOMBRE AS DESCRIPCION, um.NOMBRE AS UNIMEDIDA, i.IVA_PORCENTAJE, dpv.CANTIDAD, dpv.PRECIO, (dpv.PRECIO*dpv.CANTIDAD) AS TOTAL
		FROM detalle_pedidos_venta dpv
		INNER JOIN mercaderias m ON m.MERCADERIA_ID = dpv.MERCADERIA_ID
		INNER JOIN unidades_medida um ON um.UNIMEDIDA_ID = dpv.UNIMEDIDA_ID
		INNER JOIN iva i ON i.IVA_ID = m.IVA_ID
		WHERE dpv.FACTURAVENTA_ID = {$id}";
		$request = $this->select_all($sql);
		return $request;
	}
	public function insertVenta(array $datos){
		// try {
			$datosCabecera = array($datos[6],$datos[1],$datos[8],$datos[0],$datos[4],$datos[5],$datos[9],$datos[7],$datos[2],$datos[3],$datos[10]);
			$datosDetalles = $datos[11];
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
	public function deleteVenta(int $id){
		$sql = "UPDATE facturas_venta SET FECHA_BAJA = NOW(), ESTADO_ID = ?, BAJA_EMPLEADO = ? WHERE FACTURAVENTA_ID = ?";
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
	public function selectNumeroFactura(){
		$sql = "SELECT MAX(NUMERO_FACTURA)+1 AS numFactura FROM facturas_venta";
		$request = $this->select($sql);
		return $request;
	}
	private function insertCabecera(array $datos){
		//insertar tambien formas de pago en la tabla facturaventa_formapago
		$numero = $this->selectNumeroFactura();
		$sql = "INSERT INTO facturas_venta(FACTURATIPO_ID, NUMERO_FACTURA, FORMAPAGO_ID, SUCURSAL_ID, CLIENTE_ID, EMPLEADO_ID, 
		TESTIGO_ID, ESTADO_ID, FECHA_ALTA, DIRECCION_ENVIO, TOTAL, IVA_TOTAL, FECHA_EMISION)
		VALUES(?, ".intval($numero['numFactura']).", ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)";
		$request = $this->insert($sql, $datos);
		return $request;
	}
	private function insertDetalles(int $facturaId, array $datos){
		$sql = "INSERT INTO detalle_pedidos_venta(FACTURAVENTA_ID, MERCADERIA_ID, UNIMEDIDA_ID, CANTIDAD, CANTIDAD_REAL, PRECIO)
		VALUES({$facturaId}, ?, ?, ?, 0, ?)";
		$request = $this->insert($sql, $datos);
		return $request;
	}
} 
?>