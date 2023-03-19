<?php 
class MovimientosCaja extends Controllers{
    private $permisoID;
    private $cargoID;
    private $arrPermiso;
    private $arrModuloPermiso;
    private $arrTablePer;
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
        getPermisos(11);
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
        $data["button_name"] = $apertura ? 'Nuevo movimiento' : 'Apertura';
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
    public function getMovimiento(int $cargoID){
        $id = intval(strClear($cargoID));
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
        // dep($_POST);
        $intID = intval(strClear($_POST['formasPago_id'])); //transforma el "" (vacio) en 0 (cero)
        $strNombre = mb_strtoupper(strClear($_POST['formasPagonombre']));
        $intEstado = intval(strClear($_POST['formasPagoestado']));   
        //agregar validar datos
        // var_dump($intID);
        if ($intID != 0 and !validar($intID,2,1,11)){
            $arrResponse = array(
                'status' => false,
                'message' => 'Datos incorrectos para el ID notifique al administrador.',
                'expected' => 'Se espera un valor numérico mayor que 0 (cero).'
            );
        }
        elseif (empty($strNombre) or !validar($strNombre,1,3,50)){
            $arrResponse = array(
                'status' => false,
                'message' => 'Datos incorrectos para el Nombre de la forma de pago.',
                'expected' => 'Se espera una cadena de texto (alfabetica) de entre 3 a 50 carateres.'
            );
        }
        elseif (empty($intEstado) or !validar($intEstado,2,1,11)){
            $arrResponse = array(
                'status' => false,
                'message' => 'Datos incorrectos para el estado de la forma de pago.',
                'expected' => 'Se espera un valor numérico mayor que 0 (cero) entre 1 ó 2.'
            );
        }
        else { //poner desde el try hasta antes de echo json_encode
            try {
                if ($intID == 0){
                    //crear
                    $requestMovimiento = $this->model->insertMovimiento($strNombre,$intEstado);
                    $option = 1;
                }
                else {
                    //actualizar
                    $requestMovimiento = $this->model->updateMovimiento($intID,$strNombre,$intEstado);
                    $option = 2;
                }
            } catch (PDOException $e){
                $requestMovimiento = mensajeSQL($e);
            }
            if ($requestMovimiento > 0){ //done
                if ($option == 1){
                    $arrResponse = array(
                        'status' => true,
                        'message' => 'Datos guardados correctamente.',
                        'expected' => ''
                    );
                }
                else {
                    $arrResponse = array(
                        'status' => true,
                        'message' => 'Datos actualizados correctamente.',
                        'expected' => ''
                    );
                }
            }
            elseif ($requestMovimiento == 'falopa'){ //exist
                $arrResponse = array(
                    'status' => false,
                    'message' => '¡Atencion! La Forma de Pago ya existe.',
                    'expected' => ''
                );
            }
            else { //error
                $arrResponse = array(
                    'status' => false,
                    'message' => empty($requestMovimiento)?'No es posible almacenar los datos.':$requestMovimiento." Para el Forma de Pago: ".$strNombre,
                    'expected' => ''
                );
            }
        }
        //*********************
        
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
}
?>