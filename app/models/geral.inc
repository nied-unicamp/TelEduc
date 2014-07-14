<?php

session_start();
require_once("teleduc.inc");

/* Fun��es de convers�o de texto */
require_once("conversor_texto.inc");

/* Fun��es de acesso a base de dados */
require_once("acesso_sql.inc");

/* Fun��es controle de Session do PHP */
/* Define as globais: cod_curso_s, cod_lingua_s, visitante_s, visao_aluno_s */
require_once("sessao.inc");

/* Fun��es multilinguais */
require_once("linguas.inc");

/* Fun��es de autentica��o */
require_once ("acesso_php.inc");

/* Fun��es de e-mail */
require_once ("email.inc");

/* Fun��es de data */
require_once ("data.inc");

/* Fun��es de controle de javascript*/
require_once ("data_javascript.inc");

/* Fun��es de manipula��o de arquivos*/
require_once ("arquivos.inc");

/* Fun��es de controle de usu�rios*/
require_once ("usuarios.inc");

/* Fun��es de controle de cursos*/
require_once ("cursos.inc");

/* Fun��es de controle de feedback*/
require_once ("FeedbackObject.inc.php");

?>