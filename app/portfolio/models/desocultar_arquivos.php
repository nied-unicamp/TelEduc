<?php

$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';

$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
$model_geral = '../../'.$ferramenta_geral.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$nomes_arquivos = $_POST['nomes_arquivos'];
$cod_curso = $_POST['cod_curso'];
$cod_item = $_POST['cod_item'];
$cod_usuario = $_POST['cod_usuario'];

foreach($nomes_arquivos as $cod => $linha){
	$nome_arquivo = implode("/", explode("//", $linha[0]));
	$nome_arquivo = str_replace("%20"," ",$nome_arquivo);

	Arquivos::AlteraStatusArquivo($nome_arquivo,false);

	//$objResponse->remove('arq_oculto_'.$linha[1]);
	//$objResponse->script('document.getElementById(\'nomeArq_'.$linha[1].'\').setAttribute(\'arqOculto\', \'nao\');');
}

//$objResponse->addEvent('sArq_ocultar', 'onclick', 'Ocultar();');
Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 1);

//$objResponse->call("mostraFeedback", $texto, 'true');
