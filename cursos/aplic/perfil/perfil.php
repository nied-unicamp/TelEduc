<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perfil/perfil.php

    TelEduc - Ambiente de Ensino-Aprendizagem a DistÔøΩcia
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

    Nied - Ncleo de InformÔøΩica Aplicada ÔøΩEducaÔøΩo
    Unicamp - Universidade Estadual de Campinas
    Cidade UniversitÔøΩia "Zeferino Vaz"
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

/* C√≥digo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perfil.inc");

  $cod_ferramenta=13;
  
  $cod_ferramenta_ajuda = $cod_ferramenta;
 
  $cod_pagina_ajuda=1;
  
  $tipo_usuario="A";

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das funÔøΩÔøΩes em PHP que vocÔøΩ quer chamar atravÔøΩs do xaja
  //$objAjax->register(XAJAX_FUNCTION,"IniciaPaginacaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MudaDinamic");
  //$objAjax->register(XAJAX_FUNCTION,"PaginacaoDinamic");
  // Registra funÁıes para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  include("../topo_tela.php");
  
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro √© a a√ß√£o, o segundo √© o n√∫mero da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("enviouOrientacao", 50, 51);

  /* variavel de ordenacao da lista de alunos. Ex: ordenar por nome */
  if (!isset($ordem))
  {
    $ordem="nome";
  }
  
  $lista = ListaUsuario($sock,"A",$cod_curso);
  
  /* N˙mero de usu·rios exibidos por p·gina.             */
  if (!isset($user_por_pag)) $user_por_pag = 10;

  $total_user = count($lista);

  /* Se o n˙mero total de usu·rios for superior que o numero de usu·rios por  */
  /* p·gina ent„o calcula o total de p·ginas. Do contr·rio, define o n˙mero de     */
  /* p·ginas para 1.                                                           */

  /* Calcula o n˙mero de p·ginas geradas.    */
  if($total_user > $user_por_pag)
  $totalPag = ceil($total_user / $user_por_pag);
  else
  $totalPag = 1;

  /* Se a p·gina atual n„o estiver setada ent„o, por padr„o, atribui-lhe o valor 1. */
  /* Se estiver setada, verifica se a p·gina È maior que o total de p·ginas, se for */
  /* atribui o valor de $total_pag ‡ $pagAtual.                                    */
  if ((!isset($pagAtual))or($pagAtual=='')or($pagAtual==0))
  $pagAtual =  1;
  else $pagAtual = min($pagAtual, $totalPag);
  

  /* Fun√ß√µes javascript */
  echo("    <script type=\"text/javascript\">\n");
  /* <Variaveis globais> */
  echo(" 	  var imprimir_perfil = 0;\n");
  echo("	  var qtdPag=1;\n");
  echo("	  var intervalo=1;\n");
  echo("	  var atual=1;\n");
  echo("	  var aux='T';\n");
  echo("      var pagAtual = ".$pagAtual.";\n");
  echo("      var total_user = ".$total_user.";\n");
  echo("      var totalPag = ".$totalPag.";\n");
  echo("      var user_por_pag = ".$user_por_pag.";\n");
  /* </ Variaveis globais> */
  echo("      function Iniciar()\n");
  echo("      {\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      ExibeUserPagina(".$pagAtual.");\n");
  echo("      }\n\n");
  
  echo("      function ExibeUserPagina(pagina){\n");
  echo("        var i = 0;\n");
  echo("		var num = 1;\n");
  echo("        if (pagina < 1) return;\n");

  echo("        inicio = 1;\n");
  echo("        final = ((totalPag)*".$user_por_pag.")+1;\n");
  echo("        for (i=inicio; i < final; i++){\n");
  echo("          if (!document.getElementById('tr_'+i)) break;\n");
  echo("          document.getElementById('tr_'+i).style.display=\"none\";\n");
  echo("        }\n");

  echo("        var browser=navigator.appName;\n\n");
  echo("        inicio = ((pagina-1)*".$user_por_pag.")+1;\n");
  echo("        final = ((pagina)*".$user_por_pag.");\n");
  echo("        for (i=inicio; i < final+1; i++){\n");
  echo("          if (!document.getElementById('tr_'+(i+1)) || i > total_user){break;}\n");
  echo("          if (browser==\"Microsoft Internet Explorer\")\n");
  echo("            document.getElementById('tr_'+i).style.display=\"block\";\n");
  echo("          else\n");
  echo("            document.getElementById('tr_'+i).style.display=\"table-row\";\n");
  echo("        }\n\n");
  
  echo("		var total = (total_user);\n");
  echo("        pagAtual=pagina;\n\n");
    
  echo("		if (pagAtual==totalPag){;");
  echo("        	if (browser==\"Microsoft Internet Explorer\")\n");
  echo("          		document.getElementById('tr_'+total).style.display=\"block\";\n");
  echo("        	else\n");
  echo("          		document.getElementById('tr_'+total).style.display=\"table-row\";\n");
  echo("		}");

  echo("        if (pagAtual != 1){\n");
  echo("          document.getElementById('paginacao_first').onclick = function(){ ExibeUserPagina(1); };\n");
  echo("          document.getElementById('paginacao_first').className = \"link\";\n");
  echo("          document.getElementById('paginacao_back').onclick = function(){ ExibeUserPagina(pagAtual-1); };\n");
  echo("          document.getElementById('paginacao_back').className = \"link\";\n");
  echo("        }else{\n");
  echo("          document.getElementById('paginacao_first').onclick = function(){};\n");
  echo("          document.getElementById('paginacao_first').className = \"\";\n");
  echo("          document.getElementById('paginacao_back').onclick = function(){};\n");
  echo("          document.getElementById('paginacao_back').className = \"\";\n");
  echo("        }\n");
  echo("        document.getElementById('paginacao_first').innerHTML = \"&lt;&lt;\";\n");
  echo("        document.getElementById('paginacao_back').innerHTML = \"&lt;\";\n");
  echo("        inicio = pagAtual-2;\n");
  echo("        if (inicio < 1) inicio=1;\n");
  echo("        fim = pagAtual+2;\n");
  echo("        if (fim > totalPag) fim=totalPag;\n");
  echo("        var controle=1;\n");
  echo("        var vetor= new Array();\n");
  echo("        for (j=inicio; j <= fim; j++){\n");
  echo("          // A p√°gina atual N√£o √© exibida com link.\n");
  echo("          if (j == pagAtual){\n");
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
  echo("        document.getElementById('paginacao_1').onclick=function(){ ExibeUserPagina(vetor[1]); };\n");
  echo("        document.getElementById('paginacao_2').onclick=function(){ ExibeUserPagina(vetor[2]); };\n");
  echo("        document.getElementById('paginacao_3').onclick=function(){ ExibeUserPagina(vetor[3]); };\n");
  echo("        document.getElementById('paginacao_4').onclick=function(){ ExibeUserPagina(vetor[4]); };\n");
  echo("        document.getElementById('paginacao_5').onclick=function(){ ExibeUserPagina(vetor[5]); };\n\n");

  echo("        /* Se a p√°gina atual N√£o for a √∫ltima p√°gina ent√£o cria um   \n");
  echo("           link para a pr√≥xima p√°gina */\n");
  echo("        if (pagAtual != totalPag){\n");
  echo("          document.getElementById('paginacao_fwd').onclick = function(){ ExibeUserPagina(pagAtual+1); };\n");
  echo("          document.getElementById('paginacao_fwd').className = \"link\";\n");
  echo("          document.getElementById('paginacao_last').onclick = function(){ ExibeUserPagina(totalPag); };\n");
  echo("          document.getElementById('paginacao_last').className = \"link\";\n");
  echo("        }\n");
  echo("        else{\n");
  echo("          document.getElementById('paginacao_fwd').onclick = function(){};\n");
  echo("          document.getElementById('paginacao_fwd').className = \"\";\n");
  echo("          document.getElementById('paginacao_last').onclick = function(){};\n");
  echo("          document.getElementById('paginacao_last').className = \"\";\n");
  echo("        }\n");
  echo("        document.getElementById('paginacao_fwd').innerHTML = \"&gt;\";\n");
  echo("        document.getElementById('paginacao_last').innerHTML = \"&gt;&gt;\";\n");
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
                     chamada da funÔøΩo
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
  echo("          nome_var='cod_colaborador[]';\n");
  echo("          nome_var_all='cod_colaborador_all';\n");
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
  echo("          nome_var_all='cod_colaborador_all';\n");
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
  echo("          nome_var_all='cod_colaborador_all';\n");
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
	Se houver, habilita o botÔøΩo para mostrar o(s) perfil(s) selecionados.
	Caso contrario desabilita o botÔøΩo.
    Entrada: nenhuma
    Saida:   nenhuma.
  */
  echo("      function Seek_Checked() \n");
  echo("      {\n");
  echo("        var elem=document.Perfil;\n");
  echo("        var check = new Array(5)\n");
  echo("        for (var j = 0;j<5;j++) check[j] = false;\n");
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
  echo("  		  if(elem.elements[i].checked == true && elem.elements[i].name == 'cod_colaborador[]' && check[2] == false){\n"); 
  echo("		  	document.getElementById('MostrarSelColaboradores').className = 'menuUp02';\n");
  echo("			document.getElementById('MostrarSelColaboradoresB').onclick = function(){setImprimir(0);OpenWindow();document.Perfil.submit();};\n");
  echo("			document.getElementById('ImprimirSelColaboradoresB').onclick = function(){setImprimir(1);OpenWindow();document.Perfil.submit();};\n");
  echo("			check[2] = true;}\n");
  echo("  		  if(elem.elements[i].checked == true && elem.elements[i].name == 'cod_visitante[]' && check[3] == false){\n");
  echo("		  	document.getElementById('MostrarSelVisitantes').className = 'menuUp02';\n");
  echo("			document.getElementById('MostrarSelVisitantesB').onclick = function(){setImprimir(0);OpenWindow();document.Perfil.submit();};\n");
  echo("			document.getElementById('ImprimirSelVisitantesB').onclick = function(){setImprimir(1);OpenWindow();document.Perfil.submit();};\n");
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
  echo("		if (check[2] == false && (li = document.getElementById('MostrarSelColaboradores'))){\n");
  echo("		  	li.className = 'menuUp';\n");
  echo("			document.getElementById('MostrarSelColaboradoresB').onclick = '';}\n");
  echo("		if (check[3] == false && (li = document.getElementById('MostrarSelVisitantes'))){\n");
  echo("		  	li.className = 'menuUp';\n");
  echo("			document.getElementById('MostrarSelVisitantesB').onclick = '';}\n");
  echo("		if (check[4] == false && (li = document.getElementById('MostrarSelCoordenadores'))){\n");
  echo("		  	li.className = 'menuUp';\n");
  echo("			document.getElementById('MostrarSelCoordenadoresB').onclick = '';}\n");
  echo("      }\n\n");

  /********************************************************************
   * Como no IE getElementsByName() n√£o funciona, usar a funcao abaixo. 
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

  $objAjax->printJavascript();

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 1 - Perfil */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)."</h4>\n");
  /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a href=\"#\" onclick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("            <a href=\"#\" onclick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("            <a href=\"#\" onclick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
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
    /* 2-Editar orienta√ß√£o para preenchimento do Perfil */
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

      // Se houve alteracao no Perfil
      if ((!empty($linha)) && ($linha['data'] > $ultimo_acesso)) {
        // Coloca em negrito
        $bopen_tag = "<b>";
        $bclose_tag = "</b>";
        // Coloca sublinhado
        $classe="class=\"novo\"";
      } else {
        // Senao, deixa sem negrito e sublinhado
        $classe="class=\"antigo\"";
      }
      echo("                    <tr id=\"tr_".($num_usuario+1)."\" class=\"altColor".($num_usuario%2)."\">\n");
      echo("                      <td>\n");
      echo("                        <input class=\"input\" type=\"checkbox\" name=\"cod_aluno[]\" value=\"".$dados['cod_usuario']."\" onclick=\"UnCheckHeader(1);CheckHeader(1,'cod_aluno[]');Seek_Checked();\" />\n");
      echo("                      </td>\n");
      echo("                      <td align=\"left\">\n");
      echo("                        ".$icone." <a ".$classe." href=\"#\" onclick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">".$dados['nome']."</a>\n");
      echo("                      </td>\n");

      if (!empty($linha)){
        echo("                      <td id=\"data_".$dados['cod_usuario']."\">".$bopen_tag.UnixTime2DataHora($linha['data']).$bclose_tag."\n");
      }
      else{
        /* 6-n√£o dispon√≠vel */
        echo("                      <td id=\"data_".$dados['cod_usuario']."\"> (".RetornaFraseDaLista($lista_frases,6).")</td>\n");
      }
      echo("                    </tr>\n");
      $num_usuario++;
    }
    
    echo("                  <tr id=\"trIndicePag\">\n");
    echo("                      <td colspan=\"2\" align=\"left\" style=\"border-right:none\">\n");
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

    echo("                    	<td align=\"right\">\n");
    echo("                    		<span id=\"paginacao_first\"></span> <span id=\"paginacao_back\"></span>\n");
    $controle=1;
    
    while($controle<=5){
      echo("                      	<span id=\"paginacao_".$controle."\"></span>\n");
      $controle++;
    }
    
    echo("                   		<span id=\"paginacao_fwd\"></span> <span id=\"paginacao_last\"></span>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
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
  ========================
  Tabela dos Colaboradores
  ========================
  */
  $lista_colaboradores = ListaUsuario($sock,"Z",$cod_curso);

  echo("                    <tr class=\"head01\">\n");
  echo("                      <td colspan=\"3\">\n");
  /* 124 - Colaboradores */
  echo("                        <div style=\"font-weight:bold;\" align=\"left\">".RetornaFraseDaLista($lista_frases,124)."</div>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  if (count($lista_colaboradores) > 0)
  {
    echo("                    <tr class=\"head\">\n");
    echo("                      <td width=\"2%\"><input class=\"input\" type=\"checkbox\" name=\"cod_colaborador_all\" value=\"1\" onclick=\"CheckAll(3);Seek_Checked();\" /></td>\n");
    /* 4-Nome */
    echo("                      <td align=\"left\" width=\"68%\">".RetornaFraseDaLista($lista_frases,4)."</td>\n");
    /* 5-Data */
    echo("                      <td width=\"30%\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
    echo("                    </tr>\n");

    $num_user_pag=0;
    $numPagina = 1;
    foreach ($lista_colaboradores as $i => $dados)
    {
      if ($dados['cod_usuario'] >= 0)
      {
        $tupla = RetornaPerfil($sock, $dados['cod_usuario']);

        $bopen_tag = "";
        $bclose_tag = "";
        
        // Se houve alteracao no Perfil
        if ((!empty($tupla)) && ($tupla['data'] > $ultimo_acesso))
        {
          // Coloca em negrito
          $bopen_tag = "<b>";
          $bclose_tag = "</b>";
          // Coloca sublinhado
          $classe="class=\"novo\"";
        } else {
          // Senao, deixa sem negrito e sublinhado
          $classe="class=\"antigo\"";
        }
        
        if($num_user_pag == $user_por_pag){
        $numPagina++;
        $num_user_pag = 0;
        }
        
        if($numPagina == $pagAtual) $style = "";
        else $style = "display:none";

        echo("                    <tr class=\"altColor".($num_usuario%2)."\">\n");
        echo("                      <td>\n");
        echo("                        <input class=\"input\" type=\"checkbox\" name=\"cod_colaborador[]\" value=\"".$dados['cod_usuario']."\" onclick=\"UnCheckHeader(3);CheckHeader(3,'cod_colaborador[]');Seek_Checked();\" />\n");
        echo("                      </td>\n");
        echo("                      <td align=\"left\">\n");
        echo("                        ".$icone." <a ".$classe." href=\"#\" onclick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">".$dados['nome']."</a>\n");
        echo("                      </td>\n");

        if (!empty($tupla))
        {
          echo("                      <td id=\"data_".$dados['cod_usuario']."\">".$bopen_tag.UnixTime2DataHora($tupla['data']).$bopen_tag."\n");
        }
        else
        {
          /* 6-n√£o dispon√≠vel */
          echo("                      <td id=\"data_".$dados['cod_usuario']."\">(".RetornaFraseDaLista($lista_frases, 6).")</td>\n");
        }
        echo("                    </tr>\n");
      }
      $num_user_pag++;
    }
    echo("                    <tr>\n");
    echo("                      <td colspan=\"3\">\n");
    echo("                        <ul >\n");
    echo("                          <li id=\"MostrarSelColaboradores\" class=\"menuUp\">\n");
    /* 8 - Mostrar Selecionados */
    echo("                            <span id=\"MostrarSelColaboradoresB\">".RetornaFraseDaLista($lista_frases,8)."</span>\n");
    /* 138 - Imprimir Selecionados */
    echo("                            <span id=\"ImprimirSelColaboradoresB\">".RetornaFraseDaLista($lista_frases,138)."</span>\n");
    echo("                          <br /><br />\n");
    echo("                          </li>\n");
    echo("                        </ul>\n");
    echo("                      </td>\n");
    echo("                    </tr>\n");
  }
  else{
    echo("<tr>\n");
    /* 133 - Nenhum
       135 - Colaborador
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

        if ((!empty($tupla)) && ($tupla['data'] > $ultimo_acesso))
        {
          // Coloca em negrito
          $bopen_tag = "<b>";
          $bclose_tag = "</b>";
          // Coloca sublinhado
          $classe="class=\"novo\"";
        } else {
          // Senao, deixa sem negrito e sublinhado
          $classe="class=\"antigo\"";
        }

        echo("                    <tr class=\"altColor".($num_usuario%2)."\">\n");
        echo("                      <td align=\"center\">\n");
        echo("                        <input class=\"input\" type=\"checkbox\" name=\"cod_visitante[]\" value=\"".$dados['cod_usuario']."\" onclick=\"UnCheckHeader(4);CheckHeader(4,'cod_visitante[]');Seek_Checked();\" />\n");
        echo("                      </td>\n");
        echo("                      <td align=\"left\">\n");
        echo("                        ".$icone." <a ".$classe."href=\"#\" onclick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">".$dados['nome']."</a>");
        echo("                      </td>\n");

        if (!empty($tupla))
        {
          echo("                      <td id=\"data_".$dados['cod_usuario']."\">".$bopen_tag.UnixTime2DataHora($tupla['data']).$bopen_tag."\n");
        }
        else
        {
          /* 6-n√£o dispon√≠vel */
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
    // o coordenador √© exibido como coordenador mesmo. Portanto descontamos o tamanho da lista de formadores de um (o coordenador)
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
      // se o coordenador quiser ser chamado de formador, n√£o devemos exibi-lo
      if (($dados['cod_usuario'] >= 0) && (($dados_coord['status'] == 'F') || ($dados['cod_usuario'] != $cod_coordenador)))
      {
        $tupla = RetornaPerfil($sock, $dados['cod_usuario']);

        $bopen_tag = "";
        $bclose_tag = "";

        if ((!empty($tupla)) && ($tupla['data'] > $ultimo_acesso))
        {
          // Coloca em negrito
          $bopen_tag = "<b>";
          $bclose_tag = "</b>";
          // Coloca sublinhado
          $classe="class=\"novo\"";
        } else {
          // Senao, deixa sem negrito e sublinhado
          $classe="class=\"antigo\"";
        }

        echo("                    <tr class=\"altColor".($num_usuario%2)."\">\n");
        echo("                      <td align=\"center\">\n");
        echo("                        <input class=\"input\" type=\"checkbox\" name=\"cod_formador[]\" value=\"".$dados['cod_usuario']."\" onclick=\"UnCheckHeader(2);CheckHeader(2,'cod_formador[]');Seek_Checked();\" />\n");
        echo("                      </td>\n");
        echo("                      <td align=\"left\">\n");
        echo("                        ".$icone." <a ".$classe." href=\"#\" onclick=\"return(OpenWindowLink(".$dados['cod_usuario']."));\" class=\"text\">".$dados['nome']."</a>\n");
        echo("                      </td>\n");

        if (!empty($tupla))
        {
          echo("                      <td id=\"data_".$dados['cod_usuario']."\">".$bopen_tag.UnixTime2DataHora($tupla['data']).$bopen_tag."\n");
        }
        else
        {
          /* 6-n√£o disponivel */
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

    if ((!empty($linha_perfil)) && ($linha_perfil['data'] > $ultimo_acesso))
    {
      // Coloca em negrito
      $bopen_tag = "<b>";
      $bclose_tag = "</b>";
      // Coloca sublinhado
      $classe="class=\"novo\"";
    } else {
      // Senao, deixa sem negrito e sublinhado
      $classe="class=\"antigo\"";
    }


    echo("                    <tr>\n");
    echo("                      <td align=\"center\">\n");
    echo("                        <input class=\"input\" onclick=\"Seek_Checked();\" type=\"checkbox\" name=\"cod_coordenador[]\" value=\"".$dados_coord['cod_usuario']."\" />\n");
    echo("                      </td>\n");
    echo("                      <td align=\"left\">\n");
    echo("                        ".$icone." <a ".$classe." href=\"#\" onclick=\"return(OpenWindowLink(".$dados_coord['cod_usuario']."));\" class=\"text\">".$dados_coord['nome']."</a>\n");
    echo("                      </td>\n");

    # $linha = RetornaPerfil($sock, $linha['cod_usuario']);
    if (!empty($linha_perfil))
    {
      echo("                      <td id=\"data_".$dados_coord['cod_usuario']."\">".$bopen_tag.UnixTime2DataHora($linha_perfil['data']).$bclose_tag."\n");
    }
    else
    {
      /* 6-n√£o dispon√≠vel */
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