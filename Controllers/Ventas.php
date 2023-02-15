<?php 
class Ventas extends Controllers{
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
		getPermisos(10);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard");
		}
	}
	public function Ventas(){
		// if (empty($_SESSION["permisosMod"]["r"])){
		// 	header("location:".base_url()."dashboard");
		// }
		$data["page_id"] = 10;
		$data["page_tag"] = "Ventas | SoftBoos";
		$data["page_title"] = "Ventas";
		$data["page_name"] = "ventas";
		$data["page_filejs"] = "function_ventas.js";
		$this->views->getView($this,"ventas",$data);
	}
	public function getProveedores(){
		$arrData = $this->model->selectProveedores();
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
            <button onclick="verProveedor('.$arrData[$i]['PROVEEDOR_ID'].');" class="btn btn-info btn-sm btnVerproveedor" rl="'.$arrData[$i]['PROVEEDOR_ID'].'" title="Ver" type="button"><i class="fa fa-eye"></i></button>
            <button onclick="editarProveedor('.$arrData[$i]['PROVEEDOR_ID'].');" class="btn btn-primary btn-sm btnEditarCargo" rl="'.$arrData[$i]['PROVEEDOR_ID'].'" title="Editar" type="button"><i class="fa fa-pencil"></i></button>
            <button onclick="borrarProveedor('.$arrData[$i]['PROVEEDOR_ID'].');" class="btn btn-danger btn-sm btnBorrarCargo" rl="'.$arrData[$i]['PROVEEDOR_ID'].'" title="Eliminar" type="button"><i class="fa fa-trash"></i></button>
            </div>'; 
        }
        // dep($arrData);
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
	}
	public function getProveedor(int $proveedorID){ // pasa el id del proveedor por GET
		$id = intval(strClear($proveedorID));
		if ($id > 0){
			$arrData = $this->model->selectProveedor($id);
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
				$option = 1;
			}
			else {
				$option = 2;
			}
			// podria asignar variables antes de validarlas
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
				if ($option == 1){ // actualizar
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
		if (isset($_POST["id"]) and is_numeric($_POST["id"])){
			$this->id = intval($_POST["id"]);
			$arrRequest = $this->model->deleteProveedor($this->id);
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
}
?>