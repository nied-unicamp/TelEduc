<?php
$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';

$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
$model_geral = '../../'.$ferramenta_geral.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$cod_curso = $_POST['cod_curso'];
$cod_usuario = $_POST['cod_usuario'];
$cod_itens = $_POST['cod_itens'];

$sock=AcessoSQL::Conectar($cod_curso);

if(!is_array($cod_itens)){
	Portfolio::ExcluirItem($sock, $cod_itens, $cod_usuario);
} else {
	foreach($cod_itens as $cod => $linha){
		Portfolio::ExcluirItem($sock, $linha, $cod_usuario);
	}
}

AcessoSQL::Desconectar($sock);

//$objResponse->script("Recarregar('excluirItens', 'true');");
