<?php
$ferramenta_geral = 'geral';
$ferramenta_agenda = 'agenda';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';

$numero = $_POST['numero'];
$arq = $_POST['arq'];
$cod_curso = $_POST['cod_curso'];
$cod_item = $_POST['cod_item'];
$cod_usuario = $_POST['cod_usuario'];
$origem = $_POST['origem'];

//function ExcluirArquivo($numero, $arq, $cod_curso, $cod_item, $cod_usuario, $origem){

Agenda::AbreEdicao($cod_curso, $cod_item, $cod_usuario, $origem);

$arq=htmlspecialchars_decode($arq);

Arquivos::RemoveDiretorio($arq);

//$objResponse->remove('arq_'.$numero);

$sock = AcessoSQL::Conectar($cod_curso);

Agenda::AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
