<?

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/dinamica/importar_dinamica2.php

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
  ARQUIVO : cursos/aplic/dinamica/importar_dinamica2.php
  ========================================================== */
$bibliotecas = "../bibliotecas/";
include ($bibliotecas . "geral.inc");
include ($bibliotecas . "importar.inc");
include ("dinamica.inc");
// **************** VARI�VEIS DE ENTRADA ****************
// Recebe de 'importar_dinamica.php'
//    c�digo da categoria que estava sendo listada.
//$cod_categoria = $_POST['cod_categoria'];
//    c�digo do curso do qual itens ser�o importados
//$cod_curso_import = $_POST['cod_curso_import'];
//    tipo do curso: A(ndamento), I(nscri��es abertas), L(atentes),
//  E(ncerrados)
//$tipo_curso = $_POST['tipo_curso'];
//    booleano, se o curso, cujos itens ser�o importados, foi
//  escolhido na lista de cursos compartilhados.
//$curso_compartilhado = $_POST['curso_compartilhado'];
//    booleando, se o curso, cujos itens ser�o importados, � um
//  curso extra�do.
$curso_extraido = $_POST['curso_extraido'];
//    arrays de itens e t�picos que ser�o importados
$cod_itens_import = $_POST['cod_itens_import'];

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

if ('E' == $tipo_curso) {
	//    per�odo especificado para listar os cursos
	//  encerrados.
	$data_inicio = $_SESSION['data_inicio'];
	$data_fim = $_SESSION['data_fim'];
}

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

session_register("login_import_s");
if (isset ($login_import))
	$login_import_s = $login_import;
else
	$login_import = $_SESSION['login_import_s'];

$sock = Conectar("");
$lista_frases_biblioteca = RetornaListaDeFrases($sock, -2);
Desconectar($sock);

$cod_ferramenta = 16;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda = 1;
include ("../topo_tela.php");

/* Verifica se o usuario eh formador. */
$usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

$tabela = "Dinamica";
$dir = "dinamica";

/* Fun��es JavaScript */
echo ("    <script type=\"text/javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
echo ("    <script language=\"JavaScript\" type=\"text/javascript\">\n\n");

echo ("      function Iniciar()\n");
echo ("      {\n");
echo ("        startList();\n");
echo ("      }\n\n");

echo ("      function Voltar()\n");
echo ("      {\n");
echo ("        window.location='importar_curso.php?cod_curso=" . $cod_curso . "&amp;cod_usuario=" . $cod_usuario . "&amp;cod_ferramenta=16';\n");
echo ("      }\n\n");

echo ("    </script>\n\n");

include ("../menu_principal.php");
Desconectar($sock);

echo ("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

// P�gina Principal
// 1 - Dinamica do Curso
$cabecalho = ("         <h4>" . RetornaFraseDaLista($lista_frases, 1));
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

// Se n�o foi definido um curso do qual ser�o importados
// itens, emite uma mensagem de erro.
if (!isset ($cod_curso_import)) {
	echo ("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
	echo ("            <tr>\n");
	echo ("              <td valign=\"top\">\n");
	echo ("                <ul class=\"btAuxTabs\">\n");
	/* 23 - Voltar (geral) */
	echo ("                  <li><span onclick='Voltar();'>" . RetornaFraseDaLista($lista_frases_geral, 23) . "</span></li>\n");
	echo ("                </ul>\n");
	echo ("              </td>\n");
	echo ("            </tr>\n");
	echo ("            <tr>\n");
	echo ("              <td>\n");
	echo ("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
	echo ("                  <tr>\n");
	echo ("                    <td>\n");
	/* 51(biblioteca): Erro ! Nenhum c�digo de curso para importa��o foi recebido ! */
	echo ("                      " . RetornaFraseDaLista($lista_frases_biblioteca, 51) . "\n");
	echo ("                    </td>\n");
	echo ("                  <tr>\n");
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

// Se o curso DO QUAL ser�o importados itens foi montado
// na base tempor�ria, ent�o define o par�metro $opt para
// conex�o a ela.
if ($curso_extraido)
	$opt = TMPDB;
else
	$opt = "";

// Autentica no curso PARA O QUAL ser�o importados os itens.
$cod_usuario = VerificaAutenticacao($cod_curso);

// Se o curso foi selecionado na lista de todos cursos e
// a autentica��o do usu�rio nesse curso n�o � v�lida ent�o
// encerra a execu��o do script.
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

	echo ("          <form method=\"post\" name=\"frmRedir\" action=\"importar_curso.php\">\n");
	echo ("            <input type=\"hidden\" name=\"cod_curso\" value=\"" . $cod_curso . "\">\n");
	echo ("            <input type=\"hidden\" name=\"cod_categoria\" value=\"" . $cod_categoria . "\">\n");
	echo ("            <input type=\"hidden\" name=\"cod_ferramenta\" value=\"" . $cod_ferramenta . "\">\n");
	echo ("          </form>\n");
	echo ("          <script language=\"javascript\" type=\"text/javascript\">\n\n");
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

// Marca data de �ltimo acesso ao curso tempor�rio. Esse recurso � importante
// para elimina��o das bases tempor�rias, mediante compara��o dessa data adicionado
// um per�odo de folga com a data em que o script para elimina��o estiver rodando.
MarcarAcessoCursoExtraidoTemporario($sock, $cod_curso_import);

// Se o curso foi montado (extra�do) lista os arquivos do caminho
// tempor�rio.
if ($curso_extraido)
	$diretorio_arquivos_origem = RetornaDiretorio($sock, 'Montagem');
else
	$diretorio_arquivos_origem = RetornaDiretorio($sock, 'Arquivos');

// Raiz do diret�rio de arquivos do curso PARA O QUAL ser�o importados
// os itens.
$diretorio_arquivos_destino = RetornaDiretorio($sock, 'Arquivos');
$diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');

Desconectar($sock);

// Conecta-se � base do curso.
$sock = Conectar($cod_curso_import, $opt);

// Obt�m o nome do curso.
$nome_curso_import = NomeCurso($sock, $cod_curso_import);

// Se o curso n�o foi selecionado na lista de todos cursos,
// verifica as permiss�es de acesso ao curso e �s ferramentas.
if (!$curso_compartilhado) {
	VerificaAcessoAoCurso($sock, $cod_curso_import, $cod_usuario_import);
	VerificaAcessoAFerramenta($sock, $cod_curso_import, $cod_usuario_import, $cod_ferramenta);
}

Desconectar($sock);

if (ImportarDinamica($cod_curso, $cod_usuario, $cod_curso_import, $curso_extraido, $tabela, $dir, $diretorio_arquivos_destino, $diretorio_arquivos_origem))
	$sucesso = true;
else
	$sucesso = false;

if ($cod_curso_import && !$texto_dinamica) {
	$caminho_link = "../../diretorio/dinamica_" . $cod_curso . "_" . $cod_usuario . "_" . $cod_curso_import;
	RemoveArquivo($caminho_link);
}

$sock = Conectar("");
MudarDB($sock, $cod_curso);

Desconectar($sock);

$sock = Conectar($cod_curso_import, $opt);

$nome_curso_import = NomeCurso($sock, $cod_curso_import);

if (!$curso_compartilhado) {
	VerificaAcessoAoCurso($sock, $cod_curso_import, $cod_usuario_import);
	VerificaAcessoAFerramenta($sock, $cod_curso_import, $cod_usuario_import, $cod_ferramenta);
}

echo ("          <form method=\"post\" name=\"frmImportar\" action=\"dinamica.php\">\n");
echo ("            <input type=\"hidden\" name=\"cod_curso\" value=\"" . $cod_curso . "\" />\n");
echo ("            <input type=\"hidden\" name=\"cod_categoria\" value=\"" . $cod_categoria . "\" />\n");
echo ("            <input type=\"hidden\" name=\"cod_curso_import\" value=\"" . $cod_curso_import . "\" />\n");
echo ("            <input type=\"hidden\" name=\"curso_extraido\" value=\"" . $curso_extraido . "\" />\n");
echo ("            <input type=\"hidden\" name=\"curso_compartilhado\" value=\"" . $curso_compartilhado . "\" />\n");
echo ("            <input type=\"hidden\" name=\"tipo_curso\" value=\"" . $tipo_curso . "\" />\n");
if ('E' == $tipo_curso) {
	echo ("            <input type=\"hidden\" name=\"data_inicio\" value=\"" . $data_inicio . "\" />\n");
	echo ("            <input type=\"hidden\" name=\"data_fim\" value=\"" . $data_fim . "\" />\n");
}
echo ("            <input type=\"hidden\" name=\"cod_ferramenta\" value=\"" . $cod_ferramenta . "\" />\n");
echo ("          <table cellpadding=\"0\" cellspacing=\"0\"  class=\"tabExterna\">\n");
echo ("            <tr>\n");
echo ("              <td valign=\"top\">\n");
echo ("                <ul class=\"btAuxTabs\">\n");
/* 23 - Voltar (geral) */
echo ("                  <li><span onclick='Voltar();'>" . RetornaFraseDaLista($lista_frases_geral, 23) . "</span></li>\n");
echo ("                </ul>\n");
echo ("              </td>\n");
echo ("            </tr>\n");
echo ("            <tr>\n");
echo ("              <td>\n");
echo ("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\" id=\"tabInterna\">\n");
echo ("                  <tr>\n");
echo ("                    <td>\n");

if ($sucesso) {
	// 55 - Itens importados com sucesso.
	echo ("                      " . RetornaFraseDaLista($lista_frases_biblioteca, 55) . "\n");
} else {
	// 56 - Erro na importa��o dos itens selecionados.
	echo ("                      " . RetornaFraseDaLista($lista_frases_biblioteca, 56) . "\n");
}

echo ("                    </td>\n");
echo ("                  </tr>\n");
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

Desconectar($sock);
?>
