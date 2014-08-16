<?php
require('header_inc.php');
require('db_inc.php');
require('aut_inc.php');

$log = "\niniciando comanda - " . date("Y-m-d H:i:s", time());
$log .= "\n\n".var_export($_REQUEST,true)."\n\n";

if(isset($_REQUEST['usuario'])){

	$retorno = '';

	$result = $mysqli->query("select 
			j.id as id,
			j.codigo_legivel,
			k.nome_simples as motoclube,
			l.titulo as evento,
			m.nome as consumidor,
			date_format(j.datahora,'%d/%m/%Y') as data,
			j.status 
		from comanda j 
		inner join motoclube k on k.id = j.motoclube_id 
		inner join evento l on l.id = j.evento_id
		inner join perfil m on m.id = j.consumidor_id
		where m.usuario = '{$_REQUEST['usuario']}' and j.status = 'aberta'");
	if(!$result){
		$log .= "\n\nERRO: {$mysqli->error}\n\n";
		printf("\n\nERRO: %s\n\n",$mysqli->error);
	}
	$comanda = $result->fetch_object();

	$result->close();

	if($comanda){
		$query = "select 
			k.titulo as produto,
			j.quantidade,
			j.valor_unitario 
			from comanda_item j
			inner join produto k on k.id = j.produto_id 
			where j.comanda_id = {$comanda->id}";

		//var_dump($query);

		$result = $mysqli->query($query);

		$comanda_itens = array();
		while($item = $result->fetch_assoc()){
			array_push( 
				$comanda_itens, array(
					'nome'  => $item['produto'],
					'qtd'   => $item['quantidade'],
					'preco' => $item['valor_unitario'],
				)
			);
		}
		$result->close();
		$mysqli->close();
		
		$retorno = 'comanda_ok';
		
		$json = array(
			'retorno' => $retorno,
			'cabecalho' => array(
				'codigo' => $comanda->codigo_legivel,
				'motoclube' => $comanda->motoclube,
				'evento' => $comanda->evento,
				'data' => $comanda->data,
				'cliente' => $comanda->consumidor,
			),
			'itens' => $comanda_itens,
		);
		
	}else{
		$retorno = "comanda_nok";
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
