<?php

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

/* Frases Agenda*/
$a_vars['msg22'] = _("AGENDA_EDITED_SUCCESS_1");
$a_vars['msg53'] = _("AGENDA_CANT_CONTAIN_TEXT_FILE_1");
$a_vars['msg95'] = _("SURE_DELETE_AGENDA_TEXT_1");
$a_vars['msg93'] = _("TEXT_EXCLUDED_SUCCESS_-1");
$a_vars['msg109'] = _("INVALID_CHARACTERS_ATTACH_FILE_-1");
$a_vars['msg12'] = _("SURE_TO_EXTRACT_FILE_-1");
$a_vars['msg13'] = _("ZIP_FILE_DELETED_-1");
$a_vars['msg14'] = _("ZIP_CANT_EXTRACT_SPACE_FOLDER_NAME_-1");
$a_vars['msg104'] = _("FILES_DELETED_SUCCESS_-1");
$a_vars['msg29'] = _("SURE_TO_DELETE_AGENDA_1");
$a_vars['msg30'] = _("PERMANENTLY_DELETED_1");

/* Frases Portfolio*/
$a_vars['msg210'] = _("SURE_TO_DELETE_FILE_FOLDER_-1");

echo json_encode($a_vars);

?>