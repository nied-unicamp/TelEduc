<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perguntas/perguntas.php

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
  ARQUIVO : cursos/aplic/perguntas/perguntas.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");

  $cod_ferramenta = 6;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=4;

  $tabela = "Pergunta";

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das fun��es em PHP que voc� quer chamar atrav�s do xaja
  $objAjax->register(XAJAX_FUNCTION,"EditarTexto");
  //$objAjax->register(XAJAX_FUNCTION,"AcabaEdicaoDinamic");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  //adicionar as acoes possiveis, 1o parametro é
  $feedbackObject->addAction("apagarItem", 76, 0);
  $feedbackObject->addAction("moverItem", 78, 0);

  /* Verifica se o usuario eh formador. */
  if (EFormador($sock, $cod_curso, $cod_usuario))
    $usr_formador = true;
  else
    $usr_formador = false;

  echo("<script type=\"text/javascript\" src=\"../js-css/sorttable.js\"></script>\n");
  echo("<script type=\"text/javascript\" language=\"javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("<script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("<script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");
  echo("<script language=\"javascript\">\n\n");

  echo("  img_icone = new Image();\n");
  echo("  img_icone.src = \"../figuras/assunto.gif\";\n\n");

  echo("  var existelayer = false; ");
  echo("  var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("  var versao = (navigator.appVersion.substring(0,3));\n");
  echo("  var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
  /* (ger) 18 - Ok */
  // Texto do bot�o Ok do ckEditor
  echo("    var textoOk = '".RetornaFraseDaLista($lista_frases_geral, 18)."';\n\n");
  /* (ger) 2 - Cancelar */
  // Texto do bot�o Cancelar do ckEditor
  echo("    var textoCancelar = '".RetornaFraseDaLista($lista_frases_geral, 2)."';\n\n");

  echo("  if (isNav)\n");
  echo("  {\n");
  echo("    document.captureEvents(Event.MOUSEMOVE);\n");
  echo("  }\n");
  echo("  document.onmousemove = TrataMouse;\n\n");

  echo("  function TrataMouse(e)\n");
  echo("  {\n");
  echo("    Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("    Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("  }\n\n");


  echo("  var selected_item;\n");

  echo("  function getPageScrollY()\n");
  echo("  {\n");
  echo("    if (isNav)\n");
  echo("      return(window.pageYOffset);\n");
  echo("    if (isIE)\n");
  echo("      return(document.body.scrollTop);\n");
  echo("  }\n\n");


  echo("  function AjustePosMenuIE()\n");
  echo("  {\n");
  echo("    if (isIE)\n");
  echo("      return(getPageScrollY());\n");
  echo("    else\n");
  echo("      return(0);\n");
  echo("  }\n\n");


  echo("  function Iniciar()\n");
  echo("  {\n");
  echo("    layer_estrutura = getLayer('layer_estrutura');\n");
  if ($usr_formador)
  {
    if ($acao == "novaPergunta" && $cod_pergunta != NULL){
      echo("ExibirMensagem($cod_pergunta); AlteraTexto($cod_pergunta);");
    }
    echo("    layer_estrutura_mover = getLayer('layer_estrutura_mover');\n");
  }
  echo("        var atualizacao = '".$_GET['atualizacao']."';\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("  EscondeLayers();\n");
  echo("  }\n\n");


  echo("      function VerificaCheck(){\n");
  echo("        var i;\n");
  echo("        var j=0;\n");
  echo("        var k=0;\n");
  echo("        var codPergunta=document.getElementsByName('cod_pergunta[]');\n");
  echo("        var codAssunto=document.getElementsByName('cod_assunto[]');\n");
  echo("        var Cabecalho = document.getElementById('checkMenu');\n");
  echo("        array_perguntas = new Array();\n");
  echo("        array_assunto = new Array();\n");
  echo("        for (i=0; i<codPergunta.length; i++){\n");
  echo("          if (codPergunta[i].checked){\n");
  echo("            j++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        for (i=0; i<codAssunto.length; i++){\n");
  echo("          if (codAssunto[i].checked){\n");
  echo("            k++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if ((k+j)==(codAssunto.length+codPergunta.length)) Cabecalho.checked=true;\n");
  echo("        else Cabecalho.checked=false;\n");
  echo("        // Se tiver ao menos 1 checkbox, seja assunto ou\n");
  echo("        // pergunta tickado, mostra os botoes\n");
  echo("        var i = 0;\n");
  echo("        for (i = 0; i < codAssunto.length; i++)\n");
  echo("          if (codAssunto[i].checked){\n");
  echo("            return HabilitaBotoes();\n");
  echo("          }\n");
  echo("        i = 0;\n");
  echo("        for (i = 0; i < codPergunta.length; i++)\n");
  echo("          if (codPergunta[i].checked){\n");
  echo("            return HabilitaBotoes();\n");
  echo("          }\n");
  echo("        return DesabilitaBotoes();\n");
  echo("      }\n");
  echo("  var PerguntasAbertas = 0;\n");

  echo("  function ExibirSelecionadas(){\n");
  echo("    var Perguntas = document.getElementsByName('cod_pergunta[]');\n");
  echo("    var nPerguntas = Perguntas.length;\n");
  echo("    var i = 0;\n");
  echo("      for (i = 0; i < nPerguntas; i++)\n");
  echo("        if (Perguntas[i].checked){\n");
  echo("          ExibirMensagem(Perguntas[i].value);\n");
  echo("        }\n");
  echo("  }");

  echo("  function FecharSelecionadas(){\n");
  echo("    var Perguntas = document.getElementsByName('cod_pergunta[]');\n");
  echo("    var nPerguntas = Perguntas.length;\n");
  echo("    var i = 0;\n");
  echo("      for (i = 0; i < nPerguntas; i++)\n");
  echo("        if (Perguntas[i].checked){\n");
  echo("          FechaMensagem(Perguntas[i].value);\n");
  echo("        }\n");
  echo("  }\n");

  echo("  function HabilitaBotoes(){");
  if ($usr_formador){
    if ($cod_assunto_pai == 2){ /* Tá na lixeira? */
      echo("      document.getElementById('mExcluir_Selec').className=\"menuUp02\";");
      echo("      document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionadas(); };\n");
      echo("      document.getElementById('mRecuperar_Selec').className=\"menuUp02\";");
      echo("      document.getElementById('mRecuperar_Selec').onclick=function(){ MostraLayer(layer_estrutura_recuperar,this); };\n");
    } else {
      echo("      document.getElementById('mApagar_Selec').className=\"menuUp02\";");
      echo("      document.getElementById('mApagar_Selec').onclick=function(){ ApagarSelecionadas(); };\n");
      echo("      document.getElementById('mMover_Selec').className=\"menuUp02\";");
      echo("      document.getElementById('mMover_Selec').onclick=function(){ MostraLayer(layer_estrutura_mover,this); };\n");
    }
  }
  echo("        if (PerguntasAbertas > 0) HabilitaBotaoFechar() ;");
  echo("        document.getElementById('mExibir_Selec').className=\"menuUp02\";");
  echo("        document.getElementById('mExibir_Selec').onclick=function(){ ExibirSelecionadas(); };\n");
  echo("  }");

  echo("  function HabilitaBotaoFechar(){");
  echo("       document.getElementById('mFechar_Selec').className=\"menuUp02\";");
  echo("       document.getElementById('mFechar_Selec').onclick=function(){ FecharSelecionadas(); };\n");
  echo("  }");


  echo("  function DesabilitaBotoes(){");
  if ($usr_formador){
    if ($cod_assunto_pai == 2){ /* Tá na lixeira? */
      echo("      document.getElementById('mExcluir_Selec').className=\"menuUp\";");
      echo("      document.getElementById('mExcluir_Selec').onclick=function(){};\n");
      echo("      document.getElementById('mRecuperar_Selec').className=\"menuUp\";");
      echo("      document.getElementById('mRecuperar_Selec').onclick=function(){};\n");
    } else {
      echo("      document.getElementById('mApagar_Selec').className=\"menuUp\";");
      echo("      document.getElementById('mApagar_Selec').onclick=function(){};\n");
      echo("      document.getElementById('mMover_Selec').className=\"menuUp\";");
      echo("      document.getElementById('mMover_Selec').onclick=function(){};\n");
    }
  }
  echo("      document.getElementById('mExibir_Selec').className=\"menuUp\";");
  echo("      document.getElementById('mExibir_Selec').onclick=function(){};\n");
  echo("      DesabilitaBotaoFechar();");
  echo("  }");

  echo("function DesabilitaBotaoFechar(){");
  echo("      document.getElementById('mFechar_Selec').className=\"menuUp\";");
  echo("      document.getElementById('mFechar_Selec').onclick=function(){};\n");
  echo("}");


  echo("  function EscondeLayer(cod_layer)\n");
  echo("  {\n");
  echo("    hideLayer(cod_layer);\n");
  echo("  }\n\n");

  echo("  function EscondeLayers()\n");
  echo("  {\n");
  echo("    hideLayer(layer_estrutura);\n");
  if ($usr_formador)
  {
    echo("    hideLayer(layer_estrutura_mover);\n");
  }
  echo("  }\n\n");

  echo("  function Abrir(id)\n");
  echo("  {\n");
  echo("    document.frmAssuntoAcao.action='perguntas.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=6';\n");
  echo("    document.frmAssuntoAcao.cod_assunto_pai.value = id;\n");
  echo("    document.frmAssuntoAcao.submit();\n");
  echo("  }\n\n");

  echo("  function MostraLayer(cod_layer, obj)\n");
  echo("  {\n");
  echo("    EscondeLayers();\n");
  echo("existelayer=true;");
  /* Se o browser for Netscape alinhe com a link. */
  echo("    if ((isNav) && (versao<'5.0'))\n");
  echo("    {\n");
  /* Se for a estrutura de assuntos entao desloca um pouco mais aa direita */
  /* senao o layer ficarah atras das checkboxs das perguntas.              */
  echo("      if (cod_layer == layer_estrutura)\n");
  echo("        moveLayerTo(cod_layer, obj.x + img_icone.height, obj.y + img_icone.height);\n");
  echo("      else\n");
  echo("      {\n");
  echo("        moveLayerTo(cod_layer, obj.x , obj.y + img_icone.height);\n");
  echo("      }\n");
  echo("    }\n");
  echo("    else\n");
  echo("      moveLayerTo(cod_layer, Xpos, Ypos + AjustePosMenuIE());\n");
  echo("    showLayer(cod_layer);\n");
  echo("  }\n\n");

  /* Se o usuario FOR Formador entao cria as fun�oes javascript. */
  if ($usr_formador)
  {
    echo("  function Apagar(id, tipo)\n");
    echo("  {\n");
    echo("    if (tipo == 1)\n");
    echo("    {\n");
    /* 32 - Tem certeza que deseja apagar este assunto? (todos os assunto e todas as perguntas nele contidos ser�o apagados) */
    echo("      if (confirm('".RetornaFraseDaLista($lista_frases, 32)."'))\n");
    echo("      {\n");
    echo("        document.frmAssuntoAcao.acao.value='apagarItem';\n");
    echo("        document.frmAssuntoAcao.action='acoes.php';\n");
    echo("        document.frmAssuntoAcao.cod_assunto.value = id;\n");
    echo("        document.frmAssuntoAcao.submit();\n");
    echo("      }\n");
    echo("    }\n");
    echo("    else if (tipo == 2)\n");
    echo("    {\n");
    /* 21 - Tem certeza que deseja apagar esta pergunta? */
    echo("      if (confirm('".RetornaFraseDaLista($lista_frases,21)."'))\n");
    echo("      {\n");
    echo("       document.frmAssuntoAcao.acao.value='apagarItem';\n");
    echo("        document.frmPerguntaAcao.action='acoes.php';\n");
    echo("        document.frmPerguntaAcao.cod_pergunta.value = id;\n");
    echo("        document.frmPerguntaAcao.submit();\n");
    echo("      }\n");
    echo("    }\n");
    echo("  }\n\n");

    echo("  function ApagarSelecionadas(id)\n");
    echo("  {\n");
    echo(" verificador=Validacheck();\n");
     /* 43 - Deseja excluir definitivamente esta pergunta? */
    echo("if(verificador==true)\n");
    echo("      if (confirm('".RetornaFraseDaLista($lista_frases,21)."'))\n");
    echo("      {\n");
    echo("        document.frm_pergunta.acao.value='apagarItem';\n");
    echo("        document.frm_pergunta.action='acoes.php';\n");
    echo("        document.frm_pergunta.submit();\n");
    echo("  return true;\n");
    echo("      }\n");
    echo("  return false;\n");
    echo("  }\n\n");

    echo("  function Editar(id,tipo)\n");
    echo("  {\n");
    echo("    if (tipo == 1)\n");
    echo("    {\n");
    echo("        document.frmAssuntoAcao.action='editar_assunto.php';\n");
    echo("        document.frmAssuntoAcao.cod_assunto.value = id;\n");
    echo("        document.frmAssuntoAcao.submit();\n");
    echo("    }\n");
    echo("    if (tipo == 2)\n");
    echo("    {\n");
    echo("        document.frmPerguntaAcao.action='editar_pergunta.php';\n");
    echo("        document.frmPerguntaAcao.cod_pergunta.value = id;\n");
    echo("        document.frmPerguntaAcao.submit();\n");
    echo("    }\n");
    echo("  }\n\n");

    echo("  function MoverAssunto(origem, destino, proprietario)\n");
    echo("  {\n");
    echo("    if (origem == proprietario)\n");
    /* 33 - N�o � poss�vel mover para o pr�prio assunto ou sub-assunto. */
    echo("      alert(\"".RetornaFraseDaLista($lista_frases, 33)."\");\n");
    echo("    else\n");
    echo("    {\n");
    /* 37 - Deseja realmente mover este assunto? */
    echo("      if (confirm(\"".RetornaFraseDaLista($lista_frases, 37)."\"))\n");
    echo("      {\n");
    echo("        document.frm_pergunta.action ='acoes.php';\n");
    echo("        document.frm_pergunta.acao.value = \"moverItem\";\n");
    echo("        document.frm_pergunta.cod_assunto_dest.value = destino;\n");
    echo("        document.frm_pergunta.submit();\n");
    echo("      }\n");
    echo("    }\n");
    echo("  }\n\n");

    echo("  function MoverPergunta(id, destino)\n");
    echo("  {\n");

    /* Se estiver na lixeira */
    if ($cod_assunto_pai == 2)
    {
      /* 47 - Deseja realmente recuperar esta pergunta? */
      echo("    if (confirm(\"".RetornaFraseDaLista($lista_frases, 47)."\"))\n");
      echo("    {\n");
      echo("      document.frmPerguntaAcao.action='recuperar_pergunta2.php';\n");
    }
    else
    {
      /* 38 - Deseja realmente mover esta pergunta? */
      echo("    if (confirm(\"".RetornaFraseDaLista($lista_frases, 38)."\"))\n");
      echo("    {\n");
      echo("      document.frmPerguntaAcao.action='mover_pergunta2.php';\n");
    }

    echo("      document.frmPerguntaAcao.cod_pergunta.value = id;\n");
    echo("      document.frmPerguntaAcao.cod_assunto_dest.value = destino;\n");
    echo("      document.frmPerguntaAcao.submit();\n");
    echo("    }\n");
    echo("  }\n\n");
  }

  echo("  function Envia(assunto)");
  echo("  {\n");
  echo("     verificador=Validacheck();\n");
  echo("     if(verificador==true)\n");
  echo("     {\n");
  echo("       window.open('','pergunta','width=600,height=400,top=50,left=50,scrollbars=yes,");
  echo("status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("       if(assunto==2)\n");
  echo("         document.frm_pergunta.action = \"ver_pergunta_lixeira.php\";\n");
  echo("       else\n");
  echo("         document.frm_pergunta.action = \"ver_pergunta.php?cod_curso=".$cod_curso."\";\n");
  echo("       document.frm_pergunta.target = 'pergunta';\n");
  echo("       document.frm_pergunta.submit();\n");
  echo("       return true;\n");
  echo("     }\n");
  echo("    return false;\n");
  echo("  }\n\n");

  echo("   function Validacheck()\n");
  echo("   {\n");
  echo("      var cont=false;\n");
  echo("      var e;\n");
  echo("      for (var i=0;i<document.frm_pergunta.elements.length;i++)\n");
  echo("      {\n");
  echo("        e = document.frm_pergunta.elements[i];\n");
  echo("        if (e.checked==true)\n");
  echo("        {\n");
  echo("         cont=true;\n");
  echo("        }\n");
  echo("      }\n");
  echo("     if (cont==true)\n");
  echo("     {\n");
  echo("       return true;\n");
  echo("     }\n");
  echo("     else\n");
  echo("     {\n");
  echo("       alert('".RetornaFraseDaLista($lista_frases, 50)."');\n");
  echo("       return false;\n");
  echo("     }\n");
  echo("  }\n");

  echo("      function CheckTodos(){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("        var codAssunto=document.getElementsByName('cod_assunto[]');\n");
  echo("        var codPergunta=document.getElementsByName('cod_pergunta[]');\n");
  echo("        for(i = 0; i < codAssunto.length; i++){\n");
  echo("          e = codAssunto[i];\n");
  echo("          e.checked = CabMarcado;\n");
  echo("        }\n");
  echo("        for(i = 0; i < codPergunta.length; i++){\n");
  echo("          e = codPergunta[i];\n");
  echo("          e.checked = CabMarcado;\n");
  echo("        }\n");
  echo("        VerificaCheck();\n");
  echo("      }\n\n");

  echo("      function ExibirMensagem(cod_mural)\n");
  echo("      {\n");
  echo("        PerguntasAbertas++;\n");
  echo("        VerificaCheck();");
  echo("        var browser=navigator.appName;\n\n");
  echo("        var totalMsgs=document.getElementsByName('tr_msg').length;\n");
  echo("        var vLink = document.getElementById('tr_msg_'+cod_mural);\n");

  echo("        if((vLink.style.display == 'table-row') || (vLink.style.display == 'block'))");
  echo("        {");
  echo("        vLink.style.display='none';");
//  echo("        mensagens_abertas--;");
  echo("        }");
  echo("        else");
  echo("        {");
  echo("          if (browser==\"Microsoft Internet Explorer\")\n");
  echo("            vLink.style.display=\"block\";\n");
  echo("          else\n");
  echo("            vLink.style.display=\"table-row\";\n");
//  echo("          mensagens_abertas++;\n");
  echo("        }");

  echo("        if(totalMsgs <= 10){\n");
  //echo("          VerificaAbertas();\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function FechaMensagem(cod_mural){\n");
  echo("          document.getElementById('tr_msg_'+cod_mural).style.display=\"none\";\n");
  echo("          PerguntasAbertas--;");
  echo("          if (PerguntasAbertas == 0) DesabilitaBotaoFechar();");
  echo("      }\n");

  echo("      function AlteraTexto(id){\n");
  //echo("        if (editaTexto==0){\n");
  //echo("          CancelaTodos();\n");

  //echo("          xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
  echo("          conteudo = document.getElementById('text_'+id).innerHTML;\n");
  echo("          writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true, false, id);\n");
//  echo("          startList();\n");
//  echo("          document.getElementById('text_'+id+'_text').focus();\n");
//  echo("          cancelarElemento=document.getElementById('CancelaEdita');\n");
//  echo("          editaTexto++;\n");
//  echo("        }\n");
  echo("      }\n");

  echo("      function EdicaoTexto(codigo, id, valor){\n");
  echo("        if (valor=='ok'){\n");
  echo("          eval('conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');");
  //echo("          conteudo=document.getElementById(id+'_text').contentWindow.document.body.innerHTML;\n");
  echo("          xajax_EditarTexto('".$tabela."', ".$cod_curso.", codigo, conteudo, ".$cod_usuario.");\n");
  echo("          mostraFeedback('".htmlentities(RetornaFraseDaLista($lista_frases, 23))."', true)\n");
  echo("        }\n");
//  echo("        else{\n");
//  echo("          //Cancela Edição\n");
//  echo("          if (!cancelarTodos)\n");
//  echo("            mostraFeedback('".htmlentities(RetornaFraseDaLista($lista_frases, 40))."', true)\n");
//  echo("            xajax_AcabaEdicaoDinamic('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", 0);\n");
//  echo("        }\n");
  echo("        document.getElementById(id).innerHTML=conteudo;\n");
//  echo("        editaTexto=0;\n");
//  echo("        cancelarElemento=null;\n");
  echo("      }\n\n");

  echo("</script>\n\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 1 - Perguntas */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1));


  echo("</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  /* 1 - Perguntas Freq�entes */
  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";

  //echo("  <br>\n");

  echo("  <span class=\"btsNav2\"><a href=\"#\" onClick='MostraLayer(layer_estrutura,this);return(false);'><img src=../imgs/estrutura.gif border=0></a>\n");
  //echo("  <a href=\"#\" onMouseDown='MostraLayer(lay_estrutura,0);return(false);'><img src=../figuras/estrutura.gif border=0></a>\n");
  echo("    <font class=\"text\">".RetornaLinkCaminhoAssunto($sock, $cod_assunto_pai, $cod_curso, "perguntas"));
  echo("    </font></span>\n");
  echo("  \n");

  /* Obtem os dados do assunto atual.                            */
  $dados_assunto_pai = RetornaAssunto($sock, $cod_assunto_pai);

  /* Se a descri�ao NAO for vazia ou composta por apenas espa�os */
  /* entao a exibe.                                              */
  if (EliminaEspacos($dados_assunto_pai['descricao']) != "")
  {
    echo("  <table border=0 width=100% cellspacing=2>\n");
    echo("    <tr>\n");
    // 6 - Descri��o
    echo("      <td valign=top width=1% class=textsmall><i>".RetornaFraseDaLista($lista_frases, 6));
    echo("</i>:</td>\n");
    echo("      <td class=textsmall>\n");
    echo(Space2Nbsp(Enter2BR(LimpaTags($dados_assunto_pai['descricao'])))."\n");
    echo("      </td>\n");
    echo("    </tr>\n");
    echo("  </table>\n");
  }

  $lista_assuntos = ListaAssuntos($sock, $cod_assunto_pai);
  echo("  <form method=\"post\" name=\"frm_pergunta\">");


  echo("          <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("              <tr>\n");
  echo("              <!-- Botoes de Acao -->\n");
  echo("                <td class=\"btAuxTabs\">\n");
  echo("                  <ul class=\"btAuxTabs\">\n");

  /* Se o usuario FOR Formador entao exibe os controles. */
  if ($usr_formador)
  {
    //if ($cod_assunto_pai != 1)
    //echo("    <td align=center class=menu width=1%><input type=checkbox name=\"todas\" onClick=\"MarcaOuDesmarcaTodos();\"></td>\n");
    /* Se NAO estiver na lixeira possibilita a inser��o de assunto. */
    if ($cod_assunto_pai != 2)
    {
      if($cod_assunto_pai != 1)
        /* 23 - Voltar */
        echo("      <li><span onClick=history.go(-1);>".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>");

    }
  }

  if ($usr_formador)
  {
  echo("    <input type=\"hidden\" name=origem value=perguntas>\n");
    if ($cod_assunto_pai == 2)
    {
      /* ? - Voltara exibi��o normal */
      /* 56 - Voltar para perguntas freq�entes*/
      echo("      <li><span onClick=\"history.go(-1);\">".RetornaFraseDaLista($lista_frases, 56)."</span></li>\n");
    }
    else
    {
      /* 16 - Lixeira */
      echo("      <li><span href=\"#\" onClick='Abrir(2); return(false);'>".RetornaFraseDaLista($lista_frases_geral,16)."</span></li>\n");
    }
  }

  echo("    </tr>\n");




  /* Se estiver na Lixeira o formulario submete as informa�oes para */
  /* ver_pergunta_lixeira.php, do contrario, para ver_pergunta.php  */
  echo("  <form method=\"post\" name=\"frm_pergunta\" action=");
  if ($cod_assunto_pai == 2)
    echo("ver_pergunta_lixeira.php");
  else
    echo("ver_pergunta.php");

  echo(" target='pergunta' onsubmit='return(MostrarSelecionadas());'>\n");

  echo("  <form method=\"post\" name=\"frm_pergunta\" target=\"pergunta\">");

  //echo(RetornaSessionIDInput());
  echo("<input type=\"hidden\" name=cod_curso value=".$cod_curso.">\n");
  echo("<input type=\"hidden\" name=acao value=\"\">\n");

  echo("    <input type=\"hidden\" name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
  echo("    <input type=\"hidden\" name=cod_assunto_dest value=\"\">\n");
  /* Especifica o documento da pagina principal, o qual chamou o    */
  /* ver_pergunta.php. Isto eh necessario para atualizar a pagina   */
  /* principal que pode ser perguntas.php ou exibir_todas.php.      */
  echo("    <input type=\"hidden\" name=pagprinc value=perguntas>\n");

  if ($cod_assunto_pai == 2)
    /* Passa o 'cod_assunto_anterior', necessario para se voltar ao */
    /* assunto anterior a visualiza�ao da lixeira.                  */
    echo("  <input type=\"hidden\" name=cod_assunto_anterior value=".$cod_assunto_anterior.">\n");
  else
    echo("  <input type=\"hidden\" name=cod_assunto_anterior value=".$cod_assunto_pai.">\n");

  /* Especifica o documento da pagina principal, o qual chamou o    */
  /* perguntas.php, mas com o cod_assunto_pai = 2 (lixeira). Isto   */
  /* eh necessario para voltar ao modo de visualiza�ao anterior.    */
  if (isset($pagprinc))
  /* Se jah estiver setada entao usa o valor default. Isto eh     */
  /* necessario quando o cod_assunto_pai = 2 (LIXEIRA). Entao eh  */
  /* eh preciso voltar ao modo de visualiza�ao anterior.          */
    echo("    <input type=\"hidden\" name=pag_anterior value=".$pag_anterior.">\n");
  else
    echo("    <input type=\"hidden\" name=pag_anterior value=perguntas>\n");


  echo("              <tr>\n");
  echo("                <td valign=\"top\">\n");
  echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"sortable tabInterna\">\n");
  echo("                    <tr class=\"head\">\n");

  echo("                      <td width=\"2%\" class=\"sorttable_nosort\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></td>\n");

  echo("                      <td class=\"alLeft\" colspan=\"3\" style=\"cursor:pointer\">Assunto</td>");
  echo("                    </tr>");

  /* 67 - N�o h� nenhuma pergunta freq�ente. */
  if((count($lista_assuntos) == 0) && ($cod_assunto_pai == 1))
    echo("  <tr class=\"text\"> <td class=\"text\" colspan=4>".RetornaFraseDaLista($lista_frases,67)."</td></tr>\n");

  $lista_perguntas = ListaTodasPerguntas($sock);
  if (count($lista_perguntas) > 0)
  {
    // a acao a tomar se o usuario clicar no link da pergunta varia entre formador
    // e aluno, lixeira ou nao
    if (!$usr_formador)
    {
      // apenas ver a pergunta
      $acao_link_abre = "<a class=\"text\" href=\"#\" onClick='Ver(";

      // aqui no meio vai o codigo da pergunta a ver
      $acao_link_fecha= ");  return false;'>";

    }
    else if ($cod_assunto_pai != 2)
    {
      $acao_link_abre = "<a class=\"text\" href=\"#\" onClick='selected_item=";

      // aqui no meio vai o codigo da pergunta a ver
      $acao_link_fecha= " ;MostraLayer(lay_pergunta, this);  return false;'>";
    }
    else
    {
      $acao_link_abre = "<a class=\"text\" href=\"#\" onClick='selected_item=";
      // aqui no meio vai o codigo da pergunta a ver
      $acao_link_fecha= "  ;MostraLayer(lay_lixeira_pergunta, this);  return false;'>";
    }

    // Mostra as perguntas:
    foreach ($lista_perguntas as $c => $linha_pergunta)
    {
      $teste_pergunta = RetornaPergunta($sock, $linha_pergunta['cod_pergunta']);
      $questao_pergunta = $teste_pergunta['pergunta'];
      $resposta_pergunta = $teste_pergunta['resposta'];
      echo("      <tr>\n");
      /* Coloca uma caixa de sele�ao para exibi�ao multipla de perguntas */
      echo("        <td width=1%>\n");
      echo("          <input type=\"checkbox\" name=cod_pergunta[] value=".$linha_pergunta['cod_pergunta']." onClick=\"VerificaCheck();\">");
      echo("        </td>\n");

      // Insere a imagem associada aa pergunta 
//      echo("        <td class=\"wtfield\" width=1%><a class=\"text\" href=\"#\" onClick='Ver(");
//      echo($linha_pergunta['cod_pergunta'].");");
//      echo("return(false);'>");
//      echo("<img src=\"../figuras/inter.gif\" border=0></a>\n");
//      echo("        </td>\n");
      // e cria um link nela para o layer
//      echo("        <td class=\"alLeft\"><img border=\"0\" alt=\"\" src=\"../imgs/icEnquete.jpg\"/>&nbsp;&nbsp;".$acao_link_abre.$linha_pergunta['cod_pergunta'].$acao_link_fecha.LimpaTags(TruncaString($linha_pergunta['pergunta'], 80))."</a></td>\n");
      //linha para caminho sem link: echo("        <td colspan=3 class=alLeft><img border=\"0\" alt=\"\" src=\"../imgs/icEnquete.jpg\"/>&nbsp;&nbsp;<a class=text href=\"#\" onClick=ExibirMensagem('".$linha_pergunta['cod_pergunta']."');>".LimpaTags(TruncaString($linha_pergunta['pergunta'], 80))."</a>&nbsp;(".RetornaCaminhoAssunto($sock, $linha_pergunta['cod_assunto']).")</td>\n");
      echo("        <td colspan=3 class=\"alLeft\"><img border=\"0\" alt=\"\" src=\"../imgs/icEnquete.jpg\"/>&nbsp;&nbsp;<a class=\"text\" href=\"#\" onClick=ExibirMensagem('".$linha_pergunta['cod_pergunta']."');>".LimpaTags(TruncaString($linha_pergunta['pergunta'], 80))."</a>&nbsp;(".RetornaLinkCaminhoAssunto($sock, $cod_assunto_pai, $cod_curso, "perguntas").")</td>\n");

      echo("      </tr>\n");
      echo("      <tr style=\"display:none;\" id=\"tr_msg_".$linha_pergunta['cod_pergunta']."\" name=\"tr_msg\"><td>&nbsp;</td>\n");
      echo("        <td align=left><b>".RetornaFraseDaLista($lista_frases, 11).":</b>&nbsp;&nbsp;\n");
      echo("         <div id=\"text_".$linha_pergunta['cod_pergunta']."\" class=\"divRichText\">".$resposta_pergunta."</div></td>\n");
//      echo("         <div id=\"text_".$linha_pergunta['cod_pergunta']."\" class=\"divRichText\" style=\"width:500px;height:100px;overflow:auto;border:1px solid;\";>".$resposta_pergunta."</div></td>\n");

      if ($tela_formador){
        echo("        <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
        echo("             <ul>\n");
        /* 9 - Editar */
        echo("              <li><span onclick=\"AlteraTexto(".$linha_pergunta['cod_pergunta'].");\">".RetornaFraseDaLista($lista_frases_geral,9)."</span></li>\n");
        /* 25 - Mover */
//        echo("              <li><span onclick=MostraLayer(layer_estrutura_mover,".$linha_pergunta['cod_pergunta'].");>".RetornaFraseDaLista($lista_frases_geral,25)."</span></li>\n");
        /*1 - Apagar */
//      echo("              <li><span onclick='Apagar(".$linha_pergunta['cod_pergunta'].", 2);'>".RetornaFraseDaLista($lista_frases_geral,1)."</span></li>\n");
        echo("             </ul>\n");
        echo("        </td>\n");
      }
      echo("        <td><a href=\"#\" onclick=FechaMensagem('".$linha_pergunta['cod_pergunta']."');>".RetornaFraseDaLista($lista_frases_geral,13)."</a></td>\n");
      echo("      </tr>\n");
    }
  }
  else if (count($lista_assuntos) == 0)
  {
    echo("      <tr >\n");

    echo("        <td  colspan=3>\n");
    /* 17 - N�o h� perguntas neste assunto. */
    echo("          <font class=\"text\">".RetornaFraseDaLista($lista_frases, 17)."</font>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
  }

  echo("    </table>\n");
  echo(" <ul>\n");
  /* 16 - Exibir selecionadas ff*/ 
  //if ($cod_assunto_pai != 1) {
    echo("    <li id=\"mExibir_Selec\" class=\"menuUp\"><span name=\"exibir\" onClick=''>".RetornaFraseDaLista($lista_frases,16)."</span></li>\n");
    echo("    <li id=\"mFechar_Selec\" class=\"menuUp\"><span name=\"exibir\" onClick=''>".RetornaFraseDaLista($lista_frases,83)."</span></li>\n");
  //}
  /* 69 - Apagar selecionadas */
  if ($usr_formador){ 
    /* Se não está na lixeira, exibe o botão de Mover */
    if ($cod_assunto_pai != 2){
      echo("    <li id=\"mApagar_Selec\" class=\"menuUp\"><span name=apagar onClick=''>".RetornaFraseDaLista($lista_frases,69)."</span></li>\n");
      echo("    <li id=\"mMover_Selec\" class=\"menuUp\"><span name=apagar onClick=''>".RetornaFraseDaLista($lista_frases,71)."</span></li>\n");
    } else {
      echo("    <li id=\"mExcluir_Selec\" class=\"menuUp\"><span name=apagar onClick=''>".RetornaFraseDaLista($lista_frases,68)."</span></li>\n");
      echo("    <li id=\"mRecuperar_Selec\" class=\"menuUp\"><span name=apagar onClick=''>".RetornaFraseDaLista($lista_frases,72)."</span></li>\n");
    }
  }

  echo(" </ul>\n");

  echo("    </td>");
  echo("  </tr>");
  echo("</table>");

  /* 67 - N�o h� nenhuma pergunta freq�ente. */
//  if( (count($lista_assuntos) == 0) && (count($lista_perguntas) > 0) )
 //       echo("  <tr class=text> <td class=text colspan=4>".RetornaFraseDaLista($lista_frases,67)."</td></tr>\n");




  echo("  </form>\n\n");
    echo("          <br />\n");
    /* 509 - voltar, 510 - topo */
    echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
    echo("  <form name=frmAssuntoAcao method=post>\n");
  /* Passa o 'cod_assunto_pai', necessario para atualizar a pagina */
  /* principal.                                                    */

  //echo(RetornaSessionIDInput());
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");

  echo("    <input type=\"hidden\" name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
  /* Passa o 'cod_assunto', necessario para efetuar as a�oes. */
  echo("    <input type=\"hidden\" name=cod_assunto value=-1>\n");

  if ($usr_formador)
  {
    /* Passa o 'cod_assunto_dest', necessario para mover o assunto. */
    echo("    <input type=\"hidden\" name=cod_assunto_dest value=-1>\n");


    if ($cod_assunto_pai == 2)
      /* Passa o 'cod_assunto_anterior', necessario para se voltar ao */
      /* assunto anterior a visualiza�ao da lixeira.                  */
      echo("    <input type=\"hidden\" name=cod_assunto_anterior value=".$cod_assunto_anterior.">\n");
    else
      echo("    <input type=\"hidden\" name=cod_assunto_anterior value=".$cod_assunto_pai.">\n");


    /* Especifica o documento da pagina principal, o qual chamou o    */
    /* perguntas.php, mas com o cod_assunto_pai = 2 (lixeira). Isto   */
    /* eh necessario para voltar ao modo de visualiza�ao anterior.    */
    if (isset($pagprinc))
    /* Se jah estiver setada entao usa o valor default. Isto eh     */
    /* necessario quando o cod_assunto_pai = 2 (LIXEIRA). Entao eh  */
    /* eh preciso voltar ao modo de visualiza�ao anterior.          */
      echo("    <input type=\"hidden\" name=pag_anterior value=".$pag_anterior.">\n");
    else
      echo("    <input type=\"hidden\" name=pag_anterior value=\"perguntas\">\n");

  }
  echo("  </form>\n\n");



  /* Se o usuario FOR Formador entao cria os layers e os formularios de a�oes. */
  if ($usr_formador)
  {
    echo("  <form name=frmPerguntaAcao method=post>\n");

    //echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");


    /* Passa o 'cod_assunto_pai', necessario para atualizar a pagina  */
    /* principal.                                                     */
    echo("    <input type=\"hidden\" name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
    /* Passa o 'cod_pergunta' para execu�ao das a�oes.                */
    echo("    <input type=\"hidden\" name=cod_pergunta value=-1>\n");
    /* Especifica o documento de origem para 'exibir_todas'. Isto eh  */
    /* necessario, pois tanto 'exibir_todas.php', 'perguntas.php' e   */
    /* 'ver_pergunta.php' chamam a fun�oes apagar, mover, editar,     */
    /* recuperar e excluir.   */
    echo("    <input type=\"hidden\" name=origem value=perguntas>\n");

    /* Especifica o documento da pagina principal, o qual chamou o    */
    /* ver_pergunta.php. Isto eh necessario para atualizar a pagina   */
    /* principal que pode ser perguntas.php ou exibir_todas.php.      */
    if (isset($pagprinc))
      /* Se jah estiver setada entao usa o valor default. Isto eh     */
      /* necessario quando o cod_assunto_pai = 2 (LIXEIRA). Entao eh  */
      /* eh preciso voltar ao modo de visualiza�ao anterior.          */
      echo("    <input type=\"hidden\" name=pag_anterior value=".$pag_anterior.">\n");
    else
      echo("    <input type=\"hidden\" name=pag_anterior value=perguntas>\n");

    if ($cod_assunto_pai == 2)
      /* Passa o 'cod_assunto_anterior', necessario para se voltar ao */
      /* assunto anterior a visualiza�ao da lixeira.                  */
      echo("    <input type=\"hidden\" name=cod_assunto_anterior value=".$cod_assunto_anterior.">\n");
    else
      echo("    <input type=\"hidden\" name=cod_assunto_anterior value=".$cod_assunto_pai.">\n");


    /* Passa o 'cod_assunto_dest', necessario para mover a pergunta.  */
    echo("    <input type=\"hidden\" name=cod_assunto_dest value=-1>\n");
    echo("  </form>\n\n");

////    /* layer_pergunta */
////    echo("  <div id=\"layer_pergunta\" class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
////    echo("    <table bgcolor=#ffffff cellpadding=1 cellspacing=1 border=2>\n");
////    echo("      <tr class=\"bgcolor\">\n");
////    echo("        <td class=\"bgcolor\" align=right>\n");
////    echo("          <a href=\"#\" onClick='EscondeLayer(lay_pergunta);return(false);'>");
////    echo("<img src=../figuras/x.gif border=0></a>\n");
////    echo("        </td>\n");
////    echo("      </tr>\n");
////    echo("      <tr >\n");
////    echo("        <td >\n");
////    /* 21 - Ver */
////    echo("          <a href=\"#\" class=\"text\" onClick='Ver(selected_item);EscondeLayer(lay_pergunta);return(false)'>".RetornaFraseDaLista($lista_frases_geral, 21)."</a><br>\n");
////    /* 9 - Editar */
////    echo("          <a href=\"#\" class=\"text\" onClick='Editar(selected_item,2);return(false);'>".RetornaFraseDaLista($lista_frases_geral, 9)."</a><br>\n");
////    /* 25 - Mover */
////    echo("          <a href=\"#\" class=\"text\" onClick='MostraLayer(lay_estrutura_mover_pergunta, lay_pergunta);  return false;'>".RetornaFraseDaLista($lista_frases_geral, 25)."</a><br>\n");
////    /* 1 - Apagar */
////    echo("          <a href=\"#\" class=\"text\" onClick='Apagar(selected_item,2);return(false);'>".RetornaFraseDaLista($lista_frases_geral, 1)."</a><br>\n");
////    echo("        </td>\n");
////    echo("      </tr>\n");
////    echo("    </table>\n");
////    echo("  </div>\n\n");
////
////    /* layer_lixeira_pergunta */
////    echo("  <div id=\"layer_lixeira_pergunta\" class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
////    echo("    <table bgcolor=#ffffff cellpadding=1 cellspacing=1 border=2>\n");
////    echo("      <tr class=\"bgcolor\">\n");
////    echo("        <td class=\"bgcolor\" align=right>\n");
////    echo("          <a href=\"#\" onClick='EscondeLayer(lay_lixeira_pergunta);return(false);'>");
////    echo("<img src=../figuras/x.gif border=0></a>\n");
////    echo("        </td>\n");
////    echo("      </tr>\n");
////    echo("      <tr >\n");
////    echo("        <td >\n");
////    /* 21 - Ver */
////    echo("          <a href=\"#\" class=\"text\" onClick='Ver(selected_item);");
////    echo("EscondeLayer(lay_lixeira_pergunta);return(false)'>");
////    echo(RetornaFraseDaLista($lista_frases_geral, 21)."</a><br>\n");
////    /* 48 - Recuperar */
////    echo("          <a href=\"#\" class=\"text\" onClick='MostraLayer(lay_estrutura_mover_pergunta, lay_lixeira_pergunta)'>".RetornaFraseDaLista($lista_frases_geral, 48)."</a><br>\n");
////    /* 12 - Excluir */
////    echo("          <a href=\"#\" class=\"text\" onClick='Excluir(selected_item);return(false);'>".RetornaFraseDaLista($lista_frases_geral, 12)."</a><br>\n");
////    echo("        </td>\n");
////    echo("      </tr>\n");
////    echo("    </table>\n");
////    echo("  </div>\n\n");
////
////    if ($cod_assunto_pai != 2)
////    {
////      /* layer_assunto */
////      echo("  <div id=layer_assunto class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
////      echo("    <table bgcolor=#ffffff cellpadding=1 cellspacing=1 border=2>\n");
////      echo("      <tr class=\"bgcolor\">\n");
////      echo("        <td class=\"bgcolor\" align=right>\n");
////      echo("          <a href=\"#\" onClick='EscondeLayer(lay_assunto);return(false);'>");
////      echo("<img src=../figuras/x.gif border=0></a>\n");
////      echo("        </td>\n");
////      echo("      </tr>\n");
////      echo("      <tr >\n");
////      echo("        <td >\n");
////      /* 34 - Abrir */
////      echo("          <a href=\"#\" class=\"text\" onClick='Abrir(selected_item);return(false)'>".RetornaFraseDaLista($lista_frases_geral, 34)."</a><br>\n");
////      /* 9 - Editar */
////      echo("          <a href=\"#\" class=\"text\" onClick='Editar(selected_item,1);return(false);'>".RetornaFraseDaLista($lista_frases_geral, 9)."</a><br>\n");
////      /* 25 - Mover */
////      echo("          <a href=\"#\" class=\"text\" onClick='MostraLayer(lay_estrutura_mover_assunto, lay_assunto)'>".RetornaFraseDaLista($lista_frases_geral, 25)."</a><br>\n");
////      /* 1 - Apagar */
////      echo("          <a href=\"#\" class=\"text\" onClick='Apagar(selected_item,1);return(false);'>".RetornaFraseDaLista($lista_frases_geral, 1)."</a><br>\n");
////      echo("        </td>\n");
////      echo("      </tr>\n");
////      echo("    </table>\n");
////      echo("  </div>\n\n");
////
////      /* layer_estrutura_mover_assunto */
////      echo("  <div id=layer_estrutura_mover_assunto class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
////      echo("    <table bgcolor=#ffffff cellpadding=1 cellspacing=1 border=2>\n");
////      echo("      <tr class=bgcolor>\n");
////      echo("        <td class=bgcolor>\n");
////
////      echo("          <table bgcolor=#ffffff cellpadding=0 cellspacing=0 border=0 width=100%>\n");
////      echo("            <tr class=\"bgcolor\">\n");
////      echo("              <td class=\"bgcolor\" align=left>\n");
////      /* 53 - Mover para: */
////      echo("                <b><font class=\"text\" color=white>".RetornaFraseDaLista($lista_frases, 53)."</font></b>\n");
////      echo("              </td>\n");
////      echo("              <td class=\"bgcolor\" align=right>\n");
////      echo("                <a href=\"#\" onClick='EscondeLayer(lay_estrutura_mover_assunto);return(false);'>");
////      echo("<img src=../figuras/x.gif border=0></a>\n");
////      echo("              </td>\n");
////      echo("            </tr>\n");
////      echo("          </table>\n");
////
////      echo("        </td>\n");
////      echo("      </tr>\n");
////      echo("      <tr >\n");
////      echo("        <td >\n");
////      echo("          ".EstruturaMoverAssunto($sock, $cod_assunto_pai));
////      echo("        </td>\n");
////      echo("      </tr>\n");
////      echo("    </table>\n");
////      echo("  </div>\n\n");
////    }
//
//    /* layer_estrutura_mover_pergunta */
////    echo("  <div id=layer_estrutura_mover_pergunta class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
////    echo("    <table bgcolor=#ffffff cellpadding=1 cellspacing=1 border=2>\n");
////    echo("      <tr class=bgcolor>\n");
////    echo("        <td class=bgcolor align=right>\n");
////
////    echo("          <table bgcolor=#ffffff cellpadding=0 cellspacing=0 border=0 width=100%>\n");
////    echo("            <tr class=bgcolor>\n");
////    echo("              <td class=bgcolor align=left>\n");
////
////    if ($cod_assunto_pai == 2)
////      /* 54 - Recuperar para: */
////      echo("                <b><font class=\"text\" color=white>".RetornaFraseDaLista($lista_frases, 54)."</font></b>\n");
////    else
////      /* 53 - Mover para: */
////      echo("                <b><font class=\"text\" color=white>".RetornaFraseDaLista($lista_frases, 53)."</font></b>\n");
////
////    echo("              </td>\n");
////    echo("              <td class=\"bgcolor\" align=right>\n");
////    echo("                <a href=\"#\" onClick='EscondeLayer(lay_estrutura_mover_pergunta);return(false);'>");
////    echo("<img src=../figuras/x.gif border=0></a>\n");
////    echo("              </td>\n");
////    echo("            </tr>\n");
////    echo("          </table>\n");
////
////    echo("        </td>\n");
////    echo("      </tr>\n");
////    echo("      <tr >\n");
////    echo("        <td >\n");
////    echo("          ".EstruturaMoverPergunta($sock, $cod_assunto_pai));
////    echo("        </td>\n");
////    echo("      </tr>\n");
////    echo("    </table>\n");
////    echo("  </div>\n\n");
////
  }

  /* Layer: Estrutura */
  echo("  <div id=\"layer_estrutura\" class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
  echo("    <div class=\"posX\"><span onclick=\"EscondeLayer(layer_estrutura);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <div class=\"ulPopup\">\n");

  echo("          ".EstruturaDeAssuntos($sock, $cod_assunto_pai, $usr_formador));

  echo("        </div>\n");
  echo("      </div>\n");
  echo("  </div>\n\n");

  /* Layer: Estrutura-Mover */
  echo("  <div id=\"layer_estrutura_mover\" class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
  echo("    <div class=\"posX\"><span onclick=\"EscondeLayer(layer_estrutura_mover);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <div class=\"ulPopup\">\n");

  echo("          ".EstruturaMoverAssunto($sock, $cod_assunto_pai, $usr_formador));

  echo("        </div>\n");
  echo("      </div>\n");
  echo("  </div>\n\n");




  include("../tela2.php");

  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
