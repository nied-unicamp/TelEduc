<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : instalacao/index.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
    Copyright (C) 2001  NIED - Unicamp

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2 as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    You could contact us through the following addresses:

    Nied - Núcleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : instalacao/index.php
  ========================================================== */

include_once 'instalacao.inc';

/* Versao do TelEduc */
define("VERSAO", "4.3.2", true);

/* Limpeza de variaveis usadas para controlar as mensagens exibidas
 * na console e no "miolo" da pagina. */
if (!isset($console) && !isset($content)) {
	$console = "";
	$content = "";
}

/* Para simplificar, a instalacao possui um template, para
 * o qual mandaremos o resultado da etapa atual. O controle de
 * etapas sera feito atraves de uma variavel e posts para a mesma
 * pagina. */
if (isset($_GET['etapa'])) {
	$etapa = $_GET['etapa'];
}
else if (isset($_POST['etapa'])) {
	$etapa = $_POST['etapa'];
}
else {
	$etapa = 0;
}

/* Salva os dados cadastrados nas etapas anteriores em variaveis de sessao. */
if ($etapa > 0) {
	session_start();

	if ($etapa == 2) {
		$_SESSION['dbname'] = (isset($_POST['dbname']) ? $_POST['dbname'] : $_SESSION['dbname']);
		$_SESSION['dbnamecurso'] = (isset($_POST['dbnamecurso']) ? $_POST['dbnamecurso'] : $_SESSION['dbnamecurso']);
		$_SESSION['dbuser'] = (isset($_POST['dbuser']) ? $_POST['dbuser'] : $_SESSION['dbuser']);
		$_SESSION['dbpwd'] = (isset($_POST['dbpwd']) ? $_POST['dbpwd'] : $_SESSION['dbpwd']);
		$_SESSION['dbhost'] = (isset($_POST['dbhost']) ? $_POST['dbhost'] : $_SESSION['dbhost']);
		$_SESSION['dbport'] = (isset($_POST['dbport']) ? $_POST['dbport'] : $_SESSION['dbport']);
	}
	if ($etapa == 3) {
		$_SESSION['host'] = (isset($_POST['host']) ? $_POST['host'] : $_SESSION['host']);
		$_SESSION['www'] = (isset($_POST['www']) ? $_POST['www'] : $_SESSION['www']);
		$_SESSION['arquivos'] = (isset($_POST['arquivos']) ? $_POST['arquivos'] : $_SESSION['arquivos']);
		$_SESSION['sendmail'] = (isset($_POST['sendmail']) ? $_POST['sendmail'] : $_SESSION['sendmail']);
	}
	if ($etapa == 4) {
		$_SESSION['admtele_nome'] = (isset($_POST['admtele_nome']) ? $_POST['admtele_nome'] : $_SESSION['admtele_nome']);
		$_SESSION['admtele_email'] = (isset($_POST['admtele_email']) ? $_POST['admtele_email'] : $_SESSION['admtele_email']);
		$_SESSION['admtele_senha'] = (isset($_POST['admtele_senha']) ? $_POST['admtele_senha'] : $_SESSION['admtele_senha']);
	}
}

/* Verifica se os pre-requisitos da etapa estao devidamente instalados e
 * monta o console com as mensagens de acordo com as etapas concluidas. */
if ($erro =& verificaRequisitos($etapa) && !isset($_GET['bypass_anexo'])) {
	exibeErro($etapa,$erro);
}

if ($etapa == 0) {
	$content_header = "Bem-Vindo(a) &agrave; Instala&ccedil;&atilde;o do TelEduc ".VERSAO."!";

	$content  = "<p>Leia atentamente as instru&ccedil;&otilde;es contidas em cada passo da instala&ccedil;&atilde;o.</p>";
	$content .= "<p>Em caso de d&uacute;vida, consulte o nosso <a href='http://fenix.nied.unicamp.br/redmine/projects/teleduc/wiki/Instala%C3%A7%C3%A3o_do_Teleduc' target='_blank'>Guia de Instala&ccedil;&atilde;o.</a></p>";

	$content .= "<div class='formulario'>";
		$content .= "<form method='POST' action='index.php'>";
			$content .= "<input type='hidden' class='form' name='etapa' value='1'/><br />";
			$content .= "<input type='submit' class='form' value='Instalar o TelEduc'/><br />";
		$content .= "</form>";
	$content .= "</div>";
}
else if ($etapa == 1) {
	$console  = "<p class='feedbackp'>O m&oacute;dulo php-mysql est&aacute; instalado na vers&atilde;o correta. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class='feedbackp'>A diretiva register_globals est&aacute; habilitada. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class='feedbackp'>A diretiva magic_quotes_gpc est&aacute; habilitada. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";

	$content_header = "Banco de Dados e Arquivo de Configura&ccedil;&atilde;o <span class='etapa'>Etapa <font color='#FF0000'>1</font> de 4</span>";

	$content  = "<p>O TelEduc utiliza os seguintes bancos de dados:</p>";
	$content .= "<p style='margin-top:10px'>&nbsp;&nbsp;<b>Banco Geral</b> - Cont&eacute;m configura&ccedil;&otilde;es e informa&ccedil;&otilde;es do ambiente.</p>";
	$content .= "<p>&nbsp;&nbsp;<b>Banco do Curso X</b> - Cont&eacute;m as informa&ccedil;&otilde;es do curso com o c&oacute;digo <b>X</b>.</p>";
	$content .= "<br />";

	$content .= "<p>As informa&ccedil;&otilde;es para acesso aos bancos de dados ser&atilde;o gravadas a seguir em:</p>";
	$content .= "<pre>teleduc4/cursos/aplic/bibliotecas/teleduc.inc</pre>";
	$content .= "<br />";

	$content .= "<div class='formulario'>";
		$content .= "<form id='formInstall' name='formInstall' method='POST' action='index.php' onSubmit='return valida_form(document.formInstall, 1);'>";
			$content .= "<label class='form' for='dbname'>Banco Geral:</label>";
			$content .= "<input class='form' size='25' type='text' name='dbname' value='".(isset($_SESSION['dbname']) ? $_SESSION['dbname'] : 'TelEduc4')."'/><br />";
			$content .= "<label class='form' for='dbnamecurso'>Prefixo do Banco do Curso:</label>";
			$content .= "<input class='form' size='25' type='text' name='dbnamecurso' value='".(isset($_SESSION['dbnamecurso']) ? $_SESSION['dbnamecurso'] : 'TelEduc4Curso_')."'/><br />";
			$content .= "<label class='form' for='dbuser'>Usu&aacute;rio do MySQL:</label>";
			$content .= "<input class='form' size='25' type='text' name='dbuser' value='".(isset($_SESSION['dbuser']) ? $_SESSION['dbuser'] : 'usuario')."'/><br />";
			$content .= "<label class='form' for='dbpwd'>Senha do MySQL:</label>";
			$content .= "<input class='form' size='25' type='password' name=dbpwd value='".(isset($_SESSION['dbpwd']) ? $_SESSION['dbpwd'] : 'senha')."'/><br />";
			$content .= "<label class='form' for='dbhost'>Servidor do MySQL:</label>";
			$content .= "<input class='form' size='25' type='text' name='dbhost' value='".(isset($_SESSION['dbhost']) ? $_SESSION['dbhost'] : 'localhost')."'/><br />";
			$content .= "<label class='form' for='dbport'>Porta do MySQL:</label>";
			$content .= "<input class='form' size='25' type='text' name='dbport' value='".(isset($_SESSION['dbport']) ? $_SESSION['dbport'] : '3306')."'/><br />";
			$content .= "<input class='form' type='hidden' name='etapa' value='2'/><br />";
			$content .= "<input class='form' type='submit' value='Continuar Instala&ccedil;&atilde;o' /><br />";
		$content .= "</form>";
	$content .= "</div>";
}
else if ($etapa == 2) {
	$console  = "<p class='feedbackp'>O banco de dados foi criado com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class='feedbackp'>O banco de dados foi inicializado com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class='feedbackp'>As bases de dados foram importadas com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class='feedbackp'>O arquivo de configura&ccedil;&otilde;o foi criado com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	
	$content_header = "Servidor e Diret&oacute;rios <span class='etapa'>Etapa <font color='#FF0000'>2</font> de 4</span>";

	$content  = "<p>Nesta etapa ser&aacute; necess&aacute;rio informar o nome do servidor e o caminho do TelEduc, que podem ser verificados dentro do navegador utilizando sua barra de endere&ccedil;os:</p>";
	$content .= "<pre>http://nome.do.servidor/caminho/do/teleduc/instalacao</pre>";
	$content .= "<br/>";
	$content .= "<p>&Eacute; necess&aacute;rio uma pasta para armazenar os arquivos dos usu&aacute;rios, certifique-se de que o servidor web tem as permiss&otilde;es necess&aacute;rias para escrever nessa pasta.</p>";
	$content .= "<br />";
	$content .= "<p>O caminho para o execut&aacute;vel do sendmail &eacute; necess&aacute;rio para o envio de emails.</p>";
	$content .= "<br />";

	$content .= "<div class='formulario'>";
		$content .= "<form id='formInstall' name='formInstall' method='POST' action='index.php' onSubmit='return valida_form(document.formInstall, 2);'>";
			$content .= "<label class='form' for='host'>Nome do Servidor</label>";
			$content .= "<input class='form' type='text' size='25' name='host' value='".$_SERVER['SERVER_NAME']."'/><br />";
			$content .= "<label class='form' for='www'>Caminho do TelEduc</label>";
			$content .= "<input class='form' type='text' size='25' name='www' value='".str_replace("instalacao/index.php", "", $_SERVER['PHP_SELF'])."'/><br />";
			$content .= "<label class='form' for='arquivos'>Arquivos de Usu&aacute;rio</label>";
			$content .= "<input class='form' type='text' size='25' name='arquivos' value='".str_replace("public_html/teleduc4/instalacao", "arquivos", getcwd())."'/><br />";
			$content .= "<label class='form' for='sendmail'>Caminho do Sendmail</label>";
			$content .= "<input class='form' type='text' size='25' name='sendmail' value='/usr/sbin/sendmail'/><br />";
			$content .= "<input type='hidden' name='etapa' value='3'/><br />";
			$content .= "<input class='form' type='submit' value='Continuar Instala&ccedil;&atilde;o' /><br />";
		$content .= "</form>";
	$content .= "</div>";
}
else if ($etapa == 3) {
	$console  = "<p class='feedbackp'>O diret&oacute;rio de arquivos foi configurado com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";

	$content_header = "Administrador do Ambiente <span class='etapa'>Etapa <font color='#FF0000'>3</font> de 4</span>";

	$content  = "<p>O TelEduc utiliza o usu&aacute;rio <b>admtele</b> para acesso &agrave; administra&ccedil;&atilde;o do ambiente.</p>";
	$content .= "<br />";
	$content .= "<p>Preencha os campos abaixo com os dados do administrador do ambiente.</p>";
	$content .= "<br />";

	$content .= "<div class='formulario'>";
		$content .= "<form id='formInstall' name='formInstall' method='POST' action='index.php' onSubmit='return valida_form(document.formInstall, 3);'>";
			$content .= "<label class='form' for='admtele_nome'>Nome</label>";
			$content .= "<input class='form' type='text' size='25' name='admtele_nome' value='".(isset($_SESSION['admtele_nome']) ? $_SESSION['admtele_nome'] : 'Nome Sobrenome')."'/><br />";
			$content .= "<label class='form' for='admtele_email'>E-Mail</label>";
			$content .= "<input class='form' type='text' size='25' name='admtele_email' value='".(isset($_SESSION['admtele_email']) ? $_SESSION['admtele_email'] : 'nome@email.com')."'/><br />";
			$content .= "<label class='form' for='admtele_senha'>Senha</label>";
			$content .= "<input class='form' type='password' size='25' name='admtele_senha' value='".(isset($_SESSION['admtele_senha']) ? $_SESSION['admtele_senha'] : 'admtele')."'/><br />";
			$content .= "<input type='hidden' name='etapa' value='4'/><br />";
			$content .= "<input class='form'type='submit' value='Prosseguir'/><br />";
		$content .= "</form>";
	$content .= "</div>"; 
}
else if ($etapa == 4) {
	$console  = "<p class='feedbackp'>Os dados do administrador do ambiente foram salvos com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	
	$content_header = "Fim da Instala&ccedil;&atilde;o <span class='etapa'>Etapa <font color='#FF0000'>4</font> de 4</span>";
	
	$content  = "<b><p style='text-align: center; font-size: 20px;'>O TelEduc foi instalado e est&aacute; pronto para uso!</p></b>";
	$content .= "<br />";
	$content .= "<p>Recomendamos a remo&ccedil;&atilde;o da pasta de instala&ccedil;&atilde;o por quest&otilde;es de seguran&ccedil;a.</p>";
	$content .= "<br/>";
	$content .= "<p>Habilite a notifica&ccedil;&atilde;o de novidades via email adicionando ao crontab:</p>";
	$content .= "<pre>";
	$content .= "0 17 * * * /usr/bin/lynx -dump http://".$_SESSION['host'].$_SESSION['www']."scripts/notificar.php?notificar_email=1";
	$content .= "<br/>";
	$content .= "0  9 * * * /usr/bin/lynx -dump http://".$_SESSION['host'].$_SESSION['www']."scripts/notificar.php?notificar_email=2";
	$content .= "<br/>";
	$content .= "0 18 * * * /usr/bin/lynx -dump http://".$_SESSION['host'].$_SESSION['www']."scripts/notificar.php?notificar_email=2";
	$content .= "</pre>";
	$content .= "<br />";
	
	$content .= "<input type='submit' value='Entrar' onClick=\"document.location='../'\" class='form'/><br />";
}

include 'template_instalacao.php';
exit();
?>
