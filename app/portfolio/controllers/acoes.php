<?php
$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
$view_portfolio = '../../'.$ferramenta_portfolio.'/views/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$cod_curso = ((isset($_GET['cod_curso'])) ? $_GET['cod_curso'] : $_POST['cod_curso']);
$cod_topico_raiz = ((isset($_GET['cod_topico_raiz'])) ? $_GET['cod_topico_raiz'] : $_POST['cod_topico_raiz']);
$cod_grupo_portfolio = ((isset($_GET['cod_grupo_portfolio'])) ? $_GET['cod_grupo_portfolio'] : $_POST['cod_grupo_portfolio']);
$cod_usuario_portfolio = ((isset($_GET['cod_usuario_portfolio'])) ? $_GET['cod_usuario_portfolio'] : $_POST['cod_usuario_portfolio']);
$novo_nome = $_POST['novo_nome'];
$acao = ((isset($_GET['acao'])) ? $_GET['acao'] : $_POST['acao']);
$cod_item = ((isset($_GET['cod_item'])) ? $_GET['cod_item'] : $_POST['cod_item']);
$input_files = $_FILES['input_files']['tmp_name'];

$cod_usuario_global=AcessoPHP::VerificaAutenticacao($cod_curso);

$sock1=AcessoSQL::Conectar("");

$diretorio_arquivos=Portfolio::RetornaDiretorio($sock1,'Arquivos');
$diretorio_temp=Portfolio::RetornaDiretorio($sock1,'ArquivosWeb');

AcessoSQL::Desconectar($sock1);

$sock=AcessoSQL::Conectar($cod_curso);

$cod_usuario = Usuarios::RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
Usuarios::VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

$dir_item_temp=Portfolio::CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

/* ação = Anexar Arquivo - origem = ver.php */
if ($acao=='anexar'){

	$atualizacao="true";

	// Analisa nome do arquivo
	$nome_arquivo = $_FILES['input_files']['name'];

	// Se possuir acentos ou outros caracteres problematicos
	if (Portfolio::VerificaAnexo($nome_arquivo) == 0)
	{
		// Nao realiza upload de arquivos com acentos
		$acao = "nomeAnexo";
		$atualizacao = "false";
		header("Location:../views/ver.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&acao=".$acao."&atualizacao=".$atualizacao);
	}

	/* Verifica a existência do diretório a ser movido o arquivo */
	if (!file_exists($diretorio_arquivos."/".$cod_curso)) {
		Arquivos::CriaDiretorio($diretorio_arquivos."/".$cod_curso);
	}
	if (!file_exists($diretorio_arquivos."/".$cod_curso."/portfolio/")) {
		Arquivos::CriaDiretorio($diretorio_arquivos."/".$cod_curso."/portfolio/");
	}
	if (!file_exists($diretorio_arquivos."/".$cod_curso."/portfolio/item/")) {
		Arquivos::CriaDiretorio($diretorio_arquivos."/".$cod_curso."/portfolio/item/");
	}
	if (!file_exists($diretorio_arquivos."/".$cod_curso."/portfolio/item/".$cod_item."/")) {
		Arquivos::CriaDiretorio($diretorio_arquivos."/".$cod_curso."/portfolio/item/".$cod_item."/");
	}

	$dir=$diretorio_arquivos."/".$cod_curso."/portfolio/item/".$cod_item."/";

	//converte o nome para UTF-8, pois o linux insere com essa codificação o arquivo
	//nas pasta de destino.
	//$nome_arquivo = mb_convert_encoding($nome_arquivo, "UTF-8", "ISO-8859-1");

	if (!Arquivos::RealizaUpload($input_files,$dir.$nome_arquivo))
	{
		$atualizacao="false";
	}

	Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 1);
	
	header("Location:../views/ver.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&acao=".$acao."&atualizacao=".$atualizacao);
}
if ($acao=="criarItem"){
	$cod_item=Portfolio::IniciaCriacao ($sock, $cod_topico_raiz, $cod_usuario, $cod_grupo_portfolio, $cod_curso, $diretorio_temp, $novo_nome);
	$atualizacao="true";
	/* Adiciona a Novidade */
	if ($cod_grupo_portfolio == NULL || !isset($cod_grupo_portfolio)){
		/* Portfolio Individual */
		Usuarios::AtualizaFerramentasNovaUsuario($sock, "15", $cod_usuario);
	} else {
		/* Portifolio em Grupo */
		Usuarios::AtualizaFerramentasNovaGrupo($sock,"15",$cod_grupo_portfolio);
	}
}
else if ($acao=="apagarSelecionados"){

	$atualizacao="true";
	
	$cod_topicos = $_POST['cod_topicos'];
	$cod_itens = $_POST['cod_itens'];
	
	$cod_topicos_array = explode(",", $cod_topicos);
	$cod_itens_array = explode(",", $cod_itens);

	if ($cod_topicos!=""){
		foreach ($cod_topicos_array as $cod => $linha){
			Portfolio::ApagarTopico($sock, $linha, $cod_usuario);
		}
	}

	if ($cod_itens!=""){
		foreach ($cod_itens_array as $cod => $linha){
			Portfolio::ApagarItem($sock, $linha, $cod_usuario);
		}
	}

	AcessoSQL::Desconectar($sock);

	header("Location:".$view_portfolio."portfolio.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&acao=".$acao."&atualizacao=".$atualizacao);
	exit; /* Aparentemente sem motivo, ele passava reto por esse header */
}

else if ($acao=="descompactar")
{
	$arq = $_GET['arq'];

	$dir_tmp=$dir_item_temp['diretorio'];
	$caminho="";

	$tmp=explode("/",$arq);
	for ($c=0;$c<count($tmp)-1;$c++)
		$caminho=$tmp[$c]."/";

	$res=Arquivos::DescompactarArquivoZip($dir_tmp.$arq,$dir_tmp.$caminho);
	$atualizacao="true";
	if(!$res){
		$atualizacao="false";
	}else{
		Arquivos::RemoveArquivo($dir_tmp.$arq);
	}
	
	Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 1);
	
	header("Location:../views/ver.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&acao=".$acao."&atualizacao=".$atualizacao);
}
else if ($acao=="comentar")
{
	$comentario = $_POST['comentario'];

	$atualizacao="true";

	$cod_comentario = Portfolio::PegaUltimoCodComentario($sock, $cod_item, $cod_usuario);

	Portfolio::InsereComentario ($sock, $cod_comentario, $comentario);

	if (!file_exists($diretorio_arquivos."/".$cod_curso))
		Arquivos::CriaDiretorio($diretorio_arquivos."/".$cod_curso);
	if (!file_exists($diretorio_arquivos."/".$cod_curso."/portfolio/"))
		Arquivos::CriaDiretorio($diretorio_arquivos."/".$cod_curso."/portfolio/");
	if (!file_exists($diretorio_arquivos."/".$cod_curso."/portfolio/comentario/"))
		Arquivos::CriaDiretorio($diretorio_arquivos."/".$cod_curso."/portfolio/comentario/");
	if (!file_exists($diretorio_arquivos."/".$cod_curso."/portfolio/comentario/".$cod_comentario."/"))
		Arquivos::CriaDiretorio($diretorio_arquivos."/".$cod_curso."/portfolio/comentario/".$cod_comentario."/");

	$dir = $diretorio_arquivos."/".$cod_curso."/portfolio/comentario/".$cod_comentario."/";

	$erro=false;

	if(is_array($_FILES['input_files']['name'])&&count($_FILES['input_files']['name'])>0)
	foreach($_FILES['input_files']['name'] as $cod => $linha){
		//$linha = RetiraEspacoEAcentos($linha);
		$linha = mb_convert_encoding($linha, "UTF-8", "ISO-8859-1");
		if (!Arquivos::RealizaUpload($_FILES['input_files']['tmp_name'][$cod],$dir.$linha))
		{
			$erro=true;
		}
	}

	Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 1);

	if($erro){
		$atualizacao="false";
	}

	header("Location:../views/comentarios.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_usuario=".$cod_usuario."&acao=".$acao."&atualizacao=".$atualizacao);
}

AcessoSQL::Desconectar($sock);
?>