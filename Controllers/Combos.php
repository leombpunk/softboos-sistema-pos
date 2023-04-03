<?php 
class Combos extends Controllers{
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
	public function combos(){
		$data["page_id"] = 11;
		$data["page_tag"] = "Combos | SoftBoos";
		$data["page_title"] = "Combos";
		$data["page_name"] = "combos";
        $data["page_filejs"] = "function_combos.js";
		$this->views->getView($this,"combos",$data);
	}
    public function getCombos(){
        $arrData = $this->model->selectCombos();
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
            <button onclick="verCombo('.$arrData[$i]['RECETA_ID'].');" class="btn btn-info btn-sm" title="Ver detalle" type="button"><i class="fa fa-eye"></i></button>
            <button onclick="editarCombo('.$arrData[$i]['RECETA_ID'].');" class="btn btn-primary btn-sm" title="Editar" type="button"><i class="fa fa-pencil"></i></button>
            <button onclick="borrarCombo('.$arrData[$i]['RECETA_ID'].');" class="btn btn-danger btn-sm" title="Eliminar" type="button"><i class="fa fa-trash"></i></button>
            </div>'; 
        }
        // dep($arrData);
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getCombo(int $idCombo){
        $id = intval(strClear($idCombo));
        if ($id > 0){
            $arrData = $this->model->selectCombo($id);
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
    public function getComboEInsumos(int $idCombo){
        //armar los datos a devolver
        $id = intval(strClear($idCombo));
        $insumos = array();
        if ($id > 0){
            $arrData = $this->model->selectCombo($id);
            $arrInsumos = $this->model->selectInsumosCombo($id);
            if (empty($arrData) or empty($arrInsumos)){
                $arrResponse = array('status' => false, 'message' => 'Datos no encontrados.');
            }
            else {
                //foreach
                foreach ($arrInsumos as $key => $value) {
                    array_push($insumos,array(
                        'id' => $value['id'], 
                        'nombre' => $value['nombre'],
                        'umid' => $value['umid'],
                        'umnombre' => $value['umnombre'],
                        'cantidad' => $value['cantidad'],
                    ));
                }
                $arrData['insumos'] = $insumos;
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function setCombo(){
        if($_POST){
            $id = intval(strClear($_POST['idCombo'])); //transforma el "" (vacio) en 0 (cero)
            $idMercaderia = intval(strClear($_POST['idMercaderia']));
            $nombre = mb_strtoupper(strClear($_POST['nombre']));
            $descripcion = mb_strtoupper(strClear($_POST['descripcion']));
            $estado = intval(strClear($_POST['estado']));   
            $ingredientes = empty($_POST["ingredientes"]) ? [] : $_POST["ingredientes"];
            if ($id != 0 and !validar($id,2,1,11)){
                $arrResponse = array(
                    'status' => false,
                    'message' => 'Datos incorrectos para el ID notifique al administrador.',
                    'expected' => 'Se espera un valor numérico mayor que 0 (cero).'
                );
            }
            elseif (empty($idMercaderia) or !validar($idMercaderia,2,1,11)){
                $arrResponse = array(
                    'status' => false,
                    'message' => 'El id de producto es incorrecto, notifique al administrador.',
                    'expected' => 'Se espera un valor numérico mayor que 0 (cero).'
                );
            }
            elseif (empty($nombre) or !validar($nombre,1,3,50)){
                $arrResponse = array(
                    'status' => false,
                    'message' => 'Datos incorrectos para el Nombre del combo.',
                    'expected' => 'Se espera una cadena de texto (alfabetica) de entre 3 a 50 carateres.'
                );
            }
            elseif (empty($estado) or !validar($estado,2,1,11)){
                $arrResponse = array(
                    'status' => false,
                    'message' => 'Datos incorrectos para el estado de la forma de pago.',
                    'expected' => 'Se espera un valor numérico mayor que 0 (cero) entre 1 ó 2.'
                );
            }
            elseif (!empty($descripcion) and !validar($descripcion,9,3,500)){
                $arrResponse = array(
                    'status' => false,
                    'message' => 'Datos incorrectos para la descripcion del combo.',
                    'expected' => 'Se espera una cadena de texto (alfabetica) de entre 3 a 500 carateres.'
                );
            }
            elseif (empty($ingredientes)){
                $arrResponse = array(
                    'status' => false,
                    'message' => 'Datos incorrectos para la lista de insumos del combo.',
                    'expected' => 'Se espera que almenos agregue un insumo.'
                );
            }
            else { //poner desde el try hasta antes de echo json_encode
                foreach ($ingredientes as $key => $value) {
					// array_push($arrResponse, $value['productoId']);
					if (empty($value['idInsumo']) and !validar($value['idInsumo'],2,1,11)) {
						$arrResponse = array("status" => false, "message" => "El id del insumo no es valido. {$value}");
						break;
					}
					elseif (empty($value['cantidad']) and !validar($value['cantidad'],10)) {
						$arrResponse = array("status" => false, "message" => "La cantidad del insumo no es valido. {$value}");
						break;
					}
					elseif (empty($value['idUnidadMedida']) and !validar($value['idUnidadMedida'],2,1,11)) {
						$arrResponse = array("status" => false, "message" => "La unidad de medida del insumo no es valida. {$value}");
						break;
					}
				}
                if (!isset($arrResponse)){
                    // try {
                        if ($id == 0){
                            //crear
                            $requestCombo = $this->model->insertCombo($idMercaderia,$nombre,$descripcion,$estado,$ingredientes);
                            $option = 1;
                        }
                        else {
                            //actualizar
                            $requestCombo = $this->model->updateCombo($nombre,$descripcion,$estado,$ingredientes,$id);
                            $option = 2;
                        }
                        // $arrResponse = $requestCombo;
                        if ($requestCombo == "Ok"){ //done
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
                        elseif ($requestCombo == 'Falopa'){ //exist
                            $arrResponse = array(
                                'status' => false,
                                'message' => '¡Atencion! El combo ya existe.',
                                'expected' => ''
                            );
                        }
                        else { //error
                            $arrResponse = array(
                                'status' => false,
                                'message' => empty($requestCombo)?'No es posible almacenar los datos.':$requestCombo." Para el Combo: ".$nombre,
                                'expected' => ''
                            );
                        }
                    // } catch (PDOException $e){
                    //     $arrResponse = array("status" => false, "message" => $e);
                    // }
                }
            }
        } else {
            $arrResponse = array("status" => false, "message" => "No envió datos.");
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function delCombo(){
        if ($_POST){
            $id = intval(strClear($_POST['id']));
            $requestDelete = $this->model->deleteCombo($id);
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