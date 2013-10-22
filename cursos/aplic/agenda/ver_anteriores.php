<?php
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
  
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"

  $feedbackObject->addAction("apagarSelecionados", 101, 0);
  $feedbackObject->addAction("apagarItem", 101, 0);


  $data_acesso=PenultimoAcesso($sock,$cod_usuario,"");

  /* Fun��es JavaScript */
  echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\">\n\n");
  
  echo("      function VerificaCheck(){\n");
  echo("        var i;\n");
  echo("        var j=0;\n");
  echo("        var cod_itens=document.getElementsByName('chkItem');\n");
  echo("        var Cabecalho = document.getElementById('checkMenu');\n");
  echo("        array_itens = new Array();\n");
  echo("        for (i=0; i<cod_itens.length; i++){\n");
  echo("          if (cod_itens[i].checked){\n");
  echo("            var item = cod_itens[i].id.split('_');\n");
  echo("            array_itens[j]=item[1];\n");
  echo("            j++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if ((j)==(cod_itens.length)) Cabecalho.checked=true;\n");
  echo("        else Cabecalho.checked=false;\n");
  echo("        if((j)>0){\n");
  echo("          document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
  echo("          document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(); };\n");
  echo("        }else{\n");
  echo("          document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
  echo("          document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function CheckTodos(){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("        var cod_itens=document.getElementsByName('chkItem');\n");
  echo("        if (cod_itens.length == 0){\n");
  echo("          return;\n");
  echo("        }\n");
  echo("        for(i = 0; i < cod_itens.length; i++){\n");
  echo("          e = cod_itens[i];\n");
  echo("          e.checked = CabMarcado;\n");
  echo("        }\n");
  echo("        VerificaCheck();\n");
  echo("      }\n\n");

  echo("      function ExcluirSelecionados(){\n");
  echo("        if (TemCertezaApagar()){\n");
  echo("          document.getElementById('cod_itens_form').value=array_itens;\n");
  echo("          document.form_dados.action='acoes_linha.php';\n");
  echo("          document.form_dados.method='POST';\n");
  echo("          document.getElementById('acao_form').value='apagarSelecionados';\n");
  echo("          document.form_dados.submit();\n");
  echo("        }\n");
  echo("      }\n\n");
  
  echo("      function TemCertezaApagar()\n");
  echo("      {\n");
  /* 29 - Voc� tem certeza de que deseja apagar esta agenda? */
  /* 30 - (n�o haver� como recuper�-la) */
  echo("              return(confirm(\"".RetornaFraseDaLista($lista_frases,29)."\\n".RetornaFraseDaLista($lista_frases,30)."\"));\n");
  echo("      }\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 1 - Agenda */
  echo("          <h4>".RetornaFraseDaLista($lista_frases, 1));
  echo(" - ".RetornaFraseDaLista($lista_frases, 2));
  echo("</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/
  /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
      
  /* Tabela Externa */
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /*8 - Voltar para Agenda Atual*/
  echo("                      <li><a href=\"agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=1'\">".RetornaFraseDaLista($lista_frases, 8)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  /* Tabela Interna */
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onClick=\"CheckTodos();\" /></td>\n");
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
      echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"chkItem\" id=\"itm_".$linha_item['cod_item']."\" onclick=\"VerificaCheck();\" value=\"".$linha_item['cod_item']."\" /></td>\n");
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
  
  /* 68 - Excluir Selecionados (ger)*/ 
  echo("                <ul>\n");
  echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">".RetornaFraseDaLista($lista_frases_geral,68)."</span></li>\n");
  echo("                </ul>\n");

  /*Fim tabela externa*/
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  include("../tela2.php");

  echo("    <form name=\"form_dados\" action=\"\" id=\"form_dados\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\" id=\"cod_curso\"      value=\"".$cod_curso."\" />\n");
  echo("      <input type=\"hidden\" name=\"cod_item\"  id=\"cod_item\"       value=\"\" />\n");
  echo("      <input type=\"hidden\" name=\"acao\"      id=\"acao_form\"      value=\"\" />\n");
  echo("      <input type=\"hidden\" name=\"cod_itens\" id=\"cod_itens_form\" value=\"\" />\n");
  echo("      <input type=\"hidden\" name=\"origem\"    value=\"ver_anteriores\"");
  echo("    </form>\n");

  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);

?>
 
