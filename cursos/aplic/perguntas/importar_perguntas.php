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

// @todo Adaptar a fun��o de navega��o de pastas


  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");
  
  $cod_ferramenta = 6;
  $tabela = "Pergunta";
  
  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  $objPerguntas = new xajax();
  $objPerguntas->configure("characterEncoding", 'ISO-8859-1');
  $objPerguntas->configure('javascript URI', "../xajax_0.5");
  $objPerguntas->register(XAJAX_FUNCTION,"EditarTexto");
//  $objPerguntas->register(XAJAX_FUNCTION,"AcabaEdicaoDinamic");
  $objPerguntas->processRequests();
  
  
  include("../topo_tela.php");
  
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  //adicionar as acoes possiveis, 1o parametro é
  $feedbackObject->addAction("apagarItem", 76, 0);
  $feedbackObject->addAction("recuperarPergunta", 77, 0);
  $feedbackObject->addAction("moverItem", 78, 0);
  $feedbackObject->addAction("excluirItem", 79, 0);
  
  /* Verifica se o usuario eh formador. */
  if (EFormador($sock, $cod_curso, $cod_usuario))
    $usr_formador = true;
  else
    $usr_formador = false;

  $sock = MudarDB($sock, $cod_curso_origem);
  /* Se o cod_assunto_pai NAO estiver definido OU NAO existir o assunto */
  /* entao define-o para o assunto-raiz.                                */
  if (!isset($cod_assunto_pai) || !ExisteAssunto($sock, $cod_assunto_pai))
  /* Lista os assuntos do assunto raiz */
    $cod_assunto_pai = 1;


  echo("<script type=\"text/javascript\" language=\"javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("<script language=\"javascript\">\n\n");

  echo("  img_icone = new Image();\n");
  echo("  img_icone.src = \"../figuras/assunto.gif\";\n\n");
  
  echo("  var existelayer = false; ");
  echo("  var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("  var versao = (navigator.appVersion.substring(0,3));\n");
  echo("  var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");

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
    echo("    layer_estrutura_mover = getLayer('layer_estrutura_mover');\n");
    echo("    layer_estrutura_recuperar = getLayer('layer_estrutura_recuperar');\n");
    echo("    layer_novo_assunto = getLayer('layer_novo_assunto');\n");  
    echo("    layer_nova_pergunta = getLayer('layer_nova_pergunta');\n");  
  }
  echo("        var atualizacao = '".$_GET['atualizacao']."';\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("  EscondeLayers();\n");
  echo("  }\n\n");

  echo("  function VerificaCheck(){\n");
  echo("    var cod_assunto = document.getElementsByName('cod_assunto[]');\n");
  echo("    var cod_pergunta = document.getElementsByName('cod_pergunta[]');\n");
  echo("    \n");		
  /* Se tiver ao menos 1 checkbox, seja assunto ou */
  /* pergunta tickado, mostra os botoes */
  echo("    var i = 0;\n");
  echo("    for (i = 0; i < cod_assunto.length; i++)\n");
  echo("      if (cod_assunto[i].checked){\n");
  echo("        return HabilitaBotoes();\n");
  echo("      }\n");
  echo("    \n");
  echo("    i = 0;\n");
  echo("    for (i = 0; i < cod_pergunta.length; i++)\n");
  echo("      if (cod_pergunta[i].checked){\n");
  echo("        return HabilitaBotoes();\n");
  echo("      }\n");
  echo("    \n");
  echo("    return DesabilitaBotoes();\n");
  echo("  }\n\n");
  
  echo("  var PerguntasAbertas = 0;\n");
  echo("  function ExibirSelecionadas(){\n");
  echo("    var Perguntas = document.getElementsByName('cod_pergunta[]');\n");
  echo("    var nPerguntas = Perguntas.length;\n");
  echo("    var i = 0;\n");
  echo("    \n");
  echo("      for (i = 0; i < nPerguntas; i++)\n");
  echo("        if (Perguntas[i].checked){\n");
  echo("          ExibirMensagem(Perguntas[i].value);\n");
  echo("        }\n");
  echo("    }\n\n");
  
  echo("  function FecharSelecionadas(){\n");
  echo("    var Perguntas = document.getElementsByName('cod_pergunta[]');\n");
  echo("    var nPerguntas = Perguntas.length;\n");
  echo("    var i = 0;\n");
  echo("    \n");
  echo("      for (i = 0; i < nPerguntas; i++)\n");
  echo("        if (Perguntas[i].checked){\n");
  echo("          FechaMensagem(Perguntas[i].value);\n");
  echo("        }\n");
  echo("    \n");
  echo("}\n\n");
  
  echo("  function HabilitaBotoes(){\n");
  echo("        if (PerguntasAbertas > 0) HabilitaBotaoFechar();");
  if ($cod_assunto_pai != 1){
  echo("        document.getElementById('mExibir_Selec').className=\"menuUp02\";");
  echo("        document.getElementById('mExibir_Selec').onclick=function(){ ExibirSelecionadas(); };\n");
  }
  echo("        document.getElementById('mImportar_Selec').className=\"menuUp02\";");
  echo("        document.getElementById('mImportar_Selec').onclick=function(){ ImportarSelecionadas(); };\n");
  echo("  }\n");
  
  echo("  function HabilitaBotaoFechar(){\n");
  echo("        document.getElementById('mFechar_Selec').className=\"menuUp02\";");
  echo("        document.getElementById('mFechar_Selec').onclick=function(){ FecharSelecionadas(); };\n");
  echo("  }\n");
  
  echo("  function DesabilitaBotoes(){\n");
  if ($cod_assunto_pai != 1){
  echo("        document.getElementById('mExibir_Selec').className=\"menuUp\";");
  echo("        document.getElementById('mExibir_Selec').onclick=function(){};\n");
  echo("        DesabilitaBotaoFechar();");
  }
  echo("        document.getElementById('mImportar_Selec').className=\"menuUp\";");
  echo("        document.getElementById('mImportar_Selec').onclick=function(){};\n");
  echo("  }\n");
  
  echo("  function DesabilitaBotaoFechar(){\n");
  echo("    document.getElementById('mFechar_Selec').className=\"menuUp\";");
  echo("    document.getElementById('mFechar_Selec').onclick=function(){};\n");
  echo("  }\n");
  
  
  echo("  function EscondeLayer(cod_layer)\n");
  echo("  {\n");
  echo("    hideLayer(cod_layer);\n");
  echo("  }\n\n");

  echo("  function EscondeLayers()\n");
  echo("  {\n");
  echo("    hideLayer(layer_estrutura);\n");
  if ($usr_formador)
  {
    echo("    hideLayer(layer_novo_assunto);\n");
    echo("    hideLayer(layer_estrutura_mover);\n");
    echo("    hideLayer(layer_estrutura_recuperar);\n");
    echo("    hideLayer(layer_nova_pergunta);\n");
  }
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

  echo("  function Abrir(id)\n");
  echo("  {\n");
  echo("    document.frmAssuntoAcao.action='importar_perguntas.php?cod_curso=".$cod_curso."&cod_ferramenta=6&cod_curso_origem=".$cod_curso_origem."';\n");
  echo("    document.frmAssuntoAcao.cod_assunto_pai.value = id;\n");
  echo("    document.frmAssuntoAcao.submit();\n");
  echo("  }\n\n");
  
//fun��o que talvez num precise mais
/*  echo("  function MostrarSelecionadas()\n");
  echo("  {\n");
  echo("    verificador=Validacheck();");
  echo("    if(verificador==true)\n");
  echo("    {\n");
  echo("      window.open('','pergunta','width=600,height=400,top=50,left=50,scrollbars=yes,");
  echo("status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("    }\n");
  echo("  }\n\n");*/
  
  echo("  function Envia(assunto)\n");
  echo("  {\n");
  echo("    verificador=Validacheck();\n");
  echo("    if(verificador==true)\n");
  echo("    {\n");
  echo("      window.open('','pergunta','width=600,height=400,top=50,left=50,scrollbars=yes,");
  echo("status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("      if(assunto==2)\n");
  echo("        document.frm_pergunta.action = \"ver_pergunta_lixeira.php\";\n");
  echo("      else\n");
  echo("        document.frm_pergunta.action = \"ver_pergunta.php?cod_curso=".$cod_curso."\";\n");
  echo("        document.frm_pergunta.target = 'pergunta';\n");
  echo("        document.frm_pergunta.submit();\n");
  echo("      return true;\n");
  echo("    }\n");
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
  echo("     return true;\n");
  echo("     }\n");
  echo("     else\n");
  echo("     {\n");
  echo("     alert('".RetornaFraseDaLista($lista_frases, 50)."');\n");
  echo("     return false;\n");
  echo("     }\n");
  echo("  }\n");


  echo("  function MarcaOuDesmarcaTodos()\n");
  echo("  {\n");
  echo("    var e;\n");
  echo("    var CabecalhoMarcado=document.frm_pergunta.checkMenu.checked;\n");
  echo("    for (var i=0;i<document.frm_pergunta.elements.length;i++)\n");
  echo("    {\n");
  echo("      e = document.frm_pergunta.elements[i];\n");
  echo("      if (e.name=='cod_assunto[]' || e.name=='cod_pergunta[]')\n");
  echo("      {\n");
  echo("        e.checked=CabecalhoMarcado;\n");
  echo("      }\n");
  echo("    }\n");
  echo("  }\n");
  
  echo("      function ExibirMensagem(cod_mural)\n");
  echo("      {\n");
  echo("        PerguntasAbertas++;\n");
  echo("        VerificaCheck();");
  echo("        var browser=navigator.appName;\n\n");
  echo("        var totalMsgs=document.getElementsByName('tr_msg').length;\n");
  echo("        var vLink = document.getElementById('tr_msg_'+cod_mural);\n");

  echo("        if((vLink.style.display == 'table-row') || (vLink.style.display == 'block'))");
  echo("        {");
  echo("          vLink.style.display='none';");
//  echo("          mensagens_abertas--;");
  echo("        }");
  echo("        else");
  echo("        {");
  echo("          if (browser==\"Microsoft Internet Explorer\")\n");
  echo("            vLink.style.display=\"block\";\n");
  echo("          else\n");
  echo("            vLink.style.display=\"table-row\";\n");
//  echo("        mensagens_abertas++;\n");
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

  
  echo("  function ImportarSelecionadas(){");
  echo("        document.frm_pergunta.action ='acoes.php';\n");
  echo("        document.frm_pergunta.acao.value = \"importarItem\";\n");
  echo("        document.frm_pergunta.submit();\n");
  echo("  }\n");

  echo("</script>\n\n");

  $objPerguntas->printJavascript();

  include("../menu_principal.php");
  $sock = MudarDB($sock, $cod_curso_origem);
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  // 1 - "Perguntas"
  $cabecalho = ("          <h4>" . RetornaFraseDaLista($lista_frases, 1));
  /* 58 - Importando perguntas */
  $cabecalho .= (" - " . RetornaFraseDaLista($lista_frases, 58) . "</h4>\n");
  echo ($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");
  
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
    
  /* 1 - Perguntas Freq�entes */
  $cabecalho = "  <b class=\"titulo\">".RetornaFraseDaLista($lista_frases,1)."</b>";

  //echo("  <br>\n");
  
  echo("  <span class=\"btsNav2\"><a href=\"#\" onClick='MostraLayer(layer_estrutura,this);return(false);'><img src=\"../imgs/estrutura.gif\" border=0></a>\n");
  //echo("  <a href=\"#\" onMouseDown='MostraLayer(lay_estrutura,0);return(false);'><img src=../figuras/estrutura.gif border=0></a>\n");
  echo("    <font class=\"text\">".RetornaLinkCaminhoAssunto($sock, $cod_assunto_pai, $cod_curso_origem, "perguntas"));
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
    echo("      <td valign=top width=1% class=\"textsmall\"><i>".RetornaFraseDaLista($lista_frases, 6));
    echo("</i>:</td>\n");
    echo("      <td class=\"textsmall\">\n");
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
	  /* 23 - Voltar */
      echo("      <li><span onClick=\"history.go(-1);\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>");

  }

  if ($usr_formador)
  {
  echo("    <input type=\"hidden\" name=\"origem\" value=\"perguntas\">\n");
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
  
  echo("  <form method=\"post\" name=\"frm_pergunta\" target='pergunta'>");

  //echo(RetornaSessionIDInput());
  echo("    <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
  echo("    <input type=\"hidden\" name=\"acao\" value=\"\">\n");

  echo("    <input type=\"hidden\" name=\"cod_assunto_pai\"  value=\"".$cod_assunto_pai."\">\n");
  echo("    <input type=\"hidden\" name=\"cod_assunto_dest\" value=\"\">\n");
  /* Especifica o documento da pagina principal, o qual chamou o    */
  /* ver_pergunta.php. Isto eh necessario para atualizar a pagina   */
  /* principal que pode ser perguntas.php ou exibir_todas.php.      */
  echo("    <input type=\"hidden\" name=\"pagprinc\" value=\"perguntas\">\n");

  if ($cod_assunto_pai == 2)
    /* Passa o 'cod_assunto_anterior', necessario para se voltar ao */
    /* assunto anterior a visualiza�ao da lixeira.                  */
    echo("  <input type=\"hidden\" name=\"cod_assunto_anterior\" value=\"".$cod_assunto_anterior."\">\n");
  else
    echo("  <input type=\"hidden\" name=\"cod_assunto_anterior\" value=\"".$cod_assunto_pai."\">\n");

  /* Especifica o documento da pagina principal, o qual chamou o    */
  /* perguntas.php, mas com o cod_assunto_pai = 2 (lixeira). Isto   */
  /* eh necessario para voltar ao modo de visualiza�ao anterior.    */
  if (isset($pagprinc))
  /* Se jah estiver setada entao usa o valor default. Isto eh     */
  /* necessario quando o cod_assunto_pai = 2 (LIXEIRA). Entao eh  */
  /* eh preciso voltar ao modo de visualiza�ao anterior.          */
    echo("    <input type=\"hidden\" name=\"pag_anterior\" value=\"".$pag_anterior."\">\n");
  else
    echo("    <input type=\"hidden\" name=\"pag_anterior\" value=\"perguntas\">\n");


  echo("              <tr>\n");
  echo("                <td valign=\"top\">\n");
  echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");  
  echo("                    <tr class=\"head\">\n");
  
  echo("                      <td width=\"2%\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"MarcaOuDesmarcaTodos();VerificaCheck();\" /></td>\n");

  /* 89 - Selecionar todos */
  echo("                      <td class=alLeft colspan=\"3\">".RetornaFraseDaLista($lista_frases_geral,89)."</td>");
  echo("                    </tr>");
  
  /* 67 - N�o h� nenhuma pergunta freq�ente. */
  if((count($lista_assuntos) == 0) && ($cod_assunto_pai == 1))
     echo("  <tr class=\"text\"> <td class=\"text\" colspan=4>".RetornaFraseDaLista($lista_frases,67)."</td></tr>\n");

  // Mostra os assuntos (pastas e sub-pastas)
  $contador = 0;
  if (count($lista_assuntos) > 0)
    foreach ($lista_assuntos as $cod => $linha_assunto)
    {
      $contador++;


      if (!$usr_formador)
      {
        /* Se NAO for o formador cria links para SOMENTE abrir os assuntos. */
        /* Insere o nome do assunto truncado para acima de 40 caracteres e */
        /* cria um link para o menu.                                       */
        echo("        <td width=1%>\n");
        echo("          <input type=\"checkbox\" name=cod_assunto[] value=".$linha_assunto['cod_assunto']." onClick=\"VerificaCheck();\">");
        echo("        </td>\n");

        echo("        <td colspan=3 class=\"alLeft\"><img border=\"0\" alt=\"\" src=\"../imgs/pasta.gif\"/>&nbsp;&nbsp;<a class=\"text\" href=\"#\" onClick=");
        echo("'Abrir(".$linha_assunto['cod_assunto'].");return(false);'>");
        echo(TruncaString($linha_assunto['nome'], 80)."</a>\n");

      }
      else
      {
//        echo("      <tr>\n");
//        /* Apenas para alinhamento */
//        echo("        <td class=\"wtfield\" width=1%><a class=\"text\" href=\"#\" onClick=");
//        echo("'Abrir(".$linha_assunto['cod_assunto'].");  return(false);'>");
//        echo("<img src=\"../figuras/assunto.gif\" border=0></a>");
//        echo("        </td>\n");
        echo("      <tr >\n");
        /* Coloca uma caixa de sele�ao para exibi�ao multipla de perguntas */
        echo("        <td  width=1%>\n");
        echo("          <input type=\"checkbox\" name=cod_assunto[] value=\"".$linha_assunto['cod_assunto']."\" onClick=\"VerificaCheck();\">");
        echo("        </td>\n");

        /* Se for Formador oferece exibi�ao de op�oes. */
        /* Insere o nome do assunto truncado para acima de 40 caracteres e */
        /* cria um link para o menu.                                       */
        echo("        <td colspan=3 class=\"alLeft\"><img border=\"0\" alt=\"\" src=\"../imgs/pasta.gif\"/>&nbsp;&nbsp;<a class=text href=# onClick='");
        echo("selected_item=".$linha_assunto['cod_assunto'].";");
        echo("Abrir(".$linha_assunto['cod_assunto'].");return(false);'>");
        echo(TruncaString($linha_assunto['nome'], 80)."</a>\n");
      }
//      echo($linha_assunto['cod_assunto']);
      echo("<font class=\"text\"><i>(".RetornaNumPerguntasAssunto($sock, $linha_assunto['cod_assunto']).")</i></font>");
      echo("        </td>\n");
      echo("      </tr>\n");
      }

  /* Se o assunto pai NAO for a raiz entao lista as perguntas */
	if ($cod_assunto_pai != 1)
  {
    $lista_perguntas = ListaPerguntas($sock, $cod_assunto_pai);
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
        echo("          <input type=\"checkbox\" name=cod_pergunta[] value=\"".$linha_pergunta['cod_pergunta']."\" onClick=\"VerificaCheck();\">");
        echo("        </td>\n");

//        // Insere a imagem associada aa pergunta 
//        echo("        <td class=\"wtfield\" width=1%><a class=\"text\" href=\"#\" onClick='Ver(");
//        echo($linha_pergunta['cod_pergunta'].");");
//        echo("return(false);'>");
//        echo("<img src=\"../figuras/inter.gif\" border=0></a>\n");
//        echo("        </td>\n");
//        // e cria um link nela para o layer
//        echo("        <td class=\"alLeft\"><img border=\"0\" alt=\"\" src=\"../imgs/icEnquete.jpg\"/>&nbsp;&nbsp;".$acao_link_abre.$linha_pergunta['cod_pergunta'].$acao_link_fecha.LimpaTags(TruncaString($linha_pergunta['pergunta'], 80))."</a></td>\n");
        echo("        <td colspan=3 class=\"alLeft\"><img border=\"0\" alt=\"\" src=\"../imgs/icEnquete.jpg\"/>&nbsp;&nbsp;<a class=\"text\" href=\"#\" onClick=ExibirMensagem('".$linha_pergunta['cod_pergunta']."');>".LimpaTags(TruncaString($linha_pergunta['pergunta'], 80))."</a></td>\n");
        
        echo("      </tr>\n");
        echo("      <tr style=\"display:none;\" id=\"tr_msg_".$linha_pergunta['cod_pergunta']."\" name=\"tr_msg\"><td>&nbsp;</td>\n");
        echo("        <td align=left><b>".RetornaFraseDaLista($lista_frases, 11).":</b>&nbsp;&nbsp;\n");
        echo("         <div id=\"text_".$linha_pergunta['cod_pergunta']."\" class=\"divRichText\">".$resposta_pergunta."</div></td>\n");
//        echo("         <div id=\"text_".$linha_pergunta['cod_pergunta']."\" class=\"divRichText\" style=\"width:500px;height:100px;overflow:auto;border:1px solid;\";>".$resposta_pergunta."</div></td>\n");
        
        echo("			<td><a href=\"#\" onclick=FechaMensagem('".$linha_pergunta['cod_pergunta']."');>".RetornaFraseDaLista($lista_frases_geral,13)."</a></td>\n");
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
  }
  
  echo("    </table>\n");
    echo("  <ul>\n");
    /* 16 - Exibir selecionadas */
    if ($cod_assunto_pai != 1) {
    echo("    <li id=\"mExibir_Selec\" class=\"menuUp\"><span name=\"exibir\" onClick=''>".RetornaFraseDaLista($lista_frases,16)."</span></li>\n");
    echo("    <li id=\"mFechar_Selec\" class=\"menuUp\"><span name=\"exibir\" onClick=''>".RetornaFraseDaLista($lista_frases,83)."</span></li>\n");
    }
    echo("    <li id=\"mImportar_Selec\" class=\"menuUp\"><span name=\"exibir\" onClick=''>".RetornaFraseDaLista($lista_frases,85)."</span></li>\n");
    echo("  </ul>\n");
  
  echo("    </td>");
  echo("  </tr>");
  echo("</table>");

//  /* 67 - N�o h� nenhuma pergunta freq�ente. */
//  if( (count($lista_assuntos) == 0) && (count($lista_perguntas) > 0) )
//    echo("  <tr class=\"text\"> <td class=\"text\" colspan=4>".RetornaFraseDaLista($lista_frases,67)."</td></tr>\n");

  
  
  
  echo("  </form>\n\n");
    echo("          <br />\n");    
    /* 509 - voltar, 510 - topo */
    echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
    echo("  <form name=\"frmAssuntoAcao\" method=\"post\">\n");
  /* Passa o 'cod_assunto_pai', necessario para atualizar a pagina */
  /* principal.                                                    */
 
  //echo(RetornaSessionIDInput());
  echo("    <input type=\"hidden\" name=\"cod_curso\"       value=\"".$cod_curso."\">\n");

  echo("    <input type=\"hidden\" name=\"cod_assunto_pai\" value=\"\">\n");
  /* Passa o 'cod_assunto', necessario para efetuar as a�oes. */
  echo("    <input type=\"hidden\" name=\"cod_assunto\"     value=\"-1\">\n");

  if ($usr_formador)
  {
    /* Passa o 'cod_assunto_dest', necessario para mover o assunto. */
    echo("    <input type=\"hidden\" name=\"cod_assunto_dest\" value=\"-1\">\n");


    if ($cod_assunto_pai == 2)
      /* Passa o 'cod_assunto_anterior', necessario para se voltar ao */
      /* assunto anterior a visualiza�ao da lixeira.                  */
      echo("    <input type=\"hidden\" name=\"cod_assunto_anterior\" value=\"".$cod_assunto_anterior."\">\n");
    else
      echo("    <input type=\"hidden\" name=\"cod_assunto_anterior\" value=\"".$cod_assunto_pai."\">\n");


    /* Especifica o documento da pagina principal, o qual chamou o    */
    /* perguntas.php, mas com o cod_assunto_pai = 2 (lixeira). Isto   */
    /* eh necessario para voltar ao modo de visualiza�ao anterior.    */
    if (isset($pagprinc))
    /* Se jah estiver setada entao usa o valor default. Isto eh     */
    /* necessario quando o cod_assunto_pai = 2 (LIXEIRA). Entao eh  */
    /* eh preciso voltar ao modo de visualiza�ao anterior.          */
      echo("    <input type=\"hidden\" name=\"pag_anterior\" value=\"".$pag_anterior."\">\n");
    else
      echo("    <input type=\"hidden\" name=\"pag_anterior\" value=\"perguntas\">\n");

  }
  echo("  </form>\n\n");



  /* Se o usuario FOR Formador entao cria os layers e os formularios de a�oes. */
  if ($usr_formador)
  {
    echo("  <form name=\"frmPerguntaAcao\" method=\"post\">\n");

    //echo(RetornaSessionIDInput());
    echo("    <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");


    /* Passa o 'cod_assunto_pai', necessario para atualizar a pagina  */
    /* principal.                                                     */
    echo("    <input type=\"hidden\" name=\"cod_assunto_pai\" value=\"".$cod_assunto_pai."\">\n");
    /* Passa o 'cod_pergunta' para execu�ao das a�oes.                */
    echo("    <input type=\"hidden\" name=\"cod_pergunta\"    value=\"-1\">\n");
    /* Especifica o documento de origem para 'exibir_todas'. Isto eh  */
    /* necessario, pois tanto 'exibir_todas.php', 'perguntas.php' e   */
    /* 'ver_pergunta.php' chamam a fun�oes apagar, mover, editar,     */
    /* recuperar e excluir.   */
    echo("    <input type=\"hidden\" name=\"origem\" value=\"perguntas\">\n");

    /* Especifica o documento da pagina principal, o qual chamou o    */
    /* ver_pergunta.php. Isto eh necessario para atualizar a pagina   */
    /* principal que pode ser perguntas.php ou exibir_todas.php.      */
    if (isset($pagprinc))
      /* Se jah estiver setada entao usa o valor default. Isto eh     */
      /* necessario quando o cod_assunto_pai = 2 (LIXEIRA). Entao eh  */
      /* eh preciso voltar ao modo de visualiza�ao anterior.          */
      echo("    <input type=\"hidden\" name=\"pag_anterior\" value=".$pag_anterior.">\n");
    else
      echo("    <input type=\"hidden\" name=\"pag_anterior\" value=\"perguntas\">\n");

    if ($cod_assunto_pai == 2)
      /* Passa o 'cod_assunto_anterior', necessario para se voltar ao */
      /* assunto anterior a visualiza�ao da lixeira.                  */
      echo("    <input type=\"hidden\" name=\"cod_assunto_anterior\" value=\"".$cod_assunto_anterior."\">\n");
    else
      echo("    <input type=\"hidden\" name=\"cod_assunto_anterior\" value=\"".$cod_assunto_pai."\">\n");


    /* Passa o 'cod_assunto_dest', necessario para mover a pergunta.  */
    echo("    <input type=\"hidden\" name=\"cod_assunto_dest\" value=\"-1\">\n");
    echo("  </form>\n\n");
////
  }
  /* Layer: Nova Pergunta */
  echo("    <div id=\"layer_nova_pergunta\" class=\"popup\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(layer_nova_pergunta);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup ulPopup\">\n");
  echo("        <form name=\"form_novo_top\" method=\"post\" action=\"acoes.php\" onsubmit=\"return(VerificaNovoItemTopico(document.form_novo_top.novo_nome)); \">\n");
  echo("          <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
  echo("          <input type=\"hidden\" name=\"cod_assunto_pai\" value=\"".$cod_assunto_pai."\">\n");
  echo("          <input type=\"hidden\" name=\"acao\" value=\"novaPergunta\">\n");
  /* 21 - Digite o nome da pasta a ser criada aqui: */
  echo("          ".RetornaFraseDaLista($lista_frases,21)."<br />\n");
  echo("          <input class=\"input\" type=\"text\" name=\"novo_nome\" id=\"nome_novo_pergunta\" value=\"\" maxlength=\"150\" /><br />\n");
  /* 18 - Ok (gen) */
  echo("          <input class=\"input\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  /* 2 - Cancelar (gen) */
  echo("          &nbsp; &nbsp; <input class=\"input\" type=\"button\" onclick=\"EscondeLayer(layer_nova_pergunta);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  echo("        </form>\n");
  echo("      </div>\n");
  echo("    </div>\n");

  /* Layer: Novo Assunto */
  echo("    <div id=\"layer_novo_assunto\" class=\"popup\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(layer_novo_assunto);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup ulPopup\">\n");
  echo("        <form name=\"form_novo_top\" method=\"post\" action=\"acoes.php\" onsubmit=\"return (VerificaNovoItemTopico(document.form_novo_top.novo_nome));\">\n");
  echo("          <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
  echo("          <input type=\"hidden\" name=\"cod_assunto_pai\" value=\"".$cod_assunto_pai."\">\n");
  echo("          <input type=\"hidden\" name=\"acao\" value=\"novoAssunto\">\n");
  /* 21 - Digite o nome da pasta a ser criada aqui: */
  echo("          ".RetornaFraseDaLista($lista_frases,84)."<br />\n");
  echo("          <input class=\"input\" type=\"text\" name=\"novo_nome\" id=\"nome_novo_assunto\" value=\"\" maxlength=\"150\" /><br />\n");
  /* 18 - Ok (gen) */
  echo("          <input class=\"input\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  /* 2 - Cancelar (gen) */
  echo("          &nbsp; &nbsp; <input class=\"input\" type=\"button\" onclick=\"EscondeLayer(layer_novo_assunto);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  echo("        </form>\n");
  echo("      </div>\n");
  echo("    </div>\n");

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
  
    /* Layer: Estrutura-Recuperar */
  echo("  <div id=\"layer_estrutura_recuperar\" class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
  echo("    <div class=\"posX\"><span onclick=\"EscondeLayer(layer_estrutura_recuperar);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <div class=\"ulPopup\">\n");

  echo("          ".EstruturaRecuperarAssunto($sock, 0, $usr_formador));

  echo("        </div>\n");
  echo("      </div>\n");
  echo("  </div>\n\n");
  
  
  include("../tela2.php");
  
  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
