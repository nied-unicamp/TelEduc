<?php

/**
 * acoes_linha.php
 *
 * Controller acoes da linha da agenda do modulo agenda
 *
 * Neste arquivo são recebidas algumas varias por post, incluindo titulo do compromisso da agenda.
 * Em seguida é obtido o usuário logado no sistema a partir da sessão, depois as frases do idioma do usuario. 
 * Logo apos obtem os  diretorios base  de cada item (item é um nome de variavel usado dentro do sistema. Ex.: CaminhoCurso) na tabela Diretorios.
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
require_once $model_geral.'importar.inc';
require_once $model_agenda.'agenda.inc';

$cod_curso = (isset($_GET['cod_curso'])) ? $_GET['cod_curso'] : $_POST['cod_curso'];

$acao = (isset($_GET['acao'])) ? $_GET['acao'] : $_POST['acao'];

$input_files = $_FILES['input_files']['tmp_name'];

$origem = (isset($_GET['origem'])) ? $_GET['origem'] : $_POST['origem'];

$novo_titulo = $_POST['novo_titulo'];
//$novo_texto = $_POST['novo_texto'];
$cod_usuario_global=AcessoPHP::VerificaAutenticacao($cod_curso);

$sock=AcessoSQL::Conectar("");

$diretorio_arquivos=Agenda::RetornaDiretorio($sock,'Arquivos');
$diretorio_temp=Agenda::RetornaDiretorio($sock,'ArquivosWeb');
AcessoSQL::Desconectar($sock);

$sock=AcessoSQL::Conectar($cod_curso);

$cod_usuario = Usuarios::RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);

Usuarios::VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

$cod_item = (isset($_GET['cod_item'])) ? $_GET['cod_item'] : $_POST['cod_item'];

$dir_name = "agenda";
$dir_item_temp=Agenda::CriaLinkVisualizar($sock,$dir_name,$cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

/* aÃ§Ã£o = Criar Nova Agenda - origem = ver_editar.php */
if ($acao=="criarAgenda")
{
	
	$atualizacao = "true";

	$cod_item = Agenda::IniciaCriacao($sock, $cod_usuario, $cod_curso, $diretorio_temp, $novo_titulo, $novo_texto);
	if($cod_item == -1) //erro na criacao! algum parametro da func. esta vazio
	{
		$atualizacao="false";
		AcessoSQL::Desconectar($sock);
		header("Location:".$view_agenda."agenda.php?cod_curso=".$cod_curso."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
		exit();
	}
	
	$dir_name = "agenda";
	$dir_item_temp=Agenda::CriaLinkVisualizar($sock,$dir_name,$cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

	AcessoSQL::Desconectar($sock);
	header("Location:".$view_agenda."ver_linha_agenda.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
}

if ($acao=='anexar'){
	
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
	/* Verifica a existência do diretório a ser movido o arquivo */
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
		/* 50 - Atenção: o arquivo que você anexou não existe ou tem mais de %dMb. Se você digitou o nome do arquivo, procure certificar-se que ele esteja correto ou então selecione o arquivo a partir do botão Procurar (ou Browse). */
		$atualizacao = "false";
	}
	
	Agenda::AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
	header("Location:".$view_agenda."ver_linha_agenda.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
	}
	
	/* ação = Ativar agenda - origem = ver_editar.php */
if ($acao=="ativaragenda")
{
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
	header("Location:".$view_agenda."ver_editar.php?cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);
}
	
if ($acao=="apagarItem")
{
	$atualizacao = "true";
	Agenda::ApagarItem($sock,$cod_item,$cod_curso,$cod_usuario,$diretorio_arquivos,$diretorio_temp);
	
	AcessoSQL::Desconectar($sock);
	header("Location:".$view_agenda.$origem.".php?cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);
}
	
else if ($acao=="descompactar")
{
	$arq = $_GET['arq'];
	
    $atualizacao = "true";

    $dir_tmp=$dir_item_temp['diretorio'];
    $caminho="";

    $tmp=explode("/",$arq);
    for ($c=0;$c<count($tmp)-1;$c++)
      $caminho=$tmp[$c]."/";

    if(!($res=Arquivos::DescompactarArquivoZip($dir_tmp.$arq,$dir_tmp.$caminho))){
      $atualizacao = "false";
    }else{
      Arquivos::RemoveArquivo($dir_tmp.$arq);
    }

    /*Define o status de todos os arquivos descompactados como false para que nenhum deles seja consiredo como 
      arquivo de entrada */
    $dir = Arquivos::RetornaArrayDiretorio($dir_tmp.$caminho);
    foreach ($dir as $cod => $linha)
      Arquivos::AlteraStatusArquivo($dir_tmp.$caminho.ConversorTexto::ConverteUrl2Html($linha['Diretorio']."/".$linha['Arquivo']),false);
      	
    Agenda::AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
    AcessoSQL::Desconectar($sock);
    header("Location:".$view_agenda."ver_linha_agenda.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
}
else if ($acao == "validarImportacao"){
	
	$cod_topico_raiz = $_GET['cod_topico_raiz'];
	$cod_curso_todos = $_GET['cod_curso_todos'];
	$cod_ferramenta = $_GET['cod_ferramenta'];
	
	$sock = AcessoSQL::MudarDB($sock, "");
	$cod_cursos = explode(";", $cod_curso_todos);
	$tipo_curso_origem = $cod_cursos[0]; // B = Base, E = Extraido
	$cod_curso_origem = $cod_cursos[1];
	 
	$_SESSION['cod_topico_destino'] = $cod_topico_raiz;
	$_SESSION['cod_curso_origem'] = $cod_curso_origem;
	$_SESSION['flag_curso_extraido'] = ($tipo_curso_origem == 'E');
	 
	 
	if($cod_curso_origem)
	{
		$cod_usuario_import = Usuarios::RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso_origem);

		if ( Importar::FerramentaEstaCompartilhada($sock, $cod_curso_origem, $cod_ferramenta) ){
			$_SESSION['flag_curso_compartilhado'] = TRUE;
			header("Location:".$view_agenda."importar_agenda.php?cod_curso=".$cod_curso."&cod_assunto_pai=1&cod_curso_origem=".$cod_curso_origem);
		} else if ( $cod_usuario_import != NULL && Usuarios::EFormadorMesmo($sock,$cod_curso_origem,$cod_usuario_import) ){
			$_SESSION['flag_curso_compartilhado'] = FALSE;
			header("Location:".$view_agenda."importar_agenda.php?cod_curso=".$cod_curso."&cod_assunto_pai=1&cod_curso_origem=".$cod_curso_origem);
		} else {
			header("Location:".$view_agenda."importar_curso.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&acao=".$acao."&atualizacao=false");
		}
	}
	else
		header("Location:".$view_agenda."importar_curso.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&acao=ErroImportacao&atualizacao=false");
	 
}
else if ($acao == "importarItem"){
	 
	$cod_curso_destino = $cod_curso;
	$cod_topico_destino = $_SESSION['cod_topico_destino'];
	$cod_curso_origem = $_SESSION['cod_curso_origem'];
	$flag_curso_extraido = $_SESSION['flag_curso_extraido'];
	$flag_curso_compartilhado = $_SESSION['flag_curso_compartilhado'];
	$array_topicos_origem = $cod_assunto;
	$array_itens_origem = $cod_pergunta;
	$dirname = "agenda";
	$nome_tabela = "Agenda";
	$cod_itens_import = $_POST['cod_itens_import'];
	 
	$sock=AcessoSQL::Conectar("");
	if ($curso_extraido)
		$diretorio_arquivos_origem = Agenda::RetornaDiretorio($sock, 'Montagem');
	else
		$diretorio_arquivos_origem = Agenda::RetornaDiretorio($sock, 'Arquivos');

	// Raiz do diretorio de arquivos do curso PARA O QUAL serao importados
	// os itens.
	$diretorio_arquivos_destino = Agenda::RetornaDiretorio($sock, 'Arquivos');
	$diretorio_temp = Agenda::RetornaDiretorio($sock, 'ArquivosWeb');

	Agenda::ImportarAgenda($cod_usuario, $cod_curso_destino, $cod_itens_import, $cod_curso_origem, $diretorio_arquivos_origem, $diretorio_arquivos_destino, $diretorio_temp);

	header("Location:".$view_agenda."ver_editar.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=".$cod_topico_destino."&acao=".$acao."&atualizacao=true");
}
?>