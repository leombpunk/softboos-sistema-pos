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
		$data["page_id"] = 11;
		$data["page_tag"] = "Informes | SoftBoos";
		$data["page_title"] = "Informes";
		$data["page_name"] = "informes";
        $data["page_filejs"] = "function_informes.js";
		$this->views->getView($this,"informes",$data);
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
}
?>