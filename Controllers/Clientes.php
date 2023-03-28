<?php 
class Clientes extends Controllers{
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
		getPermisos(3);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard");
		}
	}
	public function Clientes(){
		// if (empty($_SESSION["permisosMod"]["r"])){
		// 	header("location:".base_url()."dashboard");
		// }
		$data["page_id"] = 6;
		$data["page_tag"] = "Clientes | SoftBoos";
		$data["page_title"] = "Clientes";
		$data["page_name"] = "clientes";
		$data["page_filejs"] = "function_clientes.js";
		$this->views->getView($this,"clientes",$data);
	}
	public function getClientes(){
		$arrData = $this->model->selectClientes();
        for ($i=0; $i < count($arrData); $i++) { 
			if ($arrData[$i]["ESTADO_ID"] == 1){ // activo
                $arrData[$i]["estado"] = '<span class="badge badge-success">Activo</span>';
            }
            elseif ($arrData[$i]["ESTADO_ID"] == 2){ // inactivo
                $arrData[$i]["estado"] = '<span class="badge badge-danger">Inactivo</span>';
            }
            elseif ($arrData[$i]["ESTADO_ID"] == 3){ // borrado
                $arrData[$i]["estado"] = '<span class="badge badge-warning">Borrado</span>';
                // agregar el cambio de funcion y boton de borrar a restablecer
            }
            else { // dato no controlado
                $arrData[$i]["estado"] = '<span class="badge badge-danger">WTF</span>';
            }
            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="verCliente('.$arrData[$i]['CLIENTE_ID'].');" class="btn btn-info btn-sm btnVercliente" rl="'.$arrData[$i]['CLIENTE_ID'].'" title="Ver" type="button"><i class="fa fa-eye"></i></button>
            <button onclick="editarCliente('.$arrData[$i]['CLIENTE_ID'].');" class="btn btn-primary btn-sm btnEditarCargo" rl="'.$arrData[$i]['CLIENTE_ID'].'" title="Editar" type="button"><i class="fa fa-pencil"></i></button>
            <button onclick="borrarCliente('.$arrData[$i]['CLIENTE_ID'].');" class="btn btn-danger btn-sm btnBorrarCargo" rl="'.$arrData[$i]['CLIENTE_ID'].'" title="Eliminar" type="button"><i class="fa fa-trash"></i></button>
            </div>'; 
        }
        // dep($arrData);
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
	}
	public function getCliente(int $clienteID){ // pasa el id del cliente por GET
		$id = intval(strClear($clienteID));
		if ($id > 0){
			$arrData = $this->model->selectCliente($id);
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
	public function setCliente(){
		if ($_POST){
			if (!empty($_POST["cliente_id"]) and is_numeric($_POST["cliente_id"]) and intval($_POST["cliente_id"]) > 0){ 
				$id = intval($_POST["cliente_id"]);
				$option = 1;
			}
			else {
				$option = 2;
			}
			// podria asignar variables antes de validarlas
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
				if ($option == 1){ // actualizar
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
		if (isset($_POST["id"]) and is_numeric($_POST["id"])){
			$id = intval($_POST["id"]);
			$arrRequest = $this->model->deleteCliente($id);
			if ($arrRequest == "ok"){
                $arrResponse = array("status" => true, "message" => "Cliente borrado correctamente.");
            }
            // elseif ($arrRequest == 'exist'){
            //     $arrResponse = array("status" => false, "message" => "No es posible eliminar al Administrador del sistema.");
            // }
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
}
?>