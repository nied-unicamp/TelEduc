<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perguntas/exibir_todas.php

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
  ARQUIVO : cursos/aplic/perguntas/exibir_todas.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");

  $cod_ferramenta = 6;  
  include("../topo_tela.php");

  /* Verifica se o usuario eh formador. */
  if (EFormador($sock, $cod_curso, $cod_usuario))
    $usr_formador = true;
  else
    $usr_formador = false;

  /* Se o cod_assunto_pai NAO estiver definido OU NAO existir o assunto */
  /* entao define-o para o assunto-raiz.                                */
  if (!isset($cod_assunto_pai) || !ExisteAssunto($sock, $cod_assunto_pai))
  /* Lista os assuntos do assunto raiz */
    $cod_assunto_pai = 1;

  echo("<script language=JavaScript src=../bibliotecas/dhtmllib.js></script>\n");
  echo("<script language=JavaScript>\n\n");
  

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
  echo("    lay_estrutura = getLayer('layer_estrutura');\n");
  if ($usr_formador)
  {
    echo("    lay_estrutura_mover_pergunta = getLayer('layer_estrutura_mover_pergunta');\n");
    if ($cod_assunto_pai != 2)
    {
      echo("    lay_estrutura_mover_assunto = getLayer('layer_estrutura_mover_assunto');\n");
      echo("    lay_assunto = getLayer('layer_assunto');\n");
    }
    echo("    lay_lixeira_pergunta = getLayer('layer_lixeira_pergunta');\n");
    echo("    lay_pergunta = getLayer('layer_pergunta');\n");
    
  }
  echo("  EscondeLayers();\n");
  echo("  }\n\n");

  echo("  function EscondeLayer(cod_layer)\n");
  echo("  {\n");
  echo("    hideLayer(cod_layer);\n");
  echo("  }\n\n");

  echo("  function EscondeLayers()\n");
  echo("  {\n");
  echo("    hideLayer(lay_estrutura);\n");
  if ($usr_formador)
  {
    echo("    hideLayer(lay_estrutura_mover_pergunta);\n");
    if ($cod_assunto_pai != 2)
    {
      echo("    hideLayer(lay_estrutura_mover_assunto);\n");
      echo("    hideLayer(lay_assunto);\n");
    }
    echo("    hideLayer(lay_lixeira_pergunta);\n");
    echo("    hideLayer(lay_pergunta);\n");
  }
  echo("  }\n\n");

  echo("  img_icone = new Image();\n");
  echo("  img_icone.src = \"../figuras/assunto.gif\";\n\n");

  echo("\n  function MostraLayer(cod_layer, obj)\n");
  echo("  {\n");
  echo("    EscondeLayers();\n");
    echo("existelayer=true;");
  /* Se o browser for Netscape alinhe com a link. */
  echo("    if ((isNav) && (versao<'5.0'))\n");
  echo("    {\n");
  /* Se for a estrutura de assuntos entao desloca um pouco mais aa direita */
  /* senao o layer ficarah atras das checkboxs das perguntas.              */
  echo("      if (cod_layer == lay_estrutura)\n");
  echo("        moveLayerTo(cod_layer, obj.x + img_icone.height, obj.y + img_icone.height);\n");
  echo("      else\n");
   echo("    {\n");
  echo("        moveLayerTo(cod_layer, obj.x , obj.y + img_icone.height);\n");
   echo("    }\n");
  echo("    }\n");
  echo("    else\n");
  echo("      moveLayerTo(cod_layer, Xpos, Ypos + AjustePosMenuIE());\n");
  echo("    showLayer(cod_layer);\n");
  echo("  }\n\n");

  echo("  function Ver(id)\n");
  echo("  {\n");

  $doc_ver = ($cod_assunto_pai == 2) ? "ver_pergunta_lixeira" : "ver_pergunta";

  echo("    window.open('".$doc_ver.".php?cod_curso=");
  echo($cod_curso."&cod_assunto_pai=".$cod_assunto_pai."&check[]=' + id + '");
  echo("&pagprinc=perguntas&cod_assunto_anterior=".$cod_assunto_anterior);
  echo("&pag_anterior=".$pag_anterior."', 'pergunta', 'width=600,height=400,top=50,");
  echo("left=50,scrollbars=yes,status=yes,toolbar=no,menubar=no,");
  echo("resizable=yes');\n");

  echo("    return(false);\n");
  echo("  }\n\n");

  echo("  function Abrir(id)\n");
  echo("  {\n");
  echo("    document.frmAssuntoAcao.action='perguntas.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=6';\n");
  echo("    document.frmAssuntoAcao.cod_assunto_pai.value = id;\n");
  echo("    document.frmAssuntoAcao.submit();\n");
  echo("  }\n\n");
 //fun��o que talvez num precise mais
/*  echo("  function MostrarSelecionadas()\n");
  echo("  {\n");
   echo(" verificador=Validacheck();");
   echo("if(verificador==true)\n");
    echo("    {\n");
       echo("    window.open('','pergunta','width=600,height=400,top=50,left=50,scrollbars=yes,");
       echo("status=yes,toolbar=no,menubar=no,resizable=yes');\n");
    echo("    }\n");
  echo("  }\n\n");   */


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
    echo("        document.frmAssuntoAcao.action='apagar_assunto.php';\n");
    echo("        document.frmAssuntoAcao.cod_assunto.value = id;\n");
    echo("        document.frmAssuntoAcao.submit();\n");
    echo("      }\n");
    echo("    }\n");
    echo("    else if (tipo == 2)\n");
    echo("    {\n");
    /* 21 - Tem certeza que deseja apagar esta pergunta? */
    echo("      if (confirm('".RetornaFraseDaLista($lista_frases,21)."'))\n");
    echo("      {\n");
    echo("        document.frmPerguntaAcao.action='apagar_pergunta.php';\n");
    echo("        document.frmPerguntaAcao.cod_pergunta.value = id;\n");
    echo("        document.frmPerguntaAcao.submit();\n");
    echo("      }\n");
    echo("    }\n");
    echo("  }\n\n");

    echo("  function Excluir(id)\n");
    echo("  {\n");
    /* 43 - Deseja excluir definitivamente esta pergunta? */
    echo("    if (confirm('".RetornaFraseDaLista($lista_frases, 43)."'))\n");
    echo("    {\n");
    echo("      document.frmPerguntaAcao.action='excluir_pergunta.php';\n");
    echo("      document.frmPerguntaAcao.cod_pergunta.value = id;\n");
    echo("      document.frmPerguntaAcao.submit();\n");
    echo("    }\n");
    echo("  }\n\n");
    
    echo("function ExcluirSelecionadas()");
    echo("  {\n");
    echo(" verificador=Validacheck();\n");
     /* 43 - Deseja excluir definitivamente esta pergunta? */
    echo("if(verificador==true)\n");
    echo("    if (confirm('".RetornaFraseDaLista($lista_frases, 43)."'))\n");
    echo("    {\n");
    echo("  document.frm_pergunta.action = \"excluir_pergunta.php\";\n");
    echo("  document.frm_pergunta.submit();\n");
    echo("  return true;\n");
    echo("    }\n");
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
    echo("        document.frmAssuntoAcao.action='mover_assunto.php';\n");
    echo("        document.frmAssuntoAcao.cod_assunto.value = origem;\n");
    echo("        document.frmAssuntoAcao.cod_assunto_dest.value = destino;\n");
    echo("        document.frmAssuntoAcao.submit();\n");
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
  
    echo("function Envia(assunto)");
    echo("  {\n");
    echo(" verificador=Validacheck();\n");
    echo("if(verificador==true)\n");
    echo("  {\n");
    echo("    window.open('','pergunta','width=600,height=400,top=50,left=50,scrollbars=yes,");
    echo("status=yes,toolbar=no,menubar=no,resizable=yes');\n");
    echo("if(assunto==2)\n");
    echo("  document.frm_pergunta.action = \"ver_pergunta_lixeira.php\";\n");
    echo("else\n");
    echo("  document.frm_pergunta.action = \"ver_pergunta.php\";\n");
    echo("  document.frm_pergunta.target = 'pergunta';\n");
    echo("  document.frm_pergunta.submit();\n");
    echo("  return true;\n");
    echo("    }\n");
    echo("  return false;\n");
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


    echo("   function MarcaOuDesmarcaTodos()\n");
    echo("{\n");
    echo("  var e;\n");
    echo("  var CabecalhoMarcado=document.frm_pergunta.checkMenu.checked;\n");
    echo("  for (var i=0;i<document.frm_pergunta.elements.length;i++)\n");
    echo("  {\n");
    echo("    e = document.frm_pergunta.elements[i];\n");
    echo("    if (e.name=='cod_pergunta[]')\n");
    echo("    {\n");
    echo("      e.checked=CabecalhoMarcado;\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");
	      
    echo("  function VerificaCheck(){\n
  			var cod_pergunta = document.getElementsByName('cod_pergunta[]');\n

  			/* Se tiver ao menos 1 checkbox, seja assunto ou */ 
  			/* pergunta tickado, mostra os botoes */
  			var i = 0;
  			for (i = 0; i < cod_pergunta.length; i++)
  				if (cod_pergunta[i].checked){
  					return HabilitaBotoes();
  				}
  				
  			return DesabilitaBotoes();
  	 	  }
  ");
      
  echo("  function HabilitaBotoes(){");
  if ($usr_formador){
  		echo("		document.getElementById('mApagar_Selec').className=\"menuUp02\";");
  		echo("      document.getElementById('mApagar_Selec').onclick=function(){ ApagarSelecionadas(); };\n");
  	}
  echo("		document.getElementById('mExibir_Selec').className=\"menuUp02\";");
  echo("        document.getElementById('mExibir_Selec').onclick=function(){ Envia(".$cod_assunto_pai."); };\n");
  echo("}");
  
  
  echo("  function DesabilitaBotoes(){");
  if ($usr_formador){
  		echo("		document.getElementById('mApagar_Selec').className=\"menuUp\";");
  		echo("      document.getElementById('mApagar_Selec').onclick=function(){};\n");
  	}
  echo("  		document.getElementById('mExibir_Selec').className=\"menuUp\";");
  echo("      	document.getElementById('mExibir_Selec').onclick=function(){};\n");
  echo("}");
  
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
    
  echo("</script>\n\n");

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
  
  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span>\n");
  
  /* 1 - Perguntas Freq�entes */
  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";

  //echo("  <br>\n");
  
  echo("  <span class=\"btsNav2\"><a href=# onClick='MostraLayer(lay_estrutura,this);return(false);'><img src=../imgs/estrutura.gif border=0></a>\n");
  //echo("  <a href=# onMouseDown='MostraLayer(lay_estrutura,0);return(false);'><img src=../figuras/estrutura.gif border=0></a>\n");
  echo("    <font class=text>".RetornaLinkCaminhoAssunto($sock, $cod_assunto_pai, $cod_curso, "perguntas"));
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
  
  echo("          <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("              <tr>\n");
  echo("              <!-- Botoes de Acao -->\n");
  echo("                <td class=\"btAuxTabs\">\n");
  echo("                  <ul class=\"btAuxTabs\">\n");

  echo("      <li><span onClick=\"history.go(-1);\">".RetornaFraseDaLista($lista_frases, 56)."</span></li>\n");
  
  if ($usr_formador)
  {
   /* 16 - Lixeira */
   echo("      <li><span href=# onClick='Abrir(2); return(false);'>".RetornaFraseDaLista($lista_frases_geral,16)."</span></li>\n");
   
  }

  echo("    </tr>\n");
  echo("  <form method=post name=frm_pergunta action='acoes.php'>");

  //echo(RetornaSessionIDInput());
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");


  echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
  /* Especifica o documento da pagina principal, o qual chamou o    */
  /* ver_pergunta.php. Isto eh necessario para atualizar a pagina   */
  /* principal que pode ser perguntas.php ou exibir_todas.php.      */
  echo("    <input type=hidden name=pagprinc value=perguntas>\n");
  echo("    <input type=hidden name=acao value=''>\n");

  echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
  echo("    <input type=hidden name=cod_assunto_dest value=\"\">\n");

  if ($cod_assunto_pai == 2)
    /* Passa o 'cod_assunto_anterior', necessario para se voltar ao */
    /* assunto anterior a visualiza�ao da lixeira.                  */
    echo("  <input type=hidden name=cod_assunto_anterior value=".$cod_assunto_anterior.">\n");
  else
    echo("  <input type=hidden name=cod_assunto_anterior value=".$cod_assunto_pai.">\n");

  /* Especifica o documento da pagina principal, o qual chamou o    */
  /* perguntas.php, mas com o cod_assunto_pai = 2 (lixeira). Isto   */
  /* eh necessario para voltar ao modo de visualiza�ao anterior.    */
  if (isset($pagprinc))
  /* Se jah estiver setada entao usa o valor default. Isto eh     */
  /* necessario quando o cod_assunto_pai = 2 (LIXEIRA). Entao eh  */
  /* eh preciso voltar ao modo de visualiza�ao anterior.          */
    echo("    <input type=hidden name=pag_anterior value=".$pag_anterior.">\n");
  else
    echo("    <input type=hidden name=pag_anterior value=perguntas>\n");


  echo("              <tr>\n");
  echo("                <td valign=\"top\">\n");
  echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");  
  echo("                    <tr class=\"head\">\n");
  
  echo("                      <td width=\"2%\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"MarcaOuDesmarcaTodos();VerificaCheck();\" /></td>\n");
  
  echo("					<td class=alLeft colspan=\"2\">Assunto</td>");
  echo("					</tr>");

    $lista_perguntas = ListaTodasPerguntas($sock);
    if (count($lista_perguntas) > 0)
    {
      // a acao a tomar se o usuario clicar no link da pergunta varia entre formador
      // e aluno, lixeira ou nao
      if (!$usr_formador)
      {
        // apenas ver a pergunta
        $acao_link_abre = "<a class=text href=# onClick='Ver(";
        // aqui no meio vai o codigo da pergunta a ver
        $acao_link_fecha= ");  return false;'>";
      }
      else if ($cod_assunto_pai != 2)
      {
        $acao_link_abre = "<a class=text href=# onClick='selected_item=";
        // aqui no meio vai o codigo da pergunta a ver
        $acao_link_fecha= " ;MostraLayer(lay_pergunta, this);  return false;'>";
      }
      else
      {
        $acao_link_abre = "<a class=text href=# onClick='selected_item=";
        // aqui no meio vai o codigo da pergunta a ver
        $acao_link_fecha= "  ;MostraLayer(lay_lixeira_pergunta, this);  return false;'>";
      }

      // Mostra as perguntas:
      foreach ($lista_perguntas as $c => $linha_pergunta)
      {
      	
        echo("      <tr >\n");
        /* Coloca uma caixa de sele�ao para exibi�ao multipla de perguntas */
        echo("        <td  width=1%>\n");
        echo("          <input type=checkbox name=cod_pergunta[] onClick=\"VerificaCheck();\" value=".$linha_pergunta['cod_pergunta'].">");
        echo("        </td>\n");

        // Insere a imagem associada aa pergunta 
//        echo("        <td class=wtfield width=1%><a class=text href=# onClick='Ver(");
//        echo($linha_pergunta['cod_pergunta'].");");
//        echo("return(false);'>");
//        echo("<img src=\"../figuras/inter.gif\" border=0></a>\n");
//        echo("        </td>\n");
        // e cria um link nela para o layer
        echo("        <td class=alLeft><img border=\"0\" alt=\"\" src=\"../imgs/icEnquete.jpg\"/>&nbsp;&nbsp;".$acao_link_abre.$linha_pergunta['cod_pergunta'].$acao_link_fecha.LimpaTags(TruncaString($linha_pergunta['pergunta'], 80))."</a>&nbsp;&nbsp;<font class=text>(".RetornaCaminhoAssunto($sock, $linha_pergunta['cod_assunto']).")</font></td>\n");
        echo("      </tr>\n");
      }
    } else {
    	echo("  <tr class=text> <td class=text colspan=4>".RetornaFraseDaLista($lista_frases,67)."</td></tr>\n");
    }

  
  echo("    </table>\n");
  /* Se houver perguntas exibe o botao para multipla exibi�ao. */
  if (count($lista_perguntas) != 0)
  {
  	echo(" <ul>\n");
    /* 16 - Exibir selecionadas */
    echo("    <li id=\"mExibir_Selec\" class=\"menuUp\"><span name=exibir onClick='';>".RetornaFraseDaLista($lista_frases,16)."</span></li>\n");
    /* 69 - Apagar selecionadas */
    if ($usr_formador) 
        echo("    <li id=\"mApagar_Selec\" class=\"menuUp\"><span name=apagar onClick='';>".RetornaFraseDaLista($lista_frases,69)."</span></li>\n");
    echo(" </ul>\n");
  } 
  
  
  echo("	</td>");
  echo("  </tr>");
  echo("</table>");  
  
  echo("  </form>\n\n");
  echo("  <span class=\"btsNavBottom\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a><a href=\"#topo\"><img src=\"../imgs/btTopo.gif\" border=\"0\" alt=\"Topo\" /></a></span>\n");
  echo("  <form name=frmAssuntoAcao method=post>\n");
  /* Passa o 'cod_assunto_pai', necessario para atualizar a pagina */
  /* principal.                                                    */
 
  //echo(RetornaSessionIDInput());
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");

  echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
  /* Passa o 'cod_assunto', necessario para efetuar as a�oes. */
  echo("    <input type=hidden name=cod_assunto value=-1>\n");

  if ($usr_formador)
  {
    /* Passa o 'cod_assunto_dest', necessario para mover o assunto. */
    echo("    <input type=hidden name=cod_assunto_dest value=-1>\n");


    if ($cod_assunto_pai == 2)
      /* Passa o 'cod_assunto_anterior', necessario para se voltar ao */
      /* assunto anterior a visualiza�ao da lixeira.                  */
      echo("    <input type=hidden name=cod_assunto_anterior value=".$cod_assunto_anterior.">\n");
    else
      echo("    <input type=hidden name=cod_assunto_anterior value=".$cod_assunto_pai.">\n");


    /* Especifica o documento da pagina principal, o qual chamou o    */
    /* perguntas.php, mas com o cod_assunto_pai = 2 (lixeira). Isto   */
    /* eh necessario para voltar ao modo de visualiza�ao anterior.    */
    if (isset($pagprinc))
    /* Se jah estiver setada entao usa o valor default. Isto eh     */
    /* necessario quando o cod_assunto_pai = 2 (LIXEIRA). Entao eh  */
    /* eh preciso voltar ao modo de visualiza�ao anterior.          */
      echo("    <input type=hidden name=pag_anterior value=".$pag_anterior.">\n");
    else
      echo("    <input type=hidden name=pag_anterior value=\"perguntas\">\n");

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
    echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
    /* Passa o 'cod_pergunta' para execu�ao das a�oes.                */
    echo("    <input type=hidden name=cod_pergunta value=-1>\n");
    /* Especifica o documento de origem para 'exibir_todas'. Isto eh  */
    /* necessario, pois tanto 'exibir_todas.php', 'perguntas.php' e   */
    /* 'ver_pergunta.php' chamam a fun�oes apagar, mover, editar,     */
    /* recuperar e excluir.   */
    echo("    <input type=hidden name=origem value=perguntas>\n");

    /* Especifica o documento da pagina principal, o qual chamou o    */
    /* ver_pergunta.php. Isto eh necessario para atualizar a pagina   */
    /* principal que pode ser perguntas.php ou exibir_todas.php.      */
    if (isset($pagprinc))
      /* Se jah estiver setada entao usa o valor default. Isto eh     */
      /* necessario quando o cod_assunto_pai = 2 (LIXEIRA). Entao eh  */
      /* eh preciso voltar ao modo de visualiza�ao anterior.          */
      echo("    <input type=hidden name=pag_anterior value=".$pag_anterior.">\n");
    else
      echo("    <input type=hidden name=pag_anterior value=perguntas>\n");

    if ($cod_assunto_pai == 2)
      /* Passa o 'cod_assunto_anterior', necessario para se voltar ao */
      /* assunto anterior a visualiza�ao da lixeira.                  */
      echo("    <input type=hidden name=cod_assunto_anterior value=".$cod_assunto_anterior.">\n");
    else
      echo("    <input type=hidden name=cod_assunto_anterior value=".$cod_assunto_pai.">\n");


    /* Passa o 'cod_assunto_dest', necessario para mover a pergunta.  */
    echo("    <input type=hidden name=cod_assunto_dest value=-1>\n");
    echo("  </form>\n\n");

    /* layer_pergunta */
    echo("  <div id=layer_pergunta class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
    echo("    <table bgcolor=#ffffff cellpadding=1 cellspacing=1 border=2>\n");
    echo("      <tr class=bgcolor>\n");
    echo("        <td class=bgcolor align=right>\n");
    echo("          <a href=# onClick='EscondeLayer(lay_pergunta);return(false);'>");
    echo("<img src=../figuras/x.gif border=0></a>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr >\n");
    echo("        <td >\n");
    /* 21 - Ver */
    echo("          <a href=# class=text onClick='Ver(selected_item);EscondeLayer(lay_pergunta);return(false)'>".RetornaFraseDaLista($lista_frases_geral, 21)."</a><br>\n");
    /* 9 - Editar */
    echo("          <a href=# class=text onClick='Editar(selected_item,2);return(false);'>".RetornaFraseDaLista($lista_frases_geral, 9)."</a><br>\n");
    /* 25 - Mover */
    echo("          <a href=# class=text onClick='MostraLayer(lay_estrutura_mover_pergunta, lay_pergunta);  return false;'>".RetornaFraseDaLista($lista_frases_geral, 25)."</a><br>\n");
    /* 1 - Apagar */
    echo("          <a href=# class=text onClick='Apagar(selected_item,2);return(false);'>".RetornaFraseDaLista($lista_frases_geral, 1)."</a><br>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </div>\n\n");

    /* layer_lixeira_pergunta */
    echo("  <div id=layer_lixeira_pergunta class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
    echo("    <table bgcolor=#ffffff cellpadding=1 cellspacing=1 border=2>\n");
    echo("      <tr class=bgcolor>\n");
    echo("        <td class=bgcolor align=right>\n");
    echo("          <a href=# onClick='EscondeLayer(lay_lixeira_pergunta);return(false);'>");
    echo("<img src=../figuras/x.gif border=0></a>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr >\n");
    echo("        <td >\n");
    /* 21 - Ver */
    echo("          <a href=# class=text onClick='Ver(selected_item);");
    echo("EscondeLayer(lay_lixeira_pergunta);return(false)'>");
    echo(RetornaFraseDaLista($lista_frases_geral, 21)."</a><br>\n");
    /* 48 - Recuperar */
    echo("          <a href=# class=text onClick='MostraLayer(lay_estrutura_mover_pergunta, lay_lixeira_pergunta)'>".RetornaFraseDaLista($lista_frases_geral, 48)."</a><br>\n");
    /* 12 - Excluir */
    echo("          <a href=# class=text onClick='Excluir(selected_item);return(false);'>".RetornaFraseDaLista($lista_frases_geral, 12)."</a><br>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </div>\n\n");

    if ($cod_assunto_pai != 2)
    {
      /* layer_assunto */
      echo("  <div id=layer_assunto class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
      echo("    <table bgcolor=#ffffff cellpadding=1 cellspacing=1 border=2>\n");
      echo("      <tr class=bgcolor>\n");
      echo("        <td class=bgcolor align=right>\n");
      echo("          <a href=# onClick='EscondeLayer(lay_assunto);return(false);'>");
      echo("<img src=../figuras/x.gif border=0></a>\n");
      echo("        </td>\n");
      echo("      </tr>\n");
      echo("      <tr >\n");
      echo("        <td >\n");
      /* 34 - Abrir */
      echo("          <a href=# class=text onClick='Abrir(selected_item);return(false)'>".RetornaFraseDaLista($lista_frases_geral, 34)."</a><br>\n");
      /* 9 - Editar */
      echo("          <a href=# class=text onClick='Editar(selected_item,1);return(false);'>".RetornaFraseDaLista($lista_frases_geral, 9)."</a><br>\n");
      /* 25 - Mover */
      echo("          <a href=# class=text onClick='MostraLayer(lay_estrutura_mover_assunto, lay_assunto)'>".RetornaFraseDaLista($lista_frases_geral, 25)."</a><br>\n");
      /* 1 - Apagar */
      echo("          <a href=# class=text onClick='Apagar(selected_item,1);return(false);'>".RetornaFraseDaLista($lista_frases_geral, 1)."</a><br>\n");
      echo("        </td>\n");
      echo("      </tr>\n");
      echo("    </table>\n");
      echo("  </div>\n\n");

      /* layer_estrutura_mover_assunto */
      echo("  <div id=layer_estrutura_mover_assunto class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
      echo("    <table bgcolor=#ffffff cellpadding=1 cellspacing=1 border=2>\n");
      echo("      <tr class=bgcolor>\n");
      echo("        <td class=bgcolor>\n");

      echo("          <table bgcolor=#ffffff cellpadding=0 cellspacing=0 border=0 width=100%>\n");
      echo("            <tr class=bgcolor>\n");
      echo("              <td class=bgcolor align=left>\n");
      /* 53 - Mover para: */
      echo("                <b><font class=text color=white>".RetornaFraseDaLista($lista_frases, 53)."</font></b>\n");
      echo("              </td>\n");
      echo("              <td class=bgcolor align=right>\n");
      echo("                <a href=# onClick='EscondeLayer(lay_estrutura_mover_assunto);return(false);'>");
      echo("<img src=../figuras/x.gif border=0></a>\n");
      echo("              </td>\n");
      echo("            </tr>\n");
      echo("          </table>\n");

      echo("        </td>\n");
      echo("      </tr>\n");
      echo("      <tr >\n");
      echo("        <td >\n");
      echo("          ".EstruturaMoverAssunto($sock, $cod_assunto_pai));
      echo("        </td>\n");
      echo("      </tr>\n");
      echo("    </table>\n");
      echo("  </div>\n\n");
    }

    /* layer_estrutura_mover_pergunta */
    echo("  <div id=layer_estrutura_mover_pergunta class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
    echo("    <table bgcolor=#ffffff cellpadding=1 cellspacing=1 border=2>\n");
    echo("      <tr class=bgcolor>\n");
    echo("        <td class=bgcolor align=right>\n");

    echo("          <table bgcolor=#ffffff cellpadding=0 cellspacing=0 border=0 width=100%>\n");
    echo("            <tr class=bgcolor>\n");
    echo("              <td class=bgcolor align=left>\n");

    if ($cod_assunto_pai == 2)
      /* 54 - Recuperar para: */
      echo("                <b><font class=text color=white>".RetornaFraseDaLista($lista_frases, 54)."</font></b>\n");
    else
      /* 53 - Mover para: */
      echo("                <b><font class=text color=white>".RetornaFraseDaLista($lista_frases, 53)."</font></b>\n");

    echo("              </td>\n");
    echo("              <td class=bgcolor align=right>\n");
    echo("                <a href=# onClick='EscondeLayer(lay_estrutura_mover_pergunta);return(false);'>");
    echo("<img src=../figuras/x.gif border=0></a>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");

    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr >\n");
    echo("        <td >\n");
    echo("          ".EstruturaMoverPergunta($sock, $cod_assunto_pai));
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </div>\n\n");

  }

  /* layer_estrutura */
  echo("  <div id=\"layer_estrutura\" class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
  echo("    <div class=\"posX\"><span onclick=\"EscondeLayer(lay_estrutura);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <div class=\"ulPopup\">\n"); 

  echo("          ".EstruturaDeAssuntos($sock, $cod_assunto_pai, $usr_formador));

  echo("        </div>\n");
  echo("      </div>\n");
  echo("  </div>\n\n");
  
  include("../tela2.php");
  
  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
