<?php 
class Conexion {
    private $conect;
    public function __construct(){
        $connectionString = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";".DB_CHARSET;
        try {
            $this->conect = new PDO($connectionString, DB_USER, DB_PASSWORD);
            $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // $this->conect = "Error de conexión."; //da error de tipo de dato devuelto en la funcion conect hereda en la clase mysql
            echo "Error de conexión. ERROR: ".$e->getMessage();
        }
    }
    public function conect(){
        return $this->conect;
    }
    /*testear este codigo para la conexion*/
    public function setConexion(string $user = DB_USER, string $pass = DB_PASSWORD){
        $connectionString = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";".DB_CHARSET;
        try {
            $this->conect = new PDO($connectionString, $user, $pass);
            $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // $this->conect = "Error de conexión."; //da error de tipo de dato devuelto en la funcion conect hereda en la clase mysql
            echo "Error de conexión. ERROR: ".$e->getMessage();
        }
    }
    /*------------------------------------*/
}
?>