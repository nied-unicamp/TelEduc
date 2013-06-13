<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : index.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�ncia
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

    Nied - N�cleo de Inform�tica Aplicada � Educa��o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : index.php
  ========================================================== */

include_once 'instalacao.inc';

/* Versao do TelEduc */
define("VERSAO", "4.3.1", true);

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
	$content_header = "Bem-Vindo(a) à Instalação do TelEduc ".VERSAO."!";

	$content  = "<p>Leia atentamente as instruções contidas em cada passo da instalação.</p>";
	$content .= "<p>Em caso de dúvida, consulte o nosso <a href='doc/InstalacaoTelEduc4.pdf' target='_blank'>Guia de Instalação.</a></p>";

	$content .= "<div class=formulario>";
		$content .= "<form method='POST' action='index.php'>";
			$content .= "<input type=hidden class='form' name=etapa value='1'/><br />";
			$content .= "<input type=submit class='form' value='Instalar o TelEduc'/><br />";
		$content .= "</form>";
	$content .= "</div>";
}
else if ($etapa == 1) {
	$console  = "<p class=feedbackp>O módulo php-mysql está instalado na versão correta. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class=feedbackp>A diretiva register_globals está habilitada. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class=feedbackp>A diretiva magic_quotes_gpc está habilitada. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";

	$content_header = "Banco de Dados e Arquivo de Configuração <span class=etapa>Etapa <font color='#FF0000'>1</font> de 4</span>";

	$content  = "<p>O TelEduc utiliza os seguintes bancos de dados:</p>";
	$content .= "<p style='margin-top:10px'>&nbsp;&nbsp;<b>Banco Geral</b> - Contém configurações e informações do ambiente.</p>";
	$content .= "<p>&nbsp;&nbsp;<b>Banco do Curso X</b> - Contém as informações do curso com o código <b>X</b>.</p>";
	$content .= "<br />";

	$content .= "<p>As informações para acesso aos bancos de dados serão gravadas a seguir em:</p>";
	$content .= "<pre>teleduc4/cursos/aplic/bibliotecas/teleduc.inc</pre>";
	$content .= "<br />";

	$content .= "<div class=formulario>";
		$content .= "<form id='formInstall' name='formInstall' method='POST' action='index.php' onSubmit='return valida_form(document.formInstall, 1);'>";
			$content .= "<label class='form' for=dbname>Banco Geral:</label>";
			$content .= "<input class='form' size=25 type=text name=dbname value='".(isset($_SESSION['dbname']) ? $_SESSION['dbname'] : 'TelEduc4')."'/><br />";
			$content .= "<label class='form' for=dbnamecurso>Prefixo do Banco do Curso:</label>";
			$content .= "<input class='form' size=25 type=text name=dbnamecurso value='".(isset($_SESSION['dbnamecurso']) ? $_SESSION['dbnamecurso'] : 'TelEduc4Curso_')."'/><br />";
			$content .= "<label class='form' for=dbuser>Usuario do MySQL:</label>";
			$content .= "<input class='form' size=25 type=text name=dbuser value='".(isset($_SESSION['dbuser']) ? $_SESSION['dbuser'] : 'usuario')."'/><br />";
			$content .= "<label class='form' for=dbpwd>Senha do MySQL:</label>";
			$content .= "<input class='form' size=25 type=password name=dbpwd value='".(isset($_SESSION['dbpwd']) ? $_SESSION['dbpwd'] : 'senha')."'/><br />";
			$content .= "<label class='form' for=dbhost>Servidor do MySQL:</label>";
			$content .= "<input class='form' size=25 type=text name=dbhost value='".(isset($_SESSION['dbhost']) ? $_SESSION['dbhost'] : 'localhost')."'/><br />";
			$content .= "<label class='form' for=dbport>Porta do MySQL:</label>";
			$content .= "<input class='form' size=25 type=text name=dbport value='".(isset($_SESSION['dbport']) ? $_SESSION['dbport'] : '3306')."'/><br />";
			$content .= "<input class='form' type=hidden name=etapa value='2'/><br />";
			$content .= "<input class='form' type=submit value='Continuar Instalação' /><br />";
		$content .= "</form>";
	$content .= "</div>";
}
else if ($etapa == 2) {	
	$console  = "<p class=feedbackp>O banco de dados foi criado com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class=feedbackp>O banco de dados foi inicializado com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class=feedbackp>As bases de dados foram importadas com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class=feedbackp>O arquivo de configuração foi criado com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	
	$content_header = "Servidor e Diretórios <span class=etapa>Etapa <font color='#FF0000'>2</font> de 4</span>";

	$content  = "<p>Nesta etapa será necessário informar o nome do servidor e o caminho do TelEduc, que podem ser verificados dentro do navegador utilizando sua barra de endereços:</p>";
	$content .= "<pre>http://nome.do.servidor/caminho/do/teleduc/instalacao</pre>";
	$content .= "<br/>";
	$content .= "<p>É necessário uma pasta para armazenar os arquivos dos usuários, certifique-se de que o servidor web tem as permissões necessárias para escrever nessa pasta.</p>";
	$content .= "<br />";
	$content .= "<p>O caminho para o executável do sendmail é necessário para o envio de emails.</p>";
	$content .= "<br />";

	$content .= "<div class=formulario>";
		$content .= "<form id='formInstall' name='formInstall' method='POST' action='index.php' onSubmit='return valida_form(document.formInstall, 2);'>";
			$content .= "<label class='form' for=host>Nome do Servidor</label>";
			$content .= "<input type=text size=25 class='form' name=host value='".$_SERVER['SERVER_NAME']."'/><br />";
			$content .= "<label class='form' for=www>Caminho do TelEduc</label>";
			$content .= "<input type=text size=25 class='form' name=www value='".str_replace("instalacao/index.php", "", $_SERVER['PHP_SELF'])."'/><br />";
			$content .= "<label class='form' for=arquivos>Arquivos de Usuário</label>";
			$content .= "<input type=text size=25 class='form' name=arquivos value='".str_replace("public_html/teleduc4/instalacao", "arquivos", getcwd())."'/><br />";
			$content .= "<label class='form' for=sendmail>Caminho do Sendmail</label>";
			$content .= "<input type=text size=25 class='form' name=sendmail value='/usr/sbin/sendmail'/><br />";
			$content .= "<input type=hidden name=etapa value='3'/><br />";
			$content .= "<input class='form' type=submit value='Continuar Instalação' /><br />";
		$content .= "</form>";
	$content .= "</div>";
}
else if ($etapa == 3) {
	$console  = "<p class=feedbackp>O diretório de arquivos foi configurado com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";

	$content_header = "Administrador do Ambiente <span class=etapa>Etapa <font color='#FF0000'>3</font> de 4</span>";

	$content  = "<p>O TelEduc utiliza o usuário <b>admtele</b> para acesso à administração do ambiente.</p>";
	$content .= "<br />";
	$content .= "<p>Preencha os campos abaixo com os dados do administrador do ambiente.</p>";
	$content .= "<br />";

	$content .= "<div class=formulario>";
		$content .= "<form id='formInstall' name='formInstall' method='POST' action='index.php' onSubmit='return valida_form(document.formInstall, 3);'>";
			$content .= "<label class='form' for=admtele_nome>Nome</label>";
			$content .= "<input type=text size=25 class='form' name=admtele_nome value='".(isset($_SESSION['admtele_nome']) ? $_SESSION['admtele_nome'] : 'Nome Sobrenome')."'/><br />";
			$content .= "<label class='form' for=admtele_email>E-Mail</label>";
			$content .= "<input type=text size=25 class='form' name=admtele_email value='".(isset($_SESSION['admtele_email']) ? $_SESSION['admtele_email'] : 'nome@email.com')."'/><br />";
			$content .= "<label class='form' for=admtele_senha>Senha</label>";
			$content .= "<input type=password size=25 class='form' name=admtele_senha value='".(isset($_SESSION['admtele_senha']) ? $_SESSION['admtele_senha'] : 'admtele')."'/><br />";
			$content .= "<input type=hidden name=etapa value='4'/><br />";
			$content .= "<input type=submit value='Prosseguir' class='form'/><br />";
		$content .= "</form>";
	$content .= "</div>"; 
}
else if ($etapa == 4) {
	$console  = "<p class=feedbackp>Os dados do administrador do ambiente foram salvos com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	
	$content_header = "Fim da Instalação <span class=etapa>Etapa <font color='#FF0000'>4</font> de 4</span>";
	
	$content  = "<b><p style='text-align: center; font-size: 20px;'>O TelEduc foi instalado e está pronto para uso!</p></b>";
	$content .= "<br />";
	$content .= "<p>Recomendamos a remoção da pasta de instalação por questões de segurança.</p>";
	$content .= "<br/>";
	$content .= "<p>Habilite a notificação de novidades via email adicionando ao crontab:</p>";
	$content .= "<pre>0 17 * * * /usr/bin/lynx -dump http://hera.nied.unicamp.br/~teleduc4/scripts/notificar.php?notificar_email=1";
	$content .= "<br/>";
	$content .= "0 9 * * * /usr/bin/lynx -dump http://hera.nied.unicamp.br/~teleduc4/scripts/notificar.php?notificar_email=2";
	$content .= "<br/>";
	$content .= "0 18 * * * /usr/bin/lynx -dump http://hera.nied.unicamp.br/~teleduc4/scripts/notificar.php?notificar_email=2</pre>";
	$content .= "<br />";
	
	$content .= "<input type=submit value='Entrar' onClick=\"document.location='../'\" class='form'/><br />";
}

include 'template_instalacao.php';
exit();
?>
