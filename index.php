<?php 
require_once("Config/Config.php");
require_once("Helpers/Helpers.php");
$url = !empty($_GET['url'])?$_GET['url']:'home/home';
$arrayUrl = explode('/',$url);
$controller = $arrayUrl[0];
$method = $arrayUrl[0];
$params = "";
if (!empty($arrayUrl[1])){
	$method = $arrayUrl[1];
}
if (!empty($arrayUrl[2])){
	for ($i=2;$i<count($arrayUrl);$i++){
		$params .= $arrayUrl[$i].',';
	}
	$params = trim($params,',');
}
require_once("Libraries/Core/Autoload.php");
require_once("Libraries/Core/Load.php");
?>