<?php 
class PerfilModel extends Mysql {
    public function __construct() {
        parent::__construct();
    }
    public function selectPerfil(int $id){
        $sql = "SELECT e.DNI, e.NOMBRE, e.APELLIDO, e.FECHA_NACIMIENTO, e.CUIL, e.MAIL, e.TELEFONO, e.DIRECCION, 
        s.CODIGO_SUCURSAL, s.RAZONSOCIAL, s.DIRECCION AS SUC_DIRECCION, s.CUIT AS SCUIT, s.MAIL AS SMAIL, 
        s.TELEFONO AS STELEFONO, s.WEB, c.CARGO_DESCRIPCION 
        FROM empleados e 
        INNER JOIN cargos c ON c.CARGO_ID = e.CARGO_ID 
        INNER JOIN sucursales s ON s.SUCURSAL_ID = e.SUCURSAL_ID 
        WHERE e.EMPLEADO_ID = {$id} AND e.ESTADO_ID < 3";
        $request = $this->select($sql);
        return $request;
    }
    public function updatePerfil(array $datos){
        $sql = "UPDATE empleados SET DNI = ?, NOMBRE = ?, APELLIDO = ?, FECHA_NACIMIENTO = ?, MAIL = IF('".$datos[4]."'='',NULL,?), 
        TELEFONO = IF('".$datos[5]."'='',NULL,?), DIRECCION = IF('".$datos[6]."'='',NULL,?) WHERE EMPLEADO_ID = ?";
        $request = $this->update($sql,$datos);
        return $request;
    }
    public function updatePassword(array $datos){
        $sql = "UPDATE empleados SET CUIL = ?, CONTRASENA = ? WHERE EMPLEADO_ID = ?";
        $request = $this->update($sql,$datos);
        return $request;
    }
    public function compararPassword(int $id, string $contrasena){
        $sql = "SELECT 1 
        FROM empleados e 
        WHERE e.EMPLEADO_ID = {$id} AND e.CONTRASENA = '{$contrasena}' AND e.ESTADO_ID < 3";
        $request = $this->select($sql);
        return $request;
    }
    public function selectSessionData(int $id){
		$sql = "CALL SpEmpleadosDatosSelect({$id})";
		$request = $this->select($sql);
		return $request;
	}
}