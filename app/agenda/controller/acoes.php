<?php

$ctrl_agenda = '../controller/';

include $ctrl_agenda.'AgendaController.php';

$acao = $_GET['acao'];
$cod_item = $_GET['cod_item'];
$cod_curso = $_GET['cod_curso'];

$agendaControler = new AgendaController();

if ($acao=="ativaragenda")
{
	$atualizacao = "true";
	$agendaControler->ativaAgenda($cod_item, $cod_usuario, $cod_curso);
	
	header("Location:../view/agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario);
}
