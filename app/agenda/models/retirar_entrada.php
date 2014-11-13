<?php

$ferramenta_geral = 'geral';
$ferramenta_agenda = 'agenda';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';
$view_agenda = '../../'.$ferramenta_agenda.'/views/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';
require_once $model_geral.'arquivos.inc';

$nome_arquivo = $_POST['nome_arquivo'];
$cod_curso = $_POST['cod_curso'];
$cod_item = $_POST['cod_item'];
$cod_usuario = $_POST['cod_usuario'];
$origem = $_POST['origem'];

$sock = AcessoSQL::Conectar($cod_curso);

Agenda::AbreEdicao($cod_curso, $cod_item, $cod_usuario,$origem);

Arquivos::AlteraStatusArquivo($nome_arquivo,false);

Agenda::AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);

$caminho = "".$view_agenda."ver_linha_agenda.php?&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=1&cod_item=".$cod_item."&cod_usuario=".$cod_usuario."&origem=".$origem."&acao=retirar_entrada&atualizacao=true";

echo json_encode($caminho);

?>