<?


/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/agenda/importar_agenda.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
//importar_curso.php


/*==========================================================
  ARQUIVO : cursos/aplic/agenda/importar_agenda.php
  ========================================================== */
$bibliotecas = "../bibliotecas/";
include ($bibliotecas . "geral.inc");
include ($bibliotecas . "importar.inc");
include ("agenda.inc");

// **************** VARIï¿½VEIS DE ENTRADA ****************
// Recebe de 'importar_curso2.php'
//    cï¿½digo do curso
$cod_curso = $_GET['cod_curso'];

////    cï¿½digo da categoria que estava sendo listada.
//$cod_categoria = $_GET['cod_categoria'];
////    cï¿½digo do curso do qual itens serï¿½o importados
//$cod_curso_import = $_GET['cod_curso_import'];
////    cï¿½digo da ferramenta cujos itens serï¿½o importados
//$cod_ferramenta = $_GET['cod_ferramenta'];
////    tipo do curso: A(ndamento), I(nscriï¿½ï¿½es abertas), L(atentes),
////  E(ncerrados)
//
//$tipo_curso = $_POST['tipo_curso'];
//
//if ('E' == $tipo_curso) {
//	//  perï¿½odo especificado para listar os cursos
//	//  encerrados.
//	$data_inicio = $_POST['data_inicio'];
//	$data_fim = $_POST['data_fim'];
//}
////    booleano, se o curso, cujos itens serï¿½o importados, foi
////  escolhido na lista de cursos compartilhados.
////$curso_compartilhado = $_POST['curso_compartilhado'];
////    booleando, se o curso, cujos itens serï¿½o importados, ï¿½ um
////  curso extraï¿½do.
//$curso_extraido = $_POST['curso_extraido'];
//
//// ******************************************************

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

$cod_ferramenta = 1;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda = 6;
include ("../topo_tela.php");

$tabela = "Agenda";
$dir = "agenda";

echo ("    <script type=\"text/javascript\" src=\"../js-css/dhtmllib.js\"></script>\n");
echo ("    <script type=\"text/javascript\"src=\"../bibliotecas/javacrypt.js\" defer></script>\n");
/* Funï¿½ï¿½es JavaScript */
echo ("    <script type=\"text/javascript\" defer>\n\n");

echo ("      function Iniciar()\n");
echo ("      {\n");
echo ("        startList();\n");
echo ("      }\n\n");

echo ("      function ExibirItem(cod_item)\n");
echo ("       {\n");
echo ("         document.frmImportar.cod_item.value = cod_item;\n");
echo ("         document.frmImportar.action = 'importar_ver.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=1';");
echo ("         document.frmImportar.submit();\n");
echo ("       }\n\n");

echo ("      function Validacheck(){\n");
echo ("        var i;\n");
echo ("        var j=0;\n");
echo ("        var cod_itens=document.getElementsByName('cod_itens_import[]');\n");
echo ("        var Cabecalho = document.getElementById('select_all');\n");
echo ("        for (i=0; i < cod_itens.length; i++){\n");
echo ("          if (cod_itens[i].checked){\n");
echo ("            j++;\n");
echo ("          }\n");
echo ("        }\n");
echo ("        if ((j)==(cod_itens.length)) Cabecalho.checked=true;\n");
echo ("        else Cabecalho.checked=false;\n");
echo ("        if((j)>0){\n");
echo ("          document.getElementById('Importar_Selec').className=\"menuUp02\";\n");
echo ("          document.getElementById('Importar_Selec').onclick=function(){ Importar(); };\n");
echo ("           return true;\n");
echo ("        }else{\n");
echo ("          document.getElementById('Importar_Selec').className=\"menuUp\";\n");
echo ("          document.getElementById('Importar_Selec').onclick=function(){  };\n");
echo ("          return false;\n");
echo ("        }\n");
echo ("      }\n\n");

echo ("       function Importar()\n");
echo ("       {\n");
echo ("         if(Validacheck())\n");
echo ("         {\n");
echo ("           document.frmImportar.action = 'importar_agenda2.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=1';");
echo ("           document.frmImportar.submit();\n");
echo ("         }\n");
echo ("       }\n\n");

echo ("       function CancelarImportacao()\n");
echo ("       {\n");
echo ("         document.frmImportar.action = 'importar_curso.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=1';\n");
echo ("         document.frmImportar.submit();\n");
echo ("       }\n\n");

echo ("       function Cancelar()\n");
echo ("       {\n");
echo ("         window.location = 'importar_curso.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=1';\n");
echo ("       }\n\n");

echo ("      function CheckAll(){\n");
echo ("        var e;\n");
echo ("        var i;\n");
echo ("        var CabMarcado = document.getElementById('select_all').checked;\n");
echo ("        var cod_itens=document.getElementsByName('cod_itens_import[]');\n");
echo ("        for(i = 0; i < cod_itens.length; i++){\n");
echo ("          e = cod_itens[i];\n");
echo ("          e.checked = CabMarcado;\n");
echo ("        }\n");
echo ("        Validacheck();\n");
echo ("      }\n\n");

echo ("     </script>\n\n");

include ("../menu_principal.php");
Desconectar($sock);

if ($tela_formador) {
	$sock = Conectar("");
	$nome_curso_import = NomeCurso($sock, $cod_curso_import);
	$lista_frases_biblioteca = RetornaListaDeFrases($sock, -2);
	//Desconectar($sock);
}

echo ("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* Impede o acesso a algumas secoes aos usuÃ¡rios que nÃ£o sÃ£o formadores. */

if ($tela_formador != 1) {
	/* 1 - Agenda*/
	echo ("          <h4>" . RetornaFraseDaLista($lista_frases, 1));
	/* 73 - Acao exclusiva a formadores. */
	echo ("    - " . RetornaFraseDaLista($lista_frases, 73) . "</h4>");

	/*Voltar*/
	echo ("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

	echo ("          <div id=\"mudarFonte\">\n");
	echo ("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
	echo ("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
	echo ("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
	echo ("          </div>\n");

	echo ("        </td>\n");
	echo ("      </tr>\n");
	include ("../tela2.php");
	echo ("    </table>\n");
	echo ("  </body>\n");
	echo ("</html>\n");
	//Desconectar($sock);
	exit;
}

// Pï¿½gina Principal
// 1 - Agenda
$cabecalho = ("          <h4>" . RetornaFraseDaLista($lista_frases, 1));
/*66 - Importando Agenda */
$cabecalho .= (" - " . RetornaFraseDaLista($lista_frases, 66) . "</h4>\n");
echo ($cabecalho);

// 3 A's - Muda o Tamanho da fonte
echo ("<div id=\"mudarFonte\">\n");
echo ("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
echo ("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
echo ("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
echo ("          </div>\n");

/*Voltar*/
echo ("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br />\n");

if (!isset ($cod_curso_import)) {
	echo ("        <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
	echo ("          <tr>\n");
	echo ("            <td valign=\"top\">\n");
	echo ("              <ul class=\"btAuxTabs\">\n");
	/* 2 - Cancelar (geral) */
	echo ("                  <li><span onclick='Cancelar();'>" . RetornaFraseDaLista($lista_frases_geral, 2) . "</span></li>\n");
	echo ("              </ul>\n");
	echo ("            </td>\n");
	echo ("          </tr>\n");
	echo ("          <tr>\n");
	echo ("            <td>\n");
	echo ("              <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
	echo ("                <tr class=\"head\">\n");
	echo ("                  <td colspan=3>\n");
	echo ("                    " . RetornaFraseDaLista($lista_frases, 78) . " \"" . $nome_curso_import . "\"\n");
	echo ("                  </td>\n");
	echo ("                </tr>\n");
	echo ("                <tr>\n");
	/* 51(biblioteca): Erro ! Nenhum cï¿½digo de curso para importaï¿½ï¿½o foi recebido ! */
	echo ("                  <td>" . RetornaFraseDaLista($lista_frases_biblioteca, 51) . "</td>\n");
	echo ("                </tr>\n");
	echo ("              </table>\n");
	echo ("            </td>\n");
	echo ("          <tr>\n");
	echo ("        </table>\n");
	include ("../tela2.php");
	echo ("        </td>\n");
	echo ("      </tr>\n");
	echo ("    </table>\n");
	echo ("  </body>\n");
	echo ("</html>\n");
	exit ();
}

/*If obscuro,pois tem um parecido logo em seguida*/
if ($curso_extraido)
	$opt = TMPDB;
else
	$opt = "";

// Autentica no curso PARA O QUAL serï¿½o importados os itens.
$cod_usuario = VerificaAutenticacao($cod_curso);

if ((!$curso_compartilhado) && (false === ($cod_usuario_import = UsuarioEstaAutenticadoImportacao($cod_curso, $cod_usuario, $cod_curso_import, $opt)))) {
	// Testar se ï¿½ identicamente falso,
	// pois 0 pode ser um valor vï¿½lido para cod_usuario
	echo ("          <script type=\"text/javascript\" defer>\n\n");
	echo ("            function ReLogar()\n");
	echo ("            {\n");
	// 52(biblioteca) - Login ou senha invï¿½lidos
	echo ("              alert(\"" . RetornaFraseDaLista($lista_frases_biblioteca, 52) . "\");\n");
	echo ("              document.frmRedir.submit();\n");
	echo ("            }\n\n");

	echo ("          </script>\n\n");

	echo ("          <form method=\"post\" name=\"frmRedir\" action=\"importar_curso.php\">\n");
	echo ("            <input type=\"hidden\" name=\"cod_curso\" value=\"" . $cod_curso . "\">\n");
	echo ("            <input type=\"hidden\" name=\"cod_categoria\" value=\"" . $cod_categoria . "\">\n");
	echo ("            <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"" . $cod_topico_raiz . "\">\n");
	echo ("            <input type=\"hidden\" name=\"cod_ferramenta\" value=\"" . $cod_ferramenta . "\">\n");
	echo ("          </form>\n");

	echo ("          <script type=\"text/javascript\">\n\n");
	echo ("            ReLogar();\n");
	echo ("          </script>\n\n");

	echo ("        </td>\n");
	echo ("      </tr>\n");
	echo ("    </table>\n");
	echo ("  </body>\n");
	echo ("</html>\n");
	exit;
}

$sock = Conectar("");

// Marca data de ï¿½ltimo acesso ao curso temporï¿½rio. Esse recurso ï¿½ importante
// para eliminaï¿½ï¿½o das bases temporï¿½rias, mediante comparaï¿½ï¿½o dessa data adicionado
// um perï¿½odo de folga com a data em que o script para eliminaï¿½ï¿½o estiver rodando.
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

if (isset ($caminho_original)) {
	// 88 - Importando para:
	echo ("          " . RetornaFraseDaLista($lista_frases, 88));
	echo ($caminho_original);
	echo ("          <br />\n");
}

echo ("          <form method=\"post\" name=\"frmImportar\">\n");
echo ("          <input type=\"hidden\" name=\"cod_curso\" value=\"" . $cod_curso . "\">\n");
echo ("          <input type=\"hidden\" name=\"cod_categoria\" value=\"" . $cod_categoria . "\">\n");
echo ("          <input type=\"hidden\" name=\"cod_curso_import\" value=\"" . $cod_curso_import . "\">\n");
echo ("          <input type=\"hidden\" name=\"cod_item\" value=''>\n");
echo ("          <input type=\"hidden\" name=\"curso_compartilhado\" value=\"" . $curso_compartilhado . "\">\n");
echo ("          <input type=\"hidden\" name=\"curso_extraido\" value=\"" . $curso_extraido . "\">\n");
echo ("          <input type=\"hidden\" name=\"tipo_curso\" value=\"" . $tipo_curso . "\">\n");
if ('E' == $tipo_curso) {
	echo ("          <input type=\"hidden\" name=\"data_inicio\" value='" . $data_inicio . "'>\n");
	echo ("          <input type=\"hidden\" name=\"data_fim\" value='" . $data_fim . "'>\n");
}

echo ("        <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo ("          <tr>\n");
echo ("            <td valign=\"top\">\n");
echo ("              <ul class=\"btAuxTabs\">\n");
/* 2 - Cancelar (geral) */
echo ("                  <li><span onclick='CancelarImportacao();'>" . RetornaFraseDaLista($lista_frases_geral, 2) . "</span></li>\n");
echo ("              </ul>\n");
echo ("            </td>\n");
echo ("          </tr>\n");
echo ("          <tr>\n");
echo ("            <td>\n");
echo ("              <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo ("                <tr class=\"head\">\n");
echo ("                  <td align=\"center\"><b><input type=\"checkbox\" name=\"select_all\" id=\"select_all\" onclick=\"CheckAll();\"></td>\n");
// 78 - Agendas do Curso:
echo ("                  <td align=\"left\"><b>" . RetornaFraseDaLista($lista_frases, 78) . "&nbsp;" . $nome_curso_import . "</b></td>\n");
/* 7 - Data */
echo ("                  <td align=\"center\"><b>" . RetornaFraseDaLista($lista_frases, 7) . "</b></td>\n");
echo ("                </tr>\n");

/*verificar status... confirmar.. e verificar c eh necessario cancelar a edicao!*/
$lista_itens = RetornaAgendaCurso($sock);

/* Belissimo exemplo do legado do PHP, concebido como linguagem direcionada ao "templating"
 * de sites. Código do período jurassico a frente. Viva o CSS!
 */
if ((count($lista_itens) > 0) && ($lista_itens != "")) {
	foreach ($lista_itens as $cod => $linha_item) {
		/*verificar esses status*/
		if ($linha_item['status'] != 'E') /* nao esta em edicao!*/ {
			$data = UnixTime2Data($linha_item['data']);
			if ($data_acesso < $linha_item['data']) {
				$marcaib = "<b>";
				$marcafb = "</b>";
				$marcatr = " bgcolor=#f0f0f0";
			} else {
				$marcaib = "";
				$marcafb = "";
				$marcatr = "";
			}

			$titulo = "<span id=\"tit_" . $linha_item['cod_item'] . "\" class=\"link\" onclick='ExibirItem(";
			$titulo .= $linha_item['cod_item'] . ");'>" . $linha_item['titulo'] . "</span>";

			$icone = "<img src=\"../imgs/arqp.gif\" border=\"0\" /> ";

			echo ("                <tr" . $marcatr . " class=\"altColor" . ($cod % 2) . "\">\n");
			echo ("                  <td width=\"1%\"><input type=\"checkbox\" name=\"cod_itens_import[]\" onclick=\"Validacheck();\" value=\"" . $linha_item['cod_item'] . "\"></td>\n");
			echo ("                  <td width=\"80%\" align=\"left\">" . $icone . $titulo . "</td>\n");
			echo ("                  <td align=\"center\">" . $marcaib . $data . $marcafb . "</td>\n");

			echo ("                </tr>\n");
		}
	} // de foreach
} else {
	echo ("                <tr " . $marcatr . ">\n");
	/* 4 - Nenhuma agenda adicionada ainda! */
	echo ("                  <td colspan=\"4\">" . RetornaFraseDaLista($lista_frases, 4) . "</td>\n");
	echo ("                </tr>\n");
}
echo ("                </table>\n");
echo ("              </td>\n");
echo ("            </tr>\n");
if (count($lista_itens) > 0) {
	echo ("            <tr>\n");
	echo ("              <td valign=\"top\">\n");
	echo ("                <ul>\n");
	/* 54(biblioteca) - Importar Selecionados */
	echo ("                  <li id=\"Importar_Selec\" class=\"menuUp\"><span id=\"importar\">" . RetornaFraseDaLista($lista_frases_biblioteca, 54) . "</span></li>\n");
	echo ("                </ul>\n");
	echo ("              </td>\n");
	echo ("            </tr>\n");
}

echo ("          </table>\n");
echo ("          </form>\n");
include ("../tela2.php");
echo ("        </td>\n");
echo ("      </tr>\n");
echo ("    </table>\n");
echo ("  </body>\n");
echo ("</html>\n");

Desconectar($sock);
?>
