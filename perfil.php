<?php
require('header_inc.php');
require('db_inc.php');
require('aut_inc.php');


$log = "\niniciando - " . date("Y-m-d H:i:s", time());
$log .= "\n\n".var_export($_REQUEST,true)."\n\n";


$retorno = '';

if(isset($_REQUEST['usuario'])){

	if(isset($_REQUEST['operacao'])){

		if($_REQUEST['operacao']==='salvar'){

			$mysqli->query("update perfil set  apelido = '{$_REQUEST['apelido']}', 
				graduacao = '{$_REQUEST['graduacao']}',
				sede = '{$_REQUEST['sede']}'
				where usuario = '{$_REQUEST['usuario']}'");

			$retorno = 'salvar_sucesso';

		}
		
	}

	$result = $mysqli->query("
		select
			j.usuario as usuario,
			j.nome as nome,
			j.apelido as apelido,
			j.graduacao as graduacao,
			j.sede as sede,
			j.foto_perfil as foto_perfil,
			k.nome as motoclube
		from perfil j
		inner join motoclube k on k.id = j.motoclube_id
		where j.usuario = '{$_REQUEST['usuario']}'");

	$perfil = $result->fetch_object();
	$result->close();


	$usuario = array(
		'retorno' => $retorno,
		'usuario' => $perfil->usuario,
		'nome' => $perfil->nome,
		'apelido' => $perfil->apelido,
		'graduacao' => $perfil->graduacao,
		'sede' => $perfil->sede,
		'motoclube' => $perfil->motoclube,
		'foto_perfil' => $perfil->foto_perfil,
	);
}else{
	$retorno = "usuario_nok";
	$usuario = array(
		'retorno' => $retorno,
	);
}

echo json_encode($usuario);

$mysqli->close();
file_put_contents('log.txt', $log);