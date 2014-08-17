<?php
require('header_inc.php');
require('db_inc.php');
require('aut_inc.php');

$log = "\niniciando tabela preços - " . date("Y-m-d H:i:s", time());
$log .= "\n\n".var_export($_REQUEST,true)."\n\n";

if(isset($_REQUEST['usuario'])){

	$retorno = '';

	$result = $mysqli->query("select 
			j.id as id,
			k.nome_simples as motoclube,
			l.titulo as evento,
			date_format(l.datahora,'%d/%m/%Y') as data
		from valor_praticado j 
		inner join motoclube k on k.id = j.motoclube_id 
		inner join evento l on l.id = j.evento_id
		inner join comanda cmd on cmd.evento_id = l.id
		inner join perfil per on per.id = cmd.consumidor_id
		where per.usuario = '{$_REQUEST['usuario']}' and cmd.status = 'aberta'");
 
	if(!$result){
		printf("\n\nERRO: %s\n\n",$mysqli->error);
		exit;
	}

	$tabela = $result->fetch_object();
	$result->close();

	if($tabela){
		$query = "select 
			k.titulo as produto,
			j.valor_unitario 
			from valor_praticado_item j
			inner join produto k on k.id = j.produto_id 
			where j.valor_praticado_id = {$tabela->id}";

		$result = $mysqli->query($query);
		if(!$result){
			printf("\n\nERRO: %s\n\n",$mysqli->error);
			exit;
		}
		$tabela_itens = array();
		while($item = $result->fetch_assoc()){
			array_push(
				$tabela_itens, array(
					'nome'  => $item['produto'],
					'preco' => $item['valor_unitario'],
				)
			);
		}
		$result->close();

		$retorno = 'tabela_ok';
		$json = array(
			'retorno' => $retorno,
			'cabecalho' => array(
				'motoclube' => $tabela->motoclube,
				'evento' => $tabela->evento,
				'data' => $tabela->data,
			),
			'itens' => $tabela_itens,
		);

		$mysqli->close();
	}else{
		$retorno = "nenhuma_tabela_disponivel";
		$json = array(
			'retorno' => $retorno,
		);
	}

}else{

	$retorno = "usuario_nok";
	$json = array(
		'retorno' => $retorno,
	);
}
echo json_encode($json);
file_put_contents('log.txt', $log);
