<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/forum_fluxo_conversacao.php

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
  ARQUIVO : cursos/aplic/intermap/forum_fluxo_conversacao.php
  ========================================================== */

  include("forum.inc");

  $linha_curso=RetornaDadosCurso($sock,$cod_curso);

  $foruns=RetornaForuns($sock);

  if (count($foruns)>0)
  {
    echo("<form name=mapa action=forum_fluxo_conversacao2.php target=Intermap method=get>\n");
    //echo(RetornaSessionIDInput()."\n");
    echo("<input type=hidden name=cod_curso value=".$cod_curso." />\n");
    echo("<input type=hidden name=todos value=sim />\n");

    echo("<table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("  <tr class=\"head\">\n");
    // 29 - F�rum de Discuss�o:
    echo("    <td width=50%>".RetornaFraseDaLista($lista_frases,29)."</td>\n");
    // 25 - Exibir:
    echo("    <td>".RetornaFraseDaLista($lista_frases,25)."</td>\n");
    echo("  </tr>\n");

    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <select class=input name=cod_forum>\n");
    foreach($foruns as $cod_forum => $linha)
    {
      echo("        <option value=".$cod_forum.">");
      echo($linha['nome']." ");
      if ($linha['status']=='L')
        echo("(somente leitura)");
      // 18 - de
      echo(" - ".RetornaFraseDaLista($lista_frases,18)." ".UnixTime2Data($linha['inicio']));
      // 11 - at�
      echo(" ".RetornaFraseDaLista($lista_frases,11)." ".UnixTime2Data($linha['fim']));
      echo("        </option>\n");
    }
    echo("      </select>\n");
    echo("    </td>\n");

    echo("    <td>\n");
    // 24 - Estrutura de Respostas
    echo("      <input type=radio checked class=g1field name=exibir value=estrutura />".RetornaFraseDaLista($lista_frases,24)."<br/>\n");
    // 35 - Interven��o do Professor
    echo("      <input type=radio class=g1field name=exibir value=intervencao />".RetornaFraseDaLista($lista_frases,35)."<br/>\n");
    echo("    </td>\n");
    echo("  </tr>\n");

    echo("</table>\n");

    echo("<div align=\"right\">\n");
    // 53 - Ok
    echo("  <input class=input type=submit value='".RetornaFraseDaLista($lista_frases,53)."' />\n");
    echo("</div>\n");
    echo("</form>\n");
  }
  else
  {
    echo("<table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("  <tr>\n");
    // 47 - N�o existe nenhum f�rum neste curso.
    echo("    <td>".RetornaFraseDaLista($lista_frases,47)."</td>");
    echo("  </tr>\n");
    echo("</table>\n");
  }
?>