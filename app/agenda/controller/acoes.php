<?php

$ctrl_agenda = '../controller/';

include $ctrl_agenda.'AgendaController.php';

$acao = (isset($_GET['acao'])) ? $_GET['acao'] : $_POST['acao'];
$cod_item = (isset($_GET['cod_item'])) ? $_GET['cod_item'] : $_POST['cod_item'];
$cod_curso = (isset($_GET['cod_curso'])) ? $_GET['cod_curso'] : $_POST['cod_curso'];
$cod_usuario = (isset($_GET['cod_usuario'])) ? $_GET['cod_usuario'] : $_POST['cod_usuario'];
//$origem = (isset($_GET['origem'])) ? $_GET['origem'] : $_POST['origem'];

$agendaControler = new AgendaController();

if ($acao=="ativaragenda")
{
	$atualizacao = "true";
	$agendaControler->ativaAgenda($cod_item, $cod_usuario, $cod_curso);
	
	header("Location:../view/agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&acao=".$acao."&atualizacao=true");
}

if ($acao=="apagarSelecionados")
{
	$origem = $_POST['origem'];
	$cod_itens = $_POST['cod_itens'];
	
	$atualizacao = "true";

	$cod_itens_array = explode(",", $cod_itens);

	if ($cod_itens!=""){
		foreach ($cod_itens_array as $cod => $linha){
			$agendaControler->apagaAgenda($linha);
			//ApagarItem($sock,$linha,$cod_curso,$cod_usuario,$diretorio_arquivos,$diretorio_temp);
		}
	}else{
		$atualizacao = "false";
	}

	//Desconectar($sock);
	header("Location:../view/".$origem.".php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&acao=".$acao."&atualizacao=".$atualizacao);
}
