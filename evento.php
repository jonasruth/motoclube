<?php
require('header_inc.php');
require('db_inc.php');
require('aut_inc.php');

$log = "\niniciando evento - " . date("Y-m-d H:i:s", time());
$log .= "\n\n".var_export($_REQUEST,true)."\n\n";

if(isset($_REQUEST['usuario'])){

	$retorno = '';

	$result = $mysqli->query("select 
			ev.titulo as titulo,
			ev.sede as sede,
			date_format(ev.datahora,'%d/%m') as data,
			CASE WHEN date_format(ev.datahora,'%Y') <> YEAR(ev.datahora) THEN date_format(ev.datahora,'%Y') ELSE ''END as ano,
			date_format(ev.datahora,'%H\h%i') as hora,
			mc.nome as motoclube
		from evento ev
		inner join motoclube mc on mc.id = ev.motoclube_id
		where ev.status in ('','cxaberto')
		and DATE(ev.datahora) >= DATE(CURDATE()-1)");
		
	if(!$result){
		printf("\n\nERRO: %s\n\n",$mysqli->error);
		exit;
	}
	
	if($result->num_rows>0){
		
		$evento_itens = array();
		while($item = $result->fetch_assoc()){
			array_push( 
				$evento_itens, array(
					'data'      => $item['data'],
					'ano'       => $item['ano'],
					'hora'      => $item['hora'],
					'titulo'    => $item['titulo'],
					'motoclube' => $item['motoclube'],
					'sede'      => $item['sede'],
				)
			);
		}
		
		$result->close();
		$mysqli->close();
			
		$retorno = 'evento_ok';
		$json = array(
			'retorno' => $retorno,
			'itens' => $evento_itens
		);
	}else{
		$retorno = "nenhum_registro_encontrado";
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