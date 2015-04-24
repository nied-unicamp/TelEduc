<?php

$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';
	
$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
	
require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$cod_curso = $_POST['cod_curso'];
$cod_usuario = $_POST['cod_usuario'];
$cod_grupo_portfolio = $_POST['cod_grupo_portfolio'];
$cod_usuario_portfolio = $_POST['cod_usuario_portfolio'];
$cod_topico_raiz = $_POST['cod_topico_raiz'];
$dirname = $_POST['dirname'];
$novo_nome = $_POST['novo_nome'];

$sock = AcessoSQL::Conectar("");

$diretorio_arquivos=Portfolio::RetornaDiretorio($sock,'Arquivos');

AcessoSQL::Desconectar($sock);


$sock=AcessoSQL::Conectar($cod_curso);

if (Portfolio::NaoExisteTop($sock, $cod_topico_raiz, $novo_nome, $cod_usuario))
{
	$cod_topico=Portfolio::CriarTopico($sock, $cod_topico_raiz, $novo_nome, $cod_usuario, $cod_grupo_portfolio);
	
	//$objResponse->redirect("portfolio.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico."&cod_usuario=".$cod_usuario."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&acao=criarTopico&atualizacao=true");
	$retorno = 'true';
}else{
	//$objResponse->call("mostraFeedback", $msg_erro, 'false');
	$retorno = 'false';
}

AcessoSQL::Desconectar($sock);

$result = array();
$result['retorno'] = $retorno;
$result['cod_topico'] = $cod_topico;

echo json_encode($result);