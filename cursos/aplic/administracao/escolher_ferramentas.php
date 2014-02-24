<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/escolher_ferramentas.php

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
  ARQUIVO : cursos/aplic/administracao/escolher_ferramentas.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda = 4;

  include("../topo_tela.php");

  Desconectar($sock);
  $sock = Conectar("");
  $lista_frases_ferramentas = RetornaListaDeFrases($sock, -4);
  $lista_ferramentas = RetornaListaFerramentas($sock);
  $ordem_ferramentas = RetornaOrdemFerramentas($sock);
  Desconectar($sock);
  $sock = Conectar($cod_curso);

  echo("    <script type=\"text/javascript\">\n");

  echo("     function Iniciar()\n");
  echo("     {\n");
  echo("       startList();\n");
  echo("     }\n\n");
  echo("    </script>\n");

  include("../menu_principal.php");

  $ferramentas_curso=RetornaFerramentasCurso($sock);

  $ferramentas_curso=CompletaFerramentasCurso($sock,$lista_ferramentas,$ferramentas_curso);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
    /* 1 - Administracao  28 - Area restrita ao formador. */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,28)."</h4>\n");

    /*Voltar*/
    /* 509 - Voltar */
    echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("          <form><input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");

    echo("        </td>\n");
    echo("      </tr>\n");

    include("../tela2.php");

    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }
  
/*Forms*/
  echo("    <form action=\"acoes.php\" name=\"ferramentas\" method=\"post\">\n");
  echo("    <input type=\"hidden\" name=\"cod_curso\" value=".$cod_curso.">\n");

  echo("    <input type=\"hidden\" name=\"cod_ferramenta\" value=".$cod_ferramenta.">\n");
  echo("    <input type=\"hidden\" name=\"action\" value='escolherFerramentas'>\n");

  

  // P�gina Principal
  /* 1 - Administra��o */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1)."\n");
  /* 40 - Escolher Ferramentas do Curso */
  $cabecalho .= "- ".RetornaFraseDaLista($lista_frases, 40)."</h4>";
  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");


  /*Voltar*/			
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (geral)*/
  echo("                  <li><a href=\"administracao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;confirma=0\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\"  class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 45-Ferramenta */
  echo("                    <td width=\"15%\"><b>".RetornaFraseDaLista($lista_frases,45)."</b></td>\n");
  /* 46-Descri��o */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,46)."</b></td>\n");
  /* 44-Vis�vel por */
  echo("                    <td width=\"10%\"><b>".RetornaFraseDaLista($lista_frases,44)."</b></td>\n");
  echo("                  </tr>\n");
  
  $i=0;

  foreach ($ordem_ferramentas as $cod=>$linha)
  {
    if ($linha['cod_ferramenta']>0)
    {
      $cod_ferramenta=$linha['cod_ferramenta'];
        echo("                  <tr class=\"altColor".(($i++)%2)."\">\n");
        if ($cod_ferramenta==1 || $cod_ferramenta==16 || $cod_ferramenta==17){
          /* 47 - (Ferramenta Obrigatoria) */
          echo("                    <td>&nbsp;".RetornaFraseDaLista($lista_frases_ferramentas,$lista_ferramentas[$cod_ferramenta]['cod_texto_nome'])."&nbsp;<br />&nbsp;<font style=\"font-size:0.9em\" color=\"red\">".RetornaFraseDaLista($lista_frases,47)."</font>&nbsp;</td>\n");
        } else {
          echo("                    <td>&nbsp;".RetornaFraseDaLista($lista_frases_ferramentas,$lista_ferramentas[$cod_ferramenta]['cod_texto_nome'])."&nbsp;</td>\n");
        }

        echo("                    <td class=\"alLeft\" style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases_ferramentas,$lista_ferramentas[$cod_ferramenta]['cod_texto_descricao'])."</td>\n");
        echo("                    <td class=\"alLeft\">");

      if ($cod_ferramenta==1 || $cod_ferramenta==16 || $cod_ferramenta==17)
      {
        /* 41 - Todos */
        echo("&nbsp;<input type=\"radio\" value='A' name=\"status[".$cod_ferramenta."]\" checked>".RetornaFraseDaLista($lista_frases,41)."<br>");
      }
      else if (($cod_ferramenta!=22))
      {
        $status=$ferramentas_curso[$cod_ferramenta]['status'];
        /* 41 - Todos */
        echo("&nbsp;<input type=\"radio\" value='A' ".($status=='A'?"checked":"")." name=\"status[".$cod_ferramenta."]\" id=\"".$cod_ferramenta."_1\"><label for=\"".$cod_ferramenta."_1\">".RetornaFraseDaLista($lista_frases,41)."</label>&nbsp;<br>");
        /* 42 - Formador */
        echo("&nbsp;<input type=\"radio\" value='F' ".($status=='F'?"checked":"")." name=\"status[".$cod_ferramenta."]\" id=\"".$cod_ferramenta."_2\"><label for=\"".$cod_ferramenta."_2\">".RetornaFraseDaLista($lista_frases,42)."</label>&nbsp;<br>");
        /* 43 - Ningu�m */
        echo("&nbsp;<input type=\"radio\" value='D' ".($status=='D'?"checked":"")." name=\"status[".$cod_ferramenta."]\" id=\"".$cod_ferramenta."_3\"><label for=\"".$cod_ferramenta."_3\">".RetornaFraseDaLista($lista_frases,43)."</label>&nbsp;");
      }
      else
      {
        $status=$ferramentas_curso[$cod_ferramenta]['status'];

        /* 41 - Todos */
        echo("&nbsp;<input type=\"radio\" value='A' ".($status=='A'?"checked":"")." name=\"status[".$cod_ferramenta."]\" id=\"".$cod_ferramenta."_1\" ><label for=\"".$cod_ferramenta."_1\">".RetornaFraseDaLista($lista_frases,41)."</label>&nbsp;<br>");
        /* 42 - Formador */
        echo("&nbsp;<input type=\"radio\" value='F' ".($status=='F'?"checked":"")." name=\"status[".$cod_ferramenta."]\" id=\"".$cod_ferramenta."_2\" ><label for=\"".$cod_ferramenta."_2\">".RetornaFraseDaLista($lista_frases,42)."</label>&nbsp;<br>");

        /* 43 - Ningu�m */
        echo("&nbsp;<input type=\"radio\" value='D' ".($status=='D'?"checked":"")." name=\"status[".$cod_ferramenta."]\" id=\"".$cod_ferramenta."_3\" ><label for=\"".$cod_ferramenta."_3\">".RetornaFraseDaLista($lista_frases,43)."</label>&nbsp;");
      }
      echo("</td>\n");
      echo("                  </tr>\n");

    }
  }
  /*****************/
  /* Alterar Senha */
  echo("                  <tr class=\"altColor".(($i++)%2)."\">\n");
  /* 47 - (Ferramenta Obrigatoria) */
  echo("                    <td>&nbsp;".RetornaFraseDaLista($lista_frases_ferramentas,47)."&nbsp;<br>&nbsp;<font style=\"font-size:0.9em\" color=\"red\">".RetornaFraseDaLista($lista_frases,47)."</font>&nbsp;</td>\n");
  echo("                    <td>".RetornaFraseDaLista($lista_frases_ferramentas,42)."</td>\n");
  /* 41 - Todos */
  echo("                    <td class=\"alLeft\">&nbsp;<input type=\"radio\" value='A' name=\"status[-1]\" checked>".RetornaFraseDaLista($lista_frases,41)."</td>\n");
  echo("                  </tr>\n");

  /*****************/
  /* Administracao */
  echo("                  <tr class=\"altColor".(($i++)%2)."\">\n");
  /* 47 - (Ferramenta Obrigatoria) */
  echo("                    <td>&nbsp;".RetornaFraseDaLista($lista_frases_ferramentas,37)."&nbsp;<br />&nbsp;<font style=\"font-size:0.9em\" color=\"red\">".RetornaFraseDaLista($lista_frases,47)."</font>&nbsp;</td>\n");
  echo("                    <td style=\"padding-left:20px;\">".RetornaFraseDaLista($lista_frases_ferramentas,38)."</td>\n");
  /* 42 - Formador */
  echo("                    <td class=\"alLeft\">&nbsp;<input type=\"radio\" value='F' name=\"status[-2]\" checked>".RetornaFraseDaLista($lista_frases,42)."</td>\n");
  echo("                  </tr>\n");

  /***********/
  /* Suporte */
  echo("                  <tr class=\"altColor".(($i++)%2)."\">\n");
  /* 47 - (Ferramenta Obrigatria) */
  echo("                    <td>&nbsp;".RetornaFraseDaLista($lista_frases_ferramentas,39)."&nbsp;<br />&nbsp;<font style=\"font-size:0.9em\" color=\"red\">".RetornaFraseDaLista($lista_frases,47)."</font>&nbsp;</td>\n");
  echo("                    <td class=\"alLeft\" style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases_ferramentas,40)."</td>\n");
  /* 42 - Formador */
  echo("                    <td class=\"alLeft\">&nbsp;<input type=\"radio\" value='F' name=\"status[-3]\" checked>".RetornaFraseDaLista($lista_frases,42)."</td>\n");
  echo("                  </tr>\n");

  /********/
  /* Sair */
  echo("                  <tr class=\"altColor".(($i++)%2)."\">\n");
  /* 47 - (Ferramenta Obrigatria) */
  echo("                    <td>&nbsp;".RetornaFraseDaLista($lista_frases_ferramentas,43)."&nbsp;<br />&nbsp;<font style=\"font-size:0.9em\" color=\"red\">".RetornaFraseDaLista($lista_frases,47)."</font>&nbsp;</td>\n");
  echo("                    <td class=\"alLeft\" style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases_ferramentas,44)."</td>\n");
  /* 41 - Todos */
  echo("                    <td class=\"alLeft\">&nbsp;<input type=\"radio\" value='A' name=\"status[-4]\" checked>".RetornaFraseDaLista($lista_frases,41)."</td>\n");
  echo("                  </tr>\n");

  echo("                </table>\n");
  /* 48 - Escolher */
  echo("                <div align=\"right\"><input type=\"button\" class=\"input\" value='".RetornaFraseDaLista($lista_frases,48)."' onclick=\"document.ferramentas.submit();\"></div>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php"); 
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
