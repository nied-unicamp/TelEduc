<?php


/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/importar_curso2.php

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
  ARQUIVO : cursos/aplic/material/importar_curso2.php
  ========================================================== */

/* C�digo principal */

$bibliotecas = "../bibliotecas/";
include ($bibliotecas . "geral.inc");
include ($bibliotecas . "importar.inc");

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
$cod_categoria = $_GET['cod_categoria'];
//    c�digo do curso selecionado na lista de cursos compartilhados
if (isset ($_GET['cod_curso_compart']))
	$cod_curso_compart = $_GET['cod_curso_compart'];
//    c�digo do curso selecionado na lista todos cursos
if (isset ($_GET['cod_curso_todos']))
	$cod_curso_todos = $_GET['cod_curso_todos'];
//    c�digo da ferramenta cujos itens ser�o importados
$cod_ferramenta = $_GET['cod_ferramenta'];
//    login do usu�rio no curso a ser importado
$login_import = $_GET['login_import'];

//    senha criptografada do usu�rio no curso a ser importado.
$senha_import_crypt = $_GET['senha_import'];
//    tipo do curso: A(ndamento), I(nscri��es abertas), L(atentes),
//  E(ncerrados)
$tipo_curso = $_GET['tipo_curso'];
if ('E' == $tipo_curso) {
	//  per�odo especificado para listar os cursos
	//  encerrados.
	$data_inicio = $_GET['data_inicio'];
	$data_fim = $_GET['data_fim'];
}

include ("../topo_tela.php");
// ******************************************************

// Se selecionado um curso da lista com todos eles,
// incializa o contador de tentativas de autentifica��o.
if (isset ($_GET['cod_curso_todos'])) {
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

Desconectar($sock);
$sock = Conectar("");

$lista_frases_biblioteca = RetornaListaDeFrases($sock, -2);

//   MudarDB($sock, $cod_curso);

switch ($cod_ferramenta) {
	case 3 :
		$tabela = "Atividade";
		$dir = "atividades";
		break;
	case 4 :
		$tabela = "Apoio";
		$dir = "apoio";
		break;
	case 5 :
		$tabela = "Leitura";
		$dir = "leituras";
		break;
	case 7 :
		$tabela = "Obrigatoria";
		$dir = "obrigatoria";
		break;
}

$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda = 1;

Desconectar($sock);
$sock = Conectar($cod_curso);

echo ("    <script type=\"text/javascript\" language=\"JavaScript\">\n");
echo ("      function Iniciar()\n");
echo ("      {\n");
echo ("        startList();\n");
echo ("      }\n\n");

echo ("    </script>\n");

include ("../menu_principal.php");

echo ("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

if ($tela_formador != 1) {
	echo ("          <h4>" . RetornaFraseDaLista($lista_frases, 1));
	/* 73 - Acao exclusiva a formadores. */
	echo ("    - " . RetornaFraseDaLista($lista_frases, 45) . "</h4>");

	/*Voltar*/
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
	echo ("          <div id=\"mudarFonte\">\n");
	echo ("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
	echo ("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
	echo ("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
	echo ("          </div>\n");

	echo ("        </td>\n");
	echo ("      </tr>\n");
	echo ("    </table>\n");
	echo ("  </body>\n");
	echo ("</html>\n");
	Desconectar($sock);
	exit;
}

// P�gina Principal
// 1 - "Material"
$cabecalho = ("         <h4>" . RetornaFraseDaLista($lista_frases, 1));
/*107 - Importando "material" */
$cabecalho .= (" - " . RetornaFraseDaLista($lista_frases, 107));

$cabecalho .= ("</h4>\n");
echo ($cabecalho);

// 3 A's - Muda o Tamanho da fonte
echo ("<div id=\"mudarFonte\">\n");
echo ("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
echo ("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
echo ("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
echo ("          </div>\n");

/*Voltar*/
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

Desconectar($sock);

// Extrai o codigo do curso selecionado.
if ((isset ($cod_curso_compart)) && ($cod_curso_compart != "")) {
	list ($status, $cod) = explode(";", $cod_curso_compart, 2);

	$curso_compartilhado = 1;
} else
	if ((isset ($cod_curso_todos)) && ($cod_curso_todos != "")) {

		list ($status, $cod) = explode(";", $cod_curso_todos, 2);
		$curso_compartilhado = 0;
	}

$codigo_import = (int) $cod;

// Se o curso estiver extraido (em arquivo), definimos
// o parametro, $opt, para conexao a base de dados de montagem
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

	// Se o curso nao estiver montado, entao lista as tabelas e pastas
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
				echo ("          <br />\n");
			}

			$sock = Conectar("");
			$nome_curso_import = NomeCursoExtraido($sock, $codigo_import);
			Desconectar($sock);

			echo ("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
			echo ("            <tr>\n");
			echo ("              <td valign=\"top\">\n");
			// 23 - Voltar (geral)
			echo ("                <ul class=\"btAuxTabs\">\n");
			echo ("                  <li><span onClick='javascript:history.back(-1);'>" . RetornaFraseDaLista($lista_frases_geral, 23) . "</span></li>\n");
			echo ("                </ul>\n");
			echo ("              </td>\n");
			echo ("            </tr>\n");
			echo ("            <tr>\n");
			echo ("              <td>\n");
			echo ("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
			echo ("                  <tr>\n");
			echo ("                    <td>\n");
			// 49(biblioteca) - O curso solicitado est� ocupado. Por favor tente novamente.
			echo ("                      " . RetornaFraseDaLista($lista_frases_biblioteca, 49) . "\n");
			echo ("                    </td>\n");
			echo ("                  <tr>\n");
			echo ("                </table>\n");
			echo ("              </td>\n");
			echo ("            <tr>\n");
			echo ("          </table>\n");
			echo ("        </td>\n");
			echo ("      </tr>\n");
			echo ("    </table>\n");
			echo ("  </body>\n");
			echo ("</html>\n");
			exit ();
		}
}

if (isset ($caminho_original)) {
	// 88 - Importando para:
	echo ("          " . RetornaFraseDaLista($lista_frases, 88));
	echo ($caminho_original);
	echo ("          <br />\n");
}

// Conecta-se � base de dados do curso. Tempor�ria,
// se o curso estava extra�do e foi montado (par�metro $opt).
$sock = Conectar($codigo_import, $opt);

$nome_curso_import = NomeCurso($sock, $codigo_import);

Desconectar($sock);

echo ("          <form method=\"get\" action=\"\" name=\"frmRedir\">\n");
echo ("            <input type=\"hidden\" name=\"cod_categoria\" value=\"" . $cod_categoria . "\" />\n");
echo ("            <input type=\"hidden\" name=\"cod_curso\" value=\"" . $cod_curso . "\" />\n");
echo ("            <input type=\"hidden\" name=\"cod_ferramenta\" value=\"" . $cod_ferramenta . "\" />\n");

if ('E' == $tipo_curso) {
	echo ("            <input type=\"hidden\" name=\"data_inicio\" value=\"" . $data_inicio . "\" />\n");
	echo ("            <input type=\"hidden\" name=\"data_fim\" value=\"" . $data_fim . "\" />\n");
}

// Se selecionado um curso com itens compartilhados, redireciona a p�gina.
if (isset ($cod_curso_compart) && ($cod_curso_compart != "")) {
	echo ("            <input type=\"hidden\" name=\"cod_curso_import\" value=\"" . $codigo_import . "\" />\n");
	echo ("            <input type=\"hidden\" name=\"curso_extraido\" value=\"" . $curso_extraido . "\" />\n");
	echo ("            <input type=\"hidden\" name=\"curso_compartilhado\" value=\"" . $curso_compartilhado . "\" />\n");
	echo ("            <input type=\"hidden\" name=\"tipo_curso\" value=\"" . $tipo_curso . "\" />\n");

	echo ("            <script type=\"text/javascript\" language=\"JavaScript\">\n\n");

	echo ("              document.frmRedir.action = 'importar_material.php';\n");
	echo ("              document.frmRedir.submit();\n");

	echo ("            </script>\n\n");
}
// Se selecionado um curso na listagem de todos eles, verifica a autentica��o.
else
	if ((isset ($cod_curso_todos)) && ($cod_curso_todos != "")) {

		$cod_usuario_import = VerificaAutentImportacao($codigo_import, $login_import, $senha_import_crypt, $opt);
		if ($login_import_count_s > MAX_TENTATIVAS_LOGIN) {

			echo ("            <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
			echo ("              <tr>\n");
			echo ("                <td valign=\"top\">\n");
			// 23 - Voltar (geral)
			echo ("                  <ul class=\"btAuxTabs\">\n");
			echo ("                    <li><span onClick='javascript:history.back(-1);'>" . RetornaFraseDaLista($lista_frases_geral, 23) . "</span></li>\n");
			echo ("                  </ul>\n");
			echo ("                </td>\n");
			echo ("              </tr>\n");
			echo ("              <tr>\n");
			echo ("                <td>\n");
			echo ("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
			echo ("                    <tr class=\"head\">\n");
			echo ("                      <td>\n");
			// 78 - "Materiais" do curso:      
			echo ("                      " . RetornaFraseDaLista($lista_frases, 114) . " \"<b>" . $nome_curso_import . "</b>\"\n");
			echo ("                      </td>\n");
			echo ("                    </tr>\n");
			echo ("                    <tr>\n");
			echo ("                      <td>\n");
			// 50(biblioteca) - Excedido o limite de tentativas para acesso. Tente novamente.
			echo ("                        <a href=\"material.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=" . $cod_ferramenta . "&cod_topico_raiz=" . $cod_topico_raiz . "\">" . RetornaFraseDaLista($lista_frases_biblioteca, 50) . "</a><br />\n");
			echo ("                      </td>\n");
			echo ("                    </tr>\n");
			echo ("                  </table>\n");
			echo ("                </td>\n");
			echo ("              </tr>\n");
			echo ("            </table>\n");
			echo ("          </form>\n");
			echo ("        </td>\n");
			echo ("      </tr>\n");
			echo ("    </table>\n");
			echo ("  </body>\n");
			echo ("</html>\n");
			exit ();
		}
		// Se o usu�rio n�o existe (autentica��o falhou) no curso do
		// qual se deseja importar os itens, oferece nova tentativa
		// de autentica��o.
		if (false === $cod_usuario_import) {

			echo ("          <script type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/javacrypt.js\" defer></script>\n\n");
			echo ("          <script type=\"text/javascript\" language=\"JavaScript\" defer>\n\n");

			echo ("            function Valida(){\n");
			echo ("              var c=0;login_imp = document.frmRedir.login_import.value;\n");
			echo ("              while (login_imp.search(\" \") != -1)\n");
			echo ("              login_imp = login_imp.replace(/ /, \"\");\n\n");

			echo ("              senha_imp = document.frmRedir.senha_import.value;\n");
			echo ("              while (senha_imp.search(\" \") != -1)\n");
			echo ("              senha_imp = senha_imp.replace(/ /, \"\");\n\n");

			echo ("              if ((login_imp == \"\") || (senha_imp == \"\")){\n");
			// 52(biblioteca) - Login ou senha inv�lido
			echo ("                alert(\"" . RetornaFraseDaLista($lista_frases_biblioteca, 52) . "\");\n");
			echo ("                if (login_imp == \"\")\n");
			echo ("                  document.frmRedir.login_import.focus();\n");
			echo ("                else if (senha_imp == \"\")\n");
			echo ("                  document.frmRedir.senha_import.focus();\n");
			echo ("                return(false);\n");
			echo ("              }\n");
			echo ("              else{\n");
			echo ("                document.frmRedir.senha_import.value = ");
			echo (" Javacrypt.displayPassword(document.frmRedir.senha_import.value, 'AA');\n");
			echo ("                return(true);\n");
			echo ("              }\n");
			echo ("            }\n\n");

			echo ("            function ReAutent(){\n");
			echo ("              document.frmRedir.action = 'importar_curso2.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=" . $cod_ferramenta . "&cod_topico_raiz=" . $cod_topico_raiz . "&tipo_curso=" . $tipo_curso . "&cod_categoria=" . $cod_categoria . "';\n");
			echo ("              return (Valida());\n");
			echo ("            }\n\n");

			echo ("            function Cancelar(){\n");
			echo ("              document.frmRedir.action = 'importar_curso.php?';\n");
			echo ("              document.frmRedir.submit();\n");
			echo ("            }\n\n");

			echo ("          </script>\n\n");

			echo ("          <input type=\"hidden\" name=cod_curso_todos value='" . $cod_curso_todos . "' />\n");

			echo ("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
			echo ("            <tr>\n");
			echo ("              <td valign=\"top\">\n");
			// 23 - Voltar (geral)
			echo ("                <ul class=\"btAuxTabs\">\n");
			echo ("                  <li><span onClick='javascript:history.back(-1);'>" . RetornaFraseDaLista($lista_frases_geral, 23) . "</span></li>\n");
			echo ("                </ul>\n");
			echo ("              </td>\n");
			echo ("            </tr>\n");
			echo ("            <tr>\n");
			echo ("              <td>\n");
			echo ("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
			echo ("                  <tr class=\"head\">\n");
			echo ("                    <td colspan=3>\n");
			// 78 - "Materiais" de Apoio do curso:
			echo ("                      " . RetornaFraseDaLista($lista_frases, 114) . " \"<b>" . $nome_curso_import . "</b>\"<br>\n");
			echo ("                    </td>\n");
			echo ("                  </tr>\n");
			echo ("                  <tr>\n");
			// 27(biblioteca) - Login:
			echo ("                    <td width=\"10%\" style=\"border:0pt;\">\n");
			echo ("                      " . RetornaFraseDaLista($lista_frases_biblioteca, 27) . "\n");
			echo ("                    </td>\n");
			echo ("                    <td colspan=\"2\" style=\"border:0pt; text-align:left;\">\n");
			echo ("                      <input type=\"text\" class=\"input\" name=\"login_import\" value=\"\" />\n");
			echo ("                    </td>\n");
			echo ("                  </tr>\n");
			echo ("                  <tr>\n");
			// 48(biblioteca) - Senha:
			echo ("                    <td width=\"10%\" style=\"border:0pt;\">\n");
			echo ("                      " . RetornaFraseDaLista($lista_frases_biblioteca, 48) . "\n");
			echo ("                    </td>\n");
			echo ("                    <td width=\"10%\" style=\"border:0pt; text-align:left;\">\n");
			echo ("                      <input class=\"input\" type=\"password\" name=\"senha_import\" value=\"\" />\n");
			echo ("                    </td>\n");
			/* 105 - Importar Material de Apoio*/
			echo ("                    <td style=\"border:0pt; text-align:left;\">\n");
			echo ("                      <input class=\"input\" type=\"submit\"  value=\"" . RetornaFraseDaLista($lista_frases, 105) . "\" style=\"width:230px;\" />\n");
			echo ("                    </td>\n");
			echo ("                  </tr>\n");
			echo ("                </table>\n");
			echo ("              </td>\n");
			echo ("            </tr>\n");
			echo ("          </table>\n");
			echo ("          <script type=\"text/javascript\" language=\"JavaScript\" defer>\n\n");
			echo ("            document.frmRedir.onsubmit = function() { return ReAutent(); };\n");
			echo ("            document.frmRedir.login_import.focus();\n");
			echo ("          </script>\n\n");
		} else // Usu�rio autenticado.
			{

			echo ("            <input type=\"hidden\" name=\"cod_curso_import\" value=\"" . $codigo_import . "\" />\n");
			echo ("            <input type=\"hidden\" name=\"curso_extraido\" value=\"" . $curso_extraido . "\" />\n");
			echo ("            <input type=\"hidden\" name=\"curso_compartilhado\" value=\"" . $curso_compartilhado . "\" />\n");
			echo ("            <input type=\"hidden\" name=\"senha_import\" value=\"" . $senha_import . "\" />\n");
			echo ("            <input type=\"hidden\" name=\"tipo_curso\" value=\"" . $tipo_curso . "\" />\n");
			echo ("            <input type=\"hidden\" name=\"cod_categoria\" value=\"" . $cod_categoria . "\" />\n");
			echo ("            <input type=\"hidden\" name=\"cod_curso_todos\" value=\"" . $cod_curso_todos . "\" />\n");
			echo ("            <input type=\"hidden\" name=\"cod_curso\" value=\"" . $cod_curso . "\" />\n");
			echo ("            <input type=\"hidden\" name=\"cod_ferramenta\" value=\"" . $cod_ferramenta . "\" />\n");

			// Armazena chave de autentica��o do usu�rio.
			session_register("login_import_s");
			$login_import_s = CriaChaveDeAutenticacao($cod_curso, $cod_usuario, $codigo_import, $login_import, $senha_import_crypt);
			//?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&cod_topico_raiz_import=".$cod_topico_raiz."';\n");
			echo ("          <script type=\"text/javascript\" language=\"JavaScript\" defer>\n\n");
			echo ("            document.frmRedir.action = 'importar_material.php';");
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
