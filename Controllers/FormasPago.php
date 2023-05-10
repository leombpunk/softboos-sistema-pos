<?php 
class FormasPago extends Controllers{
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
        getPermisos(11);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard/?m=Formas%20de%20Pago");
		}
	}
	public function formasPago(){
		$data["page_id"] = 11;
		$data["page_tag"] = "Formas de pago | SoftBoos";
		$data["page_title"] = "Formas de pago";
		$data["page_name"] = "forma de pago";
        $data["page_filejs"] = "function_formasPago.js";
		$this->views->getView($this,"formasPago",$data);
	}
    public function getFormasPagos(){
        if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
            $arrData = $this->model->selectFormasPagosMaster();
        }
        else {
            $arrData = $this->model->selectFormasPagos();
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
                // agregar el cambio de funcion y boton de borrar a restablecer
            }
            else { // dato no controlado
                $arrData[$i]["estado"] = '<span class="badge badge-danger">WTF</span>';
            }
            $btnEditar = '<button onclick="editarFormasPago('.$arrData[$i]['FORMAPAGO_ID'].');" class="btn btn-primary btn-sm" type="button" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Editar"':'disabled title="No tienes permiso para editar"').'><i class="fa fa-pencil"></i></button>';
            if($arrData[$i]["ESTADO_ID"] == 3 and $_SESSION["userDATA"]["CARGO_ID"] == 1){
                $btnBorrar = '<button onclick="restaurarFormasPago('.$arrData[$i]['FORMAPAGO_ID'].');" class="btn btn-success title="Restaurar" btn-sm" type="button"><i class="fa fa-arrow-up"></i></button>';
            } else {
                $btnBorrar = '<button onclick="borrarFormasPago('.$arrData[$i]['FORMAPAGO_ID'].');" class="btn btn-danger btn-sm" type="button" '.($_SESSION["permisos"][0]["BORRAR"] == 1?'title="Eliminar"':'disabled title="No tienes permiso para eliminar"').'><i class="fa fa-trash"></i></button>';
            }
            $arrData[$i]['actions'] = '<div class="text-center">'.$btnEditar.' '.$btnBorrar.' </div>'; 
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getFormasPago(int $cargoID){
        $id = intval(strClear($cargoID));
        if ($id > 0){
            if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
                $arrData = $this->model->selectFormasPagoMaster($id);
            }
            else {
                $arrData = $this->model->selectFormasPago($id);
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
    public function setFormasPago(){
        if ($_POST){
            $intID = empty($_POST['formasPago_id']) ? 0 : intval(strClear($_POST['formasPago_id']));
            $strNombre = empty($_POST['formasPagonombre']) ? "" : mb_strtoupper(strClear($_POST['formasPagonombre']));
            $intEstado = empty($_POST['formasPagoestado']) ? 0 : intval(strClear($_POST['formasPagoestado']));   
            if ($intID == 0){
                if ($_SESSION["permisos"][0]["AGREGAR"] == 0){
					$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para crear registros en este módulo.");
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                    die();
				}
            } else {
                if ($_SESSION["permisos"][0]["MODIFICAR"] == 0){
                    $arrResponse = array("status" => false,"message" => "Usted no tiene permisos para editar registros en este módulo.");
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                    die();
                }
            }
            //validaciones
            if ($intID != 0 and !validar($intID,2,1,11)){
                $arrResponse = array(
                    'status' => false,
                    'message' => 'Datos incorrectos para el ID notifique al administrador.',
                    'expected' => 'Valor numérico mayor que 0 (cero).'
                );
            }
            elseif (empty($strNombre) or !validar($strNombre,1,3,50)){
                $arrResponse = array(
                    'status' => false,
                    'message' => 'Datos incorrectos para el Nombre de la forma de pago.',
                    'expected' => 'Cadena de texto (alfabetica) de entre 3 a 50 carateres.'
                );
            }
            elseif (empty($intEstado) or !validar($intEstado,2,1,11)){
                $arrResponse = array(
                    'status' => false,
                    'message' => 'Datos incorrectos para el estado de la forma de pago.',
                    'expected' => 'Valor numérico mayor que 0 (cero) entre 1 ó 2.'
                );
            }
            else {
                try {
                    if ($intID == 0){
                        //crear
                        $requestFormasPago = $this->model->insertFormasPago($strNombre,$intEstado);
                        if ($requestFormasPago > 0){ //done
                            $arrResponse = array(
                                'status' => true,
                                'message' => 'Datos guardados correctamente.',
                                'expected' => ''
                            );
                        }
                        else { //error
                            $arrResponse = array(
                                'status' => false,
                                'message' => empty($requestFormasPago)?'No es posible almacenar los datos.':$requestFormasPago." Para el Forma de Pago: ".$strNombre,
                                'expected' => ''
                            );
                        }
                    }
                    else {
                        //actualizar
                        $requestFormasPago = $this->model->updateFormasPago($intID,$strNombre,$intEstado);
                        if ($requestFormasPago == 'exist'){ //exist
                            $arrResponse = array(
                                'status' => false,
                                'message' => '¡Atencion! La Forma de Pago ya existe.',
                                'expected' => ''
                            );
                        }
                        elseif ($requestFormasPago > 0){ //done
                            $arrResponse = array(
                                'status' => true,
                                'message' => 'Datos actualizados correctamente.',
                                'expected' => ''
                            );
                        }
                        else { //error
                            $arrResponse = array(
                                'status' => false,
                                'message' => empty($requestFormasPago)?'No es posible almacenar los datos.':$requestFormasPago." Para el Forma de Pago: ".$strNombre,
                                'expected' => ''
                            );
                        }
                    }
                } catch (PDOException $e){
                    $requestFormasPago = mensajeSQL($e);
                    $arrResponse = array("status" => false, "message" => "Excepcion encontrada: {$requestFormasPago}", "details" => array($e, $e->getMessage()));
                }
            }
        } 
        else {
            $arrResponse = array('status' => true, 'message' => "Datos no válidos.", "details" => array("No 'POST' data."));
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function delFormasPago(){
        if ($_SESSION["permisos"][0]["BORRAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para borrar registros en este módulo.");
		}
		elseif ($_POST){
            $id = intval(strClear($_POST['id']));
            $requestDelete = $this->model->deleteFormasPago($id);
            if ($requestDelete == "ok"){
                $arrResponse = array("status" => true, "message" => "Datos borrados.");
            }
            elseif ($requestDelete == 'exist'){
                $arrResponse = array("status" => false, "message" => "No es posible eliminar una forma de pago en uso.");
            }
            else {
                $arrResponse = array("status" => false, "message" => "Error al eliminar los datos.");
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function setRestaurar(){
        if($_SESSION["userDATA"]["CARGO_ID"] != 1){
            $arrResponse = array("status" => false, "message" => "Usted no tiene permisos para realizar esta acción.");
        }
        elseif ($_POST){
            $id = empty($_POST['idFormasPago']) ? 0 : intval(strClear($_POST['idFormasPago']));
            if ($id > 0){
                try {
                    $requestRestaurar = $this->model->restaurarFormasPago($id);
                    if ($requestRestaurar > 0){
                        $arrResponse = array("status" => true, "message" => "La forma de pago ha sido restaurada");
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "La forma de pago NO ha sido restaurada");
                    }
                }
                catch (Exception $e){
                    $arrResponse = array("status" => false, "message" => "Se ha producido un error", "details" => array($e->getMessage()));
                }
            }
            else {
                $arrResponse = array("status" => false, "message" => "El 'id' de la forma de pago no es valido");
            }
        }
        else {
            $arrResponse = array("status" => false, "message" => "Datos no validos");
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
}
