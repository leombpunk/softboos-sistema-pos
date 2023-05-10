<?php 
class Compras extends Controllers{
	public function __construct(){
		parent::__construct();
		session_start();
		if(isSessionExpired()){
            session_unset();
            session_destroy();
            header('location: '.base_url().'login/?m=1');
        }
        elseif (isset($_SESSION["userLogin"])){
			if (empty($_SESSION["userLogin"])){
				header('location: '.base_url().'login');
			}
		}
		else {
			header('location: '.base_url().'login');
		}
		getPermisos(8);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard/?m=Compras");
		}
	}
	public function Compras(){
		$data["page_id"] = 8;
		$data["page_tag"] = "Compras | SoftBoos";
		$data["page_title"] = "Compras";
		$data["page_name"] = "compras";
		$data["page_filejs"] = "function_compras.js";
		$this->views->getView($this,"compras",$data);
	}

	public function nuevaCompra(){
		if ($_SESSION["permisos"][0]["AGREGAR"] == 0){
			header("location: ".base_url()."Dashboard/?m=Nueva%20Compra");
		}
		if (!isSetAperturaCaja()){
			header("location:".base_url()."movimientosCaja");
		}
		$data["page_id"] = 8;
		$data["page_tag"] = "Nueva Compra | SoftBoos";
		$data["page_title"] = "Nueva Compra";
		$data["page_name"] = "nueva compra";
		$data["page_filejs"] = "function_compras.js";
		$data["page_specialjs"] = array('<script src="'.media().'js/function_datalist_style_nuevaCompra.js" type="text/javascript"></script>');
		$this->views->getView($this,"nuevaCompra",$data);
	}

	public function getCompras(){
		$arrData = $this->model->selectCompras();
		$pago = "";
        for ($i=0; $i < count($arrData); $i++) {  
			$estado = "";
			if ($arrData[$i]['ESTADO_ID'] == 1) {//pendiente
				$estado = '<span class="badge badge-warning">'.$arrData[$i]['DESCRIPCION'].'</span>';
			} elseif ($arrData[$i]['ESTADO_ID'] == 2) {//cancelado
				$estado = '<span class="badge badge-danger">'.$arrData[$i]['DESCRIPCION'].'</span>';
			} elseif ($arrData[$i]['ESTADO_ID'] == 3) {//pagado
				$estado = '<span class="badge badge-success">'.$arrData[$i]['DESCRIPCION'].'</span>';
			}
			$arrData[$i]["ESTADO"] = $estado;
			$pago = '<span class="badge badge-success">'.$arrData[$i]['FORMA_PAGO'].'</span>';
			$arrData[$i]["FORMAPAGO"] = $pago;
			if ($arrData[$i]['ESTADO_ID'] == 2){
				$arrData[$i]['actions'] = '<div class="text-center">
				<button onclick="verCompra('.$arrData[$i]['FACTURACOMPRA_ID'].');" class="btn btn-info btn-sm" title="Ver compra" type="button"><i class="fa fa-eye"></i></button>
				</div>';
			} else {
				$arrData[$i]['actions'] = '<div class="text-center">
				<button onclick="verCompra('.$arrData[$i]['FACTURACOMPRA_ID'].');" class="btn btn-info btn-sm" title="Ver compra" type="button"><i class="fa fa-eye"></i></button>
				<button onclick="anularCompra('.$arrData[$i]['FACTURACOMPRA_ID'].');" class="btn btn-danger btn-sm" title="Anular compra" type="button"><i class="fa fa-ban"></i></button>
				</div>';
			}
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function getCompra(int $compraID){
		try {
			$facturaId = intval(strClear($compraID));
			if ($facturaId > 0){
				$arrDataFactura = $this->model->selectCompra($facturaId);
				$arrDataDetalle = $this->model->selectDetalle($facturaId);
				if (empty($arrDataFactura)){
					$arrResponse = array("status" => false, "message" => "Lista vacia.");
				}
				else {
					$arrData = array("cabecera" => $arrDataFactura, "detalle" => $arrDataDetalle);
					$arrResponse = array("status" => true, "message" => "ok", "data" => $arrData);
				}
			}
		} catch (Exception $e) {
			$arrResponse = array("status" => false, "message" => "error. {$e}");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function setCompra(){
		if ($_SESSION["permisos"][0]["AGREGAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para crear registros en este módulo.");
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			die();
		}
		elseif ($_POST){
			// $arrResponse = array("status" => true, "message" => $_POST);
			//total subtotal e iva podria calcularlos aqui
			$proveedor = empty($_POST["proveedorId"]) ? 0 : intval(strClear($_POST["proveedorId"]));
			$numeroFactura = empty($_POST["numeroFactura"]) ? 0 : intval(strClear($_POST["numeroFactura"]));
			$formaPago = empty($_POST["formaPagoId"]) ? 0 : intval(strClear($_POST["formaPagoId"]));
			$total = empty($_POST["total"]) ? 0.00 : floatval(strClear($_POST["total"]));
			$subtotal = empty($_POST["subtotal"]) ? 0.00 : floatval(strClear($_POST["subtotal"]));
			$iva = empty($_POST["iva"]) ? 0.00 : floatval(strClear($_POST["iva"]));
			$cantidadPagos = 1;
			$fecha = empty($_POST['fecha'])? "" : strClear($_POST['fecha']);
			$empleadoId = intval($_SESSION['userDATA']['EMPLEADO_ID']);
			$testigoId = intval($_SESSION['userDATA']['EMPLEADO_ID']);
			$facturaTipoId = 1;
			$sucursalId = empty($_POST["sucursalId"]) ? 0 : intval(strClear($_POST["sucursalId"]));
			$estadoId = 3;
			$direccionEnvio = "";
			$detalle = empty($_POST["detalle"]) ? [] : $_POST["detalle"];
	 		// primero se validan los campos obligatorios
			if (empty($proveedor) or !validar($proveedor,2,1,11)){
				$arrResponse = array("status" => false, "message" => "El cliente ingresado no es valido o esta vacío.");
			}
			elseif (empty($numeroFactura) and !validar($numeroFactura,2,1,13)) {
				$arrResponse = array("status" => false, "message" => "El numero de factura ingresado no es valido o esta vacío.");
			}
			elseif (empty($formaPago) and !validar($formaPago,2,1,11)) {
				$arrResponse = array("status" => false, "message" => "La forma de pago ingresada no es valida o esta vacía.");
			}
			elseif (empty($total) and !validar($total,10)) {
				$arrResponse = array("status" => false, "message" => "El monto total no es valido o está vacío.");
			}
			elseif (empty($subtotal) and !validar($subtotal,10)) {
				$arrResponse = array("status" => false, "message" => "El monto subtotal no es valido o está vacío.");
			}
			elseif (empty($iva) and !validar($iva,10)) {
				$arrResponse = array("status" => false, "message" => "El monto IVA no es valido o está vacío.");
			}
			elseif (empty($sucursalId) and !validar($sucursalId,2,1,11)) {
				$arrResponse = array("status" => false, "message" => "La sucursal seleccionada no es valida o esta vacía.");
			}
			elseif (empty($detalle)) {
				$arrResponse = array("status" => false, "message" => "El detalle de la factura no es valido o esta vacío.");
			}
			else {
				//dentro de este else validaria el detalle
				// $arrResponse = array();
				foreach ($detalle as $key => $value) {
					// array_push($arrResponse, $value['productoId']);
					if (empty($value['productoId']) and !validar($value['productoId'],2)) {
						$arrResponse = array("status" => false, "message" => "El id del producto no es valido. {$value}");
						break;
					}
					elseif (empty($value['cantidad']) and !validar($value['cantidad'],10)) {
						$arrResponse = array("status" => false, "message" => "La cantidad del producto no es valido. {$value}");
						break;
					}
					// se puede obviar este dato para guardarlo
					elseif (empty($value['iva']) and !validar($value['iva'],10)) {
						$arrResponse = array("status" => false, "message" => "El iva del producto no es valido. {$value}");
						break;
					}
					elseif (empty($value['precio']) and !validar($value['precio'],10)) {
						$arrResponse = array("status" => false, "message" => "El precio del producto no es valido. {$value}");
						break;
					}
					elseif (empty($value['unidadMedidaId']) and !validar($value['unidadMedidaId'],2)) {
						$arrResponse = array("status" => false, "message" => "La unidad de medida del producto no es valida. {$value}");
						break;
					}
					elseif (empty($value['total']) and !validar($value['total'],10)) {
						$arrResponse = array("status" => false, "message" => "El monto total del producto no es valido. {$value}");
						break;
					}
				}
				if (!isset($arrResponse)){
					try {
						$this->model->mysqlStartTransaction();
						$arrData = array($facturaTipoId, $numeroFactura, $formaPago, $proveedor, $sucursalId, $empleadoId, $testigoId, $estadoId, $fecha, 
						$direccionEnvio, $total, $iva, $detalle);
						$requestCompra = $this->model->insertCompra($arrData);
						if ($requestCompra == "ok"){
							$arrResponse = array("status" => true, "message" => "La factura de compra se ha dado de alta satisfactoriamente.", "data" => $requestCompra);
						}
						else {
							$arrResponse = array("status" => false, "message" => "Algo salio mal, no se pudo guardar la factura de compra.", "data" => $requestCompra);
						}
						$this->model->mysqlCommit();
					} catch (Exception $e) {
						$this->model->mysqlRollback();
						$arrResponse = array("status" => false, "message" => "Algo salio mal, no se pudo guardar la factura de compra.", "data" => $e);
					}
				}
			}
		}
		else {
			$arrResponse = array("status" => false, "message" => "No envió datos.");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function delCompra(){
		if ($_SESSION["permisos"][0]["BORRAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para borrar registros en este módulo.");
		}
		elseif (isset($_POST["id"]) and is_numeric($_POST["id"])){
			$id = intval($_POST["id"]);
			$arrRequest = $this->model->deleteCompra($id);
			if ($arrRequest == "ok"){
                $arrResponse = array("status" => true, "message" => "Compra borrada correctamente.");
            }
            else {
                $arrResponse = array("status" => false, "message" => "Error al eliminar la compra.");
            }
		}
		else {
			$arrResponse = array("status" => false, "message" => "Algo malio sal.");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}
}
?>