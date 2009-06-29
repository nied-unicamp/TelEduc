<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/compartilhar_ferramentas.php

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

/*=================================================================
  ARQUIVO : cursos/aplic/administracao/compartilhar_ferramentas.php
  ================================================================= */


  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");
  
  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda = 1;

  include("../topo_tela.php");
 
  Desconectar($sock);

  $sock=Conectar("");
  $lista_ferramentas = RetornaListaFerramentas($sock);
  $lista_frases=RetornaListaDeFrases($sock,0);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  $lista_frases_ferramentas=RetornaListaDeFrases($sock,-4);

  /*obtem as ferramentas q jah estao compartilhadas*/
  $lista_compart = RetornaFerramCompartilhadas($sock, $cod_curso);  

  Desconectar($sock);
  
  $sock=Conectar($cod_curso);
  
  /*Obt�m ferramentas dispon�veis para compartilhamento(cod = 3, 4, 5 ou 7) <- Fazer uma funcao em administracao.inc*/
  /*tem como padronizar isso? armazenar em algum lugar a lista de ferramentas compartilhaveis(tahcerto?)*/
  $lista_ferramentas_disp = RetornaFerramCompartilhaveis($sock);

  
  /*Funcao JavaScript*/
  echo("    <script type=\"text/javascript\" language=\"JavaScript\">\n\n");
  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("        VerificaCheck();\n");
  echo("      }\n\n");
  echo("      function BtnCancelClick()\n");
  echo("      {\n");
  echo("        document.frmComp.action = \"administracao.php?cod_curso=".$cod_curso."\";\n");
  echo("        document.frmComp.submit();\n");
  echo("      }\n\n");

  echo("      function CheckTodos(){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("        var cod_itens=document.getElementsByName('ferr_comp[]');\n");
  echo("        for(i = 0; i < cod_itens.length; i++){\n");
  echo("          e = cod_itens[i];\n");
  echo("          e.checked = CabMarcado;\n");
  echo("        }\n");
  echo("      }\n\n");
  
  echo("      function VerificaCheck(){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var checkPai = document.getElementById('checkMenu');\n");
  echo("        var cod_itens=document.getElementsByName('ferr_comp[]');\n");
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
  	/* 1 - Administracao  297 - Area restrita ao formador. */
  	echo("<h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,28)."</h4>\n");
	
    /*Voltar*/
    echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("<form><input class=\"input\" type=button value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");

    Desconectar($sock);
    exit();
  }
  
/*Forms*/
  echo("    <form name=\"frmComp\" method=\"post\" action=\"acoes.php\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=".$cod_curso.">\n");
  echo("      <input type=\"hidden\" name=\"cod_ferramenta\" value=".$cod_ferramenta.">\n");
  echo("      <input type=\"hidden\" name=\"action\" value='compartilharFerramentas'>\n");

  // P�gina Principal
  /* 1 - Administra��o */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1));
  /* 202 - Compartilhar Ferramentas */
  $cabecalho .= (" - ".RetornaFraseDaLista($lista_frases, 202)."</h4>\n");
  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/      
  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");
  
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (geral)*/
  echo("                  <li><a href=\"administracao.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;confirma=0\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\"  class=\"tabInterna\">\n");
  echo("                  <tr class=\"head alLeft\">\n");
  /* 203 - Selecione as ferramentas que devem ser compartilhadas: */
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,203)."</td>\n");
  echo("                  </tr>\n"); 
  echo("                  <tr class=\"head01\">\n");
  echo("                    <td width=\"2%\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></td>\n");
  /* 45-Ferramenta */
  echo("                    <td align=\"left\" colspan=\"3\"><b>".RetornaFraseDaLista($lista_frases,45)."</b></td>\n");
  echo("                  </tr>\n"); 
  
  /* Lista as ferramentas que podem ser compartilhadas */

  $total_fer_comp = count($lista_ferramentas_disp); 
  $k=0;
  for($i=0;$i<$total_fer_comp;)
  {
    for($count=0;$count<1;$count++)   
    {
      if($lista_ferramentas_disp[$i]['status'] != 'D')
      {
        $compartilhada = false;
        $nome_ferramenta = RetornaFraseDaLista($lista_frases_ferramentas, $lista_ferramentas[$lista_ferramentas_disp[$i]['cod_ferramenta']]['cod_texto_nome']);
        $j=0;
        /*verifica se ferramenta ja era compartilhada*/
        while($j<count($lista_compart)&&!$compartilhada)
        {
          if(($lista_compart != NULL)&&(in_array($lista_ferramentas_disp[$i]['cod_ferramenta'], $lista_compart[$j], false)))
            $compartilhada = true;
          $j++;
        }
        if($k==0)
            echo("                  <tr>\n");
        echo("                    <td width=\"2%\">");
        if($compartilhada) /*se ja era compartilhada a check vem marcada*/
          echo("<input onclick=\"VerificaCheck();\" type=\"checkbox\" name=\"ferr_comp[]\" value='".$lista_ferramentas_disp[$i]['cod_ferramenta']."'checked>");
        else
          echo("<input onclick=\"VerificaCheck();\" type=\"checkbox\" name=\"ferr_comp[]\" value='".$lista_ferramentas_disp[$i]['cod_ferramenta']."'>");
        echo("</td>\n");
        echo("                    <td width=\"48%\" align=\"left\">".$nome_ferramenta."</td>\n");
        $k++;
        if($k==2)
          {	
            echo("                  </tr>\n");
            $k=0;
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
  echo("                <div align=\"right\"><input type=\"button\" class=\"input\" value='".RetornaFraseDaLista($lista_frases_geral,18)."' onclick=\"document.frmComp.submit();\"></div>\n");
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
