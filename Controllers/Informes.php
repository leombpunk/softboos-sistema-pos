<?php 
class Informes extends Controllers{
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
        getPermisos(13);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard");
		}
	}
	public function today(){
		$data["page_id"] = 13;
		$data["page_tag"] = "Informes | SoftBoos";
		$data["page_title"] = "Informes";
		$data["page_name"] = "informes";
        $data["page_filejs"] = "function_informes.js";
		$this->views->getView($this,"informes",$data);
	}
    public function getinformeDelDia(){
        $arrData = $this->model->selectInformeDelDia();
        //diferenciar entre total efectivo (cash) y las demas formas de pago?
        $arrTotalEfectivo = array('id'=>'','descripcion'=>'TOTAL EFECTIVO','UNIDADMEDIA_ID'=>'','NOMBRE'=>'','cantidad'=>'','PRECIO'=>'','FORMAPAGO_ID'=>'','FORMA_PAGO'=>'PESOS','monto'=>0.00);
        $arrTotalGeneral = array('id'=>'','descripcion'=>'TOTAL GENERAL','UNIDADMEDIA_ID'=>'','NOMBRE'=>'','cantidad'=>'','PRECIO'=>'','FORMAPAGO_ID'=>'','FORMA_PAGO'=>'PESOS','monto'=>0.00);
        if (!empty($arrData)){
            for ($i=0; $i < count($arrData); $i++) { 
                if ($arrData[$i]["FORMA_PAGO"] === 'EFECTIVO'){ // activo
                    $arrTotalEfectivo['monto'] = floatval($arrTotalEfectivo['monto']) + floatval($arrData[$i]['monto']);
                }
                $arrTotalGeneral['monto'] = floatval($arrTotalGeneral['monto']) + floatval($arrData[$i]['monto']);
            }
            array_push($arrData, $arrTotalEfectivo);
            array_push($arrData, $arrTotalGeneral);
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
}
?>