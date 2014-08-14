<?php
require('header_inc.php');
require('db_inc.php');
require('aut_inc.php');

$log = "\niniciando tesouraria - " . date("Y-m-d H:i:s", time());
$log .= "\n\n".var_export($_REQUEST,true)."\n\n";

if(isset($_REQUEST['usuario'])){

	$retorno = '';

	$result = $mysqli->query("select 
			date_format(j.datahora,'%d/%m/%Y') as data,
			j.descricao as descricao,
			j.valor as valor,
			j.operacao as operacao	
		from financeiro j 
		inner join perfil k on k.id = j.perfil_id 
		where k.usuario = '{$_REQUEST['usuario']}'");
	$tesouraria_itens = array();
	while($item = $result->fetch_assoc()){
		array_push( 
			$tesouraria_itens, array(
				'data'  => $item['data'],
				'descricao'   => $item['descricao'],
				'operacao'   => $item['operacao'],
				'valor' => $item['valor'],
			)
		);
	}
	
	$result->close();
	$mysqli->close();
		
	$retorno = 'tesouraria_ok';
	$json = array(
		'retorno' => $retorno,
		'itens' => $tesouraria_itens
	);
		
}else{

	$retorno = "usuario_nok";
	$json = array(
		'retorno' => $retorno,
	);
}
echo json_encode($json);
file_put_contents('log.txt', $log);