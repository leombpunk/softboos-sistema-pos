<?php 
class Mysql extends Conexion {
    private $conexion;
    private $strquery;
    private $arrValues;
    public function __construct(){
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->conect();
        //$this->setConexion();
    }
    // public function getConexion(){
    //     return $this->conexion;
    // }
    // public function setConexion(){
    //     return $this->conexion;
    // }
    public function insert(string $query, array $arrvalues){
        $this->strquery = $query;
        $this->arrValues = $arrvalues;
        $insert = $this->conexion->prepare($this->strquery);
        $resInsert = $insert->execute($this->arrValues);
        if($resInsert){
            $lastInsert = $this->conexion->lastInsertId();
        }
        else {
            $lastInsert = 0;
        }
        return $lastInsert;
    }
    // seleccionar un solo registro
    public function select(string $query){
        $this->strquery = $query;
        $result = $this->conexion->prepare($this->strquery);
        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);
        return $data;
    }
    //seleccionar varios registros
    public function select_all(string $query){
        $this->strquery = $query;
        $result = $this->conexion->prepare($this->strquery);
        $result->execute();
        $data = $result->fetchall(PDO::FETCH_ASSOC);
        return $data;
    }
    public function update(string $query, array $arrvalues){
        $this->strquery = $query;
        $this->arrValues = $arrvalues;
        $update = $this->conexion->prepare($this->strquery);
        $resExecute = $update->execute($this->arrValues);
        return $resExecute;
    }
    public function delete(string $query){
        $this->strquery = $query;
        $result = $this->conexion->prepare($this->strquery);
        $delete = $result->execute();
        return $delete;
    }
    // agregado por mi (necesita ser testeado)
    public function SpCALL(string $query){
        $this->strquery = $query;
        $call = $this->conexion->prepare($this->strquery);
        $result = $call->execute($this->arrValues);
        return $result;
    }
    public function mysqlStartTransaction(){
        $this->conexion->beginTransaction();
    }
    public function mysqlCommit(){
        $this->conexion->commit();
    }
    public function mysqlRollback(){
        $this->conexion->rollBack();
    }
    // hasta aca
}
?>