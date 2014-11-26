<?php

$ferramenta_geral = 'geral';
$ferramenta_agenda = 'agenda';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';

$cod_curso=$_POST['cod_curso'];
$cod_item=$_POST['cod_item'];
$novo_nome=$_POST['novo_nome'];
$cod_usuario=$_POST['cod_usuario'];

// como vou precisar atualizar campos de data, preciso saber a data em UnixTime
$data = time();

$sock=AcessoSQL::Conectar($cod_curso);
$consulta="update Agenda_itens set titulo='".ConversorTexto::VerificaStringQuery(htmlentities($novo_nome))."' where cod_item=".$cod_item;
$res=AcessoSQL::Enviar($sock, $consulta);

Agenda::AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);

AcessoSQL::Desconectar($sock);



?>