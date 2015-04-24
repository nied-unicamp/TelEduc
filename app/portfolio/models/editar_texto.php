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

$novo_nome=ConversorTexto::ConverteAspas2BarraAspas($novo_nome);
$sock=AcessoSQL::Conectar($cod_curso);

$consulta="update Portfolio_itens set texto='".trim($novo_nome)."' where cod_item=".$cod_item;
$res=AcessoSQL::Enviar($sock, $consulta);

AcessoSQL::Desconectar($sock);

Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 1);

// Imprime no div valores do formulário
//$objResponse->assign("tr_".$cod_item, "className", "novoitem");
//$objResponse->assign("text_".$cod_item, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas($novo_nome)), true));

//$objResponse->call("mostraFeedback", $texto, 'true');
