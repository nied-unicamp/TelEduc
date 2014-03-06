<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perguntas/editar_assunto.php

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
  ARQUIVO : cursos/aplic/perguntas/editar_assunto.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");

  $cod_ferramenta = 6;  
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=7;
  
  $lista_frases_geral = RetornaListaDeFrases($sock,-1);
  
    /**************** ajax ****************/

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das fun��es em PHP que voc� quer chamar atrav�s do xaja
  $objAjax->register(XAJAX_FUNCTION,"AlteraDadosAssuntoDinamic");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();
  
  include("../topo_tela.php");
  
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  //adicionar as acoes possiveis, 1o parametro é
  $feedbackObject->addAction("novoAssunto", 80, 0);
  
  /* Verifica se o usuario eh formador. */
  if (EFormador($sock, $cod_curso, $cod_usuario))
    $usr_formador = true;
  else
    $usr_formador = false;

  $cod_assunto = $cod_assunto_pai;
  
  echo("<script language=\"javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("<script language=\"javascript\">\n\n");
  
  echo("  img_icone = new Image();\n");
  echo("  img_icone.src = \"../figuras/assunto.gif\";\n\n");
  
  echo("  var existelayer = false; ");
  echo("  var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("  var versao = (navigator.appVersion.substring(0,3));\n");
  echo("  var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
  echo("  var nome_antigo = '';\n");
  echo("  var descricao_antigo = '';\n");

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
  echo("      return(document.documentElement.scrollTop);\n");
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
  if ($usr_formador)
  {
  	echo("    layer_estrutura_mover = getLayer('layer_estrutura_mover');\n");  
  }
  echo("        var atualizacao = '".$_GET['atualizacao']."';\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("  EscondeLayers();\n");
  echo("  }\n\n");
  
  echo("  function EscondeLayer(cod_layer)\n");
  echo("  {\n");
  echo("    hideLayer(cod_layer);\n");
  echo("  }\n\n");

  echo("  function EscondeLayers()\n");
  echo("  {\n");
  if ($usr_formador)
  {
    echo("    hideLayer(layer_estrutura_mover);\n");
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
   echo("    {\n");
  echo("        moveLayerTo(cod_layer, obj.x , obj.y + img_icone.height);\n");
   echo("    }\n");
  echo("    }\n");
  echo("    else\n");
  echo("      moveLayerTo(cod_layer, Xpos, Ypos + AjustePosMenuIE());\n");
  echo("    showLayer(cod_layer);\n");
  echo("  }\n\n");
  
  echo("  function VerificaCheck(){\n
  			var cod_assunto = document.getElementsByName('cod_assunto[]');\n
  			var cod_pergunta = document.getElementsByName('cod_pergunta[]');\n

  			/* Se tiver ao menos 1 checkbox, seja assunto ou */ 
  			/* pergunta tickado, mostra os botoes */
  			var i = 0;
  			for (i = 0; i < cod_assunto.length; i++)
  				if (cod_assunto[i].checked){
  					return HabilitaBotoes();
  				}
  				
  			i = 0;
  			for (i = 0; i < cod_pergunta.length; i++)
  				if (cod_pergunta[i].checked){
  					return HabilitaBotoes();
  				}
  				
  			return DesabilitaBotoes();
  	 	  }
  ");
  
  
  echo("  function HabilitaBotoes(){");
  if ($usr_formador){
  	if ($cod_assunto_pai == 2){ /* Tá na lixeira? */
  		echo("		document.getElementById('mExcluir_Selec').className=\"menuUp02\";");
  		echo("      document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionadas(); };\n");
  		echo("		document.getElementById('mRecuperar_Selec').className=\"menuUp02\";");
  		echo("      document.getElementById('mRecuperar_Selec').onclick=function(){ MostraLayer(layer_estrutura_recuperar,this); };\n");
  	} else {
  		echo("		document.getElementById('mApagar_Selec').className=\"menuUp02\";");
  		echo("      document.getElementById('mApagar_Selec').onclick=function(){ ApagarSelecionadas(); };\n");
  		echo("		document.getElementById('mMover_Selec').className=\"menuUp02\";");
  		echo("      document.getElementById('mMover_Selec').onclick=function(){ MostraLayer(layer_estrutura_mover,this); };\n");
  	}
  }
  echo("		document.getElementById('mExibir_Selec').className=\"menuUp02\";");
  echo("        document.getElementById('mExibir_Selec').onclick=function(){ Envia(".$cod_assunto_pai."); };\n");
  echo("}");
  
  
  echo("  function DesabilitaBotoes(){");
  if ($usr_formador){
  	if ($cod_assunto_pai == 2){ /* Tá na lixeira? */
  		echo("		document.getElementById('mExcluir_Selec').className=\"menuUp\";");
  		echo("      document.getElementById('mExcluir_Selec').onclick=function(){};\n");
  		echo("		document.getElementById('mRecuperar_Selec').className=\"menuUp\";");
  		echo("      document.getElementById('mRecuperar_Selec').onclick=function(){};\n");
  	} else {
  		echo("		document.getElementById('mApagar_Selec').className=\"menuUp\";");
  		echo("      document.getElementById('mApagar_Selec').onclick=function(){};\n");
  		echo("		document.getElementById('mMover_Selec').className=\"menuUp\";");
  		echo("      document.getElementById('mMover_Selec').onclick=function(){};\n");
  	}
  }
  echo("  		document.getElementById('mExibir_Selec').className=\"menuUp\";");
  echo("      	document.getElementById('mExibir_Selec').onclick=function(){};\n");
  echo("}");
  
  
  echo("  function EscondeLayer(cod_layer)\n");
  echo("  {\n");
  echo("    hideLayer(cod_layer);\n");
  echo("  }\n\n");

  echo("  function EscondeLayers()\n");
  echo("  {\n");
  if ($usr_formador)
  {
    echo("    hideLayer(layer_estrutura_mover);\n");
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
  echo("        location.href = 'acoes.php?cod_curso=$cod_curso&acao=moverItem&cod_assunto_dest='+destino+'&cod_assunto='+$cod_assunto;");
  echo("      }\n");
  echo("    }\n");
  echo("  }\n\n");
    

  /* Se o usuario FOR Formador entao cria as fun�oes javascript. */
  if ($usr_formador)
  {
//  	echo ("     function EditaTituloEnter(campo, evento, id)\n");
//    echo ("     {\n");
//    echo ("         var tecla;\n");
//    echo ("         CheckTAB=true;\n\n");
//    echo ("         if(navigator.userAgent.indexOf(\"MSIE\")== -1)\n");
//    echo ("         {\n");
//    echo ("             tecla = evento.which;\n");
//    echo ("         }\n");
//    echo ("         else\n");
//    echo ("         {\n");
//    echo ("             tecla = evento.keyCode;\n");
//    echo ("         }\n\n");
//    echo ("         if ( tecla == 13 )\n");
//    echo ("         {\n");
//    echo ("             EdicaoTitulo(id, 'tit_'+id, 'ok');\n"); //A fun��o e par�metros s�o os mesmos utilizados na fun��o de edi��o j� utilizada.
//    echo ("         }\n\n");
//    echo ("         return true;\n");
//    echo ("     }\n\n");

    echo("		function EdicaoTitulo(id, b, state){
    				if (state == 'canc') {
    					if(id == 'Assunto') {	//Se for edicao do nome do assunto
    						document.getElementById(b).innerHTML = nome_antigo;
    					} else if(id == 'Descricao') {	//Se for edicao da descricao do assunto
    						document.getElementById(b).innerHTML = descricao_antigo;
    					}
    					document.getElementById('renomear_'+id).onclick=function(){ AlteraTitulo(id) };
    				} else {
    					xajax_AlteraDadosAssuntoDinamic(id, document.getElementById(b).childNodes[0].value, $cod_assunto);
    					document.getElementById('renomear_'+id).onclick=function(){ AlteraTitulo(id) };
    					var novo = document.getElementById(b).childNodes[0].value;
    					document.getElementById(b).innerHTML = novo;
    					if (id == 'Assunto'){
    						var node = document.getElementById('topo_caminho').childNodes.length - 2; 
    						document.getElementById('topo_caminho').childNodes[node].innerHTML = novo;
  						} else {
  							document.getElementById('topo_descricao').innerHTML = novo
  						}
    					
    				} 
    			}");
    			
    echo("		function LimpaTitulo(id){\n");
    echo("			xajax_AlteraDadosAssuntoDinamic('Descricao', '', $cod_assunto);\n");
    echo("			document.getElementById('tit_Descricao').innerHTML=''\n");
    echo("		}\n");
  	
    
  	echo("      function AlteraTitulo(id){\n");
    echo("			var id_aux = id;\n");
    //echo("        if (editaTitulo==0){\n");
    //echo("          CancelaTodos();\n");
    //echo("          xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("			var conteudo = document.getElementById('tit_'+id).innerHTML;\n");
    echo("			if(id=='Descricao') {\n");
    echo("          	descricao_antigo = conteudo;\n");
    echo("			} else if(id=='Assunto'){\n");
    echo("          	nome_antigo = conteudo;\n");
    echo("			}\n");
    
    echo("          document.getElementById('tr_'+id).className=\"\";\n");
    echo("          document.getElementById('tit_'+id).innerHTML='';\n");

    echo("          createInput = document.createElement('input');\n");    
    echo("          document.getElementById('renomear_'+id).onclick=function(){ };\n\n");

    echo("          createInput.setAttribute('type', 'text');\n");
    echo("			if (id == 'Descricao') {\n");
    echo("				if(isIE) {\n");	//setar o style no IE
    echo("          		createInput.style.setAttribute('cssText', 'border: 2px solid #9bc; width: 350px;');\n");
    echo("				} else {\n");	//setar o style no FF
    echo("          		createInput.setAttribute('style', 'border: 2px solid #9bc; width: 350px;');\n");
    echo("				}\n");
    echo("			} else {\n");
    echo("				if(isIE) {\n");	//setar o style no IE
    echo("          		createInput.style.setAttribute('cssText', 'border: 2px solid #9bc');\n");
    echo("				} else {\n");	//setar o style no FF
	echo("          		createInput.setAttribute('style', 'border: 2px solid #9bc');\n");
    echo("				}\n");
	echo("			}\n");
    echo("          createInput.setAttribute('id', 'tit_'+id+'_text');\n\n");
    echo("          createInput.setAttribute('value', conteudo);\n\n");
    //echo("          if (createInput.addEventListener){\n"); //not IE
    //echo("            createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);\n");
    //echo("          } else if (createInput.attachEvent){\n"); //IE
    //echo("            createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});\n");
    //echo("          }\n");

    echo("          document.getElementById('tit_'+id).appendChild(createInput);\n");
    //echo("          xajax_DecodificaString('tit_'+id+'_text', conteudo, 'value');\n\n");

    echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
    echo("          espaco = document.createElement('span');\n");
    echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
    echo("          document.getElementById('tit_'+id).appendChild(espaco);\n");

    echo("          createSpan = document.createElement('span');\n");
    echo("          createSpan.className='link';\n");
    echo("          createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'ok'); };\n");
    echo("          createSpan.setAttribute('id', 'OkEdita');\n");
    echo("          createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral,18)."';\n");
    echo("          document.getElementById('tit_'+id).appendChild(createSpan);\n\n");

    echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
    echo("          espaco = document.createElement('span');\n");
    echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
    echo("          document.getElementById('tit_'+id).appendChild(espaco);\n\n");

    echo("          createSpan = document.createElement('span');\n");
    echo("          createSpan.className='link';\n");
    echo("          createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'canc'); };\n");
    echo("          createSpan.setAttribute('id', 'CancelaEdita');\n");
    echo("          createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral,2)."';\n");
    echo("          document.getElementById('tit_'+id).appendChild(createSpan);\n\n");

    //echo("          startList();\n");
    echo("          cancelarElemento=document.getElementById('CancelaEdita');\n");
    echo("          document.getElementById('tit_'+id+'_text').select();\n");
    //echo("          editaTitulo++;\n");
    //echo("        }\n");
    echo("      }\n\n");
    
    echo("  function Apagar(id, tipo)\n");
    echo("  {\n");
    echo("    if (tipo == 1)\n");
    echo("    {\n");
    /* 32 - Tem certeza que deseja apagar este assunto? (todos os assunto e todas as perguntas nele contidos ser�o apagados) */
    echo("      if (confirm('".RetornaFraseDaLista($lista_frases, 32)."'))\n");
    echo("      {\n");
    echo("		  document.frm_pergunta.acao.value='apagarItem';\n");
    echo("        document.frm_pergunta.action='acoes.php';\n");
    echo("        document.frm_pergunta.cod_assunto.value = id;\n");
    echo("        document.frm_pergunta.submit();\n");
    echo("      }\n");
    echo("    }\n");
    echo("    else if (tipo == 2)\n");
    echo("    {\n");
    /* 21 - Tem certeza que deseja apagar esta pergunta? */
    echo("      if (confirm('".RetornaFraseDaLista($lista_frases,21)."'))\n");
    echo("      {\n");
    echo("		  document.frmAssuntoAcao.acao.value='apagarItem';\n");
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
    echo("  document.frm_pergunta.action = \"acoes.php\";\n");
    echo("  document.frm_pergunta.acao.value = \"excluirItem\";\n");
    echo("  document.frm_pergunta.submit();\n");
    echo("  return true;\n");
    echo("    }\n");
    echo("  return false;\n");
    echo("  }\n\n");

  }
  
  echo("</script>\n\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");
  
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  
  /* Se for Aluno, j� corta */
  if ($tela_formador != 1) {
    echo ("          <h4>" . RetornaFraseDaLista($lista_frases, 1));
    /* 73 - Acao exclusiva a formadores. */
    echo ("    - " . RetornaFraseDaLista($lista_frases_geral, 76) . "</h4>");

    /*Voltar*/
   /* 509 - Voltar */
    echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo ("          <div id=\"mudarFonte\">\n");
    echo ("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo ("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo ("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo ("          </div>\n");

    echo ("        </td>\n");
    echo ("      </tr>\n");
    echo ("    </table>\n");
    echo ("  </body>\n");
    echo ("</html>\n");
    Desconectar($sock);
    exit;
}
  
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
  $cabecalho = "  <b class=\"titulo\">".RetornaFraseDaLista($lista_frases,1)."</b>";

  //echo("  <br>\n");
  
  echo("  <span class=\"btsNav2\"><a href=\"#\" onClick='MostraLayer(layer_estrutura,this);return(false);'><img src=\"../imgs/estrutura.gif\" border=0></a>\n");
  //echo("  <a href=# onMouseDown='MostraLayer(lay_estrutura,0);return(false);'><img src=../figuras/estrutura.gif border=0></a>\n");
  echo("    <font id=\"topo_caminho\" class=\"text\">".RetornaLinkCaminhoAssunto($sock, $cod_assunto_pai, $cod_curso, "perguntas"));
  echo("    </font></span>\n");
  echo("  \n");

  /* Obtem os dados do assunto atual. */
  $dados_assunto = RetornaAssunto($sock, $cod_assunto);
  $nome = $dados_assunto['nome'];
  $descricao = $dados_assunto['descricao'];
  $data = $dados_assunto['data'];
  $cod_assunto = $dados_assunto['cod_assunto'];
  $cod_assunto_pai = $dados_assunto['cod_assunto_pai'];
  
  /* Se a descri�ao NAO for vazia ou composta por apenas espa�os */
  /* entao a exibe.                                              */
  //if (EliminaEspacos($descricao) != "")
  //{
    echo("  <table border=0 width=100% cellspacing=2>\n");
    echo("    <tr>\n");
    // 6 - Descri��o
    echo("      <td valign=top width=1% class=textsmall><i>".RetornaFraseDaLista($lista_frases, 6));
    echo("</i>:</td>\n");
    echo("      <td id=\"topo_descricao\" class=textsmall>\n");
    echo(Space2Nbsp(Enter2BR(LimpaTags($descricao)))."\n");
    echo("      </td>\n");
    echo("    </tr>\n");
    echo("  </table>\n");
  //}
  
  echo("          <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");

  echo("              <tr>\n");
  echo("              <!-- Botoes de Acao -->\n");
  echo("                <td class=\"btAuxTabs\">\n");
  echo("                  <ul class=\"btAuxTabs\">\n");
  
  /* 23 - Voltar */
  echo("      			<li><span onClick=\"location.href = 'perguntas.php?cod_curso=$cod_curso&cod_assunto_pai=$cod_assunto';\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  /* 52 - Atualizar  */
  echo("      			<li><span onClick=\"location.reload();\">".RetornaFraseDaLista($lista_frases_geral,52)."</span></li>\n");

  echo("    </tr>\n");
  
  /* Se estiver na Lixeira o formulario submete as informa�oes para */
  /* ver_pergunta_lixeira.php, do contrario, para ver_pergunta.php  */
  echo("  <form method=\"post\" name=\"frm_pergunta\" action=");
  if ($cod_assunto_pai == 2)
    echo("ver_pergunta_lixeira.php");
  else
    echo("ver_pergunta.php");

  echo(" target=_self onsubmit='return(MostrarSelecionadas());'>\n");
  
  echo("  <form method=\"post\" name=\"frm_pergunta\" target=_self>");

  //echo(RetornaSessionIDInput());
  echo("    <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
  echo("    <input type=\"hidden\" name=\"acao\" value=\"\">\n");

  echo("    <input type=\"hidden\" name=\"cod_assunto_pai\" value=\"".$cod_assunto_pai."\">\n");
  echo("    <input type=\"hidden\" name=\"cod_assunto_dest\" value=\"\">\n");
  echo("    <input type=\"hidden\" name=\"cod_assunto\" value=\"".$cod_assunto."\">\n");
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
  /* Assunto */
  echo("                    <td width=\"\" class=\"itens\">".RetornaFraseDaLista($lista_frases,55)."</td>\n");
  /* 70 - Opções */
  echo("                    <td width=\"17%\" align=\"center\">".RetornaFraseDaLista($lista_frases_geral,70)."</td>\n");
  /* 13 - Data */
  echo("                    <td width=\"14%\" align=\"center\">".RetornaFraseDaLista($lista_frases,74)."</td>\n");
  echo("					</tr>");
  
  echo("					<tr  id=\"tr_Assunto\">");
  echo("                    <td class=\"itens\">\n");

  echo("					<span id=\"tit_Assunto\">".$nome."</span>");

  echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
  echo("                      <ul>\n");
  /* 19 - Renomear */
  echo("                        <li><span onclick=\"AlteraTitulo('Assunto')\" id=\"renomear_Assunto\">".RetornaFraseDaLista($lista_frases_geral,19)."</span></li>\n");
  /* 73 - Editar descrição */
  echo("                        <li><span onclick=\"AlteraTitulo('Descricao')\" id=\"renomear_Descricao\">".RetornaFraseDaLista($lista_frases,73)."</span></li>\n");
  /* 75 - Limpar descrição */
  echo ("                       <li><span onclick=\"LimpaTitulo('Descricao');\">".RetornaFraseDaLista ($lista_frases, 75)."</span></li>\n");
  /* 25 - Mover (gen) */
  echo("                        <li><span onClick=\"MostraLayer(layer_estrutura_mover,this);\">".RetornaFraseDaLista($lista_frases_geral,25)."</span></li>\n");
  /* 1 - Apagar (gen) */
  echo("                        <li><span onClick=\"Apagar($cod_assunto,1);\">".RetornaFraseDaLista($lista_frases_geral,1)."</span></li>\n");
  echo("                      </ul>\n");
  echo("                    </td>\n");
  
  echo("                    <td align=\"center\">\n");
  echo("                      <span id=data_".$linha_item['cod_item'].">".UnixTime2DataHora($data)."</span>\n");
  echo("                    </td>\n");
  echo("	</tr>");
  echo("                    <tr class=\"head\">\n");
  /* Assunto */
  echo("                    <td width=\"\" colspan=3 class=\"itens\">".RetornaFraseDaLista($lista_frases,6)."</td>\n");
  echo("	</tr>");
  echo("					<tr  id=\"tr_Descricao\">");
  echo("                    <td colspan=3 class=\"itens\">\n");

  echo("<span id=\"tit_Descricao\">".$descricao."</span>");
  echo("</td></tr>");
  
  
  echo("    </table>\n");
  echo("	</td>");
  echo("  </tr>");
  echo("</table>");
    echo("          <br />\n");    
    /* 509 - voltar, 510 - topo */
    echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
    
    /* Layer: Estrutura-Mover */
  echo("  <div id=\"layer_estrutura_mover\" class=\"popup\" visibility=hidden onContextMenu='return(false);'>\n");
  echo("    <div class=\"posX\"><span onclick=\"EscondeLayer(layer_estrutura_mover);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <div class=\"ulPopup\">\n");

  echo("          ".EstruturaMoverAssunto($sock, $cod_assunto, $usr_formador));

  
  echo("        </div>\n");
  echo("      </div>\n");
  echo("  </div>\n\n");
  
  include("../tela2.php");
  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
