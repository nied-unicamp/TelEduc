<?php
$view_agenda="../view/";
include 'AgendaController.php';

//Pega ação passada pela agenda view
$acao = (isset($_GET['acao'])) ? $_GET['acao'] : $_POST['acao'];
$codcurso= $_GET['cod_curso'];
$coduser = (isset($_GET['coduser'])) ? $_GET['coduser'] : $_POST['coduser'];	
$origem =  $_POST['origem'];
$titulo= $_POST['novo_titulo'];

/* aÃ§Ã£o = Criar Nova Agenda - origem = ver_editar.php */
if ($acao=="criarAgenda") {


	$controlerAgenda = new AgendaController();
	// $controlerAgenda->Conecta();
	
	$cod_item= $controlerAgenda->criaAgenda($titulo,$codcurso, $coduser);
	
	if($cod_item){
		echo ("\n inseriu \n");
		$atualizacao = "true";

	}
	else{
		echo ("\n não inseriu \n");
		$atualizacao = "false";

	}
		header("Location:".$view_agenda."ver_linha.php?cod_curso=".$codcurso."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);

}




?>