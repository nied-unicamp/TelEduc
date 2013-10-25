<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/marcar_sessao.php

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
  ARQUIVO : cursos/aplic/batepapo/marcar_sessao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  // 87 - N�o � poss�vel marcar sess�es em datas/hor�rios j� passados. Verifique o campos de data e hora.
  //  - Erro.
  $feedbackObject->addAction("erro_sessao", 87, "Erro.");

  /*
  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,10); */

  $AcessoAvaliacao = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  $e_formador=EFormador($sock,$cod_curso,$cod_usuario);

  GeraJSVerificacaoData();

  /****************** Fun��es JavaScript **************** */
  echo("  <script type=\"text/javascript\" language=JavaScript src=../bibliotecas/dhtmllib.js></script>\n");

  echo("  <script type=\"text/javascript\" language=JavaScript>\n\n");

  echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("    var versao = (navigator.appVersion.substring(0,3));\n");
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

  /* Iniciliza os layers. */
  echo("    function Iniciar() \n");
  echo("    { \n");
              $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("      startList(); \n");
  echo("      lay_calendario = getLayer('layer_calendario');\n"); 
  echo("    }\n");

  // Esconde o layer especificado por cod_layer.
  echo("    function EscondeLayer(cod_layer)\n");
  echo("    {\n");
  echo("      hideLayer(cod_layer);\n");
  echo("    }\n\n");

  /* Esconde todos os layers. Se o usuario for o propriet�rio do di�rio   */
  /* visualizado ent�o esconde o layer para renomear o item.              */
  echo("    function EscondeLayers()\n");
  echo("    {\n");
 	echo("      hideLayer(lay_calendario);\n"); 
  echo("    }\n\n");

  /* Exibe o layer especificado por cod_layer.                            */
  echo("    function MostraLayer(cod_layer)\n");
  echo("    {\n");
  echo("      EscondeLayers();\n");
  echo("      moveLayerTo(cod_layer, Xpos, Ypos + AjustePosMenuIE());\n");
  echo("      showLayer(cod_layer);\n");
  echo("    }\n\n");

  echo("  function ChecaAssunto()\n");
  echo("  {\n");
  echo("    if (document.formul.assunto.value!='')\n");
  echo("      return true;\n");
  echo("    else\n");
  echo("    {\n");
  /* 54 - O assunto da sess�o deve estar preenchido! */
  echo("      alert('".RetornaFraseDaLista($lista_frases,54)."');\n");
  echo("      return false;\n");
  echo("    }\n");
  echo("    return true;\n");
  echo("  }\n");

  echo("  function padroniza_hora(hora)\n");
  echo("  {\n");
  echo("    var tmphora=hora.value;\n");
  echo("    var tmphora2='';\n");
  echo("    i=tmphora.indexOf(':');\n");
  echo("    if (i==-1)\n");
  echo("    {\n");
  echo("      tmphora2=tmphora;\n");
  echo("      tmphora=tmphora2.substring(0,2)+':'+tmphora2.substring(2,4);\n");
  echo("      i=tmphora.indexOf(':');\n");
  echo("    }\n");
  echo("    if (i==1)\n");
  echo("      tmphora='0'+tmphora;\n");
  echo("    while (tmphora.length<5)\n");
  echo("      tmphora=tmphora+'0';\n");
  echo("    hora.value=tmphora;\n");
  echo("  }\n");

  echo("  function hora_valida(hora)\n");
  echo("  {\n");
  echo("    var tmphora=hora.value;\n");
  echo("    var tmphora0=tmphora.substring(0,1);\n");
  echo("    var tmphora1=tmphora.substring(1,2);\n");
  echo("    var tmphora2=tmphora.substring(2,3);\n");
  echo("    var tmphora3=tmphora.substring(3,4);\n");
  echo("    var tmphora4=tmphora.substring(4,5);\n");
  echo("    if (tmphora0<'0' || tmphora0>'2' || tmphora1<'0' || tmphora1>'9' || tmphora2!=':' || tmphora3<'0' || tmphora3>'5' || tmphora4<'0' || tmphora4>'9')\n");
  echo("    {\n");
   /* 102 - Hora inv�lida*/ /* 103 - Utilize o formato hh:mm.*/
  echo("      alert('".RetornaFraseDaLista($lista_frases,102).": '+tmphora+'! ".RetornaFraseDaLista($lista_frases,103)."');\n");
  echo("      return  false;\n");
  echo("    }\n");
  echo("    if (tmphora0=='2' && tmphora1>'3')\n");
  echo("    {\n");
   /* 102 - Hora inv�lida*/ /* 103 - Utilize o formato hh:mm.*/
  echo("      alert('".RetornaFraseDaLista($lista_frases,102).": '+tmphora+'! ".RetornaFraseDaLista($lista_frases,103)."');\n");
  echo("      return  false;\n");
  echo("    }\n");
  echo("    return true;\n");
  echo("  }\n");

  echo("  function ChecaCampos()\n");
  echo("  {\n");
  echo("    if (ChecaAssunto())\n");
  echo("      if (data_valida(document.formul.data))\n");
  echo("        if (hora_valida(document.formul.hora_inicio))\n");
  echo("          if (hora_valida(document.formul.hora_fim))\n");
  echo("            return true;\n");
  echo("    return false;\n");
  echo("  }\n");

  echo("  function OpenWindow() \n");
  echo("  {\n");
  echo("    window.open(\"entrar_sala.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\",\"Batepapo\",\"width=1000,height=700,top=50,left=50,scrollbars=no,status=yes,toolbar=no,menubar=no,resizable=no\");\n");
  echo("    return(false);\n");
  echo("  }\n");
  
  echo("        function VerificaDigitosValor(valor) {\n");
  echo("          re_com_virgula = /^[0-9]+(\.|,)?[0-9]+\$/; \n"); // nota com decimal
  echo("          re_somente_numeros = /^[0-9]+\$/; \n"); // somente numeros
  echo("          if (valor == '' || re_com_virgula.test(valor) || re_somente_numeros.test(valor) ) { \n");
  echo("            return false;\n");
  echo("          } else {\n");
  echo("            return true;\n");
  echo("          }\n");
  echo("        }\n");

  echo("        function VerificaValor(valor) \n");
  echo("        {\n");
  echo("          if (valor==''){\n");
  // 111 - O campo Valor n&atilde;o pode ser vazio.
  echo("            alert('".RetornaFraseDaLista($lista_frases,111)."');\n");
  echo("            return false; \n");
  echo("          } \n");
  echo("          if (VerificaDigitosValor(valor)){\n");
  // 112 - Voc&ecirc; digitou caracteres estranhos no campo Valor.
  // 113 - Use apenas d&iacute;gitos de 0 a 9 e o ponto ( . ) ou a v&iacute;rgula ( , ) para este campo (exemplo: 7.5).
  echo("            alert('".RetornaFraseDaLista($lista_frases,112)."\\n".RetornaFraseDaLista($lista_frases,113)."');\n");
  echo("            return false; \n");
  echo("          } \n");
  // verificamos se o Valor tem virgula, se tiver, convertemos para ponto
  echo("          valor = valor.replace(/\,/, '.'); \n");
  echo("          if (valor < 0) { \n");
  // 114 - A avaliação não pode ter valor negativo.
  echo("            alert('".RetornaFraseDaLista($lista_frases,114)."');\n");
  echo("            return false; \n");
  echo("          }  \n");
  echo("          return true;\n");
  echo("        }  \n");

  
  echo("      function AdicionaInputAvaliacao(div_hidden){\n");
  echo("          document.getElementById('ValorAval').style.visibility='visible';\n");
  echo("          document.getElementById('ObjetivosAval').style.visibility='visible';\n");
  echo("          document.getElementById('CriteriosAval').style.visibility='visible';\n");
  echo("          if(div_hidden=='divAvaliacao')\n");
  echo("            document.getElementById('dadosAvaliacao').className='divHidden';\n");
  echo("          document.getElementById(div_hidden).className='divHidden';\n");
  echo("          document.getElementById('divAvaliacaoEdit').className='';\n");
  echo("          cancelarElemento=document.getElementById('cancelaAval');\n");
  echo("      }\n\n");
  
  echo("      function EditaAvaliacao(opt){\n");
  echo("          if (opt==1){\n");
  echo("            if (!VerificaValor(document.getElementById('ValorAval').value)){\n");
  echo("              return false;\n");
  echo("            }\n");
  echo("          }\n");
  echo("          document.getElementById('ValorAval').style.visibility='hidden';\n");
  echo("          document.getElementById('ObjetivosAval').style.visibility='hidden';\n");
  echo("          document.getElementById('CriteriosAval').style.visibility='hidden';\n");
  echo("          document.getElementById('divAvaliacaoEdit').className='divHidden';\n");
	
  /* Cancelamento de inclusão de avaliação */
  echo("          if (opt==0){\n");
  echo("            document.getElementById('ValorAval').value='';\n");
  echo("            document.getElementById('ObjetivosAval').value='';\n");
  echo("            document.getElementById('CriteriosAval').value='';\n");
  echo("            document.getElementById('divAvaliacaoAdd').className='';\n\n");
  echo("          }\n");
  echo("          else\n");
  echo("          {\n");
  /* Inclusão de avaliação */
  echo("            if(opt==1)\n");
  echo("            {\n");
  echo("              document.getElementById('span_ValorAval').innerHTML=document.getElementById('ValorAval').value;\n");
  echo("              if(document.getElementById('ObjetivosAval').value == '')\n");
  /* 110 - Nao definidos*/
  echo("                document.getElementById('span_ObjetivosAval').innerHTML='".RetornaFraseDaLista($lista_frases,110)."';\n");
  echo("              else\n");
  echo("                document.getElementById('span_ObjetivosAval').innerHTML=document.getElementById('ObjetivosAval').value;\n");

  echo("              if(document.getElementById('CriteriosAval').value == '')\n");
 /* 110 - Nao definidos*/
  echo("                document.getElementById('span_CriteriosAval').innerHTML='".RetornaFraseDaLista($lista_frases,110)."';\n");
  echo("              else\n");
  echo("                document.getElementById('span_CriteriosAval').innerHTML=document.getElementById('CriteriosAval').value;\n");
  echo("            }\n");
  echo("            document.getElementById('divAvaliacao').className='';\n\n");
  echo("            document.getElementById('dadosAvaliacao').className='';\n\n");
  echo("          }\n");
  echo("          cancelarElemento=null;\n");
  echo("        }\n\n");
    
  echo("      function ApagaAvaliacao(){\n");
  echo("          document.getElementById('ValorAval').style.visibility='hidden';\n");
  echo("          document.getElementById('ObjetivosAval').style.visibility='hidden';\n");
  echo("          document.getElementById('CriteriosAval').style.visibility='hidden';\n");
  echo("          document.getElementById('ValorAval').value='';\n");
  echo("          document.getElementById('ObjetivosAval').value='';\n");
  echo("          document.getElementById('CriteriosAval').value='';\n");
  echo("          document.getElementById('divAvaliacao').className='divHidden';\n");
  echo("          document.getElementById('dadosAvaliacao').className='divHidden';\n");
  echo("          document.getElementById('divAvaliacaoEdit').className='divHidden';\n");
  echo("          document.getElementById('divAvaliacaoAdd').className='';\n\n");
  echo("      }\n");
    
  echo("  </script>\n");

  /****************** Corpo HTML ****************/

  include("../menu_principal.php");

  echo("<td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* 1 - Bate-Papo */
  echo("<h4>".RetornaFraseDaLista($lista_frases, 1));
  /* 47 - Marcar sess�o */
  echo(" - ".RetornaFraseDaLista($lista_frases, 47)."</h4>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("<div id=\"mudarFonte\">\n");
  echo("	<a href=\"#\" onClick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	<a href=\"#\" onClick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	<a href=\"#\" onClick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("</div>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");

  echo("      <ul class=\"btAuxTabs\">\n");
  /* 27 - Ver sess�es realizadas */
  echo("        <li><span title=\"Ver sess�es realizadas\" onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 27)."</span></li>\n");
  if ($e_formador)
  {
    /* 47 - Marcar sess�o */
    echo("        <li><span title=\"Marcar sess�o\" onClick=\"document.location='marcar_sessao.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 47)."</span></li>\n");
    /* 63 - Desmarcar sess�es */
    echo("        <li><span title=\"Desmarcar sess�es\" onClick=\"document.location='desmarcar_sessoes.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 63)."</span></li>\n");

    /* 78 - Lixeira */
    echo("        <li><span title=\"Lixeira\" onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;lixeira=sim';\">".RetornaFraseDaLista($lista_frases, 78)."</span></li>\n");
  }
  /* 55 - Pr�xima sess�o marcada */
  echo("        <li><span title=\"Pr�xima sess�o marcada\" onClick=\"document.location='ver_sessoes_marcadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 55)."</span></li>\n");
  echo("      </ul>\n");

  echo("    </td>\n");
  echo("  </tr>\n");

  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");

  echo("      <form name=formul action=\"marcar_sessao2.php?cod_curso=".$cod_curso."\" method=post onSubmit=return(ChecaCampos());>\n");
  //echo(RetornaSessionIDInput());
  echo("        <input type=hidden name=cod_curso value=".$cod_curso." />\n");

  /* <!----------------- Tabela Interna -----------------> */
  echo("      <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  /*40 -  Assunto da Sess�o */
  echo("          <td width=30%>".RetornaFraseDaLista($lista_frases,40)."</td>\n");
  /* 41 - Data */
  echo("          <td width=20%>".RetornaFraseDaLista($lista_frases,41)."</td>\n");
  /* 49 - Hor�rio de In�cio */
  echo("          <td width=10%>".RetornaFraseDaLista($lista_frases,48)."</td>\n");
  /* 49 - Hor�rio de T�rmino */
  echo("          <td width=10%>".RetornaFraseDaLista($lista_frases,49)."</td>\n");
//  if ($e_formador)
//  {
//    /* 89 - Criar Avalia��o para esta sess�o de Bate-Papo? */
//    echo("          <td width=20%>".RetornaFraseDaLista($lista_frases,89)."</td>\n");
//  }
  echo("        </tr>\n");

  echo("        <tr>\n");
  echo("          <td>\n");
  echo("            <input class=input type=text maxlength=255 size=40 name=assunto />\n");
  echo("          </td>\n");
  echo("          <td>\n");
  echo("            <input class=\"input\" size=\"10\" maxlength=\"10\" id=\"data_ini\" name=\"data\" value=\"".UnixTime2Data(time())."\" type=\"text\" />\n");
  echo("            <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('data_ini'),'dd/mm/yyyy',this);\" />\n");
  /* 50 - dd/mm/aaaa */
  echo("            <br/><div class=textsmall>(".RetornaFraseDaLista($lista_frases,50).")</div>\n");
  echo("          </td>\n");
  /*90 - 12:00*/
  echo("          <td>\n");
  echo("            <input class=input type=text maxlength=5 size=5 name=hora_inicio value='".RetornaFraseDaLista($lista_frases,90)."' onBlur='padroniza_hora(document.formul.hora_inicio);' />\n");
  /* 51 - hh:mm */
  echo("            <br/><div class=textsmall>(".RetornaFraseDaLista($lista_frases,51).")</div>\n");
  echo("          </td>\n");
  /*91 - 14:00*/
  echo("          <td>\n");
  echo("            <input class=input type=text maxlength=5 size=5 name=hora_fim value='".RetornaFraseDaLista($lista_frases,91)."' onBlur='padroniza_hora(document.formul.hora_fim);' />\n");
  /* 51 - hh:mm */
  echo("            <br/><div class=textsmall>(".RetornaFraseDaLista($lista_frases,51).")</div>\n");
  echo("          </td>\n");
  if ($e_formador)
  {
  	echo("        </tr>\n");
  	echo("		  <tr class=\"head\">\n");
  	/* 88 - Avaliação */
    echo("        	<td colspan=\"4\">".RetornaFraseDaLista($lista_frases,88)."</td>\n");
    echo("        </tr>\n");
    
    
    echo("                  <tr>\n");
    echo("                    <td align=\"left\" colspan=\"4\" id=\"dadosAvaliacao\" class=\"divHidden\">\n");
    echo("                      <ul class=\"btAuxTabs\">\n");
    /* 108 - Alterar Avaliação */
    echo("                        <li><span onclick=\"AdicionaInputAvaliacao('divAvaliacao');\">".RetornaFraseDaLista($lista_frases,108)."</span></li>\n");
    /* 109 - Apagar Avaliação */
    echo("                        <li><span onclick=\"ApagaAvaliacao();\">".RetornaFraseDaLista($lista_frases,109)."</span></li>\n");
    echo("                      </ul>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    
    
    echo("                  <tr>\n");
    echo("                    <td colspan=\"4\" align=\"left\" >\n");

    echo("                      <div id=\"divAvaliacao\" class=\"divHidden\">\n");
    echo("                        <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n");
    echo("                          <tr>\n");
    /* 104 - Valor */
    echo("                            <td width=\"20%\" align=\"right\"><b>".RetornaFraseDaLista($lista_frases,104)."</b></td>\n");
    echo("                            <td align=\"left\"><span id=\"span_ValorAval\">".$lista['valor']."</span></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 105 - Objetivos */
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,105)."</b></td>\n");
    echo("                            <td align=\"left\"><span id=\"span_ObjetivosAval\">".$span_objetivos."</span></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 106 - Critérios*/
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,106)."</b></td>\n");
    echo("                            <td align=\"left\"><span id=\"span_CriteriosAval\">".$span_criterios."</span></td>\n");
    echo("                          </tr>\n");
    echo("                        </table>\n");
    echo("                      </div>\n");

    echo("                      <div id=\"divAvaliacaoAdd\" ".$class_avaliacao_add.">\n");
    /* 107 - Incluir Avaliação */
    echo("                        <img alt=\"\" src=\"../imgs/portfolio/lapis.gif\" border=0 /> <span id=\"incluiAval\" class=\"link\" onclick=\"AdicionaInputAvaliacao('divAvaliacaoAdd');\">".RetornaFraseDaLista($lista_frases,107)."</span>\n");
    echo("                      </div>\n");
    echo("                      <div id=\"divAvaliacaoEdit\" class=\"divHidden\">\n");
    echo("                        <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n");
    echo("                          <tr>\n");
    /* 97 - Valor */
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,104)."</b></td>\n");
    echo("                            <td align=\"left\"><input class=\"input\" type=\"text\" name=\"ValorAval\" id=\"ValorAval\"  size=3 value=\"".$lista['valor']."\"/></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 99 - Objetivos*/
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,105)."</b></td>\n");
    echo("                            <td align=\"left\"><textarea class=\"input\" name=\"ObjetivosAval\" id=\"ObjetivosAval\" cols=36 rows=5 />".$lista['objetivos']."</textarea></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 100 - Critérios*/
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,106)."</b></td>\n");
    echo("                            <td align=\"left\"><textarea class=\"input\" name=\"CriteriosAval\" id=\"CriteriosAval\" cols=36 rows=5 />".$lista['criterios']."</textarea></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 100 - Critérios*/
    echo("                            <td></td>\n");
    echo("                            <td align=\"left\">\n");
    /* 18 (gn) - Ok */
    echo("                              <span class=\"link\" onclick=\"EditaAvaliacao(1);\">".RetornaFraseDaLista($lista_frases_geral,18)."</span>\n");
    echo("                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
    /* 2 (gn) - Cancelar */
    echo("                              <span class=\"link\" id=\"cancelaAval\" onclick=\"EditaAvaliacao(0);\">".RetornaFraseDaLista($lista_frases_geral,2)."</span><br >\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
    echo("                        </table>\n");
    echo("                      </div>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }


  echo("        <tr>\n");
  /* 52 - Obs.: Se o hor�rio de t�rmino for menor que o hor�rio de in�cio, ser� considerado hor�rio do dia seguinte. */
  echo("          <td colspan=\"5\">".RetornaFraseDaLista($lista_frases,52)."</td>\n");
  echo("        </tr>\n");

  // Fim Tabela Interna
  echo("      </table>\n");

  echo("      <ul class=\"btAuxTabs03\">\n");
  /* 2 - Entrar na sala de bate-papo */
  echo("        <li><span title=\"Entrar na sala de bate-papo\" onClick=\"return(OpenWindow());\">".RetornaFraseDaLista($lista_frases, 2)."</span></li>\n");
  echo("      </ul>\n");

  echo("      <div align=\"right\">\n");
  /* 11 - Enviar (ger) */
  echo("        <input class=input type=submit value='".RetornaFraseDaLista($lista_frases_geral,11)."' />\n");
  echo("      </div>\n");

  echo("      </form>\n");

  echo("    </td>\n");
  echo("  </tr>\n");
  //Fim Tabel�o
  echo("</table>\n");

  include("../tela2.php");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
