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
		$this->views->getView($this,"dashboard",$data);
	}
}
?>