<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/enquete/nova_enquete.php

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
  ARQUIVO : cursos/aplic/enquete/nova_enquete.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("enquete.inc");

  $cod_ferramenta=24;
  $cod_ferramenta_ajuda = 24;
  $cod_pagina_ajuda = 5;
  
  $categ = "N";

  include("../topo_tela.php");

  GeraJSVerificacaoData();
  GeraJSComparacaoDatas();

  /*********************************************************/
  /* in�io - JavaScript */
  echo("  <script  type=\"text/javascript\" language=\"javascript\" src='../bibliotecas/dhtmllib.js'></script>\n");
  
  /* AJAX para o calend�io */
  //echo("<script src='../bibliotecas/ajax.js'></script>\n");
  
  echo("  <script  type=\"text/javascript\" language=\"javascript\">\n\n");

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
  echo("    function Iniciar()\n");
  echo("    {\n");
  echo("      startList();\n");
  echo("      lay_calendario = getLayer('layer_calendario');\n");
  echo("    }\n\n");

  // Esconde o layer especificado por cod_layer.
  echo("    function EscondeLayer(cod_layer)\n");
  echo("    {\n");
  echo("      hideLayer(cod_layer);\n");
  echo("    }\n\n");

  /* Esconde todos os layers. Se o usuario for o propriet�io do di�io   */
  /* visualizado ent� esconde o layer para renomear o item.              */
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
  
  
  echo("  function CancelaEnquete()\n");
  echo("  {\n");
  echo("    document.location.href=\"enquete.php?&cod_curso=".$cod_curso."\";\n");
  echo("    return(true);\n");
  echo("  }\n\n");
  
  echo("  function testa_titulo()\n");
  echo("  {\n");
  /* Elimina os espa�s para verificar se o titulo nao eh formado por apenas espa�s */
  echo("    tit = document.enquete.titulo.value;\n");
  echo("    while (tit.search(\" \") != -1)\n");
  echo("    {\n");
  echo("      tit = tit.replace(/ /, \"\");\n");
  echo("    }\n");
  echo("    if (tit=='')\n");
  echo("    {\n");
  /* 12 - A enquete deve ter um t�ulo */
  echo("      alert('".RetornaFraseDaLista($lista_frases, 12).".');\n");
  echo("      return(false);\n");
  echo("    } else {\n");
  echo("      return(true);\n");
  echo("    }\n");
  echo("  }\n\n");

  echo("  function testa_pergunta()\n");
  echo("  {\n");
  /* Elimina os espa�s para verificar se o conteudo nao eh formado por apenas espa�s */
  echo("    cont = document.enquete.pergunta.value;\n");
  echo("    while (cont.search(\" \") != -1)\n");
  echo("    {\n");
  echo("      cont = cont.replace(/ /, \"\");\n");
  echo("    }\n");
  echo("    if (cont=='')\n");
  echo("    {\n");
  /* 13 - A enquete deve ter uma pergunta */
  echo("      alert('".RetornaFraseDaLista($lista_frases, 13)."');\n");
  echo("      return(false);\n");
  echo("    } else {\n");
  echo("      return(true);\n");
  echo("    }\n");
  echo("  }\n\n");
  
  echo("  function testa_alternativas()\n");
  echo("  {\n");
  echo("      if ((document.getElementById('alternativa0').value == '') || (document.getElementById('alternativa1').value == ''))\n");
  echo("      {\n");
  // 22 - Voc�deve preencher no minimo a primeira e segunda alternativas */
  echo("        alert ('".RetornaFraseDaLista($lista_frases, 22)."');\n");
  echo("        return false;\n");
  echo("      }\n");
  echo("      if (document.getElementById('alternativa0').value == document.getElementById('alternativa1').value)\n");
  echo("      {\n");
  // 112 - N� podem haver alternativas repetidas. */
  echo("        alert ('".RetornaFraseDaLista($lista_frases, 112)."');\n");
  echo("        return false;\n");
  echo("      }\n");
  echo("      return(true);\n");
  echo("  }\n\n");
  
  echo("  function testa_datas()\n");
  echo("  {\n");
  echo("    var d_hoje=document.enquete.data_hoje;\n");
  echo("    var d_inicio=document.enquete.data_inicio;\n");
  echo("    var d_fim=document.enquete.data_fim;\n");
  echo("    var h_inicio=document.enquete.hora_inicio;\n");
  echo("    var h_hoje=document.enquete.hora_hoje;\n");
  echo("    var h_fim=document.enquete.hora_fim;\n");
  echo("    if (!DataValidaAux(d_inicio))\n");
  echo("      return false;\n");
  echo("    if (!hora_valida(h_inicio)){\n");
  /* 109- Horario de inicio invalido. */
  echo("      alert('".RetornaFraseDaLista($lista_frases,109)."');\n");
  echo("      return false;\n");
  echo("    }\n");
  echo("    if (!DataValidaAux(d_fim))\n");
  echo("      return false;\n");
  echo("    if (! hora_valida(h_fim)){\n");
  /* 110- Horario de termino invalido. */
  echo("      alert('".RetornaFraseDaLista($lista_frases,110)."');\n");
  echo("      return false;\n");
  echo("    }\n");
  echo("    if (ComparaDataHora(d_hoje,h_hoje,d_inicio,h_inicio) > 0) // (hoje>inicio) \n");
  echo("    {\n");
  /* 111 - A data de inicio deve ser maior do que a data de hoje. */
  echo("     alert('".RetornaFraseDaLista($lista_frases,111)."');\n");
  echo("     return(false);\n");
  echo("    }\n");
  echo("    if (ComparaDataHora(d_inicio,h_inicio,d_fim,h_fim) > 0) // (inicio>fim) \n");
  echo("    {\n");
  /* 108- A data de inicio e posterior a data de termino. */
  echo("     alert('".RetornaFraseDaLista($lista_frases,108)."');\n");
  echo("     return(false);\n");
  echo("    }\n");
  echo("    return true");
  echo("  }\n\n");
  
  echo("    function testa_configuracoes()\n");
  echo("    {\n");
  echo("      if ( (!document.getElementById('aplic0').checked) && (!document.getElementById('aplic1').checked) && (!document.getElementById('aplic2').checked) && (!document.getElementById('aplic3').checked))\n");
  echo("      {\n");
  // 23 - Selecione a quem a enquete ser�aplicada. Voc�deve selecionar no m�imo uma op�o
  echo("        alert ('".RetornaFraseDaLista($lista_frases, 23)."');\n");
  echo("        return false;\n");
  echo("      }\n");
  echo("      if ( (!document.getElementById('result0').checked) && (!document.getElementById('result1').checked) && (!document.getElementById('result2').checked) && (!document.getElementById('result3').checked) && (!document.getElementById('result4').checked))\n");
  echo("      {\n");
  // 24 - Selecione o compartilhamento do resultado. Voc�deve selecionar no m�imo uma op�o
  echo("        alert ('".RetornaFraseDaLista($lista_frases, 24)."');\n");
  echo("        return false;\n");
  echo("      }\n");
  echo("      if ( !  (document.enquete.resultado_parcial.value != '') )\n");
  echo("      {\n");
  // 25 - A configura�o de exibi�o de resultado parcial n� foi informada 
  echo("        alert ('".RetornaFraseDaLista($lista_frases, 25)."');\n");
  echo("        return false;\n");
  echo("      }\n");
  echo("      if ( ! (document.enquete.identidade_votos.value != '') )\n");
  echo("      {\n");
  // 26 - A configura�o de exibi�o da identidade dos votos n� foi informada
  echo("        alert ('".RetornaFraseDaLista($lista_frases, 26)."');\n");
  echo("        return false;\n");
  echo("      }\n");
  echo("      if ( ! (ok = (document.enquete.num_escolhas.value != '') ))\n");
  echo("      {\n");
  // 27 - Selecione o nmero de escolhas da enquete
  echo("        alert ('".RetornaFraseDaLista($lista_frases, 27)."');\n");
  echo("        return false;\n");
  echo("      }\n");
  echo("      return true;\n");
  echo("    }\n");


  echo("  function testa_campos()\n");
  echo("  {\n");
  echo("    if ((testa_titulo()) && (testa_pergunta()) && (testa_alternativas()) && (testa_datas()) && (testa_configuracoes()))\n");
  echo("    {\n");
  echo("      return(true);\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
  echo("      return(false);\n");
  echo("    }\n");
  echo("  }\n");

  echo("\n");

  echo("  var alternativa_number; //variavel global que guarda o nmero de alternativas\n");
  echo("  alternativa_number = 5;\n");

  echo("  function addAlternativa()\n");
  echo("  {\n");
  echo("	alternativa_number++;\n");

  echo("	var span = document.createElement(\"span\");\n");
  echo("	span.setAttribute(\"id\", \"choice\" + alternativa_number);\n");
	
  echo("	var inputAlternativa = document.createElement(\"input\");\n");
  echo("	inputAlternativa.setAttribute(\"type\", \"text\");\n");
  echo("	inputAlternativa.setAttribute(\"name\",\"alternativa[]\");\n");
  echo("	inputAlternativa.setAttribute(\"class\",\"input\");\n");
  echo("        inputAlternativa.setAttribute(\"id\",\"alternativa\");\n");
  echo("	inputAlternativa.setAttribute(\"size\", \"46\");\n");
  echo("	inputAlternativa.setAttribute(\"maxlength\", \"255\");\n");
  echo("	inputAlternativa.setAttribute(\"style\", \"margin-bottom:5px;\");\n");

  echo("	document.getElementById('choices').appendChild(document.createElement(\"br\"));\n");
  echo("	document.getElementById('choices').appendChild(document.createTextNode(alternativa_number+\" \"));\n");
  echo("	document.getElementById('choices').appendChild(inputAlternativa);\n");
  echo("  }\n");

  echo("  function Voltar()\n");
  echo("  {\n");
  echo("    window.location='enquete.php?cod_curso=".$cod_curso."';\n");
  echo("  }\n");

  echo("\n</script>\n\n");
  /* fim - JavaScript */
  /*********************************************************/

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if ($tela_formador || $tela_colaborador)
  {
    /* 1 - Enquete */
    /* 8 - Nova Mensagem */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,8)."</h4>\n");

    /*Voltar*/
    /* 509 - Voltar */
    echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("            <tr>\n");
    echo("              <td valign=\"top\">\n");

    echo("                <ul class=\"btAuxTabs\">\n");

    /* 23 - Voltar  (gen) */
    echo("                  <li><span onclick='Voltar();'>".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");

    /* 39 - Enquetes nao aplicadas */
    echo("                  <li><a href='enquete.php?cod_curso=".$cod_curso."&amp;categ=0'>".RetornaFraseDaLista($lista_frases, 39)."</a></li>\n");

    /* 39 - Enquetes em andamento */
    echo("                  <li><a href='enquete.php?cod_curso=".$cod_curso."&amp;categ=1'>".RetornaFraseDaLista($lista_frases,40)."</a></li>\n");
    /* 41 - Enquetes encerradas  */
    echo("                  <li><a href='enquete.php?cod_curso=".$cod_curso."&amp;categ=2'>".RetornaFraseDaLista($lista_frases,41)."</a></li>\n");
    /* 97 - Lixeira */
    echo("                  <li><a href='enquete.php?cod_curso=".$cod_curso."&amp;categ=3'>".RetornaFraseDaLista($lista_frases,97)."</a></li>\n");

    echo("                </ul>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");

    echo("              <td valign=\"top\">\n");
    echo("                <form name=\"enquete\" method=\"post\" action=\"nova_enquete2.php?cod_curso=".$cod_curso."\" onsubmit='return(testa_campos());'>\n");

    /* Passa o cod_curso */
    echo("                  <input type=\"hidden\" name=\"data_hoje\" value=".UnixTime2Data(time())." />\n");
    echo("                  <input type=\"hidden\" name=\"hora_hoje\" value=".UnixTime2Hora(time())." />\n");

    echo("                  <table border=0 width=\"100%\" cellspacing=0 id=\"tabelaInterna\" class=\"tabInterna\">\n");
    echo("                    <tr class=\"head\">\n");
    /* 9 - T�ulo */
    echo("                      <td class=\"itens\" colspan=\"2\">".RetornaFraseDaLista($lista_frases,9)."</td>\n");
    echo("                    </tr>\n");
    echo("                    <tr>\n");
    echo("                      <td class=\"itens\" colspan=\"2\">\n");
    echo("                        <input type=\"text\" name=\"titulo\" class=\"input\" size=\"40\" maxlength=\"80\">\n");
    echo("                      </td>\n");
    echo("                    </tr>\n");

    echo("                    <tr class=\"head\">\n");
    /* 10 - Pergunta */
    echo("                      <td class=\"center\" width=\"50%\">".RetornaFraseDaLista($lista_frases,10)."</td>\n");
    /* 11 - Alternativas */
    echo("                      <td class=\"center\">".RetornaFraseDaLista($lista_frases,11)."</td>\n");
    echo("                    </tr>\n");
    echo("                    <tr>\n");
    echo("                      <td width=\"50%\" style='vertical-align:top; text-align:left;'>\n");
    echo("                        <textarea name=\"pergunta\" class=\"input\" rows=\"7\" style='width:95%' cols='100'></textarea>\n");
    echo("                      </td>\n");
    echo("                      <td width=\"50%\" class='itens'>\n");
    echo("                        <div id='choices'>\n");
    echo("                          1 <input type=\"text\" id='alternativa0' name=\"alternativa[0]\" class=\"input\" size=\"46\" maxlength=\"255\" style=\"margin-bottom:5px;\"> *<br>\n");
    echo("                          2 <input type=\"text\" id='alternativa1' name=\"alternativa[1]\" class=\"input\" size=\"46\" maxlength=\"255\" style=\"margin-bottom:5px;\"> *<br>\n");
    echo("                          3 <input type=\"text\" name=\"alternativa[2]\" class=\"input\" size=\"46\" maxlength=\"255\" style=\"margin-bottom:5px;\"><br>\n");
    echo("                          4 <input type=\"text\" name=\"alternativa[3]\" class=\"input\" size=\"46\" maxlength=\"255\" style=\"margin-bottom:5px;\"><br>\n");
    echo("                          5 <input type=\"text\" name=\"alternativa[4]\" class=\"input\" size=\"46\" maxlength=\"255\" style=\"margin-bottom:5px;\">\n");
    echo("                        </div>\n");
    /* 38 - Adicionar nova alternativa */
    echo("                        <a href='javascript:void(0);' onClick='addAlternativa()'>".RetornaFraseDaLista($lista_frases,38)."</a>\n");
    echo("                      </td>\n");
    echo("                    </tr>\n");

    echo("                    <tr class=\"head\">\n");
    /* 28 - Periodo de consulta */
    echo("                      <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,28)."</td>\n");
    echo("                    </tr>\n");
    echo("                    <tr>\n");
    echo("                      <td colspan=\"2\">\n");
    echo("                        <table border=0 width=\"100%\" cellspacing=0>\n");
    echo("                          <tr>\n");
    echo("                            <td width=\"50%\">\n");
    /* 48 - Data de In�io*/
    echo("                              ".RetornaFraseDaLista($lista_frases,48)."\n");
    echo("                              <input class=\"input\" type=\"text\" size=\"10\" maxlength=\"10\" id=\"data_inicio\" name=\"data_inicio\" value=\"".UnixTime2Data(time())."\" />\n");
    echo("                              <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('data_inicio'),'dd/mm/yyyy',this);\"/>\n");
    echo("                              <input class=\"input\" type=\"text\" maxlength=\"5\" size=\"5\" name=\"hora_inicio\" value='".UnixTime2Hora(time())."' onBlur='padroniza_hora(document.enquete.hora_inicio);'>\n");
    echo("                            </td>\n");
    echo("                            <td width=\"50%\">\n");
    /* 49 - Data de T�mino */
    echo("                               ".RetornaFraseDaLista($lista_frases,49)."\n");
    echo("                               <input class=\"input\" type=\"text\" size=\"10\" maxlength=\"10\" id=\"data_fim\" name=\"data_fim\" value=\"".UnixTime2Data(time())."\" />\n");
    echo("                               <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('data_fim'),'dd/mm/yyyy',this);\"/>\n");
    /* 30 - 23:59*/
    echo("                              <input class=\"input\" type=\"text\" maxlength=\"5\" size=\"5\" name=\"hora_fim\" value='".RetornaFraseDaLista($lista_frases,30)."' onBlur='padroniza_hora(document.enquete.hora_fim);'>\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
    echo("                        </table>\n");
    echo("                      </td>\n");
    echo("                    </tr>\n");
    echo("                    <tr class=\"head\">\n");
    /* 31 - Configura�es */
    echo("                      <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,31)."</td>\n");
    echo("                    </tr>\n");

    echo("                    <tr>\n");
    echo("                      <td colspan=\"2\">\n");
    echo("                        <table border=0 width=\"100%\" cellspacing=0>\n");
    echo("                          <tr>\n");
    /* 32 - Aplicar Enquete a  */
    echo("                            <td class='itens' width='280px'>".RetornaFraseDaLista($lista_frases,32)."</td>\n");
    echo("                            <td class='itens' >\n");
    /* 21 - Formadores  */
    echo("                              <input id='aplic0' type=\"checkbox\" name=\"aplic[0]\" value='F'><label for=\"aplic0\">".RetornaFraseDaLista ($lista_frases, 21)."</label>\n");
    /* 20 - Alunos  */
    echo("                              <input id='aplic1' type=\"checkbox\" name=\"aplic[1]\" value='A'><label for=\"aplic1\">".RetornaFraseDaLista ($lista_frases, 20)."</label>\n");
    /* 57 - Visitantes  */
    echo("                              <input id='aplic2' type=\"checkbox\" name=\"aplic[2]\" value='V'><label for=\"aplic2\">".RetornaFraseDaLista ($lista_frases, 57)."</label>\n");
    /* 58 - Colaboradores  */
    echo("                              <input id='aplic3' type=\"checkbox\" name=\"aplic[3]\" value='Z'><label for=\"aplic3\">".RetornaFraseDaLista ($lista_frases, 58)."</label>\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 33 - Compartilhar resultado com */
    echo("                            <td class='itens' >".RetornaFraseDaLista($lista_frases,33)."</td>\n");
    echo("                            <td class='itens' >\n");
    /* 56 - Coordenador  */
    echo("                              <input id='result0' type=\"checkbox\" name=\"result[0]\" value='R'><label for=\"result0\">".RetornaFraseDaLista ($lista_frases, 56)."</label>\n");
    /* 21 - Formadores  */
    echo("                              <input id='result1' type=\"checkbox\" name=\"result[1]\" value='F'><label for=\"result1\">".RetornaFraseDaLista ($lista_frases, 21)."</label>\n");
    /* 20 - Alunos  */
    echo("                              <input id='result2' type=\"checkbox\" name=\"result[2]\" value='A'><label for=\"result2\">".RetornaFraseDaLista ($lista_frases, 20)."</label>\n");
    /* 57 - Visitantes  */
    echo("                              <input id='result3' type=\"checkbox\" name=\"result[3]\" value='V'><label for=\"result3\">".RetornaFraseDaLista ($lista_frases, 57)."</label>\n");
    /* 58 - Colaboradores  */
    echo("                              <input id='result4' type=\"checkbox\" name=\"result[4]\" value='Z'><label for=\"result4\">".RetornaFraseDaLista ($lista_frases, 58)."</label>\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 34 - Disponibilizar resultados parciais? */
    echo("                            <td class='itens' >".RetornaFraseDaLista($lista_frases,34)."</td>\n");
    echo("                            <td class='itens' >\n");
    echo("                              <input type=\"hidden\" name=\"resultado_parcial\" value=''>\n");
    /* 61 - Sim  */
    echo("                              <input class=\"input\" type=\"radio\" name=\"res_parc\" id=\"res_parc_S\" onClick=\"document.enquete.resultado_parcial.value='S';\"><label for=\"res_parc_S\">".RetornaFraseDaLista($lista_frases,61)."</label>\n");
    /* 62 - Nao  */
    echo("                              <input class=\"input\" type=\"radio\" name=\"res_parc\" id=\"res_parc_N\" onClick=\"document.enquete.resultado_parcial.value='N';\"><label for=\"res_parc_N\">".RetornaFraseDaLista($lista_frases,62)."</label>\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 35 - Disponibilizar identidade dos votos? */
    echo("                            <td class='itens' >".RetornaFraseDaLista($lista_frases,35)."</td>\n");
    echo("                            <td class='itens' >\n");
    echo("                              <input type=hidden name=identidade_votos value=''>\n");
    /* 61 - Sim  */
    echo("                              <input class=input type=radio name=id_votos id=id_votos_S onClick=\"document.enquete.identidade_votos.value='S';\"><label for=\"id_votos_S\">".RetornaFraseDaLista($lista_frases,61)."</label>\n");
    /* 62 - Nao  */
    echo("                              <input class=input type=radio name=id_votos id=id_votos_N onClick=\"document.enquete.identidade_votos.value='N';\"><label for=\"id_votos_N\">".RetornaFraseDaLista($lista_frases,62)."</label>\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 36 - Numero de escolhas */
    echo("                            <td class='itens' >".RetornaFraseDaLista($lista_frases,70)."</td>\n");
    echo("                            <td class='itens' >\n");
    echo("                              <input type=hidden name=num_escolhas value=''>\n");
    /* 59 - Somente uma alternativa  */
    echo("                              <input class=input type=radio name=num_esc id=num_esc_1 onClick=\"document.enquete.num_escolhas.value='1';\"><label for=\"num_esc_1\">".RetornaFraseDaLista($lista_frases,59)."</label>\n");
    /* 60 - Uma ou mais alternativas */
    echo("                              <input class=input type=radio name=num_esc id=num_esc_n onClick=\"document.enquete.num_escolhas.value='N';\"><label for=\"num_esc_n\">".RetornaFraseDaLista($lista_frases,60)."</label>\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    echo("                            <td colspan='2' align='left'>\n");
    /* 11 - Enviar */
    echo("                              <input type=\"submit\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,11)."\" />\n");
    /* 2 - Cancelar */
    echo("                              <input type=\"button\" class=\"input\" onclick=\"CancelaEnquete();\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
    echo("                       </table>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
    echo("                </form>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
    echo("          <br />\n");
    /* 509 - voltar, 510 - topo */
    echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
    //*NAO �FORMADOR*/
  }
  else
  {
    /* 1 - Enquete */
    /* 37 - Area restrita ao formador. */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,37)."</h4>\n");

    /*Voltar*/
    /* 509 - Voltar */
    echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("         <form><input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");
  }

  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");

  include("layer.php");

  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>