<?php
require("../header_inc.php");
require("../db_inc.php");

$sedes = array('São José dos Pinhais','Blumenau','Lapa','Florianópolis','Curitiba');

$operacao = null;

if(isset($_GET['evento_id'])){
	
	$evento = new stdClass();
	$evento->id = $_GET['evento_id'];
	
	
	if(isset($_GET['op'])&&$_GET['op']==='abrir'){
		$operacao = 'cxabrir';
	}else if(isset($_GET['op'])&&$_GET['op']==='fechar'){
		$operacao = 'cxfechar';
	}else{
		$operacao = 'preupdate';
	}
	
	
} else if(isset($_POST['evento'])){
	$evento = new stdClass();
	$evento->id = $_POST['evento']['id'];
	$evento->titulo = $_POST['evento']['titulo'];
	$evento->datahora = $_POST['evento']['datahora'];
	// $evento->datahora;
	//$evento->datahora = date("Y-m-d\TH:i",$evento->datahora);
	$evento->motoclube = 1;
	$evento->sede = $_POST['evento']['sede'];

	if(isset($evento->id) && $evento->id>0){
		$operacao = "update";
	}else{
		$operacao = 'insert';
	}
}else{
	$evento = new stdClass();
	$evento->id = '';
	$evento->titulo = '';
	$evento->datahora = '';
	$evento->motoclube = '';
	$evento->sede = '';
}

if($operacao === 'insert'){
	$strquery = "insert into evento (motoclube_id,sede,datahora,titulo) values ({$evento->motoclube},'{$evento->sede}','{$evento->datahora}','{$evento->titulo}');";
	$mysqli->query($strquery);	
	header( 'Location:evento_list.php?msg=insert_ok' );
	exit;
}else if($operacao === 'update'){
	$strquery = "update evento set motoclube_id = {$evento->motoclube}, sede = '{$evento->sede}', datahora = '{$evento->datahora}', titulo ='{$evento->titulo}' where id = {$evento->id};";
	$mysqli->query($strquery);
	header( 'Location:evento_list.php?msg=update_ok' );
	exit;
}else if($operacao === 'preupdate'){
	$sqlquery = "SELECT j.id,j.titulo,date_format(j.datahora,'%Y-%m-%dT%H:%i') as datahora,j.sede,k.nome as motoclube FROM evento j INNER JOIN motoclube k ON k.id = j.motoclube_id where j.id={$evento->id}";
	$result = $mysqli->query($sqlquery);
	$evento = $result->fetch_object();
}else if($operacao ==='cxabrir'){
	$strquery = "update evento set status='cxaberto' where id = {$evento->id};";
	$mysqli->query($strquery);
	header( 'Location:evento_list.php?msg=cxaberto_ok' );
	exit;
}else if($operacao ==='cxfechar'){
	$strquery = "update evento set status='cxfechado' where id = {$evento->id};";
	$mysqli->query($strquery);
	header( 'Location:evento_list.php?msg=cxfechado_ok' );
	exit;
}

$sqlquery = "SELECT j.id,j.titulo,date_format(j.datahora,'%d\/%m\/%Y às %H\h%i') as datahora,j.sede,k.nome as motoclube,status FROM evento j INNER JOIN motoclube k ON k.id = j.motoclube_id;";
$result = $mysqli->query($sqlquery);
$eventos = array();
while($item = $result->fetch_object()){
	array_push($eventos,$item);
}
$result->close();
$mysqli->close();
?>
<!DOCTYPE html>
<html>
<body>

<head>
<meta charset="UTF-8"/>
<link type="text/css" rel="stylesheet" href="admin.css"/>
</head>
<div class="aviso">
	<p>Esta página utiliza HTML5 e alguns recursos só estão disponíveis para o <b>Google Chrome</b>. Por favor utilize o Google Chrome. Resolução mínima 1280px (largura).</p>
</div>
<div class="conteudo">
	
	<h1>Manter Eventos</h1>
	
	<?php include('menu.php')?>
	
	<div style="margin:10px 0; text-align:right;">
		<a href="evento_list.php" class="buttonlink">Adicionar novo</a>
	</div>
	
	<div class="listagem">
		<table>
			<caption>Listagem de Eventos</caption>
			<tr>
				<th>Data</th>
				<th>Título</th>
				<th>Motoclube</th>
				<th>Sede</th>
				<th></th>
			</tr>
			<?php if(!count($eventos)>0): ?>
			<tr class="no-records">
				<td colspan="5">Nenhum registro encontrado</td>
			</tr>
			<?php endif; ?>
			<?php foreach($eventos as $key=>$item):  ?>
			<tr class="<?php echo $key % 2 ? 'even' : 'odd'  ?>">
				<td><?php echo $item->datahora ?></td>
				<td><?php echo $item->titulo ?></td>
				<td><?php echo $item->motoclube ?></td>
				<td><?php echo $item->sede ?></td>
				<td>
					<?php if(!$item->status): ?>
					<a href="evento_list.php?evento_id=<?php echo $item->id ?>">Editar</a>
					<a href="evento_list.php?op=abrir&evento_id=<?php echo $item->id ?>" onclick="return confirm('Deseja realmente abrir o caixa?\nDepois não será possível editar o evento')">Abrir Caixa</a>
					<?php elseif($item->status==='cxaberto'): ?>
					<a href="evento_list.php?op=fechar&evento_id=<?php echo $item->id ?>" onclick="return confirm('Deseja realmente fechar o caixa?\nNão será possível desfazer alteração')">Fechar Caixa</a>
					<?php endif; ?>
				</td>
			</tr>
			<?php endforeach; ?>
			
		</table>
	</div>
	
	<div class="form">
	
		<h2>Criar/Editar</h2>
		<form action="evento_list.php" method="post">
		
			<input type="hidden" name="evento[id]" value="<?php echo $evento->id ?>"/>
		
			<div class="form-line">
				<label for="titulo">Título</label>
				<input id="titulo" type="text" name="evento[titulo]" value="<?php echo $evento->titulo ?>" maxlength="30" placeholder="Título do evento" required="true"/>
			</div>

			<div class="form-line">
				<label for="data">Data</label>
				<input id="data" type="datetime-local" name="evento[datahora]" value="<?php echo $evento->datahora ?>" maxlength="10" placeholder="Data de início" required="true">
			</div>

			<div class="form-line">
			<label for="">Motoclube</label>
			<span class="campo-fixo">Gárgulas Moto Clube</span> <span class="campo-obs">(Fixo por enquanto)</span>
			</div>

			<div class="form-line">
			<label for="sede">Sede</label>
				<select id="sede" name="evento[sede]" required="true">
					<option value="">--Selecione--</option>
					<?php foreach($sedes as $item):  ?>
					<option <?php echo $evento->sede == $item ? 'selected' : ''  ?> value="<?php echo $item ?>"><?php echo $item ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="form-line action-bar">
				<button type="submit">Gravar</button>
			</div>
		
		</form>
	</div>

</div>

</body>
</html>