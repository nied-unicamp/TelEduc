<?

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/dinamica/importar_dinamica.php

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
  ARQUIVO : cursos/aplic/dinamica/importar_dinamica.php
  ========================================================== */
$bibliotecas = "../bibliotecas/";
include ($bibliotecas . "geral.inc");
include ($bibliotecas . "importar.inc");
include ("dinamica.inc");
// **************** VARI�VEIS DE ENTRADA ****************
// Recebe de 'importar_curso2.php'
//    c�digo do curso
$cod_curso = $_GET['cod_curso'];
//  //    c�digo da categoria que estava sendo listada.
//  $cod_categoria = $_POST['cod_categoria'];
//  //    c�digo do curso do qual itens ser�o importados
//  $cod_curso_import = $_POST['cod_curso_import'];
//  //    c�digo da ferramenta cujos itens ser�o importados
$cod_ferramenta = $_GET['cod_ferramenta'];
//  //    tipo do curso: A(ndamento), I(nscri��es abertas), L(atentes),
//  //  E(ncerrados)
//  $tipo_curso = $_POST['tipo_curso'];
//  if ('E' == $tipo_curso)
//  {
//    //  per�odo especificado para listar os cursos
//    //  encerrados.
//    $data_inicio = $_POST['data_inicio'];
//    $data_fim = $_POST['data_fim'];
//  }
//  //  booleano, se o curso, cujos itens ser�o importados, foi
//  //  escolhido na lista de cursos compartilhados.
//  $curso_compartilhado = $_POST['curso_compartilhado'];
//  //    booleando, se o curso, cujos itens ser�o importados, � um
//  //  curso extra�do.
//  $curso_extraido = $_POST['curso_extraido'];

// ******************************************************

/* Pegando as variaveis da SESSION */
$cod_categoria = $_SESSION['cod_categoria'];
$cod_curso_compart = $_SESSION['cod_curso_compart'];
$cod_curso_todos = $_SESSION['cod_curso_todos'];
$login_import = $_SESSION['login_import'];
$senha_import_crypt = $_SESSION['senha_import'];
$tipo_curso = $_SESSION['tipo_curso'];
$data_inicio = $_SESSION['data_inicio'];
$data_fim = $_SESSION['data_fim'];
$cod_curso_import = $_SESSION['cod_curso_import'];
$curso_compartilhado = $_SESSION['curso_compartilhado'];
$login_import = $_SESSION['login_import_s'];

/* Registrando c�digo da ferramenta nas vari�veis de sess�o.
   � necess�rio para saber qual ferramenta est� sendo
   utilizada, j� que este arquivo faz parte de quatro
   ferramentas quase distintas.
 */
session_register("cod_ferramenta_s");
if (isset ($cod_ferramenta))
	$cod_ferramenta_s = $cod_ferramenta;
else
	$cod_ferramenta = $cod_ferramenta_s;

/* Necess�rio para a lixeira. */
session_register("cod_topico_s");
unset ($cod_topico_s);

session_register("login_import_s");
if (isset ($login_import))
	$login_import_s = $login_import;
else
	$login_import = $_SESSION['login_import_s'];

/* Verifica se o usuario eh formador. */
$sock = Conectar($cod_curso);
$usr_formador = EFormador($sock, $cod_curso, $cod_usuario);
Desconectar($sock);
if ($usr_formador) {
	$sock = Conectar("");
	$nome_curso_import = NomeCurso($sock, $cod_curso_import);
	$lista_frases_biblioteca = RetornaListaDeFrases($sock, -2);
	Desconectar($sock);
}

$cod_ferramenta = 16;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda = 1;
include ("../topo_tela.php");

$tabela = "Dinamica";
$dir = "dinamica";

/* Fun��es JavaScript */
echo ("    <script type=\"text/javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
echo ("    <script language=\"JavaScript\" type=\"text/javascript\">\n\n");

echo ("      function Iniciar()\n");
echo ("      {\n");
echo ("        startList();\n");
echo ("      }\n\n");

echo ("      function Importar()\n");
echo ("      {\n");
echo ("        document.frmImportar.action = \"importar_dinamica2.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=" . $cod_ferramenta . "\"\n");
echo ("        document.frmImportar.submit();\n");
echo ("      }\n\n");

echo ("      function CancelarImportacao()\n");
echo ("      {\n");
echo ("        window.location='importar_curso.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=16'");
echo ("      }\n\n");

echo ("       function Cancelar()\n");
echo ("       {\n");
echo ("         window.location = 'importar_curso.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=16';\n");
echo ("       }\n\n");

echo ("      function WindowOpenVer(id)\n");
echo ("      {\n");
echo ("         window.open(id+'?" . time() . "','Dinamica','top=50,left=100,width=600,height=400,menubar=no,status=yes,toolbar=no,scrollbars=yes');\n");
echo ("      }\n");

echo ("    </script>\n\n");

include ("../menu_principal.php");
Desconectar($sock);

echo ("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

/* Impede o acesso a algumas secoes aos usuários que não são formadores. */
if (!$tela_formador) {
	/* 1 - Dinamica do Curso */
	echo ("          <h4>" . RetornaFraseDaLista($lista_frases, 1));
	/* 76 - Acao exclusiva a formadores. */
	echo ("    - " . RetornaFraseDaLista($lista_frases_geral, 76) . "</h4>");

	/*Voltar*/
	echo ("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

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
// 1 - Dinamica do Curso
$cabecalho = ("          <h4>" . RetornaFraseDaLista($lista_frases, 1));
/*37 - Importando Dinamica */
$cabecalho .= (" - " . RetornaFraseDaLista($lista_frases, 37) . "</h4>\n");
echo ($cabecalho);

// 3 A's - Muda o Tamanho da fonte
echo ("          <div id=\"mudarFonte\">\n");
echo ("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
echo ("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
echo ("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
echo ("          </div>\n");

/*Voltar*/
echo ("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br />\n");

if (!isset ($cod_curso_import)) {
	echo ("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
	echo ("            <tr>\n");
	echo ("              <td valign=\"top\">\n");
	echo ("                <ul class=\"btAuxTabs\">\n");
	/* 2 - Cancelar (geral) */
	echo ("                  <li><span onclick='Cancelar();'>" . RetornaFraseDaLista($lista_frases_geral, 2) . "</span></li>\n");
	echo ("                </ul>\n");
	echo ("              </td>\n");
	echo ("            </tr>\n");
	echo ("            <tr>\n");
	echo ("              <td>\n");
	echo ("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
	echo ("                  <tr class=\"head\">\n");
	echo ("                    <td colspan=\"3\">\n");
	echo ("                      " . RetornaFraseDaLista($lista_frases, 37) . " de \"" . $nome_curso_import . "\"\n");
	echo ("                    </td>\n");
	echo ("                  </tr>\n");
	echo ("                  <tr>\n");
	/* 51(biblioteca): Erro ! Nenhum c�digo de curso para importa��o foi recebido ! */
	echo ("                    <td>" . RetornaFraseDaLista($lista_frases_biblioteca, 51) . "</td>\n");
	echo ("                  </tr>\n");
	echo ("                </table>\n");
	echo ("              </td>\n");
	echo ("            </tr>\n");
	echo ("          </table>\n");
	echo ("        </td>\n");
	echo ("      </tr>\n");
	include ("../tela2.php");
	echo ("  </body>\n");
	echo ("</html>\n");
	exit ();
}

if ($curso_extraido)
	$opt = TMPDB;
else
	$opt = "";

$cod_usuario = VerificaAutenticacao($cod_curso);

if ((!$curso_compartilhado) && (false === ($cod_usuario_import = UsuarioEstaAutenticadoImportacao($cod_curso, $cod_usuario, $cod_curso_import, $opt)))) {
	// Testar se � identicamente falso,
	// pois 0 pode ser um valor v�lido para cod_usuario
	echo ("          <script language=javascript type=text/javascript defer>\n\n");
	echo ("            function ReLogar()\n");
	echo ("            {\n");
	// 52(biblioteca) - Login ou senha inv�lidos
	echo ("              alert('" . RetornaFraseDaLista($lista_frases_biblioteca, 52) . "');\n");
	echo ("              document.frmRedir.submit();\n");
	echo ("            }\n\n");

	echo ("          </script>\n\n");

	echo ("          <form method=post name=frmRedir action=importar_curso.php>\n");
	echo ("            <input type=hidden name=cod_curso value=" . $cod_curso . ">\n");
	echo ("            <input type=hidden name=cod_categoria value=" . $cod_categoria . ">\n");
	echo ("            <input type=hidden name=cod_ferramenta value=" . $cod_ferramenta . ">\n");
	echo ("          </form>\n");
	echo ("          <script language=javascript type=text/javascript>\n\n");
	echo ("            ReLogar();\n");
	echo ("          </script>\n\n");
	echo ("        </td>\n");
	echo ("      </tr>\n");
	echo ("    </table>\n");
	echo ("  </body>\n");
	echo ("</html>\n");
	exit ();
}

$sock = Conectar("");

MarcarAcessoCursoExtraidoTemporario($sock, $cod_curso_import);

if ($curso_extraido) {
	$opt = TMPDB;
	$diretorio_arquivos = RetornaDiretorio($sock, 'Montagem');
} else {
	$opt = "";
	$diretorio_arquivos = RetornaDiretorio($sock, 'Arquivos');
}

$diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');

Desconectar($sock);

// Alterna para a base de dados do curso
$sock = Conectar($cod_curso);

$data_acesso = PenultimoAcesso($sock, $cod_usuario, "");

Desconectar($sock);

$sock = Conectar($cod_curso_import, $opt);

$nome_curso_import = NomeCurso($sock, $cod_curso_import);

if (!$curso_compartilhado) {

	VerificaAcessoAoCurso($sock, $cod_curso_import, $cod_usuario_import);
	VerificaAcessoAFerramenta($sock, $cod_curso_import, $cod_usuario_import, $cod_ferramenta);
}

// Apaga link simbolico que por acaso tenha sobrado daquele usuario
$link_arquivo = $diretorio_temp . "/" . $dir . "_" . $cod_curso_import . "_" . $cod_usuario_import;

if (ExisteArquivo($link_arquivo)) {
	RemoveArquivo($link_arquivo);
}

echo ("\n");

echo ("          <form method=\"post\" name=\"frmImportar\" action=\"\">\n");
echo ("            <input type=\"hidden\" name=\"cod_categoria\" value=\"" . $cod_categoria . "\" />\n");
echo ("            <input type=\"hidden\" name=\"cod_curso_import\" value=\"" . $cod_curso_import . "\" />\n");
echo ("            <input type=\"hidden\" name=\"cod_ferramenta\" value=\"" . $cod_ferramenta . "\" />\n");
echo ("            <input type=\"hidden\" name=\"cod_item\" value='' />\n");
echo ("            <input type=\"hidden\" name=\"curso_compartilhado\" value=\"" . $curso_compartilhado . "\" />\n");
echo ("            <input type=\"hidden\" name=\"curso_extraido\" value=\"" . $curso_extraido . "\" />\n");
echo ("            <input type=\"hidden\" name=\"tabela\" value=\"" . $tabela . "\" />\n");
echo ("            <input type=\"hidden\" name=\"tipo_curso\" value=\"" . $tipo_curso . "\" />\n");
if ('E' == $tipo_curso) {
	echo ("             <input type=\"hidden\" name=\"data_inicio\" value='" . $data_inicio . "' />\n");
	echo ("             <input type=\"hidden\" name=\"data_fim\" value='" . $data_fim . "' />\n");
}

echo ("          <table cellpadding=\"0\" cellspacing=\"0\"  class=\"tabExterna\">\n");
echo ("            <tr>\n");
echo ("              <td valign=\"top\">\n");
echo ("                <ul class=\"btAuxTabs\">\n");
/* 2 - Cancelar (geral) */
echo ("                  <li><span onclick='CancelarImportacao()'>" . RetornaFraseDaLista($lista_frases_geral, 2) . "</span></li>\n");
echo ("                </ul>\n");
echo ("              </td>\n");
echo ("            </tr>\n");
echo ("            <tr>\n");
echo ("              <td>\n");
echo ("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\" id=\"tabInterna\">\n");

$texto = ConverteAspas2Html(Enter2Br(RetornaTextoDinamica($sock, $cod_curso_import)));
$tipo_import = ExisteDinamicaImport($sock, $cod_curso_import, $diretorio_arquivos);
if ($tipo_import != 'N' && ($texto != "" || $tipo_import == 'A')) {
	echo ("                  <tr class=\"head\">\n");
	/* Conte�do da Din�mica */
	echo ("                    <td align=\"center\">" . RetornaFraseDaLista($lista_frases, 43) . "</td>\n");
	echo ("                  </tr>\n");

	Desconectar($sock);

	$sock = Conectar($cod_curso);

	/* Campo para o Texto */
	echo ("                  <tr class=\"head01\">\n");
	/* 10 - Texto */
	echo ("                    <td align=\"left\">" . RetornaFraseDaLista($lista_frases, 10) . "</td>\n");
	echo ("                  </tr>\n");
	echo ("                  <tr>\n");

	echo ("                    <td align=\"left\"><div class=\"divRichText\">" . $texto . "</div></td>\n");
	echo ("                  </tr>\n");
	echo ("                  <tr class=\"head01\">\n");
	/* 57 (biblioteca) - Arquivos */
	echo ("                    <td align=\"left\">" . RetornaFraseDaLista($lista_frases_biblioteca, 57) . "</td>\n");
	echo ("                  </tr>\n");
	echo ("                  <tr>\n");
	echo ("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");

	$dir_name = "dinamica";
	$linha_item = RetornaDadosDinamica($sock);
	$cod_item = $linha_item['cod_dinamica'];
	$dir_item_temp = CriaLinkVisualizar($sock, $dir_name, $cod_curso_import, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);
	$lista_arq = RetornaArquivosDinamicaVer($cod_curso, $dir_item_temp['diretorio']);

	if (count($lista_arq) > 0) {
		$conta_arq = 0;

		// Procuramos na lista de arquivos se existe algum visivel
		$ha_visiveis = true;

		$nivel_anterior = 0;
		$nivel = -1;

		foreach ($lista_arq as $cod => $linha) {
			if (!($linha['Arquivo'] == "" && $linha['Diretorio'] == "")) {
				$nivel_anterior = $nivel;
				$espacos = "";
				$espacos2 = "";
				$temp = explode("/", $linha['Diretorio']);
				$nivel = count($temp) - 1;
				for ($c = 0; $c <= $nivel; $c++) {
					$espacos .= "&nbsp;&nbsp;&nbsp;&nbsp;";
					$espacos2 .= "  ";
				}

				$caminho_arquivo = $dir_item_temp['link'] . ConverteUrl2Html($linha['Diretorio'] . "/" . $linha['Arquivo']);

				if ($linha['Arquivo'] != "") {

					if ($linha['Diretorio'] != "") {
						$espacos .= "&nbsp;&nbsp;&nbsp;&nbsp;";
						$espacos2 .= "  ";
					}

					if ($linha['Status'])
						$arqEntrada = "arqEntrada='sim'";
					else
						$arqEntrada = "arqEntrada='nao'";

					if (eregi(".zip$", $linha['Arquivo'])) {
						// arquivo zip
						$imagem = "<img alt=\"\" src=\"../imgs/arqzip.gif\" border=\"0\" />";
						$tag_abre = "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" onclick=\"WindowOpenVer('" . $caminho_arquivo . "');\" tipoArq=\"zip\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\" arqZip=\"" . $linha['Arquivo'] . "\" " . $arqEntrada . ">";
					} else {
						// arquivo comum
						$imagem = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
						$tag_abre = "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" onclick=\"WindowOpenVer('" . $caminho_arquivo . "');\" tipoArq=\"comum\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\" " . $arqEntrada . ">";
					}

					$tag_fecha = "</span>";

					echo ("                        " . $espacos2 . "<span id=\"arq_" . $conta_arq . "\">\n");
					echo ("                          " . $espacos2 . $espacos . $imagem . $tag_abre . $linha['Arquivo'] . $tag_fecha . " - (" . round(($linha['Tamanho'] / 1024), 2) . "Kb)");

					echo ("<span id=\"local_entrada_" . $conta_arq . "\">");
					if ($linha['Status'])
						// ?? - entrada
						echo ("<span id=\"arq_entrada_" . $conta_arq . "\">- <span style='color:red;'>entrada</span></span>");
					echo ("</span>\n");
					echo ("                          " . $espacos2 . "<br />\n");
					echo ("                        " . $espacos2 . "</span>\n");
				} else {
					if ($nivel_anterior >= $nivel) {
						$i = $nivel_anterior - $nivel;
						$j = $i;
						$espacos3 = "";
						do {
							$espacos3 .= "  ";
							$j--;
						} while ($j >= 0);
						do {
							echo ("                      " . $espacos3 . "</span>\n");
							$i--;
						} while ($i >= 0);
					}
					// pasta
					$imagem = "<img alt=\"\" src=\"../imgs/pasta.gif\" border=\"0\" />";
					echo ("                      " . $espacos2 . "<span id=\"arq_" . $conta_arq . "\">\n");
					echo ("                        " . $espacos2 . "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" tipoArq=\"pasta\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\"></span>\n");
					if ($usr_formador) {
						echo ("                        " . $espacos2 . "<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_" . $conta_arq . "\">\n");
					}
					echo ("                        " . $espacos2 . $espacos . $imagem . $temp[$nivel] . "\n");
					echo ("                        " . $espacos2 . "<br />\n");
				}

			}
			$conta_arq++;
		}
		do {
			$j = $nivel;
			$espacos3 = "";
			do {
				$espacos3 .= "  ";
				$j--;
			} while ($j >= 0);
			//           echo("                      ".$espacos3."</span>\n");
			$nivel--;
		}
		while ($nivel >= 0);
	}

	echo ("                    </td>\n");
	echo ("                  </tr>\n");
	echo ("                </table>\n");
	echo ("              <input type=\"hidden\" name=\"texto_dinamica\" value='" . $texto . "' />\n");
	echo ("              <input type=\"hidden\" name=\"naohadinamica\" value=\"1\" />\n");
	echo ("              </td>\n");
	echo ("            </tr>\n");
	echo ("            <tr>\n");
	echo ("              <td valign=\"top\">\n");
	echo ("                <ul class=\"btAuxTabs\">\n");
	// 36 - Importar Dinamica
	echo ("                  <li><span onclick='Importar();'>" . RetornaFraseDaLista($lista_frases, 36) . "</span></li>\n");
	echo ("                </ul>\n");
} else {
	echo ("                  <tr>\n");
	// 44 - Este curso ainda não contém Dinâmica.
	echo ("                    <td align=\"left\">" . RetornaFraseDaLista($lista_frases, 44) . "</td>\n");
	echo ("                  </tr>\n");
	echo ("                </table>\n");
}
echo ("              </td>\n");
echo ("            </tr>\n");
echo ("          </table>\n");
echo ("            </form>\n");
echo ("        </td>\n");
echo ("      </tr>\n");
include ("../tela2.php");
echo ("  </body>\n");
echo ("</html>\n");

Desconectar($sock);
?>
