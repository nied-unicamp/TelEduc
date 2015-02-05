<?php
$ferramenta_geral = 'geral';
$ferramenta_agenda = 'agenda';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';

$lingua = $_SESSION['cod_lingua_s'];

if($lingua == 1){
	$locale = "pt_BR";
}
else if($lingua == 3){
	$locale = "en_US";
}
else if($lingua == 4){
	$locale = "pt_PT";
}

putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain("TelEduc", "../../../gettext/i18n");
textdomain("TelEduc");

$a_vars = array();
$a_vars['msg22'] = _("msg22_1");
$a_vars['msg53'] = _("msg53_1");
$a_vars['msg95'] = _("msg95_1");
$a_vars['msg93'] = _("msg93_1");
$a_vars['msg109'] = _("msg109_1");
$a_vars['msg12'] = _("msg12_1");
$a_vars['msg13'] = _("msg13_1");
$a_vars['msg14'] = _("msg14_1");
$a_vars['msg104'] = _("msg104_1");
$a_vars['msg29'] = _("msg29_1");
$a_vars['msg30'] = _("msg30_1");

echo json_encode($a_vars);

?>