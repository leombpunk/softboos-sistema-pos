<?php 
class MovimientosCaja extends Controllers{
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
        getPermisos(12);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard/?m=Movimientos%20Caja");
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
        if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
            $arrData = $this->model->selectMovimientosMaster();
        }
        else {
            $arrData = $this->model->selectMovimientos();
        }
        for ($i=0; $i < count($arrData); $i++) { 
            if($arrData[$i]["ESTADO_ID"] == 3 and $_SESSION["userDATA"]["CARGO_ID"] == 1){
                $btnBorrar = '<button onclick="restaurarMovimiento('.$arrData[$i]['id'].');" class="btn btn-success btn-sm" title="Restaurar" type="button"><i class="fa fa-arrow-up"></i></button>';
            }
            else {
                $btnBorrar = '<button onclick="borrarMovimiento('.$arrData[$i]['id'].');" class="btn btn-danger btn-sm" type="button" '.($_SESSION["permisos"][0]["BORRAR"] == 1?'title="Eliminar"':'disabled title="No tienes permiso para eliminar"').'><i class="fa fa-trash"></i></button>';
            }
            
            $arrData[$i]['actions'] = '<div class="text-center">'.$btnBorrar.'</div>'; 
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getMovimiento(int $id){
        $id = intval(strClear($id));
        if ($id > 0){
            if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
                $arrData = $this->model->selectMovimientoMaster($id);
            }
            else {
                $arrData = $this->model->selectMovimiento($id);
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
    public function getTipoMovimiento(){
        $arrData = $this->model->selectTipoMovimiento();
        $arrResponse = array('status' => true, 'data' => $arrData);
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function setMovimiento(){
        if ($_SESSION["permisos"][0]["AGREGAR"] == 0){
            $arrResponse = array("status" => false,"message" => "Usted no tiene permisos para crear registros en este módulo.");
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();
        }
        elseif ($_POST){
            $descripcion = empty($_POST['movimientoDescripcion']) ? "" : mb_strtoupper(strClear($_POST['movimientoDescripcion']));
            $tipo = empty($_POST['movimientoTipo']) ? 0 : intval(strClear($_POST['movimientoTipo']));
            $monto = empty($_POST['movimientoMonto']) ? 0.0 : floatval(strClear($_POST['movimientoMonto']));   
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
        }
        else {
            $arrResponse = array("status" => false, "message" => "Datos no validos!.", "details" => array("No 'POST' data"));
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function delMovimiento(){
        if ($_SESSION["permisos"][0]["BORRAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para borrar registros en este módulo.");
		}
		elseif ($_POST){
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
    public function setRestaurar(){
        if($_SESSION["userDATA"]["CARGO_ID"] != 1){
            $arrResponse = array("status" => false, "message" => "Usted no tiene permisos para realizar esta acción.");
        }
        elseif ($_POST){
            $id = empty($_POST['idMovimiento']) ? 0 : intval(strClear($_POST['idMovimiento']));
            if ($id > 0){
                try {
                    $requestRestaurar = $this->model->restaurarMovimiento($id);
                    if ($requestRestaurar > 0){
                        $arrResponse = array("status" => true, "message" => "El movimiento ha sido restaurado");
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "El movimiento NO ha sido restaurado");
                    }
                }
                catch (Exception $e){
                    $arrResponse = array("status" => false, "message" => "Se ha producido un error", "details" => array($e->getMessage()));
                }
            }
            else {
                $arrResponse = array("status" => false, "message" => "El 'id' del movimiento no es valido");
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