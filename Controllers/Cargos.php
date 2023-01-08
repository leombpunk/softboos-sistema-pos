<?php 
class Cargos extends Controllers{
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
        getPermisos(2);
		// dep($_SESSION);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard");
		}
	}
	public function cargos(){
		$data["page_id"] = 3;
		$data["page_tag"] = "Cargos | SoftBoos";
		$data["page_title"] = "Cargos de Empleados";
		$data["page_name"] = "cargos";
		$data["page_content"] = "la re concha de la lora";
        $data["page_filejs"] = "function_cargos.js";
		$this->views->getView($this,"cargos",$data);
	}
    public function getCargos(){
        $arrData = $this->model->selectCargos();
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
            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="verPermisos('.$arrData[$i]['CARGO_ID'].',\''.$arrData[$i]['CARGO_DESCRIPCION'].'\');" class="btn btn-secondary btn-sm btnPermisosCargo" rl="'.$arrData[$i]['CARGO_ID'].'" title="Permisos" type="button"><i class="fa fa-key"></i></button>
            <button onclick="editarCargo('.$arrData[$i]['CARGO_ID'].');" class="btn btn-primary btn-sm btnEditarCargo" rl="'.$arrData[$i]['CARGO_ID'].'" title="Editar" type="button"><i class="fa fa-pencil"></i></button>
            <button onclick="borrarCargo('.$arrData[$i]['CARGO_ID'].');" class="btn btn-danger btn-sm btnBorrarCargo" rl="'.$arrData[$i]['CARGO_ID'].'" title="Eliminar" type="button"><i class="fa fa-trash"></i></button>
            </div>'; 
        }
        // dep($arrData);
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function setCargo(){
        // dep($_POST);
        $intID = intval(strClear($_POST['cargo_id'])); //transforma el "" (vacio) en 0 (cero)
        $strNombre = mb_strtoupper(strClear($_POST['cargonombre']));
        $intNivelAcceso = intval(strClear($_POST['cargonacceso']));
        $intEstado = intval(strClear($_POST['cargoestado']));   
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
                'message' => 'Datos incorrectos para el Nombre del cargo.',
                'expected' => 'Se espera una cadena de texto (alfabetica) de entre 3 a 50 carateres.'
            );
        }
        elseif (empty($intNivelAcceso) or !validar($intNivelAcceso,2,1,11)){
            $arrResponse = array(
                'status' => false,
                'message' => 'Datos incorrectos para el nivel de acceso del cargo.',
                'expected' => 'Se espera un valor numérico mayor que 0 (cero).'
            );
        }
        elseif (empty($intEstado) or !validar($intEstado,2,1,11)){
            $arrResponse = array(
                'status' => false,
                'message' => 'Datos incorrectos para el estado del cargo.',
                'expected' => 'Se espera un valor numérico mayor que 0 (cero) entre 1 ó 2.'
            );
        }
        else { //poner desde el try hasta antes de echo json_encode
            try {
                if ($intID == 0){
                    //crear
                    $requestCargo = $this->model->insertCargo($strNombre,$intNivelAcceso,$intEstado);
                    $option = 1;
                }
                else {
                    //actualizar
                    $requestCargo = $this->model->updateCargo($intID,$strNombre,$intNivelAcceso,$intEstado);
                    $option = 2;
                }
            } catch (PDOException $e){
                $requestCargo = mensajeSQL($e);
            }
            if ($requestCargo > 0){ //done
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
            elseif ($requestCargo == 'falopa'){ //exist
                $arrResponse = array(
                    'status' => false,
                    'message' => '¡Atencion! El Cargo ya existe.',
                    'expected' => ''
                );
            }
            else { //error
                $arrResponse = array(
                    'status' => false,
                    'message' => empty($requestCargo)?'No es posible almacenar los datos.':$requestCargo." Para el Cargo: ".$strNombre,
                    'expected' => ''
                );
            }
        }
        //*********************
        
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getCargo(int $cargoID){
        $id = intval(strClear($cargoID));
        if ($id > 0){
            $arrData = $this->model->selectCargo($id);
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
    public function delCargo(){
        if ($_POST){
            $id = intval(strClear($_POST['id']));
            $requestDelete = $this->model->deleteCargo($id);
            if ($requestDelete == "ok"){
                $arrResponse = array("status" => true, "message" => "Datos borrados.");
            }
            elseif ($requestDelete == 'exist'){
                $arrResponse = array("status" => false, "message" => "No es posible eliminar un cargo asignado a usuarios del sistema.");
            }
            else {
                $arrResponse = array("status" => false, "message" => "Error al eliminar los datos.");
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function getNivelesAcceso(){
        $arrData = $this->model->selectNivelesAcceso();
        if (empty($arrData)){
            $arrResponse = array("status" => false, "message" => "No se pueden recuperar los datos.");
        }
        else {
            $arrResponse = array("status" => true, "message" => "ok", "data" => $arrData);
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
    }
    public function getPermisos(int $id){
        $this->permisoID = intval(strClear($id));
        $arrData = $this->model->selectPermisos($this->permisoID);
        if (empty($arrData)){
            $arrResponse = array("status" => false, "message" => "No se pueden recuperar los datos de los permisos.");
        }
        else {
            $arrResponse = array("status" => true, "message" => "ok", "data" => $arrData);
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
    }
    public function setPermisos(){
        $arrResult = $this->model->getModulos();
        // var_dump($arrResult);
        foreach ($arrResult as $key => $value) {
            $this->arrTablePer[] = array("modulo" => $value["MODULO_ID"], "datos" => array("leer" => 0,"agregar" => 0,"modificar" => 0,"borrar" => 0));
        }
        // echo json_encode($this->arrTablePer,JSON_UNESCAPED_UNICODE);
        // die();
        if ($_POST){
            // var_dump($_POST);
            foreach ($_POST as $key => $value) {
                // var_dump($key);
                if (is_numeric($value)){ //valido que el id del cargo sea un numero
                    $this->cargoID = intval($value);
                }
                else { //armo el array de modulo id y el permiso que se habilita
                    $this->arrPermiso[] .= $value;
                }
            }
            // var_dump($this->arrPermiso);
            foreach ($this->arrPermiso as $key => $value) { //verifico que los valores sean numericos para pasarselos al modelo y realizar el update
                // var_dump($value);
                $this->arrModuloPermiso = explode("_", $value);
                if (!is_numeric($this->arrModuloPermiso[0])){
                    $arrResponse = array("status" => false, "message" => "El dato recibido no es valido. (0)");
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                }
                elseif (!is_numeric($this->arrModuloPermiso[1])){
                    $arrResponse = array("status" => false, "message" => "El dato recibido no es valido. (1)");
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                }
                // else { // no necesita el else porque valida por negativo
                //     $arrResponse = array("status" => false, "message" => "Algo malio sal en la validacion del permiso del modulo.");
                //     echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                // }
                if($this->arrModuloPermiso[1] == 1){
                    $this->arrTablePer[intval($this->arrModuloPermiso[0])-1]["datos"]["leer"] = 1;
                }
                elseif($this->arrModuloPermiso[1] == 2){
                    $this->arrTablePer[intval($this->arrModuloPermiso[0])-1]["datos"]["agregar"] = 1;
                }
                elseif($this->arrModuloPermiso[1] == 3){
                    $this->arrTablePer[intval($this->arrModuloPermiso[0])-1]["datos"]["modificar"] = 1;
                }
                elseif($this->arrModuloPermiso[1] == 4){
                    $this->arrTablePer[intval($this->arrModuloPermiso[0])-1]["datos"]["borrar"] = 1;
                }
                else {
                    $arrResponse = array("status" => false, "message" => "No modifique los datos enviados al servidor.");
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                }
            }
            // echo json_encode($this->arrTablePer,JSON_UNESCAPED_UNICODE);
            // die();
            $arrData = $this->model->updatePermisos($this->cargoID, $this->arrTablePer); 
            // echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
            // die();
            if ($arrData){
                $arrResponse = array("status" => true, "message" => "Datos de permisos actualizados.");
            }
            elseif ($arrData == "error"){
                $arrResponse = array("status" => false, "message" => "Error en los datos.");
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
    }
}
?>