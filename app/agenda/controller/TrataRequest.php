<?php
$view_agenda= '../view/';
$ctrl_agenda = '../controller/';

include 'AgendaController.php';


//Pega acao passada pela agenda view
$acao = (isset($_GET['acao'])) ? $_GET['acao'] : $_POST['acao'];
$cod_item = (isset($_GET['cod_item'])) ? $_GET['cod_item'] : $_POST['cod_item'];
$cod_curso = (isset($_GET['cod_curso'])) ? $_GET['cod_curso'] : $_POST['cod_curso'];
$cod_usuario = (isset($_GET['cod_usuario'])) ? $_GET['cod_usuario'] : $_POST['cod_usuario'];
$origem =  $_POST['origem'];
$titulo= $_POST['novo_titulo'];

/* acao = Criar Nova Agenda - origem = ver_editar.php */
if ($acao=="criarAgenda") {


	$controlerAgenda = new AgendaController();
	// $controlerAgenda->Conecta();
	
	$cod_item= $controlerAgenda->criaAgenda($titulo,$cod_curso, $cod_usuario);
	
	if($cod_item){
		echo ("\n inseriu \n");
		$atualizacao = "true";

	}
	else{
		echo ("\n não inseriu \n");
		$atualizacao = "false";

	}
		header("Location:".$view_agenda."ver_linha.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);

}
else if ($acao=="ativaragenda")
{
	$agendaControler = new AgendaController();
	
	$atualizacao = "true";
	$agendaControler->ativaAgenda($cod_item, $cod_usuario, $cod_curso);
	
	header("Location:../view/agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&acao=".$acao."&atualizacao=true");
}
else if ($acao=="apagarSelecionados")
{
	$agendaControler = new AgendaController();
	
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
?>