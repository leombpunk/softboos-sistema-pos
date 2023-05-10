<?php 
class NotificacionesModel extends Mysql {
    public function __construct() {
        parent::__construct();
    }
    public function selectAletas(int $sucursalId){
        $sql = "SELECT COUNT(m.MERCADERIA_ID) AS alertas 
        FROM mercaderias AS m
        INNER JOIN mercaderias_cantidad_actual AS mca ON mca.MERCADERIA_ID = m.MERCADERIA_ID
        WHERE m.ESTADO_ID < 3 
            AND (mca.CANTIDAD_ACTUAL < m.ALERTA_MINCANT OR mca.CANTIDAD_ACTUAL > m.ALERTA_MAXCANT)";
        $request = $this->select($sql);
        return $request;
    }
    public function selectExcedentes(int $sucursalId){
        $sql = "SELECT m.MERCADERIA_ID, m.CODIGO, m.NOMBRE, m.ALERTA_MAXCANT, mca.CANTIDAD_ACTUAL, mca.SUCURSAL_ID, s.RAZONSOCIAL, s.CODIGO_SUCURSAL 
        FROM mercaderias AS m
        INNER JOIN mercaderias_cantidad_actual AS mca ON mca.MERCADERIA_ID = m.MERCADERIA_ID
        INNER JOIN sucursales AS s ON s.SUCURSAL_ID = mca.SUCURSAL_ID
        WHERE m.ESTADO_ID < 3 
            AND mca.CANTIDAD_ACTUAL > m.ALERTA_MAXCANT";
        $request = $this->select_all($sql);
        return $request;
    }
    public function selectFaltantes(int $sucursalId){
        $sql = "SELECT m.MERCADERIA_ID, m.CODIGO, m.NOMBRE, m.ALERTA_MINCANT, mca.CANTIDAD_ACTUAL, mca.SUCURSAL_ID, s.RAZONSOCIAL, s.CODIGO_SUCURSAL 
        FROM mercaderias AS m
        INNER JOIN mercaderias_cantidad_actual AS mca ON mca.MERCADERIA_ID = m.MERCADERIA_ID
        INNER JOIN sucursales AS s ON s.SUCURSAL_ID = mca.SUCURSAL_ID
        WHERE m.ESTADO_ID < 3 
            AND mca.CANTIDAD_ACTUAL < m.ALERTA_MINCANT";
        $request = $this->select_all($sql);
        return $request;
    }
}
?>