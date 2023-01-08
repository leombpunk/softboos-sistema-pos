<?php
class UdMedidas extends Controllers{
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
        getPermisos(7);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard");
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
        $arrData = $this->model->selectUdMedidas();
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
                // agregar el cambio de funcion y boton de borrar a restablecer
            }
            else { // dato no controlado
                $arrData[$i]["estado"] = '<span class="badge badge-danger">WTF</span>';
            }
            // BOTONES DE ACCION
            if ($_SESSION["permisos"][0]["MODIFICAR"] == 1){
                $btnEditar = '<button onclick="editarUdMedida('.$arrData[$i]['id'].');" class="btn btn-primary btn-sm btnEditarUdMedida" rl="'.$arrData[$i]['id'].'" title="Editar" type="button"><i class="fa fa-pencil"></i></button>';
            }
            else {
                $btnEditar = '';
            }
            if ($_SESSION["permisos"][0]["BORRAR"] == 1){
                $btnBorrar = '<button onclick="borrarUdMedida('.$arrData[$i]['id'].');" class="btn btn-danger btn-sm btnBorrarUdMedida" rl="'.$arrData[$i]['id'].'" title="Eliminar" type="button"><i class="fa fa-trash"></i></button>';
            }
            else {
                $btnBorrar = '';
            }
            $arrData[$i]['actions'] = '<div class="text-center">'.$btnEditar.' '.$btnBorrar.'</div>';
            //<button onclick="verUdMedida('.$arrData[$i]['id'].');" class="btn btn-info btn-sm btnPermisosUdMedida" rl="'.$arrData[$i]['id'].'" title="Ver" type="button"><i class="fa fa-eye"></i></button> 
        }
        // dep($arrData);
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getUdMedida(int $udMedidaID){
        $id = intval(strClear($udMedidaID));
        if ($id > 0){
            $arrData = $this->model->selectUdMedida($id);
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
            // var_dump($_POST);
            if (!empty($_POST["udMedida_id"]) and is_numeric($_POST["udMedida_id"]) and intval($_POST["udMedida_id"]) > 0){ 
				$id = intval($_POST["udMedida_id"]);
				$option = 1;
			}
			else {
				$option = 2;
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
                if ($option == 1){ // actualizar
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
        if (isset($_POST['id'])){
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
}
?>