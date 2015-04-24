<?php
$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';

$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
$model_geral = '../../'.$ferramenta_geral.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$cod_curso = $_POST['cod_curso'];
$cod_endereco = $_POST['cod_endereco'];
$cod_item = $_POST['cod_item'];
$cod_usuario = $_POST['cod_usuario'];

$sock=AcessoSQL::Conectar($cod_curso);

$consulta="delete from Portfolio_itens_enderecos where cod_endereco=".$cod_endereco;
$res=AcessoSQL::Enviar($sock, $consulta);

//$objResponse->remove('end_'.$cod_endereco);

AcessoSQL::Desconectar($sock);

Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 1);

//$objResponse->call("mostraFeedback", $texto, 'true');

