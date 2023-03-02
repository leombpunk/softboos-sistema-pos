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
	//metodos con los queries para mostrar los datos en el dashboard
}
?>