<?php 
class Clientes extends Controllers{
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
		getPermisos(3);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard/?m=Clientes");
		}
	}
	public function Clientes(){
		$data["page_id"] = 6;
		$data["page_tag"] = "Clientes | SoftBoos";
		$data["page_title"] = "Clientes";
		$data["page_name"] = "clientes";
		$data["page_filejs"] = "function_clientes.js";
		$this->views->getView($this,"clientes",$data);
	}
	public function getClientes(){
		if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
            $arrData = $this->model->selectClientesMaster();
        }
        else {
            $arrData = $this->model->selectClientes();
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
			$btnEditar = '<button onclick="editarCliente('.$arrData[$i]['CLIENTE_ID'].');" class="btn btn-primary btn-sm" type="button" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Editar"':'disabled title="No tienes permiso para editar"').'><i class="fa fa-pencil"></i></button>';
			if ($arrData[$i]["ESTADO_ID"] == 3 and $_SESSION["userDATA"]["CARGO_ID"] == 1){
				$btnBorrar = '<button onclick="restaurarCliente('.$arrData[$i]['CLIENTE_ID'].');" class="btn btn-success btn-sm" title="Restaurar" type="button"><i class="fa fa-arrow-up"></i></button>';
			} else {
				$btnBorrar = '<button onclick="borrarCliente('.$arrData[$i]['CLIENTE_ID'].');" class="btn btn-danger btn-sm" type="button" '.($_SESSION["permisos"][0]["BORRAR"] == 1?'title="Eliminar"':'disabled title="No tienes permiso para eliminar"').'><i class="fa fa-trash"></i></button>';
			}
			
            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="verCliente('.$arrData[$i]['CLIENTE_ID'].');" class="btn btn-info btn-sm" title="Ver" type="button"><i class="fa fa-eye"></i></button>
			'.$btnEditar.' '.$btnBorrar.' </div>'; 
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function getCliente(int $clienteID){ // pasa el id del cliente por GET
		$id = intval(strClear($clienteID));
		if ($id > 0){
			if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
                $arrData = $this->model->selectClienteMaster($id);
            }
            else {
                $arrData = $this->model->selectCliente($id);
            }
			if (empty($arrData)){
				$arrResponse = array("status" => false, "message" => "Lista vacia.");
			}
			else {
				$arrResponse = array("status" => true, "message" => "ok", "data" => $arrData);
			}
		}
		else {
			$arrResponse = array("status" => false, "message" => "Identificador no válido.");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function setCliente(){
		if ($_POST){
			if (!empty($_POST["cliente_id"]) and is_numeric($_POST["cliente_id"]) and intval($_POST["cliente_id"]) > 0){ 
				$id = intval($_POST["cliente_id"]);
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
			$dni = empty($_POST["clientedni"]) ? "" : strClear($_POST["clientedni"]);
			$fechanac = empty($_POST["clientefechanac"]) ? "" : strClear($_POST["clientefechanac"]);
			$nombre = empty($_POST["clientenombre"]) ? "" : mb_strtoupper(strClear($_POST["clientenombre"]));
			$apellido = empty($_POST["clienteapellido"]) ? "" : mb_strtoupper(strClear($_POST["clienteapellido"]));
			$cuil = empty($_POST["clientecuil"]) ? "" : str_ireplace("-","",strClear($_POST["clientecuil"]));
			$mail = empty($_POST["clientemail"]) ? "" : strClear($_POST["clientemail"]);
			$telefono = empty($_POST["clientetelefono"]) ? "" : strClear($_POST["clientetelefono"]);
			$direccion = empty($_POST["clientedireccion"]) ? "" : mb_strtoupper(strClear($_POST["clientedireccion"]));
			$estado = intval(empty($_POST["clienteestado"]) ? 0 : strClear($_POST["clienteestado"]));
			// primero se validan los campos obligatorios
			if (empty($dni) or !validar($dni,2,7,8)){
				$arrResponse = array("status" => false, "message" => "El numero de DNI ingresado no es valido o esta vacío.");
			}
			elseif (empty($nombre) or !validar($nombre,1,2,50)) {
				$arrResponse = array("status" => false, "message" => "El nombre ingresado no es valido o esta vacío.");
			}
			elseif (empty($apellido) or !validar($apellido,1,2,50)) {
				$arrResponse = array("status" => false, "message" => "El apellido ingresado no es valido o esta vacío.");
			}
			elseif (empty($fechanac) or !validar($fechanac,11)) {
				$arrResponse = array("status" => false, "message" => "La fecha de nacimiento ingresada no es valida o esta vacía.");
			}
			// aca comienza la validacion de los campos que no son obligatorios
			elseif (!empty($cuil) and !validar($cuil,2,10,11)) {
				$arrResponse = array("status" => false, "message" => "El CUIL ingresado no es valido o esta vacío.");
			}
			elseif (!empty($mail) and (!validar($mail,7) or (strlen($mail) < 10 or strlen($mail) > 30))) {
				$arrResponse = array("status" => false, "message" => "El cargo seleccionado no es valido o no selecciono ninguno.");
			}
			elseif (!empty($telefono) and !validar($telefono,3,6,20)) {
				$arrResponse = array("status" => false, "message" => "El nuemro de telefono ingresado no es valido o está vacío.");
			}
			elseif (!empty($direccion) and !validar($direccion,9,10,100)) {
				$arrResponse = array("status" => false, "message" => "La direccion ingresada no es valida o está vacía.");
			}
			elseif (!empty($estado) and !validar($estado,2,1,11)) {
				$arrResponse = array("status" => false, "message" => "El estado ingresado no es valida o está vacía.");
			}
			else {
				if ($id > 0){ // actualizar
					$arrData = array($dni, $nombre, $apellido, $fechanac, $cuil, $telefono, $mail, $direccion, $estado, $id);
					$requestCliente = $this->model->updateCliente($arrData);
					if ($requestCliente > 0){
						$arrResponse = array("status" => true, "message" => "El cliente se ha actualizado satisfactoriamente.", "data" => $requestCliente);
					}
				}
				else { // insertar
					$arrData = array($dni, $nombre, $apellido, $fechanac, $cuil, $telefono, $mail, $direccion, $estado);
					$requestCliente = $this->model->insertCliente($arrData);
					if ($requestCliente > 0){
						$arrResponse = array("status" => true, "message" => "El cliente se ha dado de alta satisfactoriamente.", "data" => $requestCliente);
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
	public function delCliente(){
		if ($_SESSION["permisos"][0]["BORRAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para borrar registros en este módulo.");
		}
		elseif (isset($_POST["id"]) and is_numeric($_POST["id"])){
			$id = intval($_POST["id"]);
			$arrRequest = $this->model->deleteCliente($id);
			if ($arrRequest == "ok"){
                $arrResponse = array("status" => true, "message" => "Cliente borrado correctamente.");
            }
            elseif ($arrRequest == "exist"){
                $arrResponse = array("status" => false, "message" => "No es posible eliminar un cliente con una o mas ventas a su nombre.");
            }
            else {
                $arrResponse = array("status" => false, "message" => "Error al eliminar al cliente.");
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
            $id = empty($_POST['idCliente']) ? 0 : intval(strClear($_POST['idCliente']));
            if ($id > 0){
                try {
                    $requestRestaurar = $this->model->restaurarCliente($id);
                    if ($requestRestaurar > 0){
                        $arrResponse = array("status" => true, "message" => "El cliente ha sido restaurado");
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "El cliente NO ha sido restaurado");
                    }
                }
                catch (Exception $e){
                    $arrResponse = array("status" => false, "message" => "Se ha producido un error", "details" => array($e->getMessage()));
                }
            }
            else {
                $arrResponse = array("status" => false, "message" => "El 'id' del cliente no es valido");
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