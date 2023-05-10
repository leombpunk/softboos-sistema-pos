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
        getPermisos(2);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."dashboard/?m=Cargos");
		}
	}
	public function cargos(){
		$data["page_id"] = 3;
		$data["page_tag"] = "Cargos | SoftBoos";
		$data["page_title"] = "Cargos de Empleados";
		$data["page_name"] = "cargos";
        $data["page_filejs"] = "function_cargos.js";
		$this->views->getView($this,"cargos",$data);
	}
    public function getCargos(){
        if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
            $arrData = $this->model->selectCargosMaster();
        }
        else {
            $arrData = $this->model->selectCargos();
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
            }
            else { // dato no controlado
                $arrData[$i]["estado"] = '<span class="badge badge-danger">WTF</span>';
            }
            $btnPermisos = '<button onclick="verPermisos('.$arrData[$i]['CARGO_ID'].',\''.$arrData[$i]['CARGO_DESCRIPCION'].'\');" class="btn btn-secondary btn-sm" type="button" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Permisos"':'disabled title="No tienes acceso a editar los permisos"').'><i class="fa fa-key"></i></button>';
            $btnEditar = '<button onclick="editarCargo('.$arrData[$i]['CARGO_ID'].');" class="btn btn-primary btn-sm" type="button" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Editar"':'disabled title="No tienes permiso para editar"').'><i class="fa fa-pencil"></i></button>';
            if ($arrData[$i]["ESTADO_ID"] == 3 and $_SESSION["userDATA"]["CARGO_ID"] == 1){
                $btnBorrar = '<button onclick="restaurarCargo('.$arrData[$i]['CARGO_ID'].');" class="btn btn-success btn-sm" type="button" title="Restaurar"><i class="fa fa-arrow-up"></i></button>';
            } elseif ($arrData[$i]["ESTADO_ID"] == 3){
                $btnBorrar = '';
            }else {
                $btnBorrar = '<button onclick="borrarCargo('.$arrData[$i]['CARGO_ID'].');" class="btn btn-danger btn-sm" type="button" '.($_SESSION["permisos"][0]["BORRAR"] == 1?'title="Eliminar"':'disabled title="No tienes permiso para eliminar"').'><i class="fa fa-trash"></i></button>';
            }
            $arrData[$i]['actions'] = '<div class="text-center">'.$btnPermisos.'
            <button onclick="verEmpleados('.$arrData[$i]['CARGO_ID'].');" class="btn btn-sm btn-outline-secondary" type="button" title="Ver Empleados"><i class="fa fa-user"></i></button> 
            '.$btnEditar.' '.$btnBorrar.'</div>'; 
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function setCargo(){
        if ($_POST){
            $intID = empty($_POST['cargo_id']) ? 0 : intval(strClear($_POST['cargo_id']));
            $strNombre = empty($_POST['cargonombre']) ? "" : mb_strtoupper(strClear($_POST['cargonombre']));
            $intEstado = empty($_POST['cargoestado']) ? 0 : intval(strClear($_POST['cargoestado']));
            if ($intID == 1){
                $arrResponse = array("status" => false,"message" => "No es posible editar el cargo 'Master'", "expected" => "");
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                die();
            }
            elseif ($intID == 0){
                if ($_SESSION["permisos"][0]["AGREGAR"] == 0){
					$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para crear registros en este módulo.", "expected" => "");
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                    die();
				}
            } 
            else {
                if ($_SESSION["permisos"][0]["MODIFICAR"] == 0){
                    $arrResponse = array("status" => false,"message" => "Usted no tiene permisos para editar registros en este módulo.", "expected" => "");
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
                    'message' => 'Datos incorrectos para el Nombre del cargo.',
                    'expected' => 'Cadena de texto (alfabetica) de entre 3 a 50 carateres.'
                );
            }
            elseif (empty($intEstado) or !validar($intEstado,2,1,11)){
                $arrResponse = array(
                    'status' => false,
                    'message' => 'Datos incorrectos para el estado del cargo.',
                    'expected' => 'Valor numérico mayor que 0 (cero) entre 1 ó 2.'
                );
            } else {
                try {
                    if ($intID == 0){
                        //crear
                        $requestCargo = $this->model->insertCargo($strNombre,$intEstado);
                        if ($requestCargo > 0){ //done
                            $arrResponse = array(
                                'status' => true,
                                'message' => 'Datos guardados correctamente.',
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
                    else {
                        //actualizar
                        $requestCargo = $this->model->updateCargo($intID,$strNombre,$intEstado);
                        if ($requestCargo == 'exist'){ //exist
                            $arrResponse = array(
                                'status' => false,
                                'message' => '¡Atencion! El Cargo ya existe.',
                                'expected' => ''
                            );
                        }
                        elseif ($requestCargo > 0){ //done
                                $arrResponse = array(
                                    'status' => true,
                                    'message' => 'Datos actualizados correctamente.',
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
                } catch (PDOException $e){
                    $requestCargo = mensajeSQL($e);
                    $arrResponse = array("status" => false, "message" => "Excepcion encontrada: {$requestCargo}", "details" => array($e, $e->getMessage()), "expected" => "");
                }
            }
        } else {
            $arrResponse = array("status" => false, "message" => "Datos no validos!.", "details" => array("No 'POST' data"), "expected" => "");
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getCargo(int $cargoID){
        $id = intval(strClear($cargoID));
        if ($id > 0){
            if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
                $arrData = $this->model->selectCargoMaster($id);
            }
            else {
                $arrData = $this->model->selectCargo($id);
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
    public function delCargo(){
        if ($_SESSION["permisos"][0]["BORRAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para borrar registros en este módulo.");
		}
        elseif ($_POST){
            $id = intval(strClear($_POST['id']));
            if ($id == 1){
                $arrResponse = array("status" => false,"message" => "No es posible eliminar el cargo 'Master'");
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                die();
            }
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
        }
        else {
            $arrResponse = array("status" => false, "message" => "Error al recibir datos.");
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
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
    public function setRestaurar(){
        if($_SESSION["userDATA"]["CARGO_ID"] != 1){//verifica que sea el Master del sistema
            $arrResponse = array("status" => false, "message" => "Usted no tiene permisos para realizar esta acción.");
        }
        elseif ($_POST){//que los datos vengan por post -> solo envia el id del item
            //efecto inverso de borrar
            $id = empty($_POST['idCargo']) ? 0 : intval(strClear($_POST['idCargo']));
            if ($id > 0){
                try {
                    $requestRestaurar = $this->model->restaurarCargo($id);
                    if ($requestRestaurar > 0){
                        $arrResponse = array("status" => true, "message" => "El cargo ha sido restaurado");
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "El cargo NO ha sido restaurado");
                    }
                }
                catch (Exception $e){
                    $arrResponse = array("status" => false, "message" => "Se ha producido un error", "details" => array($e->getMessage()));
                }
            }
            else {
                $arrResponse = array("status" => false, "message" => "El 'id' de cargo no es valido");
            }
        }
        else {
            $arrResponse = array("status" => false, "message" => "Datos no validos");
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getEmpleados($sucursalId){
        $id = empty($sucursalId) ? 0 : intval(strClear($sucursalId));
        if ($id > 0){
            try {
                $arrData = $this->model->selectEmpleados($id);
                if (empty($arrData)){
                    $arrResponse = array("status" => false, "message" => "No hay datos", "data" => $arrData);
                }
                else {
                    $arrResponse = array("status" => true, "message" => "Lista de empledos de la sucursal solicitada", "data" => $arrData);
                }
            } catch (Exception $e){
                $arrResponse = array("status" => false, "message" => "Ha ocurrido un error", "data" => [], "details" => array($e, $e->getMessage()));
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