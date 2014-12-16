<?php
$ferramenta_geral = 'geral';
$ferramenta_agenda = 'agenda';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';

$sock=AcessoSQL::Conectar("");

$lista_frases=Linguas::RetornaListaDeFrases($sock,1);

AcessoSQL::Desconectar($sock);

$retorno="{\n";

foreach ($lista_frases as $cod => $linha){
	if ($cod>1) $retorno.=", ";
	$retorno.="\"msg".$cod."\" : \"".$linha."\"";
}

$retorno.="\n}";

echo $retorno;

?>