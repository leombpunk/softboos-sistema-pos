<?php 
class Productos extends Controllers{
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
		getPermisos(5);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard/?m=Productos");
		}
	}
	public function Productos(){
		$data["page_id"] = 5;
		$data["page_tag"] = "Productos | SoftBoos";
		$data["page_title"] = "Productos";
		$data["page_name"] = "productos";
		$data["page_filejs"] = "function_productos.js";
		$data["page_specialjs"] = array('<script src="'.media().'js/ckeditor/ckeditor.js" type="text/javascript"></script>');
		$this->views->getView($this,"productos",$data);
	}
	public function getProductosFacturaVenta(){
		$arrData = $this->model->selectSoloProductos();
		for ($i=0; $i < count($arrData); $i++){
			$arrData[$i]["cant"] = '<input type="number" name="prodcutoCant'.$arrData[$i]['id'].'" id="prodcutoCant'.$arrData[$i]['id'].'" class="form-control" value=0.0 />';
			$arrData[$i]["action"] = '<div class="text-center"><button type="button" onclick="agregarProducto('.$arrData[$i]['id'].');" title="Agregar Producto" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></button></div>';
		}
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function getProductosFacturaCompra(){
		$arrData = $this->model->selectProductosCompra();
		for ($i=0; $i < count($arrData); $i++){
			$arrData[$i]["preciocosto2"] = '<input type="number" name="prodcutoCosto'.$arrData[$i]['id'].'" id="prodcutoCosto'.$arrData[$i]['id'].'" class="form-control" value='.$arrData[$i]["preciocosto"].' />';
			$arrData[$i]["cant"] = '<input type="number" name="prodcutoCant'.$arrData[$i]['id'].'" id="prodcutoCant'.$arrData[$i]['id'].'" class="form-control" value=0.0 />';
			$arrData[$i]["action"] = '<div class="text-center"><button type="button" onclick="agregarProducto('.$arrData[$i]['id'].');" title="Agregar Producto" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></button></div>';
		}
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	//para usarlo en la vista factura compra
	public function getProductos(){
		if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
            $arrData = $this->model->selectProductosMaster();
        }
        else {
            $arrData = $this->model->selectProductos();
        }
		for ($i=0; $i < count($arrData); $i++) {
			$arrData[$i]["cant"] = floatval(formatDecimal($arrData[$i]["cant"]));
			$arrData[$i]["precioventa"] = formatMoney(floatval($arrData[$i]["precioventa"])); 
            if ($arrData[$i]["est"] == 1){ // activo
                $arrData[$i]["est"] = '<span class="badge badge-success">Activo</span>';
            }
            elseif ($arrData[$i]["est"] == 2){ // inactivo
                $arrData[$i]["est"] = '<span class="badge badge-danger">Inactivo</span>';
            }
            elseif ($arrData[$i]["est"] == 3){ // borrado
                $arrData[$i]["est"] = '<span class="badge badge-warning">Borrado</span>';
            }
            else { // dato no controlado
                $arrData[$i]["est"] = '<span class="badge badge-danger">WTF</span>';
            }

            $btnEditar = '<button onclick="editarProducto('.$arrData[$i]['id'].');" class="btn btn-primary btn-sm" type="button" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Editar"':'disabled title="No tienes permiso para editar"').'><i class="fa fa-pencil"></i></button>';
            if ($arrData[$i]["est"] == 3 and $_SESSION["userDATA"]["CARGO_ID"] == 1){
                $btnBorrar = '<button onclick="restaurarProducto('.$arrData[$i]['id'].');" class="btn btn-success btn-sm" type="button"><i class="fa fa-arrow-up"></i></button>';
            }
            else {
                $btnBorrar = '<button onclick="borrarProducto('.$arrData[$i]['id'].');" class="btn btn-danger btn-sm" type="button" '.($_SESSION["permisos"][0]["BORRAR"] == 1?'title="Eliminar"':'disabled title="No tienes permiso para eliminar"').'><i class="fa fa-trash"></i></button>';
            }
            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="verProducto('.$arrData[$i]['id'].');" class="btn btn-info btn-sm" title="Ver" type="button"><i class="fa fa-eye"></i></button> '.
            $btnEditar.' '.$btnBorrar.'</div>';
        }
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function getProducto(int $productoID){
		$id = intval(strClear($productoID));
        if ($id > 0){
			if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
				$arrData = $this->model->selectProductoMaster($id);
			}
			else {
				$arrData = $this->model->selectProducto($id);
			}
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
	public function getSoloInsumos(){
		$arrData = $this->model->selectSoloInsumos();
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	//para usarlo e la vista de combos y factura venta
	public function getSoloProductos(){
		$arrData = $this->model->selectSoloProductos();
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	//----**estas funciones quizas sea mejor ponerlas en otra clase**----
	public function getRubros(){
		$arrData = $this->model->selectRubros();
		if (!empty($arrData)){
			$arrResponse = array("status" => true, "data" => array("<option value='' selected>Seleccione...</option>"));
			foreach ($arrData as $key => $value) {
				array_push($arrResponse["data"],"<option value='".$value["RUBRO_ID"]."'>".$value["NOMBRE"]."</option>");
			}
		}
		else {
			$arrResponse = array("status" => false, "message" => "Lista de rubros vacia.");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function getUnMedidas(){
		$arrData = $this->model->selectUnMedidas();
		if (!empty($arrData)){
			$arrResponse = array("status" => true, "data" => array("<option value='' selected>Seleccione...</option>"));
			foreach ($arrData as $key => $value) {
				array_push($arrResponse["data"],"<option value='".$value["UNIMEDIDA_ID"]."'>".$value["NOMBRE"]."</option>");
			}
		}
		else {
			$arrResponse = array("status" => false, "message" => "Lista de unidades de medidas vacia.");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function getIVA(){
		$arrData = $this->model->selectIVA();
		if (!empty($arrData)){
			$arrResponse = array("status" => true, "data" => array("<option value='' selected>Seleccione...</option>"));
			foreach ($arrData as $key => $value) {
				array_push($arrResponse["data"],"<option value='".$value["IVA_ID"]."'>".$value["IVA_PORCENTAJE"]." %</option>");
			}
		}
		else {
			$arrResponse = array("status" => false, "message" => "Lista de IVA vacia.");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}
	//------------------------------**hasta aca**------------------------------
	public function setProducto(){
		if ($_POST){ // valido post
            if (!empty($_POST["producto_id"]) and is_numeric($_POST["producto_id"]) and intval($_POST["producto_id"]) > 0){ 
				$id = intval($_POST["producto_id"]);
				if ($_SESSION["permisos"][0]["MODIFICAR"] == 0){
					$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para editar registros en este módulo.");
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                    die();
				}
			}
			else {
				$id = 0;
				if ($_SESSION["permisos"][0]["AGREGAR"] == 0){
					$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para crear registros en este módulo.");
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                    die();
				}
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
            $imagenes = "";
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
				try {
					if ($id > 0){ // actualizar
						$arrData = array($producto, $codigo, $rubro, $udmedida, $cantmin, $cantmax, $iva, $preciocosto, $precioventa, $insumo, $vendible, $estado, $id);
						$requestProducto = $this->model->updateProducto($arrData);
						if ($requestProducto == -1){
							$arrResponse = array("status" => false, "message" => "El codigo ingresado esta duplicado. Code: {$requestProducto}");
						}
						elseif ($requestProducto){
							$arrResponse = array("status" => true, "message" => "El producto se ha actualizado satisfactoriamente. {$requestProducto}");
						}
						else {
							$arrResponse = array("status" => false, "message" => "requestUpdate: {$requestProducto}");
						}
					}
					else { // insertar
						$arrData = array($producto, $codigo, $rubro, $udmedida, $cantmin, $cantmax, $iva, $preciocosto, $precioventa, $insumo, $vendible, $estado);
						$requestProducto = $this->model->insertProducto($arrData);
						if ($requestProducto > 0){
							$arrResponse = array("status" => true, "message" => "El producto se ha dado de alta satisfactoriamente.");
						}
						else {
							$arrResponse = array("status" => false, "message" => "requestInsert: {$requestProducto}");
						}
					}
				}
				catch (Exception $e){
					$arrResponse = array("status" => false, "message" => "{$e->getMessage()}");
				}
            }
        }
        else {
            $arrResponse = array("status" => false, "message" => "Datos no validos!.");
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
	}
	public function delProducto(){
		if ($_SESSION["permisos"][0]["BORRAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para borrar registros en este módulo.");
		}
		elseif (isset($_POST['id'])){
            $id = intval(strClear($_POST['id']));
            $requestDelete = $this->model->deleteProducto($id);
            if ($requestDelete == "ok"){
                $arrResponse = array("status" => true, "message" => "Datos borrados.");
            }
            elseif ($requestDelete == 'exist'){
                $arrResponse = array("status" => false, "message" => "No es posible eliminar un producto asignado a productos del sistema.");
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
	public function getProductoFull(int $productoID){
		$id = intval(strClear($productoID));
        if ($id > 0){
            $arrData = $this->model->selectProductoFull($id);
            if (empty($arrData)){
                $arrResponse = array('status' => false, 'message' => 'Datos no encontrados.');
            }
            else {
				//hacer un for para darle formato legible al return
				$data = [];
				foreach ($arrData as $key => $value) {
					if ($key == 0){
						//cargar el array con los datos basicos del producto
					}
					else {
						//agregar al array correspondiente los valores
						//(unidad de media, rubro, cantidad)
					}
					echo "llave: ".$key;
					echo "valor: ".$value;
				}
                $arrResponse = array('status' => true, 'data' => $data);
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
	}
	public function setRestaurar(){
        if($_SESSION["userDATA"]["CARGO_ID"] != 1){
            $arrResponse = array("status" => false, "message" => "Usted no tiene permisos para realizar esta acción.");
        }
        elseif ($_POST){
            $id = empty($_POST['idProducto']) ? 0 : intval(strClear($_POST['idProducto']));
            if ($id > 0){
                try {
                    $requestRestaurar = $this->model->restaurarProducto($id);
                    if ($requestRestaurar > 0){
                        $arrResponse = array("status" => true, "message" => "El producto ha sido restaurado");
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "El producto NO ha sido restaurado");
                    }
                }
                catch (Exception $e){
                    $arrResponse = array("status" => false, "message" => "Se ha producido un error", "details" => array($e->getMessage()));
                }
            }
            else {
                $arrResponse = array("status" => false, "message" => "El 'id' del producto no es valido");
            }
        }
        else {
            $arrResponse = array("status" => false, "message" => "Datos no validos");
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
}
?>