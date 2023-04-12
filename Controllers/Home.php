<?php 
class Home extends Controllers{
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
	public function home(){
		$data["page_id"] = 1;
		$data["page_tag"] = "Home";
		$data["page_title"] = "Weas";
		$data["page_name"] = "home";
		$this->views->getView($this,"home",$data);
	}
}
?>