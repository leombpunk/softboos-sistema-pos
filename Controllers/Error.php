<?php 
class Errors extends Controllers{
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
	public function notFound(){
		$data["page_id"] = 99;
		$data["page_tag"] = "Pagina no encontrada | SoftBoos";
		$data["page_title"] = "Pagina no encontrada";
		$data["page_name"] = "Pagina no encontrada";
		$data["page_filejs"] = "";
		$this->views->getView($this,"error",$data);
	}
}
$notFound = new Errors();
$notFound->notFound();
?>