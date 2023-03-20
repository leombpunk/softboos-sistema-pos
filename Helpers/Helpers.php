<?php 
function base_url(){
    return BASE_URL;
}
function dep($data){
    $format = print_r('<pre>');
    $format .= print_r($data);
    $format .= print_r('</pre>');
    return $format;
}
function media(){
    return BASE_URL."Assets/";
}
function headerAdmin($data=""){
    $view_header = "Views/Template/header_admin.php";
    require_once($view_header);
}
function footerAdmin($data=""){
    $view_footer = "Views/Template/footer_admin.php";
    require_once($view_footer);
}
function getPermisos(int $moduloID){
    require_once("Libraries/Core/Mysql.php");
    $con = new Mysql();
    $sql = "CALL SpPermisosGet(".$_SESSION["userDATA"]["CARGO_ID"].",".$moduloID.")";
    $_SESSION["permisos"] = $con->select_all($sql);
}
function strClear($strCadena){
    $string = preg_replace(['/\s+/','/^\s|\s$/'],[' ',''],$strCadena);//quita espacios en blanco
    $string = trim($string);
    $string = stripslashes($string);
    $string = str_ireplace("<script>","",$string);
    $string = str_ireplace("</script>","",$string);
    $string = str_ireplace("<script src>","",$string);
    $string = str_ireplace("<script type=>","",$string);
    $string = str_ireplace("SELECT * FROM","",$string);
    $string = str_ireplace("DELETE FROM","",$string);
    $string = str_ireplace("INSERT INTO","",$string);
    $string = str_ireplace("SELECT COUNT(*) FROM","",$string);
    $string = str_ireplace("DROP TABLE","",$string);
    $string = str_ireplace("OR '1'='1'","",$string);
    $string = str_ireplace('OR "1"="1"',"",$string);
    $string = str_ireplace("OR ´1´=´1´","",$string);
    $string = str_ireplace("is NULL; --","",$string);
    $string = str_ireplace("LIKE '","",$string);
    $string = str_ireplace('LIKE "',"",$string);
    $string = str_ireplace('LIKE ´',"",$string);
    $string = str_ireplace("OR 'a'='a","",$string);
    $string = str_ireplace('OR "a"="a"',"",$string);
    $string = str_ireplace("OR ´a´=´a","",$string);
    $string = str_ireplace("OR ´a´=´a´","",$string);
    $string = str_ireplace("--","",$string);
    $string = str_ireplace("^","",$string);
    $string = str_ireplace("[","",$string);
    $string = str_ireplace("]","",$string);
    $string = str_ireplace("==","",$string);
    return $string;
}
function passGenerator($length = 10){
    $pass = "";
    $longitudPass = $length;
    $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $longitudCadena = strlen($cadena);
    for($i=1;$i<=$longitudPass;$i++){
        $pos = rand(0,$longitudCadena-1);
        $pass .= substr($cadena,$pos,1);
    }
    return $pass;
}
function token(){
    $r1 = bin2hex(random_bytes(10));
    $r2 = bin2hex(random_bytes(10));
    $r3 = bin2hex(random_bytes(10));
    $r4 = bin2hex(random_bytes(10));
    $token = "{$r1}-{$r2}-{$r3}-{$r4}";
    return $token;
}
function formatMoney($cantidad){
    $cantidad = number_format($cantidad,2,PSD,SPM);
    return $cantidad;
}
function formatDecimal($cantidad){
    $cantidad = number_format($cantidad,3,PSD,SPM);
    return $cantidad;
}
function getModal(string $nameModal, $data){
    $view_modal = "Views/Template/Modals/{$nameModal}.php";
    require_once($view_modal); 
}
function validar($value, int $option, int $minlength=1, int $maxlength=100){
    $regExp = "";
    switch ($option) {
        case 1: // valida una cadena (mayusculas minusculas y carateres con acento y eñe) segun longitudes proporcionadas
            $regExp = '/^([a-zA-Z\sÑñÁáÉéÍíÓóÚú]){'.$minlength.','.$maxlength.'}+$/';
            if (is_string($value)){
                return preg_match_all($regExp,$value);
            }
            return false;
        case 2: // valida nuemro enteros positivos mayores que cero
            $regExp = '/^(?!0)[0-9]{'.$minlength.','.$maxlength.'}+$/';
            if (is_numeric($value) or is_string($value)){ 
                return preg_match_all($regExp,$value);
            }
            return false;
        case 3: // valida enteros positivos incluido el cero
            $regExp = '/^[0-9]{'.$minlength.','.$maxlength.'}+$/';
            if (is_numeric($value) or is_string($value)){
                return preg_match_all($regExp,$value);
            }
            return false;
        case 4: // valida numeros enteros negativos solamente
            $regExp = '/^\-\d{'.$minlength.','.$maxlength.'}+$/';
            if (is_numeric($value)){
                return preg_match_all($regExp,$value);
            }
            return false;
        case 5: // valida numeros enteros positivos y negativos incluido el cero
            $regExp = '/^\-?\d{'.$minlength.','.$maxlength.'}+$/';
            if (is_numeric($value) or is_string($value)){
                return preg_match_all($regExp,$value);
            }
            return false;
        case 6: // valida contraseñas segun el formato
            $regExp = '/^[A-Z,a-z,0-9_\.]{'.$minlength.','.$maxlength.'}+$/';
            if (is_string($value) or is_numeric($value)){
                return preg_match_all($regExp,$value);
            }
            return false;
        case 7: // valida que la cadena sea un correo electronico valido
            $regExp = '/^\w+([-._]\w+)*@\w+([-._]\w+)*\.\w+$/';
            if (is_string($value)){
                return preg_match_all($regExp,$value);
            }
            return false;
        case 8: // valida nombres de usuario 
            $regExp = '/^[\w\.]{'.$minlength.','.$maxlength.'}+$/';
            if (is_string($value)){
                return preg_match_all($regExp,$value);
            }
            return false;
        case 9: // valida una cadena de caracteres tipo oracion, numeros y letras y carateres con acento
            $regExp = '/^([a-zA-Z0-9\/\sÑñÁáÉéÍíÓóÚú-]){'.$minlength.','.$maxlength.'}+$/';
            if (is_string($value)){
                return preg_match_all($regExp,$value);
            }
            return false;
        case 10: // valida numeros con decimales positivos y negativos
            $regExp = '/^\-?((\d{1,8})*|(\.|\,)(\d{1,2}))+$/'; //^\-?\d{1,5}+([,.]\d{1,2})?+$
            if (is_float($value) or is_string($value)){
                return preg_match_all($regExp,$value);
            }
            return false;
        case 11: // valida una fecha en el formato yyyy-mm-dd
            $regExp = '/^(\d{4}(\/|-)(0[1-9]|1[0-2])\2([0-2][0-9]|3[0-1]))$/'; //yyyy-mm-dd
            return preg_match_all($regExp,$value);
        case 12: // valida una fecha incluida la hora
            $regExp = '/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})(\s)([0-1][0-9]|2[0-3])(:)([0-5][0-9])(:)([0-5][0-9])$/';
            return preg_match_all($regExp,$value);
        case 13: // valida una url o direccion de una pagina web
            $regExp = '/^(ht|f)tp(s?)\:\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z-])*(:(0-9)*)*(\/?)( [a-zA-Z0-9\-\.\?\,\'\/\\\+&%\$#_]*)?$/';
            return preg_match_all($regExp,$value);
        case 14: // valida una cadena de caracteres tipo oracion, letras y carateres con acento y algunos caracteres especiales
            $regExp = '/^([a-zA-Z\/\sÑñÁáÉéÍíÓóÚú-]){'.$minlength.','.$maxlength.'}+$/';
            if (is_string($value)){
                return preg_match_all($regExp,$value);
            }
            return false;
        case 15: //valida numeros letras mayusculas y minusculas (pensado para codigo de producto)
            $regExp = '/^[a-z,A-Z,0-9]{'.$minlength.','.$maxlength.'}+$/';
            if (is_string($value) or is_numeric($value)){
                return preg_match_all($regExp,$value);
            }
            return false;
        case 16: //boolean 1 ó 0 
            $regExp = '/^[0|1]{1,1}+$/';
            if (is_string($value) or is_numeric($value)){
                return preg_match_all($regExp,$value);
            }
            return false;
        default: // entra por defecto si no coincide con ninguna opcion
            return false;
    }
}
function mensajeSQL($PDOobj){
    $mensaje = "";
    if ($PDOobj->errorInfo[1] == 1062){
        $mensaje = "Entrada duplicada.";
    }
    return $mensaje;
}
//verificar que en la tabla de movimientos de caja exista una apertura de caja para el dia de hoy
//si ya hay una apertura para hoy devuelve verdadero
//si aun no hay ninguna apertura para hoy devuelve falso
function isSetAperturaCaja(){
    require_once("Libraries/Core/Mysql.php");
    $con = new Mysql();
    $sql = "SELECT 1 FROM `movimientos_caja` AS mc
    WHERE mc.TIPO_ID = 3 
        AND DATE(mc.FECHA_ALTA) = DATE(NOW()) 
        AND mc.ESTADO_ID <> 3";
    $result = $con->select($sql);
    if (!empty($result)) {
        return true;
    }
    return false;
}
?>