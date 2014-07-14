<?php

$diretorio_models = "../models/";
$diretorio_ctrlers = "../controllers/";
$diretorio_views = "../views/";
$diretorio_jscss = "../../web-content/js-css/";
$diretorio_imgs  = "../../web-content/imgs/";

require_once $diretorio_models.'geral.inc';
require_once $diretorio_models.'inicial.inc';

$erro1 = $_GET['erro1'];
$erro2 = $_GET['erro2'];
$erro3 = $_GET['erro3'];
$texto = $_GET['texto'];
Inicial::CadastraDadosUsuario($erro1, $erro2, $erro3, $texto);

?>