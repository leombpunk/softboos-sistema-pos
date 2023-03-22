<?php 
class Dashboard extends Controllers{
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
	}
	public function dashboard(){
		$data["page_id"] = 2;
		$data["page_tag"] = "Dashboard e-Comerce";
		$data["page_title"] = "Dashboard e-Comerce";
		$data["page_name"] = "dashboard";
		$data["page_filejs"] = "function_dashboard.js";
		$data["page_specialjs"] = array('<script src="'.media().'js/plugins/chart.js" type="text/javascript"></script>',
		'<script src="'.media().'js/plugins/chart.umd.js" type="text/javascript"></script>');
		$this->views->getView($this,"dashboard",$data);
	}
	public function productosVentaCantidad(){
		//cantidad de productos a la venta
		$arrData = $this->model->selectProductosVentaCantidad();
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function productoMasVendido(){
		//el mas vendido en general
		$arrData = $this->model->selectProductoMasPedido();
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function ingresosDelDia(){
		//total de ingresos del dia
		$arrData = $this->model->selectIngresosDelDia();
		$arrData['total'] = formatMoney($arrData['total']);
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function totalProductosVendidos(){
		//total de productos vendidos
		$arrData = $this->model->selectTotalVentas();
		// $arrData['total_ventas'] = formatDecimal($arrData['total_ventas']);
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function grafico1(){
		//total de ventas por producto cantidad y monto
		$arrData = $this->model->selectCantidadProductosVendidos();
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
	public function grafico2(){
		//total de ventas totales por horas
		$arrData = $this->model->selectMontoPorProductoVenido();
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}
}
?>