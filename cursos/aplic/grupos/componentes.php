<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/grupos/componentes.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
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

    Nied - Ncleo de Inform�ica Aplicada �Educa�o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/grupos/componentes.php
  ========================================================== */

/* C�igo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("grupos.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->registerFunction("MostraComponenteDinamic");
  $objAjax->registerFunction("MudarConfiguracaoDinamic");

  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  $cod_ferramenta=12;
  $cod_ferramenta_ajuda=$cod_ferramenta;
  $cod_pagina_ajuda=1;

  include("../topo_tela.php");


  /*Para evitar chamar a mesma função diversas vezes */
  $bool_grupos_fechados=GruposFechados($sock);


  /*
  ==================
  FUN�ES JAVASCRIPT
  ==================
  */

   if (!$SalvarEmArquivo)
  {
    echo("    <script type=\"text/javascript\">\n");

    echo("      function ImprimirRelatorio()\n");
    echo("      {\n");
    echo("        if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape')\n");
    echo("        {\n");
    echo("          self.print();\n");
    echo("        }\n");
    echo("        else\n");
    echo("        {\n");
    echo("          alert('Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <P> para imprimir.');\n");
    echo("        }\n");
    echo("      }\n");
    
    echo("    </script>\n");
  }
  
  echo("    <script type=\"text/javascript\">\n");

  echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n\n");

  echo("      if (isNav)\n");
  echo("      {\n");
  echo("        document.captureEvents(Event.MOUSEMOVE);\n");
  echo("      }\n");
  echo("      document.onmousemove = TrataMouse;\n\n");

  echo("      function TrataMouse(e)\n");
  echo("      {\n");
  echo("        Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("        Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("      }\n\n");

  echo("      function getPageScrollY()\n");
   echo("      {\n");
   echo("        if (isNav)\n");
   echo("          return(window.pageYOffset);\n");
   echo("        if (isIE){\n");
   echo("          if(document.documentElement.scrollLeft>=0){\n");
   echo("            return document.documentElement.scrollTop;\n");
   echo("          }else if(document.body.scrollLeft>=0){\n");
   echo("            return document.body.scrollTop;\n");
   echo("          }else{\n");
   echo("            return window.pageYOffset;\n");
   echo("          }\n");
   echo("        }\n");
   echo("      }\n");

  echo("      function AjustePosMenuIE()\n");
  echo("      {\n");
  echo("        if (isIE)\n");
  echo("          return(getPageScrollY());\n");
  echo("        else\n");
  echo("          return(0);\n");
  echo("      }\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  if(!EVisitante($sock,$cod_curso,$cod_usuario))
  {
    if (EFormador($sock,$cod_curso,$cod_usuario))
    {
    echo("        lay_conf = getLayer('layer_conf');\n");
    }
  }
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function EscondeLayer(cod_layer)\n");
  echo("      {\n");
  echo("        hideLayer(cod_layer);\n");
  echo("      }\n\n");

  echo("      function EscondeLayers()\n");
  echo("      {\n");
  /* Se estiver visualizando os f�uns dispon�eis ent� esconde os layers   */
  /* para cria�o de um novo f�um, para acesso � op�es (Ver, Configurar, */
  /* Renomear e Apagar) de cada f�um e para acesso as op�es (Editar, */
  /* Apagar, Ver Notas, Ver Atividades Entregues e Ver Atividades Pendentes) de avalia�o.                                                     */
  if(!EVisitante($sock,$cod_curso,$cod_usuario))
  {
    if (EFormador($sock,$cod_curso,$cod_usuario))
    {
      echo("        hideLayer(lay_conf);\n");
    }
  }
  echo("      }\n\n");

  echo("      function MostraLayer(cod_layer, ajuste){\n");
  echo("        EscondeLayers();\n");
  echo("        moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
  echo("        showLayer(cod_layer);\n");
  echo("      }\n\n");
  
  echo("      function OpenWindowPerfil(id)\n");
  echo("      {\n");
  echo("        window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\" + id, \"PerfilDisplay\",\"width=600,height=400,");
  echo("top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n\n");

  if(!EVisitante($sock,$cod_curso,$cod_usuario))
  {
    if (EFormador($sock,$cod_curso,$cod_usuario))
    {
      echo("      var statusConf =  \"".((GruposFechados($sock))?"T":"A")."\";\n");
      echo("      function Configurar()\n");
      echo("      {\n");
      echo("        document.getElementById('configuracao_A').innerHTML='&nbsp;';\n");
      echo("        document.getElementById('configuracao_T').innerHTML='&nbsp;';\n");
      echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif' alt='blue.gif'/>\"\n");
      echo("        document.getElementById('configuracao_'+statusConf).innerHTML=imagem;\n");
      echo("        MostraLayer(lay_conf, 0);\n");
      echo("      }\n\n");
    }
  }

  echo("      function MostrarComponentes(cod_usuario)\n");
  echo("      {\n");
  echo("        var trElementTmp = document.getElementById('tr_usuario_'+cod_usuario);\n");
  echo("        if((trElementTmp)&&(cod_usuario!=-1)){\n");
  echo("          if (navigator.appName==\"Microsoft Internet Explorer\"){\n");
  echo("            trElementTmp.style.display=\"block\";\n");
  echo("          }else{\n");
  echo("            trElementTmp.style.display=\"table-row\";\n");
  echo("          }\n");
  echo("          return;\n");
  echo("        }\n");
  echo("        var trElement = document.getElementById('tr_'+cod_usuario);\n");
  echo("        var tableElement = trElement.parentNode;\n");
  echo("        trElement=trElement.nextSibling;\n");
  echo("        if(document.getElementById('td_usuario_'+cod_usuario)) return;\n");
  echo("        newElement = document.createElement('tr');\n");
  echo("        newElement.setAttribute('id', 'tr_usuario_'+cod_usuario);\n");
  echo("        newTd = document.createElement('td');\n");
  echo("        newTd.colSpan = 2;\n");
  echo("        newTd.setAttribute('id', 'td_usuario_'+cod_usuario);\n");
  echo("        newSpan = document.createElement('span');\n");
  echo("        newSpan.setAttribute('id', 'span_usuario_'+cod_usuario);\n");
  echo("        newSpan.innerHTML='".RetornaFraseDaLista($lista_frases, 69).":';\n");
  echo("        newSpan.style.fontWeight='bold';\n");
  echo("        newBR = document.createElement('br');\n");
  echo("        newSpan.appendChild(newBR);\n");
  echo("        newBR = document.createElement('br');\n");
  echo("        newSpan.appendChild(newBR);\n");  
  echo("        newTd.appendChild(newSpan);\n");
  echo("        newSpan = document.createElement('span');\n");
  echo("        newSpan.setAttribute('id', 'span2_usuario_'+cod_usuario);\n");
  echo("        newTd.appendChild(newSpan);\n");
  echo("        newElement.appendChild(newTd);\n\n");
  echo("        newTd = document.createElement('td');\n");
  echo("        newTd.setAttribute('id', 'td_close_'+cod_usuario);\n\n");
  echo("        newSpan = document.createElement('span');\n");
  /* 13 (ger) - Fechar */
  echo("        newSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral, 13)."<br />';\n");
  echo("        newSpan.className='link';\n");
  echo("        newSpan.setAttribute('id', 'fechar_'+cod_usuario);\n");
  echo("        newSpan.onclick = function(){ FecharComponentes(cod_usuario); };\n");
  echo("        newTd.appendChild(newSpan);\n");
  echo("        newElement.appendChild(newTd);\n");
  echo("        tableElement.insertBefore(newElement, trElement);\n");
  echo("        document.getElementById('td_usuario_'+cod_usuario).align='left';\n");
  echo("        var frasesComponentes = new Array();\n");
  echo("        frasesComponentes[0] = '".RetornaFraseDaLista($lista_frases, 7)."';\n");
  echo("        frasesComponentes[1] = '".RetornaFraseDaLista($lista_frases, 26)."';\n");
  echo("        xajax_MostraComponenteDinamic(".$cod_curso.", cod_usuario, frasesComponentes);\n");
  echo("      }\n\n");

  echo("      function FecharComponentes(cod_usuario){\n");
  echo("          trElement = document.getElementById('tr_usuario_'+cod_usuario);\n");
  echo("        if(cod_usuario==-1){\n");
  echo("          tableElement = trElement.parentNode;\n");
  echo("          tableElement.removeChild(trElement);\n");
  echo("        }else{\n");
  echo("          trElement.style.display=\"none\";\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("    </script>\n");
  
  $objAjax->printJavascript("../xajax_0.2.4/");
  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if (EConvidado($sock, $cod_usuario, $cod_curso))
  {
    echo("          <br />\n");
    echo("          <br />\n");
    echo("          <br />\n");
    /* 1 - Grupos */
    $cabecalho = "<h4>".RetornaFraseDaLista($lista_frases, 1);
    /* 61 - �ea restrita a alunos e formadores */
    $cabecalho .= "  <b> - ".RetornaFraseDaLista($lista_frases, 61)."</b>";
    echo("          ".$cabecalho);
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();    
  }

  MarcaAcesso($sock,$cod_usuario,12);

  /* 1 - Grupos */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1));
  
  if ($bool_grupos_fechados)
  {
    /* 48 - Grupos já formados */
    $tmp=RetornaFraseDaLista($lista_frases,48);
  }
  else
  {
    /* 47 - Grupos em formação */
    $tmp=RetornaFraseDaLista($lista_frases,47);
  }

  echo(" - ".$tmp."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");


  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <!-- Botoes de Acao -->\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");

  /* 67 - Listar Grupos */
  echo("                  <li><span onclick=\"window.location='grupos.php?cod_curso=".$cod_curso."';\" >".RetornaFraseDaLista($lista_frases,67)."</span></li>\n");

  if(!EVisitante($sock,$cod_curso,$cod_usuario))
  {
    if (EFormador($sock,$cod_curso,$cod_usuario))
    {
      /* Formador: Permitir abertura e fechamento de grupos */
      /* 46 - Configurar Grupos */
      echo("                  <li><span onclick=\"Configurar();\">".RetornaFraseDaLista($lista_frases,46)."</span></li >");
    }
  }
   
  echo("                </ul>\n");
  echo("                <br /><br />\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\"  class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">");
  /* 82 - Lista de integrantes */
  echo("                    <td>&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases,82)."</td>\n");
  /* 83 - Número de grupos */
  echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases,83)."&nbsp;&nbsp;</td>\n");
  /* 70 Ver Perfil */
  echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases_geral,70)."</td>\n");
  echo("                  </tr>\n");

  $lista_users=RetornaTodosUsuarios($sock, $cod_curso);

  $num_users=count($lista_users);
  if ($num_users > 0){
    $icone = "<img src=\"../imgs/icPerfil.gif\" border=\"0\" alt=\"".RetornaFraseDaLista($lista_frases, 1)."\" />";

    $num_usuario=0;
    foreach ($lista_users as $cod => $linha)
    {
    
      if ($linha['tipo_usuario'] == 'A')
        /* 18 - Aluno */
        $tmp = RetornaFraseDaLista($lista_frases, 18);
      else if ($linha['tipo_usuario'] == 'F')
        /* 19 - Formador */
        $tmp = RetornaFraseDaLista($lista_frases, 19);
      else
        $tmp = "Erro";

      $onclick= "onclick=\"MostrarComponentes(".$linha['cod_usuario'].");\"";
      $classTd="class=\"link\"";
      $classTr ="";

      echo("                  <tr id=\"tr_".$linha['cod_usuario']."\" class=\"altColor".($num_usuario%2)." alLeft\"  >\n");
      echo("                    <td>&nbsp;&nbsp;".$icone." <span id=\"usuario_".$linha['cod_usuario']."\" ".$classTd." ".$onclick.">".$tmp." ".$linha['nome']."</span></td>\n");
      echo("                    <td align=\"center\">".$linha['num_grupos']."</td>\n");
      echo("                    <td width=\"10%\" align=\"center\" valign=\"top\" class=\"botao2\">\n");
      echo("                      <ul>\n");
      /* 70 - Ver Perfil */
      echo("                        <li><span onclick='OpenWindowPerfil(".$linha['cod_usuario'].")'>".RetornaFraseDaLista($lista_frases, 70)."</span></li>\n");
      echo("                      </ul>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
      $num_usuario++;
    }
  }

  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("      <td>\n");

  //div do Layer de alteração da configuração
  echo("    <div id='layer_conf' class=\"popup\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(lay_conf);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <form method=\"post\" id=\"formConfiguracao\" name=\"formConfiguracao\" action=\"\">\n");
  echo("          <input type=\"hidden\" name=\"cod_curso\" value=\"$cod_curso\" />\n");
  
  echo("          <input type=\"hidden\" name=\"cod_forum\" value=\"\" />\n");
  echo("          <input type=\"hidden\" name=\"nova_configuracao\" id=\"nova_relevancia\" value=\"\" />\n");
  echo("        </form>\n");
  echo("        <ul class=\"ulPopup\">\n");
  /* 79 - Permitir Alteração */
  echo("          <li onclick=\"xajax_MudarConfiguracaoDinamic(xajax.getFormValues('formConfiguracao'), 'A'); EscondeLayers();\">\n");
  echo("            <span class=\"check\" id=\"configuracao_A\"></span>\n");
  echo("            <span>".RetornaFraseDaLista($lista_frases, 79)."</span>\n");
  echo("          </li>\n");
  /* 80 - Não Permitir Alteração */
  echo("          <li onclick=\"xajax_MudarConfiguracaoDinamic(xajax.getFormValues('formConfiguracao'), 'T'); EscondeLayers();\">\n");
  echo("            <span class=\"check\" id=\"configuracao_T\"></span>\n");      
  echo("            <span>".RetornaFraseDaLista($lista_frases, 80)."</span>\n");
  echo("          </li>\n");
  echo("        </ul>\n");
  echo("      </div>\n");
  echo("    </div>\n\n");
  
  echo("      <script type=\"text/javascript\">\n");
  echo("        Iniciar();\n");
  echo("      </script>\n");
  echo("        </td>\n");
   
  echo("      </tr>\n");  


  include("../tela2.php");
  
  echo("  </body>");
  echo("</html>");
  Desconectar($sock);
?>
