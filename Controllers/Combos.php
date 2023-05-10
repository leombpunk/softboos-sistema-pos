<?php 
class Combos extends Controllers{
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
        getPermisos(15);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."dashboard/?m=Combos");
		}
	}
	public function combos(){
		$data["page_id"] = 15;
		$data["page_tag"] = "Combos | SoftBoos";
		$data["page_title"] = "Combos";
		$data["page_name"] = "combos";
        $data["page_filejs"] = "function_combos.js";
		$this->views->getView($this,"combos",$data);
	}
    public function getCombos(){
        if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
            $arrData = $this->model->selectCombosMaster();
        }
        else {
            $arrData = $this->model->selectCombos();
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
            $btnEditar = '<button onclick="ingresarCombo('.$arrData[$i]['RECETA_ID'].');" class="btn btn-outline-secondary btn-sm" type="button" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Ingresar combos"':'disabled title="No tienes permiso para editar"').'><i class="fa fa-plus"></i></button>
            <button onclick="quitarCombo('.$arrData[$i]['RECETA_ID'].');" class="btn btn-outline-secondary btn-sm" type="button" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Quitar combos"':'disabled title="No tienes permiso para editar"').'><i class="fa fa-minus"></i></button>
            <button onclick="editarCombo('.$arrData[$i]['RECETA_ID'].');" class="btn btn-primary btn-sm" type="button" '.($_SESSION["permisos"][0]["MODIFICAR"] == 1?'title="Editar"':'disabled title="No tienes permiso para editar"').'><i class="fa fa-pencil"></i></button>';

            if($arrData[$i]["ESTADO_ID"] == 3 and $_SESSION["userDATA"]["CARGO_ID"] == 1){//está borrado
				$btnBorrar = '<button onclick="restaurarCombo('.$arrData[$i]['RECETA_ID'].');" class="btn btn-success btn-sm" title="Restaurar" type="button"><i class="fa fa-arrow-up"></i></button>';
			} else {
                $btnBorrar = '<button onclick="borrarCombo('.$arrData[$i]['RECETA_ID'].');" class="btn btn-danger btn-sm" type="button" '.($_SESSION["permisos"][0]["BORRAR"] == 1?'title="Eliminar"':'disabled title="No tienes permiso para eliminar"').'><i class="fa fa-trash"></i></button>';
            }

            $arrData[$i]['actions'] = '<div class="text-center">
            <button onclick="verCombo('.$arrData[$i]['RECETA_ID'].');" class="btn btn-info btn-sm" title="Ver detalle" type="button"><i class="fa fa-eye"></i></button>'.
            $btnEditar.' '.$btnBorrar.'</div>'; 
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getCombo(int $idCombo){
        $id = intval(strClear($idCombo));
        if ($id > 0){
            if ($_SESSION["userDATA"]["CARGO_ID"] == 1){
                $arrData = $this->model->selectComboMaster($id);
            }
            else {
                $arrData = $this->model->selectCombo($id);
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
    public function getComboEInsumos(int $idCombo){
        $id = intval(strClear($idCombo));
        $insumos = array();
        if ($id > 0){
            $arrData = $this->model->selectCombo($id);
            $arrInsumos = $this->model->selectInsumosCombo($id);
            if (empty($arrData) or empty($arrInsumos)){
                $arrResponse = array('status' => false, 'message' => 'Datos no encontrados.');
            }
            else {
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
    public function getComboDetalleCantidad(int $idCombo){
        $id = intval(strClear($idCombo));
        if ($id > 0){
            $arrData = $this->model->selectComboDetalleCantidad($id);
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
    public function setComboDetalleCantidad(){
        if ($_SESSION["permisos"][0]["MODIFICAR"] == 0){
            $arrResponse = array("status" => false,"message" => "Usted no tiene permisos para editar registros en este módulo.");
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();
        }
        elseif($_POST){
            //validar datos
            $id = empty($_POST["comboActualizarId"]) ? 0 : intval(strClear($_POST["comboActualizarId"]));
            $idMercaderia = empty($_POST["comboMercaderiaId"]) ? 0 : intval(strClear($_POST["comboMercaderiaId"]));
            $idUnidadMedida = empty($_POST["comboUnidadMeidaId"]) ? 0 : intval(strClear($_POST["comboUnidadMeidaId"]));
            $cantidad = empty($_POST["combitoAgregar"]) 
                ? (
                    empty($_POST["combitoQuitar"]) ? 0.0 : floatval("-".strClear($_POST["combitoQuitar"]))
                ) : floatval($_POST["combitoAgregar"]);
            
            if (empty($id) or !validar($id,2,1,11)){
                $arrResponse = array('status' => false, 'message' => 'El ID del combo es incorrecto o esta vacio.');
            }
            elseif (empty($idMercaderia) or !validar($idMercaderia,2,1,11)){
                $arrResponse = array('status' => false, 'message' => 'El ID del producto es incorrecto, notifique al administrador.');
            }
            elseif (empty($idUnidadMedida) or !validar($idUnidadMedida,2,1,11)){
                $arrResponse = array('status' => false, 'message' => 'El ID de la unidad de medida es incorrecta, notifique al administrador.');
            }
            elseif (empty($cantidad) or !validar($cantidad,10)){
                $arrResponse = array('status' => false, 'message' => 'La cantidad es incorrecta o esta vacía. Cantidad: '.$cantidad);
            }
            else {
                //falta actualizar la cantidad en mercaderias_cantidad_actual y restar los insumos que usa el combo
                $arrData = array($id,$idUnidadMedida,$cantidad,$cantidad);
                $request = $this->model->insertComboElaborado($arrData);
                if ($request) {
                    $arrResponse = array("status" => true, "message" => "La cantidad del combo ha sido actualizada correctamente.");
                }
                else {
                    $arrResponse = array("status" => false, "message" => "Error al actualizar la cantidad del combo.", "request" => $request);
                }
            }
        } else {
            $arrResponse = array("status" => false, "message" => "No envió datos.");
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function setCombo(){
        if($_POST){
            $id = intval(strClear($_POST['idCombo']));
            $idMercaderia = intval(strClear($_POST['idMercaderia']));
            $nombre = mb_strtoupper(strClear($_POST['nombre']));
            $descripcion = mb_strtoupper(strClear($_POST['descripcion']));
            $estado = intval(strClear($_POST['estado']));   
            $ingredientes = empty($_POST["ingredientes"]) ? [] : $_POST["ingredientes"];
            //permisos
            if ($id == 0){
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
                        elseif ($requestCombo == 'exist'){ //exist
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
        if ($_SESSION["permisos"][0]["BORRAR"] == 0){
			$arrResponse = array("status" => false,"message" => "Usted no tiene permisos para borrar registros en este módulo.");
		}
		elseif ($_POST){
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
    public function setRestaurar(){
        if($_SESSION["userDATA"]["CARGO_ID"] != 1){
            $arrResponse = array("status" => false, "message" => "Usted no tiene permisos para realizar esta acción.");
        }
        elseif ($_POST){
            $id = empty($_POST['idCombo']) ? 0 : intval(strClear($_POST['idCombo']));
            if ($id > 0){
                try {
                    $requestRestaurar = $this->model->restaurarCombo($id);
                    if ($requestRestaurar > 0){
                        $arrResponse = array("status" => true, "message" => "El combo ha sido restaurado");
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "El combo NO ha sido restaurado");
                    }
                }
                catch (Exception $e){
                    $arrResponse = array("status" => false, "message" => "Se ha producido un error", "details" => array($e->getMessage()));
                }
            }
            else {
                $arrResponse = array("status" => false, "message" => "El 'id' del combo no es valido");
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