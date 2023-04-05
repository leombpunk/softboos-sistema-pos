<?php 
class Compras extends Controllers{
	public function __construct(){
		parent::__construct();
		session_start();
		if (isset($_SESSION["userLogin"])){
			if (empty($_SESSION["userLogin"])){
				header('location: '.base_url().'login');
			}
		}
		else {
			header('location: '.base_url().'login');
		}
		getPermisos(8);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard");
		}
	}
	public function Compras(){
		// if (empty($_SESSION["permisosMod"]["r"])){
		// 	header("location:".base_url()."dashboard");
		// }
		$data["page_id"] = 8;
		$data["page_tag"] = "Compras | SoftBoos";
		$data["page_title"] = "Compras";
		$data["page_name"] = "compras";
		$data["page_filejs"] = "function_compras.js";
		$this->views->getView($this,"compras",$data);
	}

	public function nuevaCompra(){
		if (!isSetAperturaCaja()){
			header("location:".base_url()."movimientosCaja");
		}
		$data["page_id"] = 8;
		$data["page_tag"] = "Nueva Compra | SoftBoos";
		$data["page_title"] = "Nueva Compra";
		$data["page_name"] = "nueva compra";
		$data["page_filejs"] = "function_compras.js";
		$data["page_specialjs"] = array('<script src="'.media().'js/function_datalist_style.js" type="text/javascript"></script>');
		$this->views->getView($this,"nuevaCompra",$data);
	}

	public function getCompras(){
		$arrData = $this->model->selectCompras();
		$pago = "";
        for ($i=0; $i < count($arrData); $i++) {  
			$pago = "";
			if ($arrData[$i]["FORMAPAGO1"] == 1){ // efectivo
				$pago .= '<span class="badge badge-success">Efectivo</span> ';
            }

			if ($arrData[$i]["FORMAPAGO2"] == 2){ // debito
				$pago .= ' <span class="badge badge-warning">Debito</span> ';
            }

            if ($arrData[$i]["FORMAPAGO3"] == 3){ // credito
				$pago .= ' <span class="badge badge-danger">Credito</span>';
            }

			$arrData[$i]["FORMAPAGO"] = $pago;
            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="verCompra('.$arrData[$i]['FACTURAVENTA_ID'].');" class="btn btn-info btn-sm" title="Ver compra" type="button"><i class="fa fa-eye"></i></button>
            <button onclick="anularCompra('.$arrData[$i]['FACTURAVENTA_ID'].');" class="btn btn-danger btn-sm" title="Anular compra" type="button"><i class="fa fa-ban"></i></button>
            </div>'; 
        }
        // dep($arrData);
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function getCompra(int $compraID){
		try {
			$facturaId = intval(strClear($compraID));
			if ($facturaId > 0){
				$arrDataFactura = $this->model->selectCompra($facturaId);
				$arrDataFormaPago = $this->model->selectFormaPago($facturaId);
				$arrDataDetalle = $this->model->selectDetalle($facturaId);
				if (empty($arrDataFactura)){
					$arrResponse = array("status" => false, "message" => "Lista vacia.");
				}
				else {
					$arrData = array("cabecera" => $arrDataFactura, "formaPago" => $arrDataFormaPago, "detalle" => $arrDataDetalle);
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
		if ($_POST){
			// $arrResponse = array("status" => true, "message" => $_POST);
			//total subtotal e iva podria calcularlos aqui
			$cliente = empty($_POST["clienteId"]) ? 0 : intval(strClear($_POST["clienteId"]));
			$formaPago = empty($_POST["formaPagoId"]) ? 0 : intval(strClear($_POST["formaPagoId"]));
			$total = empty($_POST["total"]) ? 0.00 : floatval(strClear($_POST["total"]));
			$subtotal = empty($_POST["subtotal"]) ? 0.00 : floatval(strClear($_POST["subtotal"]));
			$iva = empty($_POST["iva"]) ? 0.00 : floatval(strClear($_POST["iva"]));
			//datos que faltan agregar
			//$_SESSION['userDATA']
			$cantidadPagos = 1;
			$empleadoId = intval($_SESSION['userDATA']['EMPLEADO_ID']);
			$testigoId = intval($_SESSION['userDATA']['EMPLEADO_ID']);
			$facturaTipoId = 1;
			$sucursalId = intval($_SESSION['userDATA']['SUCURSAL_ID']);
			$estadoId = 3;
			$direccionEnvio = "";
			//validar la estructura del detalle
			$detalle = empty($_POST["detalle"]) ? [] : $_POST["detalle"];
	 		// primero se validan los campos obligatorios
			if (empty($cliente) or !validar($cliente,2,1,11)){
				$arrResponse = array("status" => false, "message" => "El cliente ingresado no es valido o esta vacío.");
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
					//si tiene todos los campos valido paso a guardar los datos (cabecera y detalle)
					//datos que faltan empleadoId(quien se supone hizo la compra), sucursalId, estadoId, direccionEnvio(opcional), 
					//tipoFactura, testigoId(es el empleado logeado que carga la factura)
					//para formaPago cantidad de pagos creo que tambien necesita
					$arrData = array($cliente, $formaPago, $total, $iva, $empleadoId, $testigoId, $facturaTipoId, $direccionEnvio, $sucursalId, $estadoId, $detalle);
					$requestCompra = $this->model->insertCompra($arrData);
					if ($requestCompra > 0){
						$arrResponse = array("status" => true, "message" => "La factura de compra se ha dado de alta satisfactoriamente.", "data" => $requestCompra);
					}
					//si hay algun campo incorrecto tiro el else de algo salio mal
					else {
						$arrResponse = array("status" => false, "message" => "Algo salio mal, no se pudo guardar la factura de compra.", "data" => $requestCompra);
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
		if (isset($_POST["id"]) and is_numeric($_POST["id"])){
			$id = intval($_POST["id"]);
			$arrRequest = $this->model->deleteProveedor($id);
			if ($arrRequest == "ok"){
                $arrResponse = array("status" => true, "message" => "Proveedor borrado correctamente.");
            }
            else {
                $arrResponse = array("status" => false, "message" => "Error al eliminar al proveedor.");
            }
		}
		else {
			$arrResponse = array("status" => false, "message" => "Algo malio sal.");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function getNumeroFactura(){
		$request = $this->model->selectNumeroFactura();
		echo json_encode($request,JSON_UNESCAPED_UNICODE);
		die();
	}
}
?>