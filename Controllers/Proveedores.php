<?php 
class Proveedores extends Controllers{
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
		getPermisos(9);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard/?m=Proveedores");
		}
	}
	public function Proveedores(){
		$data["page_id"] = 9;
		$data["page_tag"] = "Proveedores | SoftBoos";
		$data["page_title"] = "Proveedores";
		$data["page_name"] = "proveedores";
		$data["page_filejs"] = "function_proveedores.js";
		$this->views->getView($this,"proveedores",$data);
	}
	public function getProveedores(){
		if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
            $arrData = $this->model->selectProveedoresMaster();
        }
        else {
            $arrData = $this->model->selectProveedores();
		}
        for ($i=0; $i < count($arrData); $i++) { 
			if ($arrData[$i]["ESTADO_ID"] == 1){ // activo
                $arrData[$i]["estado"] = '<span class="badge badge-success">Activo</span>';
            }
            elseif ($arrData[$i]["ESTADO_ID"] == 2){ // inactivo
                $arrData[$i]["estado"] = '<span class="badge badge-danger">Inactivo</span>';
            }
            elseif ($arrData[$i]["ESTADO_ID"] == 3){ // borrado
                $arrData[$i]["estado"] = '<span class="badge badge-warning">Borrado</span>';
            }
            else { // dato no controlado
                $arrData[$i]["estado"] = '<span class="badge badge-danger">WTF</span>';
            }
			$btnEditar = '<button onclick="editarProveedor('.$arrData[$i]['PROVEEDOR_ID'].');" class="btn btn-primary btn-sm" type="button" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Editar"':'disabled title="No tienes permiso para editar"').'><i class="fa fa-pencil"></i></button>';
			if($arrData[$i]["ESTADO_ID"] == 3 and $_SESSION["userDATA"]["CARGO_ID"] == 1){
				$btnBorrar = '<button onclick="restaurarProveedor('.$arrData[$i]['PROVEEDOR_ID'].');" class="btn btn-success btn-sm" title="Restaurar" type="button"><i class="fa fa-arrow-up"></i></button>';
			}
			else {
				$btnBorrar = '<button onclick="borrarProveedor('.$arrData[$i]['PROVEEDOR_ID'].');" class="btn btn-danger btn-sm" type="button" '.($_SESSION["permisos"][0]["BORRAR"] == 1?'title="Eliminar"':'disabled title="No tienes permiso para eliminar"').'><i class="fa fa-trash"></i></button>';
			}
            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="verProveedor('.$arrData[$i]['PROVEEDOR_ID'].');" class="btn btn-info btn-sm" title="Ver" type="button"><i class="fa fa-eye"></i></button>'.
            $btnEditar.' '.$btnBorrar.'</div>'; 
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
	}
	public function getProveedor(int $proveedorID){ 
		$id = intval(strClear($proveedorID));
		if ($id > 0){
			if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
				$arrData = $this->model->selectProveedorMaster($id);
			}
			else {
				$arrData = $this->model->selectProveedor($id);
			}
			if (empty($arrData)){
				$arrResponse = array("status" => false, "message" => "Lista vacia.");
			}
			else {
				$arrResponse = array("status" => true, "message" => "ok", "data" => $arrData);
			}
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function setProveedor(){
		if ($_POST){
			if (!empty($_POST["proveedor_id"]) and is_numeric($_POST["proveedor_id"]) and intval($_POST["proveedor_id"]) > 0){ 
				$id = intval($_POST["proveedor_id"]);
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
			$razonsocial = empty($_POST["proveedorrazonSocial"]) ? "" : mb_strtoupper(strClear($_POST["proveedorrazonSocial"]));
			$cuit = empty($_POST["proveedorcuit"]) ? "" : str_ireplace("-","",strClear($_POST["proveedorcuit"]));
			$mail = empty($_POST["proveedormail"]) ? "" : strClear($_POST["proveedormail"]);
			$telefono = empty($_POST["proveedortelefono"]) ? "" : strClear($_POST["proveedortelefono"]);
			$direccion = empty($_POST["proveedordireccion"]) ? "" : mb_strtoupper(strClear($_POST["proveedordireccion"]));
			$web = empty($_POST["proveedorweb"]) ? "" : strClear($_POST["proveedorweb"]);
			$estado = intval(empty($_POST["proveedorestado"]) ? 0 : strClear($_POST["proveedorestado"]));
			// primero se validan los campos obligatorios
			if (empty($razonsocial) or !validar($razonsocial,1,3,20)){
				$arrResponse = array("status" => false, "message" => "La razon social ingresada no es valida o esta vacía.");
			}
			// aca comienza la validacion de los campos que no son obligatorios
			elseif (!empty($cuit) and !validar($cuit,2,10,11)) {
				$arrResponse = array("status" => false, "message" => "El CUIT ingresado no es valido o esta vacío.");
			}
			elseif (!empty($mail) and (!validar($mail,7) or (strlen($mail) < 10 or strlen($mail) > 30))) {
				$arrResponse = array("status" => false, "message" => "El correo electronico no es valido o no selecciono ninguno.");
			}
			elseif (!empty($telefono) and !validar($telefono,3,6,20)) {
				$arrResponse = array("status" => false, "message" => "El nuemro de telefono ingresado no es valido o está vacío.");
			}
			elseif (!empty($direccion) and !validar($direccion,9,10,100)) {
				$arrResponse = array("status" => false, "message" => "La direccion ingresada no es valida o está vacía.");
			}
			elseif (!empty($web) and !validar($web,13,5,100)) {
				$arrResponse = array("status" => false, "message" => "La direccion web ingresada no es valida o esta vacía.");
			}
			elseif (!empty($estado) and !validar($estado,2,1,11)) {
				$arrResponse = array("status" => false, "message" => "El estado ingresado no es valida o está vacía.");
			}
			else {
				if ($id > 0){ // actualizar
					$arrData = array($razonsocial, $cuit, $mail, $telefono, $web, $direccion, $estado, $id);
					$requestProveedor = $this->model->updateProveedor($arrData);
					if ($requestProveedor > 0){
						$arrResponse = array("status" => true, "message" => "El proveedor se ha actualizado satisfactoriamente.", "data" => $requestProveedor);
					}
				}
				else { // insertar
					$arrData = array($razonsocial, $cuit, $mail, $telefono, $web, $direccion, $estado);
					$requestProveedor = $this->model->insertProveedor($arrData);
					if ($requestProveedor > 0){
						$arrResponse = array("status" => true, "message" => "El proveedor se ha dado de alta satisfactoriamente.", "data" => $requestProveedor);
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
	public function delProveedor(){
		if ($_SESSION["permisos"][0]["BORRAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para borrar registros en este módulo.");
		}
		elseif (isset($_POST["id"]) and is_numeric($_POST["id"])){
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
	public function setRestaurar(){
        if($_SESSION["userDATA"]["CARGO_ID"] != 1){
            $arrResponse = array("status" => false, "message" => "Usted no tiene permisos para realizar esta acción.");
        }
        elseif ($_POST){
            $id = empty($_POST['idProveedor']) ? 0 : intval(strClear($_POST['idProveedor']));
            if ($id > 0){
                try {
                    $requestRestaurar = $this->model->restaurarProveedor($id);
                    if ($requestRestaurar > 0){
                        $arrResponse = array("status" => true, "message" => "El proveedor ha sido restaurado");
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "El proveedor NO ha sido restaurado");
                    }
                }
                catch (Exception $e){
                    $arrResponse = array("status" => false, "message" => "Se ha producido un error", "details" => array($e->getMessage()));
                }
            }
            else {
                $arrResponse = array("status" => false, "message" => "El 'id' del proveedor no es valido");
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