<?php
require('header_inc.php');
require('db_inc.php');

$log = ">>> Login.php " . date("Y-m-d H:i:s", time());
$log .= "\n\n".var_export($_REQUEST,true)."\n\n";

if(!(isset($_REQUEST['usuario']) && isset($_REQUEST['senha']))){
	$log .= "\nNão autorizado";
	header('HTTP/1.1 401 Unauthorized', true, 401);
	file_put_contents('log.txt', $log);
	exit("Não autorizado");
}

$usuario = $mysqli->real_escape_string($_REQUEST['usuario']);
$senha = $mysqli->real_escape_string($_REQUEST['senha']);

// AUTENTICAR USUARIO
$result = $mysqli->query("
	select
		usuario
	from perfil
	where usuario = '{$usuario}' and senha = '{$senha}' ");

$perfil = $result->fetch_object();
$result->close();

$retorno = 'login_fail';

if($perfil!=null && $perfil->usuario!=null && $perfil->usuario == $usuario){
	$retorno = 'login_success';
}

$log .= "\nRetorno: " . $retorno;

$json = array(
	'retorno' => $retorno,
);
echo json_encode($json);
file_put_contents('log.txt', $log);