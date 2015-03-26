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
$novo_texto=$_POST['novo_texto'];
$cod_usuario=$_POST['cod_usuario'];

$novo_texto=ConversorTexto::ConverteAspas2BarraAspas($novo_texto);
$sock=AcessoSQL::Conectar($cod_curso);

$consulta="update Agenda_itens set texto='".ConversorTexto::VerificaStringQuery($sock, trim(Agenda::VerificaTexto($novo_texto)))."' where cod_item=".$cod_item;
$res=AcessoSQL::Enviar($sock, $consulta);

Agenda::AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);

AcessoSQL::Desconectar($sock);

$texto_modificado = ConversorTexto::AjustaParagrafo(ConversorTexto::ConverteBarraAspas2Aspas(Agenda::VerificaTexto($novo_texto)));

echo json_encode($texto_modificado);
?>