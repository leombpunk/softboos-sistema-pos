<?php 
class Sucursales extends Controllers{
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
		getPermisos(16);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard/?m=Sucursales");
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
		if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
            $arrData = $this->model->selectSucursalesMaster();
        }
        else {
            $$arrData = $this->model->selectSucursales();
		}
		for ($i=0; $i < count($arrData); $i++) {
            if ($arrData[$i]["ESTADO_ID"] == 1){
                $arrData[$i]["estado"] = '<span class="badge badge-success">'.$arrData[$i]["DESCRIPCION"].'</span>';
            }
            elseif ($arrData[$i]["ESTADO_ID"] == 2){
                $arrData[$i]["estado"] = '<span class="badge badge-danger">'.$arrData[$i]["DESCRIPCION"].'</span>';
            }
            elseif ($arrData[$i]["ESTADO_ID"] == 3){
                $arrData[$i]["estado"] = '<span class="badge badge-warning">'.$arrData[$i]["DESCRIPCION"].'</span>';
            }
            else { 
                $arrData[$i]["estado"] = '<span class="badge badge-danger">'.$arrData[$i]["DESCRIPCION"].'</span>';
            }
            // BOTONES DE ACCION
            $btnEditar = '<button onclick="editarSucursal('.$arrData[$i]['SUCURSAL_ID'].');" class="btn btn-primary btn-sm" type="button" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Editar"':'disabled title="No tienes permiso para editar"').'><i class="fa fa-pencil"></i></button>';
			if($arrData[$i]["ESTADO_ID"] == 3 and $_SESSION["userDATA"]["CARGO_ID"] == 1){//está borrado
				$btnBorrar = '<button onclick="restaurarSucursal('.$arrData[$i]['SUCURSAL_ID'].');" class="btn btn-success btn-sm" title="Restaurar" type="button"><i class="fa fa-arrow-up"></i></button>';
			} else {
				$btnBorrar = '<button onclick="borrarSucursal('.$arrData[$i]['SUCURSAL_ID'].');" class="btn btn-danger btn-sm" type="button" '.($_SESSION["permisos"][0]["BORRAR"] == 1?'title="Eliminar"':'disabled title="No tienes permiso para eliminar"').'><i class="fa fa-trash"></i></button>';
			}
            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="verSucursal('.$arrData[$i]['SUCURSAL_ID'].');" class="btn btn-info btn-sm" title="Ver" type="button"><i class="fa fa-eye"></i></button>
			<button onclick="verSucursalEmpleados('.$arrData[$i]['SUCURSAL_ID'].',\''.$arrData[$i]['nombre'].'\');" class="btn btn-outline-secondary btn-sm" title="Ver Empelados" type="button"><i class="fa fa-list"></i></button> '.
            $btnEditar.' '.$btnBorrar.'</div>';
        }
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function getSucursal(int $sucursalID){
		$id = intval(strClear($sucursalID));
        if ($id > 0){
			if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
				$arrData = $this->model->selectSucursalMaster($id);
			}
			else {
				$arrData = $this->model->selectSucursal($id);
			}
            if (empty($arrData)){
                $arrResponse = array('status' => false, 'message' => 'Datos no encontrados.');
            }
            else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
        }
		else {
			$arrResponse = array('status' => false, 'message' => 'ID no valido.');
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
	}
	public function getSucursalesEmpleados(int $sucursalID){
		$id = intval(strClear($sucursalID));
        if ($id > 0){
            $arrData = $this->model->selectSucursalEmpleados($sucursalID);
            if (empty($arrData)){
                $arrResponse = array('status' => false, 'message' => 'Datos no encontrados.', 'data' => []);
            }
            else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
        }
		else {
			$arrResponse = array('status' => false, 'message' => 'ID no valido.', 'data' => []);
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
	}
	public function setSucursal(){
		if ($_POST){ // valido post
			// echo json_encode($_POST,JSON_UNESCAPED_UNICODE);
        	// die();
            if (!empty($_POST["sucursal_id"]) and is_numeric($_POST["sucursal_id"]) and intval($_POST["sucursal_id"]) > 0){ 
				$id = intval($_POST["sucursal_id"]);
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
            $sucursal = empty($_POST["sucursalnombre"]) ? "" : mb_strtoupper(strClear($_POST["sucursalnombre"]));
			$codigo = empty($_POST["sucursalcodigo"]) ? "" : strClear($_POST["sucursalcodigo"]);
			$cuit = empty($_POST["sucursalcuit"]) ? 0 : intval(strClear($_POST["sucursalcuit"]));
			$direccion = empty($_POST["sucursaldireccion"]) ? "" : strClear($_POST["sucursaldireccion"]);
			$telefono = empty($_POST["sucursaltelefono"]) ? "" : strClear($_POST["sucursaltelefono"]);
			$mail = empty($_POST["sucursalmail"]) ? "" : strClear($_POST["sucursalmail"]);
			$web = empty($_POST["sucursalweb"]) ? "" : strClear($_POST["sucursalweb"]);
            $estado = empty($_POST["sucursalestado"]) ? 0 : intval(strClear($_POST["sucursalestado"]));
            $imagen = "";
			if (isset($_FILES["sucursalimg"])){ //valido la imagen
                $target_dir = "./Assets/images/uploads/";
				$image_name = time()."-".basename($_FILES["sucursalimg"]["name"]);
				$target_file = $target_dir.$image_name;
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$imageFileZise = $_FILES["sucursalimg"]["size"];
				// Allow certain file formats
				if(($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) and $imageFileZise > 0){
					$arrResponse = array("status" => false,"message" => "Sólo se permiten archivos con extension JPG , JPEG, PNG y GIF");
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                    die();
				} 
                elseif ($imageFileZise > 1048576){ // 1048576 byte=1MB
					$arrResponse = array("status" => false, "message" => "El archivo es demasiado grande. Debe ser menor de 1MB");
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                    die();
				}
                else {                  
                    if ($imageFileZise > 0){
                        move_uploaded_file($_FILES["sucursalimg"]["tmp_name"], $target_file);
                        $imagen = "images/uploads/$image_name";
                    }
                    else {
                        $imagen = "images/uploads/logo-icon3.png";
                    }
                }
            }
            // Validaciones
            if (empty($sucursal) or !validar($sucursal,9,2,50)){
                $arrResponse = array("status" => false, "message" => "El nombre de la sucursal esta vacio o es invalido.");
            }
			elseif (empty($codigo) or !validar($codigo,15,4,10)){
				$arrResponse = array("status" => false, "message" => "El codigo ingresado es invalido o esta vacio.");
			}
			elseif (!empty($cuit) and !validar($cuit,2,10,11)){
				$arrResponse = array("status" => false, "message" => "El cuit ingresado es incorrecto o esta vacio.");
			}
			elseif (!empty($direccion) and !validar($direccion,9,5,100)){
				$arrResponse = array("status" => false, "message" => "La direccion es incorrecta o esta vacia.");
			}
			elseif (!empty($telefono) and !validar($telefono,3,6,20)){
				$arrResponse = array("status" => false, "message" => "El telefono ingresado es incorrecto o esta vacio.");
			}
			elseif (!empty($mail) and !validar($mail,7,5,50)){
				$arrResponse = array("status" => false, "message" => "El email ingresado es incorrecto o esta vacio.");
			}
			elseif (!empty($web) and !validar($web,13,5,100)){
				$arrResponse = array("status" => false, "message" => "La web es incorrecta o esta vacia.");
			}
            elseif (empty($estado) or (intval($estado)>2 or intval($estado)<1)){
				$arrResponse = array("status" => false, "message" => "El estado seleccionado no es valido.");
            }
            else {
                if ($id > 0){ // actualizar
					try {
						$arrData = array($sucursal, $codigo, $cuit, $direccion, $telefono, $mail, $web, $estado, $imagen, $id);
						// $arrResponse = array("status" => false, "message" => $arrData);
						$requestSucursal = $this->model->updateSucursal($arrData);
						// $arrResponse = array("status" => false, "message" => json_encode($requestSucursal,JSON_UNESCAPED_UNICODE));
						if ($requestSucursal == -1){
							$arrResponse = array("status" => false, "message" => "El codigo ingresado esta duplicado. Code: {$requestSucursal}");
						}
						elseif ($requestSucursal){
							$arrResponse = array("status" => true, "message" => "La sucursal se ha actualizado satisfactoriamente. {$requestSucursal}");
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
						$arrData = array($sucursal, $codigo, $cuit, $direccion, $telefono, $mail, $web, $estado, $imagen);
						// $arrResponse = array("status" => false, "message" => $arrData);
						$requestSucursal = $this->model->insertSucursal($arrData);
						if ($requestSucursal > 0){
							$arrResponse = array("status" => true, "message" => "La sucursal se ha dado de alta satisfactoriamente.");
						}
						else {
							$arrResponse = array("status" => false, "message" => "No se ha podido registrar la sucursal. Code: {$requestSucursal}");
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
		if ($_SESSION["permisos"][0]["BORRAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para borrar registros en este módulo.");
		}
		elseif (isset($_POST['id'])){
            $id = intval(strClear($_POST['id']));
            $requestDelete = $this->model->deleteSucursal($id);
            if ($requestDelete == "ok"){
                $arrResponse = array("status" => true, "message" => "Datos borrados.");
            }
            elseif ($requestDelete == 'exist'){
                $arrResponse = array("status" => false, "message" => "No es posible eliminar una sucursal asignada a empleados.");
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
	public function setRestaurar(){
        if($_SESSION["userDATA"]["CARGO_ID"] != 1){
            $arrResponse = array("status" => false, "message" => "Usted no tiene permisos para realizar esta acción.");
        }
        elseif ($_POST){
            $id = empty($_POST['idSucursal']) ? 0 : intval(strClear($_POST['idSucursal']));
            if ($id > 0){
                try {
                    $requestRestaurar = $this->model->restaurarSucursal($id);
                    if ($requestRestaurar > 0){
                        $arrResponse = array("status" => true, "message" => "La sucursal ha sido restaurada");
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "La sucursal NO ha sido restaurada");
                    }
                }
                catch (Exception $e){
                    $arrResponse = array("status" => false, "message" => "Se ha producido un error", "details" => array($e->getMessage()));
                }
            }
            else {
                $arrResponse = array("status" => false, "message" => "El 'id' de la sucursal no es valido");
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