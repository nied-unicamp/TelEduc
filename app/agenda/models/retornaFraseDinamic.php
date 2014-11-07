<?php
$ferramenta_geral = 'geral';
$ferramenta_agenda = 'agenda';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';
$view_agenda = '../../'.$ferramenta_agenda.'/views/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$diretorio_jscss = '../../../web-content/js-css/';
$diretorio_imgs = '../../../web-content/imgs/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';

$sock=AcessoSQL::Conectar("");

$lista_frases=Linguas::RetornaListaDeFrases($sock,1);

AcessoSQL::Desconectar($sock);

$retorno="{\n";

foreach ($lista_frases as $cod => $linha){
	if ($cod>1) $retorno.=", ";
	$retorno.="\"msg".$cod."\":\"".$linha."\" ";
}

$retorno.="\n}";

//$objResponse->script($variavel." = ".$retorno.";");

echo json_encode($retorno.";");

?>