<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/agenda/ver_anteriores.php

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
  ARQUIVO : cursos/aplic/agenda/ver_anteriores.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("agenda.inc");

  $cod_ferramenta=1;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=5;

  include("../topo_tela.php");

  $data_acesso=PenultimoAcesso($sock,$cod_usuario,"");

  /* Fun��es JavaScript */
  echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n\n");		

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 1 - Agenda */
  echo("          <h4>".RetornaFraseDaLista($lista_frases, 1));
  echo(" - ".RetornaFraseDaLista($lista_frases, 2));
  echo("</h4>");			

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/			
  echo("          <span class=\"btsNav\" onClick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");
    
  /* Tabela Externa */
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");		
  /*8 - Voltar para Agenda Atual*/
  echo("                      <li><a href=\"agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=1'\">".RetornaFraseDaLista($lista_frases, 8)."</a></li>\n");
  echo("                </ul>\n");	
  echo("        	</td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  /* Tabela Interna */	
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /*1 - Agenda */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,1)."</td>\n");
  /*7 - Data */
  echo("                    <td align=\"center\" width=\"15%\">".RetornaFraseDaLista($lista_frases,7)."</td>\n");		
  echo("                  </tr>\n");
  /* Conteudo */
   
  $lista_agendas=RetornaItensHistorico($sock);

  if ((count($lista_agendas)>0)&&($lista_agendas != null))
  {
    foreach ($lista_agendas as $cod => $linha_item)
    {
      $data=UnixTime2Data($linha_item['data']);
      if ($data_acesso<$linha_item['data'])
      {
        $marcaib="<b>";
        $marcafb="</b>";
      }
      else
      {
        $marcaib="";
        $marcafb="";
      }
      if ($linha_item['status']=="E")
      {
        $data="<span class=\"link\" onclick=\"window.open('em_edicao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_editar','EmEdicao','width=600,height=280,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">".RetornaFraseDaLista($lista_frases,43)."</span>";
        $titulo=$linha_item['titulo'];
      }
      else 
      {
	$titulo="<a id=\"tit_".$linha_item['cod_item']."\" href=\"ver_linha.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_anteriores\">".$linha_item['titulo']."</a>";
      }

      $icone="<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" /> ";
      echo("                  <tr>\n");
      echo("                    <td align=\"left\">".$icone.$titulo."</td>\n");
      echo("                    <td align=\"center\">".$marcaib.$data.$marcafb."</td>\n");
      echo("                  </tr>\n");
    }
  } 
  else
  { 
    /* 90 - Nao ha agendas anteriores. */
    echo("                  <tr>\n");	
    echo("                    <td colspan=\"5\">".RetornaFraseDaLista($lista_frases,90)."</td>\n");
    echo("                  </tr>\n");	
  }
  /*Fim tabela interna*/		
  echo("                </table>\n");
    
  /*Fim tabela externa*/
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("    	  </table>\n");
  include("../tela2.php");	
  echo("  </body>\n");			
  echo("</html>\n");	
  Desconectar($sock);

?>
 
