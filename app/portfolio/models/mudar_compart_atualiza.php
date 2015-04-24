<?php

$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$novo_comp = $_POST['novo_comp'];
$cod_curso=$_POST['cod_curso'];
$cod_item = $_POST['cod_item'];
$tipo_comp = $_POST['tipo_comp'];
$cod_usuario = $_POST['cod_usuario'];

$sock=AcessoSQL::Conectar($cod_curso);

$cod_grupo = Portfolio::RetornaGrupoPortfolio($sock, $cod_item);
$em_grupo = ($cod_grupo != NULL );
if ($em_grupo){
	Usuarios::AtualizaFerramentasNovaGrupo($sock,"15",$cod_grupo);
	Usuarios::AtualizaFerramentasNova($sock, "15", $_POST['tipo_comp']);
} else {
	Usuarios::AtualizaFerramentasNovaUsuario($sock,"15", $cod_usuario);
	Usuarios::AtualizaFerramentasNova($sock, "15", $tipo_comp);
}

$dados = array();

Portfolio::MudarCompartilhamento($cod_curso, $cod_item, $cod_usuario, $tipo_comp, $novo_comp);

