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
        $apertura = isSetAperturaCaja();
		$data["page_id"] = 17;
		$data["page_tag"] = "Notificaciones | SoftBoos";
		$data["page_title"] = "Notificaciones";
		$data["page_name"] = "notificaciones";
        $data["page_filejs"] = "function_notificaciones.js";
        $data["alert_message"] = $apertura ? '' : 'No se encontro ninguna notificación relacionada con la falta o exceso en el Inventario!';
		$this->views->getView($this,"notificaciones",$data);
	}
}