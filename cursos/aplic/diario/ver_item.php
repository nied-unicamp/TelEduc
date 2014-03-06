<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/diario/ver_item.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/diario/ver_item.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("diario.inc");

  /**************** ajax ****************/

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das fun??es em PHP que voc? quer chamar atrav?s do xajax
  $objAjax->register(XAJAX_FUNCTION,"MudaTipoCompartilhamento");
  $objAjax->register(XAJAX_FUNCTION,"EditarTexto");
  $objAjax->register(XAJAX_FUNCTION,"EditarTitulo");
  $objAjax->register(XAJAX_FUNCTION,"DecodificaString");
  // Registra funções para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();
  
  /**************** ajax ****************/

  $cod_ferramenta=14;
  $cod_ferramenta_ajuda=$cod_ferramenta;
  $cod_pagina_ajuda=2;
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro Ã© a aÃ§Ã£o, o segundo Ã© o nÃºmero da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("novo_item", 18, 19);  
  
  /* Verifica se o usuario ï¿½ visitante. */
  $usr_visitante = EVisitante($sock, $cod_curso, $cod_usuario);

  if (isset ($acao) && $acao == "mudarcomp")
  {
    AlteraTipoCompartilhamento ($sock, $cod_item, $tipo_comp);
  }

  /* Se o cï¿½digo do usuï¿½rio cujo item serï¿½ exibido nï¿½o   */
  /* estiver definido, atribui-lhe o cï¿½digo do usuï¿½rio   */
  /* que estï¿½ visualizando o item, por padrï¿½o.           */
  /* Esta reatribuiï¿½ï¿½o ï¿½ necessï¿½ria porque as pï¿½ginas    */
  /* editar_item2 e renomear_item2 nï¿½o passam a variï¿½vel */
  /* cod_propriet, visto que essas aï¿½ï¿½es sï¿½ poderiam ser */
  /* feitas se o usuï¿½rio for o proprietï¿½rio do diï¿½rio e  */
  /* dos itens.                                          */
  if (!isset($cod_propriet))
  {
    $cod_propriet = RetornaCodProprietario($sock, $cod_item);
    //$cod_propriet = $cod_usuario;
  }

  // verifica se eh dono do diario
  $dono_diario = VerificaDonoDiario ($sock, $cod_curso, $cod_usuario, $cod_propriet);

  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");

  echo("  <script type=\"text/javascript\">\n\n");
  echo("    var js_tabela = '".$tabela."';\n");
  echo("    var js_cod_item = ".$cod_item.";\n");
  echo("    var js_cod_usuario = ".$cod_usuario.";\n");
  echo("    var js_cod_curso = ".$cod_curso.";\n");
  echo("    var conteudo='';");
  echo("    var editaTexto=0; \n");
  echo("    var editaTitulo=0; \n");
  echo("    var cancelarElemento=null; \n");
  /* (ger) 18 - Ok */
  // Texto do botão Ok do ckEditor
  echo("    var textoOk = '".RetornaFraseDaLista($lista_frases_geral, 18)."';\n\n");
  /* (ger) 2 - Cancelar */
  // Texto do botão Cancelar do ckEditor
  echo("    var textoCancelar = '".RetornaFraseDaLista($lista_frases_geral, 2)."';\n\n");

  // Iniciliza os layers. Se o usuario for o proprietario do diario entao 
  // atribui layer_renomear ï¿½ variavel lay_renomear.                      
  echo("    function Iniciar()\n");
  echo("    {\n");
  if ($dono_diario)
  {
    echo("      lay_comp = getLayer('layer_comp');\n");
  }
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("      startList();\n");
  echo("    }\n\n");


  echo("    function OpenWindowPerfil(id)\n");
  echo("    {\n");
  echo("      window.open(\"../perfil/exibir_perfis.php?");
  echo("&cod_curso=".$cod_curso."&cod_aluno[]=\" + id, \"PerfilDisplay\",\"width=600,height=400,");
  echo("top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("      return(false);\n");
  echo("    }\n\n");

  /* Esconde todos os layers. Se o usuario for o proprietï¿½rio do diï¿½rio   */
  /* visualizado entï¿½o esconde o layer para renomear o item.              */
  echo("    function EscondeLayers()\n");
  echo("    {\n");
  if ($dono_diario)
  {
    echo("      hideLayer(lay_comp);\n");
  }
  echo("    }\n\n");

  /* Se o usuï¿½rio for o proprietï¿½rio do diï¿½rio, cria as funï¿½ï¿½es para   */
  /* editar, renomear e apagar o item.                                 */
  if ($dono_diario)
  {
    echo("    function ApagarItem(){ \n");
    echo("      CancelaTodos(); \n");
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases, 34)."'))");
    echo("        window.location='acoes.php?cod_curso='+js_cod_curso+'&cod_item='+js_cod_item+'&acao=apagarItem'; \n");
    echo("      } \n");

    echo("    function CancelaTodos(){ \n");
    echo("      EscondeLayers(); \n");
    echo("      if(cancelarElemento) { cancelarElemento.onclick(); } \n");
    echo("    } \n");

    echo("    function EdicaoTitulo(codigo, id, ok){ \n");
    echo("      if (ok){ \n");
    echo("        conteudo = document.getElementById(id+\"_text\").value; \n");
    echo("        xajax_EditarTitulo(js_cod_curso, js_cod_item, conteudo, js_cod_usuario, '".RetornaFraseDaLista($lista_frases,39)."'); \n");
    echo("      } else { \n");
    echo("          document.getElementById(id).innerHTML=conteudo;\n");
    echo("          document.getElementById(id).className=\"\";\n");
    echo("      }\n");
    echo("      editaTitulo=0;\n");
    echo("      cancelarElemento=null;\n");
    echo("    } \n");

    echo("     function EditaTituloEnter(campo, evento, id)\n");
    echo("     {\n");
    echo("       var tecla;\n");
    echo("       CheckTAB=true;\n\n");
    echo("       if(navigator.userAgent.indexOf(\"MSIE\")== -1)\n");
    echo("       {\n");
    echo("           tecla = evento.which;\n");
    echo("       }\n");
    echo("       else\n");
    echo("       {\n");
    echo("           tecla = evento.keyCode;\n");
    echo("       }\n\n");
    echo("       if ( tecla == 13 )\n");
    echo("       {\n");
    echo("           EdicaoTitulo(id, 'tit_'+id, 'ok');\n"); //A funcoes e parametros sao os mesmos utilizados na funcao de edicao ja utilizada.
    echo("       }\n\n");
    echo("       return true;\n");
    echo("   }\n\n");

    echo("    function AlteraTitulo(id){ \n");
    echo("      var id_aux = id;\n");
    echo("      if (editaTitulo==0){ \n");
    echo("        CancelaTodos(); \n");

    echo("        xajax_DecodificaString(document.getElementById('tit_'+id).innerHTML); \n");
    echo("        conteudo = document.getElementById('tit_'+id).innerHTML; \n");

    echo("        document.getElementById(\"tr_\"+id).className=\"\"; \n");
    echo("        document.getElementById(\"tit_\"+id).className=\"\"; \n");
    echo("        document.getElementById(\"tr_\"+id).className=\"\"; \n");

    echo("        createInput = document.createElement(\"input\"); \n");
    echo("        document.getElementById(\"tit_\"+id).innerHTML=\"\"; \n");
    echo("        document.getElementById(\"tit_\"+id).onclick=function(){ }; \n");

    echo("        createInput.setAttribute(\"type\", \"text\"); \n");
    echo("        createInput.setAttribute(\"style\", \"border: 2px solid #9bc\"); \n");
    echo("        createInput.setAttribute(\"id\", \"tit_\"+id+\"_text\"); \n");
    echo("        if (createInput.addEventListener){\n"); //not IE
    echo("          createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);\n");
    echo("        } else if (createInput.attachEvent){\n"); //IE
    echo("          createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});\n");
    echo("        }\n");

    echo("        document.getElementById(\"tit_\"+id).appendChild(createInput); \n");
    echo("        xajax_DecodificaString(\"tit_\"+id+\"_text\", conteudo, \"value\"); \n");

    //cria o elemento 'espaco' e adiciona na pagina
    echo("        espaco = document.createElement(\"span\"); \n");
    echo("        espaco.innerHTML=\"&nbsp;&nbsp;\" \n");
    echo("        document.getElementById(\"tit_\"+id).appendChild(espaco); \n");

    echo("        createSpan = document.createElement(\"span\"); \n");
    echo("        createSpan.className=\"link\"; \n");
    echo("        createSpan.onclick= function(){ EdicaoTitulo(id, \"tit_\"+id, 1); }; \n");
    echo("        createSpan.setAttribute(\"id\", \"OkEdita\"); \n");
    echo("        createSpan.innerHTML=textoOk; \n");
    echo("        document.getElementById(\"tit_\"+id).appendChild(createSpan); \n");

    //cria o elemento 'espaco' e adiciona na pagina
    echo("        espaco = document.createElement(\"span\"); \n");
    echo("        espaco.innerHTML=\"&nbsp;&nbsp;\" \n");
    echo("        document.getElementById(\"tit_\"+id).appendChild(espaco); \n");

    echo("        createSpan = document.createElement(\"span\"); \n");
    echo("        createSpan.className=\"link\"; \n");
    echo("        createSpan.onclick= function(){ EdicaoTitulo(id, \"tit_\"+id, 0); }; \n");
    echo("        createSpan.setAttribute(\"id\", \"CancelaEdita\"); \n");
    echo("        createSpan.innerHTML=textoCancelar; \n");
    echo("        document.getElementById(\"tit_\"+id).appendChild(createSpan); \n");

    //cria o elemento 'espaco' e adiciona na pagina
    echo("        espaco = document.createElement(\"span\"); \n");
    echo("        espaco.innerHTML=\"&nbsp;&nbsp;\" \n");
    echo("        document.getElementById(\"tit_\"+id).appendChild(espaco); \n");
    echo("        startList(); \n");
    echo("        cancelarElemento=document.getElementById(\"CancelaEdita\"); \n");
    echo("        document.getElementById(\"tit_\"+id+\"_text\").select(); \n");
    echo("        editaTitulo++; \n");
    echo("      } \n");
    echO("    } \n");

    echo("    function EdicaoTexto(codigo, id, valor){ \n");
    echo("      if (valor=='ok'){ \n");
    echo("        eval('conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');");
    echo("        xajax_EditarTexto(js_cod_curso, js_cod_item, conteudo, js_cod_usuario, '".RetornaFraseDaLista($lista_frases,27)."'); \n");
    echo("      } \n");
    echo("      else{ \n");
    echo("      } \n");
    echo("      document.getElementById(id).innerHTML=conteudo; \n");
    echo("      editaTexto=0; \n");
    echo("      cancelarElemento=null; \n");
    echo("    } \n");

    echo("    function AlteraTexto(id){ \n");
    echo("      if (editaTexto==0) { \n");
    echo("        CancelaTodos(); \n");
    echo("        conteudo = document.getElementById('text_'+id).innerHTML; \n");
    echo("        writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true,false, id); \n");
    echo("        startList(); \n");
    echo("        document.getElementById('text_'+id+'_text').focus();\n");
    echo("        cancelarElemento=document.getElementById('CancelaEdita'); \n");
    echo("        editaTexto++; \n");
    echo("      } \n");
    echo("    } \n");

    echo("    function LimpaTexto(id){\n");
    // 66 - VocÃª tem certeza que deseja apagar o texto deste comentÃ¡rio?
    echo("      if (confirm('".RetornaFraseDaLista($lista_frases,66)."')){\n");
    echo("        checks = document.getElementsByName('chkArq');\n\n");
    echo("        CancelaTodos();\n");
    echo("        document.getElementById('text_'+id).innerHTML='';\n\n");
    // 67 - Texto excluido com sucesso
    echo("        xajax_EditarTexto(js_cod_curso, js_cod_item, '', js_cod_usuario, '".RetornaFraseDaLista($lista_frases,67)."');\n\n");
    echo("        editaTexto=0; \n");
    echo("      }\n");
    echo("    }\n\n");

    echo("    function AtualizaComp(js_cod_item, js_tipo_comp)\n");
    echo("    { \n");
    echo("      if ((isNav) && (!isMinNS6)) { \n");
    echo("        document.comp.document.form_comp.tipo_comp.value=js_tipo_comp; \n");
    echo("        document.comp.document.form_comp.cod_item.value=js_cod_item; \n");
    echo("        var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_P')); \n");
    echo("      } else { \n");
    echo("      if (isIE || ((isNav)&&(isMinNS6)) ){ \n");
    echo("          document.form_comp.tipo_comp.value=js_tipo_comp; \n");
    echo("          document.form_comp.cod_item.value=js_cod_item; \n");
    echo("          var tipo_comp = new Array(document.getElementById('tipo_comp_T'), document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_P')); \n");
    echo("          } \n");
    echo("      } \n");
    echo("      var imagem=\"<img src='../imgs/checkmark_blue.gif'>\" \n");
    echo("      if (js_tipo_comp=='F') { \n");
    echo("        tipo_comp[0].innerHTML=imagem; \n");
    echo("        tipo_comp[1].innerHTML=\"&nbsp;\"; \n");
    echo("        tipo_comp[2].innerHTML=\"&nbsp;\"; \n");
    echo("      } else if (js_tipo_comp=='T') { \n");
    echo("        tipo_comp[0].innerHTML=\"&nbsp;\"; \n");
    echo("        tipo_comp[1].innerHTML=imagem; \n");
    echo("        tipo_comp[2].innerHTML=\"&nbsp;\"; \n");
    echo("      } else{ \n");
    echo("        tipo_comp[0].innerHTML=\"&nbsp;\"; \n");
    echo("        tipo_comp[1].innerHTML=\"&nbsp;\"; \n");
    echo("        tipo_comp[2].innerHTML=imagem; \n");
    echo("      }\n");
    echo("    } \n");

    echo("        function MudaSpanCompartilhamento(spanID,novoComp,tipoComp,js_cod_item)\n");
    echo("        {\n");
    echo("          spanElement = document.getElementById(spanID);\n");
    echo("          spanElement.innerHTML = novoComp;\n");
    echo("          spanElement.onclick = function(event) { AtualizaComp(js_cod_item, tipoComp);MostraLayer(lay_comp,100,event); }\n");
    echo("        }\n");

    echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
    echo("    var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
  
    echo("    if (isNav)\n");
    echo("    {\n");
    echo("      document.captureEvents(Event.MOUSEMOVE);\n");
    echo("    }\n");
    echo("    document.onmousemove = TrataMouse;\n\n");
  
    echo("    function TrataMouse(e)\n");
    echo("    {\n");
    echo("      Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
    echo("      Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
    echo("    }\n\n");
  
    echo("    function getPageScrollY()\n");
    echo("    {\n");
    echo("      if (isNav)\n");
    echo("        return(window.pageYOffset);\n");
    echo("      if (isIE)\n");
    echo("        return(document.body.scrollTop);\n");
    echo("    }\n\n");
  
    echo("    function AjustePosMenuIE()\n");
    echo("    {\n");
    echo("      if (isIE)\n");
    echo("        return(getPageScrollY());\n");
    echo("      else\n");
    echo("        return(0);\n");
    echo("    }\n\n");
  
    // Esconde todos os layers. Se o usuario for o proprietï¿½rio do diï¿½rio
    // visualizado entï¿½o esconde o layer para renomear o item.
    echo("    function EscondeLayer()\n");
    echo("    {\n");
    echo("      hideLayer(lay_comp);\n");
    echo("    }\n\n");
  
  
    echo("      function MostraLayer(cod_layer, ajuste, ev)\n");
    echo("        {\n");
    echo("        EscondeLayers();\n");
    echo("        ev = ev || window.event;\n");
    echo("        if(ev.pageX || ev.pageY){\n");
    echo("          Xpos = ev.pageX;\n");
    echo("          Ypos = ev.pageY;\n");
    echo("        }else{\n");
    echo("          Xpos = ev.clientX + document.body.scrollLeft - document.body.clientLeft;\n");
    echo("          Ypos = ev.clientY + document.body.scrollTop  - document.body.clientTop;\n");
    echo("        }\n");
    echo("        moveLayerTo(cod_layer,Xpos-100,Ypos);\n");
    echo("        showLayer(cod_layer);\n");
    echo("      }\n\n");

  }

  echo("</script>\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario);

  /* 1 - Diï¿½rio de Bordo */
  echo("          <h4>".RetornaFraseDaLista($lista_frases, 1));
  /* 41 - Ver anotaï¿½ï¿½o */
  echo(" - ".RetornaFraseDaLista($lista_frases, 41)."</h4>");
  
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
  
  /* 42 - Voltar ao diario */
  echo("                  <li><a href=\"javascript:history.back(-1);\">".RetornaFraseDaLista($lista_frases, 42)."</a></li>\n");
  
  /* 5 - Ver outros diarios */
  echo("                  <li><a href=\"ver_outros.php?&amp;cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$cod_item."&amp;cod_propriet=".$cod_propriet."&amp;origem=diario\">".RetornaFraseDaLista($lista_frases, 5)."</a></li>\n");
  
  /* 12 - Comentarios */

  echo("                  <li><a href=\"comentarios.php?&amp;cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$cod_item."&amp;cod_propriet=".$cod_propriet."\">".RetornaFraseDaLista($lista_frases, 12)."</a></li>\n");
  
  if ($dono_diario){
  	/* 1 - Apagar (Ger) */
    echo("                <li><span onclick=\"ApagarItem();\">".RetornaFraseDaLista($lista_frases_geral,1)."</span></li>\n");
  }
  
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  /* Obtï¿½m o cod_usuario, titulo, texto e a data do item especificado. */
  $item_dados = RetornaItem($sock, $cod_item);

  /* <!----------------- Tabela Interna -----------------> */
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  echo("                  <tr class=\"head\">\n");
  /* 8 - Titulo */
  echo("                    <td>".RetornaFraseDaLista($lista_frases, 8)."</td>\n");
  
  if($dono_diario)
  {
    /* 70(ger) - Op&ccedil;&otilde;es */
    echo("                    <td width=\"15%\">".RetornaFraseDaLista($lista_frases_geral, 70)."</td>\n");
  }
  
  /* 10 - Data */
  echo("                    <td width=\"15%\">".RetornaFraseDaLista($lista_frases, 10)."</td>\n");
  // 57 - Compartilhamento
  echo("                    <td width=\"30%\">".RetornaFraseDaLista($lista_frases, 57)."</td>\n");
  echo("                  </tr>\n");



  /* Exibe o titulo, as opcoes, a data do item e o modo de compartilhamento */
  echo("                  <tr id=\"tr_".$cod_item."\">\n");


  if($dono_diario)
  {
  
    echo("                    <td align=\"left\"><span id=\"tit_".$cod_item."\">".$item_dados['titulo']."</span></td>\n");

    /* <!----------------- Lista Opcoes -----------------> */
    echo("                    <td width=\"15%\" class=\"botao2\" align=\"center\">\n");
    echo("                      <ul>\n");

    /* 38 - Renomear titulo */
    echo("                        <li><span onClick=\"AlteraTitulo('".$cod_item."');\">".RetornaFraseDaLista($lista_frases,38)."</span></li>\n");
    /* 65 - Editar texto */
    echo("                        <li><span onclick=\"AlteraTexto('".$cod_item."');\">".RetornaFraseDaLista($lista_frases,65)."</span></li>\n");
    /* 68 - Limpar texto */
    echo("                        <li><span onclick=\"LimpaTexto('".$cod_item."');\">".RetornaFraseDaLista($lista_frases,68)."</span></li>\n");
    /* 1 - Apagar (Ger) */
    //echo("                      <li><span onclick=\"ApagarItem();\">".RetornaFraseDaLista($lista_frases_geral,1)."</span></li>\n");

  echo("                      </ul>\n");
  echo("                    </td>\n");
  }
  else
  {
    /* Titulo */
    echo("                    <td align=\"left\"><span id=\"tit_".$cod_item."\">".$item_dados['titulo']."</span></td>\n");
  }  

  /* Data */
  echo("                    <td width=\"15%\">".UnixTime2Data($item_dados['data']).", ".UnixTime2Hora($item_dados['data'])."</td>\n");
  
  if ( ($tipo_compartilhamento = $item_dados ['tipo_compartilhamento'] ) == 'T')
    // 60 - Totalmente compartilhado
    $cod_frase = 60;
  if ($tipo_compartilhamento == 'F')
    // 59 - Compartilhado com formadores
    $cod_frase = 59;
  if ($tipo_compartilhamento == 'P')
    // 58 - Nao compartilhado
    $cod_frase = 58;

  if ( $dono_diario )
  {
    $link_abre = "<span id='span_".$cod_item."' class=link onclick=\"AtualizaComp(".$cod_item.",'".$tipo_compartilhamento."');MostraLayer(lay_comp,100,event);return(false);\">";
    $link_fecha= "</span>";
  }
  else
  {
    $link_abre = "";
    $link_fecha= "";
  }
  /* Compartilhamento */
  echo("                    <td width=\"20%\">".$link_abre.RetornaFraseDaLista ($lista_frases, $cod_frase).$link_fecha."</td>\n");
  echo("                  </tr>\n");

  echo("                  <tr class=\"head\">\n");
  /* 15 - Texto */
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases, 15)."</td>\n");
  echo("                  </tr>\n");

  /* Exibe o texto do item.                                            */
  echo("                  <tr>\n");
//  echo("        <td class=text colspan=2>".Enter2BR(Space2Nbsp(LimpaConteudo($item_dados['texto'])))."</td>\n");
  //echo("        <td class=text colspan=2>".AjustaParagrafo(Enter2Br($item_dados['texto']))."</td>\n");
  $texto="<span id=\"text_".$cod_item."\">".AjustaParagrafo($item_dados['texto'])."</span>";
  echo("                    <td colspan=\"4\" align=\"left\">\n");
  echo("                      <div class=\"divRichText\">".$texto."</div>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");

  /* Obtem a data do penultimo acesso do usuario para compara-la com   */
  /* datas dos comentarios.                                            */
  $penultac = PenultimoAcesso($sock, $cod_usuario, "");

 
  echo("                </table>\n");

  $status_curso=RetornaStatusCurso($sock,$cod_curso);
  /* Se o usuï¿½rio for o proprietï¿½rio do diï¿½rio, exibe os botï¿½es para   */
  /* editar, renomear e apagar o item.                                 */
  
  $usr_formador = EFormador($sock,$cod_curso,$cod_usuario);
  

  echo("                </table>\n");


  if ( $dono_diario )
  {

    // Mudar Compartilhamento
    echo("          <div class=\"popup\" id=\"layer_comp\" style=\"visibility:hidden oncontextmenu:return(false);\">\n");
    echo("            <div class=\"posX\"><span onclick=\"EscondeLayer(lay_comp);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
    echo("            <div class=\"int_popup\">\n");
    echo("            <form name=\"form_comp\" id=\"form_comp\" action=\"\">\n");
    echo("              <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
    echo("              <input type=\"hidden\" name=\"cod_item\" value=\"".$cod_item."\" />\n");
    echo("              <input type=\"hidden\" name=\"tipo_comp\" id=\"tipo_comp\" value=\"\" />\n");
    /* 71 - Compartilhamento alterado com sucesso. */
    echo("              <input type=\"hidden\" name=\"texto\" id=\"texto\" value=\"".RetornaFraseDaLista($lista_frases,71)."\" />\n");
    echo("              <ul class=\"ulPopup\">\n");
    echo("                <li onclick=\"document.getElementById('tipo_comp').value='T'; xajax_MudaTipoCompartilhamento(xajax.getFormValues('form_comp'), '".RetornaFraseDaLista($lista_frases,60)."'); EscondeLayers();\">\n");
    echo("                  <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
    /* 60 - Totalmente compartilhado */
    echo("                  <span>".RetornaFraseDaLista($lista_frases,60)."</span>\n");
    echo("                </li>\n");
    echo("               <li onClick=\"document.getElementById('tipo_comp').value='F'; xajax_MudaTipoCompartilhamento(xajax.getFormValues('form_comp'), '".RetornaFraseDaLista($lista_frases,59)."'); EscondeLayers();\">\n");
    echo("                  <span id=\"tipo_comp_T\" class=\"check\"></span>\n");
    /* 59 - Compartilhado com formadores */
    echo("                  <span>".RetornaFraseDaLista($lista_frases,59)."</span>\n");
    echo("                </li>\n");
    echo("                <li onClick=\"document.getElementById('tipo_comp').value='P'; xajax_MudaTipoCompartilhamento(xajax.getFormValues('form_comp'), '".RetornaFraseDaLista($lista_frases,58)."'); EscondeLayers();\">\n");
    echo("                  <span id=\"tipo_comp_P\" class=\"check\"></span>\n");
    /* 58 - Nao Compartilhado */
    echo("                  <span>".RetornaFraseDaLista($lista_frases,58)."</span>\n");
    echo("                </li>\n");
    echo("              </ul>\n");
    echo("            </form>\n");
    echo("            </div>\n");
    echo("          </div>\n");

  }

  echo("          <br />\n");
  /* 509 - voltar, 510 - topo */
  echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");

  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");
  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
