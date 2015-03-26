<?php

$ferramenta_geral = 'geral';
$ferramenta_agenda = 'agenda';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';

$cod_curso = $_POST['cod_curso'];
$cod_item = $_POST['cod_item'];
$cod_usuario = $_POST['cod_usuario'];
$acao = $_POST['acao'];

$sock=AcessoSQL::Conectar($cod_curso);

// como vou precisar atualizar campos de data, preciso saber a data em UnixTime

$data = time();

$consulta="update Agenda_itens set status='L', data=".$data." where cod_item=".$cod_item;
$res=AcessoSQL::Enviar($sock, $consulta);

if($acao){
	$consulta="insert into Agenda_itens_historicos values ('".ConversorTexto::VerificaStringQuery($sock, $cod_item)."', '".ConversorTexto::VerificaStringQuery($sock, $cod_usuario)."', '".ConversorTexto::VerificaStringQuery($sock, $data)."', 'F')";
}else{
	$consulta="insert into Agenda_itens_historicos values ('".$cod_item."', '".ConversorTexto::VerificaStringQuery($sock, $cod_usuario)."', '".ConversorTexto::VerificaStringQuery($sock, $data)."', 'D')";
}

$res=AcessoSQL::Enviar($sock, $consulta);

AcessoSQL::Desconectar($sock);

?>