<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/forum_ver_mensagem.php

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
  ARQUIVO : cursos/aplic/forum/ver_mensagem.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("forum.inc");

  $cod_ferramenta=19;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  /* topo_tela.php faz isso
  $cod_usuario = VerificaAutenticacao($cod_curso);

  $sock = Conectar("");

  $lista_frases = RetornaListaDeFrases($sock, 19);
  $lista_frases_geral = RetornaListaDeFrases($sock, -1);

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  VerificaAcessoAoCurso($sock, $cod_curso, $cod_usuario); */

  /* Verifica se o usuario eh formador. */
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  /* Obt�m o nome e o status do f�rum                     */
  $forum_dados = RetornaForum($sock, $cod_forum);

  /* Obt�m dados da mensagem.           */
  list($total, $mensagem_dados) = RetornaMensagem($sock, $cod_msg, $cod_forum);

  echo("<script type=\"text/javascript\" language=\"javascript\">\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList(); \n");
  echo("  } \n");

  echo("  function OpenWindowPerfil(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=130,left=130,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("</script>\n");

  echo("<body onLoad=\"Iniciar();\">\n");
  echo("<br><br>\n");
  /* 1 - Intermap */
  echo("<h4>".RetornaFraseDaLista($lista_frases,1));
  /* 28 - F�rum de Discuss�o */
  echo(" - ".RetornaFraseDaLista($lista_frases,28));
  /* 77 - Ver Mensagem */
  echo(" - ".RetornaFraseDaLista($lista_frases,77)."</h4>\n");

  echo("<br>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <td>\n");
  echo("      <ul class=\"btAuxTabs\">\n");
  /* 13 - Fechar (geral) */
  echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");

  if ($total > 0)
  {
    $nome_usuario = NomeUsuario($sock, $mensagem_dados['cod_usuario']);
    $data = UnixTime2Data($mensagem_dados['data']);
    $hora = UnixTime2Hora($mensagem_dados['data']);

    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    /* Exibe os nomes dos campos da tabela.                                  */
    echo("        <tr class=\"head\">\n");
    // 71 - T�tulo
    echo("          <td width=\"40%\">".RetornaFraseDaLista($lista_frases,71)."</td>\n");
    // 22 - Emissor
    echo("          <td width=\"30%\">".RetornaFraseDaLista($lista_frases,22)."</td>\n");
    // 17 - Data
    echo("          <td width=\"30%\">".RetornaFraseDaLista($lista_frases,17)."</td>\n");
    echo("        </tr>\n");

    /* Exibe os dados da emissao da mensagem: titulo, nome do emissor e data */
    echo("        <tr>\n");
    /* Exibe o titulo da mensagem eliminando algumas tags HTML.              */
    echo("          <td>".LimpaTitulo($mensagem_dados['titulo'])."</td>\n");
    echo("          <td><a href=\"#\" onClick=\"return(OpenWindowPerfil(".$mensagem_dados['cod_usuario']."));\">".$nome_usuario."</a></td>\n");
    echo("          <td>".$data.", ".$hora."</td>\n");
    echo("        </tr>\n");

    echo("        <tr class=\"head\">\n");
    // 39 - Mensagem
    echo("          <td colspan=3>".RetornaFraseDaLista($lista_frases,39)."</td>\n");
    echo("        </tr>\n");

    /* Exibe o conte�do da mensagem convertendo quebras de linhas em tags  */
    /* <BR> e elimina algumas tags HTML.                                   */
    echo("        <tr>\n");
    echo("          <td colspan=\"3\">".Enter2BR(LimpaConteudo($mensagem_dados['mensagem']))."</td>\n");
    echo("        </tr>\n");
    // Fim Tabela Interna
    echo("      </table>\n");
  }

  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabel�o
  echo("</table>\n");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>