<?php 
class Notificaciones extends Controllers {
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
        getPermisos(17);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard/?m=Notificaciones");
		}
	}
	public function notificaciones(){
		$data["page_id"] = 17;
		$data["page_tag"] = "Notificaciones | SoftBoos";
		$data["page_title"] = "Notificaciones";
		$data["page_name"] = "notificaciones";
        $data["page_filejs"] = "function_notificaciones.js";
		$this->views->getView($this,"notificaciones",$data);
	}

    //**id de sucursal para filtrar los resultados
    //*si es el master -1 muestra los datos de todas las sucursales 
    //(agregar sucursal id a la tabla mercaderias_cantidades)
    //*si es otro cargo que solo traiga lo solicitado de su sucursal
    //*poder filtrar por sucursal, y que soolo lo pueda hacer el master?
    public function getAlertas($sucursalId){ //numero de alertas
        $id = empty($sucursalId) ? 0 : intval($sucursalId);
        $arrData = $this->model->selectAletas($id);
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getExcedentes($sucursalId){ //solo los excedentes
        $id = empty($sucursalId) ? 0 : intval($sucursalId);
        $arrData = $this->model->selectExcedentes($id);
        for ($i=0; $i < count($arrData); $i++) { 
            $arrData[$i]["ALERTA_MAXCANT"] = floatval($arrData[$i]["ALERTA_MAXCANT"]);
            $arrData[$i]["CANTIDAD_ACTUAL"] = floatval($arrData[$i]["CANTIDAD_ACTUAL"]);
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getFaltantes($sucursalId){ //solo los faltantes
        $id = empty($sucursalId) ? 0 : intval($sucursalId);
        $arrData = $this->model->selectFaltantes($id);
        for ($i=0; $i < count($arrData); $i++) { 
            $arrData[$i]["ALERTA_MINCANT"] = floatval($arrData[$i]["ALERTA_MINCANT"]);
            $arrData[$i]["CANTIDAD_ACTUAL"] = floatval($arrData[$i]["CANTIDAD_ACTUAL"]);
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
}