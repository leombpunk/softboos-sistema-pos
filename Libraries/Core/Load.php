<?php 
// Load
// $controller = ucwords($controller);
$controllerfile = "Controllers/".$controller.".php";
// echo $controllerfile;
if(file_exists($controllerfile)){
	require_once($controllerfile);
	$controller = new $controller();
	if(method_exists($controller, $method)){
		$controller->{$method}($params);
	}
	else {
		require_once("Controllers/Error.php");
	}
}
else {
	require_once("Controllers/Error.php");
}
?>