<?php
$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';

$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
$model_geral = '../../'.$ferramenta_geral.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$numero  = $_POST['numero'];
$arq = $_POST['arq'];
$cod_curso = $_POST['cod_curso'];
$cod_item = $_POST['cod_item'];
$cod_usuario = $_POST['cod_usuario'];

$arq=htmlspecialchars_decode($arq);
$arq=str_replace("%20"," ",$arq);

Arquivos::RemoveDiretorio($arq);

Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 1);


