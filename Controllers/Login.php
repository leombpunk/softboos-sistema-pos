<?php 
class Login extends Controllers{
	public function __construct(){
		session_start();
		if (isset($_SESSION["userLogin"])){
			header('location: '.base_url().'dashboard');
		}
		parent::__construct();
	}
	public function login(){
		$data["page_id"] = 5;
		$data["page_tag"] = "Login | SoftBoos";
		$data["page_title"] = "Login";
		$data["page_name"] = "login";
		$data["page_filejs"] = "function_login.js";
		$this->views->getView($this,"login",$data);
	}
	public function loginUser(){
		if($_POST){
			if (empty($_POST["user"]) or empty($_POST["password"])) {
				$arrResponse = array("status" => false, "message" => "Error, datos vacios.");
			} 
			else {
				$user = strClear($_POST["user"]);
				$pass = strClear($_POST["password"]);
				$requestLogin = $this->model->loginUser($user,$pass);
				// dep($requestLogin);
				if (empty($requestLogin)){
					$arrResponse = array("status" => false, "message" => "El usuario o la contraseña son incorrectos.");
				}
				else {
					// variables de sesion del usuario/empleado
					$_SESSION['userID'] = intval($requestLogin["EMPLEADO_ID"]);
					$_SESSION['userUser'] = $requestLogin["CUIL"];
					$_SESSION['userPASS'] = $requestLogin["CONTRASENA"];
					$_SESSION['userLogin'] = true;
					$arrData = $this->model->sessionLogin($_SESSION['userID']);
					$_SESSION['userDATA'] = $arrData;
					//-----------------------------------------
					$arrResponse = array("status" => true, "message" => "Bienvenido/a al sistema!.");
				}
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}
}
?>