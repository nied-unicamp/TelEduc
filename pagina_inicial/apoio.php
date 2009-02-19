<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/apoio.php

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
  ARQUIVO : pagina_inicial/apoio.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  $pag_atual = "apoio.php";
  include("../topo_tela_inicial.php");

  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 11 - Apoio */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,11)."</h4>\n");

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
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr>\n");
  echo("                    <td align=left>\n");
  /* 46 - Este projeto contou com o apoio financeiro da */
  /* 47 - Funda��o de Amparo � Pesquisa do Estado de S�o Paulo */
  /* 48 - e do */
  /* 49 - Conselho Nacional de Desenvolvimento Cient�fico e Tecnol�gico */
  echo("                      <p>".RetornaFraseDaLista($lista_frases,46)." ".RetornaFraseDaLista($lista_frases,47)." - <b>FAPESP</b> ".RetornaFraseDaLista($lista_frases,48)." ".RetornaFraseDaLista($lista_frases,49)." - <b>CNPq</b>.\n");
  /* 50 - Atualmente est� sendo apoiado pela */
  /* 51 - Organiza��o dos Estados Americanos */
  echo("                      <p>".RetornaFraseDaLista($lista_frases,50)." ".RetornaFraseDaLista($lista_frases,51)." - <b>".RetornaFraseDaLista($lista_frases,52)."</b>.\n");

  echo("                      <p>&nbsp;\n");
  echo("                      <table>\n");
  echo("                        <tr>\n");
  /* 47 - Funda��o de Amparo � Pesquisa do Estado de S�o Paulo */
  /* 49 - Conselho Nacional de Desenvolvimento Cient�fico e Tecnol�gico */
  /* 51 - Organiza��o dos Estados Americanos */

  echo("                          <td style=\"border:none\" valign=bottom align=center><a href=http://www.oas.org/ target=OEA><img src=\"figuras/oea.jpg\" border=\"0\" /><br />".RetornaFraseDaLista($lista_frases,51)."</a></td>\n");
  echo("                          <td style=\"border:none\" valign=bottom align=center><a href=http://www.fapesp.br/ target=FAPESP><img src=\"figuras/fapesp.jpg\" border=\"0\" /><br />".RetornaFraseDaLista($lista_frases,47)."</a></td>\n");
  echo("                          <td style=\"border:none\" valign=bottom align=center><a href=http://www.cnpq.br/ target=CNPQ><img src=\"figuras/cnpq.gif\" border=\"0\" /><br />".RetornaFraseDaLista($lista_frases,49)."</a></td>\n");
  echo("                        </tr>\n");
  echo("                      </table>\n");
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
  echo("</html>");
  Desconectar($sock);
?>
