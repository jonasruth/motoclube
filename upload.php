<?php
require('header_inc.php');
require('db_inc.php');
require('aut_inc.php');

$log = "\niniciando - " . date("Y-m-d H:i:s", time());
$log .= "\n\n".var_export($_REQUEST,true)."\n\n";
$log .= "\n\n".var_export($_FILES,true)."\n\n";

if(isset($_REQUEST['usuario'])){
	if(isset($_FILES['uploadedfile'])){

		// Where the file is going to be placed
		$target_path = "foto_perfil/";
		/* Add the original filename to our target path.
		Result is "uploads/filename.extension" */
		//$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);
		$target_path = $target_path . basename( $_FILES['uploadedfile']['name'] ); 

		$retorno = '';
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
			$log .= "The file ".  basename( $_FILES['uploadedfile']['name']).
			" has been uploaded to ".$target_path;
			
			$mysqli->query("update perfil set  foto_perfil = '{$_FILES['uploadedfile']['name']}' 
							where usuario = '{$_REQUEST['usuario']}'");
			
			$retorno = 'upload_sucesso';
		} else{
			$log .= "\nThere was an error uploading the file, please try again!";
			$log .= "\nfilename: " .  basename( $_FILES['uploadedfile']['name']);
			$log .= "\ntarget_path: " .$target_path;
			$retorno = 'upload_fail';
		}
		$log .= "\nfim.";
	}else{
		$retorno = "uploadedfile_nok";
	}
}else{
	$retorno = "usuario_nok";
}

file_put_contents('log.txt', $log);

echo json_encode(
	array(
		'retorno' => $retorno,
	)
);
