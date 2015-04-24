<?php

$ferramenta_geral = 'geral';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$diretorio_jscss = "../../../web-content/js-css/";


require_once $model_geral.'geral.inc';
require_once $model_geral.'menu.inc';
require_once $model_geral.'inicial.inc';

$sock = AcessoSQL::Conectar("");

$cod_curso = $_POST['cod_curso'];

$lingua_curso = Menu::RetornaLinguaCurso($sock,$cod_curso);

// Se diferente, então língua do curso é diferente da língua do usuário, atualiza a lista de frases
$locale = "pt_BR";

if($lingua_curso != $_SESSION['cod_lingua_s']) {
	$lingua = $lingua_curso;

	if($lingua == 1){
		$locale = "pt_BR";
	}
	else if($lingua == 3){
		$locale = "en_US";
	}
	else if($lingua == 4){
		$locale = "pt_PT";
	}
}
else{
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
}

putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain("TelEduc", "../../../gettext/i18n");
textdomain("TelEduc");

$textos = array(
"22"  => _("AGENDA_EDITED_SUCCESS_1"),
"53"  => _("AGENDA_CANT_CONTAIN_TEXT_FILE_1"),
"95"  => _("SURE_DELETE_AGENDA_TEXT_1"),
"93"  => _("TEXT_EXCLUDED_SUCCESS_-1"),
"109" => _("INVALID_CHARACTERS_ATTACH_FILE_-1"),
"12"  => _("SURE_TO_EXTRACT_FILE_-1"),
"13"  => _("ZIP_FILE_DELETED_-1"),
"14"  => _("ZIP_CANT_EXTRACT_SPACE_FOLDER_NAME_-1"),
"104" => _("FILES_DELETED_SUCCESS_-1"),
"29"  => _("SURE_TO_DELETE_AGENDA_1"),
"30"  => _("PERMANENTLY_DELETED_1"),
"210" => _("SURE_TO_DELETE_FILE_FOLDER_-1"),
"198" => _("FILES_DELETED_SUCCESS_-1"),
"199" => _("FILES_HIDDEN_SUCCESS_-1"),
"200" => _("FILES_UNHIDDEN_SUCCESS_-1"),
"63"  => _("ERROR_UPDATING_MATERIAL_-1"),
"28"  => _("CANT_MOVE_FOLDER_ITSELF_SUBFOLDER_15"),
"71"  => _("CANT_MOVE_FOLDER_NAME_-1"),
"49"  => _("ITEM_EDITED_SUCCESS"),
"196" => _("ITEM_RENAMED_SUCCESS_-1"),
"36"  => _("TITLE_CANNOT_BE_EMPTY_-1"),
"208" => _("TEXT_DELETED_SUCCESS_-1"),
"32"  => _("SURE_TO_DELETE_ADDRESS_-1"),
"197" => _("ADDRESS_DELETED_SUCCESS_-1"),
"188" => _("SURE_TO_DELETE_TEXT_CONTENT_-1"),
"216" => _("INVALID_CHARACTERS_ATTACH_FILE_-1"),
"64"  => _("FILL_IN_ADDRESS_-1"),
"33"  => _("SURE_TO_EXTRACT_FILE_-1"),
"34"  => _("ZIP_FILE_DELETED_-1"),
"35"  => _("ZIP_CANT_EXTRACT_SPACE_FOLDER_NAME_-1"),
"118" => _("HIDDEN_-1"),
"18"  => _("SURE_TO_DELETE_ITEM_-1"),
"179" => _("ITEM_MOVE_TO_TRASH_-1"),
"67"  => _("ADDRESS_INCLUDED_SUCCESS_-1")
);


$retorno="{\n";
$qtd = 0;
foreach ($textos as $cod => $linha){
	if ($qtd>=1) $retorno.=", ";
	$retorno.="\"msg".$cod."\":\"".$linha."\"";
	$qtd++;
}

$retorno.="\n}";

echo $retorno;

?>