<?php

$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
$view_portfolio = '../../'.$ferramenta_portfolio.'/views/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$cod_curso = $_POST['cod_curso'];
$cod_item = $_POST['cod_item'];
$cod_usuario = $_POST['cod_usuario'];
$novo_nome = $_POST['novo_nome'];

// como vou precisar atualizar campos de data, preciso saber a data em UnixTime
$data = time();

$sock=AcessoSQL::Conectar($cod_curso);
$consulta="update Portfolio_itens set titulo='".htmlentities($novo_nome)."', data=".$data.", status='L' where cod_item=".$cod_item;
$res=AcessoSQL::Enviar($sock, $consulta);

AcessoSQL::Desconectar($sock);

// Imprime no div valores do formul?io
//$objResponse->assign("tr_".$cod_item, "className", "novoitem");
//$objResponse->assign("tit_".$cod_item, "innerHTML", htmlentities($novo_nome));
//$objResponse->addEvent("renomear_".$cod_item, "onclick", "AlteraTitulo('".$cod_item."');");

Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 1);

//$objResponse->call("mostraFeedback", $texto, 'true');