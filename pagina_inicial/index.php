<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/info.php

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
  ARQUIVO : pagina_inicial/info.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  $pag_atual = "index.php";
  include("../topo_tela_inicial.php");

  $lista_frases_aux = RetornaListaDeFrases($sock,-2);

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases_aux);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("permissaoAdmExterna", 0, 4);

  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 2 - Pagina Inicial */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,2)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellspacing=0 class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 3 - O que é o TelEduc */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,3)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>   \n");
  /* 19 - O <b>TelEduc</b> � um ambiente de ensino a dist�ncia pelo qual se pode realizar cursos atrav�s da Internet. Est� sendo desenvolvido conjuntamente pelo <b>N�cleo de Inform�tica Aplicada � Educa��o (Nied)</b> e pelo <b>Instituto de Computa��o (IC)</b> da <b>Universidade Estadual de Campinas (Unicamp)</b>. */
  echo("                    <td class=\"alLeft\">\n");
  /* 31 - O */
  /* 23 - � um ambiente para realiza��o de cursos a dist�ncia atrav�s da Internet. Est� sendo desenvolvido no */
  echo("                      <p style=\"text-indent:15px\">".RetornaFraseDaLista($lista_frases,31)." <b>TelEduc</b> ".RetornaFraseDaLista($lista_frases,23)."\n");

  /* 20 - N�cleo de Inform�tica Aplicada a Educa��o */
  /* 24 - sob a orienta��o da Profa. Dra. */
  /* 25 - do */
  /* 21 - Instituto de Computa��o */
  /* 26 - da */
  /* 22 - Universidade Estadual de Campinas */
  /* 27 - a partir de uma metodologia de forma��o de professores constru�da com base na an�lise das v�rias experi�ncias presenciais realizadas pelos profissionais do n�cleo. */

  echo("                      <a href=\"http://www.nied.unicamp.br\"><b>Nied</b> (".RetornaFraseDaLista($lista_frases,20).")</a> ".RetornaFraseDaLista($lista_frases,24)." <i>Helo&iacute;sa Vieira da Rocha</I> ".RetornaFraseDaLista($lista_frases,25)." <a href=http://www.ic.unicamp.br target=ic><B>".RetornaFraseDaLista($lista_frases,21)."</b></a> ".RetornaFraseDaLista($lista_frases,26)." <a href=http://www.unicamp.br target=unicamp><B>Unicamp</B> (".RetornaFraseDaLista($lista_frases,22).")</a>, ".RetornaFraseDaLista($lista_frases,27)."\n");
  echo("                      </p>\n");

  echo("                      <p style=\"text-indent:15px\">\n");
  /* 28 - O ambiente � parte integrante da disserta��o de mestrado */
  /* 29 - Forma��o a Dist�ncia de Recursos Humanos para Inform�tica Educativa */
  /* 30 - de autoria de */
  echo(RetornaFraseDaLista($lista_frases,28)." <B>\"".RetornaFraseDaLista($lista_frases,29)."\"</b> ".RetornaFraseDaLista($lista_frases,30)." <I>Alessandra de Dutra e Cerceau</i>.\n");
  echo("                      </p>\n");

  echo("                      <p style=\"text-indent:15px\">\n");
  /* 31 - O */
  /* 32 - como uma de suas linhas de pesquisa, tem realizado diversos cursos a dist�ncia atrav�s do TelEduc desde 1998, acompanhando progressivamente o desenvolvimento do ambiente. */
  echo("                      ".RetornaFraseDaLista($lista_frases,31)." <B>Nied</b>, ".RetornaFraseDaLista($lista_frases,32)."</p>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>