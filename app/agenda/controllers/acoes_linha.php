<?php

/**
 * acoes_linha.php
 *
 * Controller acoes da linha da agenda do modulo agenda
 *
 * Neste arquivo sгo recebidas algumas varias por post, incluindo titulo do compromisso da agenda.
 * Em seguida й obtido o usuбrio logado no sistema a partir da sessгo, depois as frases do idioma do usuario. 
 * Logo apos obtem os  diretorios base  de cada item (item й um nome de variavel usado dentro do sistema. Ex.: CaminhoCurso) na tabela Diretorios.
 * E cria-se uma nova agenda. 
 * 
 */
/**
 *
 */

$ferramenta_geral = 'geral';
$ferramenta_agenda = 'agenda';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';
$view_agenda = '../../'.$ferramenta_agenda.'/views/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';

$cod_curso = (isset($_GET['cod_curso'])) ? $_GET['cod_curso'] : $_POST['cod_curso'];

$acao = (isset($_GET['acao'])) ? $_GET['acao'] : $_POST['acao'];

$input_files = $_FILES['input_files']['name'];

$origem = (isset($_GET['origem'])) ? $_GET['origem'] : $_POST['origem'];

$novo_titulo = $_POST['novo_titulo'];
//$novo_texto = $_POST['novo_texto'];
$cod_usuario_global=AcessoPHP::VerificaAutenticacao($cod_curso);

$sock=AcessoSQL::Conectar("");

$lista_frases=Linguas::RetornaListaDeFrases($sock,1);

$diretorio_arquivos=Agenda::RetornaDiretorio($sock,'Arquivos');
$diretorio_temp=Agenda::RetornaDiretorio($sock,'ArquivosWeb');

AcessoSQL::Desconectar($sock);

$sock=AcessoSQL::Conectar($cod_curso);

$cod_usuario = Usuarios::RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);

Usuarios::VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

$dir_name = "agenda";
$dir_item_temp=Agenda::CriaLinkVisualizar($sock,$dir_name,$cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

/* aГ§ГЈo = Criar Nova Agenda - origem = ver_editar.php */
if ($acao=="criarAgenda")
{
	
	$atualizacao = "true";

	$cod_item = Agenda::IniciaCriacao($sock, $cod_usuario, $cod_curso, $diretorio_temp, $novo_titulo, $novo_texto);
	if($cod_item == -1) //erro na criacao! algum parametro da func. esta vazio
	{
		echo "chegou aqui\n";
		$atualizacao="false";
		AcessoSQL::Desconectar($sock);
		header("Location:".$view_agenda."agenda.php?cod_curso=".$cod_curso."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
		exit();
	}

	AcessoSQL::Desconectar($sock);
	header("Location:".$view_agenda."ver_linha_agenda.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
}

if ($acao=='anexar'){
	
	$cod_item = $_POST['cod_item'];

	$atualizacao = "true";

	// Analisa nome do arquivo
	$nome_arquivo = $_FILES['input_files']['name'];

	// Se possuir acentos ou outros caracteres problematicos
	if (Agenda::VerificaAnexo($nome_arquivo) == 0)
	{
		// Nao realiza upload de arquivos com acentos
		$acao = "nomeAnexo";
		$atualizacao = "false";
		header("Location:".$view_agenda."ver_linha_agenda.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
		exit;
	}
	/* Verifica a existкncia do diretуrio a ser movido o arquivo */
	if (!file_exists($diretorio_arquivos."/".$cod_curso)) {
		Arquivos::CriaDiretorio($diretorio_arquivos."/".$cod_curso);
	}
	if (!file_exists($diretorio_arquivos."/".$cod_curso."/agenda/")) {
		Arquivos::CriaDiretorio($diretorio_arquivos."/".$cod_curso."/agenda/");
	}
	if (!file_exists($diretorio_arquivos."/".$cod_curso."/agenda/".$cod_item."/")) {
		Arquivos::CriaDiretorio($diretorio_arquivos."/".$cod_curso."/agenda/".$cod_item."/");
	}
	
	$dir=$diretorio_arquivos."/".$cod_curso."/agenda/".$cod_item."/";
	
	if (!Arquivos::RealizaUpload($input_files,$dir.$nome_arquivo))
	{
		/* 50 - Atenзгo: o arquivo que vocк anexou nгo existe ou tem mais de %dMb. Se vocк digitou o nome do arquivo, procure certificar-se que ele esteja correto ou entгo selecione o arquivo a partir do botгo Procurar (ou Browse). */
		$atualizacao = "false";
	}
	
	Agenda::AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
	header("Location:".$view_agenda."ver_linha_agenda.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
	}
	
	/* aзгo = Ativar agenda - origem = ver_editar.php */
	if ($acao=="ativaragenda")
	{
		$cod_item=$_GET['cod_item'];
		$atualizacao = "true";
		Agenda::AtivarAgenda($sock, $cod_item, $cod_usuario);
	
		AcessoSQL::Desconectar($sock);
		header("Location:".$view_agenda."agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario);
	}
	
	if ($acao=="apagarSelecionados")
	{
		$origem = $_POST['origem'];
		$cod_itens = $_POST['cod_itens'];
		$atualizacao = "true";
	
		$cod_itens_array = explode(",", $cod_itens);
	
		if ($cod_itens!=""){
			foreach ($cod_itens_array as $cod => $linha){
				Agenda::ApagarItem($sock,$linha,$cod_curso,$cod_usuario,$diretorio_arquivos,$diretorio_temp);
			}
		}else{
			$atualizacao = "false";
		}
	
		AcessoSQL::Desconectar($sock);
		header("Location:".$view_agenda.$origem.".php?cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);
	}
	
	if ($acao=="apagarItem")
	{
		$cod_item = $_GET['cod_item'];
		$atualizacao = "true";
		Agenda::ApagarItem($sock,$cod_item,$cod_curso,$cod_usuario,$diretorio_arquivos,$diretorio_temp);
	
		AcessoSQL::Desconectar($sock);
		header("Location:".$view_agenda.$origem.".php?cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);
	}
?>