<?php
class UdMedidas extends Controllers{
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
        getPermisos(7);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard/?m=Unidades%20de%20Medidas");
		}
	}
	public function UdMedidas(){
		$data["page_tag"] = "Unidades de Medida | SoftBoos";
		$data["page_title"] = "Unidades de Medida";
		$data["page_name"] = "udMedidas";
		$data["page_filejs"] = "function_udMedidas.js";
		$this->views->getView($this,"udMedidas",$data);
	}
    public function getUMBase(){
        $arrData = $this->model->selectUMBase();
        if (empty($arrData)){
            $arrResponse = array("status" => false, "message" => "Lista vacia.");
        }
        else {
            $arrResponse = array("status" => true, "message" => "OK.", "data" => $arrData);
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getUdMedidas(){
        if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
            $arrData = $this->model->selectUdMedidasMaster();
        }
        else {
            $arrData = $this->model->selectUdMedidas();
		}
        for ($i=0; $i < count($arrData); $i++) { 
            $arrData[$i]["val"] = formatMoney($arrData[$i]["val"]);
            if ($arrData[$i]["estado"] == 1){ // activo
                $arrData[$i]["estado"] = '<span class="badge badge-success">Activo</span>';
            }
            elseif ($arrData[$i]["estado"] == 2){ // inactivo
                $arrData[$i]["estado"] = '<span class="badge badge-danger">Inactivo</span>';
            }
            elseif ($arrData[$i]["estado"] == 3){ // borrado
                $arrData[$i]["estado"] = '<span class="badge badge-warning">Borrado</span>';
            }
            else { // dato no controlado
                $arrData[$i]["estado"] = '<span class="badge badge-danger">WTF</span>';
            }
            
            $btnEditar = '<button onclick="editarUdMedida('.$arrData[$i]['id'].');" class="btn btn-primary btn-sm" type="button" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Editar"':'disabled title="No tienes permiso para editar"').'><i class="fa fa-pencil"></i></button>';
            if ($arrData[$i]["estado"] == 3 and $_SESSION["userDATA"]["CARGO_ID"] == 1){
                $btnBorrar = '<button onclick="restaurarUdMedida('.$arrData[$i]['id'].');" class="btn btn-danger btn-sm" title="Restaurar" type="button"><i class="fa fa-trash"></i></button>';
            }
            else {
                $btnBorrar = '<button onclick="borrarUdMedida('.$arrData[$i]['id'].');" class="btn btn-danger btn-sm" type="button" '.($_SESSION["permisos"][0]["BORRAR"] == 1?'title="Eliminar"':'disabled title="No tienes permiso para eliminar"').'><i class="fa fa-trash"></i></button>';
            }
            $arrData[$i]['actions'] = '<div class="text-center">'.$btnEditar.' '.$btnBorrar.'</div>';
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getUdMedida(int $udMedidaID){
        $id = intval(strClear($udMedidaID));
        if ($id > 0){
            if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
                $arrData = $this->model->selectUdMedidaMaster($id);
            }
            else {
                $arrData = $this->model->selectUdMedida($id);
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
    public function setUdMedida(){
        if ($_POST){ // valido post
            if (!empty($_POST["udMedida_id"]) and is_numeric($_POST["udMedida_id"]) and intval($_POST["udMedida_id"]) > 0){ 
				$id = intval($_POST["udMedida_id"]);
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
            $udMedida = empty($_POST["udMedidanombre"]) ? "" : mb_strtoupper(strClear($_POST["udMedidanombre"]));
            $abr = empty($_POST["udMedidaabr"]) ? "" : mb_strtoupper(strClear($_POST["udMedidaabr"]));
            $equal = empty($_POST["udMedidaequal"]) ? "" : intval(strClear($_POST["udMedidaequal"]));
            $valor = empty($_POST["udMedidaval"]) ? 0.00 : floatval(str_ireplace(',','.',strClear($_POST["udMedidaval"])));
            $estado = empty($_POST["udMedidaestado"]) ? "" : intval(strClear($_POST["udMedidaestado"]));
            // Validaciones
            if (empty($udMedida) or !validar($udMedida,1,2,10)){
                $arrResponse = array("status" => false, "message" => "El nombre de la unidad de medida esta vacio o es invalido.");
            }
            elseif (empty($abr) or !validar($abr,1,1,4)){
                $arrResponse = array("status" => false, "message" => "La abreviatura es invalida o esta vacia.");
            }
            elseif (!empty($equal) and (!validar($equal,2,1,11) or $equal <= 0)){
                $arrResponse = array("status" => false, "message" => "La unidad de medida seleccionada no es correcta.");
            }
            elseif (!empty($valor) and (!validar($valor,10) or $valor <= 0)){
                $arrResponse = array("status" => false, "message" => "La cantidad ingresada es incorrecta, vaci o menor igual que cero");
            }
            elseif (empty($estado) or (intval($estado)>2 and intval($estado)<1)){
				$arrResponse = array("status" => false, "message" => "El estado seleccionado no es valido o no selecciono ninguno.");
            }
            else {
                if ($id > 0){ // actualizar
					$arrData = array($udMedida, $abr, $equal, $valor, $estado, $id);
					$requestUdMedida = $this->model->updateUdMedida($arrData);
					if ($requestUdMedida > 0){
						$arrResponse = array("status" => true, "message" => "La unidad de medida se ha actualizado satisfactoriamente.");
					}
				}
				else { // insertar
					$arrData = array($udMedida, $abr, $equal, $valor, $estado);
					$requestUdMedida = $this->model->insertUdMedida($arrData);
					if ($requestUdMedida > 0){
                        $this->model->updateUdMinID($requestUdMedida);
						$arrResponse = array("status" => true, "message" => "La unidad de medida se ha dado de alta satisfactoriamente.", "data" => $requestUdMedida);
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
    public function delUdMedida(){
        if ($_SESSION["permisos"][0]["BORRAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para borrar registros en este módulo.");
		}
		elseif (isset($_POST['id'])){
            $id = intval(strClear($_POST['id']));
            $requestDelete = $this->model->deleteUdMedida($id);
            if ($requestDelete == "ok"){
                $arrResponse = array("status" => true, "message" => "Datos borrados.");
            }
            elseif ($requestDelete == 'exist'){
                $arrResponse = array("status" => false, "message" => "No es posible eliminar una Unidad de Medida asignada a productos del sistema.");
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
            $id = empty($_POST['idUdMedida']) ? 0 : intval(strClear($_POST['idUdMedida']));
            if ($id > 0){
                try {
                    $requestRestaurar = $this->model->restaurarUdMedida($id);
                    if ($requestRestaurar > 0){
                        $arrResponse = array("status" => true, "message" => "La unidad de medida ha sido restaurada");
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "La unidad de medida NO ha sido restaurada");
                    }
                }
                catch (Exception $e){
                    $arrResponse = array("status" => false, "message" => "Se ha producido un error", "details" => array($e->getMessage()));
                }
            }
            else {
                $arrResponse = array("status" => false, "message" => "El 'id' de la unidad de medida no es valido");
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