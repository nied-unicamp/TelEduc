<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/selecionar_categoria.php

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
  ARQUIVO : administracao/selecionar_categoria.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  require_once("../cursos/aplic/xajax_0.2.4/xajax.inc.php");

  VerificaAutenticacaoAdministracao();

  $objAjax = new xajax();
  $objAjax->registerFunction("TrocaCategoriaDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);
  $lista_frases_pag_inicial=RetornaListaDeFrases($sock,-3);
  $cursos=RetornaCursos();
  $total_cursos=count($cursos);
  
  /* Nmero de mensagens exibidas por p�ina.             */
  if (!isset($cursos_por_pag))
    $cursos_por_pag = 10;

  /* Calcula o nmero de p�inas geradas.                                       */
  $total_pag = ceil($total_cursos / $cursos_por_pag);  

  /* Se a p�ina atual n� estiver setada ent�, por padr�, atribui-lhe o valor 1. */
  /* Se estiver setada, verifica se a p�ina �maior que o total de p�inas, se for */
  /* atribui o valor de $total_pag �$pag_corrente.                                    */
  if ((!isset($pag_corrente))or($pag_corrente=='')or($pag_corrente==0))
    $pag_corrente = 1;
  else 
    $pag_corrente = min($pag_corrente, $total_pag);

  /* Inicio do JavaScript */
  echo("    <script type=\"text/javascript\">\n");
  // FraseAColocar
//  echo("function RespostaUsuario(){\n");
  /* 148 - Categoria alterada com sucesso! */
//  echo("      alert(\"".RetornaFraseDaLista($lista_frases_adm, 529)."\");\n");
//  echo("}\n");

  echo("      var pag_atual = ".$pag_corrente.";\n\n");
  echo("      var total_pag = ".$total_pag.";\n\n");
  
  echo("      function Iniciar() {\n");
  echo("	startList();\n");
  echo("        ExibeMsgPagina(".$pag_corrente.");\n");
  echo("      }\n");

  echo("      function ExibeMsgPagina(pagina){\n");
  echo("        if (pagina==-1) return;\n");
  echo("        tabela = document.getElementById('tabelaMsgs');\n");
  echo("        if(!tabela) return;\n");
  echo("        inicio = ((pag_atual-1)*".$cursos_por_pag.")+2;\n");
  echo("        final = ((pag_atual)*".$cursos_por_pag.")+2;\n");
  echo("        for (i=inicio; i <= final; i++){\n");
  echo("          if (!tabela.rows[i]) break;\n");
  echo("          tabela.rows[i].style.display=\"none\";\n");
  echo("        }\n");
  echo("        var browser=navigator.appName;\n\n");
  echo("        inicio = ((pagina-1)*".$cursos_por_pag.")+2;\n");
  echo("        final = (pagina)*".$cursos_por_pag.";\n");
  echo("        iTmp = 0; contador=0;\n");
  echo("        for (i=inicio; i < final+2; i++){\n");
  echo("          if (!tabela.rows[i]){ iTmp=1; break;}\n");
  echo("            if (browser==\"Microsoft Internet Explorer\")\n");
  echo("              tabela.rows[i].style.display=\"block\";\n");
  echo("            else\n");
  echo("              tabela.rows[i].style.display=\"table-row\";\n");  
  echo("        }\n\n");
  echo("        document.getElementById('prim_msg_index').innerHTML=inicio-1;\n"); 
  echo("        if (!iTmp) document.getElementById('ult_msg_index').innerHTML=final;\n"); 
  echo("        else document.getElementById('ult_msg_index').innerHTML=i-3;\n\n"); 
  echo("        if (browser==\"Microsoft Internet Explorer\")\n");
  echo("          tabela.rows[tabela.rows.length-1].style.display=\"block\";\n");
  echo("        else\n");
  echo("          tabela.rows[tabela.rows.length-1].style.display=\"table-row\";\n");
  echo("        pag_atual=pagina;\n\n");
  echo("        if (pag_atual != 1){\n");
  echo("          document.getElementById('paginacao_first').onclick = function(){ ExibeMsgPagina(1); };\n");
  echo("          document.getElementById('paginacao_first').className = \"link\";\n");
  echo("          document.getElementById('paginacao_back').onclick = function(){ ExibeMsgPagina(pag_atual-1); };\n");
  echo("          document.getElementById('paginacao_back').className = \"link\";\n");
  echo("        }else{\n");
  echo("         document.getElementById('paginacao_first').onclick = function(){};\n");
  echo("         document.getElementById('paginacao_first').className = \"\";\n");
  echo("         document.getElementById('paginacao_back').onclick = function(){};\n");
  echo("         document.getElementById('paginacao_back').className = \"\";\n");
  echo("        }\n");
  echo("        document.getElementById('paginacao_first').innerHTML = \"&lt;&lt;\";\n");
  echo("        document.getElementById('paginacao_back').innerHTML = \"&lt;\";\n");
  echo("        inicio = pag_atual-2;\n");
  echo("        if (inicio<1) inicio=1;\n");
  echo("        fim = pag_atual+2;\n");
  echo("        if (fim>total_pag) fim=total_pag;\n");
  echo("        var controle=1;\n");
  echo("        var vetor= new Array();\n");
  echo("        for (j=inicio; j <= fim; j++){\n");
  echo("          // A página atual Não é exibida com link.\n");
  echo("          if (j == pag_atual){\n");
  echo("             document.getElementById('paginacao_'+controle).innerHTML='<b>['+j+']<\/b>';\n");
  echo("             document.getElementById('paginacao_'+controle).className='';\n");
  echo("             vetor[controle] = -1;\n");
  echo("          }else{\n");
  echo("             document.getElementById('paginacao_'+controle).innerHTML=j;\n");
  echo("             document.getElementById('paginacao_'+controle).className='link';\n");
  echo("             vetor[controle]=j;\n");
  echo("          }\n");
  echo("          controle++;\n");
  echo("        }\n");
  echo("        while (controle<=5){\n");
  echo("          document.getElementById('paginacao_'+controle).innerHTML='';\n");
  echo("          document.getElementById('paginacao_'+controle).className='';\n");
  echo("          document.getElementById('paginacao_'+controle).onclick= function() { };\n");
  echo("          controle++;\n");
  echo("        }\n");
  echo("        document.getElementById('paginacao_1').onclick=function(){ ExibeMsgPagina(vetor[1]); };\n");
  echo("        document.getElementById('paginacao_2').onclick=function(){ ExibeMsgPagina(vetor[2]); };\n");
  echo("        document.getElementById('paginacao_3').onclick=function(){ ExibeMsgPagina(vetor[3]); };\n");
  echo("        document.getElementById('paginacao_4').onclick=function(){ ExibeMsgPagina(vetor[4]); };\n");
  echo("        document.getElementById('paginacao_5').onclick=function(){ ExibeMsgPagina(vetor[5]); };\n\n");
  echo("        /* Se a página atual Não for a última página então cria um   \n");
  echo("           link para a próxima página */\n");
  echo("        if (pag_atual != total_pag){\n");
  echo("         document.getElementById('paginacao_fwd').onclick = function(){ ExibeMsgPagina(pag_atual+1); };\n");
  echo("         document.getElementById('paginacao_fwd').className = \"link\";\n");
  echo("         document.getElementById('paginacao_last').onclick = function(){ ExibeMsgPagina(".$total_pag."); };\n");
  echo("         document.getElementById('paginacao_last').className = \"link\";\n");
  echo("        }\n");
  echo("        else{\n");
  echo("         document.getElementById('paginacao_fwd').onclick = function(){};\n");
  echo("         document.getElementById('paginacao_fwd').className = \"\";\n");
  echo("         document.getElementById('paginacao_last').onclick = function(){};\n");
  echo("         document.getElementById('paginacao_last').className = \"\";\n");
  echo("        }\n");
  echo("        document.getElementById('paginacao_fwd').innerHTML = \"&gt;\";\n");
  echo("        document.getElementById('paginacao_last').innerHTML = \"&gt;&gt;\";\n");
  echo("      }\n\n");
  echo("    </script>\n");

  $objAjax->printJavascript("../cursos/aplic/xajax_0.2.4/");

  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 131 - Selecionar Categoria dos Cursos */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,131)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <form name=\"frmConfirmar\" action='selecionar_categoria2.php?acao=novo' method=\"get\">\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\" id=\"tabelaMsgs\">\n");
  echo("                  <tr class=\"head\">\n");
  /*133 - Selecione abaixo as categorias a que devem... */
  echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,133)."</td>\n");
  echo("                  </tr>\n");

  $categ=RetornaCategorias();

  if (count($categ)==0 || $categ == "") 
  {
    echo("                  <tr>\n");
    echo("                    <td colspan=\"2\">\n");
    echo("                      ".RetornaFraseDaLista($lista_frases,132)."\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
  }
  else 
  {
    echo("                  <tr class=\"head01\">\n");
    echo("                    <td style=\"width:70%;text-align:left;\">\n");
    /* 157 - Curso     */
    echo("                      ".RetornaFraseDaLista($lista_frases,157));
    echo("(<span id=\"prim_msg_index\"></span>");
    /* 515 - a             */
    echo(" ".RetornaFraseDaLista($lista_frases,515)."&nbsp;");
    /* 516 - de            */
    echo("<span id=\"ult_msg_index\"></span> ".RetornaFraseDaLista($lista_frases,516)." ");
    echo(($total_cursos).")\n");
    echo("                    </td>\n");
    /*517 - Selecione a Categoria desejada */
    echo("                    <td>".RetornaFraseDaLista($lista_frases,517)."</td>\n");
    echo("                  </tr>\n");

    $num_cursos_pag=0;
    $num_cursos = 0;
    $num_pagina = 1;
    $i=0;

    if (count($cursos)>0)
    {
      foreach ($cursos as $cod_curso => $linha)
      {

        if($num_cursos_pag == $cursos_por_pag)
        {
          $num_pagina++;
          $num_cursos_pag = 0;
        }

        if ($num_pagina == $pag_corrente)
          $style = "";
        else 
          $style = "display:none;";

        echo("                  <tr class=\"altColor".$i%(2)."\" style=\"".$style."\">\n");
        echo("                    <td style=\"text-align:left;\">\n");
        echo("                      <span id=\"span_".$cod_curso."\" >".$linha['nome_curso']."</span>\n");
        echo("                    </td>\n");
        echo("                    <td>\n");
        echo("                      <select name=\"codigo_pasta[".$cod_curso."]\" class=\"input\" onchange=\"xajax_TrocaCategoriaDinamic(".$cod_curso.",this.value,'".RetornaFraseDaLista($lista_frases_adm, 529)."');\">\n");
        echo("                        <option value=NULL>".RetornaFraseDaLista($lista_frases_pag_inicial,115)."</option>\n");
        foreach ($categ as $cod_pasta => $pasta)
        {
          if($linha['cod_pasta'] == $cod_pasta)
            $flag = "selected";
          else
            $flag = "";

          echo("                        <option value=".$cod_pasta." ".$flag.">".$pasta."</option>\n");
        }
        echo("                      </select>\n");
        echo("                    </td>\n");
        echo("                  </tr>\n");

        $num_cursos++;
        $num_cursos_pag++;
        $i++;
      }
      echo("                  <tr>\n");  
      echo("                    <td colspan=\"5\" align=\"right\">\n");
      echo("                      <span id=\"paginacao_first\"></span> <span id=\"paginacao_back\"></span>\n");
      $controle=1;
      while ($controle<=5){
        echo("                      <span id=\"paginacao_".$controle."\"></span>\n");
        $controle++;
      }
      echo("                      <span id=\"paginacao_fwd\"></span> <span id=\"paginacao_last\"></span>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
      echo("                </table>\n");
      echo("              </td>\n");
      echo("            </tr>\n");
      echo("          </table>\n");
    }
    else
    {
      echo("                  <tr>\n");
      /* 118 - Nenhum curso dispon�vel */
      echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,118)."</td>\n");
      echo("                  </tr>\n");
      echo("                </table>\n");
      echo("              </td>\n");
      echo("            </tr>\n");
      echo("          </table>\n");
    }
  }

  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>