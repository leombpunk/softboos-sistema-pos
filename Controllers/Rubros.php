<?php
class Rubros extends Controllers{
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
        getPermisos(6);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard");
		}
	}
	public function Rubros(){
		$data["page_tag"] = "Rubros | SoftBoos";
		$data["page_title"] = "Rubros";
		$data["page_name"] = "rubros";
		$data["page_filejs"] = "function_rubros.js";
        // $data["page_specialjs"] = array("<script src='".media()."js/bootstrap-filestyle.js' type='text/javascript' ></script>");
		$this->views->getView($this,"rubros",$data);
	}
    public function getRubros(){
        $arrData = $this->model->selectRubros();
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
            // BOTONES DE ACCION
            if ($_SESSION["permisos"][0]["MODIFICAR"] == 1){
                $btnEditar = '<button onclick="editarRubro('.$arrData[$i]['id'].');" class="btn btn-primary btn-sm btnEditarRubro" rl="'.$arrData[$i]['id'].'" title="Editar" type="button"><i class="fa fa-pencil"></i></button>';
            }
            else {
                $btnEditar = '';
            }
            if ($_SESSION["permisos"][0]["BORRAR"] == 1){
                $btnBorrar = '<button onclick="borrarRubro('.$arrData[$i]['id'].');" class="btn btn-danger btn-sm btnBorrarRubro" rl="'.$arrData[$i]['id'].'" title="Eliminar" type="button"><i class="fa fa-trash"></i></button>';
            }
            else {
                $btnBorrar = '';
            }
            // $arrData[$i]['actions'] = '<div class="text-center">
            // <button onclick="verRubro('.$arrData[$i]['id'].');" class="btn btn-info btn-sm btnPermisosRubro" rl="'.$arrData[$i]['id'].'" title="Ver" type="button"><i class="fa fa-eye"></i></button>
            // <button onclick="editarRubro('.$arrData[$i]['id'].');" class="btn btn-primary btn-sm btnEditarRubro" rl="'.$arrData[$i]['id'].'" title="Editar" type="button"><i class="fa fa-pencil"></i></button>
            // <button onclick="borrarRubro('.$arrData[$i]['id'].');" class="btn btn-danger btn-sm btnBorrarRubro" rl="'.$arrData[$i]['id'].'" title="Eliminar" type="button"><i class="fa fa-trash"></i></button>
            // </div>'; 
            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="verRubro('.$arrData[$i]['id'].');" class="btn btn-info btn-sm btnPermisosRubro" rl="'.$arrData[$i]['id'].'" title="Ver" type="button"><i class="fa fa-eye"></i></button> '.
            $btnEditar.' '.$btnBorrar.'</div>';
        }
        // dep($arrData);
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getRubro(int $rubroID){
        $id = intval(strClear($rubroID));
        if ($id > 0){
            $arrData = $this->model->selectRubro($id);
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
            // var_dump($_POST);
            if (!empty($_POST["rubro_id"]) and is_numeric($_POST["rubro_id"]) and intval($_POST["rubro_id"]) > 0){ 
				$id = intval($_POST["rubro_id"]);
				$option = 1;
			}
			else {
				$option = 2;
			}
            $rubro = empty($_POST["rubronombre"]) ? "" : mb_strtoupper(strClear($_POST["rubronombre"]));
            $estado = empty($_POST["rubroestado"]) ? "" : intval(strClear($_POST["rubroestado"]));
            $imagen = "";
            if (isset($_FILES["rubroimg"])){ //valido la imagen
                // var_dump($_FILES["rubroimg"]);
                // $imagen = $_FILES["rubroimg"];
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
                if ($option == 1){ // actualizar
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
        if (isset($_POST['id'])){
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
}
?>