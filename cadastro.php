<?php
require('header_inc.php');
require('db_inc.php');

$log = ">>> cadastro.php " . date("Y-m-d H:i:s", time());
$log .= "\n\n".var_export($_REQUEST,true)."\n\n";

$retorno = 'cadastro_fail';

if(!(isset($_REQUEST['usuario']) && isset($_REQUEST['email']) && isset($_REQUEST['senha']))){
	$log .= "\nNão autorizado";
	header('HTTP/1.1 401 Unauthorized', true, 401);
	file_put_contents('log.txt', $log);
	exit("Não autorizado");
}

$usuario = $mysqli->real_escape_string($_REQUEST['usuario']);
$email = $mysqli->real_escape_string($_REQUEST['email']);
$senha = $mysqli->real_escape_string($_REQUEST['senha']);

// AUTENTICAR USUARIO
$result = $mysqli->query("
	select
		(select count(id) from perfil where email = '{$email}') as email_existe,
		(select count(id) from perfil where usuario = '{$usuario}') as usuario_existe");

$existente = $result->fetch_object();
$result->close();

$log .= "\n\n".var_export($existente,true)."\n\n";

if($existente){
	if($existente->usuario_existe>0){
		$retorno = 'usuario_existente';
	} else if($existente->email_existe>0){
		$retorno = 'email_existente';
	}else{
		$result = $mysqli->query("insert into perfil (usuario,email,senha,motoclube_id,foto_perfil)
		values ('{$usuario}','{$email}','{$senha}',1,'default.jpg')");	
		$cadastro = $result->fetch_object();
		$log .= "\n\n".var_export($cadastro,true)."\n\n";
		$result->close();
		if($cadastro && $mysqli->affected_rows>0){
			$retorno = 'cadastro_success';
		}	
	}
}

$mysqli->close();

$log .= "\nRetorno: " . $retorno;

$json = array(
	'retorno' => $retorno,
);
echo json_encode($json);
file_put_contents('log.txt', $log);