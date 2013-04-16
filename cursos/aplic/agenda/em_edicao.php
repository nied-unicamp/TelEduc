<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/agenda/em_edicao.php

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
  ARQUIVO : cursos/aplic/agenda/em_edicao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("agenda.inc");

  $cod_ferramenta=1;
  include("../topo_tela.php");

  if (!EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("  </head>");
    echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" bgcolor=\"white\">\n");
    /* 1 - Agenda */
    $cabecalho = "  <br /><br /><h5>".RetornaFraseDaLista($lista_frases, 1);
    /* 25 - �rea restrita ao formador. */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 25)."</h5>";
    echo($cabecalho);
    echo("    <br />\n");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }
  else
  {
    echo("    <script type=\"text/javascript\">\n");
    echo("      function Iniciar(){\n");
    echo("        startList();\n");
    echo("        this.focus();\n");
    echo("      }\n");
    echo("    </script>\n");
    echo("  </head>\n");
    echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" onLoad=\"Iniciar();\">\n");

    /* Página Principal */

    /* 1 - Agenda */
    $cabecalho = ("<br /><br /><h4>".RetornaFraseDaLista ($lista_frases, 1));
    /*43 - Em edicao */
    $cabecalho.= (" - ".RetornaFraseDaLista($lista_frases,43)."</h4>\n");
    echo($cabecalho);

    echo ("<br />\n");

    $linha_item=RetornaDadosDoItem($sock, $cod_item);

    echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("      <tr>\n");
    echo("        <td valign=\"top\" colspan=3>\n");
    echo("          <ul class=\"btAuxTabs\">\n");
     /* 13 - Fechar (ger) */
    echo("            <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
    /* 52 - Atualizar (ger) */
    echo("            <li><a href=\"em_edicao.php?cod_curso=".$cod_curso."&amp;cod_item=".$cod_item."&amp;origem=".$origem."\">".RetornaFraseDaLista($lista_frases_geral,52)."</a></li>\n");
    echo("          </ul>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr>\n");
    echo("        <td colspan=3>\n");    
    echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("            <tr>\n");
    echo("              <td  align=right><b>".RetornaFraseDaLista($lista_frases,18).":&nbsp;</b></td>\n");
    echo("              <td colspan=2>".$linha_item['titulo']."</td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("      <tr>\n");
    echo("        <td>\n");
    echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("            <tr>\n");
    /* 53 - Situacao (ger)*/
    echo("              <td align=center><b>".RetornaFraseDaLista($lista_frases_geral,53)."</b></td>\n");
    /* 56  - Desde (ger) */
    echo("              <td align=center><b>".RetornaFraseDaLista($lista_frases_geral,56)."</b></td>\n");
     /* 57 - Por (ger)*/
    echo("              <td align=center><b>".RetornaFraseDaLista($lista_frases_geral,57)."</b></td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");
 
    $res=RetornaResHistoricoDoItem($sock, $cod_item);
    $num_linhas=RetornaNumLinhas($res);

    $linha=RetornaLinha($res);
    $num_linhas--;
    $nome_usuario=NomeUsuario($sock, $linha['cod_usuario'], $cod_curso);
    $data=UnixTime2DataHora($linha['data']);

    if ($linha['acao']=="E")
      /* 54 - Em Edicao (ger) */
      echo("              <td align=center>".RetornaFraseDaLista($lista_frases_geral,54)."</td>\n");
    else
      /* 55 - Edicao concluida (ger) */
      echo("              <td align=center>".RetornaFraseDaLista($lista_frases_geral,55)."</td>\n");

    echo("              <td align=center>".$data."</td>\n");
    echo("              <td align=center>".$nome_usuario."</td>\n");

    echo("          </table>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
  }

?>
