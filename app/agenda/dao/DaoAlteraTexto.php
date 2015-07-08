<?php 

require_once '../../../lib/Conexao.php';

$cod_item=$_POST['cod_item'];
$novo_titulo=$_POST['texto'];


	 
	$sql = "update Agenda_item set texto='".$novo_titulo."' where cod_item=".$cod_item;
	$conexao = new Conexao();
	 
	$conexao->Conectar();
	 
	$res = $conexao->Enviar($sql);
	 	 
	$conexao->Desconectar();
	 
	
?>php 
