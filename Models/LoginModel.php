<?php 
class LoginModel extends Mysql {
	private $intUserId;
	private $strUser;
	private $strPass;
	private $strToken;
	public function __construct(){
		parent::__construct();
	}
	public function loginUser(string $user,string $pass){
		/*----prueba de conexion----*/
		// $connectionString = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";".DB_CHARSET;
        // try {
        //     $this->conect = new PDO($connectionString, $user, $pass);
        //     $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // } catch (PDOException $e) {
        //     // $this->conect = "Error de conexión."; //da error de tipo de dato devuelto en la funcion conect hereda en la clase mysql
        //     echo "Error de conexión. ERROR: ".$e->getMessage();
        // }
		/*--------------------------*/
		$this->strUser = $user;
		$this->strPass = $pass;
		$sql = "SELECT * FROM empleados e WHERE CUIL = '".$this->strUser."' AND CONTRASENA = '".$this->strPass."' AND FECHA_BAJA IS NULL";
		$request = $this->select($sql);
		return $request;
	}
	public function sessionLogin(int $userid){
		$this->intUserId = $userid;
		$sql = "CALL SpEmpleadosDatosSelect(".$this->intUserId.")";
		$request = $this->select($sql);
		return $request;
	}
} 
?>