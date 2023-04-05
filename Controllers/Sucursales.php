<?php 
class Sucursales extends Controllers{
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
		getPermisos(16);
		// dep($_SESSION);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard");
		}
	}
	public function Sucursales(){
		$data["page_id"] = 16;
		$data["page_tag"] = "Sucursales | SoftBoos";
		$data["page_title"] = "Sucursales";
		$data["page_name"] = "sucursales";
		$data["page_filejs"] = "function_sucursales.js";
		$this->views->getView($this,"sucursales",$data);
	}
	public function getSucursales(){
		$arrData = $this->model->selectSucursales();
		for ($i=0; $i < count($arrData); $i++) {
            if ($arrData[$i]["ESTADO_ID"] == 1){
                $arrData[$i]["est"] = '<span class="badge badge-success">'.$arrData[$i]["DESCRIPCION"].'</span>';
            }
            elseif ($arrData[$i]["ESTADO_ID"] == 2){
                $arrData[$i]["est"] = '<span class="badge badge-danger">'.$arrData[$i]["DESCRIPCION"].'</span>';
            }
            elseif ($arrData[$i]["ESTADO_ID"] == 3){
                $arrData[$i]["est"] = '<span class="badge badge-warning">'.$arrData[$i]["DESCRIPCION"].'</span>';
                // agregar el cambio de funcion y boton de borrar a restablecer
            }
            else { 
                $arrData[$i]["est"] = '<span class="badge badge-danger">'.$arrData[$i]["DESCRIPCION"].'</span>';
            }
            // BOTONES DE ACCION
            if ($_SESSION["permisos"][0]["MODIFICAR"] == 1){
                $btnEditar = '<button onclick="editarSucursal('.$arrData[$i]['SUCURSAL_ID'].');" class="btn btn-primary btn-sm" title="Editar" type="button"><i class="fa fa-pencil"></i></button>';
            }
            else {
                $btnEditar = '';
            }
            if ($_SESSION["permisos"][0]["BORRAR"] == 1){
                $btnBorrar = '<button onclick="borrarSucursal('.$arrData[$i]['SUCURSAL_ID'].');" class="btn btn-danger btn-sm" title="Eliminar" type="button"><i class="fa fa-trash"></i></button>';
            }
            else {
                $btnBorrar = '';
            }
            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="verSucursal('.$arrData[$i]['SUCURSAL_ID'].');" class="btn btn-info btn-sm" title="Ver" type="button"><i class="fa fa-eye"></i></button> '.
            $btnEditar.' '.$btnBorrar.'</div>';
        }
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function getSucursal(int $productoID){
		$id = intval(strClear($productoID));
        if ($id > 0){
            $arrData = $this->model->selectSucursal($id);
            if (empty($arrData)){
                $arrResponse = array('status' => false, 'message' => 'Datos no encontrados.');
            }
            else {
				$arrData["preciocosto"] = formatMoney($arrData["preciocosto"]);
				$arrData["precioventa"] = formatMoney($arrData["precioventa"]);
				$arrData["cmin"] = formatDecimal($arrData["cmin"]);
				$arrData["cmax"] = formatDecimal($arrData["cmax"]);
				$arrData["cant"] = formatDecimal($arrData["cant"]);
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
	}
	//para usarlo en la vista de combos y factura compra
	public function getSucursalesEmpleados(){
		$arrData = $this->model->selectSoloInsumos();
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function setSucursal(){
		if ($_POST){ // valido post
            if (!empty($_POST["producto_id"]) and is_numeric($_POST["producto_id"]) and intval($_POST["producto_id"]) > 0){ 
				$id = intval($_POST["producto_id"]);
				$option = 1;
			}
			else {
				$option = 2;
			}
            $producto = empty($_POST["productonombre"]) ? "" : mb_strtoupper(strClear($_POST["productonombre"]));
			$codigo = empty($_POST["productocodigo"]) ? "" : strClear($_POST["productocodigo"]);
			$rubro = empty($_POST["productorubro"]) ? "" : strClear($_POST["productorubro"]);
			$udmedida = empty($_POST["productoudmedida"]) ? "" : strClear($_POST["productoudmedida"]);
			$cantmin = empty($_POST["productocantmin"]) ? "" : strClear($_POST["productocantmin"]);
			$cantmax = empty($_POST["productocantmax"]) ? "" : strClear($_POST["productocantmax"]);
			$iva = empty($_POST["productoiva"]) ? "" : strClear($_POST["productoiva"]);
			$precioventa = empty($_POST["productopventa"]) ? 0.00 : floatval(strClear($_POST["productopventa"]));
			$preciocosto = empty($_POST["productopcosto"]) ? 0.00 : floatval(strClear($_POST["productopcosto"]));
			$insumo = empty($_POST["productoinsumo"]) ? 0 : intval($_POST["productoinsumo"]);
			$vendible = empty($_POST["productoventa"]) ? 0 : intval($_POST["productoventa"]);
            $estado = empty($_POST["productoestado"]) ? 0 : intval(strClear($_POST["productoestado"]));
            $imagen = "";
            // Validaciones
            if (empty($producto) or !validar($producto,9,2,50)){
                $arrResponse = array("status" => false, "message" => "El nombre del producto esta vacio o es invalido.");
            }
			elseif (empty($codigo) or !validar($codigo,15,4,10)){
				$arrResponse = array("status" => false, "message" => "El codigo ingresado es invalido o esta vacio.");
			}
			elseif (empty($rubro) or !validar($rubro,2,1,11)){
				$arrResponse = array("status" => false, "message" => "El rubro seleccionado es incorrecto o esta vacio.");
			}
			elseif (empty($udmedida) or !validar($udmedida,2,1,11)){
				$arrResponse = array("status" => false, "message" => "La unidad de medida seleccionado es incorrecto o esta vacio.");
			}
			elseif (empty($cantmin) or !validar($cantmin,10)){
				$arrResponse = array("status" => false, "message" => "La cantidad minima ingresada es incorrecta o esta vacia.");
			}
			elseif (empty($cantmax) or !validar($cantmax,10)){
				$arrResponse = array("status" => false, "message" => "La cantidad maxima ingresada es incorrecta o esta vacia.");
			}
			elseif (empty($iva) or !validar($iva,2,1,11)){
				$arrResponse = array("status" => false, "message" => "El IVA seleccionado es incorrecto o esta vacio.");
			}
			elseif (empty($preciocosto) or !validar($preciocosto,10)){
				$arrResponse = array("status" => false, "message" => "El precio de costo es incorrecto o esta vacio.");
			}
			elseif (empty($precioventa) or !validar($precioventa,10)){
				$arrResponse = array("status" => false, "message" => "El precio de venta es incorrecto o esta vacio.");
			}
			elseif (!validar($insumo,16)){
				$arrResponse = array("status" => false, "message" => "El valor de Insumo es incorrecto.");
			}
			elseif (!validar($vendible,16)){
				$arrResponse = array("status" => false, "message" => "El valor de Venta es incorrecto.");
			}
            elseif (empty($estado) or (intval($estado)>2 or intval($estado)<1)){
				$arrResponse = array("status" => false, "message" => "El estado seleccionado no es valido.");
            }
            else {
                if ($option == 1){ // actualizar
					try {
						$arrData = array($producto, $codigo, $rubro, $udmedida, $cantmin, $cantmax, $iva, $preciocosto, $precioventa, $insumo, $vendible, $estado, $id);
						// $arrResponse = array("status" => false, "message" => $arrData);
						$requestSucursal = $this->model->updateSucursal($arrData);
						// $arrResponse = array("status" => false, "message" => json_encode($requestSucursal,JSON_UNESCAPED_UNICODE));
						if ($requestSucursal){
							$arrResponse = array("status" => true, "message" => "El producto se ha actualizado satisfactoriamente. {$requestSucursal}");
						}
						else {
							$arrResponse = array("status" => false, "message" => "requestUpdate: {$requestSucursal}");
						}
					} catch (Exception $e) {
						$arrResponse = array("status" => false, "message" => "{$e}");
					}
				}
				else { // insertar
					try {
						$arrData = array($producto, $codigo, $rubro, $udmedida, $cantmin, $cantmax, $iva, $preciocosto, $precioventa, $insumo, $vendible, $estado);
						// $arrResponse = array("status" => false, "message" => $arrData);
						$requestSucursal = $this->model->insertSucursal($arrData);
						if ($requestSucursal > 0){
							$arrResponse = array("status" => true, "message" => "El producto se ha dado de alta satisfactoriamente.");
						}
						else {
							$arrResponse = array("status" => false, "message" => "requestInsert: {$requestSucursal}");
						}
					} catch (Exception $e) {
						$arrResponse = array("status" => false, "message" => "{$e}");
					}
				}
            }
        }
        else {
            $arrResponse = array("status" => false, "message" => "Datos no validos!.");
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
	}
	public function delSucursal(){
		if (isset($_POST['id'])){
            $id = intval(strClear($_POST['id']));
            $requestDelete = $this->model->deleteSucursal($id);
            if ($requestDelete == "ok"){
                $arrResponse = array("status" => true, "message" => "Datos borrados.");
            }
            elseif ($requestDelete == 'exist'){
                $arrResponse = array("status" => false, "message" => "No es posible eliminar un producto asignado a sucursales del sistema.");
            }
            else {
                $arrResponse = array("status" => false, "message" => "Error al eliminar los datos.");
            }
        }
        else {
            $arrResponse = array("status" => false, "message" => "Error al recibir datos.");
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
	}
}
?>