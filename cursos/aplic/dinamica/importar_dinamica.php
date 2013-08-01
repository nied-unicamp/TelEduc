<?php

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
include ("dinamica.inc");

$cod_ferramenta = 16;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda = 1;
$sock = Conectar("");
$flag_curso_extraido = $_SESSION['flag_curso_extraido'];
if ($curso_extraido)
	$diretorio_arquivos = RetornaDiretorio($sock, 'Montagem');
else
	$diretorio_arquivos = RetornaDiretorio($sock, 'Arquivos');
$diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');

include ("../topo_tela.php");

$tabela = "Dinamica";
$dir = "dinamica";


if (EFormador($sock, $cod_curso, $cod_usuario))
    $usr_formador = true;
  else
    $usr_formador = false;

    
/* Fun��es JavaScript */
echo ("    <script type=\"text/javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
echo ("    <script language=\"JavaScript\" type=\"text/javascript\">\n\n");

echo ("      function Iniciar()\n");
echo ("      {\n");
echo ("        startList();\n");
echo ("      }\n\n");

echo ("      function Importar()\n");
echo ("      {\n");
echo ("        if(confirm('" . RetornaFraseDaLista($lista_frases, 19) . "'))");
echo ("        {\n");
echo ("          document.frmImportar.action = \"acoes_linha.php\"\n");
echo ("          document.frmImportar.acao.value = \"importarItem\";\n");
echo ("          document.frmImportar.submit();\n");
echo ("        }\n\n");
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

$sock = MudarDB($sock, $cod_curso_origem);
echo ("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

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
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
$cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";

echo ("          <form method=\"post\" name=\"frmImportar\">\n");
echo ("          <input type=\"hidden\" name=\"cod_curso\" value=\"" . $cod_curso . "\">\n");
echo ("          <input type=\"hidden\" name=\"cod_categoria\" value=NULL>\n");
echo ("          <input type=\"hidden\" name=\"cod_curso_import\" value=\"" . $cod_curso_origem . "\">\n");
echo ("          <input type=\"hidden\" name=\"cod_item\" value=''>\n");
echo ("          <input type=\"hidden\" name=\"curso_compartilhado\" value=\"" . $curso_compartilhado . "\">\n");
echo ("          <input type=\"hidden\" name=\"curso_extraido\" value=\"" . $curso_extraido . "\">\n");
echo ("          <input type=\"hidden\" name=\"tipo_curso\" value=\"" . $tipo_curso . "\">\n");
echo ("          <input type=\"hidden\" name=\"acao\" value=\"\">\n");
if ('E' == $tipo_curso) {
	echo ("          <input type=\"hidden\" name=\"data_inicio\" value='" . $data_inicio . "'>\n");
	echo ("          <input type=\"hidden\" name=\"data_fim\" value='" . $data_fim . "'>\n");
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

$texto = ConverteAspas2Html(Enter2Br(RetornaTextoDinamica($sock, $cod_curso_origem)));
$tipo_import = ExisteDinamicaImport($sock, $cod_curso_origem, $diretorio_arquivos);

if ($tipo_import != 'N' && ($texto != "" || $tipo_import == 'A')) {
	
	echo ("                  <tr class=\"head\">\n");
	/* Conte�do da Din�mica */
	echo ("                    <td align=\"center\">" . RetornaFraseDaLista($lista_frases, 43) . "</td>\n");
	echo ("                  </tr>\n");
	if($texto != "")
	{
		/* Campo para o Texto */
		echo ("                  <tr class=\"head01\">\n");
		/* 10 - Texto */
		echo ("                    <td align=\"left\">" . RetornaFraseDaLista($lista_frases, 10) . "</td>\n");
		echo ("                  </tr>\n");
		echo ("                  <tr>\n");
	
		echo ("                    <td align=\"left\"><div class=\"divRichText\">" . $texto . "</div></td>\n");
		echo ("                  </tr>\n");
	}
	else
	{
		echo ("                  <tr class=\"head01\">\n");
		/* 57 (biblioteca) - Arquivos */
		echo ("                    <td align=\"left\">" . RetornaFraseDaLista($lista_frases_biblioteca, 57) . "</td>\n");
		echo ("                  </tr>\n");
	
		echo ("                  <tr>\n");
		echo ("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
	
		$dir_name = "dinamica";
		$linha_item = RetornaDadosDinamica($sock);
	
		$cod_item = $linha_item['cod_dinamica'];
		
		$dir_item_temp = CriaLinkVisualizar($sock, $dir_name, $cod_curso_origem, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);
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
	}
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
