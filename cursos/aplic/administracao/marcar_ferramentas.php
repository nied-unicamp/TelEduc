<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/marcar_ferramentas.php

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
  ARQUIVO : cursos/aplic/administracao/marcar_ferramentas.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta = 0;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda = 5;

  include("../topo_tela.php");

  Desconectar($sock);
  $sock=Conectar("");

  /* Obt�m todas as ferramentas */
  $lista_ferramentas = RetornaListaFerramentas($sock);
  /* Obt�m a ordem das ferramentas */
  $ordem_ferramentas = RetornaOrdemFerramentas($sock);
  /* Conta quantas ferramentas existem. */
  $total_ferramentas = count($lista_ferramentas);
  $lista_frases_ferramentas=RetornaListaDeFrases($sock,-4);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/javacrypt.js\" defer></script>\n");
  
  /*Funcao JavaScript*/

  echo("    <script type=\"text/javascript\">\n");
  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("        VerificaCheck();\n");
  echo("      }\n\n");

  echo("      function CancelaMarca()\n");
  echo("      {\n");
  echo("        document.frmMarcar.action = \"administracao.php?cod_curso=".$cod_curso."\";\n");
  echo("        document.frmMarcar.submit();\n");
  echo("      }\n\n");

  echo("      function CheckTodos(){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("        var cod_itens=document.getElementsByName('ferramentas[]');\n");
  echo("        for(i = 0; i < cod_itens.length; i++){\n");
  echo("          e = cod_itens[i];\n");
  echo("          e.checked = CabMarcado;\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function VerificaCheck(){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var checkPai = document.getElementById('checkMenu');\n");
  echo("        var cod_itens=document.getElementsByName('ferramentas[]');\n");
  echo("        for(i = 0; i < cod_itens.length; i++){\n");
  echo("          e = cod_itens[i];\n");
  echo("          if(!e.checked)\n");
  echo("            break;\n");
  echo("        }\n");
  echo("        if(i == cod_itens.length)\n");
  echo("          checkPai.checked = true;\n");
  echo("        else\n");
  echo("          checkPai.checked = false;\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal.php");

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
  echo("    <form name=\"frmMarcar\" method=\"post\" action=\"acoes.php\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=".$cod_curso.">\n");
  echo("      <input type=\"hidden\" name=\"time\" value=".time().">\n");
  echo("      <input type=\"hidden\" name=\"action\" value='marcarFerramentas'>\n");
  echo("      <input type=\"hidden\" name=\"cod_ferramenta\" value=".$cod_ferramenta.">\n");
  // P�gina Principal
  /* 1 - Administra��o */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1));
   /* 141 - Marcar Ferramentas */
  $cabecalho .= (" - ".RetornaFraseDaLista($lista_frases, 141)."</h4>\n");
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
  echo("                  <tr class=\"head alLeft\">\n");
  /* 142 - Selecione as ferramentas que devem aparecer destacadas no menu  */
  /* 143 - (a ferramenta ficará escrita em vermelho): */
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,142).RetornaFraseDaLista($lista_frases, 143)."</td>\n");
  echo("                  </tr>\n"); 
  echo("                  <tr class=\"head01\">\n");
  echo("                    <td width=\"2%\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></td>\n");
  /* 45-Ferramenta */
  echo("                    <td align=\"left\" colspan=\"3\"><b>".RetornaFraseDaLista($lista_frases,45)."</b></td>\n");
  echo("                  </tr>\n");

  /* Lista as ferramentas dispon�veis no curso, ou seja, que foram selecionadas na */
  /* ferramenta 'escolher ferramentas'.                                            */
  $ferramentas_curso = RetornaFerramentasCurso($sock);
  /* Obt�m as ferramentas que est�o marcadas.                                      */
  $ferramentas_marcadas = RetornaFerramentasMarcadas($sock);
  /* Conta quantas ferramentas est�o marcadas.                                     */
  $total_marcadas = count($ferramentas_marcadas);

  $k=0;
  $total_fer_ordenadas = count($ordem_ferramentas);	
  for($i=0;$i<$total_fer_ordenadas;)
  {
    for($count=0;$count<2;$count++)		
    {
      $cod_ferramenta = $ordem_ferramentas[$i]['cod_ferramenta'];
      if($cod_ferramenta > 0)
        $status = $ferramentas_curso[$cod_ferramenta]['status'];

      /* Se h� ferramentas e se o status dela n�o for 'Ningu�m'.                    */
      if (($cod_ferramenta > 0) && ($status != 'D'))
      {
        for ($j = 0; (($j < $total_marcadas) && ($ferramentas_marcadas[$j]['cod_ferramenta'] != $cod_ferramenta)); $j++)
        {
          # Apenas incrementa o valor
        }
        if ($cod_ferramenta!=43 || $cod_curso==151)
        {
          if($k==0)
            echo("                  <tr>\n");
          echo("                    <td width=\"2%\"><input onclick=\"VerificaCheck();\" type=\"checkbox\" name=\"ferramentas[]\" value=".$cod_ferramenta);
          echo(($ferramentas_marcadas[$cod_ferramenta] ? " checked" : "")."></td>\n");
          echo("                    <td align=\"left\">".RetornaFraseDaLista($lista_frases_ferramentas,$lista_ferramentas[$cod_ferramenta]['cod_texto_nome'])."</td>\n");
          $k++;
          if($k==2)
          {	
            echo("                  </tr>\n");
            $k=0;
          }	
        }
      }   
      $i++;	
    }
  }

  if($k != 0)
  {
    echo("<td colspan=\"2\"></td>");
    echo("                  </tr>\n");
  }
  echo("                </table>\n");
  /* 18 - Ok (gen)*/
  echo("                <div align=\"right\"><input type=\"button\" class=\"input\" value='".RetornaFraseDaLista($lista_frases_geral,18)."' onclick=\"document.frmMarcar.submit();\"></div>\n");
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
