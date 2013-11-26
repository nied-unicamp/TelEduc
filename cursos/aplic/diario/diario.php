<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/diario/diario.php

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
  ARQUIVO : cursos/aplic/diario/diario.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("diario.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
  $objAjax->registerFunction("MudaTipoCompartilhamento");
  $objAjax->registerFunction("ApagaItensDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  $cod_ferramenta=14;
  $cod_ferramenta_ajuda=$cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("apagarItem", 36, 37);
  $feedbackObject->addAction("apagar_itens", 73, 0);
  
  /* Verifica se o usuario eh formador. */
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  /* Verifica se o usuario e visitante. */
  $usr_visitante = EVisitante($sock, $cod_curso, $cod_usuario);

  /* Se o codigo do usuario cujo diario sera exibido nao */
  /* estiver definido, atribui-lhe o cadigo do usuario   */
  /* que esta visualizando o diario, por padrao.         */
  if (!isset($cod_propriet))
    $cod_propriet = $cod_usuario;

  // verifica se eh dono do diario
  $dono_diario = VerificaDonoDiario ($sock, $cod_curso, $cod_usuario, $cod_propriet);

  /*
  ==================
  Fun��es JavaScript
  ==================
  */
  echo("    <script type=\"text/javascript\" src=\"../js-css/sorttable.js\"></script>\n");
  echo("    <script type=\"text/javascript\">\n");

  echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("      var versao = (navigator.appVersion.substring(0,3));\n");
  echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");

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

  echo("      var selected_item;\n");

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

  /* Iniciliza os layers. Se o usuario for o propriet�rio do di�rio ent�o */
  /* atribui layer_renomear � vari�vel lay_renomear.                      */
  echo("      function Iniciar()\n");
  echo("      {\n");
  if ($dono_diario)
  {
    echo("         cod_comp = getLayer('layer_comp');\n");
  }
  echo("        cod_novoitem = getLayer(\"novoitem\");\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");

  // Esconde o layer especificado por cod_layer.
  echo("      function EscondeLayer(cod_layer)\n");
  echo("      {\n");
  echo("        hideLayer(cod_layer);\n");
  echo("      }\n\n");

  /* Esconde todos os layers. Se o usuario for o propriet�rio do di�rio   */
  /* visualizado ent�o esconde o layer para renomear o item.              */
  echo("      function EscondeLayers()\n");
  echo("      {\n");
  if ($dono_diario)
  {
    echo("        hideLayer(cod_comp);\n");
  }
  echo("        hideLayer(cod_novoitem);\n");
  echo("      }\n\n");


  echo("      function MostraLayer(cod_layer, ajuste, ev){\n");
  echo("        EscondeLayers();\n");
  echo("        ev = ev || window.event;\n");
  echo("        if(ev.pageX || ev.pageY){\n");
  echo("          Xpos = ev.pageX;\n");
  echo("          Ypos = ev.pageY;\n");
  echo("        }else{\n");
  echo("          Xpos = ev.clientX + document.body.scrollLeft - document.body.clientLeft;\n");
  echo("          Ypos = ev.clientY + getPageScrollY();\n");
  echo("        }\n");
  echo("        moveLayerTo(cod_layer,Xpos-100,Ypos);\n");
  echo("        showLayer(cod_layer);\n");
  echo("      }\n\n");
  
  echo("      function OpenWindowPerfil(id)\n");
  echo("      {\n");
  echo("        window.open(\"../perfil/exibir_perfis.php?");
  echo("&cod_curso=".$cod_curso."&cod_aluno[]=\" + id, \"PerfilDisplay\",\"width=600,height=400,");
  echo("top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n\n");

  echo("      function VerificaNovoItemTitulo(textbox) {\n");
  echo("        texto=textbox.value;\n");
  echo("        if (texto==''){\n");
  echo("          // se nome for vazio, nao pode\n");
                /* 36 - O titulo nao pode ser vazio. */
  //echo("        alert(\"".RetornaFraseDaLista($lista_frases,36)."\");\n");
  echo("          alert(\"O titulo n�o pode ser vazio. \");\n");
  echo("          textbox.focus();\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        return true;\n");
  echo("      }\n\n");

  echo("      function AtualizaComp(js_cod_item, js_tipo_comp)\n");
  echo("      {\n");
  echo("        if ((isNav) && (!isMinNS6)) {\n");
  echo("          document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
  echo("          document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
  echo("          var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_P'));\n");
  echo("        } else {\n");
  echo("          if (isIE || ((isNav)&&(isMinNS6)) ){\n");
  echo("            document.form_comp.tipo_comp.value=js_tipo_comp;\n");
  echo("            document.form_comp.cod_item.value=js_cod_item;\n");
  echo("            var tipo_comp = new Array(document.getElementById('tipo_comp_T'), document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_P'));\n");
  echo("          } \n");
  echo("        }\n");
  echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif' />\";\n");
  echo("        if (js_tipo_comp=='F') { \n");
  echo("          tipo_comp[0].innerHTML=imagem; \n");
  echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
  echo("          tipo_comp[2].innerHTML=\"&nbsp;\";\n");
  echo("        } else if (js_tipo_comp=='T') {\n");
  echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
  echo("          tipo_comp[1].innerHTML=imagem;\n");
  echo("          tipo_comp[2].innerHTML=\"&nbsp;\";\n");
  echo("        } else{\n");
  echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
  echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
  echo("          tipo_comp[2].innerHTML=imagem;\n");
  echo("        }\n");
  echo("      }\n");

  echo("      function MudaSpanCompartilhamento(spanID,novoComp,tipoComp,js_cod_item)\n");
  echo("      {\n");
  echo("        spanElement = document.getElementById(spanID);\n");
  echo("        spanElement.innerHTML = novoComp;\n");
  echo("        spanElement.onclick = function(event) { AtualizaComp(js_cod_item, tipoComp);MostraLayer(cod_comp,100,event); }\n");
  echo("      }\n");

  echo("      function VerificaCheck(){\n");
  echo("        var i;\n");
  echo("        var j=0;\n");
  echo("        var cod_itens=document.getElementsByName('chkItem');\n");
  echo("        var Cabecalho = document.getElementById('checkMenu');\n");
  echo("        array_itens = new Array();\n");
  echo("        for (i=0; i < cod_itens.length; i++){\n");
  echo("          if (cod_itens[i].checked){\n");
  echo("            var item = cod_itens[i].id.split('_');\n");
  echo("            array_itens[j]=item[1];\n");
  echo("            j++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if (j == (cod_itens.length)) Cabecalho.checked=true;\n");
  echo("        else Cabecalho.checked=false;\n");
  echo("        if(j > 0){\n");
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
  echo("        for(i = 0; i < cod_itens.length; i++){\n");
  echo("          e = cod_itens[i];\n");
  echo("          e.checked = CabMarcado;\n");
  echo("        }\n");
  echo("        VerificaCheck();\n");
  echo("      }\n\n");
  
  echo("      function ExcluirSelecionados(){\n");
  /* 34 - Deseja realmente apagar esta anotação? Ela não poderá ser recuperada */
  echo("        if (confirm('".RetornaFraseDaLista($lista_frases,34)."')){\n");
  echo("          xajax_ApagaItensDinamic('".$cod_curso."', array_itens,'".$cod_ferramenta."', '".$cod_usuario."');\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function Recarregar(acao, atualizacao){\n");
  echo("        window.location='diario.php?&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_item=".$cod_item."&cod_propriet=".$cod_propriet."&origem=".$origem."&acao='+acao+'&atualizacao='+atualizacao;");
  echo("      }\n\n");

  echo("    </script>\n");

  $objAjax->printJavascript("../xajax_0.2.4/");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* 1 - Diario de Bordo */
  echo("          <h4>".RetornaFraseDaLista($lista_frases, 1)."</h4>\n");

   /* 509 - Voltar */
  echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a href=\"#\" onclick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("            <a href=\"#\" onclick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("            <a href=\"#\" onclick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("          </div>\n");


  echo("          <img alt=\"".RetornaFraseDaLista($lista_frases, 1)."\" src=\"../imgs/icPerfil.gif\" border=\"0\" />&nbsp;<a class=\"text\" href=\"#\" onclick=\"OpenWindowPerfil(".$cod_propriet.");return(false);\">".NomeUsuario($sock, $cod_propriet, $cod_curso)."</a>\n");

  //<!----------------- Tabelao ----------------->
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 3 - Atualizar */
  echo("                  <li><span onclick=\"window.location.reload();\">".RetornaFraseDaLista($lista_frases, 3)."</span></li>\n");

  /* Se o usuario for o proprietario do diario cria um link para a     */
  /* funcao de incluir anotacao.                                       */
  $status_curso=RetornaStatusCurso($sock,$cod_curso);

  if ($dono_diario)
  {
    /* 4 - Incluir nova anotacao */
    echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases, 4)."\" onclick=\"MostraLayer(cod_novoitem, 140,event);document.getElementById('titulo').focus(); document.getElementById('titulo').value=''\">".RetornaFraseDaLista($lista_frases, 4)."</span></li>\n");
  }

  /* 5 - Ver outros diarios */
  echo("                  <li><a href=\"ver_outros.php?&amp;cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=-1&amp;cod_propriet=".$cod_propriet."&amp;origem=diario\">".RetornaFraseDaLista($lista_frases, 5)."</a></li>\n");

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  

  /* <!----------------- Tabela Interna -----------------> */
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"sortable tabInterna\">\n");
  $lista_itens = RetornaItens ($sock, $cod_curso, $cod_usuario, $cod_propriet);
  $ultimo_acesso = PenultimoAcesso($sock,$cod_usuario,"");

  echo("                  <tr class=\"head\">\n");
  if($dono_diario){
    echo("                    <td width=\"2\" class=\"sorttable_nosort\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></td>\n");
  }
  /* 8 - Titulo */
  echo("                    <td class=\"colorfield\" style=\"cursor:pointer\" width=\"49%\">".RetornaFraseDaLista($lista_frases, 8)."</td>\n");
  /* 10 - Data */
  echo("                    <td class=\"colorfield\" style=\"cursor:pointer\" width=\"20%\" align=\"center\">".RetornaFraseDaLista($lista_frases, 10)."</td>\n");
  /* 57 - Modo de compartilhamento */
  echo("                    <td class=\"colorfield\" style=\"cursor:pointer\" width=\"10%\" align=\"center\">".RetornaFraseDaLista($lista_frases, 57)."</td>\n");
  /* 12 - Comentarios */
  echo("                    <td class=\"colorfield\" style=\"cursor:pointer\" width=\"20%\" align=\"center\">".RetornaFraseDaLista($lista_frases, 12)."</td>\n");
  echo("                  </tr>\n");

  // variavel que indica cor no gfield. varia entre 1 ou 2
  $cor = 1;

  // Monto uma pequena tabela para compartilhamentos
  // 60 - Totalmente compartilhado
  $frase_compartilhamento ['T'] = RetornaFraseDaLista ($lista_frases, 60);
  // 59 - Compartilhado com formadores
  $frase_compartilhamento ['F'] = RetornaFraseDaLista ($lista_frases, 59);
  // 58 - N�o compartilhado
  $frase_compartilhamento ['P'] = RetornaFraseDaLista ($lista_frases, 58);

  $lista_comentarios = RetornaListaComentarios ($sock, $cod_curso, $cod_usuario, $cod_propriet);

  $icone = "                     <img src=\"../imgs/icDiario.gif\" border=\"0\" alt=\"".RetornaFraseDaLista($lista_frases, 1)."\" />\n";

  if (!$lista_itens)
  {
    echo("                 <tr>\n");
    // 14 - Nao ha itens neste Diario.
    echo("                   <td colspan=\"5\">".RetornaFraseDaLista($lista_frases, 14)."</td>\n");
    echo("                 </tr>\n");
  }

  foreach ($lista_itens as $item)
  {
    $cod_item = $item ['cod_item'];
    $estilo = ( $item['data'] > $ultimo_acesso ? "novo" : "antigo");

    echo ("                 <tr>\n");
    if($dono_diario){

      echo("                    <td width=\"2%\"><input type=\"checkbox\" name=\"chkItem\" id=\"itm_".$cod_item."\" onclick='VerificaCheck();' value=\"".$cod_item."\" /></td>\n");
    }

    echo ("                   <td class=\"alLeft\">\n".$icone."<a class=\"$estilo\" href=\"ver_item.php?&amp;cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$cod_item."&amp;cod_propriet=".$cod_propriet."&amp;origem=diario\" >".$item ['titulo']."</a></td>\n");
    echo ("                   <td align=\"center\" class=\"$estilo\">".UnixTime2DataHora($item ['data'])."</td>\n");

    if ($dono_diario)
    {
      $comp_abre = "          <span id='span_".$cod_item."' class=\"link ".$estilo."\" onclick=\"AtualizaComp(".$cod_item.",'".$item['tipo_compartilhamento']."');MostraLayer(cod_comp,100,event);return(false);\">";
      $comp_fecha= "</span>";
    }
    else
    {
      $comp_abre = "";
      $comp_fecha= "";
    }

    echo ("                   <td align=\"center\">".$comp_abre.$frase_compartilhamento [ $item ['tipo_compartilhamento'] ] .$comp_fecha."</td>\n");

    $figuras = "";
    if ($lista_comentarios [ $cod_item ]['comentario_aluno'])
      $figuras.= "<span class=\"cAluno\">(c)&nbsp;</span>";
    if ($lista_comentarios [ $cod_item ]['comentario_formador'])
      $figuras.= "<span class=\"cForm\">(c)&nbsp;</span>";
    if ($lista_comentarios [ $cod_item ]['comentario_dono'])
      $figuras.= "<span class=\"cMim\">(c)&nbsp;</span>";
    if ($lista_comentarios [ $cod_item ]['novidade'])
      $figuras.= "<span class=\"cNovo\">*</span>";
    if ($figuras == "")
      $figuras = "&nbsp;";

    echo ("                   <td align=\"center\">".$figuras."</td>\n");

    echo ("                 </tr>\n");
      
    $cor = ( $cor % 2 ) + 1;
  }

  echo("                </table>\n");
  // 62 - Comentario de Aluno
  echo("                  <span class=\"cAluno\">(c)</span>&nbsp;".RetornaFraseDaLista($lista_frases,62)."\n");
  echo(" - ");
  // 63 - Comentario de Formador
  echo("               <span class=\"cForm\">(c)</span>&nbsp;".RetornaFraseDaLista($lista_frases,63)."\n");
  echo(" - ");
  if (!EVisitante($sock,$cod_curso,$cod_usuario))
  {
    // 53 - Comentario postados por mim
    echo("               <span class=\"cMim\">(c)</span>&nbsp;".RetornaFraseDaLista($lista_frases,53)."\n");
  }

  /* Fim do tabelao */

  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");

  if($dono_diario)
  {
    echo("          <ul>\n");
    echo("            <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">".RetornaFraseDaLista($lista_frases_geral,68)."</span></li>\n");
    echo("          </ul>\n");
  }
  
    echo("          <br />\n");
    /* 509 - voltar, 510 - topo */
    echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
  /* Se o usuario for o proprietario deste diario, cria o layer para */
  /* renomear algum item.                                            */
  if ($dono_diario)
  {

    // Mudar Compartilhamento
    echo("          <div class=\"popup\" id=\"layer_comp\" style=\"visibility:hidden  oncontextmenu:return(false);\">\n");
    echo("            <div class=\"posX\"><span onclick=\"EscondeLayer(cod_comp);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
    echo("            <div class=\"int_popup\">\n");
    foreach ($lista_itens as $item)
    {
      $cod_item = $item ['cod_item'];
    }
    echo("            <form name=\"form_comp\" id=\"form_comp\" action=\"\">\n");
    echo("              <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
    echo("              <input type=\"hidden\" name=\"cod_item\" value=\"\" />\n");
    echo("              <input type=\"hidden\" name=\"tipo_comp\" id=\"tipo_comp\" value=\"\" />\n");
    /* 71 - Compartilhamento alterado com sucesso. */
    echo("              <input type=\"hidden\" name=\"texto\" id=\"texto\" value=\"".RetornaFraseDaLista($lista_frases,71)."\" />\n");
    echo("              <ul class=\"ulPopup\">\n");
    echo("                <li onclick=\"document.getElementById('tipo_comp').value='T'; xajax_MudaTipoCompartilhamento(xajax.getFormValues('form_comp'), '".RetornaFraseDaLista($lista_frases,60)."'); EscondeLayers();\">\n");
    echo("                  <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
    /* 60 - Totalmente compartilhado */
    echo("                  <span>".RetornaFraseDaLista($lista_frases,60)."</span>\n");
    echo("                </li>\n");
    echo("                <li onclick=\"document.getElementById('tipo_comp').value='F'; xajax_MudaTipoCompartilhamento(xajax.getFormValues('form_comp'), '".RetornaFraseDaLista($lista_frases,59)."'); EscondeLayers();\">\n");
    echo("                  <span id=\"tipo_comp_T\" class=\"check\"></span>\n");
    /* 59 - Compartilhado com formadores */
    echo("                  <span>".RetornaFraseDaLista($lista_frases,59)."</span>\n");
    echo("                </li>\n");
    echo("                <li onclick=\"document.getElementById('tipo_comp').value='P'; xajax_MudaTipoCompartilhamento(xajax.getFormValues('form_comp'), '".RetornaFraseDaLista($lista_frases,58)."'); EscondeLayers();\">\n");
    echo("                  <span id=\"tipo_comp_P\" class=\"check\"></span>\n");
    /* 58 - Nao Compartilhado */
    echo("                  <span>".RetornaFraseDaLista($lista_frases,58)."</span>\n");
    echo("                </li>\n");
    echo("              </ul>\n");
    echo("            </form>\n");
    echo("            </div>\n");
    echo("          </div>\n");

  }

  /* Novo Item */
  echo("          <div id=\"novoitem\" class=\"popup\">\n");
  echo("            <div class=\"posX\"><span onclick=\"EscondeLayer(cod_novoitem);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("            <div class=\"int_popup\">\n");
  echo("              <form name=\"form_novo_item\" method=\"post\" action=\"acoes.php\" onsubmit='return (VerificaNovoItemTitulo(document.form_novo_item.titulo));'>\n");
  echo("              <div class=\"ulPopup\">\n");
  /* 180 - Digite o nome do item a ser criado aqui: */
  echo("                Digite o nome do item a ser criado aqui: <br />\n");

  echo("                <input class=\"input\" type=\"text\" name=\"titulo\" id=\"titulo\" value=\"".$titulo."\" maxlength=\"150\" /><br />\n");
  echo("                <input type=\"hidden\" name=\"tipo_compartilhamento\" value=\"T\" />\n");
  echo("                <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("                <input type=\"hidden\" name=\"cod_item\" value=\"".$cod_item."\" />\n");
  echo("                <input type=\"hidden\" name=\"cod_propriet\" value=\"".$cod_propriet."\" />\n");
  echo("                <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
  echo("                <input type=\"hidden\" name=\"acao\" value=\"novo_item\" />\n");
  /* 18 - Ok (gen) */
  echo("                <input type=\"submit\" id=\"ok_novoitem\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  /* 2 - Cancelar (gen) */
  echo("                &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onclick=\"EscondeLayer(cod_novoitem);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  echo("              </div>\n");
  echo("              </form>\n");
  echo("            </div>\n");
  echo("          </div>\n");

  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");

  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
