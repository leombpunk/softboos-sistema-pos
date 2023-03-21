<?php 
class MovimientosCaja extends Controllers{
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
        getPermisos(12);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard");
		}
	}
	public function movimientosCaja(){
        $apertura = isSetAperturaCaja();
		$data["page_id"] = 11;
		$data["page_tag"] = "Caja | SoftBoos";
		$data["page_title"] = "Caja";
		$data["page_name"] = "caja";
        $data["page_filejs"] = "function_movimientosCaja.js";
        $data["alert_message"] = $apertura ? '' : 'No se encontro ninguna Apertura de Caja el dia de hoy, por favor hagalo!';
		$this->views->getView($this,"movimientosCaja",$data);
	}
    public function getMovimientos(){
        $arrData = $this->model->selectMovimientos();
        for ($i=0; $i < count($arrData); $i++) { 
            // if ($arrData[$i]["ESTADO_ID"] == 1){ // activo
            //     $arrData[$i]["estado"] = '<span class="badge badge-success">Activo</span>';
            // }
            // elseif ($arrData[$i]["ESTADO_ID"] == 2){ // inactivo
            //     $arrData[$i]["estado"] = '<span class="badge badge-danger">Inactivo</span>';
            // }
            // elseif ($arrData[$i]["ESTADO_ID"] == 3){ // borrado
            //     $arrData[$i]["estado"] = '<span class="badge badge-warning">Borrado</span>';
            //     // agregar el cambio de funcion y boton de borrar a restablecer
            // }
            // else { // dato no controlado
            //     $arrData[$i]["estado"] = '<span class="badge badge-danger">WTF</span>';
            // }
            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="borrarMovimiento('.$arrData[$i]['id'].');" class="btn btn-danger btn-sm" title="Eliminar" type="button"><i class="fa fa-trash"></i></button>
            </div>'; 
        }
        // dep($arrData);
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getMovimiento(int $id){
        $id = intval(strClear($id));
        if ($id > 0){
            $arrData = $this->model->selectMovimiento($id);
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
    public function getTipoMovimiento(){
        $arrData = $this->model->selectTipoMovimiento();
        $arrResponse = array('status' => true, 'data' => $arrData);
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function setMovimiento(){
        $descripcion =  mb_strtoupper(strClear($_POST['movimientoDescripcion']));
        $tipo = intval(strClear($_POST['movimientoTipo']));
        $monto = floatval(strClear($_POST['movimientoMonto']));   
        if (empty($descripcion) or !validar($descripcion,9,5)){
            $arrResponse = array(
                'status' => false,
                'message' => 'Datos incorrectos para la descripcion del movimiento de caja.',
                'expected' => 'Se espera una cadena de texto (alfabetica) de entre 3 a 50 carateres.'
            );
        }
        elseif (empty($tipo) or !validar($tipo,2,1,11)){
            $arrResponse = array(
                'status' => false,
                'message' => 'Datos incorrectos para tipo de movimiento de caja.',
                'expected' => 'Se espera un valor numérico mayor que 0 (cero). Entre 1 y 3.'
            );
        }
        elseif (empty($monto) or !validar($monto,10,1,8)){
            $arrResponse = array(
                'status' => false,
                'message' => 'Datos incorrectos para el monto del movimiento.',
                'expected' => 'Se espera un valor numérico mayor que 0 (cero). Entre 1 y 999999,99.'
            );
        }
        else { //poner desde el try hasta antes de echo json_encode
            try {
                $requestMovimiento = $this->model->insertMovimiento($descripcion,$tipo,$monto);
                if ($requestMovimiento > 0){
                    $arrResponse = array(
                        'status' => true,
                        'message' => 'Datos grabados correctamente.',
                        'expected' => ''
                    );
                }
                else {
                    $arrResponse = array(
                        'status' => false,
                        'message' => 'No es posible almacenar los datos. '.$requestMovimiento,
                        'expected' => ''
                    );
                }
            } catch (PDOException $e){
                $requestMovimiento = mensajeSQL($e);
                $arrResponse = array(
                    'status' => false,
                    'message' => 'Error SQL. '.$requestMovimiento,
                    'expected' => ''
                );
            }
            
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function delMovimiento(){
        if ($_POST){
            $id = intval(strClear($_POST['id']));
            $requestDelete = $this->model->deleteMovimiento($id);
            if ($requestDelete == "ok"){
                $arrResponse = array("status" => true, "message" => "Datos borrados.");
            }
            else {
                $arrResponse = array("status" => false, "message" => "Error al eliminar los datos.");
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
?>