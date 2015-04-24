<?php
$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$cod_curso = $_POST['cod_curso'];
$cod_usuario = $_POST['cod_usuario'];
$cod_topico = $_POST['cod_topico'];
$novo_nome = $_POST['novo_nome'];

$sock=AcessoSQL::Conectar($cod_curso);

$novo_nome=ConversorTexto::ConverteAspas2BarraAspas($novo_nome);

$query = "select cod_topico_pai from Portfolio_topicos where cod_topico = ".$cod_topico;
$res = AcessoSQL::Enviar($sock, $query);

$cod_topico_raiz = AcessoSQL::RetornaLinha($res);

if (Portfolio::NaoExisteTop($sock, $cod_topico_raiz[0], $novo_nome, $cod_usuario))
{
	Portfolio::RenomearTopico ($sock, $cod_topico, $novo_nome);
	/* $objResponse->assign("nome_topico_atual", "innerHTML", htmlentities($novo_nome));
	$objResponse->script("EscondeLayers();");
	$objResponse->call("mostraFeedback", $texto, 'true'); */
	$retorno = 'true';
}else{
	/* $objResponse->script("document.getElementById('nome_topico_atual').innerHTML=nome_topico_atual;");
	$objResponse->script("EscondeLayers();");
	$objResponse->call("mostraFeedback", $msg_erro, 'false'); */
	$retorno = 'false';
}

AcessoSQL::Desconectar($sock);

echo json_encode($retorno);
