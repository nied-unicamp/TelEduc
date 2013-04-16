<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/agenda/importar_curso2.php

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
  ARQUIVO : cursos/aplic/agenda/importar_curso2.php
  ========================================================== */

/* C�digo principal */

$bibliotecas = "../bibliotecas/";
include ($bibliotecas . "geral.inc");
include ($bibliotecas . "sql_dump.inc");
include ($bibliotecas . "conversor.inc");
include ($bibliotecas . "extracao.inc");
include ($bibliotecas . "importar.inc");
include ("dinamica.inc");

// per�odo de espera para nova tentativa para montagem de um
// curso extra�do
define("PERIODO_DE_ESPERA", 7000);
// n�mero m�ximo de tentativas para login
define("MAX_TENTATIVAS_LOGIN", 3);

// **************** VARI�VEIS DE ENTRADA ****************
// Recebe de 'importar_curso2.php'
//    c�digo do curso
$cod_curso = $_GET['cod_curso'];
//    c�digo da categoria que estava sendo listada.
$cod_categoria = $_POST['cod_categoria'];
//    c�digo do curso selecionado na lista de cursos compartilhados
if (isset ($_POST['cod_curso_compart']))
	$cod_curso_compart = $_POST['cod_curso_compart'];
//    c�digo do curso selecionado na lista todos cursos
if (isset ($_POST['cod_curso_todos']))
	$cod_curso_todos = $_POST['cod_curso_todos'];
//    c�digo da ferramenta cujos itens ser�o importados
$cod_ferramenta = $_GET['cod_ferramenta'];
//    login do usu�rio no curso a ser importado
$login_import = $_POST['login_import'];
//    senha criptografada do usu�rio no curso a ser importado.
$senha_import_crypt = $_POST['senha_import'];
//    tipo do curso: A(ndamento), I(nscri��es abertas), L(atentes),
//  E(ncerrados)
$tipo_curso = $_POST['tipo_curso'];
if ('E' == $tipo_curso) {
	//  per�odo especificado para listar os cursos
	//  encerrados.
	$data_inicio = $_POST['data_inicio'];
	$data_fim = $_POST['data_fim'];
}

// ******************************************************

// Se selecionado um curso da lista com todos eles,
// incializa o contador de tentativas de autentifica��o.
if (isset ($_POST['cod_curso_todos'])) {
	session_register("login_import_count_s");
	if (!isset ($login_import_count)) {
		// Se o contador j� havia sido inicializado, ent�o
		// incrementa seu valor que n�o dever� ultrapassar o valor
		// de MAX_TENTATIVAS_LOGIN
		if (isset ($_SESSION['login_import_count_s'])) {
			$login_import_count = ((int) $_SESSION['login_import_count_s'] + 1);
			$login_import_count_s = $login_import_count;
		} else {
			$login_import_count = $login_import_count_s = 0;
		}
	}
}

/* POST/GET -> SESSION */
$_SESSION['cod_categoria'] = $cod_categoria;
$_SESSION['cod_curso_compart'] = $cod_curso_compart;
$_SESSION['cod_curso_todos'] = $cod_curso_todos;
$_SESSION['login_import'] = $login_import;
$_SESSION['senha_import'] = $senha_import_crypt;
$_SESSION['tipo_curso'] = $tipo_curso;
$_SESSION['data_inicio'] = $data_inicio;
$_SESSION['data_fim'] = $data_fim;

$sock = Conectar("");
$lista_frases_biblioteca = RetornaListaDeFrases($sock, -2);
Desconectar($sock);

$cod_ferramenta = 16;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda = 1;
include ("../topo_tela.php");

/* Verifica se o usuario eh formador. */
$usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

$tabela = "Agenda";
$dir = "agendas";

echo ("    <script type=\"text/javascript\" src=\"../js-css/dhtmllib.js\"></script>\n");
echo ("    <script type=\"text/javascript\">\n\n");

echo ("      function Iniciar()\n");
echo ("      {\n");
echo ("        startList();\n");
echo ("      }\n\n");

echo ("    </script>\n\n");

include ("../menu_principal.php");
Desconectar($sock);

echo ("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

/* Impede o acesso a algumas secoes aos usuários que não são formadores. */
if (!$usr_formador) {
	/* 1 - // 1 - Dinamica do Curso*/
	echo ("          <h4>" . RetornaFraseDaLista($lista_frases, 1));
	/* 76 - Acao exclusiva a formadores. */
	echo ("    - " . RetornaFraseDaLista($lista_frases_geral, 76) . "</h6>");

	/*Voltar*/
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
	
	echo ("          <div id=\"mudarFonte\">\n");
	echo ("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
	echo ("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
	echo ("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
	echo ("          </div>\n");

	/* 23 - Voltar (gen) */
	echo ("          <form name=\"frmErro\" action=\"\" method=\"post\">\n");
	echo ("            <input class=\"input\" type=\"button\" name=\"cmdVoltar\" value='" . RetornaFraseDaLista($lista_frases_geral, 23) . "' onclick=\"window.location =  'dinamica.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=" . $cod_ferramenta . "'\" />\n");
	echo ("          </form>\n");
	echo ("        </td>\n");
	echo ("      </tr>\n");
	echo ("    </table>\n");
	echo ("  </body>\n");
	echo ("</html>\n");
	Desconectar($sock);
	exit;
}

// P�gina Principal
// 1 - Agenda
$cabecalho = ("          <h4>" . RetornaFraseDaLista($lista_frases, 1));
/*36 - Importando Dinamica */
$cabecalho .= (" - " . RetornaFraseDaLista($lista_frases, 36) . "</h4>\n");
echo ($cabecalho);

// 3 A's - Muda o Tamanho da fonte
echo ("          <div id=\"mudarFonte\">\n");
echo ("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
echo ("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
echo ("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
echo ("          </div>\n");

/*Voltar*/
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

// Extra� o c�digo do curso selecionado.
if (isset ($cod_curso_compart)) {
	list ($status, $cod) = explode(";", $cod_curso_compart, 2);
	$curso_compartilhado = 1;
} else
	if (isset ($cod_curso_todos)) {
		list ($status, $cod) = explode(";", $cod_curso_todos, 2);
		$curso_compartilhado = 0;
	}

$codigo_import = (int) $cod;
$_SESSION['cod_curso_import'] = $codigo_import;
$_SESSION['curso_compartilhado'] = $curso_compartilhado;

// Se o curso estiver extra�do (em arquivo), definimos
// o par�metro, $opt, para conex�o � base de dados de montagem
// do curso.
if ($status == 'E') {
	$opt = TMPDB;
	$curso_extraido = 1;
} else {
	$opt = "";
	$curso_extraido = 0;
}

if ($status == 'E') {
	$sock = Conectar("");
	$curso_montado = CursoFaseDeMontagem($sock, $codigo_import);

	// Se o curso estiver sendo montado ou desmontado aguarda
	// e tenta novamente.
	if (($curso_montado == 'montando') || ($curso_montando == 'desmontando')) {
		sleep(PERIODO_DE_ESPERA);
		$curso_montado = CursoFaseDeMontagem($sock, $codigo_import);
	}

	Desconectar($sock);

	// Se o curso n�o estiver montado, ent�o lista as tabelas e pastas
	// das ferramentas compartilhadas.
	if ($curso_montado == 'nao') {
		list ($tabelas, $pastas) = RetornaTabelasEPastasFerrCompartExtraido($codigo_import, $curso_compartilhado);

		MontaCursoExtraidoTemporario($codigo_import, $pastas, $tabelas);
	}
	// Se o curso continuar na fase de montagem ou desmontagem,
	// ent�o informa a impossibilidade de importa��o dos materiais
	// desse curso ao usu�rio.
	else
		if (($curso_montado == 'montando') || ($curso_montando == 'desmontando')) {
			if (isset ($caminho_original)) {
				// 88 - Importando para:
				echo ("          " . RetornaFraseDaLista($lista_frases, 88));
				echo ($caminho_original);
				echo ("          <br>\n");
			}

			$sock = Conectar("");
			$nome_curso_import = NomeCursoExtraido($sock, $codigo_import);
			Desconectar($sock);

			echo ("          <script language=javascript type=text/javascript defer>\n\n");

			echo ("            function Cancelar()\n");
			echo ("            {\n");
			echo ("              document.frmRedir.action = 'importar_curso.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=16';\n");
			echo ("              document.frmRedir.submit();\n");
			echo ("            }\n\n");

			echo ("          </script>\n\n");

			echo ("          <form method=\"post\" name=\"frmRedir\" action=\"\">\n");
			echo ("            <input type=\"hidden\" name=\"cod_categoria\" value=\"" . $cod_categoria . "\">\n");
			echo ("            <input type=\"hidden\" name=\"tipo_curso\" value='" . $tipo_curso . "'>\n");
			if ('E' == $tipo_curso) {
				echo ("            <input type=\"hidden\" name=\"data_inicio\" value='" . $data_inicio . "'>\n");
				echo ("            <input type=\"hidden\" name=\"data_fim\" value='" . $data_fim . "'>\n");
			}
			echo ("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\"  class=\"tabExterna\">\n");
			echo ("            <tr>\n");
			echo ("              <td valign=\"top\">\n");
			echo ("                <ul class=\"btAuxTabs\">\n");
			/* 23 - Voltar (geral) */
			echo ("                    <li><span onClick='javascript:history.back(-1);'>" . RetornaFraseDaLista($lista_frases_geral, 23) . "</span></li>\n");
			echo ("                </ul>\n");
			echo ("              </td>\n");
			echo ("            </tr>\n");
			echo ("            <tr>\n");
			echo ("              <td>\n");
			echo ("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
			echo ("                  <tr class=\"head\">\n");
			echo ("                    <td colspan=\"3\">\n");
			echo ("                      " . RetornaFraseDaLista($lista_frases, 78) . "&nbsp;\"" . $nome_curso_import . "\"\n");
			echo ("                    </td>\n");
			echo ("                  </tr>\n");
			echo ("                  <tr>\n");
			// 49(biblioteca) - O curso solicitado est� ocupado. Por favor tente novamente.
			echo ("                    <td>" . RetornaFraseDaLista($lista_frases_biblioteca, 49) . "</td>\n");
			echo ("                  <tr>\n");
			echo ("                </table>\n");
			echo ("              </td>\n");
			echo ("            </tr>\n");
			echo ("          </table>\n");
			echo ("          </form>\n");
			echo ("        </td>\n");
			echo ("      </tr>\n");
			include ("../tela2.php");
			echo ("  </body>\n");
			echo ("</html>\n");
			exit ();
		}
}

if (isset ($caminho_original)) {
	// 88 - Importando para:
	echo ("        " . RetornaFraseDaLista($lista_frases, 88));
	echo ($caminho_original);
	echo ("        <br>\n");
}

// Conecta-se � base de dados do curso. Tempor�ria,
// se o curso estava extra�do e foi montado (par�metro $opt).
$sock = Conectar($codigo_import, $opt);

$nome_curso_import = NomeCurso($sock, $codigo_import);

Desconectar($sock);

echo ("        <form method=\"post\" name=\"frmRedir\" action=\"\" onsubmit=\"return(ReAutent());\">\n");
echo ("          <input type=\"hidden\" name=\"cod_curso\" value=\"" . $cod_curso . "\">\n");
echo ("          <input type=\"hidden\" name=\"cod_categoria\" value=\"" . $cod_categoria . "\">\n");
echo ("          <input type=\"hidden\" name=\"tipo_curso\" value=\"'" . $tipo_curso . "'\">\n");
if ('E' == $tipo_curso) {
	echo ("          <input type=\"hidden\" name=\"data_inicio\" value='" . $data_inicio . "'>\n");
	echo ("          <input type=\"hidden\" name=\"data_fim\" value='" . $data_fim . "'>\n");
}

// Se selecionado um curso com itens compartilhados, redireciona a p�gina.
if (isset ($cod_curso_compart)) {
	echo ("          <input type=\"hidden\" name=\"cod_curso_import\" value=\"" . $codigo_import . "\">\n");
	echo ("          <input type=\"hidden\" name=\"curso_extraido\" value=\"" . $curso_extraido . "\">\n");
	echo ("          <input type=\"hidden\" name=\"curso_compartilhado\" value=\"" . $curso_compartilhado . "\">\n");

	echo ("          <script language=\"javascript\" type=\"text/javascript\">\n\n");
	echo ("            document.frmRedir.action = 'importar_dinamica.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=16';\n");
	echo ("          document.frmRedir.submit();\n");
	echo ("        </script>\n\n");
}
// Se selecionado um curso na listagem de todos eles, verifica a autentica��o.
else
	if (isset ($cod_curso_todos)) {

		$cod_usuario_import = VerificaAutentImportacao($codigo_import, $login_import, $senha_import_crypt, $opt, $cod_curso);
		// Se o usu�rio n�o existe (autentica��o falhou) no curso do
		// qual se deseja importar os itens, oferece nova tentativa
		// de autentica��o.
		if (false === $cod_usuario_import) {
			echo ("          <script language=\"javascript\" type=\"text/javascript\" src=\"../bibliotecas/javacrypt.js\"></script>\n\n");
			echo ("          <script language=\"javascript\" type=\"text/javascript\">\n\n");

			echo ("            function Valida()\n");
			echo ("            {\n");
			echo ("              login_imp = document.frmRedir.login_import.value;\n");
			echo ("              while (login_imp.search(\" \") != -1)\n");
			echo ("              login_imp = login_imp.replace(/ /, \"\");\n\n");

			echo ("              senha_imp = document.frmRedir.senha_import.value;\n");
			echo ("              while (senha_imp.search(\" \") != -1)\n");
			echo ("              senha_imp = senha_imp.replace(/ /, \"\");\n\n");

			echo ("              if ((login_imp == \"\") || (senha_imp == \"\"))\n");
			echo ("              {\n");
			// 52(biblioteca) - Login ou senha inv�lido
			echo ("                alert(\"" . RetornaFraseDaLista($lista_frases_biblioteca, 52) . "\");\n");
			echo ("                if (login_imp == \"\")\n");
			echo ("                  document.frmRedir.login_import.focus();\n");
			echo ("                else if (senha_imp == \"\")\n");
			echo ("                  document.frmRedir.senha_import.focus();\n");
			echo ("                return(false);\n");
			echo ("              }\n");
			echo ("              else\n");
			echo ("              {\n");
			echo ("                document.frmRedir.senha_import.value =");
			echo ("                Javacrypt.displayPassword(document.frmRedir.senha_import.value, 'AA');\n");
			echo ("                return(true);\n");
			echo ("              }\n");
			echo ("            }\n\n");

			echo ("            function ReAutent()\n");
			echo ("            {\n");
			echo ("              document.frmRedir.action = 'importar_curso2.php?cod_curso=" . $cod_curso . "&amp;cod_usuario=" . $cod_usuario . "&camp;od_ferramenta=16';\n");
			echo ("              if (Valida())\n");
			echo ("               return true\n");
			echo ("            }\n\n");

			echo ("            function Cancelar()\n");
			echo ("            {\n");
			echo ("              document.frmRedir.action = 'importar_curso.php?cod_curso=" . $cod_curso . "&amp;cod_usuario=" . $cod_usuario . "&amp;cod_ferramenta=16';\n");
			echo ("              document.frmRedir.submit();\n");
			echo ("            }\n\n");

			echo ("          </script>\n\n");

			echo ("          <input type=\"hidden\" name=\"cod_curso_todos\" value='" . $cod_curso_todos . "'>\n");

			echo ("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\"  class=\"tabExterna\">\n");
			echo ("            <tr>\n");
			echo ("              <td valign=\"top\">\n");
			echo ("                <ul class=\"btAuxTabs\">\n");
			/* 23 - Voltar (geral) */
			echo ("                    <li><span onClick=\"window.location='importar_curso.php?cod_curso=" . $cod_curso . "&amp;cod_usuario=" . $cod_usuario . "&amp;cod_ferramenta=16';\">" . RetornaFraseDaLista($lista_frases_geral, 23) . "</span></li>\n");
			echo ("                </ul>\n");
			echo ("              </td>\n");
			echo ("            </tr>\n");
			echo ("            <tr>\n");
			echo ("              <td>\n");
			echo ("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
			echo ("                  <tr class=\"head\">\n");
			// ?? - Dinamica do Curso
			echo ("                    <td colspan=\"3\">\n");
			echo ("                      " . RetornaFraseDaLista($lista_frases, 37) . "&nbsp;\"" . $nome_curso_import . "\"\n");
			echo ("                    </td>\n");
			echo ("                  </tr>\n");
			echo ("                  <tr>\n");
			echo ("                    <td width=\"10%\" style=\"border:0pt;\">login:</td>\n");
			echo ("                    <td width=\"2%\" style=\"border:0pt; text-align:left;\"><input type=\"text\" class=\"input\" name=\"login_import\" value=\"\" /></td>\n");
			echo ("                  </tr>\n");
			echo ("                  <tr>\n");
			echo ("                    <td width=\"10%\" style=\"border:0pt;\">senha:</td>\n");
			echo ("                    <td width=\"10%\" style=\"border:0pt; text-align:left;\"><input type=\"password\" class=\"input\" name=\"senha_import\" value=\"\"></td>\n");
			/* 36 - Importar Dinamica */
			echo ("                    <td style=\"border:0pt; text-align:left;\"><input type=\"submit\" class=\"input\" value=\"" . RetornaFraseDaLista($lista_frases, 36) . "\" style=\"width:140px;\" /></td>\n");
			echo ("                  </tr>\n");
			echo ("                </table>\n");
			echo ("              </td>\n");
			echo ("            </tr>\n");
			echo ("          </table>\n");

			echo ("          <script language=\"javascript\" type=\"text/javascript\">\n\n");
			echo ("            document.frmRedir.login_import.focus();\n");
			echo ("          </script>\n\n");
		} else // Usu�rio autentifcado.
			{
			echo ("            <input type=\"hidden\" name=\"cod_curso_import\" value=\"" . $codigo_import . "\">\n");
			echo ("            <input type=\"hidden\" name=\"curso_extraido\" value=\"" . $curso_extraido . "\">\n");
			echo ("            <input type=\"hidden\" name=\"curso_compartilhado\" value=\"" . $curso_compartilhado . "\">\n");

			// Armazena chave de autentica��o do usu�rio.
			session_register("login_import_s");
			$login_import_s = CriaChaveDeAutenticacao($cod_curso, $cod_usuario, $codigo_import, $login_import, $senha_import_crypt);
			$_SESSION['login_import_s'] = $login_import_s;

			echo ("          <script language=\"javascript\" type=\"text/javascript\">\n\n");

			echo ("            document.frmRedir.action = 'importar_dinamica.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=16';\n");
			echo ("            document.frmRedir.submit();\n");

			echo ("          </script>\n\n");
		}
	}

echo ("          </form>\n");
echo ("        </td>\n");
echo ("      </tr>\n");
include ("../tela2.php");
echo ("  </body>\n");
echo ("</html>\n");
?>
