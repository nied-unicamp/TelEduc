<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perfil/perfil.php

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
  ARQUIVO : cursos/aplic/perfil/perfil.php
  ========================================================== */

/* Código principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perfil.inc");

  $cod_ferramenta=13;
  
  $cod_ferramenta_ajuda = $cod_ferramenta;
 
  $cod_pagina_ajuda=1;
  
  $tipo_usuario="A";

  require_once("../xajax_0.2.4/xajax.inc.php");

  // Estancia o objeto XAJAX
  $objMaterial = new xajax();
  // Registre os nomes das fun��es em PHP que voc� quer chamar atrav�s do xaja
  $objMaterial->registerFunction("IniciaPaginacaoDinamic");
  $objMaterial->registerFunction("MudaDinamic");
  $objMaterial->registerFunction("PaginacaoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objMaterial->processRequests();

  
  include("../topo_tela.php");
  
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("enviouOrientacao", 50, 51);

  /* variavel de ordenacao da lista de alunos. Ex: ordenar por nome */
  if (!isset($ordem))
  {
    $ordem="nome";
  }
  

  /* Funções javascript */
  $objMaterial->printJavascript("../xajax_0.2.4/");
  echo("    <script type=\"text/javascript\">\n");
  /* <Variaveis globais> */
  echo(" 	  var imprimir_perfil = 0;\n");
  echo("	  var qtdPag=1;\n");
  echo("	  var intervalo=1;\n");
  echo("	  var atual=1;\n");
  echo("	  var aux='T';\n");
  /* </ Variaveis globais> */
  echo("      function Iniciar()\n");
  echo("      {\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("	    xajax_IniciaPaginacaoDinamic(".$cod_curso.",'".$tipo_usuario."','".$ordem."');\n");
  echo("      }\n\n");
  
  /* *****************************************************************
  Funcao OpenWindow
  Abre nova janela com os perfis, se acessados atraves de checkboxes
    Entrada: nenhuma
    Saida:   nenhuma
  ***************************************************************** */
  echo("      function OpenWindow() \n");
  echo("      {\n");
  echo("         window.open(\"\",\"PerfilDisplay\", \"width=600,height=400,screenX=100,screenY=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("      }\n\n");
    /* *****************************************************************
  Funcao ImprimeWindow
  Abre nova janela com os perfis, se acessados atraves de checkboxes
    Entrada: nenhuma
    Saida:   nenhuma
  ***************************************************************** */
  echo("      function ImprimeWindow() \n");
  echo("      {\n");
  echo("         window.open(\"imprimir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  //echo("         window.open(\"\",\"PerfilDisplay\", \"width=600,height=400,screenX=100,screenY=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n\n");
    /* *****************************************************************
  Funcao setImprimir
  Abre nova janela com os perfis, se acessados atraves de checkboxes
    Entrada: nenhuma
    Saida:   nenhuma
  ***************************************************************** */
  echo("      function setImprimir(funcao) \n");
  echo("      {\n");
  echo("      		if(funcao == 1)");
  echo("				document.Perfil.imprimir.value = 1;");
  echo("      		else");
  echo("				document.Perfil.imprimir.value = 0;");
  echo("      }\n\n");

  /* *********************************************************
  Funcao OpenWindowLink
    Abre nova janela com o perfil, se acessado atraves do link
    Entrada: funcao = $cod_curso - Codigo do curso
    Saida:   false - para nao dar reload na pagina. Conferir a
                     chamada da fun�o
  */
  echo("      function OpenWindowLink(funcao) \n");
  echo("      {\n");
  echo("         window.open(\"exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n\n");

  /* ******************************************************************
  Funcao Check All
    Marca todas as checkboxes do respectivo grupo de usuarios
    Entrada: funcao - identifica se (1)aluno ou (2)formador
    Saida: nenhuma
  */
  echo("      function CheckAll(funcao)\n");
  echo("      {\n");
  echo("        var elem=document.Perfil.elements;\n");
  echo("        var nome_var='cod_aluno[]';\n");
  echo("        var nome_var_all='cod_aluno_all';\n");
  echo("        var changed=false;\n");
  echo("\n");
  echo("        if (funcao==2)\n");
  echo("        {\n");
  echo("          nome_var='cod_formador[]';\n");
  echo("          nome_var_all='cod_formador_all';\n");
  echo("        }\n");
  echo("        if (funcao == 3)\n");
  echo("        {\n");
  echo("          nome_var='cod_convidado[]';\n");
  echo("          nome_var_all='cod_convidado_all';\n");
  echo("        }\n");
  echo("        if (funcao == 4)\n");
  echo("        {\n");
  echo("          nome_var = 'cod_visitante[]';\n");
  echo("          nome_var_all = 'cod_visitante_all';\n");
  echo("        }\n");
  echo("        var i=0;\n");
  echo("        while (i < elem.length)\n");
  echo("        {\n");
  echo("          if (elem[i].name==nome_var_all)\n");
  echo("            changed=elem[i].checked;\n");
  echo("          else if (elem[i].name==nome_var)\n");
  echo("            elem[i].checked=changed;\n");
  echo("          i++;\n");
  echo("        }\n");
  echo("      }\n");

  /* ********************************************************************************************
  Funcao UnCheckHeader
    Desmarca as checkboxes que marcam todas, caso a checkbox de um usuario tenha sido desmarcada
    Entrada: funcao - identifica a checkbox a desmarcar
    Saida:   nenhuma
  */
  echo("      function UnCheckHeader(funcao)\n");
  echo("      {\n");
  echo("        var elem=document.Perfil.elements;\n");
  echo("        var nome_var_all='cod_aluno_all';\n");
  echo("        if (funcao==2)\n");
  echo("          nome_var_all='cod_formador_all';\n");
  echo("        else if (funcao == 3)\n");
  echo("          nome_var_all='cod_convidado_all';\n");
  echo("        else if (funcao == 4)\n");
  echo("          nome_var_all='cod_visitante_all';\n");
  echo("        var i=0;\n");
  echo("        while (i < elem.length)\n");
  echo("        {\n");
  echo("          if (elem[i].name==nome_var_all)\n");
  echo("            elem[i].checked=false;\n");
  echo("          i++;\n");
  echo("        }\n");
  echo("      }\n");
  
  /* ********************************************************************************************
  Funcao CheckHeader
    Marca as checkboxes que marcam todas, caso todas checkbox de usuario tenham sido marcadas
    Entrada: funcao - identifica a checkbox a desmarcar
    Saida:   nenhuma
  */
  echo("      function CheckHeader(funcao,elementName)\n");
  echo("      {\n");
  echo("        var elem = document.getElementsByName(elementName);\n");
  echo("        var nome_var_all='cod_aluno_all';\n");
  echo("        if (funcao==2)\n");
  echo("          nome_var_all='cod_formador_all';\n");
  echo("        else if (funcao == 3)\n");
  echo("          nome_var_all='cod_convidado_all';\n");
  echo("        else if (funcao == 4)\n");
  echo("          nome_var_all='cod_visitante_all';\n");
  echo("        var i;\n");
  echo("        for(i=0; i < elem.length; i++)\n");
  echo("        {\n");
  echo("          if(elem[i].checked == false)\n");
  echo("            return;\n");
  echo("        }\n");
  echo("        document.getElementsByName(nome_var_all)[0].checked = true; \n");
  echo("      }\n");


  /* ********************************************************************************************
  Funcao Seek_Checked
	Para cada tipo de usuario verifica se existe pelo menos uma checkbox marcada.
	Se houver, habilita o bot�o para mostrar o(s) perfil(s) selecionados.
	Caso contrario desabilita o bot�o.
    Entrada: nenhuma
    Saida:   nenhuma.
  */
  echo("      function Seek_Checked() \n");
  echo("      {\n");
  echo("        var elem=document.Perfil;\n");
  echo("        var check = new Array(5)\n");
  echo("		for (var j = 0;j<5;j++) check[j] = false;\n");
  echo("        var i = 0;\n");
  echo("        while (i < elem.length)\n");
  echo("        {\n");
  echo("		  if(elem.elements[i].checked == true && elem.elements[i].name == 'cod_aluno[]' && check[0] == false){\n");
  echo("		  	document.getElementById('MostrarSelAlunos').className = 'menuUp02';\n");
  echo("			document.getElementById('MostrarSelAlunosB').onclick = function(){setImprimir(0);OpenWindow();document.Perfil.submit();};\n");
  echo("			document.getElementById('ImprimirSelAlunosB').onclick = function(){setImprimir(1);OpenWindow();document.Perfil.submit();};\n");
  echo("			check[0] = true;}\n");
  echo("  		  if(elem.elements[i].checked == true && elem.elements[i].name == 'cod_formador[]' && check[1] == false){\n"); 
  echo("		  	document.getElementById('MostrarSelFormadores').className = 'menuUp02';\n");
  echo("			document.getElementById('MostrarSelFormadoresB').onclick = function(){setImprimir(0);OpenWindow();document.Perfil.submit();};\n");
  echo("			document.getElementById('ImprimirSelFormadoresB').onclick = function(){setImprimir(1);OpenWindow();document.Perfil.submit();};\n");
  echo("			check[1] = true;}\n");
  echo("  		  if(elem.elements[i].checked == true && elem.elements[i].name == 'cod_convidado[]' && check[2] == false){\n"); 
  echo("		  	document.getElementById('MostrarSelConvidados').className = 'menuUp02';\n");
  echo("			document.getElementById('MostrarSelConvidadosB').onclick = function(){setImprimir(0);OpenWindow();document.Perfil.submit();};\n");
  echo("			document.getElementById('ImprimirSelConvidadosB').onclick = function(){setImprimir(1);OpenWindow();document.Perfil.submit();};\n");
  echo("			check[2] = true;}\n");
  echo("  		  if(elem.elements[i].checked == true && elem.elements[i].name == 'cod_visitante[]' && check[3] == false){\n"); 
  echo("		  	document.getElementById('MostrarSelVisitantes').className = 'menuUp02';\n");
  echo("			document.getElementById('MostrarSelVisitantesB').onclick = function(){setImprimir(0);OpenWindow();document.Perfil.submit();};\n");
  echo("			document.getElementById('ImprimirSelConvidadosB').onclick = function(){setImprimir(1);OpenWindow();document.Perfil.submit();};\n");
  echo("			check[3] = true;}\n");  		  	
  echo("  		  if(elem.elements[i].checked == true && elem.elements[i].name == 'cod_coordenador[]' && check[4] == false){\n"); 
  echo("		  	document.getElementById('MostrarSelCoordenadores').className = 'menuUp02';\n");
  echo("			document.getElementById('MostrarSelCoordenadoresB').onclick = function(){setImprimir(0);OpenWindow();document.Perfil.submit();};\n");
  echo("			document.getElementById('ImprimirSelCoordenadoresB').onclick = function(){setImprimir(1);OpenWindow();document.Perfil.submit();};\n");
  echo("			check[4] = true;}\n");  			
  echo("          i++;\n");
  echo("        }\n");
  echo("		if (check[0] == false && (li = document.getElementById('MostrarSelAlunos'))){\n");
  echo("		  	li.className = 'menuUp';\n");
  echo("			document.getElementById('MostrarSelAlunosB').onclick = '';}\n");
  echo("		if (check[1] == false && (li = document.getElementById('MostrarSelFormadores'))){\n");
  echo("		  	li.className = 'menuUp';\n");
  echo("			document.getElementById('MostrarSelFormadoresB').onclick = '';}\n");
  echo("		if (check[2] == false && (li = document.getElementById('MostrarSelConvidados'))){\n");
  echo("		  	li.className = 'menuUp';\n");
  echo("			document.getElementById('MostrarSelConvidadosB').onclick = '';}\n");
  echo("		if (check[3] == false && (li = document.getElementById('MostrarSelVisitantes'))){\n");
  echo("		  	li.className = 'menuUp';\n");
  echo("			document.getElementById('MostrarSelVisitantesB').onclick = '';}\n");
  echo("		if (check[4] == false && (li = document.getElementById('MostrarSelCoordenadores'))){\n");
  echo("		  	li.className = 'menuUp';\n");
  echo("			document.getElementById('MostrarSelCoordenadoresB').onclick = '';}\n");   
  echo("      }\n\n");
  
  /********************************************************************
   * Funcao Inicial
   * Inicia a paginacao dinamica criando os controladores de paginacao
   ********************************************************************/
  echo("function Inicial(limit,flag) {\n");
  echo("	if (limit>=1) {\n");
  echo("		qtdPag=limit;\n");
  echo("		var inicia=flag;\n");
  echo("		var tab=document.getElementById('tabelaInterna');\n");
  echo("		var tbody=tab.lastChild;\n");
  echo("		var controle=document.getElementById('controle_aluno');\n");
  echo("		var coluna=document.createElement('td');\n");
  echo("		coluna.colSpan=\"5\";\n");
  echo("		coluna.align=\"right\";");
  /* Criando os span necessarios para a paginacao, ir para o primeiro nao eh valido pois estamos na primeira pagina. */
  echo("		var first=document.createElement('span');\n");
  echo("		first.innerHTML=\"<<&nbsp;&nbsp;\";\n");
  echo("		coluna.appendChild(first);\n");
  echo("		var ant=document.createElement('span');\n");
  echo("		ant.innerHTML=\"<&nbsp;&nbsp;\";\n");
  echo("		coluna.appendChild(ant);\n");
  echo("		var prox=document.createElement('span');\n");
  echo("		prox.innerHTML=\"&nbsp;&nbsp;>\";\n");
  echo("		var last=document.createElement('span');\n");
  echo("		last.innerHTML=\"&nbsp;&nbsp;>>\";\n");
  echo("		first.className=\"none\";\n");
  echo("		ant.className=\"none\";\n");
  echo("		first.onclick=\"none\";\n");
  echo("		ant.onclick=\"none\";\n");
  /* Verificando se ainda existirao mais paginacoes */
  echo("		if (flag=='L') {\n");
  echo("			prox.className=\"paginacao\";\n");
  echo("			prox.onclick=function(){xajax_MudaDinamic(intervalo,'P',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("			last.className=\"paginacao\";\n");
  echo("			last.onclick=function(){xajax_MudaDinamic(intervalo,'L',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("			aux='BV';\n");
  echo("		} else {\n");
  echo("			prox.className=\"none\";\n");
  echo("			last.className=\"none\";\n");
  echo("			prox.onclick=\"none\";\n");
  echo("			last.onclick=\"none\";\n");
  echo("			aux= 'B';\n");
  echo("		}\n");
  /* Paginando os indices iniciais, ate 5, ou ate o fim das mensagens */
  echo("		for(var i=1;i<=limit;i++) {\n");
  echo("			var GerSpan=document.createElement('span');\n");
  echo("			GerSpan.id=i;\n");
  echo("			if (atual==i) {\n");
  echo("				GerSpan.className=\"paginaAtual\";\n");
  echo("				GerSpan.onclick=\"none\";");
  echo("			} else {\n");
  echo("			GerSpan.className=\"paginacao\";\n");
  echo("			GerSpan.onclick=function(){xajax_PaginacaoDinamic(aux,intervalo,this.id,".$cod_curso.",'".$tipo_usuario."','".$ordem."','".$acao."','".$ativado."','".$desativado."');}\n");
  echo("			}\n");
  echo("			GerSpan.innerHTML='<a>[&nbsp;'+i+'&nbsp;]</a>';\n");
  echo("			coluna.appendChild(GerSpan);\n");
  echo("		}\n");
  echo("		coluna.appendChild(prox);\n");
  echo("		coluna.appendChild(last);\n");
  echo("		var linha=document.createElement('tr');\n");
  echo("		linha.setAttribute('name','germen');\n");
  echo("		linha.id=\"control\";\n");
  echo("		linha.appendChild(coluna);\n");
  echo("		tbody.insertBefore(linha,controle);\n");
  echo("	}\n");
  echo("}\n");


  /********************************************************************************************** 
   * Paginacao
   * Funcao que controla paginacao - estou criando a paginacao nova...depois de mudar o intervalo 
   **********************************************************************************************/
  echo("function Paginacao(status) {\n");
  echo("	var tab=document.getElementById('tabelaInterna');\n");
  echo("	var tbody=document.createElement('tbody');\n");
  echo("	var td_pagina=document.createElement('td');\n");
  echo("	td_pagina.colSpan=\"5\";");
  echo("	td_pagina.align=\"right\";");
  echo("	var span_first=document.createElement('span');\n");
  echo("	span_first.innerHTML=\"<<&nbsp;&nbsp;\";\n");
  echo("	var span_ant=document.createElement('span');\n");
  echo("	span_ant.innerHTML=\"<&nbsp;&nbsp;\";\n");
  echo("	var span_last=document.createElement('span');\n");
  echo("	span_last.innerHTML=\"&nbsp;&nbsp;>>\";\n");
  echo("	var span_prox=document.createElement('span');\n");
  echo("	span_prox.innerHTML=\"&nbsp;&nbsp;>\";\n");
  /* Verificando se a paginacao para voltar esta liberada, ou seja se nao eh a primeira pagina */
  echo("	if(status== 'BV') {\n");
  echo("		span_first.className=\"none\";\n");
  echo("		span_first.onclick=\"none\";\n");
  echo("		span_ant.className=\"none\";\n");
  echo("		span_ant.onclick=\"none\";\n");
  echo("		span_last.className=\"paginacao\";\n");
  echo("		span_last.onclick=function(){xajax_MudaDinamic(intervalo,'L',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		span_prox.className=\"paginacao\";\n");
  echo("		span_prox.onclick=function(){xajax_MudaDinamic(intervalo,'P',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		aux='BV';\n");
  echo("	}\n");
  echo("	if(status== 'LV' || status== 'LF') {\n");
  echo("		span_first.className=\"paginacao\";\n");
  echo("		span_first.onclick=function(){xajax_MudaDinamic(intervalo,'PR',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		span_ant.className=\"paginacao\";\n");
  echo("		span_ant.onclick=function(){xajax_MudaDinamic(intervalo,'A',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		span_last.className=\"paginacao\";\n");
  echo("		span_last.onclick=function(){xajax_MudaDinamic(intervalo,'L',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		span_prox.className=\"paginacao\";\n");
  echo("		span_prox.onclick=function(){xajax_MudaDinamic(intervalo,'P',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		aux='LF';\n");
  echo("	}\n");
  echo("	if(status== 'BF') {\n");
  echo("		span_first.className=\"paginacao\";\n");
  echo("		span_first.onclick=function(){xajax_MudaDinamic(intervalo,'PR',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		span_ant.className=\"paginacao\";\n");
  echo("		span_ant.onclick=function(){xajax_MudaDinamic(intervalo,'A',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		span_last.className=\"none\";\n");
  echo("		span_last.onclick=\"none\";\n");
  echo("		span_prox.className=\"none\";\n");
  echo("		span_prox.onclick=\"none\";\n");
  echo("		aux='BF';\n");
  echo("	}");
  echo("	if(status== 'B') {\n");
  echo("  		span_first.className=\"none\";\n");
  echo("		span_first.onclick=\"none\";\n");
  echo("		span_ant.className=\"none\";\n");
  echo("		span_ant.onclick=\"none\";\n");
  echo("		span_last.className=\"none\";\n");
  echo("		span_last.onclick=\"none\";\n");
  echo("		span_prox.className=\"none\";\n");
  echo("		span_prox.onclick=\"none\";\n");
  echo("		aux='B';\n");
  echo("	}");
  echo("	td_pagina.appendChild(span_first);\n");
  echo("	td_pagina.appendChild(span_ant);\n");
  /* Criando os indices */
  echo("	for(var i=1;i<=qtdPag;i++) {\n");
  echo("		var td_span=document.createElement('span');\n");
  echo("		ind=(parseInt(i))+(parseInt(intervalo))-1;\n");
  echo("		if (ind==atual) {\n");
  echo("			td_span.className=\"paginaAtual\";\n");
  echo("			td_span.onclick=\"none\";\n");
  echo("		} else {\n");
  echo("			td_span.className=\"paginacao\";\n");
  echo("			td_span.onclick=function(){xajax_PaginacaoDinamic(aux,intervalo,this.id,".$cod_curso.",'".$tipo_usuario."','".$ordem."','".$acao."','".$ativado."','".$desativado."');}\n");
  echo("		}\n");
  echo("		td_span.id=ind;\n");
  echo("		td_span.innerHTML='<a>[&nbsp;'+ind+'&nbsp;]</a>';\n");
  echo("		td_pagina.appendChild(td_span);\n");
  echo("	}\n");
  /* Verificando a paginacao para frente */
  echo("    td_pagina.appendChild(span_prox);\n");
  echo("	td_pagina.appendChild(span_last);\n");
  echo("	var tr_span=document.createElement('tr');\n");
  echo("	tr_span.setAttribute('name','germen');\n");
  echo("	tr_span.id=\"control\";\n");
  echo("	tr_span.appendChild(td_pagina);\n");
  echo("	tbody.appendChild(tr_span);\n");
  echo("	tab.appendChild(tbody);\n");
  echo("}\n");


  echo("function MudaIntervalo(aux) {\n");
  echo("	intervalo=aux;\n");
  echo("}\n");

  echo("function ApagaPagina(flag) {\n");
  echo("	var	tr_ger=getElementsByName_iefix('tr', 'germen');\n");
  echo("	var tam=tr_ger.length;\n");
  echo("	var naveg=document.getElementById('control');\n");
  echo("	naveg.parentNode.removeChild(naveg);\n");
  echo("	if(flag=='T') {\n");
  echo("		for(var i=1; i<tam; i++){\n");
  echo("			var ger=document.getElementById('ger');\n");
  echo("			ger.parentNode.removeChild(ger);\n");
  echo("		}\n");
  echo("	}");
  echo("}\n");

  echo("function CriaElementoTab(nome,dtins,dados,cod,acao,port) {\n");
  echo("	var tab=document.getElementById('tabelaInterna');\n");
  echo("	var tbody=document.createElement('tbody');");
  echo("	var td_check=document.createElement('td');\n");
  echo("	td_check.style.width='2%';");
  echo("	var check=document.createElement('input');\n");
  echo("	check.type=\"checkbox\";\n");
  echo("	check.setAttribute('name','cod_usu[]');\n");
  echo("	check.setAttribute('value',cod);\n");
  echo("	check.onclick=new Function(\"VerificaCheck()\");\n");
  echo("	td_check.appendChild(check);\n");
  echo("	var td_nome=document.createElement('td');\n");
  echo("	td_nome.align=\"left\";\n");
  echo("	td_nome.innerHTML=nome;\n");
  echo("	var td_data=document.createElement('td');\n");
  echo("	td_data.innerHTML=dtins;\n");
  echo("	var td_dados=document.createElement('td');\n");
  echo("	td_dados.innerHTML=\"<a href=gerenciamento2.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=".$acao."&amp;ordem=".$ordem."&amp;opcao=dados&amp;cod_usu[]=\"+cod+\"\>".RetornaFraseDaLista($lista_frases,79)."</a>\";\n");
  echo("	var tr_ger=document.createElement('tr');\n");
  echo("	tr_ger.setAttribute('name','germen');\n");
  echo("	tr_ger.id=\"ger\";\n");
  echo("	tr_ger.appendChild(td_check);\n");
  echo("	tr_ger.appendChild(td_nome);\n");
  echo("	tr_ger.appendChild(td_data);\n");
  echo("	tr_ger.appendChild(td_dados);\n");
  /* Caso esteja em inscricoes registradas precisamos criar o campo portifolio */
  echo("	if(acao== 'R'){\n");
  echo("		var td_port=document.createElement('td');\n");
  echo("		td_port.innerHTML=port;\n");
  echo("		td_port.id='status_port'+cod;\n");
  echo("		tr_ger.appendChild(td_port);\n");
  echo("	}\n");
  echo("	tab.appendChild(tbody);");
  echo("	tbody.appendChild(tr_ger);\n");
  echo("}\n");

  /********************************************************************
   * Como no IE getElementsByName() não funciona, usar a funcao abaixo. 
   ********************************************************************/
  echo("function getElementsByName_iefix(tag, name) {\n");
  echo("	var elem = document.getElementsByTagName(tag);\n");
  echo("	var arr = new Array();\n");
  echo("	for(var i = 0, iarr = 0; i < elem.length; i++) {\n");
  echo("		var att = elem[i].getAttribute('name');\n");
  echo("		if(att == name) {\n");
  echo("			arr[iarr] = elem[i];\n");
  echo("			iarr++;\n");
  echo("		}\n");
  echo("	}\n");
  echo("	return arr;\n");
  echo("}\n");

  echo("    </script>\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 1 - Perfil */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)."</h4>\n");
  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <div id=\"mudarFonte\">\n");
  echo("	    <a href=\"#\" onclick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	    <a href=\"#\" onclick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	    <a href=\"#\" onclick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("          </div>\n");

  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  
  echo("              <!-- Botoes de Acao -->\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  
  if (EFormador($sock,$cod_curso,$cod_usuario)&&(!$_SESSION['visao_aluno_s']))
  {
    
    echo("                  <li><a href=\"editar_orientacao.php?cod_curso=".$cod_curso."\">");
    /* 2-Editar orientação para preenchimento do Perfil */
    echo(RetornaFraseDaLista($lista_frases,2)."</a></li>");
  }
  echo("                  <li><a href=\"#\" onclick=\"return(OpenWindowLink(".$cod_usuario."));\">");
  /* 132 - Meu Perfil*/
  echo(RetornaFraseDaLista($lista_frases,132)."</a></li>");
  
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <form target=\"PerfilDisplay\" name=\"Perfil\" action=\"exibir_perfis.php\" method=\"get\" onsubmit=\"OpenWindow();\">\n");
  echo("                  <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("                  <input type=\"hidden\" name=\"imprimir\" id=\"imprimir\" value=\"\" />\n");
  echo("                  <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaInterna\" class=\"tabInterna\">\n");

  $ultimo_acesso = PenultimoAcesso($sock,$cod_usuario,"");

  $icone = "<img src=\"../imgs/icPerfil.gif\" border=\"0\" alt=\"".RetornaFraseDaLista($lista_frases, 1)."\" />";

  /*
  ================
  Tabela de alunos
  ================
  */

  $lista = ListaUsuario($sock,"A",$cod_curso);

  /* Sistema de Paginacao */
  $num=count($lista);
  /* Numero de mensagens exibidas por pagina.*/
  $msg_por_pag=10;
  /* Calcula o numero de paginas geradas.*/
  $total_pag = ceil($num / $msg_por_pag);

  /* Se a pagina atual nao estiver setada entao, por padrao, atribui-lhe o valor 1. */
  /* Se estiver setada, verifica se a pagina eh maior que o total de paginas, se for */
  /* atribui o valor de $total_pag  a $pag_atual.                                    */
  if ((!isset($pag_atual))or($pag_atual=='')or($pag_atual==0)) {
  	$pag_atual =  1;
  } else {
  	$pag_atual = min($pag_atual, $total_pag);
  }


  echo("                    <tr class=\"head01\">\n");
  echo("                      <td colspan=\"3\">\n");
  /* 3-Alunos */
  echo("                        <div style=\"font-weight:bold;\" align=\"left\">".RetornaFraseDaLista($lista_frases,3)."</div>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  if (count($lista) > 0)
  {
    echo("                    <tr class=\"head\">\n");
    echo("                      <td width=\"2%\"><input class=\"input\" type=\"checkbox\" name=\"cod_aluno_all\" value=\"1\" onclick=\"CheckAll(1);Seek_Checked();\"  /></td>\n");
    /* 4-Nome */
    echo("                      <td align=\"left\" width=\"68%\">".RetornaFraseDaLista($lista_frases,4)."</td>\n");
    /* 5-Data */
    echo("                      <td width=\"30%\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
    echo("                    </tr>\n");

    $num_usuario=0;
    foreach ($lista as $i => $dados)
    # for ($c=0;$c<count($lista);$c++)
    {
      $linha = Retornaperfil($sock,$dados['cod_usuario']);

      $bopen_tag = "";
      $bclose_tag = "";

      if ((isset($linha)) && ($linha['data'] > $ultimo_acesso))
      {
        $bopen_tag = "<b>";
        $bclose_tag = "</b>";
      }

      echo("                    <tr class=\"altColor".($num_usuario%2)."\">\n");
      echo("                      <td>\n");
      echo("                        <input class=\"input\" type=\"checkbox\" name=\"cod_aluno[]\" value=\"".$dados['cod_usuario']."\" onclick=\"UnCheckHeader(1);CheckHeader(1,'cod_aluno[]');Seek_Checked();\" />\n");
      echo("                      </td>\n");
      echo("                      <td align=\"left\">\n");
      echo("                        ".$icone." ".$bopen_tag."<a href=\"#\" onclick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">".$dados['nome']."</a>".$bclose_tag."\n");
      echo("                      </td>\n");

      if (isset($linha)){
        echo("                      <td id=\"data_".$dados['cod_usuario']."\">".$bopen_tag.UnixTime2DataHora($linha['data']).$bclose_tag."\n");
      }
      else{
        /* 6-não disponível */
        echo("                      <td id=\"data_".$dados['cod_usuario']."\"> (".RetornaFraseDaLista($lista_frases,6).")</td>\n");
      }
      echo("                    </tr>\n");
      $num_usuario++;
    }
    
    echo("                    <tr id=\"controle_aluno\">\n");
    echo("                      <td colspan=\"3\">\n");
    echo("                        <ul>\n");
    echo("                          <li id=\"MostrarSelAlunos\" class=\"menuUp\">\n");
    /* 8 - Mostrar Selecionados */
    echo("                            <span id=\"MostrarSelAlunosB\">".RetornaFraseDaLista($lista_frases,8)."</span>\n");
    /* 138 - Imprimir Selecionados */
    echo("                            <span id=\"ImprimirSelAlunosB\">".RetornaFraseDaLista($lista_frases,138)."</span>\n");
    echo("                          <br /><br />\n");
    echo("                          </li>\n");    
    echo("                        </ul>\n");
    echo("                      </td>\n");
    echo("                    </tr>\n");
  }
  else{
  	echo("<tr>\n");
  	/* 133 - Nenhum
  	   18  - Aluno
  	   134 - encontrado */
	echo("	<td align=\"left\" colspan=\"3\">".RetornaFraseDaLista($lista_frases,133)." ".RetornaFraseDaLista($lista_frases,18)." ".RetornaFraseDaLista($lista_frases,134)."</td>\n");
	echo("</tr>\n");
  }



  /*
  =====================
  Tabela dos Convidados
  =====================
  */
  $lista_convidados = ListaConvidados ($sock, $cod_curso);

  echo("                    <tr class=\"head01\">\n");
  echo("                      <td colspan=\"3\">\n");
  /* 124-Convidados */
  echo("                        <div style=\"font-weight:bold;\" align=\"left\">".RetornaFraseDaLista($lista_frases,124)."</div>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  if (count($lista_convidados) > 0)
  {
    echo("                    <tr class=\"head\">\n");
    echo("                      <td width=\"2%\"><input class=\"input\" type=\"checkbox\" name=\"cod_convidado_all\" value=\"1\" onclick=\"CheckAll(3);Seek_Checked();\" /></td>\n");
    /* 4-Nome */
    echo("                      <td align=\"left\" width=\"68%\">".RetornaFraseDaLista($lista_frases,4)."</td>\n");
    /* 5-Data */
    echo("                      <td width=\"30%\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
    echo("                    </tr>\n");

    $num_usuario=0;
    foreach ($lista_convidados as $i => $dados)
    {
      if ($dados['cod_usuario'] >= 0)
      {
        $tupla = RetornaPerfil($sock, $dados['cod_usuario']);

        $bopen_tag = "";
        $bclose_tag = "";

        if ((isset($tupla)) && ($tupla['data'] > $ultimo_acesso))
        {
          $bopen_tag = "<b>";
          $bclose_tag = "</b>";
        }

        echo("                    <tr class=\"altColor".($num_usuario%2)."\">\n");
        echo("                      <td>\n");
        echo("                        <input class=\"input\" type=\"checkbox\" name=\"cod_convidado[]\" value=\"".$dados['cod_usuario']."\" onclick=\"UnCheckHeader(3);CheckHeader(3,'cod_convidado[]');Seek_Checked();\" />\n");
        echo("                      </td>\n");
        echo("                      <td align=\"left\">\n");
        echo("                        ".$icone." ".$bopen_tag."<a href=\"#\" onclick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">".$dados['nome']."</a>".$bclose_tag."\n");
        echo("                      </td>\n");

        if (isset($tupla))
        {
          echo("                      <td id=\"data_".$dados['cod_usuario']."\">".$bopen_tag.UnixTime2DataHora($tupla['data']).$bopen_tag."\n");
        }
        else
        {
          /* 6-não disponível */
          echo("                      <td id=\"data_".$dados['cod_usuario']."\">(".RetornaFraseDaLista($lista_frases, 6).")</td>\n");
        }
        echo("                    </tr>\n");
      }
      $num_usuario++;
    }
    echo("                    <tr>\n");
    echo("                      <td colspan=\"3\">\n");
    echo("                        <ul >\n");
    echo("                          <li id=\"MostrarSelConvidados\" class=\"menuUp\">\n");
    /* 8 - Mostrar Selecionados */
    echo("                            <span id=\"MostrarSelConvidadosB\">".RetornaFraseDaLista($lista_frases,8)."</span>\n");
    /* 138 - Imprimir Selecionados */
    echo("                            <span id=\"ImprimirSelConvidadosB\">".RetornaFraseDaLista($lista_frases,138)."</span>\n");
    echo("                          <br /><br />\n");
    echo("                          </li>\n");    
    echo("                        </ul>\n");
    echo("                      </td>\n");
    echo("                    </tr>\n");
  }
  else{
	echo("<tr>\n");
  	/* 133 - Nenhum
  	   135 - Convidado
  	   134 - encontrado	*/	
  	echo("	<td align=\"left\" colspan=\"3\">".RetornaFraseDaLista($lista_frases,133)." ".RetornaFraseDaLista($lista_frases,135)." ".RetornaFraseDaLista($lista_frases,134)."</td>\n");
	echo("</tr>\n");
  }

  /*
  ====================
  Tabela de Visitantes
  ====================
  */

  $lista_visitantes = ListaUsuario($sock, 'V',$cod_curso);

  echo("                    <tr class=\"head01\">\n");
  echo("                      <td colspan=\"3\">\n");
  /* 131 - Visitantes */
  echo("                        <div style=\"font-weight:bold;\" align=\"left\">".RetornaFraseDaLista($lista_frases,131)."</div>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  if (count($lista_visitantes) > 0)
  {
    echo("                    <tr class=\"head\">\n");
    echo("                      <td width=\"2%\"><input class=\"input\" type=\"checkbox\" name=\"cod_visitante_all\" value=\"1\" onclick=\"CheckAll(4);Seek_Checked();\"  /></td>\n");
    /* 4-Nome */
    echo("                      <td align=\"left\" width=\"68%\">".RetornaFraseDaLista($lista_frases,4)."</td>\n");
    /* 5-Data */
    echo("                      <td width=\"30%\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
    echo("                    </tr>\n");

    $num_usuario=0;
    foreach ($lista_visitantes as $i => $dados)
    {
      if ($dados['cod_usuario'] >= 0)
      {
        $tupla = RetornaPerfil($sock, $dados['cod_usuario']);

        $bopen_tag = "";
        $bclose_tag = "";

        if ((isset($tupla)) && ($tupla['data'] > $ultimo_acesso))
        {
          $bopen_tag = "<b>";
          $bclose_tag = "</b>";
        }

        echo("                    <tr class=\"altColor".($num_usuario%2)."\">\n");
        echo("                      <td align=\"center\">\n");
        echo("                        <input class=\"input\" type=\"checkbox\" name=\"cod_visitante[]\" value=\"".$dados['cod_usuario']."\" onclick=\"UnCheckHeader(4);CheckHeader(4,'cod_visitante[]');Seek_Checked();\" />\n");
        echo("                      </td>\n");
        echo("                      <td align=\"left\">\n");
        echo("                        ".$icone." ".$bopen_tag."<a href=\"#\" onclick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">".$dados['nome']."</a>".$bclose_tag);
        echo("                      </td>\n");

        if (isset($tupla))
        {
          echo("                      <td id=\"data_".$dados['cod_usuario']."\">".$bopen_tag.UnixTime2DataHora($tupla['data']).$bopen_tag."\n");
        }
        else
        {
          /* 6-não disponível */
          echo("                      <td id=\"data_".$dados['cod_usuario']."\">(".RetornaFraseDaLista($lista_frases, 6).")</td>\n");
        }
        echo("                    </tr>\n");
      }
      $num_usuario++;
    }
    echo("                    <tr>\n");
    echo("                      <td colspan=\"3\">\n");
    echo("                        <ul >\n");
    echo("                          <li id=\"MostrarSelVisitantes\" class=\"menuUp\">\n");
    /* 8 - Mostrar Selecionados */
    echo("                            <span id=\"MostrarSelVisitantesB\">".RetornaFraseDaLista($lista_frases,8)."</span>\n");
    /* 138 - Imprimir Selecionados */
    echo("                            <span id=\"ImprimirSelVisitantesB\">".RetornaFraseDaLista($lista_frases,138)."</span>\n");
    echo("                          <br /><br />\n");
    echo("                          </li>\n");
    echo("                        </ul>\n");    
    echo("                      </td>\n");
    echo("                    </tr>\n");
  }
  else{
	echo("<tr>\n");
  	/* 133 - Nenhum
  	   136 - Visitante
  	   134 - encontrado	*/
  	echo("	<td align=\"left\" colspan=\"3\">".RetornaFraseDaLista($lista_frases,133)." ".RetornaFraseDaLista($lista_frases,136)." ".RetornaFraseDaLista($lista_frases,134)."</td>\n");
	echo("</tr>\n");
  }

  /*
  ====================
  Tabela de Formadores
  ====================
  */

  $dados_coord = ListaCoordenador($sock, $cod_curso);

  if(count($dados_coord)>0)
  {
    $cod_coordenador = $dados_coord['cod_usuario'];
    // esta variavel indica se o coordenador deve ser exibido na tabela de formadores ou na tabela de coordenador
    $coord_como_form   = ($dados_coord['status'] != 'C');
  }
  else
  {
    $coord_como_form   = false;
  }

  $lista = ListaUsuario($sock, "F", $cod_curso);

  $total = count($lista);


  if (! $coord_como_form)
  {
    // o coordenador é exibido como coordenador mesmo. Portanto descontamos o tamanho da lista de formadores de um (o coordenador)
    $total -= 1;
  }


  echo("                    <tr class=\"head01\">\n");
  echo("                      <td colspan=\"3\">\n");
  /* 7 - Formadores */
  echo("                        <div style=\"font-weight:bold;\" align=\"left\">".RetornaFraseDaLista($lista_frases,7)."</div>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  if ($total > 0)
  {
    echo("                    <tr class=\"head\">\n");
    echo("                      <td width=\"2%\"><input class=\"input\" type=\"checkbox\" name=\"cod_formador_all\" value=\"1\" onclick=\"CheckAll(2);Seek_Checked();\"  /></td>\n");
    /* 4-Nome */
    echo("                      <td align=\"left\" width=\"68%\">".RetornaFraseDaLista($lista_frases,4)."</td>\n");
    /* 5-Data */
    echo("                      <td width=\"30%\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
    echo("                    </tr>\n");

    $num_usuario=0;
    foreach ($lista as $i => $dados)
    # for ($i = 0; $i < $total; $i++)
    {
      # if (($lista[$i]['cod_usuario'] != $cod_coordenador) && ($lista[$i]['cod_usuario'] >= 0))
      // se o coordenador quiser ser chamado de formador, não devemos exibi-lo
      if (($dados['cod_usuario'] >= 0) && (($dados_coord['status'] == 'F') || ($dados['cod_usuario'] != $cod_coordenador)))
      {
        $tupla = RetornaPerfil($sock, $dados['cod_usuario']);

        $bopen_tag = "";
        $bclose_tag = "";

        if ((isset($tupla)) && ($tupla['data'] > $ultimo_acesso))
        {
          $bopen_tag = "<b>";
          $bclose_tag = "</b>";
        }

        echo("                    <tr class=\"altColor".($num_usuario%2)."\">\n");
        echo("                      <td align=\"center\">\n");
        echo("                        <input class=\"input\" type=\"checkbox\" name=\"cod_formador[]\" value=\"".$dados['cod_usuario']."\" onclick=\"UnCheckHeader(2);CheckHeader(2,'cod_formador[]');Seek_Checked();\" />\n");
        echo("                      </td>\n");
        echo("                      <td align=\"left\">\n");
        echo("                        ".$icone." ".$bopen_tag."<a href=\"#\" onclick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">".$dados['nome']."</a>".$bclose_tag."\n");
        echo("                      </td>\n");

        if (isset($tupla))
        {
          echo("                      <td id=\"data_".$dados['cod_usuario']."\">".$bopen_tag.UnixTime2DataHora($tupla['data']).$bopen_tag."\n");
        }
        else
        {
          /* 6-não disponivel */
          echo("                      <td id=\"data_".$dados['cod_usuario']."\">".RetornaFraseDaLista($lista_frases, 6));
         echo("		</td>");
        }
        echo("                        </tr>\n");
      }
      $num_usuario++;
    }
    echo("                    <tr>\n");
    echo("                      <td colspan=\"3\">\n");
    echo("                        <ul >\n");
    echo("                          <li id=\"MostrarSelFormadores\" class=\"menuUp\">\n");
    /* 8 - Mostrar Selecionados */
    echo("                            <span id=\"MostrarSelFormadoresB\">".RetornaFraseDaLista($lista_frases,8)."</span>\n");
    /* 138 - Imprimir Selecionados */
    echo("                            <span id=\"ImprimirSelFormadoresB\">".RetornaFraseDaLista($lista_frases,138)."</span>\n");
    echo("                          <br /><br />\n");
    echo("                          </li>\n");
    echo("                        </ul>\n");
    echo("                      </td>\n");
    echo("                    </tr>\n");
  }
  else{
	echo("<tr>\n");
  	/* 133 - Nenhum
  	   137 - Formador
  	   134 - encontrado	*/
  	echo("	<td align=\"left\" colspan=\"3\">".RetornaFraseDaLista($lista_frases,133)." ".RetornaFraseDaLista($lista_frases,137)." ".RetornaFraseDaLista($lista_frases,134)."</td>\n");
	echo("</tr>\n");
  }

  /*
  =====================
  Tabela do Coordenador
  =====================
  */
  if ((isset($dados_coord)) && ($dados_coord['status'] == 'C'))
  {
    # $linha=ListaCoordenador($sock,$cod_curso);
    $linha_perfil = RetornaPerfil($sock, $dados_coord['cod_usuario']);
    echo("                    <tr class=\"head01\">\n");
    echo("                      <td colspan=\"3\">\n");
    /* 28 - Coordenador */
    echo("                        <div style=\"font-weight:bold;\" align=\"left\">".RetornaFraseDaLista($lista_frases,28)."</div>\n");
    echo("                      </td>\n");
    echo("                    </tr>\n");
    echo("                    <tr class=\"head\">\n");
    echo("                      <td width=\"2%\">&nbsp;</td>\n");
    /* 4-Nome */
    echo("                      <td align=\"left\" width=\"68%\">".RetornaFraseDaLista($lista_frases,4)."</td>\n");
    /* 5-Data */
    echo("                      <td width=\"30%\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
    echo("                    </tr>\n");

    $bopen_tag = "";
    $bclose_tag = "";

    if ((isset($linha_perfil)) && ($linha_perfil['data'] > $ultimo_acesso))
    {
      $bopen_tag = "<b>";
      $bclose_tag = "</b>";
    }

    echo("                    <tr>\n");
    echo("                      <td align=\"center\">\n");
    echo("                        <input class=\"input\" onclick=\"Seek_Checked();\" type=\"checkbox\" name=\"cod_coordenador[]\" value=\"".$dados_coord['cod_usuario']."\" />\n");
    echo("                      </td>\n");
    echo("                      <td align=\"left\">\n");
    echo("                        ".$icone." ".$bopen_tag."<a href=\"#\" onclick=\"return(OpenWindowLink(".$dados_coord['cod_usuario']."));\" class=\"text\">".$dados_coord['nome']."</a>".$bclose_tag."\n");
    echo("                      </td>\n");

    # $linha = RetornaPerfil($sock, $linha['cod_usuario']);
    if (isset($linha_perfil))
    {
        echo("                      <td id=\"data_".$dados_coord['cod_usuario']."\">".$bopen_tag.UnixTime2DataHora($linha_perfil['data']).$bclose_tag."\n");
    }
    else
    {
      /* 6-não disponível */
      echo("                      <td id=\"data_".$dados_coord['cod_usuario']."\">(".RetornaFraseDaLista($lista_frases, 6).")</td>\n");
    }
    echo("                    </tr>\n");
    echo("                    <tr>\n");
    echo("                      <td colspan=\"3\">\n");
    echo("                        <ul >\n");
    echo("                          <li id=\"MostrarSelCoordenadores\" class=\"menuUp\">\n");
    /* 8 - Mostrar Selecionados */
    echo("                            <span id=\"MostrarSelCoordenadoresB\">".RetornaFraseDaLista($lista_frases,8)."</span>\n");
    /* 138 - Imprimir Selecionados */
    echo("                            <span id=\"ImprimirSelCoordenadoresB\">".RetornaFraseDaLista($lista_frases,138)."</span>\n");
    echo("                          <br /><br />\n");
    echo("                          </li>\n");
    echo("                        </ul>\n");    
    echo("                      </td>\n");
    echo("                    </tr>\n");
  }
  echo("                  </table>\n");
  echo("                </form>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");

//  echo("          <script type=\"text/javascript\">\n");
//  echo("            Iniciar();\n");
//  echo("          </script>\n");

  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>