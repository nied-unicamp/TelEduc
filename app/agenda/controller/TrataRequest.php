<?php
$ctrl_agenda = '../controller/';
include 'AgendaController.php';
//Pega ação passada pela agenda view
$acao = (isset($_GET['acao'])) ? $_GET['acao'] : $_POST['acao'];
$codcurso= $_GET['cod_curso'];
// $coduser= $_GET['coduser'];
$coduser = (isset($_GET['coduser'])) ? $_GET['coduser'] : $_POST['coduser'];
$titulo= $_POST['novo_titulo'];

/* aÃ§Ã£o = Criar Nova Agenda - origem = ver_editar.php */
if ($acao=="criarAgenda") {


	$controlerAgenda = new AgendaController();
	// $controlerAgenda->Conecta();
	
	$rs= $controlerAgenda->criaAgenda($titulo,$codcurso, $coduser);
	
	if($rs){
		echo ("\n inseriu \n");
	}
	else{
		echo ("\n não inseriu \n");
	}
}




?>