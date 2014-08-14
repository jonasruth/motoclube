<?php
require('header_inc.php');
require('db_inc.php');
require('aut_inc.php');

$log = "\niniciando financeiro - " . date("Y-m-d H:i:s", time());
$log .= "\n\n".var_export($_REQUEST,true)."\n\n";

if(isset($_REQUEST['usuario'])){

	$retorno = '';

	$result = $mysqli->query("select 
			date_format(j.datahora,'%d/%m/%Y') as data,
			j.descricao,
			j.valor,
			j.operacao			
		from financeiro j 
		inner join perfil k on k.id = j.perfil_id 
		where k.usuario = '{$_REQUEST['usuario']}'");
	$financeiro = $result->fetch_object();
	$result->close();

	$itens = array(
		'itens' => $financeiro,
	);

	$mysqli->close();
		
	$retorno = 'financeiro_ok';
	$json = array(
		'retorno' => $retorno,
		'itens'=>$itens
	);
		
}else{

	$retorno = "usuario_nok";
	$json = array(
		'retorno' => $retorno,
	);
}
echo json_encode($json);
file_put_contents('log.txt', $log);