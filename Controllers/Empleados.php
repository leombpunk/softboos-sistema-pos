<?php
class Empleados extends Controllers{
	private $id;
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
		getPermisos(4);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard/?m=Empleados");
		}
	}
	public function Empleados(){
		$data["page_id"] = 4;
		$data["page_tag"] = "Empleados | SoftBoos";
		$data["page_title"] = "Empleados";
		$data["page_name"] = "empleados";
		$data["page_filejs"] = "function_empleados.js";
		$this->views->getView($this,"empleados",$data);
	}
	public function perfil(){
		$data["page_id"] = 5;
		$data["page_tag"] = "Perfil | SoftBoos";
		$data["page_title"] = "Perfil";
		$data["page_name"] = "perfil";
		$data["page_filejs"] = "function_empleados.js";
		$this->views->getView($this,"perfil",$data);
	}
	public function getEmpleados(){
		if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
            $arrData = $this->model->selectEmpleadosMaster();
        }
        else {
            $arrData = $this->model->selectEmpleados();
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
			$btnEditar = '<button onclick="editarEmpleado('.$arrData[$i]['EMPLEADO_ID'].');" class="btn btn-primary btn-sm" type="button"><i class="fa fa-pencil" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Editar"':'disabled title="No tienes permiso para editar"').'></i></button>';
			if($arrData[$i]["ESTADO_ID"] == 3 and $_SESSION["userDATA"]["CARGO_ID"] == 1){
				$btnBorrar = '<button onclick="restaurarEmpleado('.$arrData[$i]['EMPLEADO_ID'].');" class="btn btn-success btn-sm" title="Restaurar" type="button"><i class="fa fa-arrow-up"></i></button>';
			}
			else {
				$btnBorrar = '<button onclick="borrarEmpleado('.$arrData[$i]['EMPLEADO_ID'].');" class="btn btn-danger btn-sm" type="button"><i class="fa fa-trash" '.($_SESSION["permisos"][0]["BORRAR"] == 1?'title="Eliminar"':'disabled title="No tienes permiso para eliminar"').'></i></button>';
			}

            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="verEmpleado('.$arrData[$i]['EMPLEADO_ID'].');" class="btn btn-info btn-sm" title="Ver" type="button"><i class="fa fa-eye"></i></button> '.
            $btnEditar.' '.$btnBorrar.' </div>'; 
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function getEmpleado(int $empleadoID){
		$id = intval(strClear($empleadoID));
		if ($id > 0){
			if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
				$arrData = $this->model->selectEmpleadoMaster($id);
			}
			else {
				$arrData = $this->model->selectEmpleado($id);
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
	public function getSucursales(){
		$arrData = $this->model->selectSucursales();
		if (empty($arrData)) {
			$arrResponse = array("status" => false, "message" => "No se encontraron sucursales", "data" => []);
		} 
		else {
			$arrResponse = array("status" => true, "message" => "ok", "data" => $arrData);
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function getCargosDiff(){
		$arrData = $this->model->selectCargosDiff();
		if (empty($arrData)){
			$arrResponse = array("status" => false, "message" => "No se pueden recuperar los datos.");
		}
		else {
			$arrResponse = array("status" => true, "message" => "ok", "data" => $arrData);
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function setEmpleado(){ // recibe datos por POST
		// $arrResponse = array("status" => false, "message" => "Algo malio sal!", "data" => $_POST);
		// echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		// die();
		if ($_POST){
			if (!empty($_POST["empleado_id"]) and is_numeric($_POST["empleado_id"]) and intval($_POST["empleado_id"]) > 0){ 
				$id = intval($_POST["empleado_id"]);
				if ($_SESSION["permisos"][0]["MODIFICAR"] == 0){
					$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para editar registros en este módulo.");
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                    die();
				}
			}
			else {
				if ($_SESSION["permisos"][0]["AGREGAR"] == 0){
					$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para crear registros en este módulo.");
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                    die();
				}
			}
			// podria asignar variables antes de validarlas
			$dni = empty($_POST["empleadodni"]) ? "" : strClear($_POST["empleadodni"]);
			$fechanac = empty($_POST["empleadofechanac"]) ? "" : strClear($_POST["empleadofechanac"]);
			$nombre = empty($_POST["empleadonombre"]) ? "" : strClear($_POST["empleadonombre"]);
			$apellido = empty($_POST["empleadoapellido"]) ? "" : strClear($_POST["empleadoapellido"]);
			$cuil = empty($_POST["empleadocuil"]) ? "" : str_ireplace("-","",strClear($_POST["empleadocuil"]));
			$pass = empty($_POST["empleadopassword"]) ? "" : strClear($_POST["empleadopassword"]);
			$cargo = empty($_POST["empleadocargo"]) ? "" : strClear($_POST["empleadocargo"]);
			$mail = empty($_POST["empleadomail"]) ? "" : strClear($_POST["empleadomail"]);
			$telefono = empty($_POST["empleadotelefono"]) ? "" : strClear($_POST["empleadotelefono"]);
			$estado = empty($_POST["empleadoestado"]) ? "" : strClear($_POST["empleadoestado"]);
			$direccion = empty($_POST["empleadodireccion"]) ? "" : strClear($_POST["empleadodireccion"]);
			$sucursal = empty($_POST["empleadosucursal"]) ? "" : strClear($_POST["empleadoestado"]);
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
			elseif ($id > 0 and (empty($cuil) or !validar($cuil,2,10,11))) {
				$arrResponse = array("status" => false, "message" => "El CUIL ingresado no es valido o esta vacío.");
			}
			elseif ($id > 0 and (empty($pass) or !validar($pass,6,8,16))) {
				$arrResponse = array("status" => false, "message" => "La contraseña ingresada no es valida o esta vacía.");
			}
			elseif (empty($cargo) or !validar($cargo,2,1,11)) {
				$arrResponse = array("status" => false, "message" => "El cargo seleccionado no es valido o no selecciono ninguno.");
			}
			elseif (empty($estado) or (intval($estado)>2 and intval($estado)<1)){
				$arrResponse = array("status" => false, "message" => "El estado seleccionado no es valido o no selecciono ninguno.");
			}
			// aca comienza la validacion de los campos que no son obligatorios
			elseif (!empty($mail) and (!validar($mail,7) or (strlen($mail) < 10 or strlen($mail) > 30))) {
				$arrResponse = array("status" => false, "message" => "El correo electronico no es valido o esta vacio.");
			}
			elseif (!empty($telefono) and !validar($telefono,3,6,20)) {
				$arrResponse = array("status" => false, "message" => "El nuemro de telefono ingresado no es valido o está vacío.");
			}
			elseif (!empty($direccion) and !validar($direccion,9,10,100)) {
				$arrResponse = array("status" => false, "message" => "La direccion ingresada no es valida o está vacía.");
			}
			elseif (empty($sucursal) or !validar($sucursal,2,1,11)) {
				$arrResponse = array("status" => false, "message" => "La sucursal seleccionada no es valida o no selecciono ninguna.");
			}
			else {
				if ($id > 0){ // actualizar
					$arrData = array($sucursal, $cargo, $estado, $dni, $nombre, $apellido, $fechanac, $mail, $telefono, $direccion, $id);
					$requestEmpleado = $this->model->updateEmpleado($arrData);
					if ($requestEmpleado > 0){
						$arrResponse = array("status" => true, "message" => "El empleado se ha actualizado satisfactoriamente.");
					}
				}
				else { // insertar
					$arrData = array($sucursal, $cargo, $estado, $dni, $nombre, $apellido, $fechanac, $cuil, $pass, $mail, $telefono, $direccion);
					$requestEmpleado = $this->model->insertEmpleado($arrData);
					if ($requestEmpleado > 0){
						$arrResponse = array("status" => true, "message" => "El empleado se ha dado de alta satisfactoriamente.");
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
	public function delEmpleado(){
		if ($_SESSION["permisos"][0]["BORRAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para borrar registros en este módulo.");
		}
		elseif (isset($_POST["id"]) and is_numeric($_POST["id"])){
			$this->id = intval($_POST["id"]);
			$arrRequest = $this->model->deleteEmpleado($this->id);
			if ($arrRequest == "ok"){
                $arrResponse = array("status" => true, "message" => "Empleado borrado correctamente.");
            }
            elseif ($arrRequest == 'exist'){
                $arrResponse = array("status" => false, "message" => "No es posible eliminar al Administrador del sistema.");
            }
            else {
                $arrResponse = array("status" => false, "message" => "Error al eliminar al empleado.");
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
            $id = empty($_POST['idEmpleado']) ? 0 : intval(strClear($_POST['idEmpleado']));
            if ($id > 0){
                try {
                    $requestRestaurar = $this->model->restaurarEmpleado($id);
                    if ($requestRestaurar > 0){
                        $arrResponse = array("status" => true, "message" => "El empleado ha sido restaurado");
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "El empleado NO ha sido restaurado");
                    }
                }
                catch (Exception $e){
                    $arrResponse = array("status" => false, "message" => "Se ha producido un error", "details" => array($e->getMessage()));
                }
            }
            else {
                $arrResponse = array("status" => false, "message" => "El 'id' del empleado no es valido");
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