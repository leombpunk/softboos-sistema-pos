<?php 
class Informes extends Controllers{
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
        getPermisos(13);
		if ($_SESSION["permisos"][0]["LEER"] == 0){
			header("location: ".base_url()."Dashboard/?m=Informes");
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
        if ($_POST){
            $fecha = empty($_POST["fecha"]) ? date("Y-m-d") : strClear($_POST["fecha"]);
            $agrupar = empty($_POST["agrupar"]) ? 0 : intval($_POST["agrupar"]);
            switch ($agrupar) {
                case 1:
                    $arrData = $this->model->selectInformeDelDia($fecha);
                    break;
                case 2:
                    $arrData = $this->model->selectInformeDelaSemana($fecha);
                    break;   
                case 3:
                    $arrData = $this->model->selectInformeDelMes($fecha);
                    break;
                case 4:
                    $arrData = $this->model->selectInformeDelAnho($fecha);
                    break;
                default:
                    $arrData = $this->model->selectInformeDelDia($fecha);
                    break;
            }
            $arrTotalEfectivo = array('id'=>'','descripcion'=>'TOTAL EFECTIVO','UNIDADMEDIA_ID'=>'','NOMBRE'=>'','cantidad'=>'','PRECIO'=>'','FORMAPAGO_ID'=>'','FORMA_PAGO'=>'','monto'=>0.00,'movimiento'=>'');
            $arrTotalGeneral = array('id'=>'','descripcion'=>'TOTAL GENERAL','UNIDADMEDIA_ID'=>'','NOMBRE'=>'','cantidad'=>'','PRECIO'=>'','FORMAPAGO_ID'=>'','FORMA_PAGO'=>'','monto'=>0.00,'movimiento'=>'');
            if (!empty($arrData)){
                for ($i=0; $i < count($arrData); $i++) { 
                    $arrData[$i]['monto'] = floatval($arrData[$i]['monto']);
                    $arrData[$i]['PRECIO'] = floatval($arrData[$i]['PRECIO']);
                    $arrData[$i]['cantidad'] = floatval($arrData[$i]['cantidad']);
                    if ($arrData[$i]["FORMA_PAGO"] === 'EFECTIVO'){ // activo
                        $arrTotalEfectivo['monto'] = floatval($arrTotalEfectivo['monto']) + floatval($arrData[$i]['monto']);
                    }
                    $arrTotalGeneral['monto'] = floatval($arrTotalGeneral['monto']) + floatval($arrData[$i]['monto']);
                }
                array_push($arrData, $arrTotalEfectivo);
                array_push($arrData, $arrTotalGeneral);
            }
        }
        else {
            $arrData = array('id'=>'','descripcion'=>'','UNIDADMEDIA_ID'=>'','NOMBRE'=>'','cantidad'=>'','PRECIO'=>'','FORMAPAGO_ID'=>'','FORMA_PAGO'=>'','monto'=>0,'movimiento'=>'');
        }
        echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        die();
    }
}
?>