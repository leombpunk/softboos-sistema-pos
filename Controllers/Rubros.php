<?php
class Rubros extends Controllers{
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
        getPermisos(6);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard/?m=Rubros");
		}
	}
	public function Rubros(){
		$data["page_tag"] = "Rubros | SoftBoos";
		$data["page_title"] = "Rubros";
		$data["page_name"] = "rubros";
		$data["page_filejs"] = "function_rubros.js";
		$this->views->getView($this,"rubros",$data);
	}
    public function getRubros(){
        if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
            $arrData = $this->model->selectRubrosMaster();
        }
        else {
            $arrData = $this->model->selectRubros();
		}
        for ($i=0; $i < count($arrData); $i++) { 
            if ($arrData[$i]["estado"] == 1){ // activo
                $arrData[$i]["estado"] = '<span class="badge badge-success">Activo</span>';
            }
            elseif ($arrData[$i]["estado"] == 2){ // inactivo
                $arrData[$i]["estado"] = '<span class="badge badge-danger">Inactivo</span>';
            }
            elseif ($arrData[$i]["estado"] == 3){ // borrado
                $arrData[$i]["estado"] = '<span class="badge badge-warning">Borrado</span>';
                // agregar el cambio de funcion y boton de borrar a restablecer
            }
            else { // dato no controlado
                $arrData[$i]["estado"] = '<span class="badge badge-danger">WTF</span>';
            }

            $btnEditar = '<button onclick="editarRubro('.$arrData[$i]['id'].');" class="btn btn-primary btn-sm" type="button"><i class="fa fa-pencil" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Editar"':'disabled title="No tienes permiso para editar"').'></i></button>';
            if ($arrData[$i]["estado"] == 3 and $_SESSION["userDATA"]["CARGO_ID"] == 1){
                $btnBorrar = '<button onclick="restaurarRubro('.$arrData[$i]['id'].');" class="btn btn-success btn-sm" title="Restaurar" type="button"><i class="fa fa-arrow-up"></i></button>';
            }
            else {
                $btnBorrar = '<button onclick="borrarRubro('.$arrData[$i]['id'].');" class="btn btn-danger btn-sm" type="button" '.($_SESSION["permisos"][0]["BORRAR"] == 1?'title="Eliminar"':'disabled title="No tienes permiso para eliminar"').'><i class="fa fa-trash"></i></button>';
            }
            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="verRubro('.$arrData[$i]['id'].');" class="btn btn-info btn-sm" title="Ver" type="button"><i class="fa fa-eye"></i></button> '.
            $btnEditar.' '.$btnBorrar.'</div>';
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getRubro(int $rubroID){
        $id = intval(strClear($rubroID));
        if ($id > 0){
            if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
                $arrData = $this->model->selectRubroMaster($id);
            }
            else {
                $arrData = $this->model->selectRubro($id);
            }
            if (empty($arrData)){
                $arrResponse = array('status' => false, 'message' => 'Datos no encontrados.');
            }
            else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function setRubro(){
        if ($_POST){ // valido post
            if (!empty($_POST["rubro_id"]) and is_numeric($_POST["rubro_id"]) and intval($_POST["rubro_id"]) > 0){ 
				$id = intval($_POST["rubro_id"]);
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
            $rubro = empty($_POST["rubronombre"]) ? "" : mb_strtoupper(strClear($_POST["rubronombre"]));
            $estado = empty($_POST["rubroestado"]) ? "" : intval(strClear($_POST["rubroestado"]));
            $imagen = "";
            if (isset($_FILES["rubroimg"])){ //valido la imagen
                // var_dump($_FILES["rubroimg"]);
                $target_dir = "./Assets/images/uploads/";
				$image_name = time()."-".basename($_FILES["rubroimg"]["name"]);
				$target_file = $target_dir.$image_name;
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$imageFileZise = $_FILES["rubroimg"]["size"];
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
                        move_uploaded_file($_FILES["rubroimg"]["tmp_name"], $target_file);
                        $imagen = "images/uploads/$image_name";
                    }
                    else {
                        $imagen = "images/uploads/album-icon.jpg";
                    }
                }
            }
            // Validaciones
            if (empty($rubro) or !validar($rubro,14,2,50)){
                $arrResponse = array("status" => false, "message" => "El nombre del rubro esta vacio o es invalido.");
            }
            elseif (empty($estado) or (intval($estado)>2 and intval($estado)<1)){
				$arrResponse = array("status" => false, "message" => "El estado seleccionado no es valido o no selecciono ninguno.");
            }
            else {
                if ($id > 0){ // actualizar
                    if (strpos($imagen,"/album-icon") === true){
                        $imagen = "";
                    }
					$arrData = array($rubro, $estado, $imagen, $id);
					$requestRubro = $this->model->updateRubro($arrData);
					if ($requestRubro > 0){
						$arrResponse = array("status" => true, "message" => "El rubro se ha actualizado satisfactoriamente.");
					}
				}
				else { // insertar
					$arrData = array($rubro, $estado, $imagen);
					$requestRubro = $this->model->insertRubro($arrData);
					if ($requestRubro > 0){
						$arrResponse = array("status" => true, "message" => "El rubro se ha dado de alta satisfactoriamente.");
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
    public function delRubro(){
        if ($_SESSION["permisos"][0]["BORRAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para borrar registros en este módulo.");
		}
		elseif (isset($_POST['id'])){
            $id = intval(strClear($_POST['id']));
            $requestDelete = $this->model->deleteRubro($id);
            if ($requestDelete == "ok"){
                $arrResponse = array("status" => true, "message" => "Datos borrados.");
            }
            elseif ($requestDelete == 'exist'){
                $arrResponse = array("status" => false, "message" => "No es posible eliminar un rubro asignado a productos del sistema.");
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
            $id = empty($_POST['idRubro']) ? 0 : intval(strClear($_POST['idRubro']));
            if ($id > 0){
                try {
                    $requestRestaurar = $this->model->restaurarRubro($id);
                    if ($requestRestaurar > 0){
                        $arrResponse = array("status" => true, "message" => "El rubro ha sido restaurado");
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "El rubro NO ha sido restaurado");
                    }
                }
                catch (Exception $e){
                    $arrResponse = array("status" => false, "message" => "Se ha producido un error", "details" => array($e->getMessage()));
                }
            }
            else {
                $arrResponse = array("status" => false, "message" => "El 'id' del rubro no es valido");
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