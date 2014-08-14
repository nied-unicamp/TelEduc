<?php
session_start();

$diretorio_views = "../views/";

//$_SESSION['logout_flag_s'] = 1;

//unset($_SESSION['cod_lingua_s']);
//unset($_SESSION['visitantes_s']);
//unset($_SESSION['visao_aluno_s']);
//unset($_SESSION['lista_frases_s']);
//unset($_SESSION['login_usuario_s']);
//unset($_SESSION['cod_usuario_global_s']);

session_destroy();


//$_SESSION['logout_flag_s']=0;

require_once $diretorio_views.'autenticacao_cadastro.php';/*?logout=1");*/
exit;