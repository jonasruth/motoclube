<?php

require("../header_inc.php");
require("../db_inc.php");

// validar primeiro
if(isset($_POST['usuario']) || isset($_POST['email']) || isset($_POST['senha'])){
		
	// quebrar
	$usuario = $mysqli->real_escape_string($_REQUEST['usuario']);
	$email = $mysqli->real_escape_string($_REQUEST['email']);
	$senha = $mysqli->real_escape_string($_REQUEST['senha']);

	// validar
	$erros = array();
	if(empty($usuario) || strlen($usuario)<6 || strlen($usuario)>12){
		array_push($erros, "O nome de usuário precisa ter entre 6 e 12 caracteres.");
	}

	if(empty($email) || strlen($email)<6 || strlen($email)>30){
		array_push($erros, "Seu email não foi considerado válido.");
	}

	if(empty($senha) || strlen($senha)<6 || strlen($senha)>12){
		array_push($erros, "Sua senha precisa ter entre 6 e 12 caracteres.");
	}

	if(empty($erros)){

		// verificar no banco
		$result = $mysqli->query("
			select
				(select count(id) from perfil where email = '{$email}') as email_existe,
				(select count(id) from perfil where usuario = '{$usuario}') as usuario_existe");
				
				
		$existente = $result->fetch_object();
		$result->close();



		$cadastrado = false;

		if($existente){
			
			if($existente->email_existe>0){
				$retorno = 'email_existente';
				array_push($erros, "O email \"{$email}\" já está cadastrado em nosso sistema.");
			}
		
			if($existente->usuario_existe>0){
				array_push($erros, "O nome de usuário \"{$usuario}\" não está mais disponível.");
			} 
			
			if(empty($erros)){
				$strquery = "insert into perfil (usuario,email,senha,nome,foto_perfil,apelido,motoclube_id,sede,graduacao)
				values ('{$usuario}','{$email}','{$senha}','','default.jpg','',1,'','')";
				
				$mysqli->query($strquery);	
				if($mysqli->affected_rows>0){
					$retorno = 'cadastro_success';
					$cadastrado = true;
				}	
			}
		}

		$mysqli->close();		

		if($cadastrado){
			header("Location: welcome.php");
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>MeuBandoApp!!</title>

    <!-- Bootstrap core CSS -->
   <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
   <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
   <link rel="stylesheet" href="./cadastro.css">
   

    <!-- Custom styles for this template -->
    <link href="./jumbotron-narrow.css" rel="stylesheet">

  </head>

  <body>

    <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li><a href="home">Home</a></li>
          <li class="active"><a href="cadastro">Cadastro</a></li>
		  <li><a href="download">Download</a></li>
        </ul>
        <h3 class="text-muted">MeuBandoApp!!!</h3>
      </div>

      <div class="jumbotron">
        <h1>Cadastre-se</h1>
        <p class="lead">É rapidinho! Você vai demorar apenas 1 minuto para se cadastrar.</p>
      </div>
	
<?php if(true): ?>
	
	  <form action="cadastro" method="post">
	
		  <div class="row marketing">
		  
			<?php if(!empty($erros)): ?>
			<div class="col-lg-12 cadastro-erro">
			
				<ul>
				<?php foreach($erros as $erro):?>
				<li><?php echo $erro ?></li>
				<?php endforeach; ?>
				</ul>
			
			</div>
			<?php endif; ?>
		   <div class="col-lg-12">
		   
			  <h4>Seu email</h4>
			  <p>
			  <input class="form-control" name="email" value="<?php echo empty($email) ? "" : $email ?>" />
			  </p>
			
			  <h4>Motoclube</h4>
			  <p>Para fins demonstrativos, você será adicionado ao <strong>Gárgulas Motoclube</strong></p>

			  <h4>Seu nome de usuário</h4>
			  <p>Informe seu nome de usuário com no mínimo 6 e no máximo 12 caracteres</p>
			  <p>
			  <input class="form-control" name="usuario" value="<?php echo empty($usuario) ? "" : $usuario ?>"/>
			  </p>
			  
			  <h4>Crie uma senha</h4>
			  <p>Informe sua senha com no mínimo 6 e no máximo 12 caracteres</p>
			  <p>
			  <input class="form-control" type="password" name="senha" />
			  </p>
			  
			  
			</div>
			
			<div class="col-lg-12">
				<p align="center"><button type="submit" class="btn btn-lg btn-success" role="button">Cadastrar!</button></p>
			</div>
		  
		  </div>
	  
	  </form>
	  
<?php endif; ?>

      <div class="footer">
        <p>&copy; MeuBandoApp 2014</p>
      </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  </body>
</html>
