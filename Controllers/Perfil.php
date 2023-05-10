<?php 
class Perfil extends Controllers {
    public function __construct() {
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
    }
    public function perfil(){
		$data["page_id"] = 5;
		$data["page_tag"] = "Perfil | SoftBoos";
		$data["page_title"] = "Perfil";
		$data["page_name"] = "perfil";
		$data["page_filejs"] = "function_perfil.js";
		$this->views->getView($this,"perfil",$data);
	}
    public function getPerfil(){
        if(isset($_SESSION["userDATA"])){
            $id = intval($_SESSION["userID"]);
            $arrData = $this->model->selectPerfil($id);
            $arrResponse = array("status"=>true,"message"=>"Datos econtrados", "data"=>$arrData);
        }
        else {
            $arrResponse = array("status" => false, "message" => "Error al recuperar los datos");
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function setPerfil(){
        if($_POST){
            //capturar y sanitizar datos
            $id = intval($_SESSION["userID"]);
            $dni = empty($_POST["dni"]) ? "" : strClear($_POST["dni"]);
            $nombre = empty($_POST["nombre"]) ? "" : strClear($_POST["nombre"]);
            $apellido = empty($_POST["apellido"]) ? "" : strClear($_POST["apellido"]);
            $direccion = empty($_POST["direccion"]) ? "" : strClear($_POST["direccion"]);
            $telefono = empty($_POST["telefono"]) ? "" : strClear($_POST["telefono"]);
            $email = empty($_POST["email"]) ? "" : strClear($_POST["email"]);
            $fechaNac = empty($_POST["fechaNac"]) ? "" : strClear($_POST["fechaNac"]);
            //validar datos
            if (empty($dni) or !validar($dni,2,7,8)){
				$arrResponse = array("status" => false, "message" => "El numero de DNI ingresado no es valido o esta vacío.");
			}
			elseif (empty($nombre) or !validar($nombre,1,2,50)) {
				$arrResponse = array("status" => false, "message" => "El nombre ingresado no es valido o esta vacío.");
			}
			elseif (empty($apellido) or !validar($apellido,1,2,50)) {
				$arrResponse = array("status" => false, "message" => "El apellido ingresado no es valido o esta vacío.");
			}
			elseif (empty($fechaNac) or !validar($fechaNac,11)) {
				$arrResponse = array("status" => false, "message" => "La fecha de nacimiento ingresada no es valida o esta vacía.");
			}
			elseif (!empty($email) and (!validar($email,7) or (strlen($email) < 10 or strlen($email) > 30))) {
				$arrResponse = array("status" => false, "message" => "El correo electronico no es valido o esta vacio.");
			}
			elseif (!empty($telefono) and !validar($telefono,3,6,20)) {
				$arrResponse = array("status" => false, "message" => "El nuemro de telefono ingresado no es valido o está vacío.");
			}
			elseif (!empty($direccion) and !validar($direccion,9,10,100)) {
				$arrResponse = array("status" => false, "message" => "La direccion ingresada no es valida o está vacía.");
			}
			else {
                //actualizar
                try {
                    $arrData = array($dni, $nombre, $apellido, $fechaNac, $email, $telefono, $direccion, $id);
                    $requestPerfil = $this->model->updatePerfil($arrData);
                    if ($requestPerfil > 0){
                        $arrResponse = array("status" => true, "message" => "Tú perfil se ha actualizado satisfactoriamente.");
                        //actualizar datos de la variable session
                        $arrData = $this->model->selectSessionData($id);
					    $_SESSION['userDATA'] = $arrData;
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "Algo ha salido muy mal", "detalis" => $requestPerfil);
                    }
                } catch (PDOException $e) {
                    $arrResponse = array("status" => false, "message" => "Se ha encontrado una excepcion", "detalis" => array($e, $e->getMessage()));
                }
            }
        } 
        else {
            $arrResponse = array("status" => false, "message" => "Error al recuperar los datos");
        }
        //informar
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
    public function setPassword(){
        if($_POST){
            //recolectar datos
            $id = intval($_SESSION["userID"]);
            $cuil = empty($_POST["cuil"]) ? "" : str_ireplace("-","",strClear($_POST["cuil"]));
            $actual = empty($_POST["actualpass"]) ? "" : strClear($_POST["actualpass"]);
            $nueva = empty($_POST["newpass"]) ? "" : strClear($_POST["newpass"]);
            //validar datos
            if (empty($actual) or !validar($actual,6,8,16)) {
				$arrResponse = array("status" => false, "message" => "La actual contraseña no es valida o esta vacía.");
			}
            elseif (empty($nueva) or !validar($nueva,6,8,16)) {
				$arrResponse = array("status" => false, "message" => "La nueva contraseña no es valida o esta vacía.");
			}
            elseif (empty($cuil) or !validar($cuil,2,10,11)) {
				$arrResponse = array("status" => false, "message" => "El CUIL(usuario) ingresado no es valido o esta vacío.");
			}
            else {
                try {
                    //comparar
                    $comparacion = $this->model->compararPassword($id,$actual);
                    if (!empty($comparacion)) {
                        //actualizar
                        $arrData = array($cuil, $nueva, $id);
                        $requestPerfil = $this->model->updatePerfil($arrData);
                        if ($requestPerfil > 0){
                            $arrResponse = array("status" => true, "message" => "Tú contraseña se ha actualizado satisfactoriamente.");
                            //informar por correo que cambio la contraseña y enviarle la nueva contraseña?
                        }
                        else {
                            $arrResponse = array("status" => false, "message" => "Algo ha salido muy mal", "detalis" => $requestPerfil);
                        }
                    }
                    else {
                        $arrResponse = array("status" => false, "message" => "La contraseña ingresada no coincide con la actual", "detalis" => $comparacion);
                    }
                } catch (PDOException $e) {
                    $arrResponse = array("status" => false, "message" => "Se ha encontrado una excepcion", "detalis" => array($e, $e->getMessage()));
                }
            }
        }
        else {
            $arrResponse = array("status" => false, "message" => "Error al recuperar los datos");
        }
        //informar
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        die();
    }
}